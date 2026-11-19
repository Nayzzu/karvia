<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'rekap';
$judul_halaman = 'Rekap';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
  <h1 class="text-2xl font-extrabold text-slate-900">Rekap KARVIA</h1>
  <p class="mt-2 text-sm text-slate-600">Kerangka halaman rekap sekolah dan pengguna. Data rekap belum ditampilkan.</p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
