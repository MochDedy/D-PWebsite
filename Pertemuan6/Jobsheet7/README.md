# SIMPUS-Mini — Jobsheet 7 (PHP Dasar & Form Handling)

## Perubahan dari Jobsheet 6
- Semua halaman `.html` diubah menjadi `.php`.
- `includes/header.php` & `includes/footer.php` dipakai lewat `include`; path CSS/JS/menu dihitung otomatis lewat `$base`.
- `buku/tambah.php` & `anggota/tambah.php`: form `method="post"` ke `proses_tambah.php`.
- `proses_tambah.php`: validasi `$_POST` di server, simpan ke `$_SESSION['buku']` / `$_SESSION['anggota']`, redirect ke `list.php`.
- `list.php`: tabel dirender dari `$_SESSION` lewat `foreach`; flash message lewat `$_SESSION['flash']`.
- `assets/js/buku.js`, `assets/js/anggota.js`, `assets/js/tabel.js`, dan folder `data/` dari Jobsheet 6 dihapus.

## Cara menjalankan
Dari dalam folder proyek:

    php -S localhost:8000

lalu buka http://localhost:8000/index.php (atau lewat Laragon).

## Catatan
Data di `$_SESSION` hilang saat sesi browser berakhir. Mulai Jobsheet 8 penyimpanan dipindah ke PostgreSQL.
