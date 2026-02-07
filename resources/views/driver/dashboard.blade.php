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
                        <p class="text-sm text-brand-dark leading-relaxed mb-6 opacity-80">{{ $shipment->address }}</p>

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
