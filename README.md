# SIPUS-Del — Tahap 3: Web Services & REST API

Aplikasi Laravel 11 untuk pengelolaan katalog perpustakaan, integrasi SOAP legacy, dan REST API stateless untuk aplikasi mobile/kiosk.

## Identitas Mahasiswa

| Data | Identitas |
|---|---|
| Nama Lengkap | Mia Nathania Sibuea |
| NIM | 12S24005 |
| Kelas | **Lengkapi kelas Anda** |
| Program Studi | 13SI |

## Analisis Kebutuhan dan Kelayakan Web Services

| Sistem / integrasi | Peran SIPUS-Del | Protokol | Alasan |
|---|---|---|---|
| Aplikasi administrasi yudisium legacy | Provider | SOAP/XML | Sistem lama memerlukan kontrak WSDL dan pesan XML untuk memeriksa status bebas pustaka. |
| Aplikasi mobile dan kiosk mandiri | Provider | REST/JSON | HTTP stateless, payload ringkas, caching GET, dan status HTTP sesuai operasi membuatnya lebih mudah dipakai oleh berbagai klien. |
| Open Library dan GitHub | Consumer | REST/JSON | SIPUS-Del meminta metadata buku dan metadata repository publik melalui Laravel HTTP Client tanpa mengekspos token atau bergantung pada sesi browser. |

SOAP dipertahankan hanya pada integrasi yudisium yang sudah mensyaratkan kontrak SOAP/WSDL. Untuk mobile dan kiosk yang baru, REST lebih sesuai karena berorientasi resource, menggunakan semantik HTTP, dan tidak mengharuskan klien membawa envelope XML. API katalog publik memisahkan representasi JSON dari tabel Eloquent melalui API Resources. Operasi mutasi dan sirkulasi memerlukan token bearer; transaksi peminjaman/pengembalian mengunci baris buku di dalam transaksi database.

## Endpoint

Semua endpoint REST berada di `/api/v1`. Galat API menggunakan `application/problem+json` (Problem Details) dan respons sukses memakai amplop `data`, `message`, serta `meta` (paginasi juga mengembalikan `links`).

| Method | URI | Proteksi | Fungsi | Status utama |
|---|---|---|---|---|
| GET | `/soap/sipus.wsdl` | Publik | Kontrak WSDL bebas pustaka | 200 |
| POST | `/soap/sipus` | Publik | Periksa pinjaman aktif berdasarkan NIM (SOAP XML) | 200, 500 SOAP Fault |
| GET | `/api/v1/bukus` | Publik | Katalog dengan `search`, `kategori_id`, `tersedia`, `sort`, `per_page` | 200, 422 |
| GET | `/api/v1/bukus/{id}` | Publik | Detail buku, kategori, dan riwayat | 200, 404 |
| GET | `/api/v1/bukus/{id}/referensi-eksternal` | Publik | Metadata Open Library dengan timeout 8 detik | 200 |
| GET | `/api/v1/github/repos/{owner}/{repository}` | Publik | Metadata repository publik melalui GitHub REST API | 200, 404, 502 |
| POST | `/api/v1/auth/login` | Publik | Login dan penerbitan token Sanctum | 200, 401, 422 |
| GET | `/api/v1/auth/me` | Bearer | Profil pemilik token | 200, 401 |
| POST | `/api/v1/auth/logout` | Bearer | Cabut token aktif | 200, 401 |
| POST | `/api/v1/bukus` | Bearer | Buat buku | 201 + `Location`, 401, 422 |
| PUT | `/api/v1/bukus/{id}` | Bearer | Ganti semua atribut buku | 200, 401, 404, 422 |
| PATCH | `/api/v1/bukus/{id}` | Bearer | Ubah hanya atribut yang dikirim | 200, 401, 404, 422 |
| DELETE | `/api/v1/bukus/{id}` | Bearer | Hapus buku tanpa pinjaman aktif | 204, 401, 404, 409 |
| GET | `/api/v1/peminjamans` | Bearer | Riwayat dengan filter `status`, `nim`, `per_page` | 200, 401, 422 |
| GET | `/api/v1/peminjamans/{id}` | Bearer | Detail transaksi peminjaman | 200, 401, 404 |
| POST | `/api/v1/peminjamans` | Bearer | Pinjam buku dengan transaksi ACID | 201 + `Location`, 401, 409, 422 |
| PATCH | `/api/v1/peminjamans/{id}/kembalikan` | Bearer | Kembalikan buku dan tambah stok | 200, 401, 404, 409 |

