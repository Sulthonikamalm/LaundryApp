<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Status Cucian {{ $transaction['transaction_code'] }} | LaundryApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .step-line { height: 3px; }
        .step-active { background-color: #10B981; }
        .step-inactive { background-color: #E5E7EB; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="gradient-bg text-white py-6 px-4">
        <div class="max-w-md mx-auto">
            <a href="{{ route('public.tracking') }}" class="inline-flex items-center text-sm opacity-80 hover:opacity-100 mb-2">
                ← Cari Lagi
            </a>
            <h1 class="text-xl font-bold">{{ $transaction['transaction_code'] }}</h1>
            <p class="text-sm opacity-90">{{ $transaction['customer_name'] }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-md mx-auto px-4 py-6 space-y-4">

        <!-- Status Stepper -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-6">Status Pesanan</h2>
            
            @php
                $steps = [
                    'pending' => ['icon' => '📋', 'label' => 'Diterima'],
                    'processing' => ['icon' => '🧼', 'label' => 'Proses'],
                    'ready' => ['icon' => '✅', 'label' => 'Siap'],
                    'completed' => ['icon' => '🏠', 'label' => 'Selesai'],
                ];
                $currentIndex = array_search($transaction['status'], array_keys($steps));
                $isCancelled = $transaction['status'] === 'cancelled';
            @endphp

            @if($isCancelled)
                <div class="text-center py-4">
                    <div class="text-4xl mb-2">❌</div>
                    <p class="text-red-600 font-semibold">Pesanan Dibatalkan</p>
                </div>
            @else
                <div class="flex justify-between items-center relative">
                    <!-- Progress Line -->
                    <div class="absolute top-5 left-0 right-0 h-1 flex -z-10">
                        @foreach(array_keys($steps) as $index => $key)
                            @if($index < count($steps) - 1)
                                <div class="flex-1 step-line {{ $index < $currentIndex ? 'step-active' : 'step-inactive' }}"></div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Steps -->
                    @foreach($steps as $key => $step)
                        @php
                            $stepIndex = array_search($key, array_keys($steps));
                            $isActive = $stepIndex <= $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center {{ $isCurrent ? 'pulse' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl 
                                {{ $isActive ? 'bg-green-100' : 'bg-gray-100' }} 
                                {{ $isCurrent ? 'ring-4 ring-green-300' : '' }}">
                                {{ $step['icon'] }}
                            </div>
                            <span class="text-xs font-medium mt-2 {{ $isActive ? 'text-green-700' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- Current Status Message -->
                <div class="mt-6 p-4 rounded-xl {{ $transaction['status'] === 'ready' ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                    <p class="text-center font-medium {{ $transaction['status'] === 'ready' ? 'text-green-700' : 'text-gray-700' }}">
                        {{ $transaction['status_label'] }}
                    </p>
                    @if($transaction['status'] === 'ready')
                        <p class="text-center text-sm text-green-600 mt-1">
                            Cucian Anda sudah selesai dan siap diambil/diantar! 🎉
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Detail Pesanan</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tanggal Order</span>
                    <span class="font-medium">{{ $transaction['order_date'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Estimasi Selesai</span>
                    <span class="font-medium">{{ $transaction['estimated_completion'] ?? '-' }}</span>
                </div>
                <hr class="my-3">
                
                <!-- Items -->
                @foreach($transaction['items'] as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $item['service'] }}</span>
                        <span class="text-gray-500">{{ $item['quantity'] }} {{ $item['unit'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Pembayaran</h2>
            
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $transaction['payment_status'] === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $transaction['payment_status'] === 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $transaction['payment_status'] === 'unpaid' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ $transaction['payment_status_label'] }}
                    </span>
                </div>
                @if($transaction['remaining_balance'] > 0)
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Sisa Tagihan</p>
                        <p class="text-lg font-bold text-red-600">
                            Rp {{ number_format($transaction['remaining_balance'], 0, ',', '.') }}
                        </p>
                    </div>
                @endif
            </div>

            @if($transaction['remaining_balance'] > 0 && !$isCancelled)
                <button 
                    id="pay-button"
                    class="mt-4 w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition shadow-lg">
                    💳 Bayar Sekarang
                </button>
            @endif
        </div>

        <!-- Delivery Proof (if delivered) -->
        @if($transaction['shipment'] && $transaction['shipment']['status'] === 'delivered')
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Bukti Pengiriman</h2>
                
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">
                        Diantar pada {{ $transaction['shipment']['delivered_at'] }}
                    </p>
                    @if($transaction['shipment']['proof_url'])
                        <img 
                            src="{{ $transaction['shipment']['proof_url'] }}" 
                            alt="Bukti Pengiriman"
                            class="rounded-xl mx-auto max-h-64 object-cover"
                        >
                    @endif
                </div>
            </div>
        @endif

        <!-- Feedback CTA (if completed) -->
        @if($transaction['status'] === 'completed')
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white text-center">
                <p class="font-semibold mb-2">Terima kasih telah menggunakan layanan kami! 🙏</p>
                <p class="text-sm opacity-90 mb-4">Ada masukan atau komplain? Hubungi kami langsung.</p>
                <a 
                    href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20memberikan%20feedback%20untuk%20pesanan%20{{ $transaction['transaction_code'] }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-purple-600 font-semibold rounded-xl hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Chat WhatsApp
                </a>
            </div>
        @endif

    </div>

    <!-- Auto-refresh for processing status -->
    @if(in_array($transaction['status'], ['pending', 'processing']))
        <script>
            // DeepState: Polling setiap 30 detik untuk status update
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        </script>
    @endif

    <!-- Midtrans Payment Script -->
    <script>
        document.getElementById('pay-button')?.addEventListener('click', function() {
            // Get snap token from server
            fetch('/api/payment/{{ $transaction['transaction_code'] }}/snap-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            window.location.reload();
                        },
                        onPending: function(result) {
                            alert('Pembayaran pending. Silakan selesaikan pembayaran.');
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                        }
                    });
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    </script>
</body>
</html>
