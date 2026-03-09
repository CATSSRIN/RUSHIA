# Setup Guide — Meratus Supply Order System

Panduan ini menjelaskan cara menyiapkan proyek dari awal hingga siap dijalankan di lingkungan lokal.

---

## Prasyarat

| Perangkat Lunak | Versi Minimum |
|-----------------|---------------|
| PHP             | 8.2           |
| Composer        | 2.x           |
| Node.js         | 18.x          |
| npm             | 9.x           |
| MySQL           | 8.0 (atau MariaDB 10.6) |
| Git             | 2.x           |

> **Catatan:** Proyek juga mendukung SQLite untuk pengembangan lokal (tidak memerlukan instalasi MySQL terpisah).

---

## 1. Clone Repositori

```bash
git clone https://github.com/CATSSRIN/testing.git
cd testing
```

---

## 2. Install Dependensi PHP

```bash
composer install
```

---

## 3. Salin File Environment

```bash
cp .env.example .env
```

---

## 4. Generate Application Key

```bash
php artisan key:generate
```

---

## 5. Konfigurasi Database

### Opsi A — MySQL (Produksi)

Edit file `.env` dan sesuaikan nilai berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=nama_user_anda
DB_PASSWORD=password_anda
```

Buat database terlebih dahulu di MySQL:

```sql
CREATE DATABASE nama_database_anda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Opsi B — SQLite (Pengembangan Lokal)

Edit `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/absolut/ke/database/database.sqlite
```

Buat file database SQLite:

```bash
touch database/database.sqlite
```

---

## 6. Jalankan Migrasi Database

```bash
php artisan migrate
```

Perintah ini akan membuat semua tabel yang diperlukan, termasuk:
- `users`, `vendors`, `ships`, `products`, `orders`, `order_items`
- `warehouse_receipts`
- **`ransum_uploads`** — menyimpan metadata dan header dari file BPB Ransum yang diupload
- **`ransum_items`** — menyimpan setiap baris item dari tabel ransum setelah diimport

---

## 7. Install Dependensi Frontend

```bash
npm install
```

---

## 8. Build Aset Frontend

```bash
# Untuk pengembangan (dengan hot-reload):
npm run dev

# Untuk produksi:
npm run build
```

---

## 9. Jalankan Server Pengembangan

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

---

## 10. Buat Akun Admin Pertama

Jalankan seeder (jika tersedia), atau buat akun admin secara manual melalui `php artisan tinker`:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('password'),
    'is_admin' => true,
]);
```

---

## Fitur Import BPB Ransum

### Cara Penggunaan

1. Login sebagai admin.
2. Klik menu **"Import Ransum"** di navigasi atas.
3. Klik tombol **"Upload & Preview"** dan pilih file Excel BPB Ransum (format `.xlsx` atau `.xls`).
4. Sistem akan memvalidasi:
   - Format file (hanya xlsx/xls, maks 10 MB)
   - File tidak rusak / dapat dibaca
   - Bukan file yang sudah pernah diupload sebelumnya (duplikat)
   - Template sesuai format BPB Ransum (terdapat kolom Vessel Code / Vessel Name)
5. Jika validasi berhasil, tampilan **Preview** akan muncul menampilkan:
   - Informasi header dokumen (Vessel Code, Vessel Name, Voyage, dll.)
   - Tabel item per seksi (BAHAN KERING, BUMBU DAPUR, dll.) secara fleksibel — baris dan seksi dapat bertambah
6. Klik tombol **"Import ke Database"** untuk menyimpan semua data ke MySQL.
7. Setelah import, status berubah menjadi **"Imported"** dan tombol import tidak tersedia lagi untuk file yang sama.

### Format Template Excel yang Didukung

Sistem membaca template BPB Ransum dengan struktur:

| Area | Baris | Keterangan |
|------|-------|------------|
| Header dokumen | Baris 4–9, Kolom E–L | Vessel Code, Vessel Name, Voyage, ETA, Budget, dll. |
| Header tabel | Baris 11–12 | Judul kolom |
| Seksi & item | Baris 12+ | Setiap seksi diawali baris header seksi (kolom B = "KODE ITEM"), diikuti baris data item yang fleksibel |

**Kolom item yang dibaca:**

| Kolom | Field |
|-------|-------|
| A | Nama Ransum |
| B | Kode Item |
| C | Items (deskripsi) |
| D | Merk/Spec |
| E | PPN |
| F | Supplier |
| G | Harga |
| H | Satuan |
| I | Pemesanan/Order (Qty) |
| J | Non BKP |
| K | BKP |
| L | PPN 11% |
| M | Ket. Remarks |
| N | Status Received |
| O | Good Received |

---

## Perintah Berguna

```bash
# Hapus semua cache
php artisan optimize:clear

# Lihat semua rute terdaftar
php artisan route:list

# Jalankan tes
php artisan test

# Publish konfigurasi Excel (jika diperlukan)
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

---

## Struktur Storage

File Excel yang diupload disimpan di:

```
storage/app/private/ransum_uploads/
```

Direktori ini **tidak bisa diakses publik** untuk alasan keamanan.

---

## Troubleshooting

### "File tidak dapat dibaca atau rusak"
Pastikan file Excel dalam format `.xlsx` atau `.xls` dan tidak corrupt. Coba buka file di Microsoft Excel / Google Sheets terlebih dahulu.

### "File ini sudah pernah diupload sebelumnya"
Sistem mendeteksi duplikat berdasarkan hash konten file. Jika perlu mengupload ulang dokumen yang sama dengan perubahan, pastikan ada perubahan pada isi file.

### "Template tidak valid"
Pastikan file menggunakan format BPB Ransum Meratus dengan Vessel Code/Vessel Name di sel F4/H4.

### Error migrasi "Connection refused"
Pastikan konfigurasi database di `.env` sudah benar dan server database sudah berjalan.
