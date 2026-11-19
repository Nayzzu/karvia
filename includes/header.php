<?php
if (!defined('KARVIA_APP')) {
    define('KARVIA_APP', true);
}
require_once __DIR__ . '/function.php';

if (!isset($peran_halaman) || !in_array($peran_halaman, ['admin', 'guru_bk', 'siswa'], true)) {
    http_response_code(500);
    exit('Konfigurasi peran halaman tidak valid.');
}
auth_cek_peran($peran_halaman);
$pengguna_aktif = auth_pengguna();

$menu_peran = [
    'admin' => [
        ['index', 'Dashboard', 'admin/index.php'],
        ['sekolah', 'Sekolah', 'admin/sekolah.php'],
        ['pengguna', 'Pengguna', 'admin/pengguna.php'],
        ['rekap', 'Rekap', 'admin/rekap.php'],
    ],
    'guru_bk' => [
        ['index', 'Dashboard', 'guru_bk/index.php'],
        ['catatan', 'Catatan Siswa', 'guru_bk/catatan.php'],
    ],
    'siswa' => [
        ['index', 'Beranda', 'siswa/index.php'],
        ['kenali_diri', 'Kenali Diriku', 'siswa/kenali_diri.php'],
        ['profesi', 'Jelajah Profesi', 'siswa/profesi.php'],
        ['jalur', 'Jalur Setelah Lulus', 'siswa/jalur.php'],
        ['bandingkan', 'Bandingkan Pilihan', 'siswa/bandingkan.php'],
        ['rencana', 'Rencana Langkahku', 'siswa/rencana.php'],
        ['jurnal', 'Jurnal Refleksi', 'siswa/jurnal.php'],
        ['konsultasi', 'Ruang Konsultasi BK', 'siswa/konsultasi.php'],
        ['tips', 'Tips & Edukasi', 'siswa/tips.php'],
    ],
];
$prefix = ($peran_halaman === 'admin' ? '' : '../');
$judul_halaman = $judul_halaman ?? 'KARVIA';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($judul_halaman) ?> — KARVIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{karvia:{50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
  <header class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="<?= e(url($peran_halaman === 'admin' ? 'admin/index.php' : ($peran_halaman === 'guru_bk' ? 'guru_bk/index.php' : 'siswa/index.php'))) ?>" class="font-extrabold text-karvia-800"><i class="fa-solid fa-compass mr-2"></i>KARVIA</a>
      <div class="flex items-center gap-4 text-sm"><span><?= e($pengguna_aktif['nama_lengkap'] ?? '') ?></span>
        <form method="post" action="<?= e(url('logout.php')) ?>" class="inline"><?= csrf_field() ?><button class="text-rose-700 hover:underline" type="submit">Keluar</button></form>
      </div>
    </div>
  </header>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:grid lg:grid-cols-[250px_minmax(0,1fr)] lg:gap-6">
    <aside class="mb-6 lg:mb-0">
      <nav class="rounded-2xl border border-slate-200 bg-white p-3" aria-label="Navigasi utama">
        <p class="px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-400"><?= e(str_replace('_', ' ', $peran_halaman)) ?></p>
        <?php foreach ($menu_peran[$peran_halaman] as [$kunci, $label, $tujuan]): ?>
          <a href="<?= e(url($tujuan)) ?>" class="block rounded-xl px-3 py-2.5 text-sm <?= ($halaman_aktif ?? '') === $kunci ? 'bg-emerald-50 font-semibold text-emerald-800' : 'text-slate-600 hover:bg-slate-50' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
        <a href="<?= e(url('profil.php')) ?>" class="mt-2 block rounded-xl px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Profil &amp; Password</a>
      </nav>
    </aside>
    <main class="min-w-0">
      <?php flash_tampil(); ?>
