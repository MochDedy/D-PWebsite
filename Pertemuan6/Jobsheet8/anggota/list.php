<?php
$page_title = "Daftar Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require __DIR__ . '/../includes/koneksi.php';

$daftarAnggota = $pdo->query("SELECT * FROM anggota ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Anggota</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <div class="search-box">
                <label for="search-input">Cari Nama Anggota</label>
                <input type="text" id="search-input" placeholder="Ketik nama anggota..." data-kolom="1">
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
                            <td colspan="8">Belum ada data anggota. Silakan tambah lewat menu "Tambah Anggota".</td>
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
                            <td><button type="button" class="btn-edit">Edit</button></td>
                            <td><button type="button" class="btn-hapus">Hapus</button></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
