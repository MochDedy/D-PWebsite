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

$page_title = "Login";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Login Petugas</h2>
            <?php if ($flash): ?>
            <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>
            <form class="auth-form" method="post" action="proses_login.php">
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" required>
                </p>
                <p>
                    <label for="password">Password</label><br>
                    <input type="password" id="password" name="password" required>
                </p>
                <p>
                    <label class="cek-ingat"><input type="checkbox" name="ingat" value="1"> Ingat saya selama 30 hari</label>
                </p>
                <p>
                    <button type="submit">Masuk</button>
                </p>
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
