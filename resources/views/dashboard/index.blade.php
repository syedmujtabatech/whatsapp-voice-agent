@extends('layouts.app')

@section('title', 'Admin Dashboard & Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-emerald-600">
                <i class="fa-solid fa-chart-line"></i>
                <span>Executive Operations</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">WhatsApp Voice Agent Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Live metrics for voice automated sales, inventory health, and Roman-Urdu speech pipeline.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('simulator.index') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center space-x-2 transition shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-headset"></i>
                <span>Launch Voice Simulator</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center space-x-2 transition shadow-sm">
                <i class="fa-solid fa-box"></i>
                <span>Manage Orders</span>
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Voice Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Voice Orders</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1.5">{{ $totalOrders }}</h3>
                    <span class="inline-flex items-center space-x-1 text-[11px] font-semibold text-emerald-600 mt-1">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        <span>100% Automated via Voice</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-cart-check"></i>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Voice Sales Revenue</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1.5">Rs. {{ number_format($totalRevenue) }}</h3>
                    <span class="inline-flex items-center space-x-1 text-[11px] font-semibold text-indigo-600 mt-1">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <span>Cash on Delivery (COD)</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
        </div>

        <!-- Total Voice Queries / Transcripts -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Voice Inquiries Handled</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1.5">{{ $totalMessages }}</h3>
                    <span class="inline-flex items-center space-x-1 text-[11px] font-semibold text-rose-600 mt-1">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Avg response: 1.1 sec</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-microphone-lines"></i>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500"></div>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Voice-to-Order Conversion</p>
                    @php
                        $conversion = $totalConversations > 0 ? round(($totalOrders / $totalConversations) * 100, 1) : 0;
                    @endphp
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1.5">{{ $conversion }}%</h3>
                    <span class="inline-flex items-center space-x-1 text-[11px] font-semibold text-amber-600 mt-1">
                        <i class="fa-solid fa-star"></i>
                        <span>High Purchase Intent</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
        </div>

    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Intent Breakdown Chart (Col 5) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-slate-800 text-base">Customer Voice Intent Distribution</h3>
                    <span class="text-[11px] text-slate-400">Speech Classification</span>
                </div>
                <p class="text-xs text-slate-500 mb-6">Percentage breakdown of customer intent in Roman-Urdu audio notes:</p>
                <div class="relative h-64 flex items-center justify-center">
                    <canvas id="intentChart"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-6 pt-4 border-t border-slate-100 text-xs text-slate-600">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span>Order Booking</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span>Price / Rate Check</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <span>Stock Availability</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span>Order Tracking</span>
                </div>
            </div>
        </div>

        <!-- Weekly Voice Sales Trend (Col 7) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-heading font-bold text-slate-800 text-base">Voice Queries vs. Orders Placed</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Automated resolution performance over past 7 days</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                    94.2% Accuracy
                </span>
            </div>
            <div class="relative h-72">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="font-heading font-bold text-slate-800 text-base">Recent Voice Orders</h3>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                View All Orders &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Order #</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Items</th>
                        <th class="py-3.5 px-4">Total Bill</th>
                        <th class="py-3.5 px-4">Channel</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                #{{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $order->customer_name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @foreach($order->items as $it)
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px]">
                                        {{ $it->product_name }} (x{{ $it->quantity }})
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-3.5 px-4 font-bold text-emerald-600">
                                Rs. {{ number_format($order->total_amount) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-microphone text-[9px]"></i>
                                    <span>{{ $order->channel }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $stClass = match($order->status) {
                                        'confirmed' => 'bg-emerald-100 text-emerald-800',
                                        'dispatched' => 'bg-indigo-100 text-indigo-800',
                                        'delivered' => 'bg-teal-100 text-teal-800',
                                        'cancelled' => 'bg-rose-100 text-rose-800',
                                        default => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $stClass }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                {{ $order->created_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                    View Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                No voice orders recorded yet. Open the Simulator to place your first voice order!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Intent Breakdown Chart
    const intentCtx = document.getElementById('intentChart').getContext('2d');
    new Chart(intentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Book Order', 'Price Check', 'Stock Check', 'FAQ & Delivery', 'Order Status'],
            datasets: [{
                data: [35, 28, 18, 12, 7],
                backgroundColor: [
                    '#10B981', // emerald
                    '#3B82F6', // blue
                    '#8B5CF6', // purple
                    '#06B6D4', // cyan
                    '#F59E0B'  // amber
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '72%'
        }
    });

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [
                {
                    label: 'Voice Inquiries',
                    data: [42, 68, 55, 78, 92, 110, 85],
                    borderColor: '#6366F1',
                    backgroundColor: 'rgba(99, 102, 241, 0.08)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Orders Placed (Voice)',
                    data: [15, 24, 20, 31, 38, 48, 35],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
