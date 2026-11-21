<?php
// Buffer output agar redirect()/header() tetap bisa dipanggil setelah blok HTML tercetak.
ob_start();
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
        ['index', 'Dashboard', 'admin/index.php', 'home'],
        ['sekolah', 'Sekolah', 'admin/sekolah.php', 'school'],
        ['pengguna', 'Pengguna', 'admin/pengguna.php', 'users'],
        ['rekap', 'Rekap Sistem', 'admin/rekap.php', 'chart'],
    ],
    'guru_bk' => [
        ['index', 'Dashboard', 'guru_bk/index.php', 'home'],
        ['catatan', 'Catatan Siswa', 'guru_bk/catatan.php', 'file'],
        ['artikel', 'Artikel Edukasi', 'guru_bk/artikel.php', 'book'],
    ],
    'siswa' => [
        ['index', 'Beranda', 'siswa/index.php', 'home'],
        ['kenali_diri', 'Kenali Diriku', 'siswa/kenali_diri.php', 'users'],
        ['profesi', 'Jelajah Profesi', 'siswa/profesi.php', 'compass'],
        ['jalur', 'Jalur Setelah Lulus', 'siswa/jalur.php', 'map'],
        ['bandingkan', 'Bandingkan Pilihan', 'siswa/bandingkan.php', 'chart'],
        ['rencana', 'Rencana Langkahku', 'siswa/rencana.php', 'calendar'],
        ['jurnal', 'Jurnal Refleksi', 'siswa/jurnal.php', 'book'],
        ['konsultasi', 'Ruang Konsultasi BK', 'siswa/konsultasi.php', 'message'],
        ['tips', 'Tips & Edukasi', 'siswa/tips.php', 'lightbulb'],
    ],
];

$role_labels = [
    'admin' => 'Administrasi Sistem',
    'guru_bk' => 'Ruang Guru BK',
    'siswa' => 'Langkah Karierku',
];

