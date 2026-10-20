<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/remember.php';
remember_login_otomatis();
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Registrasi Petugas";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Registrasi Petugas</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <form class="auth-form" method="post" action="proses_register.php">
                <p>
                    <label for="nama">Nama Lengkap</label><br>
                    <input type="text" id="nama" name="nama" required>
                </p>
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" maxlength="50" required>
                </p>
                <p>
                    <label for="password">Password <span>(min. 6 karakter)</span></label><br>
                    <input type="password" id="password" name="password" minlength="6" required>
                </p>
                <p>
                    <button type="submit">Daftar</button>
                </p>
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
