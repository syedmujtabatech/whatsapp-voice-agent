@extends('layouts.app')

@section('title', 'Voice Logs & Audio Transcripts')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-rose-600">
                <i class="fa-solid fa-microphone-lines"></i>
                <span>Speech & NLP Logs</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">WhatsApp Voice Logs & Transcripts</h1>
            <p class="text-sm text-slate-500 mt-1">Full audit trail of customer voice notes, STT accuracy, and AI reasoning.</p>
        </div>
        <a href="{{ route('simulator.index') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center space-x-2 transition shadow-md shadow-emerald-500/20">
            <i class="fa-solid fa-headset"></i>
            <span>Test Simulator</span>
        </a>
    </div>

    <!-- Conversations Stream -->
    <div class="space-y-4">
        @forelse($conversations as $conv)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                
                <!-- Session Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="font-bold text-slate-800 text-sm">{{ $conv->customer_name ?: 'Anonymous Customer' }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                    {{ $conv->channel }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $conv->phone_number ?: 'Simulator Client' }} &bull; Session: {{ $conv->session_id }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 font-mono">
                        {{ $conv->created_at->diffForHumans() }}
                    </div>
                </div>

                <!-- Messages in this session -->
                <div class="space-y-3">
                    @foreach($conv->messages as $msg)
                        <div class="p-3.5 rounded-xl {{ $msg->sender === 'user' ? 'bg-slate-50 border border-slate-200/70' : 'bg-emerald-50/60 border border-emerald-100' }} space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="{{ $msg->sender === 'user' ? 'text-slate-700' : 'text-emerald-700' }} flex items-center space-x-1.5">
                                    <i class="fa-solid {{ $msg->sender === 'user' ? 'fa-microphone text-rose-500' : 'fa-robot text-emerald-600' }}"></i>
                                    <span>{{ $msg->sender === 'user' ? 'Customer Voice' : 'AI Agent Reply' }}</span>
                                </span>
                                @if($msg->intent)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">
                                        Intent: {{ $msg->intent }}
                                    </span>
                                @endif
                            </div>

                            @if($msg->transcript)
                                <p class="text-xs text-slate-800 italic font-mono">
                                    "{{ $msg->transcript }}"
                                </p>
                            @endif

                            @if($msg->response_text)
                                <p class="text-xs text-emerald-950 bg-white p-2.5 rounded-lg border border-emerald-200/60 whitespace-pre-line">
                                    <strong>AI:</strong> {{ $msg->response_text }}
                                </p>
                            @endif

                            @if($msg->entities && count($msg->entities) > 0)
                                <div class="text-[11px] font-mono text-slate-500 pt-1">
                                    <strong>Extracted Entities:</strong> {{ json_encode($msg->entities) }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                No voice sessions logged yet. Test out the simulator to generate voice logs!
            </div>
        @endforelse
    </div>

</div>
@endsection
