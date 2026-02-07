<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4c7d73">
    <title>{{ $title ?? 'SiLaundry' }}</title>
    
    <!-- DeepPerformance: Preconnect to critical origins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    
    <!-- DeepPerformance: Load fonts with swap for instant text display -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DeepPerformance: Tailwind CDN (Fallback for dynamic classes) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            black: '#2e2d2c',
                            dark: '#3a4643',
                            deep: '#44625c',
                            primary: '#4c7d73',
                            medium: '#569a8c',
                            light: '#5cb9a6',
                            accent: '#60d9c3',
                            surface: '#b1ece0',
                            subtle: '#d7f5ef',
                            white: '#ecfaf6',
                            bg: '#f5fcfa',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- DeepPerformance: Inline critical CSS instead of CDN Tailwind -->
    <style>
        /* ==============================================
           SILAUNDRY DESIGN SYSTEM - MINIMAL LAYOUT
           ==============================================
           DeepUI: Optimized for performance & aesthetics.
           DeepPerformance: Inline CSS = no external request.
        */
        
        /* CSS Reset & Base */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { 
            scroll-behavior: smooth; 
            -webkit-text-size-adjust: 100%;
            height: 100%;
        }
        body { 
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            color: #2e2d2c;
            background: linear-gradient(135deg, #ecfaf6 0%, #d7f5ef 50%, #ecfaf6 100%);
            min-height: 100%;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.6;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6, .font-display { 
            font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
            line-height: 1.2;
        }
        
        /* DeepGreen Color Palette */
        :root {
            --brand-black: #2e2d2c;
            --brand-dark: #3a4643;
            --brand-deep: #44625c;
            --brand-primary: #4c7d73;
            --brand-medium: #569a8c;
            --brand-light: #5cb9a6;
            --brand-accent: #60d9c3;
            --brand-surface: #b1ece0;
            --brand-subtle: #d7f5ef;
            --brand-white: #ecfaf6;
            --brand-bg: #f5fcfa;
        }

        /* Utility Classes - Minimal Set */
        .min-h-screen { min-height: 100vh; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-grow { flex-grow: 1; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .text-center { text-align: center; }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .overflow-hidden { overflow: hidden; }
        .z-10 { z-index: 10; }
        .pointer-events-none { pointer-events: none; }
        
        /* Spacing */
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        .p-10 { padding: 2.5rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
        .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-12 { margin-bottom: 3rem; }
        .mt-8 { margin-top: 2rem; }
        .mt-12 { margin-top: 3rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .gap-4 { gap: 1rem; }
        
        /* Sizing */
        .w-full { width: 100%; }
        .max-w-sm { max-width: 24rem; }
        .max-w-md { max-width: 28rem; }
        .max-w-lg { max-width: 32rem; }
        .max-w-\[480px\] { max-width: 480px; }
        .h-full { height: 100%; }
        
        /* Typography Sizes */
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-base { font-size: 1rem; line-height: 1.5rem; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .tracking-tight { letter-spacing: -0.025em; }
        .tracking-wider { letter-spacing: 0.05em; }
        .tracking-widest { letter-spacing: 0.1em; }
        .uppercase { text-transform: uppercase; }
        .leading-relaxed { line-height: 1.625; }
        .leading-snug { line-height: 1.375; }
        
        /* Colors */
        .text-brand-black { color: var(--brand-black); }
        .text-brand-dark { color: var(--brand-dark); }
        .text-brand-primary { color: var(--brand-primary); }
        .text-white { color: #fff; }
        .bg-brand-white { background-color: var(--brand-white); }
        .bg-brand-bg { background-color: var(--brand-bg); }
        .bg-brand-subtle { background-color: var(--brand-subtle); }
        .bg-brand-surface { background-color: var(--brand-surface); }
        .bg-brand-primary { background-color: var(--brand-primary); }
        .bg-brand-deep { background-color: var(--brand-deep); }
        .bg-white { background-color: #fff; }
        
        /* Opacity */
        .opacity-60 { opacity: 0.6; }
        .opacity-30 { opacity: 0.3; }
        
        /* Border & Radius */
        .border { border-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-transparent { border-color: transparent; }
        .border-brand-surface { border-color: var(--brand-surface); }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-2xl { border-radius: 1rem; }
        .rounded-3xl { border-radius: 1.5rem; }
        .rounded-\[28px\] { border-radius: 28px; }
        .rounded-\[40px\] { border-radius: 40px; }
        .rounded-full { border-radius: 9999px; }
        .ring-1 { box-shadow: 0 0 0 1px var(--tw-ring-color, rgba(76,125,115,0.1)); }
        
        /* Shadow */
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
        .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
        
        /* Effects */
        .blur-\[80px\] { filter: blur(80px); }
        .blur-\[100px\] { filter: blur(100px); }
        .backdrop-blur-sm { backdrop-filter: blur(4px); }
        
        /* Transitions */
        .transition-all { transition: all 0.3s ease; }
        .transition-colors { transition: color 0.2s, background-color 0.2s, border-color 0.2s; }
        .duration-300 { transition-duration: 300ms; }
        .transform { transform: translateZ(0); }
        
        /* Hover States */
        .hover\:bg-brand-deep:hover { background-color: var(--brand-deep); }
        .hover\:text-brand-deep:hover { color: var(--brand-deep); }
        .hover\:translate-y-\[-2px\]:hover { transform: translateY(-2px); }
        .hover\:shadow-xl:hover { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .active\:translate-y-\[1px\]:active { transform: translateY(1px); }
        
        /* Focus States */
        .focus\:ring-4:focus { box-shadow: 0 0 0 4px rgba(76, 125, 115, 0.15); }
        .focus\:border-brand-primary:focus { border-color: var(--brand-primary) !important; }
        .focus\:bg-white:focus { background-color: #fff; }
        .focus\:outline-none:focus { outline: none; }
        
        /* Group Hover */
        .group:focus-within .group-focus-within\:text-brand-primary { color: var(--brand-primary); }
        .group:focus-within .group-focus-within\:-translate-y-1 { transform: translateY(-0.25rem); }
        
        /* Form Elements */
        input, textarea, select {
            font-family: inherit;
            font-size: 1rem;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
        }
        input::placeholder {
            color: rgba(58, 70, 67, 0.4);
        }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--brand-subtle); }
        ::-webkit-scrollbar-thumb { background: var(--brand-medium); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-primary); }
        
        /* Animations */
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
        .group:hover .animate-shimmer { animation: shimmer 1.5s infinite; }
        
        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, var(--brand-subtle) 25%, var(--brand-white) 50%, var(--brand-subtle) 75%);
            background-size: 200% 100%;
            animation: skeleton-pulse 1.5s ease-in-out infinite;
        }
        @keyframes skeleton-pulse {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Dark Mode Support (for consistency with admin) */
        @media (prefers-color-scheme: dark) {
            .dark-mode-auto body {
                background: linear-gradient(135deg, #1a1a1a 0%, #2e2d2c 100%);
                color: #ecfaf6;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col">

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="py-6 text-center text-brand-dark opacity-60 text-sm">
        <p>&copy; {{ date('Y') }} SiLaundry. All rights reserved.</p>
    </footer>

    @stack('scripts')
    
    <!-- DeepPerformance: Defer non-critical JS -->
    <script>
        // Instant page navigation feel
        document.addEventListener('DOMContentLoaded', function() {
            // Pre-warm form focus
            const firstInput = document.querySelector('input:not([type="hidden"])');
            if (firstInput && window.innerWidth > 768) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    </script>
</body>
</html>
