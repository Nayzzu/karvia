<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';

if (auth_pengguna()) {
    $akun = auth_muat_ulang();
    if ($akun) {
        auth_arahkan_dashboard($akun);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $hasil = auth_login($username, $password);
    if ($hasil['berhasil']) {
        auth_arahkan_dashboard(auth_pengguna());
    }
    flash_set('gagal', $hasil['pesan']);
    redirect('login.php');
}
$flash = flash_ambil();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Masuk ke akun KARVIA untuk melanjutkan eksplorasi karier.">
  <meta name="theme-color" content="#224f35">
  <title>Masuk — KARVIA</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23224f35' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpolygon points='16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76'/%3E%3C/svg%3E">
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
              700: '#224f35',
              800: '#1d482d',
              900: '#173a24'
            }
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="flex min-h-screen flex-col bg-[#f6f8f5] text-slate-900 antialiased">
  <main class="flex flex-grow items-center justify-center px-4 py-10 sm:px-6">
    <div class="w-full max-w-md">
      <div class="mb-6 text-center">
        <a href="<?= e(url('index.php')) ?>" class="inline-flex items-center gap-3" aria-label="KARVIA, kembali ke beranda">
          <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#224f35] text-white shadow-sm"><?php karvia_icon('compass', 'h-6 w-6'); ?></span>
          <span class="text-left"><span class="block text-xl font-extrabold leading-tight tracking-tight text-[#173a24]">KARVIA</span><span class="text-[11px] font-semibold uppercase tracking-[.14em] text-[#55785f]">Career Vision &amp; Action</span></span>
        </a>
        <p class="mx-auto mt-4 max-w-sm text-xs leading-relaxed text-slate-500">Pengembangan Media Digital Bimbingan Karier (Langkah Karierku)<br><span class="text-slate-400">UIN Raden Intan Lampung (Chindy Theresia - 2411080092)</span></p>
      </div>

      <div class="rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-6">
          <h1 class="text-xl font-bold text-[#203628]">Selamat datang</h1>
          <p class="mt-1 text-sm text-slate-500">Masuk menggunakan akun KARVIA Anda.</p>
        </div>

        <?php if ($flash): ?>
          <div class="mb-5 rounded-xl border p-4 text-sm <?= $flash['tipe'] === 'peringatan' ? 'border-amber-300 bg-amber-50 text-amber-900' : 'border-rose-300 bg-rose-50 text-rose-900' ?>" role="alert"><?= e($flash['pesan']) ?></div>
        <?php endif; ?>

        <form class="space-y-5" method="post" action="<?= e(url('login.php')) ?>">
          <?= csrf_field() ?>
          <div>
            <label for="username" class="mb-1 block text-sm font-medium text-slate-700">Username</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><?php karvia_icon('users', 'h-4 w-4'); ?></span>
              <input id="username" name="username" type="text" autocomplete="username" class="block w-full rounded-xl border border-[#dfe6de] py-3 pl-10 pr-3 text-sm placeholder-slate-400 focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30" placeholder="Masukkan username" required>
            </div>
          </div>
          <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><?php karvia_icon('lock', 'h-4 w-4'); ?></span>
              <input id="password" name="password" type="password" autocomplete="current-password" class="block w-full rounded-xl border border-[#dfe6de] py-3 pl-10 pr-3 text-sm placeholder-slate-400 focus:border-[#2d6a45] focus:outline-none focus:ring-2 focus:ring-[#2d6a45]/30" placeholder="Masukkan password" required>
            </div>
          </div>
          <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-[#224f35] px-4 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1d482d] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2">
            Masuk ke KARVIA <?php karvia_icon('arrow-right', 'ml-2 h-4 w-4'); ?>
          </button>
        </form>

        <div class="mt-6 border-t border-[#eef2ed] pt-4">
          <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500">
            <span class="mt-0.5 shrink-0 text-[#2d6a45]"><?php karvia_icon('shield', 'h-4 w-4'); ?></span>
            Data eksplorasi aman, dijaga kerahasiaannya, dan digunakan untuk bimbingan karier.
          </p>
        </div>
      </div>

      <p class="mt-6 text-center text-xs text-slate-500">Belum punya akun siswa? <a class="font-semibold text-[#2d6a45] hover:underline" href="<?= e(url('daftar.php')) ?>">Daftar di sini</a></p>
      <p class="mt-2 text-center text-xs"><a class="font-semibold text-[#2d6a45] hover:underline" href="<?= e(url('index.php')) ?>">Kembali ke beranda</a></p>
    </div>
  </main>
</body>
</html>
