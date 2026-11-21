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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('assets/css/auth.css')) ?>">
</head>
<body class="auth-page">
  <main class="auth-layout">
    <aside class="auth-aside">
      <a href="<?= e(url('index.php')) ?>" class="auth-brand" aria-label="KARVIA, kembali ke beranda">
        <span class="auth-brand-mark" aria-hidden="true">K</span>
        <span><span class="auth-brand-name">KARVIA</span><span class="auth-brand-tagline">Career Vision &amp; Action</span></span>
      </a>
      <div class="auth-aside-copy">
        <p class="auth-eyebrow">Langkah Karierku</p>
        <h2>Kenali potensi.<br>Rancang masa depan.</h2>
        <p>Ruang aman untuk mengeksplorasi minat dan merencanakan langkah karier bersama KARVIA.</p>
      </div>
      <p class="auth-aside-foot">Media digital bimbingan karier<br>UIN Raden Intan Lampung · Chindy Theresia</p>
    </aside>

    <section class="auth-main" aria-labelledby="auth-title">
      <div class="auth-form-wrap">
        <header class="auth-heading">
          <h1 id="auth-title">Selamat datang</h1>
          <p>Masuk menggunakan akun KARVIA Anda.</p>
        </header>

        <?php if ($flash): ?>
          <div class="auth-alert" role="alert"><?= e($flash['pesan']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('login.php')) ?>">
          <?= csrf_field() ?>
          <div class="auth-fields">
            <div class="auth-field">
              <label for="username">Username</label>
              <input id="username" name="username" type="text" autocomplete="username" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="auth-field">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required>
            </div>
          </div>
          <button type="submit" class="auth-submit">Masuk ke KARVIA <span aria-hidden="true">→</span></button>
        </form>

        <p class="auth-note">Data eksplorasi Anda bersifat pribadi dan digunakan untuk mendukung bimbingan karier.</p>
        <p class="auth-switch-link">Belum punya akun siswa? <a class="auth-link" href="<?= e(url('daftar.php')) ?>">Daftar di sini</a></p>
        <a class="auth-link auth-back" href="<?= e(url('index.php')) ?>">Kembali ke beranda</a>
      </div>
    </section>
  </main>
</body>
</html>
