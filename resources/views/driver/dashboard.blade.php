@extends('layouts.minimal', ['title' => 'Dashboard Kurir - SiLaundry'])

@section('content')
{{-- 
    DeepUI Premium: Dashboard Kurir
    Design System: Sky Blue Theme + Glassmorphism
    Mobile-First, Touch-Optimized, Premium Feel
--}}
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-sky-50 to-blue-50">
    
    {{-- Header - Premium Gradient --}}
    <header class="bg-gradient-to-br from-sky-600 via-blue-600 to-indigo-600 text-white px-6 pt-12 pb-8 relative overflow-hidden shadow-2xl">
        {{-- Ambient Glow Effects --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-[120px] -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-sky-400/20 rounded-full blur-[100px] -ml-16 -mb-16"></div>
        
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-sky-100 text-xs font-semibold uppercase tracking-widest mb-2">Dashboard Kurir</p>
                <h1 class="text-3xl font-bold tracking-tight">{{ auth()->guard('driver')->user()->name }}</h1>
                <div class="flex items-center mt-4 gap-3">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm border border-white/30 shadow-lg">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse shadow-lg shadow-green-400/50"></span>
                        Aktif
                    </span>
                    <span class="text-sky-100 text-sm font-medium">{{ now()->translatedFormat('l, d M Y') }}</span>
                </div>
            </div>
            <form action="{{ route('driver.logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-3 bg-white/10 hover:bg-red-500/30 text-white hover:text-red-200 rounded-xl transition-all backdrop-blur-sm border border-white/20 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95" title="Keluar">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    {{-- Stats Cards - Glassmorphism Premium --}}
    <div class="px-6 -mt-6 relative z-20">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-sky-500/10 border border-white/50 p-6 grid grid-cols-2 gap-6">
            <div class="text-center py-4 relative">
                <div class="absolute inset-0 bg-gradient-to-br from-sky-50 to-blue-50 rounded-2xl opacity-50"></div>
                <div class="relative z-10">
                    <p class="text-xs text-slate-600 font-bold uppercase tracking-wider mb-2">Tugas Aktif</p>
                    <p class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-br from-sky-600 to-blue-600">{{ $myTasks->count() }}</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 bg-sky-100 text-sky-700 text-xs font-semibold rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        Pending
                    </div>
                </div>
            </div>
            <div class="text-center py-4 relative border-l-2 border-slate-100">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl opacity-50"></div>
                <div class="relative z-10">
                    <p class="text-xs text-slate-600 font-bold uppercase tracking-wider mb-2">Selesai Hari Ini</p>
                    <p class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-br from-green-600 to-emerald-600">{{ $completedToday->count() }}</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Completed
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="px-6 pt-8 pb-24 space-y-8">
        
        {{-- Active Deliveries Section --}}
        @if($myTasks->count() > 0)
        <section>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1.5 h-8 bg-gradient-to-b from-sky-500 to-blue-600 rounded-full shadow-lg shadow-sky-500/30"></div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Tugas Saya</h2>
                <span class="ml-auto px-3 py-1 bg-sky-100 text-sky-700 text-xs font-bold rounded-full">{{ $myTasks->count() }} Aktif</span>
            </div>
            
            <div class="space-y-4">
                @foreach($myTasks as $shipment)
                <article class="bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-lg shadow-slate-200/50 border border-white/50 hover:shadow-2xl hover:shadow-sky-500/10 hover:border-sky-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start mb-5">
                        <span class="px-4 py-2 bg-gradient-to-r from-slate-900 to-slate-800 text-white text-sm font-bold rounded-xl font-mono shadow-lg">
                            {{ $shipment->transaction->transaction_code }}
                        </span>
                        @if($shipment->status === 'pending')
                        <span class="px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 text-xs font-bold rounded-xl border border-sky-200 shadow-sm">
                            <span class="inline-block w-1.5 h-1.5 bg-sky-500 rounded-full mr-1.5 animate-pulse"></span>
                            Baru Ditugaskan
                        </span>
                        @else
                        <span class="px-3 py-1.5 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 text-xs font-bold rounded-xl border border-amber-200 shadow-sm">
                            <span class="inline-block w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5 animate-pulse"></span>
                            Dalam Perjalanan
                        </span>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-900 mb-2 tracking-tight">{{ $shipment->transaction->customer->name }}</h3>
                    
                    <div class="flex items-start gap-3 text-slate-600 text-sm mb-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="leading-relaxed font-medium">{{ $shipment->transaction->delivery_address ?? $shipment->transaction->customer->address }}</span>
                    </div>

                    <div class="flex items-center gap-2 text-slate-500 text-sm mb-6 p-3 bg-white rounded-xl border border-slate-100">
                        <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shipment->transaction->customer->phone_number) }}" 
                           class="hover:text-sky-600 font-semibold transition-colors">
                            {{ $shipment->transaction->customer->phone_number }}
                        </a>
                    </div>

                    @if($shipment->status === 'pending')
                    <form action="{{ route('driver.delivery.start', $shipment->transaction_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center justify-center w-full py-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-sky-500/30 hover:shadow-xl hover:shadow-sky-500/40 hover:-translate-y-0.5 active:translate-y-0">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Ambil Barang & Mulai
                        </button>
                    </form>
                    @else
                    <a href="{{ route('driver.delivery.show', $shipment->transaction_id) }}" 
                       class="flex items-center justify-center w-full py-4 bg-gradient-to-r from-slate-900 to-slate-800 hover:from-slate-800 hover:to-slate-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-slate-900/30 hover:shadow-xl hover:shadow-slate-900/40 hover:-translate-y-0.5 active:translate-y-0">
                        Selesaikan Pengiriman
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
            <div class="bg-gradient-to-br from-slate-100 to-slate-50 rounded-3xl p-10 text-center border border-slate-200 shadow-inner">
                <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-slate-700 font-bold text-lg mb-2">Tidak Ada Tugas</p>
                <p class="text-slate-500 text-sm">Tarik ke bawah untuk refresh atau tunggu tugas baru</p>
            </div>
        </section>
        @endif

        {{-- Completed Today Section --}}
        @if($completedToday->count() > 0)
        <section>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1.5 h-8 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full shadow-lg shadow-green-500/30"></div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Selesai Hari Ini</h2>
                <span class="ml-auto px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">{{ $completedToday->count() }}</span>
            </div>
            
            <div class="space-y-3">
                @foreach($completedToday as $shipment)
                <article class="bg-white/60 backdrop-blur-sm rounded-2xl p-5 border border-green-100 shadow-sm">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-slate-900 text-lg">{{ $shipment->transaction->customer->name }}</p>
                            <p class="text-xs text-slate-400 font-mono mt-1">{{ $shipment->transaction->transaction_code }}</p>
                        </div>
                        <span class="px-3 py-1.5 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 text-xs font-bold rounded-xl border border-green-200 shadow-sm">
                            <svg class="w-3 h-3 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Selesai
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
