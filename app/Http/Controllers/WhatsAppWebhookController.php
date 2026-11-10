<?php

namespace App\Http\Controllers;

use App\Models\BotSetting;
use App\Services\VoiceAgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppWebhookController extends Controller
{
    protected VoiceAgentService $agentService;

    public function __construct(VoiceAgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Webhook verification for Meta WhatsApp Cloud API
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $storedToken = BotSetting::get('whatsapp_verify_token', 'WHATSAPP_VOICE_VERIFY_2026');

        if ($mode === 'subscribe' && $token === $storedToken) {
            Log::info('WhatsApp Webhook Verified Successfully');
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming WhatsApp events (Text & Voice Notes)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('WhatsApp Webhook Incoming:', $payload);

        $entry = $payload['entry'][0]['changes'][0]['value'] ?? null;
        if (!$entry || empty($entry['messages'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $message = $entry['messages'][0];
        $from = $message['from']; // Customer phone number
        $type = $message['type']; // 'text' or 'audio'

        $transcript = null;
        $audioPath = null;

        if ($type === 'text') {
            $transcript = $message['text']['body'] ?? '';
        } elseif ($type === 'audio') {
            $audioId = $message['audio']['id'] ?? null;
            if ($audioId) {
                $audioPath = $this->downloadWhatsAppMedia($audioId);
            }
        }

        // Process message through Voice Agent Service
        $sessionId = 'wa_' . $from;
        $result = $this->agentService->processMessage(
            sessionId: $sessionId,
            text: $transcript,
            audioPath: $audioPath,
            channel: 'whatsapp',
            customerPhone: $from
        );

        // Send response back to customer on WhatsApp
        if (!empty($result['reply'])) {
            $this->sendWhatsAppMessage($from, $result['reply']);
        }

        return response()->json(['status' => 'processed', 'data' => $result], 200);
    }

    /**
     * Download WhatsApp Audio File from Meta Graph API
     */
    protected function downloadWhatsAppMedia(string $mediaId): ?string
    {
        $token = BotSetting::get('whatsapp_access_token');
        if (empty($token)) {
            Log::warning('WhatsApp Access Token not configured, skipping media download.');
            return null;
        }

        try {
            // Step 1: Get media URL
            $urlResponse = Http::withToken($token)->get("https://graph.facebook.com/v19.0/{$mediaId}");
            if (!$urlResponse->successful()) return null;

            $mediaUrl = $urlResponse->json('url');

            // Step 2: Download media binary
            $fileResponse = Http::withToken($token)->get($mediaUrl);
            if ($fileResponse->successful()) {
                $filename = 'whatsapp_audio/' . $mediaId . '.ogg';
                Storage::disk('local')->put($filename, $fileResponse->body());
                return $filename;
            }
        } catch (\Exception $e) {
            Log::error('Failed to download WhatsApp media: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Send Outgoing WhatsApp Message via Meta Cloud API
     */
    protected function sendWhatsAppMessage(string $to, string $text): bool
    {
        $token = BotSetting::get('whatsapp_access_token');
        $phoneNumberId = BotSetting::get('whatsapp_phone_number_id');

        if (empty($token) || empty($phoneNumberId)) {
            Log::info("WhatsApp API not fully configured. Message logged: [To: {$to}] {$text}");
            return false;
        }

        try {
            $response = Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $text,
                ],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message: ' . $e->getMessage());
            return false;
        }
    }
}
