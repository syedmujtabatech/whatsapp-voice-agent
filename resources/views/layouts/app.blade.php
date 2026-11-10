<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WhatsApp Voice AI Agent') | NovaTech</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        wa: {
                            light: '#25D366',
                            dark: '#075E54',
                            teal: '#128C7E',
                            bg: '#EFEAE2',
                            bubble: '#E7FCE3',
                            user: '#FFFFFF'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c5cbd5;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9fa6b2;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .animate-pulse-ring {
            animation: pulse-ring 1.8s infinite ease-in-out;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900 border-b border-slate-800 text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('simulator.index') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition">
                            <i class="fa-brands fa-whatsapp text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-heading font-extrabold text-lg tracking-tight text-white">VoiceWave AI</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">WhatsApp Agent</span>
                            </div>
                            <p class="text-xs text-slate-400">Roman-Urdu E-Commerce Voice Agent</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('simulator.index') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center space-x-2 {{ request()->routeIs('simulator.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i class="fa-solid fa-headset text-emerald-300"></i>
                        <span>Live Simulator</span>
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                    </a>

                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i class="fa-solid fa-chart-pie text-indigo-400"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 {{ request()->routeIs('admin.orders.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i class="fa-solid fa-box text-amber-400"></i>
                        <span>Voice Orders</span>
                    </a>

                    <a href="{{ route('admin.conversations.index') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 {{ request()->routeIs('admin.conversations.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i class="fa-solid fa-microphone-lines text-rose-400"></i>
                        <span>Voice Logs</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 {{ request()->routeIs('admin.products.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i class="fa-solid fa-tags text-cyan-400"></i>
                        <span>Products</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i class="fa-solid fa-sliders text-slate-400"></i>
                        <span>API & Webhook</span>
                    </a>
                </nav>

                <!-- Quick Action Status -->
                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-xs">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="text-slate-300 font-medium">WhatsApp Cloud API</span>
                        <span class="text-emerald-400 font-semibold">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-slate-700">VoiceWave AI</span> &copy; {{ date('Y') }} &bull; Enterprise WhatsApp Voice Agent
            </div>
            <div class="flex items-center space-x-4 text-slate-400">
                <span>Laravel 12</span>
                <span>&bull;</span>
                <span>Meta WhatsApp Cloud API</span>
                <span>&bull;</span>
                <span>Whisper / Gemini AI</span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