$judul_halaman = $judul_halaman ?? 'KARVIA';
$halaman_aktif = $halaman_aktif ?? 'index';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($judul_halaman) ?> — KARVIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            karvia: {
              50: '#f2f7f3',
              100: '#e5efe7',
              200: '#cce0d1',
              500: '#2d6a45',
              600: '#245938',
              700: '#1d482d',
              800: '#173a24',
              900: '#102819'
            }
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    table th + th, table td + td { border-left: 1px solid #eef2ed; }
  </style>
</head>
<body class="min-h-screen bg-[#f6f8f5] text-slate-900 antialiased">
  <div class="min-h-screen lg:pl-64">
    <!-- Backdrop Mobile -->
    <div id="karvia-backdrop" class="fixed inset-0 z-40 hidden bg-slate-950/40 backdrop-blur-sm lg:hidden"></div>

    <!-- Sidebar Attendara style -->
    <aside id="karvia-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-[#1a4029] bg-[#224f35] transition-transform duration-200 lg:translate-x-0">
      <!-- Brand Header -->
      <div class="flex items-center justify-between gap-3 px-5 py-5 border-b border-white/10">
        <div class="flex min-w-0 items-center gap-3">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white shadow-inner">
            <?php karvia_icon('compass', 'h-5 w-5'); ?>
          </span>
          <div class="min-w-0">
            <span class="block text-base font-extrabold tracking-tight text-white">KARVIA</span>
            <p class="truncate text-xs font-medium text-white/75"><?= e($role_labels[$peran_halaman] ?? 'Langkah Karierku') ?></p>
          </div>
        </div>
        <button id="karvia-menu-close" type="button" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-white/80 hover:bg-white/15 focus:outline-none lg:hidden" aria-label="Tutup menu navigasi">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav aria-label="Navigasi utama" class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-white/50">Menu Utama</p>
        <?php foreach ($menu_peran[$peran_halaman] as [$kunci, $label, $tujuan, $ikon]): ?>
          <?php $isActive = ($halaman_aktif === $kunci); ?>
          <a href="<?= e(url($tujuan)) ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive ? 'border-l-[3px] border-[#f2f6f2] bg-[#e8f0e9] pl-[9px] text-[#1b3d2b] font-semibold shadow-sm' : 'border-l-[3px] border-transparent text-white/85 hover:bg-white/15 hover:text-white' ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
            <span class="<?= $isActive ? 'text-[#1b3d2b]' : 'text-white/70' ?>"><?php karvia_icon($ikon, 'h-[18px] w-[18px]'); ?></span>
            <span class="truncate"><?= e($label) ?></span>
          </a>
        <?php endforeach; ?>

        <div class="pt-4 mt-2 border-t border-white/10">
          <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-white/50">Pengaturan</p>
          <?php $isProfilActive = ($halaman_aktif === 'profil'); ?>
          <a href="<?= e(url('profil.php')) ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors <?= $isProfilActive ? 'border-l-[3px] border-[#f2f6f2] bg-[#e8f0e9] pl-[9px] text-[#1b3d2b] font-semibold shadow-sm' : 'border-l-[3px] border-transparent text-white/85 hover:bg-white/15 hover:text-white' ?>"<?= $isProfilActive ? ' aria-current="page"' : '' ?>>
            <span class="<?= $isProfilActive ? 'text-[#1b3d2b]' : 'text-white/70' ?>"><?php karvia_icon('settings', 'h-[18px] w-[18px]'); ?></span>
            <span class="truncate">Profil &amp; Password</span>
          </a>
        </div>
      </nav>

      <!-- User & Logout Footer -->
      <div class="border-t border-white/15 p-3">
        <div class="mb-2 flex items-center gap-2.5 rounded-lg bg-black/15 px-3 py-2 text-white">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/20 text-xs font-bold text-white">
            <?= e(mb_strtoupper(mb_substr($pengguna_aktif['nama_lengkap'] ?? 'U', 0, 1))) ?>
          </span>
          <div class="min-w-0 flex-1">
            <div class="truncate text-xs font-semibold leading-tight"><?= e($pengguna_aktif['nama_lengkap'] ?? '') ?></div>
            <div class="truncate text-[10px] text-white/70"><?= e($pengguna_aktif['sekolah_nama'] ?? ucfirst(str_replace('_', ' ', $peran_halaman))) ?></div>
          </div>
        </div>
        <form method="post" action="<?= e(url('logout.php')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-medium text-white/80 transition-colors hover:bg-rose-500/20 hover:text-rose-200 focus:outline-none">
            <span class="text-white/70"><?php karvia_icon('logout', 'h-4 w-4'); ?></span>
            <span>Keluar Sistem</span>
          </button>
        </form>
      </div>
    </aside>

    <!-- Content Shell -->
    <div class="flex min-h-screen flex-col min-w-0">
      <!-- Sticky Header Attendara style -->
      <header class="sticky top-0 z-30 flex items-center justify-between border-b border-[#dfe6de] bg-white px-4 py-3.5 sm:px-6 lg:px-8 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
        <div class="flex min-w-0 items-center gap-3">
          <button id="karvia-menu-toggle" type="button" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-[#dfe6de] bg-white text-slate-600 hover:bg-[#eff3ee] focus:outline-none lg:hidden" aria-label="Buka menu navigasi">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[#55785f]">
              <?= e($role_labels[$peran_halaman] ?? 'KARVIA') ?>
            </p>
            <h1 class="text-lg font-bold tracking-tight text-[#203628] sm:text-xl">
              <?= e($judul_halaman) ?>
            </h1>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <?php if ($peran_halaman === 'admin'): ?>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#edf3ed] px-3 py-1 text-xs font-semibold text-[#245938]">
              <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
              Admin Global
            </span>
          <?php elseif (!empty($pengguna_aktif['sekolah_nama'])): ?>
            <span class="hidden items-center gap-1.5 rounded-full bg-[#edf3ed] px-3 py-1 text-xs font-semibold text-[#245938] sm:inline-flex">
              <?php karvia_icon('school', 'h-3.5 w-3.5 text-[#55785f]'); ?>
              <?= e($pengguna_aktif['sekolah_nama']) ?>
            </span>
          <?php endif; ?>
        </div>
      </header>

      <!-- Main Body -->
      <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <?php flash_tampil(); ?>
