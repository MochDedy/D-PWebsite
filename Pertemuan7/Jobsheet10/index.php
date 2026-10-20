<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';

require __DIR__ . '/includes/koneksi.php';

$totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
        <?php endif; ?>

        <section>
            <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
            <p>Aplikasi sederhana untuk mengelola data buku dan perpustakaan </p>
        </section>

        <section>
            <h2>Ringkasan</h2>
            <article>
                <h3>Total Buku</h3>
                <p><?php echo $totalBuku; ?></p>
            </article>
            <article>
                <h3>Total Anggota</h3>
                <p><?php echo $totalAnggota; ?></p>
            </article>
            <article>
                <h3>Sedang Dipinjam</h3>
                <p>3</p>
            </article>
            <article>
                <h3>Buku Terlambat</h3>
                <p>1</p>
            </article>
        </section>

        <?php if ($sudahLogin): ?>
        <section>
            <h2>Reset Data</h2>
            <form method="post" action="reset_data.php" onsubmit="return confirm('Hapus SEMUA data buku dan anggota di database? Tindakan ini tidak bisa dibatalkan.');">
                <button type="submit">Reset Data</button>
            </form>
        </section>
        <?php endif; ?>

        <section>
            <h2>Contoh Kode</h2>
            <div class="table-responsive">
                <pre>
                    function hitungTotalBuku(daftarBuku){
                        return daftarBuku.reduce((total, buku) => total + buku.stok, 0);
                    }
                </pre>
            </div>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
