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
?>
<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
  <h1 class="text-2xl font-extrabold text-slate-900">Profil &amp; Ganti Password</h1>
  <p class="mt-2 text-sm text-slate-600">Kerangka profil akun. Perubahan profil dan password belum tersedia.</p>
  <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Nama</dt><dd class="font-semibold"><?= e($pengguna['nama_lengkap']) ?></dd></div><div><dt class="text-slate-500">Username</dt><dd class="font-semibold"><?= e($pengguna['username']) ?></dd></div><div><dt class="text-slate-500">Peran</dt><dd class="font-semibold"><?= e($pengguna['role']) ?></dd></div><div><dt class="text-slate-500">Sekolah</dt><dd class="font-semibold"><?= e($pengguna['sekolah_nama'] ?? '—') ?></dd></div></dl>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
