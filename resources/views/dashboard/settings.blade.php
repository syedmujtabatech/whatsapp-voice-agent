@extends('layouts.app')

@section('title', 'API & Bot Settings')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                <i class="fa-solid fa-sliders"></i>
                <span>Configuration & Credentials</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Bot Settings & WhatsApp API</h1>
            <p class="text-sm text-slate-500 mt-1">Manage Meta WhatsApp Cloud API credentials, AI prompt personas, and store delivery rules.</p>
        </div>
        <button onclick="document.getElementById('settingsForm').submit()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center space-x-2 transition shadow-md shadow-emerald-500/20">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save All Settings</span>
        </button>
    </div>

    <form id="settingsForm" action="{{ route('admin.settings.save') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Webhook Integration Card -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white border border-slate-700 shadow-md space-y-4">
            <div class="flex items-center space-x-2.5 text-emerald-400 font-bold text-sm">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span>Official Meta WhatsApp Cloud API Webhook Setup</span>
            </div>
            <p class="text-xs text-slate-300 leading-relaxed">
                Connect this agent to a real WhatsApp Business Number via Meta Developers Dashboard. Configure the Webhook with the details below:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="bg-slate-950/70 p-3.5 rounded-xl border border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Webhook Callback URL:</span>
                    <div class="flex items-center justify-between font-mono text-xs text-emerald-400 font-bold">
                        <span>{{ url('/api/whatsapp/webhook') }}</span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/whatsapp/webhook') }}'); alert('Copied URL!')" class="text-slate-400 hover:text-white text-xs">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-slate-950/70 p-3.5 rounded-xl border border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Webhook Verify Token:</span>
                    <div class="flex items-center justify-between font-mono text-xs text-emerald-400 font-bold">
                        <span>{{ $settings['whatsapp_verify_token'] ?? 'WHATSAPP_VOICE_VERIFY_2026' }}</span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $settings['whatsapp_verify_token'] ?? 'WHATSAPP_VOICE_VERIFY_2026' }}'); alert('Copied Token!')" class="text-slate-400 hover:text-white text-xs">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Credentials Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="font-heading font-bold text-slate-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-key text-emerald-600"></i>
                <span>Meta WhatsApp Cloud API Credentials</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">WhatsApp Phone Number ID</label>
                    <input type="text" name="whatsapp_phone_number_id" value="{{ $settings['whatsapp_phone_number_id'] ?? '' }}" placeholder="e.g. 1048291048201" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">WhatsApp Verify Token</label>
                    <input type="text" name="whatsapp_verify_token" value="{{ $settings['whatsapp_verify_token'] ?? 'WHATSAPP_VOICE_VERIFY_2026' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                </div>
            </div>

            <div class="text-xs">
                <label class="font-bold text-slate-700 block mb-1">WhatsApp System User Permanent Access Token</label>
                <input type="password" name="whatsapp_access_token" value="{{ $settings['whatsapp_access_token'] ?? '' }}" placeholder="EAAG..." class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>
        </div>

        <!-- AI Engine & Persona Settings -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="font-heading font-bold text-slate-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-brain text-indigo-600"></i>
                <span>AI Speech Engine & Roman-Urdu Persona</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">OpenAI API Key (Whisper STT / GPT-4o)</label>
                    <input type="password" name="openai_api_key" value="{{ $settings['openai_api_key'] ?? '' }}" placeholder="sk-..." class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Google Gemini API Key (Multimodal Audio)</label>
                    <input type="password" name="gemini_api_key" value="{{ $settings['gemini_api_key'] ?? '' }}" placeholder="AIzaSy..." class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                </div>
            </div>

            <div class="text-xs">
                <label class="font-bold text-slate-700 block mb-1">Roman-Urdu AI Sales Assistant Persona (System Prompt)</label>
                <textarea name="system_persona" rows="4" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 leading-relaxed font-mono">{{ $settings['system_persona'] ?? '' }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">This prompt dictates the polite tone, Roman-Urdu phrasing, and sales negotiation rules.</p>
            </div>
        </div>

        <!-- Store Rules & Delivery Fees -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="font-heading font-bold text-slate-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-truck-fast text-amber-500"></i>
                <span>Store Profile & Delivery Rules</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Store Name</label>
                    <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'NovaTech Pakistan' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Store Phone / WhatsApp</label>
                    <input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '+92 300 1234567' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-amber-500 focus:border-amber-500 font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Origin City</label>
                    <input type="text" name="store_city" value="{{ $settings['store_city'] ?? 'Lahore' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-amber-500 focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Standard Delivery Fee (PKR)</label>
                    <input type="number" name="delivery_fee" value="{{ $settings['delivery_fee'] ?? '250' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Free Delivery Threshold (PKR)</label>
                    <input type="number" name="free_delivery_threshold" value="{{ $settings['free_delivery_threshold'] ?? '3000' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Estimated Delivery Days</label>
                    <input type="text" name="delivery_time_days" value="{{ $settings['delivery_time_days'] ?? '2-3 working days' }}" class="w-full rounded-xl border-slate-300 py-2.5 px-3 focus:ring-amber-500 focus:border-amber-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 transition">
                Save All Settings & Updates
            </button>
        </div>

    </form>

</div>
@endsection
