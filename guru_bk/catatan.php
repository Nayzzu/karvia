<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'catatan';
$judul_halaman = 'Catatan Siswa';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
  <h1 class="text-2xl font-extrabold text-slate-900">Catatan Siswa</h1>
  <p class="mt-2 text-sm text-slate-600">Kerangka daftar catatan yang dibagikan siswa di sekolah Anda. Filter dan tindak lanjut belum tersedia.</p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
