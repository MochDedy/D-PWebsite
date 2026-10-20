-- Latihan tambahan Jobsheet 8: kolom tanggal_ditambahkan pada tabel buku
-- Jalankan: psql -d simpus_mini -f sql/02_tambah_tanggal_ditambahkan.sql
-- NOW() = waktu server database saat baris dibuat; TIMESTAMP = tipe tanggal + jam.
-- Baris lama otomatis diisi waktu saat perintah ini dijalankan.

ALTER TABLE buku
    ADD COLUMN IF NOT EXISTS tanggal_ditambahkan TIMESTAMP NOT NULL DEFAULT NOW();
