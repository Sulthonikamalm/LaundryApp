@extends('layouts.minimal', ['title' => 'Pembayaran Berhasil'])

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-gray-200">
    <div class="max-w-2xl w-full">
        
        <!-- Success Card -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            
            <!-- Icon Header -->
            <div class="px-8 py-12 text-center bg-gradient-to-br from-{{ $message['color'] }}-50 to-{{ $message['color'] }}-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-{{ $message['color'] }}-200 opacity-20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-{{ $message['color'] }}-300 opacity-20 rounded-full -ml-32 -mb-32 blur-3xl"></div>
                
                <div class="relative z-10">
                    @if($message['icon'] === 'check')
                    <div class="w-24 h-24 mx-auto rounded-full bg-{{ $message['color'] }}-500 text-white flex items-center justify-center mb-6 shadow-lg animate-bounce-slow">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    @elseif($message['icon'] === 'clock')
                    <div class="w-24 h-24 mx-auto rounded-full bg-{{ $message['color'] }}-500 text-white flex items-center justify-center mb-6 shadow-lg animate-pulse">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @elseif($message['icon'] === 'truck')
                    <div class="w-24 h-24 mx-auto rounded-full bg-{{ $message['color'] }}-500 text-white flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m-4 0v1a1 1 0 001 1h1m10-3a2 2 0 104 0m-4 0a2 2 0 114 0m-4 0v1a1 1 0 001 1h1"/>
                        </svg>
                    </div>
                    @else
                    <div class="w-24 h-24 mx-auto rounded-full bg-{{ $message['color'] }}-500 text-white flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @endif
                    
                    <h1 class="font-display text-3xl font-bold text-gray-900 mb-2">{{ $message['title'] }}</h1>
                    <p class="text-lg text-{{ $message['color'] }}-700 font-medium">{{ $message['subtitle'] }}</p>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                <p class="text-gray-700 leading-relaxed text-center mb-8">
                    {{ $message['body'] }}
                </p>

                <!-- Transaction Info -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Kode Nota</span>
                        <span class="font-mono font-bold text-gray-900">{{ $transaction->transaction_code }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Total Tagihan</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Sudah Dibayar</span>
                        <span class="font-bold text-green-600">Rp {{ number_format($transaction->total_paid, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->getRemainingBalance() > 0)
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Sisa Tagihan</span>
                        <span class="font-bold text-orange-600">Rp {{ number_format($transaction->getRemainingBalance(), 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('public.tracking.show', $transaction->url_token) }}" 
                       class="block w-full py-4 bg-brand-primary text-white font-bold rounded-2xl text-center hover:bg-brand-deep transition-all duration-300 shadow-lg hover:shadow-xl">
                        Lihat Status Cucian
                    </a>
                    
                    <a href="{{ route('public.tracking') }}" 
                       class="block w-full py-4 bg-gray-100 text-gray-700 font-medium rounded-2xl text-center hover:bg-gray-200 transition-all duration-300">
                        Kembali ke Beranda
                    </a>
                </div>

                <!-- Help Contact -->
                <div class="text-center mt-8 pt-6 border-t border-gray-100">
                    <p class="text-sm text-gray-500 mb-3">Butuh bantuan?</p>
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', config('app.phone'))) }}" 
                       class="inline-flex items-center text-green-600 font-medium hover:text-green-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 4.255 4.045-.809c1.306.391 2.934.332 4.091-.32 1.454-1.002 1.559-2.28 1.559-2.28s1.618-.475 2.106-.723c.316-.16.536-.341.602-.551.053-.169.034-.959-.39-1.282-.249-.19-.714-.403-.984-.537-.253-.122-.505-.175-.765.234-.239.375-.515.753-.787.893-.243.125-.975-.125-2.062-1.218-.949-.953-1.161-1.636-1.047-1.896.16-.364.673-1.144.757-1.341.077-.183.024-.467-.146-.739-.148-.236-1.161-1.954-1.161-1.954s-.308-.432-.619-.387a1.4 1.4 0 0 0-.573.182z"/>
                        </svg>
                        Hubungi WhatsApp Kami
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Info (for pending approval) -->
        @if($message['icon'] === 'clock')
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center">
            <p class="text-sm text-blue-800">
                <strong>💡 Tips:</strong> Anda bisa menutup halaman ini. Kami akan mengirimkan notifikasi WhatsApp setelah pembayaran dikonfirmasi.
            </p>
        </div>
        @endif
    </div>
</div>

<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}
</style>
@endsection
