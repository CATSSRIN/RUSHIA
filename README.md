# RUSHIA - Ransum Ship Order Management System

RUSHIA adalah sistem informasi manajemen pemesanan dan distribusi logistik ransum kapal berbasis web yang dikembangkan khusus untuk **PT Andalan Maritim Sejahtera (AMS)**. Sistem ini dirancang untuk mendigitalisasi dan mengotomatisasi alur kerja logistik pengadaan bahan makanan (ransum) crew kapal dari yang semula manual berbasis spreadsheet menjadi terkomputerisasi secara efisien, akurat, dan akuntabel.

---

## 🌟 Latar Belakang & Kegunaan

Dalam operasional maritim, pemenuhan kebutuhan ransum kapal harus dilakukan secara cepat dan presisi. Sebelum adanya sistem RUSHIA, proses ini memiliki kendala sebagai berikut:
1. **Heterogenitas Excel**: Dokumen permintaan dari kapal memiliki format kolom, satuan, dan penulisan harga yang tidak seragam.
2. **Pencocokan Manual**: Admin harus mencocokkan setiap item barang pesanan kapal secara manual dengan daftar kode produk database untuk menentukan vendor dan harga belinya.
3. **Pemecahan PO Lambat**: Satu pesanan kapal harus dipecah menjadi puluhan dokumen *Purchase Order* (PO) terpisah sesuai dengan masing-masing vendor penyuplai.
4. **Human Error**: Rawan terjadi kesalahan pencatatan harga beli/jual dan penulisan dokumen pengiriman.

**RUSHIA** hadir sebagai solusi digital untuk memotong waktu pemrosesan dokumen logistik dari **3–5 jam menjadi kurang dari 30 detik** melalui otomatisasi pembacaan berkas Excel, pencocokan kode produk otomatis, pemilah PO multi-vendor otomatis, serta pembuatan dokumen PDF siap cetak.

---

## 🚀 Fitur Utama

### 1. Mesin Pengurai Excel Cerdas (Excel Parser)
* **Unggah Massal**: Mendukung pengunggahan berkas Excel BPB Ransum secara langsung.
* **Deteksi Duplikasi**: Menggunakan enkripsi hash SHA-256 untuk mencegah pengunggahan berkas pesanan yang sama.
* **Pembacaan Header Dinamis**: Mendeteksi data kapal (nama, kode, voyage, port tujuan) secara otomatis.
* **Ekstraksi Tanda Tangan**: Secara cerdas mendeteksi batas akhir tabel menggunakan sensor kata kunci penandatangan dan mengekstrak foto tanda tangan digital ('pemohon' dan 'menyetujui').

### 2. Algoritma Pemilah PO Multi-Vendor Otomatis
* **Pencocokan Kode Produk**: Mencocokkan kode barang Excel dengan database master produk secara real-time.
* **Multi-vendor Splitting**: Memecah satu pesanan ransum kapal menjadi beberapa *Purchase Order* (PO) terpisah untuk setiap vendor/pemasok terkait secara otomatis.
* **Resolusi Harga Bertingkat**: Menggunakan logika heuristik (`normalizeMoneyString`) untuk menormalisasi format penulisan angka uang tidak konsisten dari Excel.

### 3. Otomatisasi Dokumen PDF Logistik & Keuangan
* **Purchase Order (PO)**: Penerbitan PO per vendor dengan nomor PO progresif, detail isian yang dapat disunting secara instan, dan siap cetak.
* **Delivery Order (DO)**: Pembuatan dokumen pengantar pengiriman barang logistik kapal dengan persetujuan kapten.
* **Invoice & Kwitansi**: Pembuatan invoice yang dapat disesuaikan (kustom) dengan biaya tambahan operasional dan fitur **Terbilang Otomatis** (konversi angka nominal ke huruf bahasa Indonesia).
* **Surat AMS**: Dokumen resmi pengajuan penyuplaian ransum.

### 4. Transparansi & Audit (Activity Logs)
* **Log Aktivitas Admin**: Setiap aksi administratif (unggah, edit draf, unduh dokumen, ubah status) dicatat secara sistematis lengkap dengan alamat IP dan stempel waktu (*timestamp*).
* **Dashboard Histori**: Halaman khusus untuk meninjau log aktivitas audit guna meningkatkan akuntabilitas internal perusahaan.

### 5. Manajemen Dokumen Aman
* **Penyimpanan Privat**: Semua dokumen PDF yang diterbitkan disimpan di direktori privat server (`storage/app/private/`) agar tidak dapat diakses secara bebas dari publik.
* **Pembaruan Status PO**: Pelacakan alur kerja PO dengan status *Menunggu*, *Diproses*, dan *Selesai*.

---

## 🛠️ Arsitektur Teknologi

* **Framework Utama**: Laravel 11/12+ (PHP 8.2+)
* **Database**: MySQL / MariaDB
* **Desain Tampilan (UI/UX)**: Tailwind CSS, Blade Templates, dan JavaScript (AJAX/Fetch API)
* **Mesin Excel**: Laravel Excel (Maatwebsite) / PhpSpreadsheet
* **Mesin PDF**: DomPDF (Barryvdh-DomPDF)

---

## 📦 Panduan Instalasi & Setup

### 1. Persyaratan Sistem
* PHP >= 8.2 (dengan ekstensi `gd`, `zip`, `xml`, `mbstring`)
* Composer
* Node.js & NPM
* Database MySQL / MariaDB

### 2. Kloning Repositori & Instalasi Dependensi
```bash
git clone <url-repositori-anda>
cd Conversion
```

Instal dependensi PHP (Laravel):
```bash
composer install
```

Instal dependensi JavaScript & CSS (Tailwind):
```bash
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Buka berkas `.env` dan konfigurasikan koneksi database Anda:
```env
APP_NAME="RUSHIA LOGISTICS"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ship_order
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Inisialisasi Aplikasi
Generate key aplikasi:
```bash
php artisan key:generate
```

Jalankan migrasi database beserta pengisian data awal (seeder):
```bash
php artisan migrate --seed
```
*Seeder ini akan membuat akun admin bawaan, data beberapa kapal, vendor, serta master produk dengan kode ransum.*

### 5. Kredensial Akun Bawaan (Default Admin)
Setelah proses seeder berhasil, Anda dapat masuk menggunakan akun admin berikut:
* **Email**: `admin@shiporder.com`
* **Kata Sandi**: `password`

### 6. Jalankan Server
Kompilasi aset tampilan frontend:
```bash
npm run dev
```

Jalankan server lokal Laravel:
```bash
php artisan serve
```
Buka peramban (browser) Anda dan akses `http://localhost:8000`.

---

## 🧪 Menjalankan Pengujian (Testing)
Untuk memastikan keandalan alur integrasi parser Excel dan kalkulasi dokumen keuangan, jalankan perintah pengujian bawaan:
```bash
php artisan test
```

---

## 📄 Lisensi
Perangkat lunak ini dikembangkan sebagai bagian dari proyek operasional internal **PT Andalan Maritim Sejahtera** dan dilindungi di bawah Hak Kekayaan Intelektual (HKI) Program Komputer.
