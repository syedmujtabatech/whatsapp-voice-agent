@extends('layouts.app')

@section('title', 'WhatsApp Voice AI Simulator - Live Demo')

@section('styles')
<style>
    .wa-chat-bg {
        background-color: #EFEAE2;
        background-image: radial-gradient(#d3c7b8 1px, transparent 1px);
        background-size: 20px 20px;
    }
    .sound-wave-bar {
        animation: sound-bounce 1s ease-in-out infinite alternate;
    }
    @keyframes sound-bounce {
        0% { height: 4px; }
        100% { height: 24px; }
    }
</style>
@endsection

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-6">

    <!-- Top Banner for Presentation -->
    <div class="mb-6 bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 rounded-2xl p-4 sm:p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-emerald-500/20">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    Live Interactive Demo
                </span>
                <span class="text-xs text-slate-300">Targeting South Asian & Middle Eastern WhatsApp E-Commerce</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1">WhatsApp Voice Agent (Roman-Urdu AI)</h1>
            <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                Customers send voice notes in Roman-Urdu or Urdu. The AI analyzes speech, checks live inventory, answers product queries, and books Cash-on-Delivery orders automatically.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="resetConversation()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 flex items-center space-x-2 transition shadow-sm">
                <i class="fa-solid fa-rotate-right"></i>
                <span>Reset Chat Session</span>
            </button>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold flex items-center space-x-2 transition shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>View Placed Orders</span>
            </a>
        </div>
    </div>

    <!-- 3-Column Layout: Presets / Chat Simulator / AI Inspector -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Quick Demo Presets & Live Catalog (Col 3) -->
        <div class="lg:col-span-3 space-y-5">
            <!-- Preset Scenarios -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-heading font-bold text-slate-800 text-sm flex items-center space-x-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                        <span>1-Click Voice Presets</span>
                    </h3>
                    <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">For Fast Demo</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Click any preset below to simulate an incoming customer voice note immediately:</p>

                <div class="space-y-2.5">
                    @foreach($presets as $preset)
                        <button onclick="runPreset({{ json_encode($preset['text']) }})" 
                                class="w-full text-left p-3 rounded-xl border border-slate-200/80 hover:border-emerald-500 hover:bg-emerald-50/40 transition group">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-xs text-slate-800 group-hover:text-emerald-700">{{ $preset['title'] }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-medium bg-slate-100 text-slate-600 group-hover:bg-emerald-100 group-hover:text-emerald-800">
                                    {{ $preset['badge'] }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2 italic font-mono bg-slate-50 p-1.5 rounded-lg border border-slate-100">
                                "{{ $preset['text'] }}"
                            </p>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Active Store Catalog Snippet -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-heading font-bold text-slate-800 text-sm flex items-center space-x-2">
                        <i class="fa-solid fa-boxes-stacked text-indigo-500"></i>
                        <span>Live Store Inventory</span>
                    </h3>
                    <a href="{{ route('admin.products.index') }}" class="text-[11px] text-indigo-600 hover:underline font-semibold">Manage</a>
                </div>
                <div class="space-y-3">
                    @foreach($products->take(4) as $prod)
                        <div class="flex items-center space-x-3 p-2 rounded-xl border border-slate-100 bg-slate-50/60">
                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 truncate">{{ $prod->name }}</h4>
                                <div class="flex items-center space-x-2 text-[11px] mt-0.5">
                                    <span class="text-emerald-600 font-extrabold">Rs. {{ number_format($prod->effective_price) }}</span>
                                    <span class="text-slate-400">&bull;</span>
                                    <span class="text-slate-500">Stock: <strong class="{{ $prod->stock > 0 ? 'text-slate-700' : 'text-rose-600' }}">{{ $prod->stock }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- MIDDLE COLUMN: Realistic WhatsApp Web Chat (Col 5) -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col h-[760px]">
                
                <!-- WhatsApp Header -->
                <div class="bg-wa-teal text-white px-4 py-3 flex items-center justify-between shadow-md z-10">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-emerald-700 flex items-center justify-center font-bold text-white text-base shadow border-2 border-white/30">
                                NT
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-400 border-2 border-wa-teal"></span>
                        </div>
                        <div>
                            <div class="flex items-center space-x-1.5">
                                <span class="font-heading font-bold text-sm tracking-wide">{{ $storeName }}</span>
                                <i class="fa-solid fa-circle-check text-emerald-300 text-xs" title="Official WhatsApp Verified Business"></i>
                            </div>
                            <p id="chat-status" class="text-[11px] text-emerald-100 flex items-center space-x-1">
                                <span>online</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-emerald-100 text-sm">
                        <button title="Voice Audio Reply Mode" onclick="toggleAudioSpeech()" id="btn-audio-toggle" class="p-2 rounded-full hover:bg-emerald-800/60 transition text-emerald-200">
                            <i class="fa-solid fa-volume-high text-base"></i>
                        </button>
                        <button title="Options" class="p-2 rounded-full hover:bg-emerald-800/60 transition">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                    </div>
                </div>

                <!-- Chat Feed -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 wa-chat-bg">
                    
                    <!-- Date Pill -->
                    <div class="flex justify-center">
                        <span class="px-3 py-1 rounded-lg bg-white/80 backdrop-blur-sm text-[11px] text-slate-500 font-medium shadow-sm">
                            TODAY &bull; ENCRYPTED CHAT
                        </span>
                    </div>

                    <!-- Welcome Bot Message -->
                    <div class="flex items-start justify-start max-w-[85%]">
                        <div class="bg-white rounded-2xl rounded-tl-none p-3.5 shadow-sm border border-slate-200/60 space-y-2">
                            <div class="flex items-center space-x-2 text-wa-dark font-bold text-xs">
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                <span>{{ $storeName }} AI Agent</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Assalam o alaikum! 🛍️ Main {{ $storeName }} ka AI Voice Sales Assistant hoon. Aap mujhe Roman Urdu ya Urdu mein voice note bhej kar kisi bhi product ka rate, availability pooch sakte hain ya direct Cash on Delivery par order book karwa sakte hain.
                            </p>
                            <div class="flex justify-end">
                                <span class="text-[10px] text-slate-400">10:00 AM</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Typing / Processing Indicator (Hidden by default) -->
                <div id="typing-indicator" class="hidden px-4 py-2 bg-slate-100/90 border-t border-slate-200 flex items-center space-x-2 text-xs text-slate-600">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce"></div>
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce" style="animation-delay: 0.15s"></div>
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce" style="animation-delay: 0.3s"></div>
                    </div>
                    <span id="typing-text" class="italic text-[11px] font-medium text-slate-500">Agent is analyzing voice note...</span>
                </div>

                <!-- Bottom Input Bar -->
                <div class="bg-slate-100 p-3 border-t border-slate-200 flex items-center space-x-2">
                    
                    <!-- Text input -->
                    <div class="flex-1 relative">
                        <input type="text" id="user-text-input" 
                               placeholder="Type Roman-Urdu or use the mic button..." 
                               onkeydown="if(event.key === 'Enter') sendTextMessage()"
                               class="w-full bg-white rounded-full py-2.5 pl-4 pr-10 text-xs text-slate-800 placeholder-slate-400 border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <button onclick="sendTextMessage()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-600 p-1">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </div>

                    <!-- Prominent Microphone Button -->
                    <div class="relative">
                        <button id="mic-btn" onclick="toggleMicRecording()" 
                                class="w-11 h-11 rounded-full bg-wa-teal hover:bg-emerald-600 text-white flex items-center justify-center text-lg shadow-md hover:shadow-lg transition active:scale-95 relative z-10"
                                title="Click to record your voice">
                            <i id="mic-icon" class="fa-solid fa-microphone"></i>
                        </button>
                        <!-- Pulsing ring for active recording -->
                        <span id="mic-ring" class="hidden absolute inset-0 rounded-full bg-rose-500 animate-pulse-ring z-0"></span>
                    </div>

                </div>

            </div>
        </div>

        <!-- RIGHT COLUMN: Real-Time AI & Intent Inspector (Col 4) -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- Live Audio & Intent Inspector Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <h3 class="font-heading font-bold text-slate-800 text-sm">AI Voice & Intent Engine</h3>
                    </div>
                    <span id="inspector-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase tracking-wide">
                        IDLE
                    </span>
                </div>

                <!-- Live Waveform Visualizer -->
                <div class="mb-4 bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                    <div class="text-[11px] text-slate-400 flex items-center justify-between mb-2">
                        <span>Audio Stream</span>
                        <span id="audio-timer" class="font-mono text-emerald-400">00:00</span>
                    </div>
                    <div id="waveform-box" class="h-10 flex items-center justify-center space-x-1.5 overflow-hidden">
                        <div class="w-1 bg-slate-700 h-2 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-4 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-6 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-3 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-8 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-4 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-2 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-6 rounded-full"></div>
                        <div class="w-1 bg-slate-700 h-3 rounded-full"></div>
                    </div>
                </div>

                <!-- STT Transcript -->
                <div class="mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                        Speech-to-Text Transcript (Whisper / Speech API):
                    </label>
                    <div id="inspector-transcript" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 italic min-h-[48px]">
                        Waiting for voice note input...
                    </div>
                </div>

                <!-- Detected Intent & Confidence -->
                <div class="mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                        Classified Intent:
                    </label>
                    <div id="inspector-intent" class="flex items-center space-x-2">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">
                            None detected yet
                        </span>
                    </div>
                </div>

                <!-- Extracted Entities -->
                <div class="mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                        Extracted Entities (Slot-Filling):
                    </label>
                    <div id="inspector-entities" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-600 min-h-[50px] whitespace-pre-wrap">
                        {}
                    </div>
                </div>

                <!-- Real-Time Order Slip Box (Appears when order is placed) -->
                <div id="order-slip-card" class="hidden p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-emerald-900 flex items-center space-x-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>Voice Order Booked!</span>
                        </span>
                        <span id="slip-order-num" class="text-[11px] font-mono font-bold bg-white px-2 py-0.5 rounded text-emerald-800 border border-emerald-300">
                            #ORD-XXXXX
                        </span>
                    </div>
                    <div class="text-xs text-emerald-950 space-y-1 mb-3">
                        <p><strong>Customer:</strong> <span id="slip-name">-</span></p>
                        <p><strong>Phone:</strong> <span id="slip-phone">-</span></p>
                        <p><strong>Address:</strong> <span id="slip-address">-</span></p>
                        <p><strong>Total Bill:</strong> <span id="slip-total" class="font-bold text-emerald-700">Rs. 0</span> (Cash on Delivery)</p>
                    </div>
                    <a id="slip-link" href="{{ route('admin.orders.index') }}" class="block text-center w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                        Open in Order Management &rarr;
                    </a>
                </div>

            </div>

            <!-- Architecture Info Card for Presenters -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-5 text-white border border-slate-700 shadow-sm text-xs space-y-3">
                <div class="flex items-center space-x-2 text-emerald-400 font-bold">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Production Architecture Highlights</span>
                </div>
                <ul class="space-y-2 text-slate-300">
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5"></i>
                        <span><strong>Meta WhatsApp Cloud API:</strong> Real webhook listener with SHA256 HMAC verification & OGG audio parsing.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5"></i>
                        <span><strong>Roman-Urdu NLP:</strong> Understands slang, Pakistani phone numbers (03xx), city addresses, and multi-turn slot filling.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5"></i>
                        <span><strong>Instant Voice Audio Reply:</strong> Synthesizes natural spoken replies back to WhatsApp.</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    const sessionId = 'sim_session_' + Math.random().toString(36).substring(2, 9);
    let isRecording = false;
    let mediaRecorder = null;
    let audioChunks = [];
    let recordInterval = null;
    let recordSeconds = 0;
    let audioSpeechEnabled = true;

    // Web Speech API for live browser STT fallback
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        // Supports Urdu / Roman Urdu / Hindi / English
        recognition.lang = 'ur-PK'; 
        
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            console.log('Recognized speech:', transcript);
            handleUserMessage(transcript, true);
        };
        recognition.onerror = function(err) {
            console.warn('Speech recognition warning:', err);
        };
    }

    function toggleAudioSpeech() {
        audioSpeechEnabled = !audioSpeechEnabled;
        const btn = document.getElementById('btn-audio-toggle');
        if (audioSpeechEnabled) {
            btn.classList.add('text-emerald-200');
            btn.classList.remove('text-slate-400');
            btn.innerHTML = '<i class="fa-solid fa-volume-high text-base"></i>';
        } else {
            btn.classList.remove('text-emerald-200');
            btn.classList.add('text-slate-400');
            btn.innerHTML = '<i class="fa-solid fa-volume-xmark text-base"></i>';
            window.speechSynthesis.cancel();
        }
    }

    function speakText(text) {
        if (!audioSpeechEnabled || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        
        // Clean markdown and numbers for voice synthesis
        const cleanText = text.replace(/[*_#~`]/g, '');
        const utterance = new SpeechSynthesisUtterance(cleanText);
        utterance.rate = 1.0;
        utterance.pitch = 1.0;

        // Try to pick Hindi / Urdu voice if available in browser, else default
        const voices = window.speechSynthesis.getVoices();
        const urduVoice = voices.find(v => v.lang.includes('ur') || v.lang.includes('hi') || v.lang.includes('en-IN'));
        if (urduVoice) {
            utterance.voice = urduVoice;
        }

        window.speechSynthesis.speak(utterance);
    }

    function toggleMicRecording() {
        if (!isRecording) {
            startRecording();
        } else {
            stopRecording();
        }
    }

    async function startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            isRecording = true;
            audioChunks = [];

            // UI updates
            document.getElementById('mic-ring').classList.remove('hidden');
            document.getElementById('mic-btn').classList.replace('bg-wa-teal', 'bg-rose-600');
            document.getElementById('mic-icon').classList.replace('fa-microphone', 'fa-stop');
            document.getElementById('chat-status').innerHTML = '<span class="text-emerald-300 font-bold">recording audio...</span>';
            document.getElementById('inspector-badge').textContent = 'RECORDING';
            document.getElementById('inspector-badge').className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 animate-pulse';

            // Start Waveform animation
            animateWaveform(true);

            // Timer
            recordSeconds = 0;
            recordInterval = setInterval(() => {
                recordSeconds++;
                const mins = String(Math.floor(recordSeconds / 60)).padStart(2, '0');
                const secs = String(recordSeconds % 60).padStart(2, '0');
                document.getElementById('audio-timer').textContent = `${mins}:${secs}`;
            }, 1000);

            // MediaRecorder
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = e => {
                if (e.data.size > 0) audioChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                // If speech recognition was active
                stream.getTracks().forEach(track => track.stop());
            };

            mediaRecorder.start();

            // Also trigger speech recognition if supported
            if (recognition) {
                try { recognition.start(); } catch(e){}
            }

        } catch (err) {
            alert('Microphone access is required to record real voice. Alternatively, click any preset button on the left!');
            console.error('Mic error:', err);
        }
    }

    function stopRecording() {
        if (!isRecording) return;
        isRecording = false;

        clearInterval(recordInterval);
        document.getElementById('mic-ring').classList.add('hidden');
        document.getElementById('mic-btn').classList.replace('bg-rose-600', 'bg-wa-teal');
        document.getElementById('mic-icon').classList.replace('fa-stop', 'fa-microphone');
        document.getElementById('chat-status').textContent = 'online';
        animateWaveform(false);

        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        if (recognition) {
            try { recognition.stop(); } catch(e){}
        }

        // If recognition hasn't fired in 1.5 seconds, send simulated speech sample
        setTimeout(() => {
            if (document.getElementById('inspector-badge').textContent === 'RECORDING') {
                handleUserMessage("Assalam o alaikum bhai, Smart Watch Ultra 2 available hai aur price kya hai?", true);
            }
        }, 800);
    }

    function animateWaveform(active) {
        const box = document.getElementById('waveform-box');
        if (active) {
            box.innerHTML = `
                <div class="w-1 bg-emerald-400 sound-wave-bar h-4 rounded-full" style="animation-delay: 0.1s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-7 rounded-full" style="animation-delay: 0.3s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-3 rounded-full" style="animation-delay: 0.2s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-8 rounded-full" style="animation-delay: 0.4s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-5 rounded-full" style="animation-delay: 0.15s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-9 rounded-full" style="animation-delay: 0.25s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-4 rounded-full" style="animation-delay: 0.35s"></div>
                <div class="w-1 bg-emerald-400 sound-wave-bar h-6 rounded-full" style="animation-delay: 0.05s"></div>
            `;
        } else {
            box.innerHTML = `
                <div class="w-1 bg-slate-700 h-2 rounded-full"></div>
                <div class="w-1 bg-slate-700 h-4 rounded-full"></div>
                <div class="w-1 bg-slate-700 h-6 rounded-full"></div>
                <div class="w-1 bg-slate-700 h-3 rounded-full"></div>
                <div class="w-1 bg-slate-700 h-8 rounded-full"></div>
                <div class="w-1 bg-slate-700 h-4 rounded-full"></div>
                <div class="w-1 bg-slate-700 h-2 rounded-full"></div>
            `;
        }
    }

    function runPreset(text) {
        handleUserMessage(text, true);
    }

    function sendTextMessage() {
        const input = document.getElementById('user-text-input');
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        handleUserMessage(text, false);
    }

    function appendUserMessage(text, isVoice = false) {
        const container = document.getElementById('chat-messages');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const div = document.createElement('div');
        div.className = 'flex items-start justify-end';

        if (isVoice) {
            div.innerHTML = `
                <div class="bg-wa-bubble rounded-2xl rounded-tr-none p-3.5 shadow-sm border border-emerald-200/60 max-w-[85%] space-y-2">
                    <div class="flex items-center space-x-3">
                        <button class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shadow">
                            <i class="fa-solid fa-play"></i>
                        </button>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center space-x-1">
                                <div class="w-1.5 h-3 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-5 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-2 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-6 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-4 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-7 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-3 bg-emerald-700 rounded-full"></div>
                                <div class="w-1.5 h-5 bg-emerald-700 rounded-full"></div>
                            </div>
                            <span class="text-[10px] text-slate-500 font-mono">0:04 &bull; Voice Note</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-600 italic bg-white/70 p-1.5 rounded-lg border border-emerald-100">
                        "${text}"
                    </p>
                    <div class="flex items-center justify-end space-x-1 text-[10px] text-slate-400">
                        <span>${time}</span>
                        <i class="fa-solid fa-check-double text-sky-500"></i>
                    </div>
                </div>
            `;
        } else {
            div.innerHTML = `
                <div class="bg-wa-bubble rounded-2xl rounded-tr-none p-3 shadow-sm border border-emerald-200/60 max-w-[85%] space-y-1">
                    <p class="text-xs text-slate-800">${text}</p>
                    <div class="flex items-center justify-end space-x-1 text-[10px] text-slate-400">
                        <span>${time}</span>
                        <i class="fa-solid fa-check-double text-sky-500"></i>
                    </div>
                </div>
            `;
        }

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function appendBotResponse(replyText, order = null, matchedProduct = null) {
        const container = document.getElementById('chat-messages');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const div = document.createElement('div');
        div.className = 'flex items-start justify-start max-w-[85%]';

        let orderHtml = '';
        if (order) {
            orderHtml = `
                <div class="mt-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-300 space-y-1">
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                        <span>Invoice Confirmation</span>
                        <span class="font-mono text-emerald-800">#${order.order_number}</span>
                    </div>
                    <p class="text-[11px] text-emerald-800">Bill: <strong>Rs. ${Number(order.total_amount).toLocaleString()}</strong> (COD)</p>
                    <p class="text-[11px] text-emerald-700">Rider dispatch 2-3 working days mein hoga.</p>
                </div>
            `;
        }

        div.innerHTML = `
            <div class="bg-white rounded-2xl rounded-tl-none p-3.5 shadow-sm border border-slate-200/60 space-y-2">
                <div class="flex items-center justify-between text-wa-dark text-xs font-bold">
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>NovaTech Assistant</span>
                    </div>
                    <button onclick="speakText(${JSON.stringify(replyText)})" class="text-emerald-600 hover:text-emerald-800 text-[11px] flex items-center space-x-1">
                        <i class="fa-solid fa-volume-high"></i>
                        <span>Listen Voice</span>
                    </button>
                </div>
                <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">${replyText}</p>
                ${orderHtml}
                <div class="flex justify-end">
                    <span class="text-[10px] text-slate-400">${time}</span>
                </div>
            </div>
        `;

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;

        // Auto speak aloud
        speakText(replyText);
    }

    async function handleUserMessage(text, isVoice = false) {
        appendUserMessage(text, isVoice);

        // Show typing indicator
        const typing = document.getElementById('typing-indicator');
        typing.classList.remove('hidden');
        document.getElementById('typing-text').textContent = isVoice ? 'Transcribing & understanding Roman-Urdu speech...' : 'Agent is typing...';

        // Update inspector status
        document.getElementById('inspector-badge').textContent = 'PROCESSING';
        document.getElementById('inspector-badge').className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800';
        document.getElementById('inspector-transcript').textContent = `"${text}"`;

        try {
            const response = await fetch("{{ route('simulator.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    text: text,
                    phone: '+92 300 7891234'
                })
            });

            const data = await response.json();
            typing.classList.add('hidden');

            if (data.success) {
                // Update Inspector
                document.getElementById('inspector-badge').textContent = 'COMPLETED';
                document.getElementById('inspector-badge').className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';

                // Intent Badge
                const intentColors = {
                    'book_order': 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'price_check': 'bg-blue-100 text-blue-800 border-blue-300',
                    'stock_check': 'bg-purple-100 text-purple-800 border-purple-300',
                    'order_status': 'bg-amber-100 text-amber-800 border-amber-300',
                    'faq': 'bg-cyan-100 text-cyan-800 border-cyan-300',
                    'greeting': 'bg-slate-100 text-slate-700 border-slate-300'
                };
                const colorClass = intentColors[data.intent] || 'bg-slate-100 text-slate-700 border-slate-300';
                document.getElementById('inspector-intent').innerHTML = `
                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold border ${colorClass} uppercase tracking-wider flex items-center space-x-1.5">
                        <i class="fa-solid fa-tag"></i>
                        <span>${data.intent}</span>
                    </span>
                `;

                // Entities
                document.getElementById('inspector-entities').textContent = JSON.stringify(data.entities, null, 2);

                // If Order created
                if (data.order) {
                    const slip = document.getElementById('order-slip-card');
                    slip.classList.remove('hidden');
                    document.getElementById('slip-order-num').textContent = `#${data.order.order_number}`;
                    document.getElementById('slip-name').textContent = data.order.customer_name;
                    document.getElementById('slip-phone').textContent = data.order.customer_phone;
                    document.getElementById('slip-address').textContent = data.order.delivery_address;
                    document.getElementById('slip-total').textContent = `Rs. ${Number(data.order.total_amount).toLocaleString()}`;
                }

                // Append Bot Response
                appendBotResponse(data.reply, data.order, data.matched_product);
            }
        } catch (error) {
            typing.classList.add('hidden');
            console.error('API Error:', error);
            appendBotResponse("Bhai connection error aaya hai. Barahe karam dobara koshish karein.");
        }
    }

    async function resetConversation() {
        if (!confirm('Are you sure you want to reset this chat session?')) return;
        await fetch("{{ route('simulator.reset') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ session_id: sessionId })
        });
        window.location.reload();
    }
</script>
@endsection
