@extends('layouts.minimal', ['title' => 'Detail Pengiriman'])

@section('content')
<div class="min-h-screen bg-brand-white pb-24 relative">
    
    <!-- Top Nav -->
    <div class="bg-white sticky top-0 z-50 px-6 py-4 shadow-sm flex items-center justify-between">
        <a href="{{ route('driver.dashboard') }}" class="p-2 -ml-2 text-brand-dark hover:text-brand-primary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="font-display font-bold text-lg text-brand-black">Rincian Tugas</h1>
        <div class="w-6"></div> <!-- Spacer -->
    </div>

    <!-- Customer Card -->
    <div class="p-6">
        <div class="bg-brand-deep rounded-[32px] p-8 text-white relative overflow-hidden shadow-xl shadow-brand-deep/30">
            <!-- Decorative Circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-brand-accent opacity-20 rounded-full -ml-8 -mb-8 blur-xl"></div>

            <p class="text-brand-surface opacity-80 text-xs font-bold uppercase tracking-wider mb-2">Penerima</p>
            <h2 class="font-display text-3xl font-bold mb-1">{{ $transaction->customer->name }}</h2>
            <p class="text-brand-surface opacity-90 font-mono text-sm mb-6">{{ $transaction->transaction_code }}</p>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-brand-accent mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="text-sm leading-relaxed">{{ $transaction->customer->address }}</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($transaction->customer->address) }}" target="_blank" class="flex items-center justify-center py-3 bg-white text-brand-deep font-bold rounded-xl shadow-lg hover:bg-brand-surface transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-1.447-.894L15 7m0 13V7"></path></svg>
                    Buka Peta
                </a>
                <a href="https://wa.me/{{ $transaction->customer->phone_number }}" target="_blank" class="flex items-center justify-center py-3 bg-brand-accent text-brand-deep font-bold rounded-xl shadow-lg hover:bg-brand-light transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    Hubungi
                </a>
            </div>
        </div>
    </div>

    <!-- Proof of Delivery Form -->
    <div class="px-6 pb-6">
        <h3 class="font-display text-lg font-bold text-brand-black mb-4">Bukti Pengiriman</h3>
        
        <form action="{{ route('driver.delivery.complete', $transaction->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Custom Camera Input -->
            <div class="relative group">
                <input 
                    type="file" 
                    name="proof_photo" 
                    id="proof_photo" 
                    accept="image/*" 
                    capture="environment"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    onchange="previewImage(this)"
                    required
                >
                <div id="upload-placeholder" class="bg-white border-2 border-dashed border-brand-surface rounded-3xl p-10 text-center transition-all group-hover:border-brand-primary group-hover:bg-brand-subtle/30">
                    <div class="w-16 h-16 bg-brand-subtle rounded-2xl flex items-center justify-center mx-auto mb-4 text-brand-primary group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="font-bold text-brand-black">Ambil Foto Bukti</p>
                    <p class="text-xs text-brand-dark opacity-60 mt-1">Foto penerima atau barang di lokasi</p>
                </div>
                <!-- Image Preview Container -->
                <div id="preview-container" class="hidden relative rounded-3xl overflow-hidden shadow-lg">
                    <img id="image-preview" src="#" alt="Preview" class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-white font-bold flex items-center"><svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Ganti Foto</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-brand-primary uppercase tracking-wider mb-2 ml-1">Catatan (Opsional)</label>
                <textarea 
                    name="notes" 
                    rows="3" 
                    class="w-full p-4 bg-white border border-brand-surface rounded-2xl focus:ring-2 focus:ring-brand-primary focus:border-transparent transition resize-none placeholder-brand-dark/30"
                    placeholder="Contoh: Diterima oleh satpam, ibu sedang keluar..."
                ></textarea>
            </div>

            <!-- Submit -->
            <button 
                type="submit" 
                class="w-full py-4 bg-brand-primary hover:bg-brand-deep text-white font-bold rounded-xl shadow-lg shadow-brand-primary/30 transform active:scale-[0.98] transition-all"
            >
                Selesaikan Pengiriman
            </button>
        </form>
    </div>
</div>

<script>
/**
 * DeepJS: Delivery Detail Interactive Features
 * - Image Preview + Compression
 * - GPS Location Capture
 * - Form Validation
 * - Loading States
 * - Camera Enhancement
 */
