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
    function previewImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
