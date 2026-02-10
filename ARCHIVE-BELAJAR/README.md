<div align="center">

# Laundry App Database
### Jejak Pembelajaran: Dari Tugas Kuliah ke Implementasi Nyata

[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Draw.io](https://img.shields.io/badge/Design-Draw.io-F08705?style=flat-square&logo=diagrams.net&logoColor=white)](https://app.diagrams.net/)
[![Claude AI](https://img.shields.io/badge/Co--Pilot-Claude_3.5-D97757?style=flat-square&logo=anthropic&logoColor=white)](https://anthropic.com/)
[![Status](https://img.shields.io/badge/Status-Learning_Archive-success?style=flat-square)]()

*Dokumentasi perjalanan dari perancangan akademis menuju database production-ready.*

[📂 SQL Schema (Dev)](laundry_schema_dev.sql) · [📊 ERD Diagram](Laundryapp.drawio.png) · [📝 Analisis Awal](Analisis_ERD_Laundry_System.docx) · [📝 Analisis Final](Analisis_ERD_Laundry_Praktis_v2.docx)

</div>

---

## Tentang Folder Ini

Folder `ARCHIVE-BELAJAR` adalah dokumentasi lengkap perjalanan pembelajaran saya dalam merancang dan mengimplementasikan sistem database laundry. Proyek ini bermula dari Tugas Besar Rekayasa Perangkat Lunak yang saya kerjakan bersama tim, namun saya merasa sayang jika hanya berhenti sebagai dokumen perancangan tanpa pernah diimplementasikan.

Semua file tugas kuliah asli tersimpan di subfolder **[TUGASBESAR REKAYASA PERANGKAT LUNAK](TUGASBESAR%20REKAYASA%20PERANGKAT%20LUNAK/)**, sementara file-file di luar folder tersebut merupakan hasil pengembangan dan analisis lebih lanjut yang saya lakukan secara mandiri.

### Struktur Folder

```
ARCHIVE-BELAJAR/
├── TUGASBESAR REKAYASA PERANGKAT LUNAK/    # File tugas kuliah asli
├── Analisis_ERD_Laundry_System.docx        # Analisis awal (AI-generated)
├── Analisis_ERD_Laundry_Praktis_v2.docx    # Analisis final (best practice)
├── Laundryapp.drawio.png                   # ERD diagram final
├── laundry_schema_dev.sql                  # SQL schema (active development)
└── README.md                               # Dokumentasi ini
```

---

## Tim Pengembang

Proyek ini merupakan hasil kolaborasi tim dalam mata kuliah Rekayasa Perangkat Lunak:

<div align="center">

| Peran | Nama | NIM |
|:------|:-----|:----|
| **Ketua Tim** | **Sulthonika Mahfudz Al Mujahidin** | 1202230023 |
| Anggota | Muhammad Fajar Shodiq | 1202230045 |
| Anggota | Teuku Ismail Syuhada | 1202230036 |
| Anggota | Davinsyah Putra Antoro | 1202230054 |
| Anggota | Muhammad Dwiky Yanuarezza | - |

</div>

<br>

<div align="center">

**Terima Kasih Kepada Dosen Pengampu:**

### Ibu Mastuty Ayu Ningtyas, S.Kom., M.MT.

*Terima kasih atas bimbingan dan ilmu rekayasa perangkat lunak yang telah Ibu berikan. Fondasi yang Ibu tanamkan menjadi modal utama dalam pengembangan lebih lanjut proyek ini.*

</div>

---

## Evolusi Desain Database

### Fase 1: Perancangan Akademis

Desain awal kami sangat komprehensif dan mengikuti prinsip normalisasi database secara ketat. Hasilnya adalah struktur yang solid secara teori, namun ketika saya mulai membayangkan implementasinya, muncul beberapa pertanyaan:

- Apakah struktur ini terlalu kompleks untuk skala UMKM?
- Berapa lama waktu yang dibutuhkan untuk mengimplementasikan semua fitur ini?
- Apakah semua tabel dan relasi ini benar-benar dibutuhkan di tahap awal?

File `Analisis_ERD_Laundry_System.docx` adalah hasil analisis pertama yang saya generate menggunakan Claude AI untuk memvalidasi desain kami. Hasilnya mengkonfirmasi kekhawatiran saya: desain kami terlalu "enterprise" untuk konteks bisnis laundry skala kecil-menengah.

### Fase 2: Optimasi dengan AI

Saya kemudian melakukan diskusi mendalam dengan Claude Sonnet 4.5 untuk menemukan sweet spot antara kelengkapan fitur dan kesederhanaan implementasi. Proses ini menghasilkan `Analisis_ERD_Laundry_Praktis_v2.docx` yang menerapkan prinsip YAGNI (You Aren't Gonna Need It) tanpa mengorbankan skalabilitas.

### Perbandingan Detail

| Aspek | Desain Awal | Desain Final |
|:------|:------------|:-------------|
| **Jumlah Tabel** | 15+ tabel dengan normalisasi penuh | 8 tabel inti dengan denormalisasi strategis |
| **Kompleksitas JOIN** | Query membutuhkan 4-6 JOIN untuk data transaksi | Maksimal 2-3 JOIN untuk operasi umum |
| **Tabel Lookup** | Terpisah untuk setiap kategori (status, payment method, dll) | Menggunakan ENUM dan konstanta aplikasi |
| **Audit Trail** | Tabel terpisah untuk setiap jenis log | Satu tabel `transaction_status_logs` dengan polymorphic design |
| **User Management** | Sistem role-permission kompleks dengan pivot tables | Role sederhana (owner, kasir, courier) di tabel admins |
| **Inventory** | Tracking detail bahan kimia dan supplies | Dihilangkan - fokus pada core business |
| **Pricing** | Dynamic pricing dengan tabel rules kompleks | Base price di services + adjustment manual |
| **Customer Tiers** | Membership system dengan loyalty points | Simple customer_type (individual/corporate) |

### Perbedaan Filosofi

**Desain Awal:**
- Mengantisipasi semua kemungkinan fitur masa depan
- Normalisasi ketat mengikuti teori database
- Cocok untuk sistem enterprise dengan tim developer besar
- Estimasi waktu implementasi: 4-6 bulan

**Desain Final:**
- Fokus pada MVP (Minimum Viable Product)
- Denormalisasi strategis untuk performa
- Cocok untuk startup/UMKM dengan resource terbatas
- Estimasi waktu implementasi: 1-2 bulan
- Mudah di-scale up seiring pertumbuhan bisnis

### Contoh Konkret: Tabel Transaksi

**Sebelum (Desain Awal):**
```
transactions
├── transaction_items (detail per item)
├── transaction_payments (history pembayaran)
├── transaction_status_history (log perubahan status)
├── transaction_discounts (diskon yang diterapkan)
├── transaction_taxes (pajak yang dihitung)
└── transaction_notes (catatan terpisah)
```

**Sesudah (Desain Final):**
```
transactions (dengan kolom agregat: total_cost, total_paid)
├── transaction_details (item + subtotal)
├── payments (history pembayaran dengan status)
└── transaction_status_logs (log + notes dalam satu tabel)
```

Pengurangan 6 tabel menjadi 3 tabel tanpa kehilangan informasi penting. Query menjadi lebih cepat, kode lebih mudah di-maintain.

---

## Mengapa Transparan Soal AI?

Saya sengaja mencantumkan bahwa dua file analisis di-generate dengan bantuan Claude AI. Ini bukan untuk pamer teknologi, tapi untuk menunjukkan skill yang relevan di era modern:

**Kemampuan yang Saya Demonstrasikan:**

1. **Critical Thinking** - Tidak menerima output AI mentah-mentah, tapi melakukan iterasi dan validasi
2. **Prompt Engineering** - Mampu mengarahkan AI untuk menghasilkan analisis yang sesuai konteks bisnis
3. **Database Design** - Memahami trade-off antara normalisasi dan performa
4. **Practical Implementation** - Mengubah teori menjadi SQL schema yang executable

Dalam konteks melamar pekerjaan database engineer/architect, saya ingin menunjukkan bahwa saya tidak hanya paham teori, tapi juga mampu:
- Menggunakan tools modern untuk meningkatkan produktivitas
- Melakukan analisis mendalam terhadap desain database
- Membuat keputusan arsitektur berdasarkan konteks bisnis
- Mendokumentasikan proses berpikir dengan jelas

> "Kode lama bukan sampah yang harus dibuang, melainkan fondasi yang bisa diperkuat. Yang benar-benar usang adalah mindset yang menolak untuk berkembang."

---

## Artefak Teknis

### 1. ERD Diagram ([Laundryapp.drawio.png](Laundryapp.drawio.png))

Visualisasi lengkap struktur database final dengan relasi antar tabel. Diagram ini saya gambar ulang menggunakan Draw.io setelah proses optimasi, menunjukkan:
- 8 tabel inti dengan relasi yang jelas
- Cardinality yang tepat (1:N, N:M)
- Primary key dan foreign key
- Kolom-kolom penting di setiap tabel

### 2. SQL Schema ([laundry_schema_dev.sql](laundry_schema_dev.sql))

File SQL lengkap yang berisi:
- DDL (Data Definition Language) untuk semua tabel
- Index untuk optimasi query
- Foreign key constraints dengan cascade rules
- Default values dan constraints
- Comments untuk dokumentasi

File ini siap dieksekusi dan akan membuat database lengkap. Saya sengaja push file ini ke repository meskipun ukurannya besar, karena ini adalah learning project dan transparansi lebih penting daripada best practice git.

### 3. Dokumen Analisis

- **[Analisis_ERD_Laundry_System.docx](Analisis_ERD_Laundry_System.docx)** - Analisis awal yang mengidentifikasi kompleksitas berlebihan
- **[Analisis_ERD_Laundry_Praktis_v2.docx](Analisis_ERD_Laundry_Praktis_v2.docx)** - Analisis final dengan rekomendasi best practice

---

## Pembelajaran Utama

Dari proyek ini, saya belajar bahwa database design bukan hanya soal mengikuti aturan normalisasi, tapi tentang:

1. **Memahami Konteks Bisnis** - Skala UMKM butuh pendekatan berbeda dari enterprise
2. **Balance Theory & Practice** - Teori penting, tapi implementability lebih penting
3. **Iterative Design** - Desain pertama jarang sempurna, perlu refinement
4. **Documentation Matters** - Kode tanpa dokumentasi adalah technical debt
5. **Embrace Modern Tools** - AI bukan pengganti skill, tapi amplifier

---

<div align="center">

**Laundry App Database - Learning Archive**


</div>
