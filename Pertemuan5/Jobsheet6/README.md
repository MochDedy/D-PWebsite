# SIMPUS-Mini — Jobsheet 6 (Fetch API & JSON)

## Perubahan dari Jobsheet 5
- Tambah `data/buku.json` (10 objek) dan `data/anggota.json` (4 objek).
- `buku/list.html` & `anggota/list.html`: `<tbody>` dikosongkan, baris dirender oleh `assets/js/buku.js` / `anggota.js` (fetch + async/await).
- Loading indicator `#loading-indicator` (delay simulasi 600 ms) dan error handling `try/catch/finally`.
- `app.js`: `initHapusConfirm` memakai event delegation; counter & filter mengabaikan baris pesan.

## Cara menjalankan
`fetch()` tidak jalan lewat `file://`. Gunakan server lokal:

    php -S localhost:8000

lalu buka http://localhost:8000/index.html (atau Live Server / Laragon).
