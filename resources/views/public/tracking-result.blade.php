@extends('layouts.minimal', ['title' => 'Detail Cucian - ' . $transaction->transaction_code])

@section('content')
<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-gray-200">
    <!-- Header Back -->
    <div class="max-w-5xl mx-auto mb-8">
        <a href="{{ route('public.tracking') }}" class="inline-flex items-center group text-sm font-medium text-gray-600 hover:text-brand-primary transition-all duration-300">
            <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 text-gray-500 group-hover:text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </div>
            Kembali ke Pencarian
        </a>
    </div>

    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Status Timeline & Summary -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Glassmorphism Status Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/50 relative overflow-hidden p-8 transform hover:scale-[1.01] transition-all duration-500">
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-primary/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl -ml-32 -mb-32 pointer-events-none"></div>

                <div class="relative z-10 flex justify-between items-start mb-8">
                    <div>
                        <h2 class="font-display text-2xl font-bold text-gray-900">Status Pesanan</h2>
                        <p class="text-sm text-gray-500 mt-1">Update terakhir: <span class="font-medium text-brand-primary">{{ $transaction->updated_at->format('d M Y, H:i') }}</span></p>
                    </div>
                    <div class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $transaction->status == 'completed' ? 'bg-sky-100 text-sky-700' : 'bg-brand-primary/10 text-brand-primary' }}">
                        {{ ucfirst($transaction->status) }}
                    </div>
                </div>

                <!-- DeepUI: Dynamic Animated Timeline (Premium) -->
                <div class="relative" style="padding-left: 0;">
                    <!-- Single Continuous Vertical Line -->
                    <div class="absolute left-[20px] top-[20px] w-[2px] bg-gray-300" style="height: calc(100% - 100px);"></div>

                    @php
                        $steps = [
                            'pending' => ['title' => 'Pesanan Diterima', 'desc' => 'Laundry Anda telah kami terima.'],
                            'processing' => ['title' => 'Sedang Dicuci', 'desc' => 'Pakaian sedang dalam proses pencucian & setrika.'],
                            'ready' => ['title' => 'Siap Diambil', 'desc' => 'Laundry bersih, wangi, dan siap kembali ke Anda.'],
                            'completed' => ['title' => 'Selesai', 'desc' => 'Terima kasih telah mempercayakan laundry Anda.']
                        ];
                        $currentFound = false;
                        $statuses = array_keys($steps);
                        $currentIndex = array_search($transaction->status, $statuses);
                    @endphp

                    @foreach($steps as $key => $step)
                        @php
                            $index = array_search($key, $statuses);
                            $isActive = $index <= $currentIndex;
                            $isCurrent = $index === $currentIndex;
                            $isLast = $loop->last;
                        @endphp
                        
                        <div class="relative flex items-start group mb-8 {{ $isLast ? 'mb-0' : '' }}">
                            <!-- Dot Indicator -->
                            <div class="relative flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full z-10 transition-all duration-300
                                {{ $isActive ? 'bg-gradient-to-br from-brand-primary to-brand-deep shadow-lg' : 'bg-gray-200' }}
                                {{ $isCurrent ? 'ring-4 ring-brand-primary/30' : '' }}
                            ">
                                @if($isActive)
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <span class="text-sm font-bold text-gray-500">{{ $loop->iteration }}</span>
                                @endif
                            </div>

                            <!-- Text Content -->
                            <div class="ml-6 flex-1 transition-all duration-300 {{ $isActive ? 'opacity-100' : 'opacity-50' }}">
                                <h3 class="text-base font-bold text-gray-900 {{ $isCurrent ? 'text-brand-primary' : '' }}">{{ $step['title'] }}</h3>
                                <p class="text-sm text-gray-500 leading-relaxed mt-1">{{ $step['desc'] }}</p>
                                @if($isCurrent)
                                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 bg-brand-primary rounded-full mr-2"></span>
                                        Status Saat Ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- DeepVisual: TIMELINE AKTIVITAS (JENDELA KACA) -->
            @if($transaction->statusLogs->where('photo_url', '!=', null)->count() > 0)
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/50 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-brand-primary/5 to-purple-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-display text-lg font-bold text-gray-900 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-brand-primary text-white flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            Perjalanan Cucian Anda
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 ml-13">Dokumentasi real-time dari karyawan kami</p>
                    </div>
                    <span class="px-3 py-1 bg-brand-primary/10 text-brand-primary text-xs font-bold rounded-full">
                        {{ $transaction->statusLogs->where('photo_url', '!=', null)->count() }} Foto
                    </span>
                </div>

                <div class="p-8">
                    <!-- Timeline Container -->
                    <div class="relative">
                        <!-- Vertical Line -->
                        <div class="absolute left-[20px] top-[20px] w-[2px] bg-gradient-to-b from-brand-primary via-purple-300 to-gray-200" style="height: calc(100% - 40px);"></div>

                        <!-- Activity Items -->
                        <div class="space-y-8">
                            @foreach($transaction->statusLogs->where('photo_url', '!=', null)->sortByDesc('created_at') as $log)
                            <div class="relative flex gap-6 group">
                                <!-- Dot Indicator -->
                                <div class="relative flex-shrink-0 z-10">
                                    <div class="w-10 h-10 rounded-full {{ $log->is_milestone ? 'bg-gradient-to-br from-amber-400 to-orange-500 ring-4 ring-amber-100' : 'bg-gradient-to-br from-brand-primary to-purple-500' }} flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        @if($log->is_milestone)
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 pb-8">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                                {{ $log->getActivityLabel() }}
                                                @if($log->is_milestone)
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Milestone</span>
                                                @endif
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $log->created_at->format('d M Y, H:i') }}
                                                <span class="text-gray-400">•</span>
                                                <span class="text-brand-primary font-medium">{{ $log->changedBy->name ?? 'Staff' }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Photo -->
                                    <div class="relative rounded-2xl overflow-hidden shadow-lg group/photo cursor-pointer hover:shadow-2xl transition-shadow duration-300" data-lightbox="activity-{{ $log->id }}">
                                        <img 
                                            src="{{ $log->photo_url }}" 
                                            alt="{{ $log->getActivityLabel() }}" 
                                            class="w-full h-64 object-cover group-hover/photo:scale-105 transition-transform duration-500"
                                            loading="lazy"
                                        >
                                        <!-- Overlay Zoom Icon -->
                                        <div class="absolute inset-0 bg-black/0 group-hover/photo:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white opacity-0 group-hover/photo:opacity-100 transition-opacity duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    @if($log->notes)
                                    <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-sm text-gray-700 leading-relaxed flex items-start gap-2">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                            </svg>
                                            <span>{{ $log->notes }}</span>
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Glassmorphism Items Detail -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg border border-white/50 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-white/50 flex justify-between items-center">
                    <h3 class="font-display text-lg font-bold text-gray-900">Rincian Layanan</h3>
                    <span class="text-xs font-medium text-gray-400 font-mono">Invoice: #{{ $transaction->transaction_code }}</span>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-8 py-4 font-semibold">Layanan</th>
                                <th class="px-4 py-4 font-semibold text-center">Qty</th>
                                <th class="px-8 py-4 font-semibold text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transaction->details as $detail)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5 font-medium text-gray-900">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-brand-primary flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        </div>
                                        <div>
                                            {{ $detail->service->service_name }}
                                            <div class="text-xs text-gray-400 font-normal mt-0.5">{{ $detail->service->unit }} • Rp {{ number_format($detail->price_at_transaction, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-center text-gray-600 bg-gray-50/20 font-mono">{{ $detail->quantity }}</td>
                                <td class="px-8 py-5 text-right font-bold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/80 border-t border-gray-200">
                            <tr>
                                <td colspan="2" class="px-8 py-6 text-right font-medium text-gray-600">Total Tagihan</td>
                                <td class="px-8 py-6 text-right text-xl font-display font-bold text-brand-primary">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- DeepDelivery: Delivery Status Card --}}
            @if($transaction->is_delivery && $transaction->shipments->count() > 0)
                @php
                    $latestShipment = $transaction->shipments()->latest()->first();
                @endphp
                <div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-3xl shadow-lg border border-sky-200/50 overflow-hidden">
                    <div class="px-8 py-6 border-b border-sky-200/50 bg-white/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m-4 0v1a1 1 0 001 1h1m10-3a2 2 0 104 0m-4 0a2 2 0 114 0m-4 0v1a1 1 0 001 1h1"/>
                                </svg>
                            </div>
                            <h3 class="font-display text-lg font-bold text-gray-900">Status Pengiriman</h3>
                        </div>
                        @if($latestShipment->status === 'pending')
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Menunggu Kurir</span>
                        @elseif($latestShipment->status === 'picked_up')
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full animate-pulse">Dalam Perjalanan</span>
                        @elseif($latestShipment->status === 'delivered')
                        <span class="px-3 py-1 bg-sky-100 text-sky-700 text-xs font-bold rounded-full">✓ Terkirim</span>
                        @endif
                    </div>
                    
                    <div class="p-8 space-y-6">
                        {{-- Courier Info --}}
                        @if($latestShipment->courier)
                        <div class="flex items-center gap-4 p-4 bg-white/70 rounded-2xl">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-bold text-lg">
                                {{ substr($latestShipment->courier->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Kurir Anda</p>
                                <p class="font-bold text-gray-900">{{ $latestShipment->courier->name }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Delivery Address --}}
                        <div class="flex items-start gap-3 p-4 bg-white/70 rounded-2xl">
                            <svg class="w-5 h-5 text-sky-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 font-medium mb-1">Alamat Pengiriman</p>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $transaction->delivery_address ?? $transaction->customer->address }}</p>
                            </div>
                        </div>

                        {{-- Delivery Timeline --}}
                        <div class="space-y-3">
                            @if($latestShipment->assigned_at)
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-2 h-2 rounded-full bg-sky-500"></div>
                                <span class="text-gray-600">Ditugaskan:</span>
                                <span class="font-medium text-gray-900">{{ $latestShipment->assigned_at->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                            
                            @if($latestShipment->status === 'picked_up' || $latestShipment->status === 'delivered')
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <span class="text-gray-600">Dalam Perjalanan:</span>
                                <span class="font-medium text-gray-900">{{ $latestShipment->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                            
                            @if($latestShipment->status === 'delivered' && $latestShipment->completed_at)
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-2 h-2 rounded-full bg-sky-500"></div>
                                <span class="text-gray-600">Diterima:</span>
                                <span class="font-medium text-gray-900">{{ $latestShipment->completed_at->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Delivery Proof Photo --}}
                        @if($latestShipment->status === 'delivered' && $latestShipment->photo_proof_url)
                        <div class="pt-4 border-t border-sky-200/50">
                            <p class="text-xs text-gray-500 font-medium mb-3">Bukti Serah Terima</p>
                            <img 
                                src="{{ $latestShipment->photo_proof_url }}" 
                                alt="Bukti pengiriman" 
                                class="w-full h-48 object-cover rounded-2xl cursor-pointer hover:opacity-90 transition-opacity shadow-lg"
                                data-lightbox="delivery-proof"
                            >
                            <p class="text-xs text-gray-400 mt-2 text-center">Klik untuk memperbesar</p>
                        </div>
                        @endif

                        {{-- Notes --}}
                        @if($latestShipment->notes)
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                            <p class="text-xs text-amber-700 font-medium mb-1">Catatan Kurir</p>
                            <p class="text-sm text-amber-900">{{ $latestShipment->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Info & Payment -->
        <div class="space-y-6">
            
            <!-- Customer Info Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-soft p-8 border border-white/50">
                <h3 class="font-display text-lg font-bold text-gray-900 mb-6">Informasi Pelanggan</h3>
                <div class="space-y-6">
                    <div class="flex items-center group">
                        <div class="w-10 h-10 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mr-4 group-hover:bg-brand-primary group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Nama Pelanggan</p>
                            <p class="font-semibold text-gray-900 text-lg">{{ $transaction->customer->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center group">
                        <div class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center mr-4 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Kode Nota</p>
                            <p class="font-family-mono font-bold text-gray-900 text-lg tracking-wide">{{ $transaction->transaction_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Card (Premium Gradient) -->
            <div class="bg-gradient-to-br from-[#0F172A] to-[#1E293B] rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden group">
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-primary opacity-20 rounded-full -mr-20 -mt-20 blur-3xl group-hover:opacity-30 transition-opacity duration-700"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-purple-500 opacity-20 rounded-full -ml-10 -mb-10 blur-2xl group-hover:opacity-30 transition-opacity duration-700"></div>
                
                <h3 class="font-display text-lg font-medium mb-1 opacity-80 relative z-10">Status Pembayaran</h3>
                
                @if($transaction->payment_status == 'paid')
                    <div class="mt-6 flex flex-col items-center justify-center py-6 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 relative z-10">
                        <div class="w-16 h-16 rounded-full bg-sky-500 flex items-center justify-center text-white mb-4 shadow-lg shadow-sky-500/30 animate-bounce-slow">
                             <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="font-bold text-2xl tracking-tight">LUNAS</p>
                        <p class="text-sm opacity-60 mt-1">Pembayaran Terverifikasi</p>
                    </div>
                @else
                    <div class="mt-6 mb-8 relative z-10">
                        <p class="text-4xl font-display font-bold mb-2 tracking-tight">Rp {{ number_format($transaction->total_cost - $transaction->total_paid, 0, ',', '.') }}</p>
                        <p class="text-sm opacity-60 font-medium bg-white/10 inline-block px-3 py-1 rounded-full">Belum Dibayar</p>
                    </div>
                    
                    <button 
                        id="pay-button" 
                        class="relative z-10 w-full py-4 bg-white text-gray-900 font-bold rounded-2xl shadow-lg hover:bg-gray-50 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden group/btn"
                    >
                        <span class="absolute inset-0 bg-gradient-to-r from-gray-100 to-white opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative text-lg">Bayar Sekarang (QRIS)</span>
                        <svg class="relative w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </button>
                    <div id="payment-loading" class="hidden text-center mt-4 text-sm opacity-80 animate-pulse flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses Pembayaran...
                    </div>
                @endif
            </div>

            <!-- Help Contact -->
            <div class="text-center pt-4">
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', config('app.phone'))) }}" class="inline-flex items-center text-gray-500 font-medium hover:text-brand-primary transition-colors text-sm group">
                    <span class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 4.255 4.045-.809c1.306.391 2.934.332 4.091-.32 1.454-1.002 1.559-2.28 1.559-2.28s1.618-.475 2.106-.723c.316-.16.536-.341.602-.551.053-.169.034-.959-.39-1.282-.249-.19-.714-.403-.984-.537-.253-.122-.505-.175-.765.234-.239.375-.515.753-.787.893-.243.125-.975-.125-2.062-1.218-.949-.953-1.161-1.636-1.047-1.896.16-.364.673-1.144.757-1.341.077-.183.024-.467-.146-.739-.148-.236-1.161-1.954-1.161-1.954s-.308-.432-.619-.387a1.4 1.4 0 0 0-.573.182z"/></svg>
                    </span>
                    Butuh bantuan? Hubungi WhatsApp kami
                </a>
            </div>

        </div>
    </div>
</div>

@if($transaction->payment_status != 'paid')
{{-- DeepPayment: Dual-mode payment script (Demo + Midtrans) --}}
<script>
    const payButton = document.getElementById('pay-button');
    const loadingText = document.getElementById('payment-loading');
    
    payButton.addEventListener('click', async function () {
        // UI State
        payButton.disabled = true;
        payButton.classList.add('opacity-50', 'cursor-not-allowed');
        loadingText.classList.remove('hidden');
        
        try {
            // 1. Initiate Payment (Gateway-agnostic)
            const response = await fetch("{{ route('public.payment.initiate', $transaction->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (!result.success) throw new Error(result.message || 'Gagal memulai pembayaran');

            // 2. Handle based on gateway type
            if (result.gateway === 'demo') {
                // Demo Mode: Show QR Modal
                showDemoPaymentModal(result.data);
            } else if (result.gateway === 'midtrans') {
                // Midtrans Mode: Open Snap Popup
                // UNCOMMENT WHEN MIDTRANS IS ACTIVE
                /*
                window.snap.pay(result.data.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('public.payment.success', $transaction->id) }}";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('public.payment.success', $transaction->id) }}";
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal, silakan coba lagi.');
                        resetButton();
                    },
                    onClose: function() {
                        resetButton();
                    }
                });
                */
                throw new Error('Midtrans gateway is currently disabled');
            }

        } catch (error) {
            alert(error.message);
            resetButton();
        }
    });

    function resetButton() {
        payButton.disabled = false;
        payButton.classList.remove('opacity-50', 'cursor-not-allowed');
        loadingText.classList.add('hidden');
    }

    function showDemoPaymentModal(paymentData) {
        // Create modal overlay
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in';
        modal.innerHTML = `
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-scale-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-brand-primary to-purple-600 px-8 py-6 text-white">
                    <h3 class="text-2xl font-bold mb-1">Scan QR Code</h3>
                    <p class="text-sm opacity-90">Bayar dengan aplikasi mobile banking Anda</p>
                </div>

                <!-- QR Code -->
                <div class="p-8 text-center">
                    <div class="bg-gray-50 rounded-2xl p-6 mb-6 inline-block">
                        <img src="${paymentData.qr_url}" alt="QR Code" class="w-64 h-64 mx-auto">
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-left">
                        <p class="text-sm text-blue-900 leading-relaxed">
                            <strong>📱 Cara Bayar:</strong><br>
                            1. Buka aplikasi mobile banking<br>
                            2. Pilih menu QRIS/Scan QR<br>
                            3. Scan QR code di atas<br>
                            4. Konfirmasi pembayaran<br>
                            5. Klik tombol "Saya Sudah Bayar" di bawah
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Pembayaran</span>
                            <span class="text-xl font-bold text-gray-900">Rp {{ number_format($transaction->getRemainingBalance(), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <form action="{{ route('public.payment.confirm', $transaction->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="payment_id" value="${paymentData.payment_id}">
                        
                        <button type="submit" class="w-full py-4 bg-green-600 text-white font-bold rounded-2xl hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Saya Sudah Bayar
                        </button>
                        
                        <button type="button" onclick="this.closest('.fixed').remove(); resetButton();" class="w-full py-3 bg-gray-100 text-gray-700 font-medium rounded-2xl hover:bg-gray-200 transition-all duration-300">
                            Batal
                        </button>
                    </form>

                    <p class="text-xs text-gray-400 mt-4">
                        QR Code berlaku selama 24 jam
                    </p>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        resetButton();
    }
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes scale-in {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-fade-in {
    animation: fade-in 0.2s ease-out;
}
.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>

{{-- Midtrans Snap Script (UNCOMMENT WHEN SWITCHING TO PRODUCTION) --}}
{{-- <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script> --}}
@endif

@push('scripts')
<script>
/**
 * DeepJS: Tracking Result Interactive Features
 * - Copy Transaction Code
 * - Share Functionality
 * - Print Receipt
 * - Status Animation
 * - Auto Refresh
 */
(function() {
    'use strict';

    // ============================================
    // 1. COPY TRANSACTION CODE
    // ============================================
    const txCode = '{{ $transaction->transaction_code }}';
    
    document.querySelectorAll('[data-copy-code]').forEach(el => {
        el.style.cursor = 'pointer';
        el.title = 'Klik untuk menyalin';
        
        el.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(txCode);
                showToast('✓ Kode nota disalin');
                if ('vibrate' in navigator) navigator.vibrate(20);
            } catch (err) {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = txCode;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                showToast('✓ Kode nota disalin');
            }
        });
    });

    // ============================================
    // 2. SHARE FUNCTIONALITY
    // ============================================
    const shareBtn = document.getElementById('share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            const shareData = {
                title: 'Status Laundry - SiLaundry',
                text: `Status cucian ${txCode}: {{ ucfirst($transaction->status) }}`,
                url: window.location.href
            };

            if (navigator.share) {
                try {
                    await navigator.share(shareData);
                } catch (err) {
                    console.log('Share cancelled');
                }
            } else {
                // Fallback: copy link
                await navigator.clipboard.writeText(window.location.href);
                showToast('✓ Link disalin');
            }
        });
    }

    // ============================================
    // 3. CETAK NOTA
    // ============================================
    const printBtn = document.getElementById('print-btn');
    if (printBtn) {
        printBtn.addEventListener('click', () => {
            window.print();
        });
    }

    // ============================================
    // 4. ANIMASI STATUS
    // ============================================
    const statusSteps = document.querySelectorAll('.relative.pl-8 > div');
    statusSteps.forEach((step, index) => {
        step.style.opacity = '0';
        step.style.transform = 'translateX(-20px)';
        step.style.transition = 'all 0.5s ease-out';
        
        setTimeout(() => {
            step.style.opacity = '1';
            step.style.transform = 'translateX(0)';
        }, 200 + (index * 150));
    });

    // ============================================
    // 5. AUTO REFRESH (Setiap 2 menit untuk status pending)
    // ============================================
    const currentStatus = '{{ $transaction->status }}';
    if (['pending', 'processing'].includes(currentStatus)) {
        setTimeout(() => {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 bg-brand-black text-white px-6 py-3 rounded-full shadow-xl z-50 flex items-center gap-3';
            toast.innerHTML = `
                <span class="text-sm">Cek status terbaru?</span>
                <button onclick="location.reload()" class="bg-brand-primary px-3 py-1 rounded-full text-xs font-bold">Refresh</button>
                <button onclick="this.parentElement.remove()" class="text-white/50">✕</button>
            `;
            document.body.appendChild(toast);
        }, 120000); // 2 minutes
    }

    // ============================================
    // 6. LIGHTBOX BUKTI PENGIRIMAN
    // ============================================
    document.querySelectorAll('[data-lightbox]').forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', () => {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4';
            overlay.innerHTML = `
                <img src="${img.src}" class="max-w-full max-h-full rounded-lg" alt="Bukti pengiriman">
                <button class="absolute top-4 right-4 text-white text-4xl" onclick="this.parentElement.remove()">×</button>
            `;
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.remove();
            });
            document.body.appendChild(overlay);
        });
    });

    // ============================================
    // 7. PAYMENT HISTORY ACCORDION
    // ============================================
    document.querySelectorAll('[data-accordion-toggle]').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const target = document.getElementById(toggle.dataset.accordionToggle);
            if (target) {
                const isOpen = target.style.maxHeight && target.style.maxHeight !== '0px';
                target.style.maxHeight = isOpen ? '0px' : target.scrollHeight + 'px';
                target.style.overflow = 'hidden';
                toggle.querySelector('svg')?.classList.toggle('rotate-180');
            }
        });
    });

    // ============================================
    // 8. NOTIFIKASI TOAST
    // ============================================
    function showToast(message) {
        const existing = document.querySelector('.toast-notification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-notification fixed top-20 left-1/2 -translate-x-1/2 px-6 py-3 bg-brand-primary text-white rounded-full shadow-xl z-50 text-sm font-medium';
        toast.style.animation = 'fadeInUp 0.3s ease-out';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    // ============================================
    // 9. ANIMASI MASUK
    // ============================================
    const cards = document.querySelectorAll('.rounded-3xl');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease-out';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });

    console.log('📋 Halaman Hasil Tracking Siap');
})();
</script>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px) translateX(-50%); }
    to { opacity: 1; transform: translateY(0) translateX(-50%); }
}
@media print {
    header, footer, .no-print, button { display: none !important; }
    body { background: white !important; }
    .rounded-3xl { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>
@endpush
@endsection
