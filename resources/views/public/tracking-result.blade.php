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
                    <div class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-brand-primary/10 text-brand-primary' }}">
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
                        <div class="w-16 h-16 rounded-full bg-green-500 flex items-center justify-center text-white mb-4 shadow-lg shadow-green-500/30 animate-bounce-slow">
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
                    <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 4.255 4.045-.809c1.306.391 2.934.332 4.091-.32 1.454-1.002 1.559-2.28 1.559-2.28s1.618-.475 2.106-.723c.316-.16.536-.341.602-.551.053-.169.034-.959-.39-1.282-.249-.19-.714-.403-.984-.537-.253-.122-.505-.175-.765.234-.239.375-.515.753-.787.893-.243.125-.975-.125-2.062-1.218-.949-.953-1.161-1.636-1.047-1.896.16-.364.673-1.144.757-1.341.077-.183.024-.467-.146-.739-.148-.236-1.161-1.954-1.161-1.954s-.308-.432-.619-.387a1.4 1.4 0 0 0-.573.182z"/></svg>
                    </span>
                    Butuh bantuan? Hubungi WhatsApp kami
                </a>
            </div>

        </div>
    </div>
</div>

@if($transaction->payment_status != 'paid')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const payButton = document.getElementById('pay-button');
    const loadingText = document.getElementById('payment-loading');
    
    payButton.addEventListener('click', async function () {
        // UI State
        payButton.disabled = true;
        payButton.classList.add('opacity-50', 'cursor-not-allowed');
        loadingText.classList.remove('hidden');
        
        try {
            // 1. Get Snap Token from Backend
            const response = await fetch("{{ route('public.payment.token', $transaction->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Gagal memulai pembayaran');

            // 2. Open Snap Popup
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    window.location.reload();
                },
                onPending: function(result) {
                    window.location.reload(); // Refresh to show pending state
                },
                onError: function(result) {
                    alert('Pembayaran gagal, silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    loadingText.classList.add('hidden');
                },
                onClose: function() {
                    payButton.disabled = false;
                    payButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    loadingText.classList.add('hidden');
                }
            });

        } catch (error) {
            alert(error.message);
            payButton.disabled = false;
            payButton.classList.remove('opacity-50', 'cursor-not-allowed');
            loadingText.classList.add('hidden');
        }
    });
</script>
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