### Semantik dan keamanan

- `PUT` mewajibkan semua atribut buku; `PATCH` hanya memvalidasi atribut yang dikirim.
- Katalog memakai eager loading kategori dan hitungan peminjaman. Tautan `pinjam_buku` hanya muncul bila stok tersedia; tautan `kembalikan_buku` hanya muncul untuk transaksi aktif.
- Pinjam/kembali memakai `DB::transaction()` dan `lockForUpdate()`. Stok habis menghasilkan 422, pinjaman ganda atau penghapusan buku yang sedang dipinjam menghasilkan 409.
- Sanctum bearer token dipakai untuk semua mutasi, daftar transaksi, dan endpoint profil. Logout mencabut token yang digunakan.
- Respons API tidak menampilkan stack trace atau rincian exception server.

## Instalasi dan Pengujian

Prasyarat: PHP 8.2+, Composer, ekstensi PHP `pdo_sqlite`, `dom`, `simplexml`, `mbstring`, dan `curl`.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Kerjakan perubahan pada branch `week9-rest-api`:

```powershell
git switch week9-rest-api
```

Database default lokal adalah SQLite (`database/database.sqlite`). Seeder membuat pustakawan lokal:

- Email: `pustakawan@del.ac.id`
- Password: `password123`

Jika database lokal sudah memiliki data, buat akun uji tanpa mereset katalog:

```powershell
php artisan db:seed --class=PustakawanSeeder
```

Seeder ini idempoten dan aman dijalankan kembali.

Jalankan pengujian terotomasi (menggunakan SQLite in-memory, tidak menghapus database lokal):

```powershell
php artisan test
```

Kontrak OpenAPI dan koleksi/environment Postman tersedia di `docs/`. Import `SIPUS-Del-API-v1.postman_collection.json` serta `SIPUS-Del-Local.postman_environment.json`, pilih environment lokal, jalankan login, lalu isi `categoryId`, `bookId`, dan `loanId` sesuai data uji. Koleksi tidak menyimpan bearer token; token login hanya disimpan ke environment Postman saat tes dijalankan.

### Contoh pemanggilan

```powershell
curl.exe -H "Accept: application/json" "http://127.0.0.1:8000/api/v1/bukus?tersedia=1&sort=judul"
curl.exe -X POST "http://127.0.0.1:8000/api/v1/auth/login" -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"email\":\"pustakawan@del.ac.id\",\"password\":\"password123\"}"
```

Contoh body SOAP:

```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header/>
  <soap:Body>
    <CheckClearanceStatus><nim_peminjam>12S24005</nim_peminjam></CheckClearanceStatus>
  </soap:Body>
</soap:Envelope>
```

## Bukti Pengujian Manual

Tambahkan tangkapan layar hasil Postman Anda di `docs/screenshots/` sebelum pengumpulan. Jangan menyertakan bearer token aktif; sensor token apabila terlihat.

1. Permintaan SOAP XML berhasil dan SOAP Fault untuk XML/NIM tidak valid.
2. Login API berhasil (token disensor).
3. GET katalog berpaginasi dengan filter dan tautan HATEOAS.
4. POST buku menghasilkan 201 dan header `Location`.
5. Perbandingan PUT (422 saat atribut wajib hilang) dan PATCH (200).
6. Peminjaman berhasil, penolakan stok habis (422), konflik pinjaman (409), dan pengembalian.
7. Latihan GitHub REST API dari akun Anda dengan seluruh token disensor.

Tangkapan layar autentikasi GitHub memerlukan akun/token milik mahasiswa dan tidak dibuat atau disimpan oleh repository ini. Request PATCH `/user` pada koleksi Postman mengubah bio profil GitHub; jalankan hanya jika memang ingin mengubah bio tersebut.

## Struktur Utama

```text
app/Http/Controllers/Api/V1/   REST API provider
app/Http/Controllers/Soap/    SOAP bebas pustaka
app/Http/Resources/           Representasi JSON dan HATEOAS
database/migrations/          Skema katalog, transaksi, dan token
docs/                         OpenAPI dan koleksi Postman
routes/api.php                API stateless /api/v1
```

`.env`, token akses, dan kredensial eksternal tidak boleh dimasukkan ke repository.
