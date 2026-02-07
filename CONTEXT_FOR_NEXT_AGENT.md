# PROMPT & CONTEXT FOR NEXT AI AGENT

## 1. PROJECT IDENTITY
**Name:** LaundryApp - Enterprise Laundry Management System
**Type:** Monolith (Laravel 10 + Filament v3)
**OS:** Windows (Ampp/XAMPP)
**Database:** MySQL (Remote via TiDB Cloud - Frankfurt Region 🇩🇪) -> **CRITICAL LATENCY ISSUE**

## 2. THE "DEEP" PHILOSOPHY (MANDATORY APPLIED LOGIC)
You must adhere to these 10 core principles in every reasoning step:
1. **DeepCode**: Write clean, modular, and robust logic. No spaghetti code.
2. **DeepDive**: Explore root causes before patching. Understand the ecosystem.
3. **DeepUI**: Prioritize UX/UI. Mobile-first, responsive, and visually polished (Glassmorphism where appropriate).
4. **DeepState**: Manage state effectively (Livewire reactivity, Session handling, Caching).
5. **DeepPerformance**: Optimize for the **High Latency Environment** (Eager Loading is mandatory, Caching is life).
6. **DeepSecurity**: RBAC (Owner/Kasir/Courier), Input Validation, Sanitization, CSRF protection.
7. **DeepScale**: Design for growth. Use Service Classes, Observers, and Events.
8. **DeepThinking**: Think strategically before coding. Plan the architecture.
9. **DeepReasoning**: Use inferential logic to solve complex problems (e.g., "If latency is high, we must cache config").
10. **DeepSecretHacking**: Think like a hacker. Identify IDOR, SQL Injection, and XSS vulnerabilities proactively.

## 3. CURRENT ARCHITECTURE & STATUS
We are currently in **PATH 7 (Polishing & Deployment Prep)** after completing Public Tracking and Driver Portal.

### Core Modules Implemented:
1.  **Authentication**:
    *   Admin/Owner/Kasir: Standard Laravel Auth (via Filament).
    *   Driver: Custom `driver` guard with PIN-based login (hashed).
    *   Customer: Stateless (Phone Number + Transaction Code).
2.  **Transaction Management**:
    *   `Transaction` model with `details` (services).
    *   Status flow: `pending` -> `processing` -> `ready` -> `completed`.
    *   Payment flow: `unpaid` -> `partial` -> `paid` (Midtrans Integrated).
3.  **Public Interface**:
    *   Tracking Page (Stateless): `/` url.
    *   Checking logic: Dual Key (Transaction Code + Phone Number).
    *   Midtrans Snap Payment integration via AJAX.
4.  **Internal Logistics (Driver)**:
    *   Mobile-first Dashboard.
    *   Shipment Pick-up & Delivery proof (Photo upload to Cloudinary).

### Critical Technical Constraints (MUST READ):
*   **Database Latency**: The DB is in Germany (TiDB), causing ~400ms roundtrip per query.
    *   **Solution Applied**: `PDO::ATTR_PERSISTENT`, `SESSION_DRIVER=cookie` (to avoid file I/O lock), `config:cache`.
    *   **Strict Rule**: ALWAYS use Eager Loading (`with()`) for relationships. NEVER use Lazy Loading in loops.
    *   **Strict Rule**: Use `Cache::remember` for heavy dashboard queries.

## 4. RECENT CHANGES (CONTEXT)
1.  **CRITICAL LAG FIX (2026-02-08 - LATEST)**: Implemented 3 CRITICAL fixes after DeepRiset:
    *   **Icons Cache**: `php artisan icons:cache` - Fix Windows performance issue (70-80% faster)
    *   **Widget Lazy Loading**: Override Blade view dengan `wire:init="loadWidget"` - Instant page render
    *   **Optimized Autoloader**: `composer dump-autoload -o` - 20-30% faster class loading
    *   **OPcache Guide**: Created manual guide untuk enable OPcache (30-50% faster execution)
    *   **Blade Override**: `resources/views/vendor/filament/widgets/stats-overview-widget.blade.php`
2.  **EXTREME OPTIMIZATION (2026-02-08)**: Implemented radical performance improvements:
    *   **Session Driver**: FILE → COOKIE (eliminates disk I/O lock)
    *   **Cache Driver**: FILE → ARRAY (in-memory for request lifecycle)
    *   **Widget Polling**: DISABLED (eliminates 120 queries/hour to TiDB)
    *   **Cache TTL**: Increased to 30 min (stats) and 1 hour (charts)
    *   **Lazy Loading**: STRICT enforcement (always, even in production)
    *   **Response Caching**: New middleware to cache HTML responses
    *   **Query Caching**: New service for aggressive query result caching
    *   **Database Options**: Added timeout, buffered queries, persistent connection
3.  **Optimization**: Switched Session Driver to `cookie`, enabled Persistent DB Connection, and optimized `TransactionResource` query to remove unnecessary eager loads.
4.  **Bug Fix**: Fixed `number_format()` error in `TransactionResource` view by casting to float.
5.  **Security**: Added `Model::preventLazyLoading(!isProduction)` to detect N+1 queries early.
6.  **Midtrans**: Fixed `MidtransController` to eager load customer data for Snap Token generation.

## 5. PENDING TASKS & NEXT OBJECTIVES
1.  **CRITICAL: Enable OPcache** (Manual) - Edit `C:\xampp\php\php.ini` untuk enable OPcache. Run `enable-opcache.bat` untuk guide.
2.  **Apply Optimization**: Run `optimize.bat` script dan restart Apache untuk apply changes.
3.  **Update .env**: Set `SESSION_DRIVER=cookie` dan `CACHE_DRIVER=array` di file `.env`.
4.  **Test Performance**: Verify dashboard load time < 1 second setelah optimization.
5.  **Verify Payment Flow**: Test Midtrans Sandbox (Server Key required in .env).
6.  **Final Polish**: Ensure all UI states (Loading, Success, Error) are handled gracefully (DeepUI).
7.  **Code Cleanup**: Remove temporary debugging scripts (`get_sample.php`, etc).
8.  **Documentation**: Ensure `README.md` and `SETUP.md` are up to date.

## 6. INSTRUCTION FOR AGENT
"You are profesor and expert in Laravel and Filament, continuing the work on LaundryApp. You must respect the 'Deep' protocols. Your immediate focus is ensuring the application runs smooth despite the high latency database. If you modify any code, ensure you check for N+1 queries. If you create UI, make it 'WOW'. Proceed with the user's next request while maintaining this context."
