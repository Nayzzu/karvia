<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'catatan';
$judul_halaman = 'Detail Catatan Siswa';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
  <h1 class="text-2xl font-extrabold text-slate-900">Detail Catatan Siswa</h1>
  <p class="mt-2 text-sm text-slate-600">Kerangka detail catatan dan tindak lanjut Guru BK. Data siswa belum dimuat dan belum dapat disimpan.</p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
