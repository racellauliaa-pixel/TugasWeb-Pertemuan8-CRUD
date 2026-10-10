# CRUD Inventaris — Tugas Rutin 8

Aplikasi CRUD sederhana untuk mengelola data produk/inventaris, dibuat dengan
PHP Native + PDO + MySQL (tanpa framework).

## Fitur

- **Create** — tambah produk baru lewat form, dengan dropdown Kategori & Supplier
- **Read** — daftar produk dengan JOIN ke tabel `kategori` dan `supplier`
- **Update** — edit produk, form sudah terisi otomatis (pre-filled) dengan data lama
- **Delete** — hapus produk, wajib konfirmasi dulu (dialog JavaScript) sebelum terhapus
- Semua query database pakai **prepared statements** (PDO), nggak ada SQL yang digabung manual dari input user
- Semua output ke HTML dibungkus `htmlspecialchars()`
- Pesan sukses/gagal pakai **flash message** (pola redirect: POST → proses → redirect → tampilkan pesan sekali)
- Koneksi database pakai **Singleton Pattern** (lihat `includes/Database.php`)
- **Bonus:** fitur pencarian produk by nama, pagination (5 data per halaman), dan setiap proses hapus dibungkus **database transaction** bareng pencatatan `log_aktivitas` (kalau salah satu gagal, semuanya di-rollback)

## Struktur Database

3 tabel dengan foreign key:

```
kategori (id, nama_kategori)
supplier (id, nama_supplier, kontak)
produk   (id, nama_produk, kategori_id FK, supplier_id FK, harga, stok, created_at)
log_aktivitas (id, aksi, keterangan, waktu)   <- bonus, pencatatan histori delete
```

## Struktur Folder

```
TR8/
├── index.php              (daftar produk + pencarian + pagination)
├── create.php              (tambah produk)
├── update.php               (edit produk)
├── delete.php                (hapus produk, transaction + log)
├── schema.sql                 (struktur tabel + data contoh)
├── includes/
│   ├── Database.php            (koneksi PDO, Singleton Pattern)
│   ├── functions.php            (semua query CRUD, prepared statements)
│   └── flash.php                 (flash message)
└── assets/
    └── style.css
```

## Cara Menjalankan di Komputer Sendiri

### 1. Siapkan MySQL/MariaDB

Pastikan sudah ada MySQL Server jalan di komputer kamu (lewat XAMPP, atau
instalasi MySQL/MariaDB standalone).

### 2. Import Database

**Lewat phpMyAdmin** (kalau pakai XAMPP):
1. Buka `http://localhost/phpmyadmin`
2. Klik tab **Import**
3. Klik **Choose File**, pilih file `schema.sql` dari folder project ini
4. Klik **Go** di bagian bawah
5. Database `inventaris_db` otomatis kebuat beserta 3 tabel + data contohnya

**Lewat terminal/cmd** (kalau pakai MySQL standalone):
```
mysql -u root -p < schema.sql
```
(kosongkan password kalau root kamu nggak pakai password, tinggal Enter)

### 3. Sesuaikan Kredensial Database

Buka `includes/Database.php`, cek bagian ini:

```php
private const USER = 'root';
private const PASS = '';
```

Ganti `USER` dan `PASS` sesuai username/password MySQL kamu sendiri (kalau
beda dari default di atas).

### 4. Jalankan Server PHP

Dari folder project ini, jalankan:

```
php -S localhost:8000
```

Buka browser ke `http://localhost:8000/index.php`

## Catatan Keamanan

- Semua query pakai **prepared statement**, baik untuk SELECT, INSERT, UPDATE, maupun DELETE — nggak ada string SQL yang digabung langsung dari `$_POST`/`$_GET`
- Semua data dari user disaring lewat `htmlspecialchars()` sebelum disimpan maupun ditampilkan, untuk mencegah XSS
- Form delete cuma bisa diakses lewat method **POST**, bukan link `GET` biasa, supaya nggak kehapus nggak sengaja
- Validasi input di server-side (nama, kategori, supplier, harga, stok) sebelum data masuk ke database