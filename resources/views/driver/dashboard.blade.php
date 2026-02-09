@extends('layouts.minimal', ['title' => 'Dashboard Kurir - SiLaundry'])

@section('content')
{{-- 
    DeepUI Pro: Dashboard Kurir
    Design System: Clean Card System + Clear Visual Hierarchy
    Mobile-First, Touch-Optimized, Pull-to-Refresh
--}}
<div class="min-h-screen bg-slate-50">
    
    {{-- Header --}}
    <header class="bg-slate-900 text-white px-6 pt-12 pb-8 relative overflow-hidden">
        {{-- Ambient Glow --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/20 rounded-full blur-[100px] -mr-16 -mt-16"></div>
        
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Selamat Datang</p>
                <h1 class="text-2xl font-bold mt-1">{{ auth()->guard('driver')->user()->name }}</h1>
                <div class="flex items-center mt-3 gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>
                        Online
                    </span>
                    <span class="text-slate-500 text-xs">{{ now()->translatedFormat('l, d M Y') }}</span>
                </div>
            </div>
            <form action="{{ route('driver.logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-3 bg-white/10 hover:bg-red-500/20 text-slate-400 hover:text-red-400 rounded-xl transition-all" title="Keluar">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    {{-- Stats Cards --}}
    <div class="px-6 -mt-4 relative z-20">
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-5 grid grid-cols-2 gap-4">
            <div class="text-center py-3 border-r border-slate-100">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Tugas Aktif</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $myTasks->count() }}</p>
            </div>
            <div class="text-center py-3">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Selesai Hari Ini</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $completedToday->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="px-6 pt-8 pb-24 space-y-8">
        
        {{-- Active Deliveries Section --}}
        @if($myTasks->count() > 0)
        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-6 bg-emerald-500 rounded-full"></div>
                <h2 class="text-lg font-bold text-slate-900">Tugas Saya</h2>
            </div>
            
            <div class="space-y-4">
                @foreach($myTasks as $shipment)
                <article class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md hover:border-emerald-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-slate-900 text-white text-xs font-bold rounded-lg font-mono">
                            {{ $shipment->transaction->transaction_code }}
                        </span>
                        @if($shipment->status === 'pending')
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg">
                            Baru Ditugaskan
                        </span>
                        @else
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg">
                            Dalam Perjalanan
                        </span>
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $shipment->transaction->customer->name }}</h3>
                    
                    <div class="flex items-start gap-2 text-slate-600 text-sm mb-3">
                        <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="leading-relaxed">{{ $shipment->transaction->delivery_address ?? $shipment->transaction->customer->address }}</span>
                    </div>

                    <div class="flex items-center gap-2 text-slate-500 text-xs mb-5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shipment->transaction->customer->phone_number) }}" 
                           class="hover:text-emerald-600 font-medium">
                            {{ $shipment->transaction->customer->phone_number }}
                        </a>
                    </div>

                    @if($shipment->status === 'pending')
                    <form action="{{ route('driver.delivery.start', $shipment->transaction_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center justify-center w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Ambil Barang & Mulai
                        </button>
                    </form>
                    @else
                    <a href="{{ route('driver.delivery.show', $shipment->transaction_id) }}" 
                       class="flex items-center justify-center w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl transition-colors">
                        Selesaikan Pengiriman
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    @endif
                </article>
                @endforeach
            </div>
        </section>
        @else
        <section>
            <div class="bg-slate-100 rounded-2xl p-8 text-center">
                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-slate-500 font-medium">Tidak ada tugas tersedia</p>
                <p class="text-slate-400 text-sm mt-1">Tarik ke bawah untuk refresh</p>
            </div>
        </section>
        @endif

        {{-- Completed Today Section --}}
        @if($completedToday->count() > 0)
        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-6 bg-slate-300 rounded-full"></div>
                <h2 class="text-lg font-bold text-slate-900">Selesai Hari Ini</h2>
            </div>
            
            <div class="space-y-3">
                @foreach($completedToday as $shipment)
                <article class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-slate-900">{{ $shipment->transaction->customer->name }}</p>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $shipment->transaction->transaction_code }}</p>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg">
                            ✓ Selesai
                        </span>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif
    </main>
</div>
@endsection

@push('scripts')
<script>
/**
 * DeepJS Pro: Dashboard Kurir
 * - Pull to refresh
 * - Auto refresh notification
 * - Touch feedback
 * - Staggered animations
 */
(function() {
    'use strict';

    // Pull to Refresh
    let startY = 0;
    let isPulling = false;
    const threshold = 80;

    document.addEventListener('touchstart', (e) => {
        if (window.scrollY === 0) {
            startY = e.touches[0].pageY;
            isPulling = true;
        }
    }, { passive: true });

    document.addEventListener('touchmove', (e) => {
        if (!isPulling) return;
        const diff = e.touches[0].pageY - startY;
        if (diff > threshold) {
            document.body.style.transform = `translateY(${Math.min(diff * 0.3, 40)}px)`;
        }
    }, { passive: true });

    document.addEventListener('touchend', () => {
        if (!isPulling) return;
        isPulling = false;
        
        const transform = document.body.style.transform;
        const match = transform.match(/translateY\(([^)]+)px\)/);
        
        if (match && parseFloat(match[1]) > 30) {
            location.reload();
        }
        
        document.body.style.transform = '';
        document.body.style.transition = 'transform 0.3s ease';
        setTimeout(() => document.body.style.transition = '', 300);
    });

    // Staggered Animation
    document.querySelectorAll('article').forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (i * 60));
    });

    // Auto Refresh Reminder (60s)
    setTimeout(() => {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-5 py-3 rounded-full shadow-xl z-50 flex items-center gap-3 text-sm';
        toast.innerHTML = `
            <span>Data mungkin berubah</span>
            <button onclick="location.reload()" class="bg-emerald-500 px-3 py-1 rounded-full text-xs font-bold">Refresh</button>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white ml-1">✕</button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 10000);
    }, 60000);

    // Touch Feedback
    document.querySelectorAll('button, a').forEach(el => {
        el.addEventListener('touchstart', () => {
            if ('vibrate' in navigator) navigator.vibrate(5);
        }, { passive: true });
    });

    console.log('📦 Driver Dashboard Ready');
})();
</script>
@endpush
