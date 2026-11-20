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
  <style>
    :root{--karvia-green:#15803d;--karvia-ink:#14532d}
    html{scroll-behavior:smooth;scroll-padding-top:5rem}
    body{font-family:Inter,sans-serif}
    .karvia-grain{background-image:radial-gradient(#14532d0b .7px,transparent .7px);background-size:8px 8px}
    .karvia-progress{height:2px;transform:scaleX(0);transform-origin:left;background:var(--karvia-green);transition:transform .08s linear}
    .karvia-dashboard{box-shadow:0 36px 90px -45px #14532d55,0 12px 35px -25px #172a1b66}
    .karvia-card{transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}
    .karvia-card:hover{transform:translateY(-4px);border-color:#bbd8c1;box-shadow:0 18px 38px -28px #14532d70}
    .karvia-faq summary::-webkit-details-marker{display:none}
    .karvia-faq[open] summary i{transform:rotate(45deg)}
    .karvia-orbit{animation:karvia-drift 9s ease-in-out infinite}
    .karvia-orbit-delay{animation-delay:-4.5s}
    @keyframes karvia-drift{0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}
    @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}}
  </style>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
  <header id="atas" class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex min-h-[76px] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
      <a href="#atas" class="flex shrink-0 items-center gap-3" aria-label="KARVIA, kembali ke atas">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-karvia-700 text-white shadow-sm"><i class="fa-solid fa-compass text-lg" aria-hidden="true"></i></span>
        <span><strong class="block leading-tight tracking-tight text-slate-900">KARVIA</strong><small class="text-xs text-slate-500">Career Vision &amp; Action</small></span>
      </a>
      <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex" aria-label="Navigasi halaman">
        <a class="transition hover:text-karvia-800" href="#tentang">Tentang</a>
        <a class="transition hover:text-karvia-800" href="#tahapan">Tahapan</a>
        <a class="transition hover:text-karvia-800" href="#pendampingan">Pendampingan</a>
        <a class="transition hover:text-karvia-800" href="#privasi">Privasi</a>
      </nav>
      <a href="login.php" class="inline-flex shrink-0 items-center rounded-xl bg-karvia-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-karvia-800 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Masuk <i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i></a>
    </div>
    <nav class="flex justify-center gap-6 overflow-x-auto border-t border-slate-100 px-4 py-2.5 text-xs font-medium text-slate-600 md:hidden" aria-label="Navigasi halaman seluler">
      <a class="whitespace-nowrap hover:text-karvia-800" href="#tentang">Tentang</a><a class="whitespace-nowrap hover:text-karvia-800" href="#tahapan">Tahapan</a><a class="whitespace-nowrap hover:text-karvia-800" href="#pendampingan">BK</a><a class="whitespace-nowrap hover:text-karvia-800" href="#privasi">Privasi</a>
    </nav>
    <div class="karvia-progress" id="scroll-progress" aria-hidden="true"></div>
  </header>

  <main class="flex-grow">
    <section class="relative mx-auto w-full max-w-[1440px] overflow-hidden px-4 pb-16 pt-12 sm:px-8 sm:pb-24 sm:pt-20 lg:px-12 lg:pt-24" aria-labelledby="judul-hero">
      <div class="karvia-grain absolute inset-0 -z-10 opacity-40" aria-hidden="true"></div>
      <div class="pointer-events-none absolute left-[8%] top-20 h-48 w-48 rounded-full bg-emerald-200/40 blur-3xl" aria-hidden="true"></div>
      <div class="pointer-events-none absolute right-[8%] top-44 h-56 w-56 rounded-full bg-amber-100/70 blur-3xl" aria-hidden="true"></div>
      <div class="relative mx-auto max-w-5xl text-center">
        <span class="mb-7 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/85 px-4 py-2 text-xs font-bold uppercase tracking-[.16em] text-karvia-800 shadow-sm"><i class="fa-solid fa-sparkles text-amber-500" aria-hidden="true"></i>Langkah Karierku · Untuk siswa SMA/MA</span>
        <h1 id="judul-hero" class="mx-auto max-w-5xl text-4xl font-extrabold leading-[1.08] tracking-tight text-karvia-900 sm:text-6xl lg:text-7xl">Kenali dirimu.<br class="hidden sm:block"> Temukan arah yang <span class="text-karvia-600">bermakna.</span></h1>
        <p class="mx-auto mt-7 max-w-3xl text-base leading-8 text-slate-600 sm:text-lg">Jelajahi potensi dan pilihan kariermu, lalu rencanakan langkah kecil berikutnya. KARVIA mendukung proses refleksi dan ruang diskusi bersama Guru BK.</p>
        <div class="mt-9 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
          <a href="login.php" class="inline-flex items-center justify-center rounded-xl bg-karvia-700 px-7 py-3.5 font-bold text-white shadow-md shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-karvia-800 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Masuk ke KARVIA <i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i></a>
          <a href="#tahapan" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-7 py-3.5 font-bold text-slate-700 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:text-karvia-800 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">Kenali Tahapannya <i class="fa-solid fa-arrow-down ml-2 text-karvia-600" aria-hidden="true"></i></a>
        </div>
        <p class="mt-5 text-sm text-slate-500">Bukan tes untuk menentukan masa depan—ini ruang untuk mengenal pilihanmu.</p>
      </div>
      <div class="relative mx-auto mt-16 max-w-5xl sm:mt-20" aria-label="Ilustrasi ringkasan proses eksplorasi KARVIA">
        <div class="karvia-orbit absolute -left-2 top-8 z-10 hidden rounded-2xl border border-emerald-100 bg-white p-4 shadow-lg shadow-emerald-950/5 sm:block lg:-left-8">
          <div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700"><i class="fa-solid fa-seedling" aria-hidden="true"></i></span><span><span class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Mulai dari</span><span class="block text-sm font-bold text-slate-800">Mengenali diri</span></span></div>
        </div>
        <div class="karvia-orbit karvia-orbit-delay absolute -right-2 bottom-10 z-10 hidden rounded-2xl border border-amber-100 bg-white p-4 shadow-lg shadow-emerald-950/5 sm:block lg:-right-8">
          <div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fa-solid fa-route" aria-hidden="true"></i></span><span><span class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Lanjutkan dengan</span><span class="block text-sm font-bold text-slate-800">Satu langkah kecil</span></span></div>
        </div>
        <div class="karvia-dashboard overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white text-left sm:rounded-[2rem]">
          <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-6 sm:py-4"><div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-rose-300"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-300"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div><span class="text-[10px] font-semibold tracking-wide text-slate-400 sm:text-xs">PERJALANAN EKSPLORASIKU</span><span class="w-10"></span></div>
          <div class="grid min-h-[290px] md:grid-cols-[220px_1fr] lg:grid-cols-[245px_1fr]">
            <div class="hidden border-r border-slate-100 bg-slate-50/70 p-5 md:block lg:p-6">
              <div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-karvia-700 text-white"><i class="fa-solid fa-compass" aria-hidden="true"></i></span><span><span class="block text-sm font-extrabold text-slate-800">KARVIA</span><span class="text-[11px] text-slate-500">Langkah Karierku</span></span></div>
              <p class="mt-8 text-[10px] font-bold uppercase tracking-[.15em] text-slate-400">Ruang eksplorasi</p>
              <div class="mt-3 space-y-2"><div class="flex items-center gap-3 rounded-xl bg-emerald-50 px-3 py-2.5 text-xs font-semibold text-karvia-800"><i class="fa-solid fa-user-check w-4 text-center" aria-hidden="true"></i> Kenali Diriku</div><div class="flex items-center gap-3 px-3 py-2 text-xs text-slate-500"><i class="fa-solid fa-briefcase w-4 text-center" aria-hidden="true"></i> Jelajah Profesi</div><div class="flex items-center gap-3 px-3 py-2 text-xs text-slate-500"><i class="fa-solid fa-scale-balanced w-4 text-center" aria-hidden="true"></i> Bandingkan Pilihan</div><div class="flex items-center gap-3 px-3 py-2 text-xs text-slate-500"><i class="fa-solid fa-list-check w-4 text-center" aria-hidden="true"></i> Rencana Langkah</div></div>
              <div class="mt-7 rounded-xl border border-emerald-100 bg-white p-3"><p class="text-[10px] font-semibold text-slate-500">Ingat</p><p class="mt-1 text-[11px] leading-relaxed text-slate-600">Setiap perjalanan punya ritme sendiri. Mulai dari hal yang paling dekat.</p></div>
            </div>
            <div class="p-4 sm:p-6 lg:p-8">
              <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start"><div><p class="text-xs font-semibold text-karvia-700">Ruang refleksi</p><h2 class="mt-1 text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Apa yang membuatmu tertarik?</h2><p class="mt-2 max-w-lg text-xs leading-relaxed text-slate-500 sm:text-sm">Mulai dengan mengenali hal yang kamu sukai dan kekuatan yang ingin kamu kembangkan.</p></div><span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-semibold text-karvia-800"><i class="fa-solid fa-lock" aria-hidden="true"></i> Catatan personal</span></div>
              <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4"><div class="flex items-center gap-2 text-xs font-bold text-slate-700"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white text-karvia-700"><i class="fa-solid fa-heart" aria-hidden="true"></i></span> Hal yang aku minati</div><div class="mt-4 flex flex-wrap gap-2"><span class="rounded-full border border-emerald-100 bg-white px-2.5 py-1 text-[10px] text-slate-600">Memecahkan masalah</span><span class="rounded-full border border-emerald-100 bg-white px-2.5 py-1 text-[10px] text-slate-600">Membantu sesama</span><span class="rounded-full border border-emerald-100 bg-white px-2.5 py-1 text-[10px] text-slate-600">Belajar hal baru</span></div></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4"><div class="flex items-center gap-2 text-xs font-bold text-slate-700"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-700"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span> Langkah kecilku</div><p class="mt-3 text-[11px] leading-relaxed text-slate-500">Cari tahu satu profesi yang ingin kamu kenali lebih dekat.</p><div class="mt-4 flex items-center gap-2 text-[10px] font-semibold text-karvia-700"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Bisa dimulai minggu ini</div></div>
              </div>
              <div class="mt-4 flex flex-col gap-3 rounded-2xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-karvia-700 shadow-sm"><i class="fa-solid fa-comments" aria-hidden="true"></i></span><p class="text-[11px] leading-relaxed text-slate-600">Ingin membicarakan pilihanmu? Kamu bisa membagikan catatan kepada Guru BK.</p></div><span class="shrink-0 text-[10px] font-semibold text-slate-400">Atas pilihanmu</span></div>
            </div>
          </div>
        </div>
        <p class="mt-4 text-center text-[11px] text-slate-400">Ilustrasi pengalaman KARVIA · Bukan hasil tes atau rekomendasi otomatis</p>
      </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-manfaat">
      <div class="grid items-start gap-8 border-y border-slate-200 py-10 md:grid-cols-[.8fr_1.5fr] md:gap-16">
        <div><span class="text-xs font-bold uppercase tracking-[.18em] text-karvia-700">Kenapa KARVIA?</span><h2 id="judul-manfaat" class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-3xl">Pilihannya banyak. Mulai dengan memahami dirimu.</h2></div>
        <p class="text-lg leading-relaxed text-slate-600 sm:text-xl">Eksplorasi karier tidak harus berawal dari jawaban yang pasti. Kamu bisa mulai dari pertanyaan, rasa ingin tahu, dan satu langkah kecil yang terasa mungkin dilakukan.</p>
      </div>
      <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <article class="karvia-card rounded-2xl border border-slate-200 bg-white p-5"><span class="text-xs font-bold uppercase tracking-wider text-karvia-700">01 · Refleksi</span><h3 class="mt-3 font-bold text-slate-900">Apa yang penting bagiku?</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Kenali hal yang kamu sukai, kekuatanmu, dan lingkungan yang mendukungmu.</p></article>
        <article class="karvia-card rounded-2xl border border-slate-200 bg-white p-5"><span class="text-xs font-bold uppercase tracking-wider text-karvia-700">02 · Eksplorasi</span><h3 class="mt-3 font-bold text-slate-900">Pilihan apa yang bisa kupelajari?</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Cari informasi tentang profesi dan jalur setelah lulus dengan rasa ingin tahu.</p></article>
        <article class="karvia-card rounded-2xl border border-slate-200 bg-white p-5"><span class="text-xs font-bold uppercase tracking-wider text-karvia-700">03 · Rencana</span><h3 class="mt-3 font-bold text-slate-900">Apa langkah kecil berikutnya?</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Susun langkah sementara yang bisa dibahas dan ditinjau kembali.</p></article>
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
      <div class="mx-auto mt-12 grid max-w-5xl gap-4 sm:grid-cols-3">
        <article class="karvia-card rounded-2xl border border-slate-200 bg-white p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700"><i class="fa-solid fa-compass" aria-hidden="true"></i></span><h3 class="mt-4 font-bold text-slate-900">Kenali dirimu</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Mulai dari minat, kekuatan, dan hal yang ingin kamu pelajari.</p></article>
        <article class="karvia-card rounded-2xl border border-slate-200 bg-white p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fa-solid fa-binoculars" aria-hidden="true"></i></span><h3 class="mt-4 font-bold text-slate-900">Jelajahi pilihan</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Cari tahu ragam profesi dan jalur yang bisa kamu pertimbangkan.</p></article>
        <article class="karvia-card rounded-2xl border border-slate-200 bg-white p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-700"><i class="fa-solid fa-shoe-prints" aria-hidden="true"></i></span><h3 class="mt-4 font-bold text-slate-900">Ambil langkah kecil</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Susun rencana sementara, lalu diskusikan bila kamu membutuhkan dukungan.</p></article>
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

    <section id="pendampingan" class="mx-auto w-full max-w-7xl scroll-mt-6 px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-guru-bk">
      <div class="grid items-center gap-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10 lg:grid-cols-[auto_1fr] lg:gap-10">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-karvia-50 text-3xl text-karvia-700 lg:mx-0"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
        <div class="text-center lg:text-left">
          <h2 id="judul-guru-bk" class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Ruang untuk berdiskusi bersama Guru BK.</h2>
          <p class="mt-3 max-w-3xl leading-relaxed text-slate-600">Saat ingin berdiskusi, siswa dapat membagikan ringkasan eksplorasinya. Guru BK membantu meninjau pilihan dan langkah berikutnya; keputusan tetap berada pada siswa dengan dukungan orang dewasa yang dipercaya. Catatan pribadi tidak otomatis dibagikan.</p>
        </div>
      </div>
    </section>

    <section id="faq" class="border-y border-slate-200 bg-white" aria-labelledby="judul-faq">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[.8fr_1.2fr] lg:px-8">
        <div><span class="text-xs font-bold uppercase tracking-[.18em] text-karvia-700">Pertanyaan umum</span><h2 id="judul-faq" class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Yang perlu kamu tahu.</h2><p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600">Informasi singkat tentang penggunaan KARVIA, akun, dan catatan eksplorasi.</p></div>
        <div class="divide-y divide-slate-200">
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-slate-800"><span>Apa itu KARVIA?</span><i class="fa-solid fa-plus text-sm text-karvia-700 transition-transform" aria-hidden="true"></i></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">KARVIA adalah media pendukung bimbingan karier bagi siswa SMA/MA. Kamu dapat menggunakannya untuk refleksi diri, menjelajahi pilihan, dan merencanakan langkah untuk didiskusikan.</p></details>
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-slate-800"><span>Apakah KARVIA menentukan profesi yang cocok untukku?</span><i class="fa-solid fa-plus text-sm text-karvia-700 transition-transform" aria-hidden="true"></i></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">Tidak. KARVIA bukan tes psikologis dan tidak memberi vonis atau rekomendasi profesi otomatis. Pilihan tetap milikmu, dengan dukungan informasi dan diskusi bersama Guru BK.</p></details>
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-slate-800"><span>Apakah Guru BK langsung melihat catatanku?</span><i class="fa-solid fa-plus text-sm text-karvia-700 transition-transform" aria-hidden="true"></i></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">Tidak. Guru BK hanya dapat melihat catatan siswa yang dibagikan melalui KARVIA dan sesuai sekolahnya. Catatan tidak dibagikan otomatis.</p></details>
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-slate-800"><span>Bagaimana cara mendapatkan akun?</span><i class="fa-solid fa-plus text-sm text-karvia-700 transition-transform" aria-hidden="true"></i></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">Akun disiapkan oleh Admin sekolah. Landing page ini tidak menyediakan pendaftaran mandiri.</p></details>
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

  <footer class="overflow-hidden border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 pb-5 pt-9 sm:px-6 lg:px-8">
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
        <div>
          <a href="#atas" class="inline-flex items-center gap-3" aria-label="KARVIA, kembali ke atas"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-karvia-700 text-white"><i class="fa-solid fa-compass" aria-hidden="true"></i></span><span class="font-extrabold tracking-tight text-slate-900">KARVIA</span></a>
          <p class="mt-4 max-w-xs text-sm leading-relaxed text-slate-500">Ruang eksplorasi karier untuk mengenali diri, menjajaki pilihan, dan merencanakan langkah berikutnya.</p>
          <p class="mt-3 text-xs text-slate-400">UIN Raden Intan Lampung</p>
        </div>
        <div><h2 class="text-xs font-bold uppercase tracking-[.14em] text-slate-400">Jelajahi</h2><ul class="mt-4 space-y-3 text-sm text-slate-600"><li><a class="hover:text-karvia-800" href="#tentang">Tentang KARVIA</a></li><li><a class="hover:text-karvia-800" href="#tahapan">Tahapan eksplorasi</a></li></ul></div>
        <div><h2 class="text-xs font-bold uppercase tracking-[.14em] text-slate-400">Pendampingan</h2><ul class="mt-4 space-y-3 text-sm text-slate-600"><li><a class="hover:text-karvia-800" href="#pendampingan">Bersama Guru BK</a></li><li><a class="hover:text-karvia-800" href="#privasi">Privasi catatan</a></li></ul></div>
        <div><h2 class="text-xs font-bold uppercase tracking-[.14em] text-slate-400">Akun</h2><ul class="mt-4 space-y-3 text-sm text-slate-600"><li><a class="hover:text-karvia-800" href="login.php">Masuk ke KARVIA</a></li><li><a class="hover:text-karvia-800" href="#atas">Kembali ke atas</a></li></ul></div>
      </div>
      <div class="mt-9 h-px bg-slate-200"></div>
      <div class="flex flex-col gap-2 py-4 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between"><span>KARVIA — Career Vision &amp; Action</span><a class="hover:text-karvia-800" href="#faq">Pertanyaan umum</a><span>Media pendukung bimbingan karier siswa SMA/MA.</span></div>
    </div>
    <div class="select-none px-2 text-center text-[16vw] font-extrabold leading-[.8] tracking-[-.09em] text-karvia-900/[.045] sm:text-[15vw]" aria-hidden="true">KARVIA</div>
  </footer>
  <script>
    (() => {
      const progress = document.getElementById('scroll-progress');
      if (!progress) return;
      let scheduled = false;
      const update = () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.transform = `scaleX(${max > 0 ? window.scrollY / max : 0})`;
        scheduled = false;
      };
      window.addEventListener('scroll', () => {
        if (!scheduled) {
          window.requestAnimationFrame(update);
          scheduled = true;
        }
      }, { passive: true });
      window.addEventListener('resize', update, { passive: true });
      update();
    })();
  </script>
</body>
</html>
