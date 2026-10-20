<?php
// SEMENTARA (latihan 7.4 no. 3): hapus file ini setelah selesai belajar
$page_title = "Debug Session";
include __DIR__ . '/includes/header.php';
?>
        <section>
            <h2>Isi $_SESSION</h2>
            <pre><?php echo e(print_r($_SESSION, true)); ?></pre>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
