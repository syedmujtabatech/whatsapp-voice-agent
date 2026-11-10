<?php

namespace App\Http\Controllers;

use App\Models\BotSetting;
use App\Models\Product;
use App\Models\VoiceConversation;
use App\Services\VoiceAgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SimulatorController extends Controller
{
    protected VoiceAgentService $agentService;

    public function __construct(VoiceAgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Show WhatsApp Web Simulator
     */
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        $storeName = BotSetting::get('store_name', 'NovaTech Pakistan');
        $storePhone = BotSetting::get('store_phone', '+92 300 1234567');

        // Presets for effortless live demo
        $presets = [
            [
                'title' => 'Price & Availability Check',
                'text' => 'Assalam o alaikum bhai, Smart Watch Ultra 2 ka rate kya hai aur black color available hai?',
                'badge' => 'Rate Check',
                'color' => 'blue',
            ],
            [
                'title' => 'Direct Voice Order Booking',
                'text' => 'Mera order book kar dein 1 pair Smart Watch Ultra 2. Mera naam Zeeshan Ali hai, Phone 03001234567, Address House 15, Street 4, DHA Phase 5 Lahore.',
                'badge' => 'Book Order',
                'color' => 'emerald',
            ],
            [
                'title' => 'Earbuds Battery & Delivery Info',
                'text' => 'Bhai Nova Pro ANC Earbuds ki battery timing kitni hai aur delivery charges kitne hain Karachi ke?',
                'badge' => 'FAQ / Specs',
                'color' => 'purple',
            ],
            [
                'title' => 'Live Order Tracking',
                'text' => 'Mera order number ORD-84192 kahan tak pohancha hai status batayein?',
                'badge' => 'Track Status',
                'color' => 'amber',
            ],
            [
                'title' => 'Power Bank Stock Inquiry',
                'text' => 'Bhai 20000mAh wala power bank kitne ka hai aur fast charging support karta hai?',
                'badge' => 'Stock Check',
                'color' => 'cyan',
            ],
        ];

        return view('simulator.index', compact('products', 'storeName', 'storePhone', 'presets'));
    }

    /**
     * Process message from Simulator (Text or Audio)
     */
    public function sendMessage(Request $request)
    {
        $sessionId = $request->input('session_id') ?: 'sim_session_' . session()->getId();
        $text = $request->input('text');
        $audioFile = $request->file('audio');

        $audioPath = null;
        if ($audioFile) {
            $audioPath = $audioFile->store('simulator_audio', 'local');
        }

        $result = $this->agentService->processMessage(
            sessionId: $sessionId,
            text: $text,
            audioPath: $audioPath,
            channel: 'simulator',
            customerPhone: $request->input('phone', '+92 300 7891234')
        );

        return response()->json($result);
    }

    /**
     * Reset Simulator Session
     */
    public function reset(Request $request)
    {
        $sessionId = $request->input('session_id');
        if ($sessionId) {
            VoiceConversation::where('session_id', $sessionId)->delete();
        }

        return response()->json(['success' => true]);
    }
}
