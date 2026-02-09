@extends('layouts.minimal', ['title' => 'Login Kurir - SiLaundry'])

@section('content')
<div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
    
    {{-- Premium Background Effects --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-gradient-to-br from-blue-400/20 to-indigo-500/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-gradient-to-tl from-cyan-400/15 to-blue-500/15 rounded-full blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 relative z-10">
        <div class="w-full max-w-md">
            
            {{-- Brand Identity --}}
            <header class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl mb-6 shadow-2xl shadow-blue-500/30 transform hover:scale-105 transition-transform">
                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125v-4.653c0-.398-.211-.767-.552-.982l-4.348-2.734a1.125 1.125 0 00-1.2 0l-4.348 2.734a1.125 1.125 0 00-.552.982v4.653c0 .621.504 1.125 1.125 1.125h9.75z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">Portal Kurir</h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm">Masuk dengan akun kurir Anda</p>
            </header>

            {{-- Login Card - Glassmorphism Premium --}}
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-2xl border border-white/20 dark:border-slate-700/50 rounded-3xl p-8 shadow-2xl">
                
                {{-- Error Alert --}}
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 rounded-2xl flex items-start gap-3 animate-shake">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-red-700 dark:text-red-300 font-medium">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form action="{{ route('driver.login.submit') }}" method="POST" id="loginForm" class="space-y-6">
                    @csrf
                    
                    {{-- Username Field --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">
                            Username
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="username"
                                value="{{ old('username') }}"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium"
                                placeholder="Masukkan username"
                                required
                                autocomplete="username"
                                autofocus
                            >
                        </div>
                    </div>

                    {{-- PIN Field --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">
                            PIN Akses (6 Digit)
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="pin"
                                id="pinInput"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900/50 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-center text-3xl tracking-[0.8em] font-mono placeholder-slate-400 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                placeholder="••••••"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        
                        {{-- PIN Dots Indicator --}}
                        <div class="flex justify-center gap-3 mt-5" id="pinDots">
                            @for($i = 0; $i < 6; $i++)
                            <div class="w-3 h-3 rounded-full bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 transition-all duration-300" data-dot="{{ $i }}"></div>
                            @endfor
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/40 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span id="btnText">Masuk</span>
                        </span>
                    </button>
                </form>
            </div>

            {{-- Footer Help --}}
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Lupa PIN? 
                    <span class="font-semibold text-blue-600 dark:text-blue-400">Hubungi Owner</span>
                </p>
            </div>
        </div>
    </div>
    
    {{-- Bottom Branding --}}
    <footer class="py-6 text-center relative z-10">
        <p class="text-slate-500 dark:text-slate-600 text-xs font-medium">
            © {{ date('Y') }} SiLaundry. All rights reserved.
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    const form = document.getElementById('loginForm');
    const pinInput = document.getElementById('pinInput');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const pinDots = document.querySelectorAll('#pinDots [data-dot]');

    // PIN Input Handler
    if (pinInput) {
        pinInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            updatePinDots(e.target.value.length);
            
            if ('vibrate' in navigator) navigator.vibrate(5);
        });

        pinInput.addEventListener('keydown', (e) => {
            if (!/[\d]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    function updatePinDots(length) {
        pinDots.forEach((dot, i) => {
            if (i < length) {
                dot.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'border-slate-300', 'dark:border-slate-600');
                dot.classList.add('bg-blue-500', 'border-blue-400', 'scale-125', 'shadow-lg', 'shadow-blue-500/50');
            } else {
                dot.classList.add('bg-slate-200', 'dark:bg-slate-700', 'border-slate-300', 'dark:border-slate-600');
                dot.classList.remove('bg-blue-500', 'border-blue-400', 'scale-125', 'shadow-lg', 'shadow-blue-500/50');
            }
        });

        if (length === 6) {
            setTimeout(() => form.requestSubmit(), 300);
        }
    }

    // Form Submit Handler
    if (form) {
        form.addEventListener('submit', (e) => {
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            submitBtn.disabled = true;
            btnText.innerHTML = `
                <span class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memverifikasi...
                </span>
            `;
        });
    }

    // Error Animation
    const hasErrors = document.querySelector('.animate-shake');
    if (hasErrors && pinInput) {
        if ('vibrate' in navigator) navigator.vibrate([100, 50, 100]);
        pinInput.value = '';
        pinInput.focus();
        updatePinDots(0);
    }
})();
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
    20%, 40%, 60%, 80% { transform: translateX(8px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>
@endpush
