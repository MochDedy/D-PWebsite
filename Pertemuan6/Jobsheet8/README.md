# SIMPUS-Mini — Jobsheet 8 (Koneksi PostgreSQL)

## Perubahan dari Jobsheet 7
- Tambah `sql/01_buku_anggota.sql` — DDL tabel `buku` dan `anggota`.
- Tambah `includes/koneksi.php` — koneksi PDO driver `pgsql`.
- `buku/proses_tambah.php` & `anggota/proses_tambah.php`: `$_SESSION[...][] = ...` diganti `INSERT ... RETURNING id` via prepared statement.
- `buku/list.php` & `anggota/list.php`: `SELECT * FROM ... ORDER BY id DESC`.
- `index.php`: kartu Total Buku/Anggota dari `SELECT COUNT(*)`.
- `reset_data.php`: kini mengosongkan tabel di database (`TRUNCATE`).
- `debug_session.php` dihapus (data tidak lagi di `$_SESSION`).

## Persiapan database
1. PostgreSQL berjalan dan ekstensi PHP `pdo_pgsql` aktif.
2. `createdb -U postgres simpus_mini`
3. `psql -U postgres -d simpus_mini -f sql/01_buku_anggota.sql`
4. Sesuaikan `$user` / `$pass` di `includes/koneksi.php` bila perlu.

## Cara menjalankan
    php -S localhost:8000
lalu buka http://localhost:8000/index.php (atau lewat Laragon).

## Latihan tambahan (bab 7.4)
1. `anggota/proses_tambah.php`: `try/catch (PDOException)` — error UNIQUE (SQLSTATE 23505) jadi pesan flash rapi.
2. `sql/02_tambah_tanggal_ditambahkan.sql`: kolom `tanggal_ditambahkan TIMESTAMP DEFAULT NOW()`, ditampilkan di `buku/list.php`.
   Jalankan: `psql -U postgres -d simpus_mini -f sql/02_tambah_tanggal_ditambahkan.sql`
3. `buku/list.php`: pencarian server `WHERE judul ILIKE :keyword` lewat `?q=`.
4. `migrasi_buku_json.php`: `php migrasi_buku_json.php [path/buku.json]` (default `data/buku.json`).