(function() {
    'use strict';

    const form = document.querySelector('form');
    const proofInput = document.getElementById('proof_photo');
    const submitBtn = document.querySelector('button[type="submit"]');
    const notesTextarea = document.querySelector('textarea[name="notes"]');

    // ============================================
    // 1. IMAGE PREVIEW + COMPRESSION
    // ============================================
    window.previewImage = async function(input) {
        const file = input.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showToast('error', 'File harus berupa gambar');
            input.value = '';
            return;
        }

        // Validate file size (max 10MB before compression)
        if (file.size > 10 * 1024 * 1024) {
            showToast('error', 'Ukuran file terlalu besar (max 10MB)');
            input.value = '';
            return;
        }

        try {
            // Compress image for faster upload
            const compressed = await compressImage(file);
            
            // Preview
            document.getElementById('image-preview').src = compressed.dataUrl;
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('preview-container').classList.remove('hidden');

            // Show compression info
            const savings = ((1 - compressed.size / file.size) * 100).toFixed(0);
            if (savings > 10) {
                showToast('success', `Gambar dioptimasi (${savings}% lebih kecil)`);
            }

            // Vibrate feedback
            if ('vibrate' in navigator) navigator.vibrate(30);

        } catch (err) {
            console.error('Compression error:', err);
            // Fallback to original preview
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('preview-container').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    };

    async function compressImage(file, maxWidth = 1200, quality = 0.8) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    // Resize if too large
                    if (width > maxWidth) {
                        height = (height * maxWidth) / width;
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    const dataUrl = canvas.toDataURL('image/jpeg', quality);
                    const size = Math.round((dataUrl.length * 3) / 4);

                    resolve({ dataUrl, size });
                };
                img.onerror = reject;
                img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    // ============================================
    // 2. GPS LOCATION CAPTURE
    // ============================================
    let currentLocation = null;

    function captureLocation() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    console.log('📍 Location captured:', currentLocation);
                    
                    // Add hidden input for location
                    let locInput = document.getElementById('delivery_location');
                    if (!locInput) {
                        locInput = document.createElement('input');
                        locInput.type = 'hidden';
                        locInput.name = 'delivery_location';
                        locInput.id = 'delivery_location';
                        form.appendChild(locInput);
                    }
                    locInput.value = JSON.stringify(currentLocation);
                },
                (error) => {
                    console.warn('Location error:', error);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    }

    // Capture location on page load
    captureLocation();

    // ============================================
    // 3. FORM VALIDATION + SUBMISSION
    // ============================================
    if (form) {
        form.addEventListener('submit', async (e) => {
            // Validate photo
            if (!proofInput.files || proofInput.files.length === 0) {
                e.preventDefault();
                showToast('error', 'Foto bukti pengiriman wajib diisi');
                proofInput.parentElement.classList.add('animate-shake');
                setTimeout(() => proofInput.parentElement.classList.remove('animate-shake'), 500);
                return;
            }

            // Prevent double submit
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mengunggah...
                </span>
            `;

            // Vibrate feedback
            if ('vibrate' in navigator) navigator.vibrate([50, 30, 50]);
        });
    }

    // ============================================
    // 4. NOTES TEXTAREA ENHANCEMENT
    // ============================================
    if (notesTextarea) {
        // Auto-resize
        notesTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });

        // Character counter
        const maxLength = 500;
        const counter = document.createElement('div');
        counter.className = 'text-xs text-brand-dark/50 text-right mt-1';
        counter.textContent = `0/${maxLength}`;
        notesTextarea.parentElement.appendChild(counter);

        notesTextarea.addEventListener('input', () => {
            counter.textContent = `${notesTextarea.value.length}/${maxLength}`;
            if (notesTextarea.value.length > maxLength * 0.9) {
                counter.classList.add('text-amber-500');
            } else {
                counter.classList.remove('text-amber-500');
            }
        });
    }

    // ============================================
    // 5. TOAST NOTIFICATIONS
    // ============================================
    function showToast(type, message) {
        const existing = document.querySelector('.toast-notification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `toast-notification fixed top-20 left-1/2 -translate-x-1/2 px-6 py-3 rounded-full shadow-xl z-50 flex items-center gap-2 animate-fade-in-up ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-brand-primary text-white'
        }`;
        toast.innerHTML = `
            ${type === 'error' ? '⚠️' : '✓'}
            <span class="text-sm font-medium">${message}</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // ============================================
    // 6. COPY ADDRESS TO CLIPBOARD
    // ============================================
    document.querySelectorAll('[data-copy]').forEach(el => {
        el.addEventListener('click', () => {
            navigator.clipboard.writeText(el.dataset.copy).then(() => {
                showToast('success', 'Alamat disalin');
                if ('vibrate' in navigator) navigator.vibrate(20);
            });
        });
    });

    // ============================================
    // 7. CAMERA SWITCH (Front/Back)
    // ============================================
    let facingMode = 'environment'; // Default: back camera
    
    const switchCamBtn = document.createElement('button');
    switchCamBtn.type = 'button';
    switchCamBtn.className = 'absolute top-4 right-4 p-2 bg-brand-black/50 text-white rounded-full z-20';
    switchCamBtn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>`;
    
    const previewContainer = document.getElementById('preview-container');
    if (previewContainer) {
        previewContainer.appendChild(switchCamBtn);
    }

    switchCamBtn.addEventListener('click', () => {
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        proofInput.setAttribute('capture', facingMode);
        showToast('success', facingMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan');
    });

    console.log('📦 Delivery Detail JS Loaded');
})();
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-shake { animation: shake 0.5s ease-in-out; }
.animate-fade-in-up { animation: fade-in-up 0.3s ease-out; }
</style>
@endsection
