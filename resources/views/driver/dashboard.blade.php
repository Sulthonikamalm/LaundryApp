<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kurir | LaundryApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between max-w-lg mx-auto">
            <div>
                <p class="text-sm opacity-80">Selamat datang,</p>
                <h1 class="font-bold">{{ $driver->name }}</h1>
            </div>
            <form action="{{ route('driver.logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-300 rounded-xl text-green-700 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-100 border border-red-300 rounded-xl text-red-700 text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-3xl font-bold text-indigo-600">{{ $pendingDeliveries->count() }}</p>
                <p class="text-sm text-gray-500">Menunggu Pengiriman</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-3xl font-bold text-green-600">{{ $myDeliveries->where('status', 'delivered')->count() }}</p>
                <p class="text-sm text-gray-500">Selesai Hari Ini</p>
            </div>
        </div>

        <!-- Pending Deliveries -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h2 class="font-semibold text-gray-700">📦 Siap Dikirim</h2>
            </div>
            
            @forelse($pendingDeliveries as $tx)
                <div class="p-4 border-b last:border-b-0 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $tx->transaction_code }}</p>
                            <p class="text-sm text-gray-500">{{ $tx->customer->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                📍 {{ Str::limit($tx->customer->address, 40) }}
                            </p>
                        </div>
                        <form action="{{ route('driver.delivery.start', $tx) }}" method="POST">
                            @csrf
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition"
                            >
                                Ambil
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">
                    <div class="text-4xl mb-2">📭</div>
                    <p>Tidak ada pengiriman pending</p>
                </div>
            @endforelse
        </div>

        <!-- My Active Deliveries -->
        @php
            $activeDeliveries = $myDeliveries->where('status', '!=', 'delivered');
        @endphp
        
        @if($activeDeliveries->count() > 0)
            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-yellow-200 bg-yellow-100">
                    <h2 class="font-semibold text-yellow-800">🚗 Sedang Diantar</h2>
                </div>
                
                @foreach($activeDeliveries as $shipment)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $shipment->transaction->transaction_code }}</p>
                                <p class="text-sm text-gray-500">{{ $shipment->transaction->customer->name }}</p>
                            </div>
                            <a 
                                href="{{ route('driver.delivery.show', $shipment->transaction) }}"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition"
                            >
                                Selesaikan →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Completed Today -->
        @php
            $completedToday = $myDeliveries->where('status', 'delivered');
        @endphp
        
        @if($completedToday->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b bg-gray-50">
                    <h2 class="font-semibold text-gray-700">✅ Selesai Hari Ini</h2>
                </div>
                
                @foreach($completedToday as $shipment)
                    <div class="p-4 border-b last:border-b-0 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ $shipment->transaction->transaction_code }}</p>
                            <p class="text-sm text-gray-500">{{ $shipment->delivered_at?->format('H:i') }}</p>
                        </div>
                        @if($shipment->proof_image_url)
                            <img src="{{ $shipment->proof_image_url }}" class="w-12 h-12 rounded-lg object-cover" alt="Bukti">
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bottom Refresh Button -->
    <div class="fixed bottom-4 left-0 right-0 flex justify-center">
        <button 
            onclick="window.location.reload()"
            class="px-6 py-3 bg-white shadow-xl rounded-full text-indigo-600 font-medium hover:bg-gray-50 transition"
        >
            🔄 Refresh Data
        </button>
    </div>
</body>
</html>
