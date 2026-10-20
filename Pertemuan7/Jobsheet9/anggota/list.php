<?php
$page_title = "Daftar Anggota";
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
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM anggota WHERE (nama ILIKE :kw1 OR no_anggota ILIKE :kw2)");
    $hitung->execute(['kw1' => '%' . $keyword . '%', 'kw2' => '%' . $keyword . '%']);
    $totalRows = (int) $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE (nama ILIKE :kw1 OR no_anggota ILIKE :kw2) ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw1', '%' . $keyword . '%');
    $stmt->bindValue('kw2', '%' . $keyword . '%');
} else {
    $totalRows = (int) $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM anggota ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarAnggota = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Daftar Anggota</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <div class="search-box">
                <form method="get" action="list.php">
                    <span>
                        <label for="search-input">Cari Nama / No. Anggota</label>
                        <input type="text" id="search-input" name="q" value="<?php echo e($keyword); ?>" placeholder="Ketik nama atau no. anggota..." data-kolom="1,0">
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
            <p id="row-counter" class="row-counter" data-satuan="anggota"></p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">No. Anggota</th>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2">Alamat</th>
                            <th rowspan="2">No.HP</th>
                            <th colspan="2">Jenis Kelamin</th>
                            <th colspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th>Perempuan</th>
                            <th>Laki-Laki</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarAnggota)): ?>
                        <tr class="row-pesan">
                            <td colspan="8"><?php echo $keyword !== '' ? 'Tidak ada anggota yang cocok dengan pencarian.' : 'Belum ada data anggota. Silakan tambah lewat menu "Tambah Anggota".'; ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($daftarAnggota as $anggota): ?>
                        <tr>
                            <td><?php echo e($anggota['no_anggota']); ?></td>
                            <td><?php echo e($anggota['nama']); ?></td>
                            <td><?php echo e($anggota['alamat']); ?></td>
                            <td><?php echo e($anggota['no_hp']); ?></td>
                            <td><?php echo ($anggota['jenis_kelamin'] ?? '') === 'P' ? 'v' : ''; ?></td>
                            <td><?php echo ($anggota['jenis_kelamin'] ?? '') === 'L' ? 'v' : ''; ?></td>
                            <td><a href="edit.php?id=<?php echo (int) $anggota['id']; ?>" class="btn-edit">Edit</a></td>
                            <td>
                                <form class="form-hapus" method="post" action="hapus.php">
                                    <input type="hidden" name="id" value="<?php echo (int) $anggota['id']; ?>">
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
                            </td>
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
