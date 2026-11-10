<?php

namespace App\Http\Controllers;

use App\Models\BotSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\VoiceConversation;
use App\Models\VoiceMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Dashboard Overview Metrics
     */
    public function index()
    {
        $totalOrders = Order::count();
        $voiceOrders = Order::where('channel', 'LIKE', '%voice%')->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalConversations = VoiceConversation::count();
        $totalMessages = VoiceMessage::count();
        $productsInStock = Product::where('stock', '>', 0)->count();

        // Recent Orders
        $recentOrders = Order::with('items')->latest()->take(6)->get();

        // Recent Conversations
        $recentConversations = VoiceConversation::with('messages')->latest()->take(5)->get();

        // Intent Distribution
        $intentCounts = VoiceMessage::selectRaw('intent, count(*) as total')
            ->whereNotNull('intent')
            ->groupBy('intent')
            ->pluck('total', 'intent')
            ->toArray();

        return view('dashboard.index', compact(
            'totalOrders',
            'voiceOrders',
            'totalRevenue',
            'totalConversations',
            'totalMessages',
            'productsInStock',
            'recentOrders',
            'recentConversations',
            'intentCounts'
        ));
    }

    /**
     * Voice Orders List
     */
    public function orders(Request $request)
    {
        $status = $request->query('status');
        $query = Order::with('items', 'conversation')->latest();

        if ($status && in_array($status, ['pending', 'confirmed', 'dispatched', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15);
        return view('dashboard.orders', compact('orders', 'status'));
    }

    /**
     * Order Details
     */
    public function showOrder($id)
    {
        $order = Order::with(['items.product', 'conversation.messages'])->findOrFail($id);
        return view('dashboard.order-detail', compact('order'));
    }

    /**
     * Update Order Status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,confirmed,dispatched,delivered,cancelled']);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', "Order #{$order->order_number} status updated to {$order->status}");
    }

    /**
     * Voice Conversation Logs
     */
    public function conversations()
    {
        $conversations = VoiceConversation::with('messages', 'orders')->latest()->paginate(15);
        return view('dashboard.conversations', compact('conversations'));
    }

    /**
     * Product Catalog Management
     */
    public function products()
    {
        $products = Product::latest()->paginate(15);
        return view('dashboard.products', compact('products'));
    }

    /**
     * Store/Update Product
     */
    public function saveProduct(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sku' => $validated['sku'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if ($id) {
            $product = Product::findOrFail($id);
            $product->update($data);
            $msg = 'Product updated successfully.';
        } else {
            Product::create($data);
            $msg = 'Product created successfully.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Bot & Integration Settings
     */
    public function settings()
    {
        $settings = BotSetting::all()->pluck('value', 'key')->toArray();
        return view('dashboard.settings', compact('settings'));
    }

    /**
     * Update Bot Settings
     */
    public function saveSettings(Request $request)
    {
        $fields = [
            'store_name',
            'store_phone',
            'store_city',
            'delivery_fee',
            'free_delivery_threshold',
            'delivery_time_days',
            'return_policy_days',
            'ai_provider',
            'whatsapp_verify_token',
            'whatsapp_phone_number_id',
            'whatsapp_access_token',
            'openai_api_key',
            'gemini_api_key',
            'system_persona',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                BotSetting::set($field, $request->input($field));
            }
        }

        return back()->with('success', 'Bot & WhatsApp Settings updated successfully.');
    }
}
