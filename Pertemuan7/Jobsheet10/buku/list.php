<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require __DIR__ . '/../includes/koneksi.php';

// ===== Pagination + pencarian server-side =====
$perPageOptions = [5, 10, 20];
$perPage = (int) ($_GET['per_page'] ?? 5);
if (!in_array($perPage, $perPageOptions, true)) {
    $perPage = 5;
}
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE (judul ILIKE :kw1 OR pengarang ILIKE :kw2)");
    $hitung->execute(['kw1' => '%' . $keyword . '%', 'kw2' => '%' . $keyword . '%']);
    $totalRows = (int) $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM buku WHERE (judul ILIKE :kw1 OR pengarang ILIKE :kw2) ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw1', '%' . $keyword . '%');
    $stmt->bindValue('kw2', '%' . $keyword . '%');
} else {
    $totalRows = (int) $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM buku ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Daftar Buku</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <div class="search-box">
                <form method="get" action="list.php">
                    <span>
                        <label for="search-input">Cari Judul / Pengarang</label>
                        <input type="text" id="search-input" name="q" value="<?php echo e($keyword); ?>" placeholder="Ketik judul atau pengarang..." data-kolom="0,1">
                    </span>
                    <span>
                        <label for="per-page">Baris/halaman</label>
                        <select id="per-page" name="per_page">
                            <?php foreach ($perPageOptions as $opt): ?>
                            <option value="<?php echo $opt; ?>" <?php echo $opt === $perPage ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </span>
                    <button type="submit">Cari</button>
                </form>
            </div>
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
                            <?php if ($sudahLogin): ?>
                            <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarBuku)): ?>
                        <tr class="row-pesan">
                            <td colspan="<?php echo $sudahLogin ? 6 : 5; ?>"><?php echo $keyword !== '' ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".'; ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td><?php echo e($buku['judul']); ?></td>
                            <td><?php echo e($buku['pengarang']); ?></td>
                            <td><?php echo e($buku['tahun']); ?></td>
                            <td><?php echo e($buku['stok']); ?></td>
                            <td><?php echo e(ucfirst((string) $buku['kategori'])); ?></td>
                            <?php if ($sudahLogin): ?>
                            <td>
                                <a href="edit.php?id=<?php echo (int) $buku['id']; ?>" class="btn-edit">Edit</a>
                                <form class="form-hapus" method="post" action="hapus.php">
                                    <input type="hidden" name="id" value="<?php echo (int) $buku['id']; ?>">
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="list.php?page=<?php echo $i; ?>&per_page=<?php echo $perPage; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                   class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </nav>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
