<div align="center">

# Laundry Management System
### Sistem Manajemen Laundry Modern dengan Tracking Real-time

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-2.17-FDAE4B?style=flat-square&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTI0IDQ4QzM3LjI1NDggNDggNDggMzcuMjU0OCA0OCAyNEM0OCAxMC43NDUyIDM3LjI1NDggMCAyNCAwQzEwLjc0NTIgMCAwIDEwLjc0NTIgMCAyNEMwIDM3LjI1NDggMTAuNzQ1MiA0OCAyNCA0OFoiIGZpbGw9IiNGREFFNEIiLz4KPC9zdmc+Cg==&logoColor=white)](https://filamentphp.com)
[![TiDB Cloud](https://img.shields.io/badge/TiDB_Cloud-MySQL_Compatible-FF3B30?style=flat-square&logo=mysql&logoColor=white)](https://tidbcloud.com)
[![Cloudinary](https://img.shields.io/badge/Cloudinary-Image_Storage-3448C5?style=flat-square&logo=cloudinary&logoColor=white)](https://cloudinary.com)
[![Fonnte](https://img.shields.io/badge/Fonnte-WhatsApp_API-25D366?style=flat-square&logo=whatsapp&logoColor=white)](https://fonnte.com)
[![Midtrans](https://img.shields.io/badge/Midtrans-Payment_Gateway-00A8E1?style=flat-square&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjMDBBOEUxIi8+Cjwvc3ZnPgo=&logoColor=white)](https://midtrans.com)
[![Claude AI](https://img.shields.io/badge/AI_Assisted-Claude_Sonnet_4.5-D97757?style=flat-square&logo=anthropic&logoColor=white)](https://anthropic.com)
[![Google AI](https://img.shields.io/badge/Powered_by-Antigravity_AI-4285F4?style=flat-square&logo=google&logoColor=white)](https://ai.google.dev)

*Transformasi operasional laundry UMKM dengan teknologi modern dan AI-powered development*

[📖 Dokumentasi](#fitur-utama) · [🚀 Quick Start](#instalasi) · [📊 Tech Stack](#teknologi-yang-digunakan) · [🎯 Roadmap](#roadmap-pengembangan)

</div>

---

## Tentang Proyek

Sistem manajemen laundry full-stack yang dirancang untuk mengoptimalkan operasional bisnis laundry skala UMKM hingga menengah. Proyek ini berawal dari Tugas Besar Rekayasa Perangkat Lunak dan dikembangkan lebih lanjut menjadi aplikasi production-ready dengan fitur-fitur modern.

### Mengapa Proyek Ini Berbeda?

- **AI-Assisted Development** - Dikembangkan dengan bantuan Claude Sonnet 4.5 dan Google Antigravity AI untuk optimasi arsitektur dan code quality
- **Production-Ready** - Bukan sekadar prototype, tapi sistem yang siap digunakan dengan proper error handling dan security
- **Modern Stack** - Menggunakan teknologi terkini: Laravel 10, Filament Admin, TiDB Cloud, Cloudinary
- **Real Business Value** - Menyelesaikan masalah nyata: tracking cucian, manajemen pembayaran, koordinasi kurir

---

## Fitur Utama

### Admin Panel (Filament)
- Dashboard dengan statistik real-time dan revenue chart
- Manajemen Customer dengan CRM sederhana
- Manajemen Transaksi dengan workflow status lengkap
- Sistem Pembayaran dengan approval workflow
- Export laporan keuangan ke Excel
- Manajemen Service dan Pricing

### Public Tracking System
- Tracking tanpa login menggunakan kode transaksi
- Direct access via URL token untuk kemudahan customer
- Visual timeline dengan foto bukti setiap tahap
- Pembayaran online terintegrasi (Midtrans/Demo Gateway)

### Driver/Courier Portal
- Login dengan PIN untuk keamanan dan kemudahan
- Dashboard job list dengan prioritas
- Upload foto bukti pengiriman via Cloudinary
- Real-time status update

### WhatsApp Integration
- Auto-notification saat order baru
- Notifikasi pembayaran diterima
- Notifikasi cucian siap diambil
- Link tracking langsung di pesan WhatsApp

---

## Teknologi yang Digunakan

### Backend & Framework
- **Laravel 10** - PHP framework modern dengan ecosystem lengkap
- **Filament 2.17** - Admin panel builder yang powerful dan customizable
- **PHP 8.1+** - Dengan strict types dan modern syntax

### Database
- **TiDB Cloud (Frankfurt)** - MySQL-compatible distributed database

**Mengapa TiDB Cloud?**
1. **Scalability** - Horizontal scaling otomatis tanpa downtime
2. **MySQL Compatible** - Tidak perlu belajar syntax baru, migrasi mudah
3. **Free Tier Generous** - 5GB storage gratis, cocok untuk development dan portfolio
4. **Global Distribution** - Data center di Frankfurt memberikan latency rendah untuk Eropa
5. **HTAP Architecture** - Bisa handle OLTP (transactional) dan OLAP (analytical) dalam satu database

**Trade-off yang Dihadapi:**
- Latency ~200-300ms dari Indonesia (acceptable untuk non-critical app)
- Solusi: Aggressive caching dengan array driver dan cookie session

### External Services
- **Cloudinary** - Cloud-based image storage dan CDN untuk foto bukti delivery
- **Fonnte** - WhatsApp Business API gateway untuk notifikasi otomatis ke customer
- **Midtrans** - Payment gateway Indonesia (dengan demo mode untuk portfolio)

### Development Tools
- **Claude Sonnet 4.5** - AI assistant untuk code review dan architecture decisions
- **Google Antigravity AI** - AI-powered development assistance
- **Draw.io** - Database ERD design
- **Git** - Version control

---

## Deployment Strategy

### Mengapa Belum Deploy ke Koyeb?

Proyek ini **sengaja tidak di-deploy** ke production environment seperti Koyeb dengan pertimbangan strategis:

**1. Portfolio & Learning Focus**
- Proyek ini adalah learning project yang masih dalam active development
- Lebih fokus pada kualitas kode dan dokumentasi daripada uptime
- Deployment prematur bisa mengalihkan fokus dari improvement

**2. Cost-Benefit Analysis**
- Koyeb free tier memiliki limitasi (sleep after inactivity, limited resources)
- TiDB Cloud sudah menggunakan free tier, menambah Koyeb free tier = double limitation
- Untuk portfolio, local demo atau video walkthrough lebih efektif

**3. Technical Considerations**
- WhatsApp notification membutuhkan public URL yang stabil
- Payment gateway webhook membutuhkan SSL dan static IP
- File upload ke Cloudinary sudah handle storage, tidak butuh persistent disk di Koyeb

**4. Development Flexibility**
- Masih banyak fitur yang akan ditambahkan (lihat roadmap)
- Frequent changes lebih mudah di-test local
- Deployment akan dilakukan setelah feature freeze

**Rencana Deployment:**
- Setelah semua fitur roadmap selesai
- Menggunakan VPS (DigitalOcean/Vultr) untuk kontrol penuh
- Atau Koyeb/Railway jika butuh quick demo untuk interview

---

## Instalasi

### Prerequisites
```bash
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL 8.0 atau TiDB Cloud account
- Node.js & NPM (untuk asset compilation)
```

### Setup Local Development

1. **Clone Repository**
```bash
git clone https://github.com/yourusername/laundryapp.git
cd laundryapp
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**
```bash
# Edit .env dengan kredensial TiDB Cloud atau MySQL local
php artisan migrate --seed
```

5. **Run Development Server**
```bash
php artisan serve
npm run dev
```

6. **Access Application**
- Admin Panel: `http://localhost:8000/admin`
- Public Tracking: `http://localhost:8000`
- Driver Portal: `http://localhost:8000/driver`

### Default Credentials
```
Owner Account:
Email: owner@laundry.com
Password: password

Kasir Account:
Email: kasir@laundry.com
Password: password

Courier Account:
PIN: 1234
```

---

## Struktur Project

```
laundryapp/
├── app/
│   ├── Filament/          # Admin panel resources & widgets
│   ├── Http/              # Controllers & middleware
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic layer
│   │   ├── Payment/       # Payment gateway abstraction
│   │   └── WhatsApp/      # WhatsApp message builders
│   ├── Jobs/              # Background jobs
│   └── Policies/          # Authorization policies
├── database/
│   ├── migrations/        # Database schema
│   └── seeders/           # Sample data
├── resources/
│   ├── views/             # Blade templates
│   └── lang/              # Translations (ID)
├── ARCHIVE-BELAJAR/       # Learning documentation
└── public/                # Public assets
```

---

## Pembelajaran & Dokumentasi

Folder `ARCHIVE-BELAJAR` berisi dokumentasi lengkap perjalanan pembelajaran dari tugas kuliah hingga implementasi nyata. Termasuk:

- Analisis ERD awal vs final
- Keputusan arsitektur dan trade-off
- Perbandingan desain database
- SQL schema evolution

[📂 Lihat Dokumentasi Lengkap](ARCHIVE-BELAJAR/README.md)

---

## Credits & Acknowledgments

### Development Team
Proyek ini dimulai sebagai Tugas Besar Rekayasa Perangkat Lunak oleh:

- **Sulthonika Mahfudz Al Mujahidin** (1202230023) - Ketua Tim & Full-stack Developer
- Muhammad Fajar Shodiq (1202230045)
- Teuku Ismail Syuhada (1202230036)
- Davinsyah Putra Antoro (1202230054)
- Muhammad Dwiky Yanuarezza

### Special Thanks
- **Ibu Mastuty Ayu Ningtyas, S.Kom., M.MT.** - Dosen Pengampu Rekayasa Perangkat Lunak
- **Claude AI (Anthropic)** - Architecture review & code optimization
- **Google Antigravity AI** - Development assistance & problem solving

### Open Source Libraries
- Laravel Framework & Ecosystem
- Filament Admin Panel
- Cloudinary PHP SDK
- SimpleSoftwareIO QR Code
- Maatwebsite Excel

---

## License

Proyek ini adalah learning project dan portfolio piece. Silakan gunakan sebagai referensi, tapi mohon cantumkan credit jika digunakan untuk tujuan komersial.

---

<div align="center">

**Laundry Management System**

*Built with modern tools, powered by AI, designed for real business*

Dikembangkan dengan ❤️ menggunakan Laravel, Filament, dan AI

[⭐ Star this repo](https://github.com/yourusername/laundryapp) · [🐛 Report Bug](https://github.com/yourusername/laundryapp/issues) · [💡 Request Feature](https://github.com/yourusername/laundryapp/issues)

</div>
