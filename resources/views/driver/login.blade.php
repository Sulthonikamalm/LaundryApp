<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kurir | LaundryApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-900 to-purple-900 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="text-5xl mb-2">🚚</div>
            <h1 class="text-2xl font-bold text-white">Driver Portal</h1>
            <p class="text-indigo-200 text-sm">Masuk dengan PIN Anda</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-6">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('driver.login.submit') }}" method="POST">
                @csrf
                
                <!-- Username -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username / Email</label>
                    <input 
                        type="text" 
                        name="username"
                        value="{{ old('username') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <!-- PIN -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PIN (6 digit)</label>
                    <input 
                        type="password" 
                        name="pin"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        inputmode="numeric"
                        class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-center text-2xl tracking-[0.5em] font-mono"
                        placeholder="● ● ● ● ● ●"
                        required
                    >
                </div>

                <!-- Submit -->
                <button 
                    type="submit"
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition shadow-lg"
                >
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-indigo-200 text-xs mt-6">
            Hubungi admin jika lupa PIN
        </p>
    </div>
</body>
</html>
