@extends('layouts.minimal', ['title' => 'Detail Pengiriman - SiLaundry'])

@section('content')
{{-- 
    DeepUI Pro: Delivery Detail
    Design System: Task-Focused UI, Clear Actions
    Mobile-First, GPS-Enabled, Camera-Ready
--}}
<div class="min-h-screen bg-slate-50">
    
    {{-- Navigation Bar --}}
    <nav class="bg-white sticky top-0 z-50 px-4 py-3 shadow-sm flex items-center justify-between border-b border-slate-100">
        <a href="{{ route('driver.dashboard') }}" class="p-2 -ml-2 text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="font-bold text-slate-900">Detail Pengiriman</h1>
        <div class="w-9"></div>
    </nav>

    {{-- Customer Info Card --}}
    <section class="p-4">
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Penerima</p>
                    <h2 class="text-xl font-bold mt-1">{{ $transaction->customer->name }}</h2>
                </div>
                <span class="px-3 py-1 bg-white/10 text-white/80 text-xs font-mono rounded-lg border border-white/10">
                    {{ $transaction->transaction_code }}
                </span>
            </div>

            {{-- Address --}}
            <div class="bg-white/5 rounded-xl p-4 border border-white/10 mb-5">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-emerald-500/20 rounded-lg">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-slate-300 leading-relaxed flex-1">{{ $transaction->customer->address }}</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($transaction->customer->address) }}" 
                   target="_blank" 
                   class="flex items-center justify-center py-3 bg-white text-slate-900 font-semibold rounded-xl shadow-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Peta
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $transaction->customer->phone_number) }}" 
                   target="_blank" 
                   class="flex items-center justify-center py-3 bg-emerald-500 text-white font-semibold rounded-xl shadow-lg hover:bg-emerald-400 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Hubungi
                </a>
            </div>
        </div>
    </section>

    {{-- Proof of Delivery Form --}}
    <section class="px-4 pb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                Bukti Pengiriman
            </h3>
            
            <form action="{{ route('driver.delivery.complete', $transaction->id) }}" method="POST" enctype="multipart/form-data" id="deliveryForm" class="space-y-5">
                @csrf

                {{-- Photo Upload --}}
                <div class="relative" id="photoUploadContainer">
                    <input 
                        type="file" 
                        name="proof_photo" 
                        id="proofPhoto" 
                        accept="image/*" 
                        capture="environment"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        required
                    >
                    
                    {{-- Upload Placeholder --}}
                    <div id="uploadPlaceholder" class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-emerald-400 hover:bg-emerald-50/50 transition-all">
                        <div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-slate-700">Ambil Foto Bukti</p>
                        <p class="text-xs text-slate-400 mt-1">Foto penerima atau barang di lokasi</p>
                    </div>
                    
                    {{-- Preview Container --}}
                    <div id="previewContainer" class="hidden relative rounded-xl overflow-hidden">
                        <img id="imagePreview" src="" alt="Preview" class="w-full h-48 object-cover">
                        <div class="absolute bottom-3 left-3 right-3 flex justify-between">
                            <span class="px-3 py-1 bg-black/60 text-white text-xs rounded-lg backdrop-blur-sm">
                                ✓ Foto tersimpan
                            </span>
                            <span class="px-3 py-1 bg-white/90 text-slate-700 text-xs rounded-lg font-medium">
                                Tap untuk ganti
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea 
                        name="notes" 
                        id="notesField"
                        rows="2" 
                        class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all resize-none text-slate-700 placeholder-slate-400"
                        placeholder="Contoh: Dititip ke satpam..."
                    ></textarea>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    id="submitBtn"
                    class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Selesaikan Pengiriman
                    </span>
                </button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
/**
 * DeepJS Pro: Delivery Detail
 * - Image preview with compression
 * - GPS location capture
 * - Form validation
 * - Loading states
 */
(function() {
    'use strict';

    const form = document.getElementById('deliveryForm');
    const proofInput = document.getElementById('proofPhoto');
    const submitBtn = document.getElementById('submitBtn');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');

    // Image Preview Handler
    proofInput.addEventListener('change', async function() {
        const file = this.files[0];
        if (!file) return;

        // Validate
        if (!file.type.startsWith('image/')) {
            showToast('File harus berupa gambar', 'error');
            this.value = '';
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            uploadPlaceholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);

        // Haptic
        if ('vibrate' in navigator) navigator.vibrate(20);
    });

    // GPS Capture
    let gpsLocation = null;
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                gpsLocation = {
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude,
                    accuracy: pos.coords.accuracy
                };
                
                // Add hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delivery_location';
                input.value = JSON.stringify(gpsLocation);
                form.appendChild(input);
                
                console.log('📍 GPS captured:', gpsLocation);
            },
            (err) => console.warn('GPS error:', err),
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // Form Submit Handler
    form.addEventListener('submit', function(e) {
        // Validate photo
        if (!proofInput.files || proofInput.files.length === 0) {
            e.preventDefault();
            showToast('Foto bukti wajib diisi', 'error');
            document.getElementById('photoUploadContainer').classList.add('animate-shake');
            setTimeout(() => document.getElementById('photoUploadContainer').classList.remove('animate-shake'), 500);
            return;
        }

        // Prevent double submit
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }

        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Mengunggah...
            </span>
        `;

        if ('vibrate' in navigator) navigator.vibrate([50, 30, 50]);
    });

    // Toast Notification
    function showToast(message, type = 'success') {
        const existing = document.querySelector('.toast-msg');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `toast-msg fixed top-20 left-1/2 -translate-x-1/2 px-5 py-3 rounded-full shadow-xl z-50 flex items-center gap-2 text-sm font-medium ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white'
        }`;
        toast.innerHTML = `${type === 'error' ? '⚠️' : '✓'} ${message}`;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.remove(), 3000);
    }

    console.log('🚚 Delivery Detail Ready');
})();
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
.animate-shake { animation: shake 0.3s ease-in-out; }
</style>
@endpush
