<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Cek Status Laundry | LaundryApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="gradient-bg text-white py-8 px-4">
        <div class="max-w-md mx-auto text-center">
            <div class="text-4xl mb-2">🧺</div>
            <h1 class="text-2xl font-bold">Cek Status Cucian</h1>
            <p class="text-sm opacity-90 mt-1">Masukkan kode nota dan nomor HP Anda</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="max-w-md mx-auto px-4 -mt-6">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            @if(isset($error))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-2 text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ $error }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('public.tracking.search') }}" method="POST">
                @csrf
                
                <!-- Kode Nota Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Nota
                    </label>
                    <input 
                        type="text" 
                        name="transaction_code"
                        value="{{ old('transaction_code', $transaction_code ?? '') }}"
                        placeholder="Contoh: LDR-2026-0001"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition text-center font-mono text-lg uppercase"
                        required
                        autocomplete="off"
                    >
                    @error('transaction_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor HP Input -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor HP Terdaftar
                    </label>
                    <input 
                        type="tel" 
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition text-center text-lg"
                        required
                        autocomplete="tel"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 text-center">
                        Nomor HP yang digunakan saat mendaftar
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-indigo-700 transition transform hover:scale-[1.02] active:scale-[0.98] shadow-lg"
                >
                    🔍 Cek Status
                </button>
            </form>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-blue-50 rounded-xl">
                <p class="text-xs text-blue-700">
                    <strong>💡 Tips:</strong> Kode nota dapat ditemukan pada struk/nota yang Anda terima saat menyerahkan cucian.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="max-w-md mx-auto px-4 py-8 text-center">
        <p class="text-sm text-gray-500">
            Ada pertanyaan? 
            <a href="https://wa.me/6281234567890" target="_blank" class="text-purple-600 font-medium hover:underline">
                Hubungi WhatsApp
            </a>
        </p>
    </div>
</body>
</html>
