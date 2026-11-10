<?php

namespace Database\Seeders;

use App\Models\BotSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\VoiceConversation;
use App\Models\VoiceMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@voiceagent.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Bot Settings
        $settings = [
            'store_name' => 'NovaTech Pakistan',
            'store_phone' => '+92 300 1234567',
            'store_city' => 'Lahore',
            'delivery_fee' => '250',
            'free_delivery_threshold' => '3000',
            'delivery_time_days' => '2-3 working days',
            'return_policy_days' => '7 days easy return & replacement',
            'ai_provider' => 'gemini_openai_hybrid', // fallback to smart rule-based Urdu engine
            'whatsapp_verify_token' => 'WHATSAPP_VOICE_VERIFY_2026',
            'whatsapp_phone_number_id' => '1048291048201',
            'whatsapp_access_token' => '',
            'openai_api_key' => '',
            'gemini_api_key' => '',
            'system_persona' => "Aap NovaTech ke friendly aur polite WhatsApp Voice Sales Assistant hain. Aap Roman Urdu mein baat karte hain. Agar customer kisi cheez ki price pooche, to exact rate aur stock batayein. Order confirm karne ke liye unka Name, Phone Number, aur Delivery Address lein.",
        ];

        foreach ($settings as $k => $v) {
            BotSetting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        // Products
        $products = [
            [
                'name' => 'Nova Pro Wireless Earbuds ANC',
                'slug' => 'nova-pro-wireless-earbuds-anc',
                'category' => 'Audio & Gadgets',
                'sku' => 'EAR-001',
                'price' => 4500,
                'sale_price' => 3999,
                'stock' => 25,
                'description' => 'Active Noise Cancellation, 36 hours battery life, touch controls, waterproof IPX5.',
                'image_url' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Smart Watch Ultra 2 (AMOLED)',
                'slug' => 'smart-watch-ultra-2-amoled',
                'category' => 'Wearables',
                'sku' => 'WCH-002',
                'price' => 7500,
                'sale_price' => 6499,
                'stock' => 14,
                'description' => '2.02 inch AMOLED display, Bluetooth calling, heart rate & sleep tracker, 2 straps included.',
                'image_url' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=500&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'name' => '20,000mAh SuperFast Power Bank',
                'slug' => '20000mah-superfast-power-bank',
                'category' => 'Accessories',
                'sku' => 'PWR-003',
                'price' => 3800,
                'sale_price' => 3299,
                'stock' => 30,
                'description' => '22.5W PD Fast Charging, Type-C input/output, LED digital battery display.',
                'image_url' => 'https://images.unsplash.com/photo-1609592426815-591a329d47ce?w=500&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'RGB Mechanical Gaming Keyboard',
                'slug' => 'rgb-mechanical-gaming-keyboard',
                'category' => 'Gaming',
                'sku' => 'KEY-004',
                'price' => 5800,
                'sale_price' => 4999,
                'stock' => 8,
                'description' => 'Blue clicky switches, 16 RGB lighting modes, braided cable, anti-ghosting.',
                'image_url' => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=500&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Magnetic Car Charger Mount',
                'slug' => 'wireless-magnetic-car-charger-mount',
                'category' => 'Car Accessories',
                'sku' => 'CAR-005',
                'price' => 2600,
                'sale_price' => 2199,
                'stock' => 40,
                'description' => '15W Fast Qi wireless charging with strong MagSafe hold for AC vent.',
                'image_url' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=500&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Leather Slim Wallet (RFID Safe)',
                'slug' => 'premium-leather-slim-wallet-rfid',
                'category' => 'Fashion',
                'sku' => 'WAL-006',
                'price' => 2200,
                'sale_price' => 1799,
                'stock' => 19,
                'description' => 'Genuine textured leather, 8 card slots, money clip, RFID anti-theft blocking.',
                'image_url' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(['sku' => $prod['sku']], $prod);
        }

        // Seed Sample Voice Conversations & Orders
        $conv1 = VoiceConversation::create([
            'session_id' => 'sim_' . Str::random(10),
            'phone_number' => '+92 301 7654321',
            'customer_name' => 'Hamza Tariq',
            'channel' => 'whatsapp',
            'status' => 'closed',
            'last_activity' => now()->subHours(2),
        ]);

        VoiceMessage::create([
            'voice_conversation_id' => $conv1->id,
            'sender' => 'user',
            'type' => 'voice',
            'transcript' => 'Assalam o alaikum bhai, Smart Watch Ultra 2 ka price kya hai aur black color mein mil jayegi?',
            'intent' => 'product_inquiry',
            'entities' => ['product' => 'Smart Watch Ultra 2', 'color' => 'black'],
            'response_text' => 'Walaikum Assalam! Smart Watch Ultra 2 AMOLED bilkul available hai hamare pas. Iska discounted price Rs. 6,499 hai aur black strap stock mein hai. Kya aap iska order book karwana chahte hain?',
            'created_at' => now()->subHours(2)->addMinutes(1),
        ]);

        VoiceMessage::create([
            'voice_conversation_id' => $conv1->id,
            'sender' => 'user',
            'type' => 'voice',
            'transcript' => 'Ji bilkul book kar dein. Mera naam Hamza Tariq hai, address House 42, Street 8, F-10/2 Islamabad. Phone number 03017654321.',
            'intent' => 'book_order',
            'entities' => [
                'customer_name' => 'Hamza Tariq',
                'address' => 'House 42, Street 8, F-10/2 Islamabad',
                'phone' => '03017654321',
                'product' => 'Smart Watch Ultra 2',
                'quantity' => 1
            ],
            'response_text' => 'Zabardast Hamza bhai! Aapka order book ho chuka hai. Order ID hai #ORD-84192. Total bill Rs. 6,499 (Free Delivery) hai aur Cash on Delivery par 2-3 working days mein pohanch jayega.',
            'created_at' => now()->subHours(2)->addMinutes(3),
        ]);

        // Create Order for Conv 1
        $order1 = Order::create([
            'order_number' => 'ORD-84192',
            'voice_conversation_id' => $conv1->id,
            'customer_name' => 'Hamza Tariq',
            'customer_phone' => '03017654321',
            'delivery_address' => 'House 42, Street 8, F-10/2 Islamabad',
            'total_amount' => 6499,
            'payment_method' => 'Cash on Delivery (COD)',
            'status' => 'confirmed',
            'channel' => 'whatsapp_voice',
            'voice_transcript' => 'Ji bilkul book kar dein. Mera naam Hamza Tariq hai, address House 42, Street 8, F-10/2 Islamabad...',
            'created_at' => now()->subHours(2)->addMinutes(3),
        ]);

        $prodWatch = Product::where('sku', 'WCH-002')->first();
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $prodWatch->id,
            'product_name' => $prodWatch->name,
            'price' => 6499,
            'quantity' => 1,
            'subtotal' => 6499,
        ]);

        // Conversation 2 (Inquiry about Earbuds)
        $conv2 = VoiceConversation::create([
            'session_id' => 'sim_' . Str::random(10),
            'phone_number' => '+92 333 9876543',
            'customer_name' => 'Ayesha Khan',
            'channel' => 'simulator',
            'status' => 'active',
            'last_activity' => now()->subMinutes(25),
        ]);

        VoiceMessage::create([
            'voice_conversation_id' => $conv2->id,
            'sender' => 'user',
            'type' => 'voice',
            'transcript' => 'Bhai wireless earbuds ki battery timing kitni hai aur delivery charges kitne hain Lahore ke?',
            'intent' => 'faq',
            'entities' => ['product' => 'Nova Pro Wireless Earbuds ANC'],
            'response_text' => 'Nova Pro Wireless Earbuds ki battery 36 hours tak chalti hai with charging case! Lahore mein delivery charges Rs. 250 hain, lekin Rs. 3,000 se oopar ke orders par free delivery hai, is liye aapke earbuds par Delivery FREE hogi!',
            'created_at' => now()->subMinutes(25),
        ]);
    }
}
