# Sistem Informasi Kelurahan Sasi

Sistem Informasi Kelurahan Sasi adalah aplikasi web untuk pelayanan administrasi kelurahan, pelacakan permohonan surat, dan manajemen data internal. Proyek ini dibangun dengan PHP native, MySQL, dan Bootstrap 5.

## Fitur Utama

- Pengajuan surat online dengan upload dokumen pendukung
- Tracking permohonan menggunakan tracking ID unik
- Panel admin untuk manajemen jenis surat, pengumuman, penduduk, dan laporan
- Autentikasi pengguna dengan session dan password hashing
- Proteksi upload file dan validasi input server-side
- Generate PDF untuk surat yang disetujui

## Struktur Proyek

```
desa_sasi/
+-- admin/                    # Halaman admin
+-- api/                      # Endpoints internal / AJAX
+-- assets/                   # Aset CSS, JS, dan gambar
+-- config/                   # Konfigurasi dan helper
+-- database/                 # SQL schema dan data base
+-- includes/                 # Template layout bersama
+-- proses/                   # Form handler dan CRUD logic
+-- public/                   # Halaman publik
+-- tools/                    # Utility scripts
+-- uploads/                  # Folder upload file
+-- vendor/                   # Dependensi Composer
+-- .htaccess
+-- composer.json
+-- package.json
+-- tailwind.config.js
+-- README.md
```

## Halaman Publik

- `public/index.php` — beranda dengan ringkasan layanan dan pengumuman
- `public/layanan_surat.php` — form pengajuan surat online
- `public/lacak_permohonan.php` — pelacakan status permohonan
- `public/pengumuman.php` — daftar pengumuman desa
- `public/profil-desa.php` — profil desa dan informasi pemerintahan

## Halaman Admin

- `admin/login.php` — autentikasi admin/operator
- `admin/dashboard.php` — ringkasan statistik dan shortcut
- `admin/jenis_surat.php` — manajemen jenis surat
- `admin/kelola_permohonan.php` — verifikasi dan update status permohonan
- `admin/penduduk.php` — manajemen data penduduk
- `admin/pengumuman.php` — publikasi pengumuman
- `admin/profile.php` — pengaturan profil admin
- `admin/users.php` — manajemen akun admin/operator
- `admin/laporan.php` — laporan pengajuan dan statistik

## Pengaturan dan Instalasi

### 1. Siapkan lingkungan
- XAMPP atau web server dengan PHP 7.4+ / 8.x
- MySQL atau MariaDB
- Node.js untuk build CSS Tailwind

### 2. Import database
```bash
mysql -u root desa_sasi < database/desa_sasi.sql
```

### 3. Install dependensi
```bash
cd c:\xampp\htdocs\desa_sasi
npm install
```

### 4. Bangun CSS
```bash
npm run css:dev
npm run css:build
```

### 5. Jalankan aplikasi
- Buka `http://localhost/desa_sasi/`
- Buka `http://localhost/desa_sasi/admin/login.php` untuk admin

## Keamanan dan Praktik

- Semua input divalidasi di server
- Query database menggunakan PDO prepared statements
- Password disimpan dengan `password_hash`
- Folder `uploads/` dilindungi agar tidak menjalankan PHP

## Utilitas

- `tools/audit_db.php` — audit struktur database
- `tools/check_uploads.php` — cek hak akses dan proteksi folder upload
- `tools/migrate_to_app_schema.php` — migrasi skema aplikasi
# desadigital
