@extends('layouts.minimal', ['title' => 'Login Staff Laundry'])

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-brand-black text-brand-white relative">
    
    <!-- Background Texture -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#4c7d73 1px, transparent 1px); background-size: 24px 24px;"></div>
    <div class="absolute top-0 w-full h-1/2 bg-gradient-to-b from-brand-deep/20 to-transparent pointer-events-none"></div>

    <div class="w-full max-w-sm z-10">
        <!-- Brand Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-deep rounded-2xl mb-4 text-brand-accent shadow-glow border border-brand-primary">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h1 class="font-display text-2xl font-bold tracking-wide">Portal Kurir</h1>
            <p class="text-brand-surface opacity-60 text-sm mt-1">Akses khusus staf internal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-brand-dark/50 backdrop-blur-md border border-brand-deep rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <!-- Glow Effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-accent rounded-full blur-[80px] opacity-20 pointer-events-none"></div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-sm text-red-200">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('driver.login.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-brand-surface uppercase tracking-wider mb-2 ml-1">PIN Akses</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-brand-primary group-focus-within:text-brand-accent transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input 
                            type="password" 
                            name="pin"
                            class="block w-full pl-11 pr-4 py-4 bg-brand-black/50 border border-brand-deep rounded-xl text-white placeholder-brand-surface/30 focus:ring-2 focus:ring-brand-accent/50 focus:border-brand-accent transition-all duration-200 font-mono text-center text-2xl tracking-widest"
                            placeholder="••••••"
                            maxlength="6"
                            inputmode="numeric"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <button 
                    type="submit"
                    class="w-full py-4 bg-brand-primary hover:bg-brand-medium text-white font-bold rounded-xl shadow-lg shadow-brand-primary/20 transition-all transform hover:translate-y-[-2px] active:translate-y-[0px]"
                >
                    Masuk Portal
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-brand-surface opacity-40">
            Lupa PIN? Hubungi Owner
        </p>
    </div>
</div>
@endsection
