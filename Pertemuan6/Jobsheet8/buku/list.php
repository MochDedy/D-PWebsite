<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require __DIR__ . '/../includes/koneksi.php';

// Latihan 3: pencarian di sisi server (ILIKE = LIKE tanpa membedakan huruf besar/kecil)
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul ILIKE :keyword ORDER BY id DESC");
    $stmt->execute(['keyword' => '%' . $q . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM buku ORDER BY id DESC");
}
$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Buku</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <form method="get" action="list.php" class="search-box">
                <label for="search-input">Cari Judul Buku</label>
                <input type="text" id="search-input" name="q" value="<?php echo e($q); ?>" placeholder="Ketik judul buku..." data-kolom="0">
                <button type="submit">Cari</button>
                <?php if ($q !== ''): ?><a href="list.php">Reset</a><?php endif; ?>
            </form>
            <p id="row-counter" class="row-counter" data-satuan="buku"></p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Ditambahkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarBuku)): ?>
                        <tr class="row-pesan">
                            <td colspan="7"><?php echo $q !== '' ? 'Tidak ada buku dengan judul "' . e($q) . '".' : 'Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".'; ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td><?php echo e($buku['judul']); ?></td>
                            <td><?php echo e($buku['pengarang']); ?></td>
                            <td><?php echo e($buku['tahun']); ?></td>
                            <td><?php echo e($buku['stok']); ?></td>
                            <td><?php echo e(ucfirst((string) $buku['kategori'])); ?></td>
                            <td><?php echo e(date('d/m/Y H:i', strtotime($buku['tanggal_ditambahkan']))); ?></td>
                            <td>
                                <button type="button" class="btn-edit">Edit</button>
                                <button type="button" class="btn-detail">Detail</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
