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
  <meta name="description" content="KARVIA membantu siswa SMA/MA mengenali potensi diri, menjelajahi pilihan karier, dan merencanakan langkah masa depan bersama Guru BK.">
  <title>KARVIA — Langkah Karierku</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{karvia:{50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>html{scroll-behavior:smooth;scroll-padding-top:1rem}body{font-family:Inter,sans-serif}</style>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
  <header id="atas" class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex min-h-16 max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
      <a href="#atas" class="flex items-center gap-3" aria-label="KARVIA, kembali ke atas">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-karvia-600 text-white shadow-sm"><i class="fa-solid fa-compass text-lg" aria-hidden="true"></i></span>
        <span><strong class="block leading-tight text-slate-900">KARVIA</strong><small class="text-xs text-slate-500">Career Vision &amp; Action</small></span>
      </a>
      <nav class="order-3 flex w-full items-center justify-center gap-5 text-sm font-medium text-slate-600 sm:order-2 sm:w-auto sm:gap-7" aria-label="Navigasi halaman">
        <a class="transition hover:text-karvia-800" href="#tentang">Tentang</a>
        <a class="transition hover:text-karvia-800" href="#tahapan">Tahapan</a>
        <a class="transition hover:text-karvia-800" href="#privasi">Privasi</a>
      </nav>
      <a href="login.php" class="order-2 inline-flex items-center rounded-xl bg-karvia-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-karvia-700 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2 sm:order-3">Masuk <i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i></a>
    </div>
  </header>

  <main class="flex-grow">
    <section class="mx-auto w-full max-w-7xl px-4 pt-7 sm:px-6 sm:pt-10 lg:px-8" aria-labelledby="judul-hero">
      <div class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-karvia-50 to-emerald-100 px-5 py-14 text-center sm:px-10 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-4xl">
          <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/80 px-4 py-2 text-xs font-bold uppercase tracking-wider text-karvia-800 shadow-sm"><i class="fa-solid fa-compass" aria-hidden="true"></i>KARVIA · Langkah Karierku</span>
          <h1 id="judul-hero" class="mx-auto max-w-4xl text-4xl font-extrabold leading-tight tracking-tight text-karvia-900 sm:text-5xl lg:text-6xl">Kenali dirimu. Temukan pilihanmu. Rencanakan langkah berikutnya.</h1>
          <p class="mx-auto mt-6 max-w-3xl text-base leading-relaxed text-slate-600 sm:text-lg">KARVIA membantu siswa SMA/MA mengenali potensi diri, menjelajahi pilihan karier, dan menyusun langkah masa depan—dengan ruang untuk berdiskusi bersama Guru BK.</p>
          <div class="mt-8 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
            <a href="login.php" class="inline-flex items-center justify-center rounded-xl bg-karvia-700 px-6 py-3 font-bold text-white shadow-sm transition hover:bg-karvia-800 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Masuk ke KARVIA <i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i></a>
            <a href="#tahapan" class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-6 py-3 font-bold text-karvia-800 transition hover:bg-karvia-50 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Lihat Tahapan <i class="fa-solid fa-arrow-down ml-2" aria-hidden="true"></i></a>
          </div>
          <p class="mt-5 text-sm text-slate-500">Perjalanan karier dimulai dengan mengenali diri sendiri.</p>
        </div>
      </div>
    </section>

    <section id="privasi" class="mx-auto w-full max-w-7xl scroll-mt-6 px-4 pt-6 sm:px-6 lg:px-8" aria-labelledby="judul-privasi">
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 sm:p-6">
        <div class="flex items-start gap-4">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-karvia-700"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i></span>
          <div>
            <h2 id="judul-privasi" class="font-bold text-karvia-900">Eksplorasi, bukan penilaian</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-700">KARVIA adalah media pendukung bimbingan karier—bukan tes psikologis, penentu pilihan otomatis, atau pengganti Guru BK dan konselor. Catatan eksplorasi bersifat personal; Guru BK hanya dapat melihat catatan yang dibagikan siswa sesuai aturan aplikasi.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="tentang" class="mx-auto w-full max-w-7xl scroll-mt-6 px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-tentang">
      <div class="mx-auto max-w-3xl text-center">
        <span class="text-xs font-bold uppercase tracking-[0.18em] text-karvia-700">Tentang KARVIA</span>
        <h2 id="judul-tentang" class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Masa depan tidak harus diputuskan sekaligus.</h2>
        <p class="mt-5 text-base leading-relaxed text-slate-600">Mengenali minat dan kekuatan, mencari tahu berbagai pilihan, lalu mencoba satu langkah kecil dapat membantu menyiapkan bahan untuk berdiskusi dan mengambil keputusan dengan lebih terarah. KARVIA mendampingi proses itu tanpa menentukan pilihan untukmu.</p>
      </div>
    </section>

    <section id="tahapan" class="scroll-mt-6 border-y border-slate-200 bg-white" aria-labelledby="judul-tahapan">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
        <div class="mx-auto mb-10 max-w-2xl text-center">
          <span class="text-xs font-bold uppercase tracking-[0.18em] text-karvia-700">Alur eksplorasi</span>
          <h2 id="judul-tahapan" class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Langkah kecil untuk mengenal arahmu.</h2>
          <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">Jalani prosesnya secara bertahap. Kamu bisa menggunakan hasil refleksi sebagai bahan diskusi bersama Guru BK.</p>
        </div>
        <ol class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <li class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-karvia-100 text-karvia-800"><i class="fa-solid fa-user-check" aria-hidden="true"></i></span>
            <h3 class="font-bold text-slate-900">1. Kenali Diriku</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Refleksikan minat, kekuatan, dan hal yang penting bagimu.</p>
          </li>
          <li class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
            <h3 class="font-bold text-slate-900">2. Jelajah Profesi</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Kenali beragam profesi, aktivitas kerja, dan jalur pendidikan.</p>
          </li>
          <li class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-purple-100 text-purple-700"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i></span>
            <h3 class="font-bold text-slate-900">3. Bandingkan Pilihan</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Pertimbangkan beberapa pilihan berdasarkan kriteria yang bermakna bagimu.</p>
          </li>
          <li class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
            <h3 class="font-bold text-slate-900">4. Rencana Langkahku</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Susun tujuan sementara dan langkah kecil yang bisa mulai dilakukan.</p>
          </li>
          <li class="rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:col-span-2 lg:col-span-2">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-700"><i class="fa-solid fa-book-open-reader" aria-hidden="true"></i></span>
            <h3 class="font-bold text-slate-900">5. Jurnal &amp; Konsultasi</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Refleksikan prosesmu dan bagikan catatan kepada Guru BK bila ingin berdiskusi.</p>
          </li>
        </ol>
      </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-guru-bk">
      <div class="grid items-center gap-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10 lg:grid-cols-[auto_1fr] lg:gap-10">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-karvia-50 text-3xl text-karvia-700 lg:mx-0"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
        <div class="text-center lg:text-left">
          <h2 id="judul-guru-bk" class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Ruang untuk berdiskusi bersama Guru BK.</h2>
          <p class="mt-3 max-w-3xl leading-relaxed text-slate-600">Saat ingin berdiskusi, siswa dapat membagikan ringkasan eksplorasinya. Guru BK membantu meninjau pilihan dan langkah berikutnya; keputusan tetap berada pada siswa dengan dukungan orang dewasa yang dipercaya. Catatan pribadi tidak otomatis dibagikan.</p>
        </div>
      </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 pb-16 sm:px-6 sm:pb-20 lg:px-8" aria-labelledby="judul-ajakan">
      <div class="rounded-3xl bg-gradient-to-br from-karvia-900 to-emerald-700 px-6 py-12 text-center text-white sm:px-10 sm:py-14">
        <h2 id="judul-ajakan" class="text-2xl font-extrabold sm:text-3xl">Siap mulai mengenali pilihanmu?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-emerald-50 sm:text-base">Masuk ke akun KARVIA untuk melanjutkan perjalanan eksplorasi kariermu.</p>
        <a href="login.php" class="mt-6 inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 font-bold text-karvia-900 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-karvia-800">Masuk ke KARVIA <i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i></a>
      </div>
    </section>
  </main>

  <footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-center sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:text-left lg:px-8">
      <div><p class="font-bold text-slate-800">KARVIA <span class="font-normal text-slate-500">— Career Vision &amp; Action</span></p><p class="mt-1 text-xs text-slate-500">UIN Raden Intan Lampung</p></div>
      <nav class="flex flex-wrap justify-center gap-x-5 gap-y-2 text-sm text-slate-600 sm:justify-end" aria-label="Navigasi footer"><a class="hover:text-karvia-800" href="#atas">Kembali ke atas</a><a class="hover:text-karvia-800" href="#tentang">Tentang</a><a class="hover:text-karvia-800" href="#tahapan">Tahapan</a><a class="hover:text-karvia-800" href="#privasi">Privasi</a></nav>
    </div>
  </footer>
</body>
</html>
