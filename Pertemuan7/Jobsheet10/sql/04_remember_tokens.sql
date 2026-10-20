-- Latihan 10.2: token "Ingat Saya". Jalankan: psql -d simpus_mini -f sql/04_remember_tokens.sql
-- Yang disimpan hanya HASH dari token (bukan token aslinya), sama prinsipnya dengan password.
CREATE TABLE IF NOT EXISTS remember_tokens (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    selector VARCHAR(24) NOT NULL UNIQUE,
    token_hash CHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL
);
