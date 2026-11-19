<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'pengguna';
$judul_halaman = 'Tambah / Ubah Pengguna';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
  <h1 class="text-2xl font-extrabold text-slate-900">Tambah / Ubah Pengguna</h1>
  <p class="mt-2 text-sm text-slate-600">Kerangka form akun Admin, Guru BK, dan Siswa. Form belum menyimpan data.</p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
