<?php
$page_title = "Edit Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM anggota WHERE id = :id");
$stmt->execute(['id' => $id]);
$anggota = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$anggota) {
    header('Location: list.php');
    exit;
}

// Format nilai untuk input datetime-local: YYYY-MM-DDTHH:MM
$tglLahir = $anggota['tanggal_lahir'] ? date('Y-m-d\TH:i', strtotime($anggota['tanggal_lahir'])) : '';
?>
        <section>
            <span>* (Wajib diisi)</span>
            <h2>Edit Anggota</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <form id="form-tambah" class="form-edit" method="post" action="proses_edit.php" novalidate>
                <input type="hidden" name="id" value="<?php echo (int) $anggota['id']; ?>">
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama_lengkap" value="<?php echo e($anggota['nama']); ?>" required>
                </p>
                <p>
                    <label for="email">Email</label><br>
                    <input type="email" id="email" name="email" value="<?php echo e($anggota['email']); ?>" required>
                </p>
                <p>
                    <label for="no_anggota">No. Anggota</label><br>
                    <input type="text" id="no_anggota" name="no_anggota" value="<?php echo e($anggota['no_anggota']); ?>" required>
                </p>
                <p>
                    <label for="tanggal_lahir">Tanggal Lahir</label><br>
                    <input type="datetime-local" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo e($tglLahir); ?>" required>
                </p>
                <p>
                    <span>Jenis Kelamin</span><br>
                    <label><input type="radio" name="jenis_kelamin" value="P" <?php echo ($anggota['jenis_kelamin'] ?? '') === 'P' ? 'checked' : ''; ?>> Perempuan</label>
                    <label><input type="radio" name="jenis_kelamin" value="L" <?php echo ($anggota['jenis_kelamin'] ?? '') === 'L' ? 'checked' : ''; ?>> Laki-Laki</label>
                </p>
                <p>
                    <label for="alamat">Alamat</label><br>
                    <textarea id="alamat" rows="10" cols="30" name="alamat"><?php echo e($anggota['alamat']); ?></textarea>
                </p>
                <p>
                    <label for="no_hp">No. HP</label><br>
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo e($anggota['no_hp']); ?>">
                </p>
                <p>
                    <button type="submit">Simpan Perubahan</button>
                    <a href="list.php">Batal</a>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
