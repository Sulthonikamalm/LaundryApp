@extends('layouts.minimal', ['title' => 'Login Kurir - SiLaundry'])

@section('content')
{{-- 
    DeepUI Pro: Login Kurir
    Design System: Premium Glassmorphism + Clean Typography
    Mobile-First, Touch-Optimized
--}}
<div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    
    {{-- Ambient Background --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-emerald-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-teal-400/15 rounded-full blur-[100px]"></div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 relative z-10">
        <div class="w-full max-w-sm">
            
            {{-- Brand Identity --}}
            <header class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl mb-6 shadow-lg shadow-emerald-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125v-4.653c0-.398-.211-.767-.552-.982l-4.348-2.734a1.125 1.125 0 00-1.2 0l-4.348 2.734a1.125 1.125 0 00-.552.982v4.653c0 .621.504 1.125 1.125 1.125h9.75z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Portal Kurir</h1>
                <p class="text-slate-400 text-sm mt-2">Masuk dengan akun kurir Anda</p>
            </header>

            {{-- Login Card --}}
            <div class="bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
                
                {{-- Error Alert --}}
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-red-200">
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
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="username"
                                value="{{ old('username') }}"
                                class="w-full pl-12 pr-4 py-4 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all"
                                placeholder="Masukkan username"
                                required
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    {{-- PIN Field --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                            PIN Akses
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="pin"
                                id="pinInput"
                                class="w-full pl-12 pr-4 py-4 bg-slate-800/50 border border-slate-700 rounded-xl text-white text-center text-2xl tracking-[0.5em] font-mono placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all"
                                placeholder="••••••"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        
                        {{-- PIN Dots Indicator --}}
                        <div class="flex justify-center gap-3 mt-4" id="pinDots">
                            @for($i = 0; $i < 6; $i++)
                            <div class="w-3 h-3 rounded-full bg-slate-700 border border-slate-600 transition-all duration-200" data-dot="{{ $i }}"></div>
                            @endfor
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk
                        </span>
                    </button>
                </form>
            </div>

            {{-- Footer Help --}}
            <p class="mt-8 text-center text-sm text-slate-500">
                Lupa PIN? <span class="text-emerald-400">Hubungi Owner</span>
            </p>
        </div>
    </div>
    
    {{-- Bottom Branding --}}
    <footer class="py-6 text-center">
        <p class="text-slate-600 text-xs">© {{ date('Y') }} SiLaundry</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
/**
 * DeepJS Pro: Login Kurir
 * - PIN input validation & visual feedback
 * - Auto-submit on complete PIN
 * - Loading states
 * - Error shake animation
 */
(function() {
    'use strict';

    const form = document.getElementById('loginForm');
    const pinInput = document.getElementById('pinInput');
    const submitBtn = document.getElementById('submitBtn');
    const pinDots = document.querySelectorAll('#pinDots [data-dot]');
    const loginCard = document.querySelector('.backdrop-blur-xl');

    // PIN Input Handler
    if (pinInput) {
        pinInput.addEventListener('input', (e) => {
            // Allow only numbers
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            updatePinDots(e.target.value.length);
            
            // Haptic feedback
            if ('vibrate' in navigator) navigator.vibrate(5);
        });

        pinInput.addEventListener('keydown', (e) => {
            // Prevent non-numeric keys except backspace, delete, tab
            if (!/[\d]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    function updatePinDots(length) {
        pinDots.forEach((dot, i) => {
            if (i < length) {
                dot.classList.remove('bg-slate-700', 'border-slate-600');
                dot.classList.add('bg-emerald-500', 'border-emerald-400', 'scale-110');
            } else {
                dot.classList.add('bg-slate-700', 'border-slate-600');
                dot.classList.remove('bg-emerald-500', 'border-emerald-400', 'scale-110');
            }
        });

        // Auto-submit when 6 digits complete
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
            submitBtn.innerHTML = `
                <span class="flex items-center justify-center gap-2">
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
    const hasErrors = document.querySelector('.bg-red-500\\/10');
    if (hasErrors && loginCard) {
        loginCard.style.animation = 'shake 0.5s ease-in-out';
        if ('vibrate' in navigator) navigator.vibrate([100, 50, 100]);
        
        // Focus PIN and clear
        if (pinInput) {
            pinInput.value = '';
            pinInput.focus();
            updatePinDots(0);
        }
    }

    console.log('🔐 Driver Login Ready');
})();
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
    20%, 40%, 60%, 80% { transform: translateX(4px); }
}
</style>
@endpush
