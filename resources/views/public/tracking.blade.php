@extends('layouts.minimal', ['title' => 'Cek Status Laundry'])

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute top-0 right-0 w-full h-full pointer-events-none" style="background: radial-gradient(ellipse at 80% 20%, rgba(177, 236, 224, 0.3) 0%, transparent 50%);"></div>
    <div class="absolute bottom-0 left-0 w-full h-full pointer-events-none" style="background: radial-gradient(ellipse at 20% 80%, rgba(86, 154, 140, 0.1) 0%, transparent 50%);"></div>

    <!-- Main Card -->
    <div class="w-full max-w-[480px] bg-white rounded-[40px] shadow-2xl p-10 z-10 relative border border-brand-surface" style="border-color: rgba(177, 236, 224, 0.5);">
        
        <!-- Brand / Logo Area -->
        <div class="text-center mb-12">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background-color: rgba(76, 125, 115, 0.05); border-radius: 28px; margin-bottom: 1.5rem; color: var(--brand-primary); box-shadow: 0 0 0 1px rgba(76,125,115,0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 40px; height: 40px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <h1 class="font-display text-4xl font-bold text-brand-black mb-3 tracking-tight">SiLaundry</h1>
            <p class="text-brand-dark opacity-60 text-base leading-relaxed" style="max-width: 320px; margin: 0 auto;">Lacak status cucian Anda dengan mudah dan cepat tanpa ribet.</p>
        </div>

        <!-- Feedback Messages -->
        @if(isset($error) || session('error'))
            <div class="mb-8 animate-fade-in-up" style="padding: 1rem; background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 1rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <div style="padding: 0.5rem; background-color: #fee2e2; border-radius: 9999px; color: #ef4444; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 style="font-weight: 700; color: #7f1d1d; font-size: 0.875rem; margin-bottom: 0.25rem;">Pencarian Gagal</h4>
                    <p style="font-size: 0.875rem; color: #b91c1c; line-height: 1.375;">{{ $error ?? session('error') }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('public.tracking.search') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Transaction Code Input -->
                <div class="group">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: rgba(46, 45, 44, 0.7); margin-bottom: 0.5rem; padding-left: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Kode Nota</label>
                    <div class="relative transition-all duration-300" style="transform: translateZ(0);">
                        <div style="position: absolute; top: 50%; left: 0; transform: translateY(-50%); padding-left: 1.25rem; pointer-events: none;">
                            <svg style="height: 24px; width: 24px; color: rgba(58, 70, 67, 0.3); transition: color 0.2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="transaction_code"
                            value="{{ old('transaction_code', $transaction_code ?? '') }}"
                            style="display: block; width: 100%; padding: 1.25rem 1rem 1.25rem 3.5rem; background-color: var(--brand-bg); border-radius: 1rem; border: 2px solid transparent; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 1.125rem; font-weight: 500; color: var(--brand-black); transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);"
                            placeholder="LDR-XXXX-XXXX"
                            required
                            autocomplete="off"
                            onfocus="this.style.borderColor='rgba(76,125,115,0.2)'; this.style.backgroundColor='#fff'; this.style.boxShadow='0 0 0 4px rgba(76,125,115,0.05)';"
                            onblur="this.style.borderColor='transparent'; this.style.backgroundColor='var(--brand-bg)'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.06)';"
                        >
                    </div>
                    @error('transaction_code') <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #ef4444; padding-left: 1rem; font-weight: 500;">{{ $message }}</p> @enderror
                </div>

                <!-- Phone Input -->
                <div class="group">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: rgba(46, 45, 44, 0.7); margin-bottom: 0.5rem; padding-left: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Nomor Handphone</label>
                    <div class="relative transition-all duration-300" style="transform: translateZ(0);">
                        <div style="position: absolute; top: 50%; left: 0; transform: translateY(-50%); padding-left: 1.25rem; pointer-events: none;">
                            <svg style="height: 24px; width: 24px; color: rgba(58, 70, 67, 0.3); transition: color 0.2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            name="phone"
                            value="{{ old('phone') }}"
                            style="display: block; width: 100%; padding: 1.25rem 1rem 1.25rem 3.5rem; background-color: var(--brand-bg); border-radius: 1rem; border: 2px solid transparent; font-size: 1.125rem; font-weight: 500; color: var(--brand-black); transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);"
                            placeholder="0812..."
                            required
                            onfocus="this.style.borderColor='rgba(76,125,115,0.2)'; this.style.backgroundColor='#fff'; this.style.boxShadow='0 0 0 4px rgba(76,125,115,0.05)';"
                            onblur="this.style.borderColor='transparent'; this.style.backgroundColor='var(--brand-bg)'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.06)';"
                        >
                    </div>
                    @error('phone') <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #ef4444; padding-left: 1rem; font-weight: 500;">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                class="w-full group relative overflow-hidden"
                style="padding: 1.25rem; background-color: var(--brand-primary); color: #fff; font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 1.125rem; border-radius: 1rem; border: none; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(76, 125, 115, 0.3); transition: all 0.3s; margin-top: 2rem;"
                onmouseover="this.style.backgroundColor='var(--brand-deep)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 20px 25px -5px rgba(76, 125, 115, 0.4)';"
                onmouseout="this.style.backgroundColor='var(--brand-primary)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px -3px rgba(76, 125, 115, 0.3)';"
                onmousedown="this.style.transform='translateY(1px)';"
                onmouseup="this.style.transform='translateY(-2px)';"
            >
                <span style="position: relative; z-index: 10;">Lacak Pesanan</span>
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-12 text-center">
            <p style="font-size: 0.875rem; color: rgba(58, 70, 67, 0.5); font-weight: 500;">
                Ada masalah? <a href="https://wa.me/6281234567890" style="color: var(--brand-primary); font-weight: 700; text-decoration: underline; text-decoration-color: transparent; transition: text-decoration-color 0.2s;" onmouseover="this.style.textDecorationColor='rgba(76,125,115,0.3)';" onmouseout="this.style.textDecorationColor='transparent';">Hubungi CS</a>
            </p>
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(177, 236, 224, 0.5);">
                <a href="{{ route('driver.login') }}" style="display: inline-flex; align-items: center; font-size: 0.75rem; font-weight: 700; color: rgba(58, 70, 67, 0.4); text-transform: uppercase; letter-spacing: 0.1em; transition: color 0.2s; padding: 0.5rem 1rem; border-radius: 0.5rem;" onmouseover="this.style.color='var(--brand-black)'; this.style.backgroundColor='rgba(215, 245, 239, 0.5)';" onmouseout="this.style.color='rgba(58, 70, 67, 0.4)'; this.style.backgroundColor='transparent';">
                    <svg style="width: 12px; height: 12px; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Akses Pegawai
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
