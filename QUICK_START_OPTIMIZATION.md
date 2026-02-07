# 🚀 QUICK START: Performance Optimization & Troubleshooting

Jika Anda masih mengalami performa lambat atau masalah data, ikuti langkah-langkah kritis berikut:

## 1. 🛠️ Critical Setup (Wajib)

### Database Latency Fix (TiDB Singapore)
Kami telah memindahkan database ke **Singapore** (`ap-southeast-1`), namun latency tetap bisa mencapai ~600ms. Solusi yang telah diterapkan:
*   **Persistent Connection**: Diaktifkan di `config/database.php`.
*   **Eager Loading**: Wajib menggunakan `with()` untuk semua relasi.
*   **Prevent Lazy Loading**: Sistem akan melempar error jika ada N+1 query (Cek `AppServiceProvider`).

### PHP Environment (XAMPP)
Sangat disarankan untuk mengaktifkan **OPcache** di `php.ini` Anda:
1. Buka `C:\xampp\php\php.ini`.
2. Cari `[opcache]`.
3. Set `zend_extension=opcache`.
4. Set `opcache.enable=1`.
5. Restart Apache.

## 2. ⚡ Optimization Ritual
Jalankan perintah ini setiap kali ada perubahan konfigurasi atau jika sistem terasa lambat:

```powershell
# Jalankan script otomatis
.\optimize.bat
```

Atau jalankan manual:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
composer dump-autoload -o
```

## 3. 🔍 Troubleshooting "No Records Found"

Jika data tidak tampil meskipun sudah di-seed:
1. **Clear App Cache**: `php artisan cache:clear`.
2. **Check Session**: Pastikan Anda login sebagai **Owner** (`owner@laundry.com`). Kurir tidak bisa melihat data di panel admin.
3. **Database Check**: Jalankan `php Artisan tinker` dan ketik `App\Models\Transaction::count()`. Jika 0, jalankan re-seeder:
    ```bash
    php artisan db:seed --class=DemoSeeder
    ```

## 4. 🔑 Admin Credentials
| Role | Email | Password |
| --- | --- | --- |
| **Owner** | `owner@laundry.com` | `password123` |
| **Kasir** | `kasir@laundry.com` | `password123` |
| **Kurir** | `courier@laundry.com` | (Gunakan PIN `123456`) |

---
**Status Terakhir:**
*   `AdminResource` telah dibuat.
*   `TransactionStatusLog` bug fixed (nullable previous_status).
*   Latency test: ~660ms (Membutuhkan caching agresif).
