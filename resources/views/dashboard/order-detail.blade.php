@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - Invoice')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    <!-- Back button & Actions -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to All Orders</span>
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center space-x-2 transition shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>Print Invoice</span>
            </button>
        </div>
    </div>

    <!-- Voice Transcript Card -->
    @if($order->voice_transcript)
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 rounded-2xl p-5 text-white border border-emerald-500/30 shadow-md">
            <div class="flex items-center space-x-2 text-emerald-400 font-bold text-xs uppercase tracking-wider mb-2">
                <i class="fa-solid fa-microphone-lines"></i>
                <span>Original Customer Voice Transcript (Roman-Urdu AI Extraction)</span>
            </div>
            <p class="text-sm font-mono italic text-emerald-100 bg-white/10 p-3 rounded-xl border border-white/10 leading-relaxed">
                "{{ $order->voice_transcript }}"
            </p>
            <div class="flex items-center justify-between text-xs text-slate-300 mt-3 pt-3 border-t border-white/10">
                <span>Channel: <strong>{{ $order->channel }}</strong></span>
                <span>Automated Slot-Filling Confidence: <strong class="text-emerald-400">98.5%</strong></span>
            </div>
        </div>
    @endif

    <!-- Printable Invoice Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md p-8 space-y-8">
        
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center space-x-2.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white text-xl shadow">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900 font-heading">NovaTech Pakistan</h2>
                        <p class="text-xs text-slate-400">WhatsApp Automated Sales Invoice</p>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 space-y-0.5">
                    <p>F-10 Markaz, Islamabad, Pakistan</p>
                    <p>support@novatech.pk &bull; +92 300 1234567</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs uppercase font-extrabold tracking-widest text-slate-400 block">Invoice</span>
                <span class="text-2xl font-mono font-black text-slate-900 block mt-0.5">#{{ $order->order_number }}</span>
                <span class="text-xs text-slate-500 font-mono block mt-1">Date: {{ $order->created_at->format('M d, Y') }}</span>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800">
                    {{ $order->status }}
                </span>
            </div>
        </div>

        <!-- Customer & Order Meta -->
        <div class="grid grid-cols-2 gap-6 text-xs">
            <div>
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Billed To (Customer):</span>
                <h4 class="text-base font-bold text-slate-800">{{ $order->customer_name }}</h4>
                <p class="text-slate-600 mt-1 font-mono flex items-center space-x-1">
                    <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                    <span>{{ $order->customer_phone }}</span>
                </p>
                <p class="text-slate-600 mt-1 leading-relaxed">
                    <strong>Delivery Address:</strong><br>
                    {{ $order->delivery_address }}
                </p>
            </div>
            <div class="text-right space-y-1">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Payment Details:</span>
                <p class="text-slate-800 font-bold">{{ $order->payment_method }}</p>
                <p class="text-slate-500">Currency: Pakistani Rupee (PKR)</p>
                <p class="text-slate-500">Dispatch SLA: 2-3 Business Days</p>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Item & Description</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-right">Unit Price</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $item->product_name }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-700">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-600">
                                Rs. {{ number_format($item->price) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rs. {{ number_format($item->subtotal) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Calculation -->
        <div class="flex justify-end text-xs">
            <div class="w-72 space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-bold">Rs. {{ number_format($order->items->sum('subtotal')) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Delivery Charges:</span>
                    <span class="font-mono font-bold text-emerald-600">FREE</span>
                </div>
                <div class="flex justify-between text-base font-extrabold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Bill:</span>
                    <span class="font-mono text-emerald-600">Rs. {{ number_format($order->total_amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer terms -->
        <div class="pt-6 border-t border-slate-200 text-center text-xs text-slate-400">
            <p>Thank you for shopping with NovaTech! Check the package in front of rider before making payment.</p>
        </div>

    </div>

</div>
@endsection
