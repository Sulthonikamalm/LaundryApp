<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengiriman {{ $transaction->transaction_code }} | LaundryApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-4 sticky top-0 z-10">
        <div class="flex items-center gap-4 max-w-lg mx-auto">
            <a href="{{ route('driver.dashboard') }}" class="p-2 hover:bg-white/20 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-sm opacity-80">Pengiriman</p>
                <h1 class="font-bold">{{ $transaction->transaction_code }}</h1>
            </div>
        </div>
    </div>

    <div class="max-w-lg mx-auto px-4 py-6 space-y-4">
        <!-- Customer Info -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="font-semibold text-gray-700 mb-4">👤 Informasi Pelanggan</h2>
            
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">📱</div>
                    <div>
                        <p class="text-sm text-gray-500">Telepon</p>
                        <a href="tel:{{ $transaction->customer->phone_number }}" class="font-medium text-indigo-600 hover:underline">
                            {{ $transaction->customer->phone_number }}
                        </a>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="text-2xl">📍</div>
                    <div>
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p class="font-medium text-gray-800">{{ $transaction->customer->address }}</p>
                        <a 
                            href="https://maps.google.com/?q={{ urlencode($transaction->customer->address) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1 mt-2 text-sm text-green-600 hover:underline"
                        >
                            🗺️ Buka di Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📦 Item Pesanan</h2>
            
            @foreach($transaction->details as $detail)
                <div class="flex justify-between py-2 {{ !$loop->last ? 'border-b' : '' }}">
                    <span class="text-gray-700">{{ $detail->service->service_name }}</span>
                    <span class="text-gray-500">{{ $detail->quantity }} {{ $detail->service->unit }}</span>
                </div>
            @endforeach
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <span class="text-gray-700 font-semibold">Status Bayar</span>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $transaction->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $transaction->payment_status === 'unpaid' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ $transaction->payment_status === 'paid' ? 'LUNAS' : ($transaction->payment_status === 'partial' ? 'SEBAGIAN' : 'BELUM BAYAR') }}
                </span>
            </div>
            @if($transaction->payment_status !== 'paid')
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        ⚠️ Sisa tagihan: <strong>Rp {{ number_format($transaction->total_cost - $transaction->total_paid, 0, ',', '.') }}</strong>
                    </p>
                </div>
            @endif
        </div>

        <!-- Complete Delivery Form -->
        @if($shipment && $shipment->status !== 'delivered')
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
                <h2 class="font-bold text-lg mb-4">📸 Selesaikan Pengiriman</h2>
                <p class="text-sm opacity-90 mb-4">Ambil foto sebagai bukti pengiriman telah diterima pelanggan.</p>
                
                <form action="{{ route('driver.delivery.complete', $transaction) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Photo Upload -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Foto Bukti *</label>
                        <div class="relative">
                            <input 
                                type="file" 
                                name="proof_photo"
                                accept="image/*"
                                capture="environment"
                                class="hidden"
                                id="photo-input"
                                required
                            >
                            <label 
                                for="photo-input"
                                class="flex flex-col items-center justify-center w-full h-40 bg-white/20 border-2 border-dashed border-white/50 rounded-xl cursor-pointer hover:bg-white/30 transition"
                                id="photo-label"
                            >
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm font-medium">Tap untuk Ambil Foto</span>
                            </label>
                            <img id="photo-preview" class="hidden w-full h-40 object-cover rounded-xl" alt="Preview">
                        </div>
                        @error('proof_photo')
                            <p class="mt-1 text-sm text-red-200">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Catatan (opsional)</label>
                        <textarea 
                            name="notes"
                            rows="2"
                            placeholder="Contoh: Diterima oleh ibu rumah tangga"
                            class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl placeholder-white/50 text-white focus:outline-none focus:ring-2 focus:ring-white/50"
                        ></textarea>
                    </div>

                    <!-- Submit -->
                    <button 
                        type="submit"
                        class="w-full py-4 bg-white text-green-600 font-bold rounded-xl hover:bg-gray-100 transition shadow-lg text-lg"
                    >
                        ✅ Selesaikan Pengiriman
                    </button>
                </form>
            </div>
        @else
            <div class="bg-green-100 border-2 border-green-300 rounded-2xl p-6 text-center">
                <div class="text-4xl mb-2">✅</div>
                <p class="font-semibold text-green-700">Pengiriman Sudah Selesai</p>
                <p class="text-sm text-green-600 mt-1">{{ $shipment?->delivered_at?->format('d/m/Y H:i') }}</p>
            </div>
        @endif

        <!-- Call Customer Button -->
        <a 
            href="tel:{{ $transaction->customer->phone_number }}"
            class="flex items-center justify-center gap-2 w-full py-4 bg-white rounded-xl shadow-lg text-indigo-600 font-semibold hover:bg-gray-50 transition"
        >
            📞 Hubungi Pelanggan
        </a>
    </div>

    <script>
        // Photo preview
        document.getElementById('photo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                    document.getElementById('photo-preview').classList.remove('hidden');
                    document.getElementById('photo-label').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
