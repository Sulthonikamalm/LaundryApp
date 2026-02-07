# 🧺 LaundryApp - Enterprise Laundry Management System

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-2.x-orange.svg)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Modern, full-featured laundry management system built with Laravel 10 and Filament v2. Optimized for high-latency database environments with aggressive caching and performance tuning.

---

## ✨ Features

### 🎯 Core Features
- **Transaction Management** - Complete order lifecycle (pending → processing → ready → completed)
- **Customer Management** - Individual & corporate customer profiles
- **Service Management** - Flexible service catalog with pricing
- **Payment Integration** - Midtrans payment gateway integration
- **Driver Portal** - Mobile-first interface for pickup/delivery
- **Public Tracking** - Stateless customer order tracking
- **Financial Reports** - Revenue analysis and export to Excel

### 🚀 Performance Features
- **Aggressive Caching** - 30-minute stats cache, 1-hour chart cache
- **Lazy Widget Loading** - Async data loading for instant page render
- **Icons Cache** - Pre-cached Blade icons for Windows performance
- **Optimized Queries** - Strict eager loading, N+1 prevention
- **Session Optimization** - Cookie-based sessions (no file I/O)
- **Database Tuning** - Persistent connections, buffered queries

### 🔒 Security Features
- **Multi-Guard Auth** - Owner/Kasir/Driver with role-based access
- **CSRF Protection** - Laravel's built-in CSRF tokens
- **Input Validation** - Strict validation on all forms
- **SQL Injection Prevention** - Eloquent ORM with prepared statements
- **XSS Prevention** - Blade template escaping

---

## 📋 Requirements

- **PHP** >= 8.2
- **Composer** >= 2.0
- **MySQL** >= 8.0 (or TiDB Cloud)
- **Node.js** >= 18.x (for asset compilation)
- **Extensions**: PDO, OpenSSL, Mbstring, GD, Tokenizer, XML, Ctype, JSON

---

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/Sulthonikamalm/LaundryApp.git
cd LaundryApp
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=laundryapp
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Performance Settings (CRITICAL!)
SESSION_DRIVER=cookie
CACHE_DRIVER=array
```

### 5. Run Migrations
```bash
php artisan migrate --seed
```

### 6. Performance Optimization (CRITICAL!)
```bash
# Cache Filament icons (Windows performance fix)
php artisan icons:cache

# Optimize autoloader
composer dump-autoload -o

# Cache config
php artisan config:cache
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000/admin`

**Default Credentials:**
- **Owner**: `owner@laundry.com` / `password`
- **Kasir**: `kasir@laundry.com` / `password`

---

## ⚡ Performance Optimization

### For Windows Users (CRITICAL!)

LaundryApp includes specific optimizations for Windows + XAMPP:

1. **Run optimization script:**
   ```bash
   optimize.bat
   ```

2. **Enable OPcache** (30-50% faster):
   ```bash
   enable-opcache.bat
   ```
   Follow the guide to edit `php.ini`

3. **Verify optimization:**
   ```bash
   check-performance.bat
   ```

**Expected Performance:**
- Dashboard load: **< 1 second**
- Page navigation: **< 500ms**
- Widget render: **< 500ms**

📖 **Read:** `CRITICAL_FIX_LAG_NOW.md` for detailed guide

---

## 📚 Documentation

- **[Setup Guide](SETUP.md)** - Detailed installation instructions
- **[Performance Guide](PERFORMANCE_OPTIMIZATION_GUIDE.md)** - Advanced optimization
- **[Quick Start](QUICK_START_OPTIMIZATION.md)** - 5-minute optimization guide
- **[Critical Fixes](CRITICAL_FIX_LAG_NOW.md)** - Windows performance fixes
- **[Context](CONTEXT_FOR_NEXT_AGENT.md)** - Project architecture & philosophy

---

## 🏗️ Architecture

### Tech Stack
- **Backend**: Laravel 10.x
- **Admin Panel**: Filament v2.x
- **Frontend**: Livewire + Alpine.js
- **Database**: MySQL 8.0 / TiDB Cloud
- **Payment**: Midtrans Snap
- **File Storage**: Cloudinary
- **Excel Export**: Maatwebsite/Excel

### Design Philosophy (The "Deep" Principles)
1. **DeepCode** - Clean, modular, robust logic
2. **DeepDive** - Explore root causes before patching
3. **DeepUI** - Mobile-first, responsive, polished
4. **DeepState** - Effective state management
5. **DeepPerformance** - Optimize for high-latency environments
6. **DeepSecurity** - RBAC, validation, sanitization
7. **DeepScale** - Design for growth
8. **DeepThinking** - Strategic architecture planning
9. **DeepReasoning** - Inferential problem-solving
10. **DeepSecretHacking** - Proactive vulnerability identification

---

## 🔧 Configuration

### High-Latency Database Optimization

If your database is remote (e.g., TiDB Cloud in Frankfurt):

```env
# .env
SESSION_DRIVER=cookie
CACHE_DRIVER=array
```

```php
// config/database.php
'options' => [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_TIMEOUT => 5,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
]
```

---

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter=TransactionTest
```

---

## 📦 Deployment

### Production Checklist

1. **Environment:**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan icons:cache
   composer install --optimize-autoloader --no-dev
   ```

3. **Security:**
   - Set strong `APP_KEY`
   - Use HTTPS
   - Configure CORS
   - Set secure session cookies

4. **Performance:**
   - Enable OPcache
   - Use Redis for cache (optional)
   - Configure queue workers
   - Set up CDN for assets

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Filament](https://filamentphp.com) - Admin Panel Builder
- [Livewire](https://laravel-livewire.com) - Dynamic Interfaces
- [Midtrans](https://midtrans.com) - Payment Gateway
- [Cloudinary](https://cloudinary.com) - Media Management

---

## 📞 Support

For issues, questions, or suggestions:
- **GitHub Issues**: [Create an issue](https://github.com/Sulthonikamalm/LaundryApp/issues)
- **Email**: support@laundryapp.com

---

**Built with ❤️ using Laravel & Filament**
