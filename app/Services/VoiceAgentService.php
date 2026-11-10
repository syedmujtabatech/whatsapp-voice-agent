<?php

namespace App\Services;

use App\Models\BotSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\VoiceConversation;
use App\Models\VoiceMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VoiceAgentService
{
    /**
     * Process an incoming message (either voice or text) from Simulator or WhatsApp
     */
    public function processMessage(string $sessionId, ?string $text, ?string $audioPath, string $channel = 'simulator', ?string $customerPhone = null): array
    {
        // 1. Find or create conversation
        $conversation = VoiceConversation::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'phone_number' => $customerPhone,
                'channel' => $channel,
                'status' => 'active',
                'context' => [
                    'current_intent' => null,
                    'selected_product_id' => null,
                    'cart' => [],
                    'customer_data' => [
                        'name' => null,
                        'phone' => $customerPhone,
                        'address' => null,
                    ],
                ],
                'last_activity' => now(),
            ]
        );

        $context = $conversation->context ?? [];
        if (!isset($context['cart'])) $context['cart'] = [];
        if (!isset($context['customer_data'])) {
            $context['customer_data'] = ['name' => null, 'phone' => $customerPhone, 'address' => null];
        }

        // 2. If text is empty but audio is provided, simulate or call Speech-to-Text
        $transcript = $text;
        if (empty($transcript) && !empty($audioPath)) {
            $transcript = $this->transcribeAudio($audioPath);
        }

        if (empty($transcript)) {
            $transcript = "Assalam o alaikum, aapki awaz samajh nahi aayi, barahe karam dobara bole ya message likhein.";
        }

        // 3. NLP & Intent Classification (Roman Urdu + English)
        $analysis = $this->analyzeRomanUrduIntent($transcript, $context);
        $intent = $analysis['intent'];
        $entities = $analysis['entities'];

        // Update context with any newly extracted entities
        if (!empty($entities['product_id'])) {
            $context['selected_product_id'] = $entities['product_id'];
        }
        if (!empty($entities['customer_name'])) {
            $context['customer_data']['name'] = $entities['customer_name'];
            $conversation->customer_name = $entities['customer_name'];
        }
        if (!empty($entities['phone'])) {
            $context['customer_data']['phone'] = $entities['phone'];
            $conversation->phone_number = $entities['phone'];
        }
        if (!empty($entities['address'])) {
            $context['customer_data']['address'] = $entities['address'];
        }

        // 4. Generate Response based on Intent & State
        $responsePayload = $this->generateAgentResponse($intent, $entities, $context, $conversation, $transcript);
        $responseText = $responsePayload['reply'];
        $orderCreated = $responsePayload['order'] ?? null;

        // Save updated context
        $conversation->context = $context;
        $conversation->last_activity = now();
        $conversation->save();

        // 5. Store user message in DB
        $userMsg = VoiceMessage::create([
            'voice_conversation_id' => $conversation->id,
            'sender' => 'user',
            'type' => !empty($audioPath) ? 'voice' : 'text',
            'audio_path' => $audioPath,
            'transcript' => $transcript,
            'intent' => $intent,
            'entities' => $entities,
            'response_text' => $responseText,
        ]);

        return [
            'success' => true,
            'conversation_id' => $conversation->id,
            'session_id' => $conversation->session_id,
            'transcript' => $transcript,
            'intent' => $intent,
            'entities' => $entities,
            'reply' => $responseText,
            'order' => $orderCreated,
            'matched_product' => $responsePayload['product'] ?? null,
            'suggested_actions' => $responsePayload['actions'] ?? [],
        ];
    }

    /**
     * Speech-to-Text handler (Supports OpenAI Whisper API if key present, else smart simulation)
     */
    public function transcribeAudio(string $audioPath): string
    {
        $openaiKey = BotSetting::get('openai_api_key');
        if (!empty($openaiKey) && file_exists(storage_path('app/' . $audioPath))) {
            try {
                $response = Http::withToken($openaiKey)
                    ->attach('file', file_get_contents(storage_path('app/' . $audioPath)), 'audio.wav')
                    ->post('https://api.openai.com/v1/audio/transcriptions', [
                        'model' => 'whisper-1',
                        'language' => 'ur',
                        'prompt' => 'Roman Urdu transcript: Assalam o alaikum, earbuds, price, order book karna hai.',
                    ]);

                if ($response->successful()) {
                    return $response->json('text') ?? "Awaz clear nahi thi.";
                }
            } catch (\Exception $e) {
                Log::error('Whisper STT failed: ' . $e->getMessage());
            }
        }

        return "Audio voice note received.";
    }

    /**
     * Roman Urdu Intent Analysis Engine
     */
    protected function analyzeRomanUrduIntent(string $text, array $context): array
    {
        $normalized = strtolower(trim($text));
        $allProducts = Product::where('is_active', true)->get();

        $entities = [
            'product_id' => null,
            'product_name' => null,
            'quantity' => 1,
            'customer_name' => null,
            'phone' => null,
            'address' => null,
            'order_number' => null,
        ];

        // 1. Identify product mentioned
        $matchedProduct = null;
        foreach ($allProducts as $p) {
            $productKeywords = explode(' ', strtolower($p->name));
            $pName = strtolower($p->name);

            if (str_contains($normalized, $pName)) {
                $matchedProduct = $p;
                break;
            }

            // Keyword triggers
            if (str_contains($normalized, 'earbuds') || str_contains($normalized, 'airpods') || str_contains($normalized, 'handsfree')) {
                if (str_contains($pName, 'earbuds')) { $matchedProduct = $p; break; }
            }
            if (str_contains($normalized, 'watch') || str_contains($normalized, 'ghari') || str_contains($normalized, 'ghadi')) {
                if (str_contains($pName, 'watch')) { $matchedProduct = $p; break; }
            }
            if (str_contains($normalized, 'power bank') || str_contains($normalized, 'powerbank') || str_contains($normalized, 'charger')) {
                if (str_contains($pName, 'power bank')) { $matchedProduct = $p; break; }
            }
            if (str_contains($normalized, 'keyboard') || str_contains($normalized, 'gaming keyboard')) {
                if (str_contains($pName, 'keyboard')) { $matchedProduct = $p; break; }
            }
            if (str_contains($normalized, 'car mount') || str_contains($normalized, 'car charger')) {
                if (str_contains($pName, 'car charger')) { $matchedProduct = $p; break; }
            }
            if (str_contains($normalized, 'wallet') || str_contains($normalized, 'batwa') || str_contains($normalized, 'purse')) {
                if (str_contains($pName, 'wallet')) { $matchedProduct = $p; break; }
            }
        }

        if ($matchedProduct) {
            $entities['product_id'] = $matchedProduct->id;
            $entities['product_name'] = $matchedProduct->name;
        } elseif (!empty($context['selected_product_id'])) {
            $existing = Product::find($context['selected_product_id']);
            if ($existing) {
                $entities['product_id'] = $existing->id;
                $entities['product_name'] = $existing->name;
            }
        }

        // 2. Extract Phone Number
        if (preg_match('/(03[0-9]{9}|\+923[0-9]{9}|[0-9]{11})/i', $normalized, $matches)) {
            $entities['phone'] = $matches[0];
        }

        // 3. Extract Order Number
        if (preg_match('/(ord-[0-9]{4,6}|order\s*#?\s*[0-9]{4,6}|#[0-9]{4,6})/i', $normalized, $matches)) {
            $clean = preg_replace('/[^0-9]/', '', $matches[0]);
            $entities['order_number'] = 'ORD-' . $clean;
        }

        // 4. Extract Name
        if (preg_match('/(?:naam|name|mera naam)\s*(?:hai|is|:)?\s*([a-zA-Z\s]{2,35}?)(?:\s+(?:hai|is|\.|\,|$)|$)/i', $text, $matches)) {
            $rawName = trim(preg_replace('/\b(hai|is|mera|naam)\b/i', '', $matches[1]));
            if (!empty($rawName)) {
                $entities['customer_name'] = ucwords(trim($rawName));
            }
        }

        // 5. Extract Address
        if (preg_match('/(?:address|pata|adrees|delivery address|shehar)\s*(?:hai|:)?\s*([^,\.\n]{6,100})/i', $text, $matches)) {
            $entities['address'] = trim($matches[1]);
        } elseif (preg_match('/(?:house|st|street|block|phase|sector|f-10|gulshan|dha|johar|lahore|karachi|islamabad|rawalpindi)[^\.\n]*/i', $text, $matches)) {
            $entities['address'] = trim($matches[0]);
        }

        // 6. Extract Quantity
        if (preg_match('/([1-9]|ek|aik|do|teen|char|panch)\s*(?:piece|pieces|pcs|adad|dana|pair)?/i', $normalized, $matches)) {
            $qtyWord = strtolower($matches[1]);
            $qtyMap = ['ek' => 1, 'aik' => 1, 'do' => 2, 'teen' => 3, 'char' => 4, 'panch' => 5];
            $entities['quantity'] = isset($qtyMap[$qtyWord]) ? $qtyMap[$qtyWord] : (int)$qtyWord;
        }

        // 7. Intent Classification
        $intent = 'general_inquiry';

        // Order Booking Intent
        if (
            str_contains($normalized, 'book') ||
            str_contains($normalized, 'order') ||
            str_contains($normalized, 'khareed') ||
            str_contains($normalized, 'mangwana') ||
            str_contains($normalized, 'bhej do') ||
            str_contains($normalized, 'bhej dein') ||
            str_contains($normalized, 'confirm') ||
            str_contains($normalized, 'cash on delivery')
        ) {
            if (str_contains($normalized, 'track') || str_contains($normalized, 'kahan pohancha') || str_contains($normalized, 'status')) {
                $intent = 'order_status';
            } else {
                $intent = 'book_order';
            }
        }
        // Order Status / Tracking
        elseif (
            str_contains($normalized, 'kahan pohancha') ||
            str_contains($normalized, 'track') ||
            str_contains($normalized, 'status') ||
            !empty($entities['order_number'])
        ) {
            $intent = 'order_status';
        }
        // Stock / Availability
        elseif (
            str_contains($normalized, 'available') ||
            str_contains($normalized, 'stock') ||
            str_contains($normalized, 'mil jayega') ||
            str_contains($normalized, 'mil sakti') ||
            str_contains($normalized, 'para hai')
        ) {
            $intent = 'stock_check';
        }
        // Price / Rate Check
        elseif (
            str_contains($normalized, 'price') ||
            str_contains($normalized, 'rate') ||
            str_contains($normalized, 'kitne ka') ||
            str_contains($normalized, 'kitne ki') ||
            str_contains($normalized, 'cost')
        ) {
            $intent = 'price_check';
        }
        // FAQ / Delivery charges / Policy
        elseif (
            str_contains($normalized, 'delivery') ||
            str_contains($normalized, 'return') ||
            str_contains($normalized, 'warranty') ||
            str_contains($normalized, 'charges') ||
            str_contains($normalized, 'kitne din') ||
            str_contains($normalized, 'policy')
        ) {
            $intent = 'faq';
        }
        // Greetings
        elseif (
            str_contains($normalized, 'assalam') ||
            str_contains($normalized, 'salam') ||
            str_contains($normalized, 'hello') ||
            str_contains($normalized, 'hi') ||
            str_contains($normalized, 'hey')
        ) {
            $intent = 'greeting';
        }

        return [
            'intent' => $intent,
            'entities' => $entities,
        ];
    }

    /**
     * Generate Natural Roman-Urdu Response and Execute State Machine Actions
     */
    protected function generateAgentResponse(string $intent, array &$entities, array &$context, VoiceConversation $conversation, string $rawTranscript): array
    {
        $storeName = BotSetting::get('store_name', 'NovaTech Pakistan');
        $deliveryFee = BotSetting::get('delivery_fee', '250');
        $freeThreshold = BotSetting::get('free_delivery_threshold', '3000');
        $deliveryDays = BotSetting::get('delivery_time_days', '2-3 working days');
        $returnDays = BotSetting::get('return_policy_days', '7 days replacement');

        $product = null;
        if (!empty($entities['product_id'])) {
            $product = Product::find($entities['product_id']);
        }

        $orderCreated = null;
        $actions = [];

        switch ($intent) {
            case 'greeting':
                $reply = "Walaikum Assalam! {$storeName} mein khush-aamdeed. Main aapka AI Voice Assistant hoon. Aaj aapko kis item ki maloomat chahiye? Jaise ke Smart Watch, ANC Earbuds, ya Power Bank?";
                $actions = ['Smart Watch check karein', 'Earbuds price dekhein', 'Delivery maloomat'];
                break;

            case 'price_check':
            case 'stock_check':
                if ($product) {
                    $stockMsg = $product->stock > 0 ? "ye is waqt STOCK MEIN AVAILABLE hai ({$product->stock} pieces baqi hain)" : "ye filhal out of stock ho chuka hai";
                    $priceFormatted = number_format($product->effective_price);
                    $reply = "Ji bilkul! {$product->name} ka rate Rs. {$priceFormatted} hai aur {$stockMsg}. Agar aap Rs. {$freeThreshold} se zyada ka order karein to Delivery bilkul FREE hogi! Kya aap iska order book karwana chahte hain?";
                    $actions = ["Order book karein", "Specifications poochein"];
                } else {
                    $reply = "Bhai aap kis product ki price janna chahte hain? Hamare pas Smart Watch Ultra 2 (Rs. 6,499), Nova ANC Earbuds (Rs. 3,999), aur 20,000mAh Power Bank (Rs. 3,299) available hain.";
                    $actions = ["Smart Watch", "ANC Earbuds", "Power Bank"];
                }
                break;

            case 'book_order':
                if (!$product) {
                    $reply = "Zaroor! Pehle batayein aapko kaun sa item mangwana hai? (e.g., Smart Watch, Earbuds, ya Power Bank).";
                    break;
                }

                // Check if we have complete checkout information
                $name = $entities['customer_name'] ?? $context['customer_data']['name'] ?? null;
                $phone = $entities['phone'] ?? $context['customer_data']['phone'] ?? null;
                $address = $entities['address'] ?? $context['customer_data']['address'] ?? null;

                $missing = [];
                if (empty($name)) $missing[] = "Aapka Naam";
                if (empty($phone)) $missing[] = "Mobile / WhatsApp Number";
                if (empty($address)) $missing[] = "Complete Delivery Address (City aur Area)";

                if (!empty($missing)) {
                    $missingList = implode(', ', $missing);
                    $reply = "Bohat khoob! {$product->name} (Qty: {$entities['quantity']}) ka order note kar liya hai. Order confirm karne ke liye bas " . $missingList . " bata dein.";
                    $actions = ["Voice note bhejein", "Text likhein"];
                } else {
                    // All info present -> CREATE REAL ORDER IN DB
                    $quantity = max(1, $entities['quantity'] ?? 1);
                    $subtotal = $product->effective_price * $quantity;
                    $actualDeliveryFee = $subtotal >= (float)$freeThreshold ? 0 : (float)$deliveryFee;
                    $total = $subtotal + $actualDeliveryFee;

                    $orderNumber = 'ORD-' . strtoupper(Str::random(5)) . rand(10, 99);

                    $order = Order::create([
                        'order_number' => $orderNumber,
                        'voice_conversation_id' => $conversation->id,
                        'customer_name' => $name,
                        'customer_phone' => $phone,
                        'delivery_address' => $address,
                        'total_amount' => $total,
                        'payment_method' => 'Cash on Delivery (COD)',
                        'status' => 'confirmed',
                        'channel' => $conversation->channel === 'whatsapp' ? 'whatsapp_voice' : 'simulator_voice',
                        'voice_transcript' => $rawTranscript,
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->effective_price,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ]);

                    // Decrement stock
                    $product->decrement('stock', $quantity);

                    $orderCreated = $order;
                    $deliveryMsg = $actualDeliveryFee == 0 ? "FREE Delivery" : "Rs. {$actualDeliveryFee} Delivery";

                    $reply = "Mubarak ho {$name} bhai! 🎉 Aapka order kamiyabi se book ho chuka hai.\n\n📦 Order Number: #{$orderNumber}\n🛍️ Item: {$product->name} (x{$quantity})\n💵 Total Bill: Rs. " . number_format($total) . " ({$deliveryMsg})\n📍 Address: {$address}\n🚚 Delivery: 2-3 working days mein Cash on Delivery par pohanch jayega!";
                    $actions = ["Track Order", "Dusri item dekhein"];
                }
                break;

            case 'order_status':
                $orderNum = $entities['order_number'] ?? null;
                $phone = $entities['phone'] ?? $context['customer_data']['phone'] ?? null;

                $foundOrder = null;
                if ($orderNum) {
                    $foundOrder = Order::where('order_number', 'LIKE', "%{$orderNum}%")->first();
                } elseif ($phone) {
                    $foundOrder = Order::where('customer_phone', 'LIKE', "%{$phone}%")->latest()->first();
                } else {
                    $foundOrder = Order::latest()->first();
                }

                if ($foundOrder) {
                    $statusNames = [
                        'pending' => 'Pending (Verification)',
                        'confirmed' => 'Confirmed & Packing in progress',
                        'dispatched' => 'Dispatched (Courier rider ke hawale hai)',
                        'delivered' => 'Delivered successfully',
                        'cancelled' => 'Cancelled',
                    ];
                    $stText = $statusNames[$foundOrder->status] ?? $foundOrder->status;
                    $reply = "Aapka Order #{$foundOrder->order_number} is waqt '{$stText}' stage par hai. Delivery address: {$foundOrder->delivery_address}. Expected arrival 24-48 hours mein hai.";
                } else {
                    $reply = "Bhai apna Order Number (jaise #ORD-84192) ya register mobile number batayein taake main foran live status check kar sakoon.";
                }
                break;

            case 'faq':
                $reply = "Hamari delivery poore Pakistan mein {$deliveryDays} mein ho jati hai. Delivery charges Rs. {$deliveryFee} hain, lekin Rs. {$freeThreshold} se baray order par FREE delivery hai! Iske ilawa hamari taraf se {$returnDays} ki guarantee bhi shamil hai.";
                break;

            default:
                $reply = "Main {$storeName} ka voice agent hoon. Aap mujhse kisi bhi product ka rate, availability, delivery policy pooch sakte hain ya direct voice note par order book karwa sakte hain!";
                break;
        }

        return [
            'reply' => $reply,
            'order' => $orderCreated,
            'product' => $product,
            'actions' => $actions,
        ];
    }
}
