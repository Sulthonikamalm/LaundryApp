<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

/**
 * AdminPanelProvider - DeepGreen Admin Theme
 * 
 * DeepUI: Custom theme dengan warna konsisten.
 * DeepPerformance: Lazy load assets dan optimasi render.
 * DeepThinking: Branding "SiLaundry" untuk white-labeling.
 */
class AdminPanelProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // DeepUI: Inject custom CSS & Dark Mode support
        $this->registerCustomStyles();
    }

    /**
     * Register DeepGreen Design System untuk Filament Admin.
     * 
     * DeepUI: Override semua warna default Filament.
     * DeepReasoning: Kasir tidak butuh tahu ini "Laravel/Filament".
     */
    protected function registerCustomStyles(): void
    {
        Filament::registerRenderHook(
            'styles.end',
            fn (): string => $this->getCustomCss()
        );

        Filament::registerRenderHook(
            'scripts.end',
            fn (): string => $this->getCustomJs()
        );
    }

    /**
     * Custom CSS untuk DeepGreen Design System.
     */
    protected function getCustomCss(): string
    {
        return '
        <!-- DeepGreen Design System v2.0 -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <style>
            /* =================================================
               DEEPGREEN DESIGN SYSTEM - FILAMENT ADMIN OVERRIDE
               =================================================
               DeepUI: Warna konsisten dengan brand.
               DeepThinking: Tidak ada referensi "Laravel/Filament".
            */
            
            :root {
                /* DeepGreen Palette (Light Mode) */
                --dg-black: #2e2d2c;
                --dg-dark: #3a4643;
                --dg-deep: #44625c;
                --dg-primary: #4c7d73;
                --dg-medium: #569a8c;
                --dg-light: #5cb9a6;
                --dg-accent: #60d9c3;
                --dg-surface: #b1ece0;
                --dg-subtle: #d7f5ef;
                --dg-white: #ecfaf6;
                
                /* Filament CSS Variables Override */
                --primary-50: 236 250 246;
                --primary-100: 215 245 239;
                --primary-200: 177 236 224;
                --primary-300: 96 217 195;
                --primary-400: 92 185 166;
                --primary-500: 86 154 140;
                --primary-600: 76 125 115;
                --primary-700: 68 98 92;
                --primary-800: 58 70 67;
                --primary-900: 46 45 44;
                
                /* Warning Override - Gold instead of Yellow */
                --warning-50: 255 251 235;
                --warning-100: 254 243 199;
                --warning-200: 253 230 138;
                --warning-300: 252 211 77;
                --warning-400: 251 191 36;
                --warning-500: 245 158 11;
                --warning-600: 217 119 6;
                --warning-700: 180 83 9;
                --warning-800: 146 64 14;
                --warning-900: 120 53 15;
                
                /* Danger/Error - Keep Red but softer */
                --danger-600: 220 38 38;
            }

            /* Dark Mode Variables */
            .dark {
                --dg-black: #ecfaf6;
                --dg-dark: #d7f5ef;
                --dg-deep: #b1ece0;
                --dg-primary: #60d9c3;
                --dg-medium: #5cb9a6;
                --dg-light: #569a8c;
                --dg-accent: #4c7d73;
                --dg-surface: #3a4643;
                --dg-subtle: #2e2d2c;
                --dg-white: #1a1a1a;
            }

            /* Typography Override */
            body, .font-sans, .filament-body {
                font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif !important;
                -webkit-font-smoothing: antialiased;
            }
            
            h1, h2, h3, h4, h5, h6, 
            .font-display,
            .filament-sidebar-brand span,
            .filament-header h1 {
                font-family: "Outfit", ui-sans-serif, system-ui, sans-serif !important;
            }

            /* Sidebar Brand Override */
            .filament-sidebar-brand {
                font-family: "Outfit", sans-serif !important;
                font-weight: 700 !important;
                letter-spacing: -0.025em !important;
            }

            /* Sidebar Active Item */
            .filament-sidebar-item-active,
            .filament-sidebar-item[aria-current="page"] {
                background-color: rgba(76, 125, 115, 0.15) !important;
                color: var(--dg-primary) !important;
                border-color: var(--dg-primary) !important;
            }

            .dark .filament-sidebar-item-active,
            .dark .filament-sidebar-item[aria-current="page"] {
                background-color: rgba(96, 217, 195, 0.15) !important;
                color: var(--dg-accent) !important;
            }

            /* Primary Buttons */
            .filament-button,
            .fi-btn-primary,
            [type="submit"].filament-button {
                background-color: var(--dg-primary) !important;
                border-color: var(--dg-primary) !important;
            }
            
            .filament-button:hover,
            .fi-btn-primary:hover {
                background-color: var(--dg-deep) !important;
                border-color: var(--dg-deep) !important;
            }

            .dark .filament-button,
            .dark .fi-btn-primary {
                background-color: var(--dg-accent) !important;
                border-color: var(--dg-accent) !important;
                color: #1a1a1a !important;
            }

            /* Text Colors */
            .text-primary-600, .text-primary-500 { 
                color: var(--dg-primary) !important; 
            }
            .dark .text-primary-600, .dark .text-primary-500 { 
                color: var(--dg-accent) !important; 
            }
            
            .bg-primary-600, .bg-primary-500 { 
                background-color: var(--dg-primary) !important; 
            }
            .dark .bg-primary-600, .dark .bg-primary-500 { 
                background-color: var(--dg-accent) !important; 
            }

            /* Warning Badge Override (Gold/Amber) */
            .text-warning-700, .text-warning-600 {
                color: #b45309 !important;
            }
            .bg-warning-500, .bg-warning-600 {
                background-color: #f59e0b !important;
            }
            .dark .text-warning-700, .dark .text-warning-600 {
                color: #fcd34d !important;
            }
            .dark .bg-warning-500, .dark .bg-warning-600 {
                background-color: #d97706 !important;
            }

            /* Clean Dashboard - Remove Footer & Documentation Links */
            .filament-footer,
            .filament-main-footer,
            footer[class*="filament"] {
                display: none !important;
            }

            /* Login Page */
            .filament-login-page {
                background: linear-gradient(135deg, #ecfaf6 0%, #d7f5ef 100%) !important;
            }
            .dark .filament-login-page {
                background: linear-gradient(135deg, #1a1a1a 0%, #2e2d2c 100%) !important;
            }

            /* Cards & Surfaces */
            .filament-card,
            .fi-section,
            .filament-tables-container {
                border-color: rgba(177, 236, 224, 0.5) !important;
            }
            .dark .filament-card,
            .dark .fi-section,
            .dark .filament-tables-container {
                border-color: rgba(58, 70, 67, 0.5) !important;
            }

            /* Stats Widget Cards */
            .filament-stats-overview-widget-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .filament-stats-overview-widget-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(76, 125, 115, 0.1);
            }

            /* Loading States */
            .filament-loading-indicator {
                color: var(--dg-primary) !important;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            ::-webkit-scrollbar-track {
                background: rgba(215, 245, 239, 0.5);
            }
            ::-webkit-scrollbar-thumb {
                background: var(--dg-medium);
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: var(--dg-primary);
            }
            .dark ::-webkit-scrollbar-track {
                background: rgba(46, 45, 44, 0.5);
            }
            .dark ::-webkit-scrollbar-thumb {
                background: var(--dg-deep);
            }

            /* Table Row Hover */
            .filament-tables-row:hover {
                background-color: rgba(215, 245, 239, 0.3) !important;
            }
            .dark .filament-tables-row:hover {
                background-color: rgba(58, 70, 67, 0.3) !important;
            }

            /* Focus Ring Override */
            *:focus {
                --tw-ring-color: rgba(76, 125, 115, 0.5) !important;
            }
            .dark *:focus {
                --tw-ring-color: rgba(96, 217, 195, 0.5) !important;
            }

            /* Skeleton Loading Animation */
            @keyframes skeleton-pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            .skeleton {
                animation: skeleton-pulse 1.5s ease-in-out infinite;
                background: linear-gradient(90deg, #d7f5ef 25%, #ecfaf6 50%, #d7f5ef 75%);
                background-size: 200% 100%;
            }
            .dark .skeleton {
                background: linear-gradient(90deg, #3a4643 25%, #44625c 50%, #3a4643 75%);
            }

            /* =====================================================
               DeepUI FIX: Table Header Alignment (Universal Fix)
               ===================================================== */
            
            /* Target generic header container in Filament Tables */
            .filament-tables-header, 
            .fi-ta-header,
            [class*="filament-tables-header-container"],
            div:has(> .filament-tables-header-heading) {
                display: flex !important;
                flex-wrap: wrap;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 1rem;
                padding: 1rem 1rem 1rem 0.5rem !important; /* DeepUI: Geser kiri */
            }

            /* Widget Card Header - Geser ke kiri */
            .filament-widget-card > header,
            .filament-tables-container > header,
            .filament-widget header {
                padding-left: 0.5rem !important;
                text-align: left !important;
            }

            /* Widget Heading - Geser ke kiri */
            .filament-widget-card h2,
            .filament-widget header h3,
            .filament-tables-header h2 {
                margin-left: 0 !important;
                padding-left: 0 !important;
                text-align: left !important;
            }

            /* Force Heading to take available space */
            .filament-tables-header-heading,
            .fi-ta-header-heading,
            h2.filament-tables-heading {
                flex: 1;
                min-width: 200px;
                margin-bottom: 0 !important;
            }

            /* Container actions/search */
            .filament-tables-header-actions,
            .fi-ta-actions,
            .filament-tables-search-container {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            /* Specific Search Input Fix */
            input[type="search"],
            .filament-tables-search-input input {
                min-width: 250px !important;
            }

            /* Description positioning - FORCE BREAK LINE */
            .filament-tables-header p,
            .filament-tables-header .text-sm:not(.filament-tables-search-input input),
            .fi-ta-header-description {
                 flex-basis: 100%;
                 width: 100%;
                 margin-top: 0.25rem;
                 color: var(--dg-medium);
                 font-size: 0.875rem;
                 order: 3; /* Ensure description is visually last */
                 line-height: 1.25;
            }

            /* Actions Container - Keep inline with heading */
            .filament-tables-header-actions,
            .filament-tables-search-container {
                order: 2; /* Search next to Heading (1) */
                margin-left: auto; /* Push to right */
            }

            /* Alert Widget Styling - Red Border */
            .filament-widget[class*="Overdue"],
            .filament-widget[class*="overdue"] {
                border-left: 6px solid #ef4444 !important;
                background-color: #fef2f2 !important;
            }
            
            .dark .filament-widget[class*="Overdue"],
            .dark .filament-widget[class*="overdue"] {
                background-color: rgba(69, 10, 10, 0.4) !important;
                border-color: #ef4444 !important;
            }
        </style>';
    }

    /**
     * Custom JavaScript untuk interaktivitas.
     * 
     * DeepUI: Dark mode persistence.
     */
    protected function getCustomJs(): string
    {
        return '
        <script>
            // DeepUI: Persist dark mode preference
            const darkModeKey = "silaundry_dark_mode";
            
            function initDarkMode() {
                const stored = localStorage.getItem(darkModeKey);
                if (stored === "dark") {
                    document.documentElement.classList.add("dark");
                } else if (stored === "light") {
                    document.documentElement.classList.remove("dark");
                }
            }
            
            initDarkMode();
        </script>';
    }
}
