<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';
$pengguna = auth_muat_ulang();
if (!$pengguna) {
    flash_set('peringatan', 'Silakan masuk untuk melanjutkan.');
    redirect('login.php');
}
$peran_halaman = $pengguna['role'];
$halaman_aktif = 'profil';
$judul_halaman = 'Profil & Ganti Password';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$id = (int) $pengguna['id'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $aksi = (string) ($_POST['aksi'] ?? '');
    if ($aksi === 'profil') {
        $nama = trim((string) ($_POST['nama_lengkap'] ?? ''));
        $telepon = trim((string) ($_POST['no_telepon'] ?? ''));
        if (mb_strlen($nama) < 2 || mb_strlen($nama) > 150) $errors[] = 'Nama wajib diisi (2–150 karakter).';
        if (mb_strlen($telepon) > 20) $errors[] = 'Nomor telepon maksimal 20 karakter.';
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE pengguna SET nama_lengkap = :nama, no_telepon = :telepon, diperbarui_pada = NOW() WHERE id = :id');
            $stmt->execute(['nama' => $nama, 'telepon' => $telepon ?: null, 'id' => $id]);
            $_SESSION['pengguna']['nama_lengkap'] = $nama;
            flash_set('sukses', 'Profil berhasil diperbarui.');
            redirect('profil.php');
        }
    } elseif ($aksi === 'password') {
        $lama = (string) ($_POST['password_lama'] ?? '');
        $baru = (string) ($_POST['password_baru'] ?? '');
        $konfirmasi = (string) ($_POST['konfirmasi_password'] ?? '');
        $stmt = $pdo->prepare('SELECT password_hash FROM pengguna WHERE id = :id AND status = \'aktif\'');
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();
        if (!$hash || !password_verify($lama, $hash)) $errors[] = 'Password saat ini tidak sesuai.';
        if (strlen($baru) < 8 || strlen($baru) > 72) $errors[] = 'Password baru harus 8–72 karakter.';
        if (!hash_equals($baru, $konfirmasi)) $errors[] = 'Konfirmasi password tidak sama.';
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE pengguna SET password_hash = :hash, diperbarui_pada = NOW() WHERE id = :id');
            $stmt->execute(['hash' => password_hash($baru, PASSWORD_DEFAULT), 'id' => $id]);
            flash_set('sukses', 'Password berhasil diganti. Gunakan password baru saat masuk berikutnya.');
            redirect('profil.php');
        }
    } else {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
}
$stmt = $pdo->prepare('SELECT nama_lengkap, username, role, sekolah_id, kelas, no_telepon FROM pengguna WHERE id = :id');
$stmt->execute(['id' => $id]);
$profil = $stmt->fetch();
?>
<div class="mb-6"><h1 class="text-2xl font-extrabold">Profil &amp; Ganti Password</h1><p class="mt-1 text-sm text-slate-600">Perbarui nama dan nomor telepon. Peran, sekolah, kelas, dan username dikelola Admin.</p></div>
<?php if ($errors): ?><div role="alert" class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<div class="grid max-w-4xl gap-5">
<section class="rounded-2xl border border-slate-200 bg-white p-6"><h2 class="text-lg font-bold">Data Profil</h2><dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Username</dt><dd class="font-semibold"><?= e($profil['username']) ?></dd></div><div><dt class="text-slate-500">Peran</dt><dd class="font-semibold"><?= e($profil['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($profil['role'])) ?></dd></div><div><dt class="text-slate-500">Sekolah</dt><dd class="font-semibold"><?= e($pengguna['sekolah_nama'] ?? 'Admin global') ?></dd></div><?php if ($profil['role'] === 'siswa'): ?><div><dt class="text-slate-500">Kelas</dt><dd class="font-semibold"><?= e($profil['kelas'] ?: '—') ?></dd></div><?php endif; ?></dl>
<form method="post" class="mt-5 space-y-4 border-t border-slate-100 pt-5"><?= csrf_field() ?><input type="hidden" name="aksi" value="profil"><div><label for="nama_lengkap" class="mb-1 block text-sm font-semibold">Nama lengkap</label><input id="nama_lengkap" name="nama_lengkap" required maxlength="150" value="<?= e($profil['nama_lengkap']) ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2"></div><div><label for="no_telepon" class="mb-1 block text-sm font-semibold">Nomor telepon (opsional)</label><input id="no_telepon" name="no_telepon" maxlength="20" value="<?= e($profil['no_telepon']) ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2"></div><button class="rounded-lg bg-emerald-700 px-4 py-2 font-bold text-white">Simpan Profil</button></form></section>
<section class="rounded-2xl border border-slate-200 bg-white p-6"><h2 class="text-lg font-bold">Ganti Password</h2><form method="post" class="mt-4 space-y-4"><?= csrf_field() ?><input type="hidden" name="aksi" value="password"><div><label for="password_lama" class="mb-1 block text-sm font-semibold">Password saat ini</label><input id="password_lama" name="password_lama" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-300 px-3 py-2"></div><div><label for="password_baru" class="mb-1 block text-sm font-semibold">Password baru</label><input id="password_baru" name="password_baru" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2"><p class="mt-1 text-xs text-slate-500">8–72 karakter.</p></div><div><label for="konfirmasi_password" class="mb-1 block text-sm font-semibold">Ulangi password baru</label><input id="konfirmasi_password" name="konfirmasi_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2"></div><button class="rounded-lg bg-slate-800 px-4 py-2 font-bold text-white">Ganti Password</button></form></section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
