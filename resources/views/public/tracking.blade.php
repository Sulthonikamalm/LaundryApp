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
                            placeholder="LDR-2026-0001"
                            maxlength="20"
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

@push('scripts')
<script>
/**
 * DeepJS: Public Tracking Interactive Features
 * - Phone Auto-Format
 * - Transaction Code Uppercase
 * - Form Validation
 * - Loading States
 * - Recent Searches (localStorage)
 * - Keyboard Shortcuts
 */
(function() {
    'use strict';

    const form = document.querySelector('form');
    const txCodeInput = document.querySelector('input[name="transaction_code"]');
    const phoneInput = document.querySelector('input[name="phone"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    // ============================================
    // 1. TRANSACTION CODE AUTO-UPPERCASE
    // ============================================
    if (txCodeInput) {
        // Only allow alphanumeric and dashes
        txCodeInput.addEventListener('input', (e) => {
            // Allow manual dashes, just uppercase and filter invalid chars
            e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
        });

        // Auto-add dashes for LDR-YYYY-XXXX format (when typing without dashes)
        txCodeInput.addEventListener('keyup', (e) => {
            if (e.key === 'Backspace' || e.key === 'Delete' || e.key === '-') return;
            
            // Don't auto-format if user already typed a dash manually
            if (e.target.value.includes('-')) return;
            
            const val = e.target.value;
            // Format: LDR (3) + YYYY (4) + XXXX (4) = 11 chars without dashes
            if (val.length === 3) {
                e.target.value = val + '-';
            } else if (val.length === 8) {
                e.target.value = val.slice(0, 3) + '-' + val.slice(3, 7) + '-' + val.slice(7);
            }
        });

        // Show recent searches dropdown
        const recentSearches = getRecentSearches();
        if (recentSearches.length > 0) {
            createRecentSearchesDropdown(txCodeInput, recentSearches);
        }
    }

    // ============================================
    // 2. PHONE NUMBER AUTO-FORMAT
    // ============================================
    if (phoneInput) {
        phoneInput.addEventListener('input', (e) => {
            // Remove non-digits
            let val = e.target.value.replace(/\D/g, '');
            
            // Convert 0 prefix to 62
            if (val.startsWith('0')) {
                val = '62' + val.slice(1);
            }
            
            // Format: 62 812 3456 7890
            if (val.length > 2) {
                val = val.slice(0, 2) + ' ' + val.slice(2);
            }
            if (val.length > 6) {
                val = val.slice(0, 6) + ' ' + val.slice(6);
            }
            if (val.length > 11) {
                val = val.slice(0, 11) + ' ' + val.slice(11);
            }
            
            e.target.value = val.slice(0, 16);
        });

        // Show validation indicator
        phoneInput.addEventListener('blur', (e) => {
            const cleaned = e.target.value.replace(/\D/g, '');
            const isValid = cleaned.length >= 10 && cleaned.length <= 13;
            
            if (e.target.value && !isValid) {
                showInputError(phoneInput, 'Nomor tidak valid');
            } else {
                clearInputError(phoneInput);
            }
        });
    }

    // ============================================
    // 3. FORM VALIDATION + LOADING STATE
    // ============================================
    if (form) {
        form.addEventListener('submit', (e) => {
            // Validate transaction code - minimum format LDR-YYYY-XXXX
            const txCode = txCodeInput?.value.trim();
            if (!txCode || txCode.length < 10) {  // "LDR-2026-1" = 10 chars minimum
                e.preventDefault();
                showInputError(txCodeInput, 'Kode nota tidak lengkap (format: LDR-YYYY-XXXX)');
                txCodeInput?.focus();
                return;
            }

            // Validate phone
            const phone = phoneInput?.value.replace(/\D/g, '');
            if (!phone || phone.length < 10) {
                e.preventDefault();
                showInputError(phoneInput, 'Nomor HP tidak valid');
                phoneInput?.focus();
                return;
            }

            // Prevent double submit
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            // Save to recent searches
            saveRecentSearch(txCodeInput.value, phoneInput.value);

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.innerHTML = `
                <span style="display: flex; align-items: center; justify-content: center;">
                    <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px; margin-right: 8px;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mencari...
                </span>
            `;
        });
    }

    // ============================================
    // 4. RECENT SEARCHES (localStorage)
    // ============================================
    function getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem('silaundry_recent_searches') || '[]');
        } catch {
            return [];
        }
    }

    function saveRecentSearch(code, phone) {
        const searches = getRecentSearches();
        const newSearch = { code, phone, date: Date.now() };
        
        // Remove duplicate and add to front
        const filtered = searches.filter(s => s.code !== code);
        filtered.unshift(newSearch);
        
        // Keep only last 5
        const limited = filtered.slice(0, 5);
        
        localStorage.setItem('silaundry_recent_searches', JSON.stringify(limited));
    }

    function createRecentSearchesDropdown(input, searches) {
        const dropdown = document.createElement('div');
        dropdown.className = 'recent-searches-dropdown';
        dropdown.style.cssText = 'display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-top: 0.5rem; overflow: hidden; z-index: 50;';
        
        dropdown.innerHTML = `
            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f1f1; font-size: 0.75rem; font-weight: 700; color: rgba(0,0,0,0.4); text-transform: uppercase; letter-spacing: 0.05em;">Pencarian Terakhir</div>
            ${searches.map(s => `
                <button type="button" class="recent-item" data-code="${s.code}" data-phone="${s.phone}" style="width: 100%; padding: 1rem; text-align: left; background: none; border: none; cursor: pointer; transition: background 0.2s; display: flex; align-items: center;">
                    <span style="flex: 1; font-family: monospace; font-weight: 600; color: #2e2d2c;">${s.code}</span>
                    <span style="font-size: 0.75rem; color: rgba(0,0,0,0.4);">→</span>
                </button>
            `).join('')}
        `;
        
        input.parentElement.style.position = 'relative';
        input.parentElement.appendChild(dropdown);

        // Show on focus
        input.addEventListener('focus', () => {
            if (input.value === '') {
                dropdown.style.display = 'block';
            }
        });

        // Hide on blur (with delay for click)
        input.addEventListener('blur', () => {
            setTimeout(() => dropdown.style.display = 'none', 150);
        });

        // Fill on click
        dropdown.querySelectorAll('.recent-item').forEach(item => {
            item.addEventListener('click', () => {
                txCodeInput.value = item.dataset.code;
                phoneInput.value = item.dataset.phone;
                dropdown.style.display = 'none';
            });

            item.addEventListener('mouseover', () => {
                item.style.backgroundColor = 'var(--brand-subtle, #f1f1f1)';
            });
            item.addEventListener('mouseout', () => {
                item.style.backgroundColor = 'transparent';
            });
        });
    }

    // ============================================
    // 5. INPUT ERROR HELPERS
    // ============================================
    function showInputError(input, message) {
        clearInputError(input);
        
        const error = document.createElement('p');
        error.className = 'input-error-msg';
        error.style.cssText = 'margin-top: 0.5rem; font-size: 0.75rem; color: #ef4444; padding-left: 1rem; font-weight: 500;';
        error.textContent = message;
        
        input.parentElement.parentElement.appendChild(error);
        input.style.borderColor = '#ef4444';
        
        // Shake animation
        input.parentElement.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => input.parentElement.style.animation = '', 500);
    }

    function clearInputError(input) {
        const existing = input.parentElement.parentElement.querySelector('.input-error-msg');
        if (existing) existing.remove();
        input.style.borderColor = 'transparent';
    }

    // ============================================
    // 6. KEYBOARD SHORTCUTS
    // ============================================
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + Enter to submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            form?.requestSubmit();
        }
        
        // Tab between fields
        if (e.key === 'Tab' && !e.shiftKey && document.activeElement === txCodeInput) {
            e.preventDefault();
            phoneInput?.focus();
        }
    });

    // ============================================
    // 7. ENTRANCE ANIMATION
    // ============================================
    const card = document.querySelector('.max-w-\\[480px\\]');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease-out';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    }

    console.log('🔍 Tracking Page JS Loaded');
})();
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
</style>
@endpush
