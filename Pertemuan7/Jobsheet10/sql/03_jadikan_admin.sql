-- Latihan 10.1: register selalu membuat role 'petugas'.
-- Untuk membuat admin, daftar akun lewat halaman Register dulu, lalu jalankan
-- (ganti 'usernameku' dengan username akun tersebut):
--   psql -d simpus_mini -c "UPDATE users SET role = 'admin' WHERE username = 'usernameku'"
--
-- Catatan: role disimpan di $_SESSION saat login, jadi akun harus Logout lalu
-- Login ulang agar perubahan role berlaku.
UPDATE users SET role = 'admin' WHERE username = 'GANTI_USERNAME';
