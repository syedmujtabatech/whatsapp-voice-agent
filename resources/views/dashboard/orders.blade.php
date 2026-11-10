@extends('layouts.app')

@section('title', 'Voice Orders Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-emerald-600">
                <i class="fa-solid fa-box-open"></i>
                <span>Automated Sales Pipeline</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">WhatsApp Voice Orders</h1>
            <p class="text-sm text-slate-500 mt-1">Orders booked directly from customers' voice notes in Roman-Urdu.</p>
        </div>
        <a href="{{ route('simulator.index') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center space-x-2 transition shadow-md shadow-emerald-500/20">
            <i class="fa-solid fa-microphone"></i>
            <span>Simulate New Voice Order</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 overflow-x-auto pb-2 text-xs font-semibold">
        <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded-lg transition {{ empty($status) ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            All Orders
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="px-3 py-2 rounded-lg transition {{ $status === 'confirmed' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Confirmed
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'dispatched']) }}" class="px-3 py-2 rounded-lg transition {{ $status === 'dispatched' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Dispatched
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="px-3 py-2 rounded-lg transition {{ $status === 'delivered' ? 'bg-teal-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Delivered
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="px-3 py-2 rounded-lg transition {{ $status === 'cancelled' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Cancelled
        </a>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Order Details</th>
                        <th class="py-3.5 px-4">Customer Info</th>
                        <th class="py-3.5 px-4">Delivery Address</th>
                        <th class="py-3.5 px-4">Ordered Items</th>
                        <th class="py-3.5 px-4">Total Amount</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-slate-900 block">#{{ $ord->order_number }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $ord->created_at->format('M d, Y h:i A') }}</span>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $ord->channel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $ord->customer_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono flex items-center space-x-1 mt-0.5">
                                    <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                    <span>{{ $ord->customer_phone }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-slate-600 truncate" title="{{ $ord->delivery_address }}">
                                    {{ $ord->delivery_address }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                @foreach($ord->items as $it)
                                    <div class="text-slate-700 font-medium">
                                        &bull; {{ $it->product_name }} <span class="text-slate-400 font-mono">(x{{ $it->quantity }})</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-sm font-extrabold text-emerald-600 block">Rs. {{ number_format($ord->total_amount) }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">COD</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.orders.status', $ord->id) }}" method="POST" class="inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="text-[11px] font-bold rounded-lg border-slate-200 py-1 px-2 bg-slate-50 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="pending" {{ $ord->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $ord->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="dispatched" {{ $ord->status === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                                        <option value="delivered" {{ $ord->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $ord->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition">
                                    View Invoice &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                No orders matching this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
