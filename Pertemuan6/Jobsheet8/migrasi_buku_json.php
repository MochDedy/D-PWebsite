<?php
// Latihan 4: pindahkan data/buku.json (Jobsheet 6) ke tabel buku.
// Jalankan sekali dari terminal:  php migrasi_buku_json.php [path/ke/buku.json]
require __DIR__ . '/includes/koneksi.php';

$file = $argv[1] ?? __DIR__ . '/data/buku.json';
if (!is_file($file)) {
    die("File tidak ditemukan: $file\n");
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) {
    die("Isi JSON tidak valid: " . json_last_error_msg() . "\n");
}
// Dukung dua bentuk: [ {...}, {...} ] atau { "buku": [ {...} ] }
if (isset($data['buku']) && is_array($data['buku'])) {
    $data = $data['buku'];
}

$cek = $pdo->prepare("SELECT 1 FROM buku WHERE judul = :judul AND pengarang = :pengarang");
$ins = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
    VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)"
);

$masuk = 0;
$lewat = 0;
$pdo->beginTransaction(); // semua berhasil, atau tidak ada yang masuk
try {
    foreach ($data as $i => $b) {
        $judul = trim($b['judul'] ?? '');
        $pengarang = trim($b['pengarang'] ?? '');
        if ($judul === '' || $pengarang === '') {
            echo "Baris " . ($i + 1) . " dilewati (judul/pengarang kosong).\n";
            $lewat++;
            continue;
        }
        // Hindari duplikat kalau skrip dijalankan dua kali
        $cek->execute(['judul' => $judul, 'pengarang' => $pengarang]);
        if ($cek->fetchColumn()) {
            $lewat++;
            continue;
        }
        $isbn = trim($b['isbn'] ?? '');
        $ins->execute([
            'judul' => $judul,
            'pengarang' => $pengarang,
            'tahun' => (int) ($b['tahun'] ?? 0),
            'isbn' => $isbn !== '' ? $isbn : null,
            'stok' => (int) ($b['stok'] ?? 0),
            'kategori' => trim($b['kategori'] ?? ''),
        ]);
        $masuk++;
    }
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Migrasi dibatalkan: " . $e->getMessage() . "\n");
}

echo "Selesai. Masuk: $masuk, dilewati: $lewat.\n";
