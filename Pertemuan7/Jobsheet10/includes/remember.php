<?php
// Latihan 10.2: "Ingat Saya".
// Cookie berisi "selector:validator". Selector (acak) dipakai mencari baris di DB,
// validator (acak) dibandingkan dengan HASH-nya. Jadi kalau tabel bocor, token tidak bisa dipakai langsung.

const REMEMBER_COOKIE = 'remember';
const REMEMBER_HARI = 30;

function remember_set_cookie($nilai, $kadaluarsa)
{
    setcookie(REMEMBER_COOKIE, $nilai, [
        'expires'  => $kadaluarsa,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,   // tidak bisa dibaca JavaScript (mengurangi dampak XSS)
        'samesite' => 'Lax',
    ]);
}

function remember_buat($pdo, $userId)
{
    $pdo->exec("DELETE FROM remember_tokens WHERE expires_at < NOW()");

    $selector = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(32));
    $kadaluarsa = time() + REMEMBER_HARI * 86400;

    $stmt = $pdo->prepare(
        "INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
         VALUES (:uid, :sel, :hash, to_timestamp(:exp))"
    );
    $stmt->execute([
        'uid' => $userId,
        'sel' => $selector,
        'hash' => hash('sha256', $validator),
        'exp' => $kadaluarsa,
    ]);
    remember_set_cookie($selector . ':' . $validator, $kadaluarsa);
}

function remember_hapus($pdo)
{
    $cookie = $_COOKIE[REMEMBER_COOKIE] ?? '';
    $bagian = explode(':', $cookie, 2);
    if ($bagian[0] !== '') {
        $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE selector = :sel");
        $stmt->execute(['sel' => $bagian[0]]);
    }
    remember_set_cookie('', time() - 3600);
    unset($_COOKIE[REMEMBER_COOKIE]);
}

// Dipanggil sebelum pemeriksaan login. Database hanya disentuh kalau ada cookie
// DAN belum ada sesi, jadi pengunjung tanpa cookie tidak butuh database sama sekali.
function remember_login_otomatis()
{
    if (isset($_SESSION['user_id']) || empty($_COOKIE[REMEMBER_COOKIE])) {
        return;
    }

    $bagian = explode(':', $_COOKIE[REMEMBER_COOKIE], 2);
    if (count($bagian) !== 2 || $bagian[0] === '' || $bagian[1] === '') {
        remember_set_cookie('', time() - 3600);
        return;
    }
    [$selector, $validator] = $bagian;

    require __DIR__ . '/koneksi.php'; // $pdo lokal fungsi ini

    $stmt = $pdo->prepare(
        "SELECT t.id, t.token_hash, u.id AS uid, u.nama, u.role
         FROM remember_tokens t JOIN users u ON u.id = t.user_id
         WHERE t.selector = :sel AND t.expires_at > NOW()"
    );
    $stmt->execute(['sel' => $selector]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !hash_equals($row['token_hash'], hash('sha256', $validator))) {
        remember_set_cookie('', time() - 3600);
        return;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $row['uid'];
    $_SESSION['nama'] = $row['nama'];
    $_SESSION['role'] = $row['role'];

    // Rotasi: token lama dibuang, diganti token baru (token curian jadi cepat basi)
    $pdo->prepare("DELETE FROM remember_tokens WHERE id = :id")->execute(['id' => $row['id']]);
    remember_buat($pdo, $row['uid']);
}
