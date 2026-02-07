@extends('layouts.minimal', ['title' => 'Login Staff Laundry'])

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-brand-black text-brand-white relative">
    
    <!-- Background Texture -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#4c7d73 1px, transparent 1px); background-size: 24px 24px;"></div>
    <div class="absolute top-0 w-full h-1/2 bg-gradient-to-b from-brand-deep/20 to-transparent pointer-events-none"></div>

    <div class="w-full max-w-sm z-10">
        <!-- Brand Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-deep rounded-2xl mb-4 text-brand-accent shadow-glow border border-brand-primary">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h1 class="font-display text-2xl font-bold tracking-wide">Portal Kurir</h1>
            <p class="text-brand-surface opacity-60 text-sm mt-1">Akses khusus staf internal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-brand-dark/50 backdrop-blur-md border border-brand-deep rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <!-- Glow Effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-accent rounded-full blur-[80px] opacity-20 pointer-events-none"></div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-sm text-red-200">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('driver.login.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-brand-surface uppercase tracking-wider mb-2 ml-1">PIN Akses</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-brand-primary group-focus-within:text-brand-accent transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input 
                            type="password" 
                            name="pin"
                            class="block w-full pl-11 pr-4 py-4 bg-brand-black/50 border border-brand-deep rounded-xl text-white placeholder-brand-surface/30 focus:ring-2 focus:ring-brand-accent/50 focus:border-brand-accent transition-all duration-200 font-mono text-center text-2xl tracking-widest"
                            placeholder="••••••"
                            maxlength="6"
                            inputmode="numeric"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <button 
                    type="submit"
                    class="w-full py-4 bg-brand-primary hover:bg-brand-medium text-white font-bold rounded-xl shadow-lg shadow-brand-primary/20 transition-all transform hover:translate-y-[-2px] active:translate-y-[0px]"
                >
                    Masuk Portal
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-brand-surface opacity-40">
            Lupa PIN? Hubungi Owner
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * DeepJS: Driver Login Interactive Features
 * - PIN Input Enhancement
 * - Loading State
 * - Shake Animation on Error
 * - Keyboard Handling
 */
(function() {
    'use strict';

    const form = document.querySelector('form');
    const pinInput = document.querySelector('input[name="pin"]');
    const submitBtn = document.querySelector('button[type="submit"]');
    const loginCard = document.querySelector('.backdrop-blur-md');

    // ============================================
    // 1. PIN INPUT ENHANCEMENT
    // ============================================
    if (pinInput) {
        // Only allow numbers
        pinInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            updatePinDots(e.target.value.length);
        });

        // Visual PIN dots indicator
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'flex justify-center gap-3 mt-4';
        dotsContainer.innerHTML = Array(6).fill(0).map((_, i) => 
            `<div class="pin-dot w-3 h-3 rounded-full bg-brand-deep border border-brand-primary transition-all duration-200" data-index="${i}"></div>`
        ).join('');
        pinInput.parentElement.parentElement.appendChild(dotsContainer);

        function updatePinDots(length) {
            document.querySelectorAll('.pin-dot').forEach((dot, i) => {
                if (i < length) {
                    dot.classList.remove('bg-brand-deep');
                    dot.classList.add('bg-brand-accent', 'scale-110');
                } else {
                    dot.classList.add('bg-brand-deep');
                    dot.classList.remove('bg-brand-accent', 'scale-110');
                }
            });

            // Auto-submit when 6 digits entered
            if (length === 6) {
                setTimeout(() => form.requestSubmit(), 300);
            }
        }

        // Keyboard haptic feedback
        pinInput.addEventListener('keydown', () => {
            if ('vibrate' in navigator) navigator.vibrate(5);
        });
    }

    // ============================================
    // 2. LOADING STATE
    // ============================================
    if (form) {
        form.addEventListener('submit', (e) => {
            // Prevent double submit
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memverifikasi...
                </span>
            `;
        });
    }

    // ============================================
    // 3. SHAKE ANIMATION ON ERROR
    // ============================================
    const errorAlert = document.querySelector('.bg-red-500\\/10');
    if (errorAlert && loginCard) {
        loginCard.classList.add('animate-shake');
        if ('vibrate' in navigator) navigator.vibrate([100, 50, 100]);
        
        // Clear PIN on error
        if (pinInput) {
            pinInput.value = '';
            pinInput.focus();
            document.querySelectorAll('.pin-dot').forEach(dot => {
                dot.classList.add('bg-brand-deep');
                dot.classList.remove('bg-brand-accent', 'scale-110');
            });
        }
    }

    // ============================================
    // 4. ENTER KEY HANDLING
    // ============================================
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && document.activeElement !== pinInput) {
            pinInput?.focus();
        }
    });

    // ============================================
    // 5. BACKGROUND PARTICLE EFFECT
    // ============================================
    const canvas = document.createElement('canvas');
    canvas.id = 'particles';
    canvas.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; opacity: 0.3;';
    document.body.prepend(canvas);

    const ctx = canvas.getContext('2d');
    let particles = [];

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 2 + 1;
            this.speedX = Math.random() * 0.5 - 0.25;
            this.speedY = Math.random() * 0.5 - 0.25;
            this.opacity = Math.random() * 0.5 + 0.2;
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
            if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
        }
        draw() {
            ctx.fillStyle = `rgba(76, 125, 115, ${this.opacity})`;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function initParticles() {
        particles = [];
        for (let i = 0; i < 50; i++) {
            particles.push(new Particle());
        }
    }

    function animateParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(p => {
            p.update();
            p.draw();
        });
        requestAnimationFrame(animateParticles);
    }

    initParticles();
    animateParticles();

    console.log('🔐 Driver Login JS Loaded');
})();
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>
@endpush
