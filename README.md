# NewsHub REST Client

Aplikasi REST Client sederhana berbasis PHP, cURL, HTML, dan CSS yang mengakses NewsAPI.org. Dibuat untuk latihan praktikum System Testing.

## Fitur
- Mengambil berita utama dari endpoint `top-headlines`.
- Mencari berita berdasarkan kata kunci melalui endpoint `everything`.
- Menampilkan judul, sumber, tanggal, ringkasan, dan gambar berita.
- Menangani API key kosong/salah, kegagalan koneksi, respons JSON, dan batas permintaan API.
- API key disimpan pada `.env` yang tidak diunggah ke GitHub.

## Persyaratan
- PHP 8.0 atau lebih baru (disarankan PHP 8.1+).
- Ekstensi PHP cURL aktif.
- Apache/XAMPP atau web server PHP.
- API key NewsAPI.org.

## Instalasi di XAMPP
1. Ekstrak folder `NewsHub-REST-Client` ke `C:\\xampp\\htdocs\\` (sesuaikan lokasi XAMPP Anda).
2. Salin `.env.example` dan ubah nama salinannya menjadi `.env`.
3. Daftar/login di https://newsapi.org/ untuk memperoleh API key.
4. Buka `.env`, lalu ganti `PASTE_API_KEY_ANDA_DI_SINI` dengan API key Anda.
5. Jalankan Apache dari XAMPP.
6. Buka `http://localhost/NewsHub-REST-Client/`.

> Jangan pernah mengunggah `.env` atau API key ke repository publik. File `.env` sudah dimasukkan ke `.gitignore`.

## Mengaktifkan cURL
Buka `php.ini` yang digunakan XAMPP, cari baris `;extension=curl`, hapus tanda titik koma menjadi `extension=curl`, simpan, lalu restart Apache. Pada beberapa versi PHP, nama baris dapat berupa `;extension=php_curl.dll`.

Periksa modul melalui `http://localhost/dashboard/phpinfo.php` jika tersedia, atau jalankan `php -m` di terminal dan pastikan `curl` muncul.

## Pengujian
- Halaman utama: `GET http://localhost/NewsHub-REST-Client/`
- Pencarian: `GET http://localhost/NewsHub-REST-Client/?q=technology`
- NewsAPI top headlines: `GET https://newsapi.org/v2/top-headlines?country=us&pageSize=12`
- NewsAPI everything: `GET https://newsapi.org/v2/everything?q=technology&language=en&sortBy=publishedAt&pageSize=12`

Untuk menguji NewsAPI langsung dengan Postman:
- Method: `GET`
- URL: `https://newsapi.org/v2/top-headlines?country=us&pageSize=12`
- Header: `X-Api-Key: API_KEY_ANDA`
- Header: `Accept: application/json`

## Catatan penting NewsAPI
Akses endpoint, batas penggunaan, dan ketersediaan berita mengikuti paket serta ketentuan akun NewsAPI. Paket Developer dapat memiliki pembatasan penggunaan dan umumnya ditujukan untuk pengembangan/pengujian, bukan aplikasi produksi publik. Jika API mengembalikan error, baca pesan yang ditampilkan dan periksa dashboard akun NewsAPI.

## Struktur folder
```text
NewsHub-REST-Client/
├── assets/
│   └── css/style.css
├── config/
│   ├── config.php
│   └── news_api.php
├── .env.example
├── .gitignore
├── index.php
├── postman_collection.json
└── README.md
```

## Lisensi
Untuk pembelajaran dan portofolio akademik.
