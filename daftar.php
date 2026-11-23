<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';

if (auth_pengguna()) {
    $akun = auth_muat_ulang();
    if ($akun) auth_arahkan_dashboard($akun);
}

$pdo = db();
$sekolah = $pdo->query("SELECT id, nama, kota FROM sekolah WHERE status = 'aktif' ORDER BY nama")->fetchAll();
$errors = [];
$nilai = ['nama' => '', 'sekolah_id' => '', 'kelas' => '', 'username' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $nilai['nama'] = trim((string) ($_POST['nama_lengkap'] ?? ''));
    $nilai['sekolah_id'] = (string) ($_POST['sekolah_id'] ?? '');
    $nilai['kelas'] = trim((string) ($_POST['kelas'] ?? ''));
    $nilai['username'] = strtolower(trim((string) ($_POST['username'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $konfirmasi = (string) ($_POST['konfirmasi_password'] ?? '');

    if (mb_strlen($nilai['nama']) < 2 || mb_strlen($nilai['nama']) > 150) $errors[] = 'Nama lengkap wajib diisi (2–150 karakter).';
    if (!ctype_digit($nilai['sekolah_id']) || (int) $nilai['sekolah_id'] < 1) $errors[] = 'Pilih sekolah yang tersedia.';
    if (mb_strlen($nilai['kelas']) < 1 || mb_strlen($nilai['kelas']) > 20) $errors[] = 'Kelas wajib diisi (maksimal 20 karakter).';
    if (!username_valid($nilai['username'])) $errors[] = 'Username harus 3–50 karakter: huruf kecil, angka, titik, garis bawah, atau tanda hubung.';
    if (strlen($password) < 8 || strlen($password) > 72) $errors[] = 'Password harus 8–72 karakter.';
    if (!hash_equals($password, $konfirmasi)) $errors[] = 'Konfirmasi password tidak sama.';

    if (!$errors) {
        $cek = $pdo->prepare("SELECT id FROM sekolah WHERE id = :id AND status = 'aktif'");
        $cek->execute(['id' => (int) $nilai['sekolah_id']]);
        if (!$cek->fetchColumn()) $errors[] = 'Sekolah tidak ditemukan atau sudah nonaktif.';
        $cek = $pdo->prepare('SELECT id FROM pengguna WHERE username = :username');
        $cek->execute(['username' => $nilai['username']]);
        if ($cek->fetchColumn()) $errors[] = 'Username sudah digunakan. Pilih username lain.';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO pengguna (sekolah_id, nama_lengkap, username, password_hash, role, status, kelas, dibuat_oleh, dibuat_pada) VALUES (:sekolah_id, :nama, :username, :password_hash, 'siswa', 'aktif', :kelas, NULL, NOW())");
            $stmt->execute([
                'sekolah_id' => (int) $nilai['sekolah_id'],
                'nama' => $nilai['nama'],
                'username' => $nilai['username'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'kelas' => $nilai['kelas'],
            ]);
            $hasil = auth_login($nilai['username'], $password);
            if ($hasil['berhasil']) auth_arahkan_dashboard(auth_pengguna());
            $errors[] = 'Akun berhasil dibuat, tetapi login gagal. Silakan masuk melalui halaman login.';
        } catch (PDOException $kesalahan) {
            if ($kesalahan->getCode() === '23000') $errors[] = 'Username atau sekolah tidak tersedia. Periksa kembali data Anda.';
            else throw $kesalahan;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#224f35">
  <title>Daftar Akun Siswa — KARVIA</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('assets/css/auth.css')) ?>">
</head>
<body class="auth-page auth-register">
  <main class="auth-layout">
    <aside class="auth-aside">
      <a href="<?= e(url('index.php')) ?>" class="auth-brand" aria-label="KARVIA, kembali ke beranda">
        <span class="auth-brand-mark" aria-hidden="true">K</span>
        <span><span class="auth-brand-name">KARVIA</span><span class="auth-brand-tagline">Career Vision &amp; Action</span></span>
      </a>
      <div class="auth-aside-copy">
        <p class="auth-eyebrow">Mulai langkahmu</p>
        <h2>Temukan arah.<br>Tumbuhkan potensi.</h2>
        <p>Buat akun siswa untuk mulai mengenali minat dan merancang langkah karier bersama.</p>
      </div>
      <p class="auth-aside-foot">Pendaftaran mandiri khusus siswa<br>UIN Raden Intan Lampung · Chindy Theresia</p>
    </aside>

    <section class="auth-main" aria-labelledby="auth-title">
      <div class="auth-form-wrap">
        <header class="auth-heading">
          <h1 id="auth-title">Buat akun siswa</h1>
          <p>Pendaftaran terbuka untuk siswa sekolah yang terdaftar di KARVIA.</p>
        </header>
        <?php if ($errors): ?>
          <div class="auth-alert" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="post" action="<?= e(url('daftar.php')) ?>">
          <?= csrf_field() ?>
          <div class="auth-fields">
            <div class="auth-field"><label for="nama_lengkap">Nama lengkap *</label><input id="nama_lengkap" name="nama_lengkap" required minlength="2" maxlength="150" value="<?= e($nilai['nama']) ?>" autocomplete="name"></div>
            <div class="auth-field"><label for="sekolah_id">Sekolah *</label><select id="sekolah_id" name="sekolah_id" required><option value="">Pilih sekolah</option><?php foreach ($sekolah as $s): ?><option value="<?= (int) $s['id'] ?>" <?= $nilai['sekolah_id'] === (string) $s['id'] ? 'selected' : '' ?>><?= e($s['nama']) ?><?= $s['kota'] ? ' — ' . e($s['kota']) : '' ?></option><?php endforeach; ?></select></div>
            <div class="auth-field"><label for="kelas">Kelas *</label><input id="kelas" name="kelas" required maxlength="20" value="<?= e($nilai['kelas']) ?>" placeholder="Contoh: XI IPA 1"></div>
            <div class="auth-field"><label for="username">Username *</label><input id="username" name="username" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]{3,50}" value="<?= e($nilai['username']) ?>" autocomplete="username"><p class="auth-help">3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.</p></div>
            <div class="auth-field"><label for="password">Password *</label><input id="password" name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password"><p class="auth-help">Gunakan 8–72 karakter.</p></div>
            <div class="auth-field"><label for="konfirmasi_password">Konfirmasi password *</label><input id="konfirmasi_password" name="konfirmasi_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password"></div>
          </div>
          <button type="submit" class="auth-submit">Daftar dan lanjutkan <span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-switch-link">Sudah punya akun? <a class="auth-link" href="<?= e(url('login.php')) ?>">Masuk</a></p>
        <a class="auth-link auth-back" href="<?= e(url('index.php')) ?>">Kembali ke beranda</a>
      </div>
    </section>
  </main>
</body>
</html>
