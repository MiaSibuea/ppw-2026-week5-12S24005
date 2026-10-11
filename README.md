# SIPUS-Del
## Sistem Informasi Perpustakaan Kampus Del

SIPUS-Del merupakan aplikasi berbasis web untuk mengelola data perpustakaan kampus. Aplikasi ini dikembangkan menggunakan Laravel 11 dengan menerapkan konsep CRUD (Create, Read, Update, Delete) pada pengelolaan data buku dan kategori buku.

## Fitur Aplikasi

### Manajemen Kategori
- Menampilkan daftar kategori buku
- Menambahkan kategori baru
- Mengubah data kategori
- Menghapus kategori
- Melihat daftar buku berdasarkan kategori

### Manajemen Buku
- Menampilkan katalog koleksi buku
- Menambahkan data buku
- Mengubah data buku
- Menghapus data buku
- Melihat detail buku

### Fitur Tambahan
- Validasi input menggunakan server-side validation
- Pagination data
- Relasi One-to-Many antara kategori dan buku
- Eager Loading untuk mengoptimalkan query database
- Konfirmasi sebelum menghapus data


## Teknologi yang Digunakan

- Laravel 11
- PHP 8.3
- MySQL
- Bootstrap
- Blade Template Engine


## Struktur Database

### Tabel kategoris

| Field | Tipe Data |
|---|---|
| id | bigint |
| kode_kategori | varchar |
| nama_kategori | varchar |
| created_at | timestamp |
| updated_at | timestamp |


### Tabel bukus

| Field | Tipe Data |
|---|---|
| id | bigint |
| isbn | varchar |
| judul | varchar |
| penulis | varchar |
| penerbit | varchar |
| tahun_terbit | year |
| kategori_id | bigint |
| stok | integer |
| sinopsis | text |
| created_at | timestamp |
| updated_at | timestamp |


## Relasi Database

SIPUS-Del menggunakan relasi One-to-Many:

```
Kategori
    |
    | 1 : N
    |
Buku
```

Satu kategori dapat memiliki banyak buku, sedangkan satu buku hanya memiliki satu kategori.


## Instalasi Project

Clone repository:

```bash
git clone https://github.com/MiaSibuea/ppw-2026-week5-12S24005.git
```

Masuk ke folder project:

```bash
cd ppw-2026-week5-12S24005
```

Install dependency:

```bash
composer install
```

Buat file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Lakukan konfigurasi database pada file `.env`, kemudian jalankan migration:

```bash
php artisan migrate --seed
```

Jalankan aplikasi:

```bash
php artisan serve
```


## Struktur Folder Utama

```
app/
 ├── Http/
 │    └── Controllers/
 └── Models/

database/
 ├── migrations/
 └── factories/

resources/
 └── views/

routes/
 └── web.php
```


## Keamanan Repository

File `.env` dan folder `vendor` tidak disimpan dalam repository GitHub karena berisi konfigurasi dan dependency lokal.

Repository menyediakan file:

```
.env.example
```

sebagai template konfigurasi environment.


## Author

Mia Sibuea

Program Studi Sistem Informasi