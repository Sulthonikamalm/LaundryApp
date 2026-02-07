@extends('layouts.minimal', ['title' => 'Dashboard Driver'])

@section('content')
<div class="min-h-screen bg-brand-white pb-24">
    
    <!-- Top Bar -->
    <div class="bg-brand-black text-brand-white px-6 pt-10 pb-20 rounded-b-[40px] shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-deep rounded-full opacity-20 -mr-16 -mt-16 blur-3xl pointer-events-none"></div>
        
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-brand-surface opacity-80 text-sm font-medium tracking-wide">PORTAL KURIR</p>
                <h1 class="font-display text-3xl font-bold mt-1">{{ auth()->guard('driver')->user()->name }}</h1>
                <div class="flex items-center mt-3 space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-primary/20 text-brand-accent border border-brand-primary/30">
                        <span class="w-1.5 h-1.5 bg-brand-accent rounded-full mr-1.5 animate-pulse"></span>
                        Online
                    </span>
                    <span class="text-xs text-brand-surface opacity-50">{{ now()->format('d M Y') }}</span>
                </div>
            </div>
            <form action="{{ route('driver.logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-3 bg-white/10 hover:bg-red-500/20 text-brand-surface hover:text-red-200 rounded-2xl transition-all border border-white/10 backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Overview (Floating) -->
    <div class="px-6 -mt-12 relative z-20">
        <div class="bg-white rounded-3xl shadow-soft p-6 flex justify-between items-center border border-brand-surface/50">
            <div class="text-center w-1/2 border-r border-brand-surface">
                <p class="text-xs text-brand-dark font-bold uppercase tracking-wider opacity-60">Tugas Aktif</p>
                <p class="text-4xl font-display font-bold text-brand-black mt-2">{{ $myDeliveries->where('status', '!=', 'delivered')->count() }}</p>
            </div>
            <div class="text-center w-1/2">
                <p class="text-xs text-brand-dark font-bold uppercase tracking-wider opacity-60">Selesai</p>
                <p class="text-4xl font-display font-bold text-brand-primary mt-2">{{ $myDeliveries->where('status', 'delivered')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 mt-10 space-y-10">

        <!-- Section 1: Tugas Saya (Active) -->
        @if($myDeliveries->where('status', '!=', 'delivered')->count() > 0)
        <div>
            <h2 class="font-display text-lg font-bold text-brand-black mb-4 flex items-center">
                <span class="w-2 h-8 bg-brand-primary rounded-full mr-3"></span>
                Sedang Diantar
            </h2>
            <div class="space-y-4">
                @foreach($myDeliveries->where('status', '!=', 'delivered') as $shipment)
                <div class="bg-white rounded-3xl p-6 shadow-soft border border-brand-surface hover:border-brand-primary transition-all relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-brand-subtle rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 bg-brand-deep text-brand-white text-xs font-bold rounded-lg">{{ $shipment->transaction->transaction_code }}</span>
                            <span class="text-xs font-bold text-brand-primary uppercase tracking-wide">Sedang Jalan</span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-brand-black mb-1">{{ $shipment->transaction->customer->name }}</h3>
                        <p class="text-sm text-brand-dark leading-relaxed mb-6 opacity-80">{{ $shipment->customer_address }}</p>

                        <a href="{{ route('driver.delivery.show', $shipment->transaction_id) }}" class="flex items-center justify-center w-full py-4 bg-brand-black text-brand-white font-bold rounded-xl active:bg-brand-dark transition-all shadow-lg">
                            Lanjutkan Pengiriman
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Section 2: Siap Diantar (Pool) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-lg font-bold text-brand-black flex items-center">
                    <span class="w-2 h-8 bg-brand-surface rounded-full mr-3"></span>
                    Siap Diantar
                </h2>
                <span class="text-xs font-bold bg-brand-subtle text-brand-deep px-2 py-1 rounded-md">{{ $pendingDeliveries->count() }} items</span>
            </div>

            @if($pendingDeliveries->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingDeliveries as $trx)
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-brand-surface/60">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-brand-black text-lg">{{ $trx->customer->name }}</h3>
                                <p class="text-xs text-brand-dark opacity-60 font-mono">{{ $trx->transaction_code }}</p>
                            </div>
                            <div class="bg-brand-subtle p-2 rounded-xl text-brand-deep">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center text-xs text-brand-dark mb-6 bg-brand-bg/50 p-3 rounded-xl">
                            <svg class="w-4 h-4 mr-2 text-brand-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">{{ $trx->customer->address ?? 'Alamat tidak tersedia' }}</span>
                        </div>

                        <form action="{{ route('driver.delivery.start', $trx->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 border-2 border-brand-primary text-brand-primary font-bold rounded-xl hover:bg-brand-primary hover:text-white transition-colors">
                                Ambil Tugas Ini
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-brand-subtle/30 rounded-3xl p-8 text-center border border-dashed border-brand-surface">
                    <p class="text-brand-dark opacity-50 font-medium">Tidak ada antrian pengiriman saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * DeepJS: Driver Dashboard Interactive Features
 * - Pull to Refresh
 * - Auto Refresh (30s)
 * - Touch Feedback
 * - Swipe Actions
 * - Animations
 */
(function() {
    'use strict';

    // ============================================
    // 1. PULL TO REFRESH
    // ============================================
    let startY = 0;
    let isPulling = false;
    const threshold = 80;

    const pullIndicator = document.createElement('div');
    pullIndicator.id = 'pull-indicator';
    pullIndicator.innerHTML = `
        <div class="flex items-center justify-center py-4 text-brand-primary opacity-0 transition-all duration-300">
            <svg class="w-6 h-6 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm font-medium">Tarik untuk refresh...</span>
        </div>
    `;
    pullIndicator.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; z-index: 100; transform: translateY(-100%);';
    document.body.prepend(pullIndicator);

    document.addEventListener('touchstart', (e) => {
        if (window.scrollY === 0) {
            startY = e.touches[0].pageY;
            isPulling = true;
        }
    }, { passive: true });

    document.addEventListener('touchmove', (e) => {
        if (!isPulling) return;
        const currentY = e.touches[0].pageY;
        const diff = currentY - startY;
        
        if (diff > 0 && diff < 150) {
            const progress = Math.min(diff / threshold, 1);
            pullIndicator.style.transform = `translateY(${diff - 50}px)`;
            pullIndicator.querySelector('div').style.opacity = progress;
        }
    }, { passive: true });

    document.addEventListener('touchend', () => {
        if (!isPulling) return;
        isPulling = false;
        
        const currentTransform = pullIndicator.style.transform;
        const match = currentTransform.match(/translateY\(([^)]+)px\)/);
        
        if (match && parseFloat(match[1]) > threshold - 50) {
            // Trigger refresh
            pullIndicator.querySelector('span').textContent = 'Memperbarui...';
            vibrate(50);
            setTimeout(() => location.reload(), 500);
        } else {
            pullIndicator.style.transform = 'translateY(-100%)';
        }
    });

    // ============================================
    // 2. AUTO REFRESH (30 detik)
    // ============================================
    let autoRefreshTimer;
    let lastActivity = Date.now();

    function resetAutoRefresh() {
        lastActivity = Date.now();
        clearTimeout(autoRefreshTimer);
        autoRefreshTimer = setTimeout(() => {
            if (Date.now() - lastActivity >= 30000) {
                showRefreshToast();
            }
        }, 30000);
    }

    function showRefreshToast() {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 bg-brand-black text-white px-6 py-3 rounded-full shadow-xl z-50 flex items-center gap-3 animate-fade-in-up';
        toast.innerHTML = `
            <span class="text-sm">Data mungkin sudah berubah</span>
            <button onclick="location.reload()" class="bg-brand-primary px-3 py-1 rounded-full text-xs font-bold">Refresh</button>
            <button onclick="this.parentElement.remove()" class="text-brand-surface/50 hover:text-white">✕</button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 10000);
    }

    ['click', 'touchstart', 'scroll'].forEach(event => {
        document.addEventListener(event, resetAutoRefresh, { passive: true });
    });
    resetAutoRefresh();

    // ============================================
    // 3. VIBRATION FEEDBACK
    // ============================================
    function vibrate(duration = 10) {
        if ('vibrate' in navigator) {
            navigator.vibrate(duration);
        }
    }

    // Add haptic feedback to all buttons
    document.querySelectorAll('button, a[href]').forEach(el => {
        el.addEventListener('click', () => vibrate(10));
    });

    // ============================================
    // 4. SWIPE TO CALL / SWIPE TO MAP
    // ============================================
    document.querySelectorAll('[data-swipe-action]').forEach(card => {
        let touchStartX = 0;
        let touchEndX = 0;

        card.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        card.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe(card);
        }, { passive: true });

        function handleSwipe(element) {
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 100) {
                if (diff > 0) {
                    // Swipe left - show action
                    element.classList.add('translate-x-[-80px]');
                    vibrate(20);
                } else {
                    // Swipe right - hide action
                    element.classList.remove('translate-x-[-80px]');
                }
            }
        }
    });

    // ============================================
    // 5. STAGGERED ANIMATION ON LOAD
    // ============================================
    document.querySelectorAll('.space-y-4 > div').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.4s ease-out';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (index * 80));
    });

    // ============================================
    // 6. LONG PRESS FOR QUICK ACTIONS
    // ============================================
    let pressTimer;
    document.querySelectorAll('.space-y-4 > div').forEach(card => {
        card.addEventListener('touchstart', (e) => {
            pressTimer = setTimeout(() => {
                vibrate([50, 30, 50]);
                showQuickActions(card, e);
            }, 500);
        }, { passive: true });

        card.addEventListener('touchend', () => clearTimeout(pressTimer));
        card.addEventListener('touchmove', () => clearTimeout(pressTimer));
    });

    function showQuickActions(card, event) {
        const existing = document.getElementById('quick-actions');
        if (existing) existing.remove();

        const phone = card.querySelector('[data-phone]')?.dataset.phone;
        const address = card.querySelector('[data-address]')?.dataset.address;

        const menu = document.createElement('div');
        menu.id = 'quick-actions';
        menu.className = 'fixed inset-0 bg-black/50 z-50 flex items-end animate-fade-in';
        menu.innerHTML = `
            <div class="bg-white w-full rounded-t-3xl p-6 space-y-3 animate-slide-up">
                <p class="text-center text-xs text-brand-dark mb-4">Aksi Cepat</p>
                ${phone ? `<a href="tel:${phone}" class="flex items-center justify-center w-full py-4 bg-brand-primary text-white font-bold rounded-xl">📞 Telepon Langsung</a>` : ''}
                ${address ? `<a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}" target="_blank" class="flex items-center justify-center w-full py-4 bg-brand-deep text-white font-bold rounded-xl">🗺️ Buka di Maps</a>` : ''}
                <button onclick="this.closest('#quick-actions').remove()" class="w-full py-4 bg-brand-surface text-brand-dark font-bold rounded-xl">Batal</button>
            </div>
        `;
        document.body.appendChild(menu);
        menu.addEventListener('click', (e) => {
            if (e.target === menu) menu.remove();
        });
    }

    // ============================================
    // 7. ONLINE/OFFLINE DETECTION
    // ============================================
    function updateOnlineStatus() {
        const indicator = document.querySelector('.animate-pulse');
        if (indicator) {
            if (navigator.onLine) {
                indicator.classList.remove('bg-red-500');
                indicator.classList.add('bg-brand-accent');
                indicator.nextElementSibling?.textContent === 'Offline' && (indicator.nextElementSibling.textContent = 'Online');
            } else {
                indicator.classList.remove('bg-brand-accent');
                indicator.classList.add('bg-red-500');
                indicator.parentElement.querySelector('span:last-child') && (indicator.parentElement.querySelector('span:last-child').textContent = 'Offline');
            }
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    console.log('🚀 Driver Dashboard JS Loaded');
})();
</script>

<style>
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slide-up {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.3s ease-out; }
.animate-slide-up { animation: slide-up 0.3s ease-out; }
</style>
@endpush
