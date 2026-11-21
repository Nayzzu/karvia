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
    if (!preg_match('/^[a-z0-9._-]{3,50}$/', $nilai['username'])) $errors[] = 'Username harus 3–50 karakter: huruf kecil, angka, titik, garis bawah, atau tanda hubung.';
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
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f6f8f5] px-4 py-10 text-slate-900 antialiased">
  <main class="w-full max-w-lg">
    <a href="<?= e(url('index.php')) ?>" class="mb-6 block text-center text-xl font-extrabold tracking-tight text-[#173a24]">KARVIA <span class="text-sm font-medium text-slate-500">· Pendaftaran Siswa</span></a>
    <section class="rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm sm:p-8">
      <h1 class="text-xl font-bold text-[#203628]">Buat akun siswa</h1>
      <p class="mt-1 text-sm text-slate-500">Pendaftaran terbuka untuk siswa sekolah yang terdaftar di KARVIA.</p>
      <?php if ($errors): ?>
        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900" role="alert"><ul class="list-inside list-disc space-y-1"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <form method="post" class="mt-6 space-y-4">
        <?= csrf_field() ?>
        <div><label for="nama_lengkap" class="mb-1 block text-sm font-semibold text-slate-700">Nama lengkap *</label><input id="nama_lengkap" name="nama_lengkap" required minlength="2" maxlength="150" value="<?= e($nilai['nama']) ?>" autocomplete="name" class="w-full rounded-xl border border-[#dfe6de] px-3.5 py-3 text-sm focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30"></div>
        <div><label for="sekolah_id" class="mb-1 block text-sm font-semibold text-slate-700">Sekolah *</label><select id="sekolah_id" name="sekolah_id" required class="w-full rounded-xl border border-[#dfe6de] bg-white px-3.5 py-3 text-sm focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30"><option value="">Pilih sekolah</option><?php foreach ($sekolah as $s): ?><option value="<?= (int) $s['id'] ?>" <?= $nilai['sekolah_id'] === (string) $s['id'] ? 'selected' : '' ?>><?= e($s['nama']) ?><?= $s['kota'] ? ' — ' . e($s['kota']) : '' ?></option><?php endforeach; ?></select></div>
        <div><label for="kelas" class="mb-1 block text-sm font-semibold text-slate-700">Kelas *</label><input id="kelas" name="kelas" required maxlength="20" value="<?= e($nilai['kelas']) ?>" placeholder="Contoh: XI IPA 1" class="w-full rounded-xl border border-[#dfe6de] px-3.5 py-3 text-sm focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30"></div>
        <div><label for="username" class="mb-1 block text-sm font-semibold text-slate-700">Username *</label><input id="username" name="username" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]{3,50}" value="<?= e($nilai['username']) ?>" autocomplete="username" class="w-full rounded-xl border border-[#dfe6de] px-3.5 py-3 text-sm focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30"><p class="mt-1 text-xs text-slate-500">3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.</p></div>
        <div><label for="password" class="mb-1 block text-sm font-semibold text-slate-700">Password *</label><input id="password" name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-[#dfe6de] px-3.5 py-3 text-sm focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30"><p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p></div>
        <div><label for="konfirmasi_password" class="mb-1 block text-sm font-semibold text-slate-700">Konfirmasi password *</label><input id="konfirmasi_password" name="konfirmasi_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-[#dfe6de] px-3.5 py-3 text-sm focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30"></div>
        <button type="submit" class="w-full rounded-xl bg-[#224f35] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-[#1d482d] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2">Daftar dan lanjutkan</button>
      </form>
      <p class="mt-5 text-center text-sm text-slate-500">Sudah punya akun? <a class="font-bold text-[#245938] hover:underline" href="<?= e(url('login.php')) ?>">Masuk</a></p>
    </section>
  </main>
</body>
</html>
