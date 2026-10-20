<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Latihan 10.2: halaman publik juga perlu tahu kalau pengunjung punya cookie "Ingat Saya"
require_once __DIR__ . '/remember.php';
remember_login_otomatis();

$sudahLogin = isset($_SESSION['user_id']);
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Hitung prefix path relatif ($base) dari kedalaman folder halaman yang sedang dibuka
$__jobsheetRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$__scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])), '/');
$__rel = trim(substr($__scriptDir, strlen($__jobsheetRoot)), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

// Helper: escape teks sebelum dicetak ke HTML (mencegah XSS dari input pengguna)
if (!function_exists('e')) {
    function e($teks)
    {
        return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPUS-Mini<?php echo isset($page_title) ? ' | ' . e($page_title) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1>SIMPUS-Mini</h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="<?php echo $base; ?>index.php">Beranda</a></li>
                <li><a href="<?php echo $base; ?>buku/list.php">Daftar Buku</a></li>
                <?php if ($sudahLogin): ?>
                <li><a href="<?php echo $base; ?>buku/tambah.php">Tambah Buku</a></li>
                <li><a href="<?php echo $base; ?>anggota/list.php">Daftar Anggota</a></li>
                <li><a href="<?php echo $base; ?>anggota/tambah.php">Tambah Anggota</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="auth-status">
            <?php if ($sudahLogin): ?>
            <span><?php echo e($_SESSION['nama'] ?? ''); ?></span>
            <a href="<?php echo $base; ?>auth/logout.php">Logout</a>
            <?php else: ?>
            <a href="<?php echo $base; ?>auth/login.php">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
