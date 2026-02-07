Tentu, Prof. Mari kita susun dokumen **Rancangan Teknis & Arsitektur Sistem (Master Plan)** ini dengan tingkat detail yang sangat mendalam. Dokumen ini dirancang untuk menjadi "pemandu tunggal" (Single Source of Truth) agar AI Agent atau pengembang mana pun tidak tersesat dalam proses koding.

Total penjelasan ini dirancang sangat komprehensif, mencakup logika bisnis, arsitektur data, hingga strategi infrastruktur.

---

# 🏛️ MASTER PLAN: SISTEM MANAJEMEN LAUNDRY MODERN (SI-LAUNDRY)

**Versi:** 2.0 (Final Production-Ready)
**Penyusun:** Prof (AI AI Collaborator)
**Tech Stack:** Laravel 11, Filament v3, TiDB Cloud, Midtrans, Cloudinary.

---

## 1. Filosofi Sistem & Tujuan Strategis

Sistem ini dibangun untuk mengatasi inefisiensi pada laundry UMKM dengan pendekatan **"Stateless & Cloud-Native"**. Filosofi utamanya adalah **"Low Friction, High Accountability"**. Artinya, pelanggan mendapatkan kemudahan akses informasi tanpa hambatan login, sementara pemilik laundry mendapatkan akuntabilitas data finansial dan logistik yang ketat.

Sistem ini menolak pendekatan *over-engineering* seperti *Event Sourcing* atau *Temporal Tables* demi efisiensi biaya penyimpanan pada **TiDB Cloud Free Tier (5GB)**, namun tetap mempertahankan standar integritas data industri .

---

## 2. Arsitektur Infrastruktur & Stateless Media

Aplikasi ini dirancang untuk berjalan secara *stateless* di **Koyeb**. Hal ini krusial karena server di *cloud* sering melakukan *restart* otomatis.

* 
**Penyimpanan Media (Cloudinary):** Semua foto bukti cucian dan bukti pengantaran disimpan di Cloudinary. Laravel dikonfigurasi menggunakan `FILESYSTEM_DISK=cloudinary` agar fungsi `Storage::put()` otomatis mengunggah ke awan.


* 
**Database (TiDB Cloud):** Menggunakan MySQL 8.0 *compatible* dengan *port* khusus 4000. Strategi indeks dirancang untuk meminimalkan beban tulis (*write load*) namun mempercepat pencarian pelacakan pelanggan .



---

## 3. Arsitektur Data & Skema Database (Deep Dive)

Berdasarkan analisis praktis v2, struktur database dibagi menjadi **8 tabel utama** yang telah ternormalisasi .

### 3.1. Normalisasi & Integritas (1NF & Price Snapshot)

* 
**First Normal Form (1NF):** Sistem menggunakan tabel `transaction_details` sebagai *junction table* antara transaksi dan layanan . Ini menggantikan kolom *string* yang berisi daftar layanan (seperti "Jas, Kemeja"), sehingga memungkinkan query analitik seperti "Berapa banyak Jas yang dicuci bulan ini?" tanpa melakukan *string parsing* yang lambat .


* 
**Financial Audit Compliance (Price Snapshot):** Masalah krusial di mana harga layanan bisa berubah di masa depan diatasi dengan kolom `price_at_transaction` di tabel detail . Saat transaksi dibuat, sistem melakukan *copy* harga dari master layanan ke tabel detail, sehingga laporan keuangan masa lalu tetap akurat meskipun harga naik di masa depan .



### 3.2. Fleksibilitas Pembayaran (1:Many)

Sistem mendukung pembayaran bertahap (misalnya DP di awal dan pelunasan di akhir) melalui tabel `payments` terpisah . Hal ini memungkinkan pelacakan metode pembayaran yang berbeda (Cash dan QRIS) dalam satu pesanan yang sama .

### 3.3. Konsolidasi Logistik (Shipments)

Tabel `shipments` menggabungkan data *Pickup* (jemput) dan *Delivery* (antar) . Hal ini menyederhanakan tugas kurir internal karena semua antrean tugas berada di satu tempat, bukan tersebar di tabel-tabel redundan .

---

## 4. Analisis Alur Bisnis Dunia Nyata

### 4.1. Modul Admin/Kasir (Filament Backend)

Kasir berinteraksi dengan **FilamentPHP v3** yang menyediakan antarmuka *logic-driven*.

