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
        <!-- DeepGreen Design System v3.0 (Premium Edition) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <style>
            /* =================================================
               DEEPGREEN PREMIUM DESIGN SYSTEM
               =================================================
               DeepUI: Glassmorphism + Soft Shadows + Premium Feel
            */
            
            :root {
                /* Premium Color Palette */
                --dg-black: #0f172a;
                --dg-dark: #334155;
                --dg-deep: #0369a1;
                --dg-primary: #0ea5e9;
                --dg-medium: #38bdf8;
                --dg-light: #e0f2fe;
                --dg-accent: #7dd3fc;
                --dg-surface: #f8fafc;
                --dg-subtle: #f1f5f9;
                --dg-white: #ffffff;
                
                /* Glassmorphism */
                --glass-bg: rgba(255, 255, 255, 0.7);
                --glass-border: rgba(255, 255, 255, 0.3);
                --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
                
                /* Premium Shadows */
                --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
                --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.06);
                --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.08);
                --shadow-xl: 0 12px 40px rgba(0, 0, 0, 0.12);
                
                /* Filament Override */
                --primary-50: 240 249 255;
                --primary-100: 224 242 254;
                --primary-200: 186 230 253;
                --primary-300: 125 211 252;
                --primary-400: 56 189 248;
                --primary-500: 14 165 233;
                --primary-600: 2 132 199;
                --primary-700: 3 105 161;
                --primary-800: 7 89 133;
                --primary-900: 12 74 110;
                
                /* Warning Override - Amber */
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
                
                /* Danger/Error - Red */
                --danger-600: 220 38 38;
            }

            .dark {
                --dg-black: #f8fafc;
                --dg-dark: #e2e8f0;
                --dg-surface: #1e293b;
                --dg-subtle: #0f172a;
                --glass-bg: rgba(30, 41, 59, 0.7);
                --glass-border: rgba(255, 255, 255, 0.1);
            }

            /* Typography Override - Inter */
            body, .font-sans, .filament-body {
                font-family: "Inter", ui-sans-serif, system-ui, sans-serif !important;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                letter-spacing: -0.011em;
            }
            
            h1, h2, h3, h4, h5, h6, 
            .font-display,
            .filament-sidebar-brand span,
            .filament-header h1 {
                font-family: "Inter", ui-sans-serif, system-ui, sans-serif !important;
                font-weight: 700 !important;
                letter-spacing: -0.025em !important;
            }

            /* =====================================================
               GLASSMORPHISM CARDS (Premium)
               ===================================================== */
            .filament-card,
            .fi-section,
            .filament-widget-card,
            .filament-stats-overview-widget-card,
            .filament-tables-container {
                background: var(--glass-bg) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border: 1px solid var(--glass-border) !important;
                border-radius: 1.25rem !important;
                box-shadow: var(--shadow-lg) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .dark .filament-card,
            .dark .fi-section,
            .dark .filament-widget-card,
            .dark .filament-stats-overview-widget-card,
            .dark .filament-tables-container {
                background: rgba(30, 41, 59, 0.8) !important;
                border-color: rgba(255, 255, 255, 0.08) !important;
            }

            /* Premium Hover Effect */
            .filament-stats-overview-widget-card:hover,
            .filament-widget-card:hover {
                transform: translateY(-4px) !important;
                box-shadow: var(--shadow-xl) !important;
            }

            /* Stats Card Premium */
            .filament-stats-overview-widget-card {
                padding: 1.75rem !important;
                position: relative;
                overflow: hidden;
            }

            .filament-stats-overview-widget-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #0ea5e9, #38bdf8, #7dd3fc);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .filament-stats-overview-widget-card:hover::before {
                opacity: 1;
            }

            /* Stats Values with Gradient */
            .filament-stats-overview-widget-card .filament-stats-card-value {
                font-size: 2rem !important;
                font-weight: 800 !important;
                letter-spacing: -0.03em !important;
                background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Sidebar Brand Override */
            .filament-sidebar {
                background: var(--glass-bg) !important;
                backdrop-filter: blur(20px) !important;
                border-right: 1px solid var(--glass-border) !important;
                box-shadow: var(--shadow-md) !important;
            }

            .dark .filament-sidebar {
                background: rgba(15, 23, 42, 0.95) !important;
            }

            .filament-sidebar-brand {
                font-family: "Inter", sans-serif !important;
                font-weight: 800 !important;
                font-size: 1.5rem !important;
                letter-spacing: -0.03em !important;
                background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                padding: 1.5rem !important;
            }

            /* Sidebar Items Premium */
            .filament-sidebar-item {
                margin: 0.25rem 0.75rem !important;
                border-radius: 0.875rem !important;
                transition: all 0.2s ease !important;
            }

            .filament-sidebar-item:hover {
                background: rgba(14, 165, 233, 0.08) !important;
                transform: translateX(4px) !important;
            }

            /* Sidebar Active Item */
            .filament-sidebar-item-active,
            .filament-sidebar-item[aria-current="page"] {
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(56, 189, 248, 0.1) 100%) !important;
                border-left: 3px solid #0ea5e9 !important;
                font-weight: 600 !important;
            }

            .dark .filament-sidebar-item-active,
            .dark .filament-sidebar-item[aria-current="page"] {
                background: rgba(56, 189, 248, 0.15) !important;
                color: var(--dg-primary) !important;
            }

            /* Primary Buttons */
            .filament-button,
            .fi-btn-primary,
            [type="submit"].filament-button {
                background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%) !important;
                border: none !important;
                border-radius: 0.75rem !important;
                padding: 0.75rem 1.5rem !important;
                font-weight: 600 !important;
                letter-spacing: -0.011em !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .filament-button:hover,
            .fi-btn-primary:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4) !important;
            }

            .filament-button:active {
                transform: translateY(0) !important;
            }

            .dark .filament-button,
            .dark .fi-btn-primary {
                background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%) !important;
                color: #ffffff !important;
            }

            /* Premium Inputs */
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="search"],
            input[type="number"],
            input[type="tel"],
            textarea,
            select {
                background: var(--glass-bg) !important;
                backdrop-filter: blur(10px) !important;
                border: 1.5px solid var(--glass-border) !important;
                border-radius: 0.75rem !important;
                padding: 0.75rem 1rem !important;
                transition: all 0.2s ease !important;
                font-weight: 500 !important;
            }

            input:focus,
            textarea:focus,
            select:focus {
                border-color: #0ea5e9 !important;
                box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1) !important;
                outline: none !important;
                background: #ffffff !important;
            }

            .dark input,
            .dark textarea,
            .dark select {
                background: rgba(30, 41, 59, 0.6) !important;
                border-color: rgba(255, 255, 255, 0.1) !important;
                color: #e2e8f0 !important;
            }

            /* Text Colors */
            .text-primary-600, .text-primary-500 { 
                color: var(--dg-primary) !important; 
            }
            .dark .text-primary-600, .dark .text-primary-500 { 
                color: var(--dg-primary) !important; 
            }
            
            .bg-primary-600, .bg-primary-500 { 
                background-color: var(--dg-primary) !important; 
            }
            .dark .bg-primary-600, .dark .bg-primary-500 { 
                background-color: var(--dg-primary) !important; 
            }

            /* Warning Badge Override */
            .text-warning-700, .text-warning-600 {
                color: #b45309 !important;
            }
            .bg-warning-500, .bg-warning-600 {
                background-color: #f59e0b !important;
            }

            /* Premium Tables */
            .filament-tables-container {
                border-radius: 1.25rem !important;
                overflow: hidden !important;
            }

            .filament-tables-header {
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, rgba(56, 189, 248, 0.03) 100%) !important;
                padding: 1.5rem !important;
                border-bottom: 1px solid var(--glass-border) !important;
            }

            .filament-tables-header h2 {
                font-size: 1.5rem !important;
                font-weight: 700 !important;
                color: var(--dg-black) !important;
                letter-spacing: -0.025em !important;
            }

            .filament-tables-row {
                transition: all 0.2s ease !important;
                border-bottom: 1px solid rgba(226, 232, 240, 0.5) !important;
            }

            .filament-tables-row:hover {
                background: rgba(14, 165, 233, 0.04) !important;
                transform: scale(1.001) !important;
            }

            .dark .filament-tables-row:hover {
                background: rgba(56, 189, 248, 0.08) !important;
            }

            /* Premium Badges */
            .filament-tables-badge,
            .fi-badge {
                border-radius: 0.625rem !important;
                padding: 0.375rem 0.875rem !important;
                font-weight: 600 !important;
                font-size: 0.8125rem !important;
                letter-spacing: 0.01em !important;
                backdrop-filter: blur(8px) !important;
            }

            /* Clean Dashboard - Remove Footer & Documentation Links */
            .filament-footer,
            .filament-main-footer,
            footer[class*="filament"] {
                display: none !important;
            }

            /* Login Page */
            .filament-login-page {
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #dbeafe 100%) !important;
            }
            .dark .filament-login-page {
                background: linear-gradient(135deg, #020617 0%, #0f172a 50%, #1e293b 100%) !important;
            }

            /* Cards & Surfaces */
            .filament-card,
            .fi-section,
            .filament-tables-container {
                animation: fadeInUp 0.5s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Stats Widget Cards */
            .filament-stats-overview-widget-card {
                animation: fadeInUp 0.5s ease-out;
            }

            /* Loading States */
            .filament-loading-indicator {
                color: var(--dg-primary) !important;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 10px;
                height: 10px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #cbd5e1, #94a3b8);
                border-radius: 10px;
                border: 2px solid transparent;
                background-clip: padding-box;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #94a3b8, #64748b);
                background-clip: padding-box;
            }

            /* Table Row Hover */
            .filament-tables-row:hover {
                background-color: #f8fafc !important; /* slate-50 */
            }
            .dark .filament-tables-row:hover {
                background-color: rgba(255, 255, 255, 0.03) !important;
            }

            /* Focus Ring Override */
            *:focus {
                --tw-ring-color: rgba(14, 165, 233, 0.4) !important;
            }
            .dark *:focus {
                --tw-ring-color: rgba(56, 189, 248, 0.4) !important;
            }

            /* =====================================================
               DeepUI FIX: Table Header Alignment (Universal Fix)
               ===================================================== */
            
            /* Target generic header container in Filament Tables & Widgets */
            .filament-tables-header, 
            .fi-ta-header,
            .filament-widget-card > header,
            .filament-tables-container > header,
            [class*="filament-tables-header-container"],
            div:has(> .filament-tables-header-heading) {
                display: flex !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 1rem !important;
                padding: 1rem !important;
            }

            /* Widget Heading Container - Force Horizontal Layout */
            .filament-widget-card > header > div,
            .filament-tables-header > div:first-child {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
                gap: 1rem !important;
            }

            /* Heading + Description Wrapper */
            .filament-widget-card h2,
            .filament-widget header h3,
            .filament-tables-header h2,
            .filament-tables-header-heading,
            .fi-ta-header-heading {
                flex: 1 !important;
                min-width: 200px !important;
                margin: 0 !important;
                padding: 0 !important;
                text-align: left !important;
                color: var(--dg-black) !important;
            }
            
            .dark .filament-widget-card h2,
            .dark .filament-widget header h3,
            .dark .filament-tables-header h2 {
                color: var(--dg-dark) !important;
            }

            /* Description - Keep Below Heading but in Same Container */
            .filament-tables-header p,
            .filament-widget-card header p,
            .fi-ta-header-description {
                display: block !important;
                width: 100% !important;
                margin-top: 0.25rem !important;
                color: #64748b !important;
                font-size: 0.875rem !important;
                line-height: 1.5 !important;
            }

            /* Search Container - Force Right Side */
            .filament-tables-search-container,
            .filament-tables-header-actions,
            .fi-ta-actions {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                margin-left: auto !important;
                flex-shrink: 0 !important;
            }

            /* Search Input Styling */
            input[type="search"],
            .filament-tables-search-input input {
                min-width: 250px !important;
                border-radius: 0.5rem !important;
            }

            /* Widget Specific - Heading & Search Side by Side */
            .filament-widget-card > header {
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
                justify-content: space-between !important;
                gap: 1rem !important;
            }

            /* Left Side: Heading + Description */
            .filament-widget-card > header > div:first-child {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
            }

            /* Right Side: Search/Actions */
            .filament-widget-card > header > div:last-child {
                flex-shrink: 0 !important;
                display: flex !important;
                align-items: center !important;
            }

            /* Alert Widget Styling - Red Border */
            .filament-widget[class*="LatestTransactions"],
            .filament-widget[class*="Overdue"] {
                border-left: 4px solid #ef4444 !important;
                background-color: #fef2f2 !important;
            }
            
            .dark .filament-widget[class*="LatestTransactions"],
            .dark .filament-widget[class*="Overdue"] {
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
