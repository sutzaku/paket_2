# 🏫 UKK Paket 2: Sistem Pengaduan Sarana Sekolah

Aplikasi berbasis web untuk pelaporan dan pengelolaan keluhan sarana/fasilitas sekolah. Dibangun menggunakan **Laravel 12** dan **Filament Admin Panel (v4)** dengan arsitektur role-based access control (Admin & Siswa).

---

## 💻 Tech Stack & Kebutuhan Sistem

- **PHP:** >= 8.2 (ekstensi: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, intl)
- **Framework:** Laravel 12
- **Admin Panel:** Filament v4
- **Database:** MySQL (via Laragon)
- **Package Manager:** Composer
- **Web Server:** Laragon (Apache/Nginx & MySQL)

---

## 🚀 Panduan Instalasi di Laptop Teman (Menggunakan Laragon)

Ikuti langkah-langkah berikut secara berurutan agar aplikasi dapat berjalan lancar tanpa error:

### 1. Buka Laragon & Aktifkan Service
- Buka aplikasi **Laragon** di laptop.
- Klik tombol **Start All** (pastikan Apache dan MySQL sudah berjalan / warna hijau).

---

### 2. Buka Terminal / CMD di Folder Project
Sangat disarankan meletakkan folder project di dalam direktori `www` Laragon, contoh:
`C:\laragon\www\pengaduan-sarana`

Jika melakukan clone dari GitHub:
```bash
cd C:\laragon\www
git clone <URL_REPOSITORY_GITHUB_ANDA> pengaduan-sarana
cd pengaduan-sarana
```

---

### 3. Install Dependensi PHP (Composer)
Jalankan perintah berikut di terminal:
```bash
composer install
```
> *Catatan:* Konfigurasi sudah disiapkan agar Composer tidak memblokir paket Filament.

---

### 4. Salin File Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:

**Di Windows (Command Prompt / PowerShell):**
```bash
copy .env.example .env
```
*Atau di Git Bash / Linux:*
```bash
cp .env.example .env
```

> **Informasi:** File `.env.example` sudah disesuaikan secara default untuk Laragon:
> ```env
> DB_CONNECTION=mysql
> DB_HOST=127.0.0.1
> DB_PORT=3306
> DB_DATABASE=pengaduan_sarana
> DB_USERNAME=root
> DB_PASSWORD=
> ```

---

### 5. Generate Application Key
Jalankan perintah berikut untuk membuat enkripsi kunci aplikasi:
```bash
php artisan key:generate
```

---

### 6. Buat Database di Laragon (MySQL)
Buat database baru bernama **`pengaduan_sarana`**:
- **Cara 1 (HeidiSQL - Bawaan Laragon):**
  1. Klik tombol **Database** di Laragon.
  2. Klik **Open** (User: `root`, Password: kosongkan).
  3. Klik kanan di area daftar database sebelah kiri > **Create new** > **Database**.
  4. Beri nama: `pengaduan_sarana` lalu klik **OK**.
- **Cara 2 (phpMyAdmin):**
  1. Buka browser: `http://localhost/phpmyadmin`
  2. Buat database baru dengan nama `pengaduan_sarana`.

---

### 7. Jalankan Migrasi & Data Seeder
Jalankan perintah ini untuk membuat tabel otomatis dan mengisi data akun awal serta kategori sarana:
```bash
php artisan migrate --seed
```

---

### 8. Bersihkan Cache & Link Storage (Opsional)
Untuk memastikan cache aplikasi segar:
```bash
php artisan optimize:clear
php artisan storage:link
```

---

### 9. Jalankan Web Server
Jalankan perintah server Laravel:
```bash
php artisan serve
```

Buka browser dan akses URL admin panel:
👉 **[http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)**

*(Atau jika menggunakan fitur Pretty URL Laragon, bisa membuka `http://pengaduan-sarana.test/admin`)*

---

## 🔑 Akun untuk Login Pengujian

Aplikasi memiliki 2 role pengguna yang sudah di-seed secara otomatis:

### 1. Akun Admin
- **Email:** `admin@sekolah.test`
- **Password:** `admin123`
- **Hak Akses:**
  - Melihat **Dashboard** berisi Widget statistik jumlah pengaduan (*Menunggu*, *Proses*, *Selesai*).
  - Mengelola menu **Kategori Fasilitas** (Tambah, Edit, Hapus).
  - Mengelola menu **Pengguna** / Siswa (Tambah, Edit, Ganti Role, Hapus).
  - Melihat **semua aspirasi/pengaduan** dari seluruh siswa.
  - Memberikan tanggapan / respon keluhan (ubah status ke *Menunggu* / *Proses* / *Selesai* dan mengisi kolom umpan balik).
  - Melakukan pencarian dan filter laporan berdasarkan kategori, tanggal, bulan, serta NIS siswa.

### 2. Akun Siswa
- **Email:** `siswa@sekolah.test`
- **Password:** `123456`
- **Hak Akses:**
  - Melihat **Dashboard**.
  - Mengakses menu **Pengaduan**:
    - Hanya dapat melihat pengaduan yang dibuat oleh dirinya sendiri.
    - Membuat pengaduan baru (otomatis tercatat atas nama siswa login, status awal otomatis *Menunggu*).
    - Memantau perubahan status dan umpan balik yang diberikan oleh admin.
  - *Tidak memiliki akses ke menu Kategori dan Pengguna (Otomatis ditolak/Forbidden 403).*

---

## 🛠️ Panduan Penyelesaian Masalah (Troubleshooting)

| Masalah | Penyebab | Solusi |
| :--- | :--- | :--- |
| **SQLSTATE[HY000] [1049] Unknown database 'pengaduan_sarana'** | Database belum dibuat di Laragon | Buka HeidiSQL / phpMyAdmin di Laragon, lalu buat database dengan nama `pengaduan_sarana`. |
| **SQLSTATE[HY000] [2002] Connection refused** | Service MySQL belum nyala | Buka Laragon dan pastikan sudah klik tombol **Start All**. |
| **Tampilan CSS/JS Filament berantakan** | Asset belum ter-publish | Jalankan perintah `php artisan filament:upgrade` lalu refresh browser. |
| **Target class [AdminPanelProvider] does not exist** | Provider belum terdaftar | Cek file `bootstrap/providers.php` dan pastikan `AdminPanelProvider::class` sudah terdaftar. |
| **Error Permission pada cache/logs** | Izin folder di Windows/Linux | Jalankan `php artisan optimize:clear`. |

---

## 📁 Struktur Direktori Penting

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── Categories/       # Resource Manajemen Kategori (Admin)
│   │   ├── Pengaduans/       # Resource Pengaduan (Siswa & Admin)
│   │   └── Users/            # Resource Manajemen Pengguna (Admin)
│   └── Widgets/
│       └── StatsOverview.php # Widget Statistik Status Pengaduan
├── Models/
│   ├── Category.php          # Model Kategori
│   ├── Pengaduan.php         # Model Pengaduan (Relasi User & Category)
│   └── User.php              # Model Pengguna & Role Check (isAdmin)
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php  # Konfigurasi Panel Admin Filament
database/
├── migrations/               # Struktur tabel database
└── seeders/
    └── DatabaseSeeder.php    # Data awal Admin, Siswa, dan Kategori Fasilitas
```
