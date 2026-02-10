# 🚀 Panduan Deploy ke Koyeb - SiLaundry

## ❌ Troubleshooting Error 500

Jika aplikasi menampilkan error 500, ikuti langkah berikut:

### 1️⃣ Update Environment Variables di Koyeb

Buka **Dashboard Koyeb** → **Settings** → **Environment Variables**

Pastikan variabel berikut sudah benar:

```env
APP_NAME=SiLaundry
APP_ENV=production
APP_KEY=base64:64lUR4uwJDUUwukZIMlc9kGyvHYOk97oenGFpt5yFsg=
APP_DEBUG=false
APP_URL=https://bumpy-sapphira-belajarlaundryapp-e7f6bf00.koyeb.app
APP_ADDRESS=Jl. Manyung 1 / 23 Pacungan
APP_PHONE=0821 8846 7793

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=test
DB_USERNAME=3q5SUTcM23A25yw.root
DB_PASSWORD=PmV0FVe8qoCcJVey

# PENTING: Kosongkan atau hapus MYSQL_ATTR_SSL_CA
MYSQL_ATTR_SSL_CA=

FILESYSTEM_DISK=cloudinary
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

FONNTE_TOKEN=YhGp9EmFFnCUJF3b4Aap
FONNTE_ENDPOINT=https://api.fonnte.com/send

CLOUDINARY_CLOUD_NAME=dslvtufud
CLOUDINARY_API_KEY=281525218793662
CLOUDINARY_API_SECRET=lOG11BBpP3Qks5niEJ_7OYXt8Xc
CLOUDINARY_URL=cloudinary://281525218793662:lOG11BBpP3Qks5niEJ_7OYXt8Xc@dslvtufud
```

### 2️⃣ Redeploy Aplikasi

Setelah update environment variables:
1. Klik tombol **Redeploy** di Koyeb
2. Tunggu sampai status berubah jadi **Healthy** (hijau)
3. Proses ini memakan waktu 3-5 menit

### 3️⃣ Cek Logs di Koyeb

Jika masih error 500:
1. Buka tab **Logs** di Dashboard Koyeb
2. Cari pesan error yang muncul
3. Biasanya error terkait:
   - Database connection (cek kredensial DB)
   - Missing APP_KEY (pastikan ada dan valid)
   - Permission error (sudah dihandle di Dockerfile)

### 4️⃣ Test Aplikasi

Buka: https://bumpy-sapphira-belajarlaundryapp-e7f6bf00.koyeb.app

**Jika berhasil:**
- ✅ Muncul halaman Login Filament
- ✅ Bisa login dengan akun admin
- ✅ Dashboard muncul dengan benar

**Jika masih error:**
- ❌ Error 500 → Cek logs di Koyeb
- ❌ Error 404 → Cek Apache configuration
- ❌ Blank page → Cek APP_DEBUG=false dan APP_KEY

## 🔧 Perbaikan Umum

### Reset Cache (Jika Perlu)

Jika aplikasi masih error setelah deploy, tambahkan command ini di Koyeb:

```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

### Cek Database Connection

Test koneksi database dari laptop:

```bash
php artisan db:show
```

Jika gagal, berarti kredensial TiDB Cloud salah atau IP tidak diizinkan.

### Regenerate APP_KEY (Jika Hilang)

```bash
php artisan key:generate --show
```

Copy hasilnya dan paste ke environment variable `APP_KEY` di Koyeb.

## 📝 Catatan Penting

1. **APP_ENV** harus `production` (bukan `local`)
2. **APP_DEBUG** harus `false` untuk keamanan
3. **APP_URL** harus sesuai domain Koyeb
4. **MYSQL_ATTR_SSL_CA** harus kosong (tidak ada file cert di container)
5. **FILESYSTEM_DISK** harus `cloudinary` (bukan `local`)

## 🎯 Checklist Deployment

- [ ] Environment variables sudah diupdate
- [ ] APP_URL sudah sesuai domain Koyeb
- [ ] MYSQL_ATTR_SSL_CA sudah dikosongkan
- [ ] Aplikasi sudah di-redeploy
- [ ] Status di Koyeb sudah "Healthy"
- [ ] Halaman login bisa diakses
- [ ] Bisa login ke admin panel
- [ ] Upload foto berfungsi (Cloudinary)
- [ ] WhatsApp notification berfungsi (Fonnte)

## 🆘 Bantuan Lebih Lanjut

Jika masih mengalami masalah:
1. Screenshot error yang muncul
2. Copy logs dari Koyeb
3. Cek file `.env.koyeb` yang sudah saya buatkan sebagai referensi
