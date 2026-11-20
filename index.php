<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';

$akun = auth_muat_ulang();
if ($akun) {
    auth_arahkan_dashboard($akun);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KARVIA — Langkah Karierku</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{karvia:{50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex flex-col">
  <header class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-karvia-600 text-white flex items-center justify-center shadow-md"><i class="fa-solid fa-compass text-lg"></i></span>
        <span><strong class="block text-slate-900 leading-tight">KARVIA</strong><small class="text-xs text-slate-500">Career Vision &amp; Action</small></span>
      </a>
      <a href="login.php" class="px-4 py-2 rounded-xl bg-karvia-600 hover:bg-karvia-700 text-white text-sm font-bold">Masuk <i class="fa-solid fa-arrow-right ml-1"></i></a>
    </div>
  </header>
  <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-6">
    <section class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-karvia-50 to-emerald-100 px-6 py-14 text-center shadow-sm sm:px-10 sm:py-20" aria-labelledby="judul-hero">
      <div class="mx-auto max-w-4xl">
        <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/80 px-4 py-2 text-xs font-bold uppercase tracking-wider text-karvia-800 shadow-sm">
          <i class="fa-solid fa-compass" aria-hidden="true"></i>
          KARVIA · Langkah Karierku
        </span>
        <h1 id="judul-hero" class="mx-auto max-w-4xl text-4xl font-extrabold leading-tight tracking-tight text-karvia-900 sm:text-5xl lg:text-6xl">Kenali dirimu. Temukan pilihanmu. Rencanakan langkah berikutnya.</h1>
        <p class="mx-auto mt-6 max-w-3xl text-base leading-relaxed text-slate-600 sm:text-lg">KARVIA membantu siswa SMA/MA mengenali potensi diri, menjelajahi pilihan karier, dan menyusun langkah masa depan—dengan ruang untuk berdiskusi bersama Guru BK.</p>
        <div class="mt-8 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
          <a href="login.php" class="inline-flex items-center justify-center rounded-xl bg-karvia-700 px-6 py-3 font-bold text-white shadow-sm transition hover:bg-karvia-800 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Masuk ke KARVIA <i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i></a>
          <a href="#tahapan-eksplorasi" class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-6 py-3 font-bold text-karvia-800 transition hover:bg-karvia-50 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Lihat Tahapan <i class="fa-solid fa-arrow-down ml-2" aria-hidden="true"></i></a>
        </div>
        <p class="mt-5 text-sm text-slate-500">Perjalanan karier dimulai dengan mengenali diri sendiri.</p>
      </div>
    </section>
    <section class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-2xl shadow-sm">
      <div class="flex items-start"><i class="fa-solid fa-circle-info text-amber-500 text-xl mr-3 mt-0.5"></i><div><h2 class="font-bold text-amber-900 text-sm">Landasan &amp; Batasan Produk</h2><p class="text-xs text-amber-800 mt-1 leading-relaxed">KARVIA adalah media pendukung layanan bimbingan karier, bukan alat tes psikologis, bukan penentu jurusan otomatis, dan bukan pengganti konselor. Catatan eksplorasi bersifat personal dan dapat didiskusikan dengan Guru BK.</p></div></div>
    </section>
    <section id="tahapan-eksplorasi" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 scroll-mt-6" aria-label="Tahapan eksplorasi">
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-karvia-50 text-karvia-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-user-check"></i></div><h2 class="font-bold mb-1">1. Kenali Diriku</h2><p class="text-xs text-slate-500 leading-relaxed">Refleksi minat, kekuatan, dan nilai pribadi.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-briefcase"></i></div><h2 class="font-bold mb-1">2. Jelajah Profesi</h2><p class="text-xs text-slate-500 leading-relaxed">Kenali tugas dan jalur pendidikan berbagai profesi.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-scale-balanced"></i></div><h2 class="font-bold mb-1">3. Bandingkan Pilihan</h2><p class="text-xs text-slate-500 leading-relaxed">Pertimbangkan pilihan berdasarkan kriteria pribadi.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-list-check"></i></div><h2 class="font-bold mb-1">4. Rencanakan Langkah</h2><p class="text-xs text-slate-500 leading-relaxed">Susun tujuan dan langkah kecil yang bisa dilakukan.</p></article>
    </section>
  </main>
  <footer class="text-center text-xs text-slate-500 py-5">KARVIA — Career Vision &amp; Action · UIN Raden Intan Lampung</footer>
</body>
</html>