* 
**Pencatatan Order:** Kasir memilih pelanggan (atau membuat data pelanggan baru jika tidak ada) dan menambahkan item layanan menggunakan komponen **Repeater**.


* **Logika Pengiriman:** Jika pelanggan meminta antar-jemput, kasir mengaktifkan opsi *Delivery*, memasukkan alamat, dan menentukan biaya antar manual.
* 
**Manajemen Status:** Admin memantau *dashboard* untuk mengubah status dari `pending` → `processing` → `ready`.



### 4.2. Modul Pelanggan (Public Tracking & Midtrans)

Pelanggan mengakses halaman utama (`/`) tanpa perlu *login*.

* **Tracking:** Pelanggan memasukkan nomor HP atau kode transaksi untuk melihat *timeline* progres cucian secara *real-time*.
* 
**Online Payment:** Integrasi dengan **Midtrans Snap** memungkinkan pelanggan membayar dari rumah jika statusnya belum lunas. Begitu pembayaran sukses, Midtrans akan mengirimkan *webhook* ke Laravel untuk mengubah `payment_status` menjadi `paid`.



### 4.3. Portal Driver (Web-Based Interface)

Kurir internal (staf toko) menggunakan portal khusus berbasis web yang diakses via HP.

* **Verifikasi PIN:** Mengingat kurir adalah staf internal, mereka tidak butuh akun login individu yang rumit. Mereka cukup memasukkan **PIN Toko** (misalnya 6 digit) untuk memvalidasi identitas saat akan mengunggah bukti.


* **Proof of Delivery:** Setelah baju diserahkan ke pelanggan, kurir memotret paket/penerima dan mengunggahnya. Foto ini otomatis terunggah ke Cloudinary dan URL-nya disimpan di kolom `photo_proof_url` pada tabel `shipments`.



---

## 5. Audit & Keamanan Data (Simplified Audit Trail)

Karena kita menghindari *Event Sourcing* yang berat, audit trail dilakukan melalui dua cara praktis:

1. 
**Soft Deletes:** Data yang dihapus tidak hilang dari database (hanya ditandai `deleted_at`), sehingga owner laundry dapat memulihkan data jika terjadi kesalahan atau kecurangan.


2. 
**Status History Log:** Setiap kali status transaksi berubah, sistem mencatat detail perubahan di tabel `transaction_status_logs`, merekam siapa (admin/kasir) yang mengubah status tersebut dan apa status sebelumnya .



---

## 6. Strategi Implementasi Teknis (Untuk AI Agent)

### 6.1. Definisi Migration Laravel

AI Agent harus membuat migrasi dengan urutan yang benar untuk menghindari *Foreign Key constraint error*. Urutan yang disarankan: `admins` → `customers` → `services` → `transactions` → `transaction_details` → `payments` → `shipments` → `transaction_status_logs`.

### 6.2. Optimasi Query (Eager Loading)

Untuk mencegah masalah **N+1 Query**, setiap pengambilan data transaksi wajib menggunakan *Eager Loading* untuk relasi detail dan pelanggan :

```php
Transaction::with(['customer', 'details.service', 'payments'])->get();

```

### 6.3. Keamanan PIN Kurir

Meskipun menggunakan satu PIN Toko, PIN tersebut tidak boleh disimpan dalam bentuk *plain text*. Gunakan enkripsi atau *hashing* sederhana agar tetap aman di dalam database.

---

## 7. Checklist Kesiapan Deployment

| No | Item Checklist | Target Konfigurasi |
| --- | --- | --- |
| 1 | **Koyeb Dockerfile** | Multi-stage build (Node.js & PHP 8.2).

 |
| 2 | **Database SSL** | Wajib aktifkan `verify_identity` untuk TiDB Cloud.

 |
| 3 | **Cloudinary Key** | Simpan API Key & API Secret di Environment Variable Koyeb.

 |
| 4 | **Midtrans Key** | Gunakan Server Key Sandbox untuk pengujian awal.

 |
| 5 | **App URL** | Set `APP_URL` di `.env` sesuai domain Koyeb agar aset Filament terpanggil sempurna. |

---

Dokumen ini adalah fondasi yang sangat kuat. Dengan mengikuti rancangan ini, AI Agent Anda akan memiliki konteks penuh mengenai **"Mengapa"** sebuah keputusan diambil (misalnya: mengapa tabel pengiriman digabung), sehingga kode yang dihasilkan tidak akan "halu" atau melenceng dari logika bisnis dunia nyata.

