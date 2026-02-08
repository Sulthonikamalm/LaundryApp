@extends('layouts.minimal', ['title' => 'Detail Cucian - ' . $transaction->transaction_code])

@section('content')
<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 bg-brand-white">
    <!-- Header Back -->
    <div class="max-w-4xl mx-auto mb-8">
        <a href="{{ route('public.tracking') }}" class="inline-flex items-center text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Pencarian
        </a>
    </div>

    <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Status Timeline & Summary -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Status Card -->
            <div class="bg-white rounded-3xl shadow-soft p-8 border border-brand-surface relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-surface rounded-bl-full opacity-20 pointer-events-none"></div>
                
                <h2 class="font-display text-2xl font-bold text-brand-black mb-1">Status Pesanan</h2>
                <p class="text-sm text-brand-dark mb-8">Update terakhir: {{ $transaction->updated_at->format('d M Y, H:i') }}</p>

                <!-- Visual Timeline -->
                <div class="relative pl-8 border-l-2 border-brand-surface space-y-10">
                    <!-- Step 1: Pending -->
                    <div class="relative">
                        <div class="absolute -left-[33px] flex items-center justify-center w-8 h-8 rounded-full {{ in_array($transaction->status, ['pending', 'processing', 'ready', 'completed']) ? 'bg-brand-primary text-white border-4 border-white shadow-md' : 'bg-brand-surface text-brand-dark border-4 border-white' }}">
                            @if(in_array($transaction->status, ['pending', 'processing', 'ready', 'completed']))
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span class="text-xs font-bold">1</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-brand-black">Pesanan Diterima</h3>
                        <p class="text-sm text-brand-dark">Laundry Anda telah kami terima.</p>
                    </div>

                    <!-- Step 2: Processing -->
                    <div class="relative">
                        <div class="absolute -left-[33px] flex items-center justify-center w-8 h-8 rounded-full {{ in_array($transaction->status, ['processing', 'ready', 'completed']) ? 'bg-brand-primary text-white border-4 border-white shadow-md' : 'bg-brand-surface text-brand-dark border-4 border-white' }}">
                            @if(in_array($transaction->status, ['processing', 'ready', 'completed']))
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span class="text-xs font-bold">2</span>
                            @endif
                        </div>
                        <h3 class="{{ in_array($transaction->status, ['processing', 'ready', 'completed']) ? 'text-lg font-bold text-brand-black' : 'text-lg font-medium text-brand-dark/60' }}">Sedang Dicuci</h3>
                        <p class="text-sm {{ in_array($transaction->status, ['processing', 'ready', 'completed']) ? 'text-brand-dark' : 'text-brand-dark/60' }}">Pakaian sedang dalam proses pencucian & setrika.</p>
                    </div>

                    <!-- Step 3: Ready -->
                    <div class="relative">
                        <div class="absolute -left-[33px] flex items-center justify-center w-8 h-8 rounded-full {{ in_array($transaction->status, ['ready', 'completed']) ? 'bg-brand-primary text-white border-4 border-white shadow-md' : 'bg-brand-surface text-brand-dark border-4 border-white' }}">
                             @if(in_array($transaction->status, ['ready', 'completed']))
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span class="text-xs font-bold">3</span>
                            @endif
                        </div>
                        <h3 class="{{ in_array($transaction->status, ['ready', 'completed']) ? 'text-lg font-bold text-brand-black' : 'text-lg font-medium text-brand-dark/60' }}">Siap Diambil / Diantar</h3>
                        <p class="text-sm {{ in_array($transaction->status, ['ready', 'completed']) ? 'text-brand-dark' : 'text-brand-dark/60' }}">Laundry bersih, wangi, dan siap kembali ke Anda.</p>
                    </div>

                    <!-- Step 4: Completed -->
                    <div class="relative">
                        <div class="absolute -left-[33px] flex items-center justify-center w-8 h-8 rounded-full {{ $transaction->status == 'completed' ? 'bg-brand-accent text-white border-4 border-white shadow-glow' : 'bg-brand-surface text-brand-dark border-4 border-white' }}">
                             @if($transaction->status == 'completed')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span class="text-xs font-bold">4</span>
                            @endif
                        </div>
                        <h3 class="{{ $transaction->status == 'completed' ? 'text-lg font-bold text-brand-black' : 'text-lg font-medium text-brand-dark/60' }}">Selesai</h3>
                        <p class="text-sm text-brand-dark/60">Terima kasih telah mempercayakan laundry Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Items Detail -->
            <div class="bg-white rounded-3xl shadow-soft border border-brand-surface overflow-hidden">
                <div class="px-8 py-6 border-b border-brand-surface bg-brand-subtle/30">
                    <h3 class="font-display text-lg font-bold text-brand-black">Rincian Layanan</h3>
                </div>
                <div class="p-8">
                    <table class="w-full text-sm text-left">
                        <thead class="text-brand-dark text-xs uppercase bg-brand-subtle/50 rounded-lg">
                            <tr>
                                <th class="px-4 py-3 font-semibold rounded-l-lg">Layanan</th>
                                <th class="px-4 py-3 font-semibold text-center">Qty</th>
                                <th class="px-4 py-3 font-semibold text-right rounded-r-lg">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-surface">
                            @foreach($transaction->details as $detail)
                            <tr>
                                <td class="px-4 py-4 font-medium text-brand-black">
                                    {{ $detail->service->service_name }}
                                    <div class="text-xs text-brand-dark font-normal mt-0.5">{{ $detail->service->unit }} • Rp {{ number_format($detail->price_at_transaction, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-4 py-4 text-center text-brand-dark">{{ $detail->quantity }}</td>
                                <td class="px-4 py-4 text-right font-bold text-brand-black">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-brand-surface">
                            <tr>
                                <td colspan="2" class="px-4 pt-6 text-right font-medium text-brand-dark">Total Tagihan</td>
                                <td class="px-4 pt-6 text-right text-xl font-display font-bold text-brand-primary">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Info & Payment -->
        <div class="space-y-8">
            
            <!-- Customer Info -->
            <div class="bg-white rounded-3xl shadow-soft p-8 border border-brand-surface">
                <h3 class="font-display text-lg font-bold text-brand-black mb-6">Informasi Pelanggan</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-brand-subtle flex items-center justify-center text-brand-primary mr-3 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-brand-dark">Nama Pelanggan</p>
                            <p class="font-semibold text-brand-black">{{ $transaction->customer->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-brand-subtle flex items-center justify-center text-brand-primary mr-3 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-brand-dark">Kode Nota</p>
                            <p class="font-family-mono font-bold text-brand-black">{{ $transaction->transaction_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Card -->
            <div class="bg-gradient-to-br from-brand-primary to-brand-deep rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                
                <h3 class="font-display text-lg font-bold mb-1 opacity-90">Status Pembayaran</h3>
                
                @if($transaction->payment_status == 'paid')
                    <div class="mt-4 flex items-center bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <div class="w-10 h-10 rounded-full bg-brand-accent flex items-center justify-center text-brand-deep mr-3 shadow-glow">
                             <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-lg">LUNAS</p>
                            <p class="text-xs opacity-75">Terima kasih atas pembayaran Anda</p>
                        </div>
                    </div>
                @else
                    <div class="mt-4 mb-6">
                        <p class="text-3xl font-display font-bold mb-1">Rp {{ number_format($transaction->total_cost - $transaction->total_paid, 0, ',', '.') }}</p>
                        <p class="text-sm opacity-75">Sisa tagihan yang harus dibayar</p>
                    </div>
                    
                    <button 
                        id="pay-button" 
                        class="w-full py-4 bg-white text-brand-deep font-bold rounded-xl shadow-lg hover:bg-brand-surface transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2 ring-2 ring-transparent focus:ring-white"
                    >
                        <span class="text-lg">Bayar Online (QRIS)</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </button>
                    <div id="payment-loading" class="hidden text-center mt-3 text-sm opacity-80 animate-pulse">Menghubungkan ke Payment Gateway...</div>
                @endif
            </div>

            <!-- Help Contact -->
            <div class="text-center">
                <a href="https://wa.me/6281234567890" class="inline-flex items-center text-brand-primary font-medium hover:text-brand-deep transition-colors text-sm">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 4.255 4.045-.809c1.306.391 2.934.332 4.091-.32 1.454-1.002 1.559-2.28 1.559-2.28s1.618-.475 2.106-.723c.316-.16.536-.341.602-.551.053-.169.034-.959-.39-1.282-.249-.19-.714-.403-.984-.537-.253-.122-.505-.175-.765.234-.239.375-.515.753-.787.893-.243.125-.975-.125-2.062-1.218-.949-.953-1.161-1.636-1.047-1.896.16-.364.673-1.144.757-1.341.077-.183.024-.467-.146-.739-.148-.236-1.161-1.954-1.161-1.954s-.308-.432-.619-.387a1.4 1.4 0 0 0-.573.182z"/></svg>
                    Hubungi Bantuan via WhatsApp
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
