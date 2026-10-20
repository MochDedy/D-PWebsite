<?php
$page_title = "Tambah Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <span>* (Wajib diisi)</span>
            <h2>Tambah Anggota</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <form id="form-tambah" method="post" action="proses_tambah.php" novalidate>
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama_lengkap" required>
                </p>
                <p>
                    <label for="email">Email</label><br>
                    <input type="email" id="email" name="email" required>
                </p>
                <p>
                    <label for="no_anggota">No. Anggota</label><br>
                    <input type="text" id="no_anggota" name="no_anggota" required>
                </p>
                <p>
                    <label for="tanggal_lahir">Tanggal Lahir</label><br>
                    <input type="datetime-local" id="tanggal_lahir" name="tanggal_lahir" required>
                </p>
                <p>
                    <span>Jenis Kelamin</span><br>
                    <label><input type="radio" name="jenis_kelamin" value="P"> Perempuan</label>
                    <label><input type="radio" name="jenis_kelamin" value="L"> Laki-Laki</label>
                </p>
                <p>
                    <label for="alamat">Alamat</label><br>
                    <textarea id="alamat" rows="10" cols="30" name="alamat"></textarea>
                </p>
                <p>
                    <label for="no_hp">No. HP</label><br>
                    <input type="text" id="no_hp" name="no_hp">
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
