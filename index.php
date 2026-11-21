<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';

$akun = auth_muat_ulang();
if ($akun) {
    auth_arahkan_dashboard($akun);
}
try {
    $stmt_artikel = db()->query('SELECT id, judul, gambar, isi, nama_penulis, diterbitkan_pada FROM artikel ORDER BY diterbitkan_pada DESC LIMIT 6');
    $artikel_terbaru = $stmt_artikel->fetchAll();
} catch (PDOException $kesalahan) {
    error_log('KARVIA: artikel landing belum tersedia: ' . $kesalahan->getMessage());
    $artikel_terbaru = [];
}

$og_gambar = !empty($artikel_terbaru[0]['gambar']) ? $artikel_terbaru[0]['gambar'] : null;
$og_gambar_url = $og_gambar
    ? url('uploads/artikel/' . rawurlencode($og_gambar))
    : url('assets/img/profesi/guru-bk.jpg');
$skema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$alamat_absolut = $skema . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('index.php');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="KARVIA membantu siswa SMA/MA mengenali potensi diri, menjelajahi pilihan karier, dan merencanakan langkah masa depan bersama Guru BK.">
  <meta name="theme-color" content="#224f35">
  <title>KARVIA — Langkah Karierku</title>
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="KARVIA">
  <meta property="og:title" content="KARVIA — Langkah Karierku">
  <meta property="og:description" content="Kenali dirimu, jelajahi pilihan karier, dan rencanakan satu langkah kecil bersama Guru BK.">
  <meta property="og:url" content="<?= e($alamat_absolut) ?>">
  <meta property="og:image" content="<?= e($og_gambar_url) ?>">
  <meta name="twitter:card" content="summary_large_image">
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
  <style>
    :root{--karvia-green:#224f35;--karvia-ink:#173a24}
    html{scroll-behavior:smooth;scroll-padding-top:5.5rem}
    body{font-family:Inter,sans-serif}
    .karvia-grain{background-image:radial-gradient(#173a2410 .7px,transparent .7px);background-size:8px 8px}
    .karvia-progress{height:2px;transform:scaleX(0);transform-origin:left;background:linear-gradient(90deg,#71875c,#c2a052);transition:transform .08s linear}
    .karvia-dashboard{box-shadow:0 36px 90px -45px #173a2455,0 12px 35px -25px #172a1b66}
    .artikel-isi{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3;overflow:hidden}
    .karvia-card{transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}
    .karvia-card:hover{transform:translateY(-4px);border-color:#c3d8c8;box-shadow:0 18px 38px -28px #224f3570}
    .karvia-faq summary::-webkit-details-marker{display:none}
    .karvia-faq[open] summary svg{transform:rotate(45deg)}
    .artikel-track{scrollbar-width:none;scroll-behavior:smooth}
    .artikel-track::-webkit-scrollbar{display:none}
    .menu-mobile::backdrop{background:#173a24}
    @keyframes hero-masuk{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    .hero-masuk{animation:hero-masuk .65s cubic-bezier(.2,.7,.2,1) both}
    .hero-masuk-1{animation-delay:.08s}.hero-masuk-2{animation-delay:.18s}.hero-masuk-3{animation-delay:.28s}.hero-masuk-4{animation-delay:.38s}
    @media(prefers-reduced-motion:reduce){.hero-masuk{animation:none}}
    @media(min-width:768px){.artikel-track{scrollbar-width:auto}.artikel-track::-webkit-scrollbar{display:block;height:8px}.artikel-track::-webkit-scrollbar-thumb{border-radius:999px;background:#cce0d1}.artikel-track::-webkit-scrollbar-track{background:transparent}}
    #atas{transition:background-color .3s,box-shadow .3s}
    #atas.scrolled{background:#fbfcf8ef;box-shadow:0 1px 0 #dfe6de;backdrop-filter:blur(14px)}
    #atas #scroll-progress{will-change:transform}
    #konten>section:first-child{isolation:isolate;padding-top:clamp(4rem,8vw,7rem);padding-bottom:clamp(4.5rem,9vw,8rem)}
    #konten>section:first-child>.karvia-grain,#konten>section:first-child>.pointer-events-none{display:none}
    #konten>section:first-child>.hero-orbit{opacity:.35}
    #konten>section:first-child h1{max-width:900px;font-size:clamp(2.65rem,6.8vw,6.25rem);letter-spacing:-.065em;line-height:.98}
    #konten>section:first-child h1 span{color:#a78643;background:linear-gradient(110deg,#7b925e,#bd9951);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
    #konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]{max-width:1120px;margin-top:clamp(3rem,6vw,5rem);perspective:1600px}
    #konten>section:first-child .karvia-dashboard{transform:rotateX(var(--mockup-tilt,2deg));transform-origin:center top;transition:transform .25s ease-out;box-shadow:0 55px 110px -65px #173a2470,0 18px 45px -28px #173a2440}
    #konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]::before,#konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]::after{position:absolute;z-index:2;display:block;pointer-events:none;border:1px solid #ffffffd9;background:#fffdf4ed;box-shadow:0 18px 45px -25px #173a2466;backdrop-filter:blur(12px);content:""}
    #konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]::before{content:"Kenali dirimu  ·  Jelajahi pilihan";top:16%;left:-3%;padding:13px 17px;border-radius:14px;color:#315d3d;font-size:11px;font-weight:700;letter-spacing:.02em;animation:karvia-float 6s ease-in-out infinite}
    #konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]::after{content:"Satu langkah kecil hari ini";right:-3%;bottom:17%;padding:13px 17px;border-radius:14px;color:#826b3e;font-size:11px;font-weight:700;animation:karvia-float 7s ease-in-out -2s infinite}
    @keyframes karvia-float{50%{translate:0 -9px}}
    @keyframes karvia-dashboard-scroll{from{transform:rotateX(3deg) translateY(12px)}to{transform:rotateX(0) translateY(0)}}
    @supports(animation-timeline:view()){#konten>section:first-child .karvia-dashboard{animation:karvia-dashboard-scroll linear both;animation-timeline:view();animation-range:entry 0% entry 75%}}
    .motion-ready .karvia-reveal{opacity:0;transform:translateY(24px);transition:opacity .75s cubic-bezier(.2,.7,.2,1),transform .75s cubic-bezier(.2,.7,.2,1);transition-delay:var(--reveal-delay,0ms)}
    .motion-ready .karvia-reveal.is-visible{opacity:1;transform:translateY(0)}
    .hero-typography{position:absolute;right:3%;top:clamp(8rem,23vw,18rem);z-index:0;color:#224f3508;font-size:clamp(7rem,23vw,21rem);font-weight:800;letter-spacing:-.12em;line-height:.75;white-space:nowrap;pointer-events:none;user-select:none;transform:translate3d(0,var(--hero-drift,0),0)}
    #konten>section:first-child>.relative{z-index:1}
    .hero-orbit{position:absolute;top:12%;left:50%;z-index:0;width:min(78vw,920px);aspect-ratio:1;border:1px solid #224f350e;border-radius:50%;pointer-events:none;transform:translateX(-50%) scale(var(--orbit-scale,1));}
    .hero-orbit::before,.hero-orbit::after{position:absolute;border:1px solid #224f350d;border-radius:50%;content:""}
    .hero-orbit::before{inset:11%}.hero-orbit::after{inset:23%}
    @media(prefers-reduced-motion:reduce){.karvia-float,.karvia-dashboard{animation:none!important;transform:none!important}.hero-typography{transform:none}}
    #konten>section:nth-child(2){max-width:100%;padding:clamp(4rem,8vw,7rem) max(1rem,calc((100vw - 1200px)/2));background:#173a24;color:#f7f6ed}
    #konten>section:nth-child(2)>div:first-child{border-color:#ffffff2b}
    #konten>section:nth-child(2) h2,#konten>section:nth-child(2) h3{color:#f7f6ed}
    #konten>section:nth-child(2) p{color:#d6dfd3}
    #konten>section:nth-child(2) article{border-color:#ffffff24;background:#ffffff0a;box-shadow:none}
    #konten>section:nth-child(2) article>span{color:#e4cd8e}
    #konten>section:nth-child(3)>div{border-color:#d9e1d5;background:linear-gradient(130deg,#e9efe6,#f5f2e4)}
    #konten>section:nth-child(5){background:#f2f3eb}
    #konten>section:nth-child(5) ol{gap:1px;border:1px solid #dfe6de;border-radius:24px;overflow:hidden;background:#dfe6de}
    #konten>section:nth-child(5) li{position:relative;min-height:205px;border:0;border-radius:0;background:#fbfcf8;padding:clamp(1.2rem,3vw,2rem);transition:background .25s,transform .25s}
    #konten>section:nth-child(5) li:hover{z-index:1;transform:translateY(-3px);background:#fff}
    #konten>section:nth-child(5) li>span:first-child{border-radius:50%;background:#e9eddd;color:#476847}
    #konten>section:nth-child(6)>div>div{border:0;background:linear-gradient(125deg,#e9efe6,#f7f6ed);box-shadow:0 24px 65px -45px #173a2455}
    #konten>section:nth-child(7){background:#f4f5ef}
    #konten>section:nth-child(7) article{border-color:#e5e7dd;background:#fff;box-shadow:0 20px 45px -38px #173a2455}
    #konten>section:nth-child(8){background:#fbfcf8}
    #konten>section:nth-child(8) details{border-color:#e5e7dd}
    #konten>section:nth-child(9){background:#f4f5ef}
    #konten>section:nth-child(9)>div>div{position:relative;overflow:hidden;border:1px solid #ffffff20;background:radial-gradient(ellipse at 85% 0%,#78936380,transparent 38%),linear-gradient(125deg,#173a24,#224f35 72%,#496b46);box-shadow:0 35px 75px -48px #173a2480}
    #konten>section:nth-child(9)>div>div::after{position:absolute;right:-8%;bottom:-95%;width:55vw;height:55vw;max-width:520px;max-height:520px;border:1px solid #ffffff1c;border-radius:50%;box-shadow:0 0 0 32px #ffffff08,0 0 0 64px #ffffff06;content:"";pointer-events:none}
    #konten>section:nth-child(9)>div>div h2,#konten>section:nth-child(9)>div>div p,#konten>section:nth-child(9)>div>div a{position:relative;z-index:1}
    #konten>section:nth-child(6)>div{border-top:1px solid #dfe6de;border-bottom:1px solid #dfe6de;border-radius:0;background:transparent;box-shadow:none}
    footer>div:last-child{color:#173a2409;line-height:.76;letter-spacing:-.1em}
    @media(max-width:767px){#konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]::before{top:-22px;left:4px;padding:9px 11px;font-size:9px}#konten>section:first-child>div[aria-label="Ilustrasi Beranda Siswa KARVIA"]::after{right:4px;bottom:-21px;padding:9px 11px;font-size:9px}#konten>section:nth-child(5) li{min-height:0}#konten>section:nth-child(2){padding-left:1rem;padding-right:1rem}}
    @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}}
  </style>
</head>
<body class="flex min-h-screen flex-col bg-[#f6f8f5] text-slate-900 antialiased">
  <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-[#224f35] focus:px-4 focus:py-2.5 focus:text-sm focus:font-bold focus:text-white">Lewati ke konten</a>

  <header id="atas" class="sticky top-0 z-50 border-b border-[#dfe6de] bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex min-h-[68px] max-w-7xl items-center justify-between gap-4 px-4 sm:min-h-[76px] sm:px-6 lg:px-8">
      <a href="#atas" class="flex shrink-0 items-center gap-3" aria-label="KARVIA, kembali ke atas">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#224f35] text-white shadow-sm"><?php karvia_icon('compass', 'h-5 w-5'); ?></span>
        <span><strong class="block leading-tight tracking-tight text-[#203628]">KARVIA</strong><small class="text-xs text-slate-500">Career Vision &amp; Action</small></span>
      </a>
      <nav class="hidden items-center gap-7 text-sm font-medium text-slate-600 md:flex" aria-label="Navigasi halaman">
        <a class="transition hover:text-[#224f35]" href="#tentang">Tentang</a>
        <a class="transition hover:text-[#224f35]" href="#tahapan">Tahapan</a>
        <a class="transition hover:text-[#224f35]" href="#artikel">Artikel</a>
        <a class="transition hover:text-[#224f35]" href="#pendampingan">Pendampingan</a>
        <a class="transition hover:text-[#224f35]" href="#privasi">Privasi</a>
        <a class="transition hover:text-[#224f35]" href="#faq">FAQ</a>
      </nav>
      <a href="login.php" class="hidden shrink-0 items-center rounded-xl bg-[#224f35] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#1d482d] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2 md:inline-flex">Masuk <?php karvia_icon('arrow-right', 'ml-2 h-4 w-4'); ?></a>
      <button id="menu-buka" type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#dfe6de] text-[#224f35] transition hover:bg-[#f2f7f3] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2 md:hidden" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="menu-mobile">
        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
    <dialog id="menu-mobile" class="menu-mobile m-0 h-dvh max-h-none w-screen max-w-none border-0 bg-[#173a24] p-0 text-[#f7f2d7] backdrop:bg-[#173a24] md:hidden" aria-labelledby="judul-menu-mobile">
      <div class="flex min-h-dvh flex-col px-5 pb-8 pt-5 sm:px-8">
        <div class="flex items-center justify-between">
          <a href="#atas" class="text-lg font-extrabold tracking-tight" aria-label="KARVIA, kembali ke atas">KARVIA</a>
          <button id="menu-tutup" type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 text-[#f7f2d7] transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#f7f2d7]" aria-label="Tutup menu navigasi"><svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
        </div>
        <h2 id="judul-menu-mobile" class="sr-only">Navigasi KARVIA</h2>
        <nav class="my-auto" aria-label="Navigasi halaman seluler">
          <a class="block border-b border-white/10 py-4 text-3xl font-medium tracking-tight" href="#tentang">Tentang</a>
          <a class="block border-b border-white/10 py-4 text-3xl font-medium tracking-tight" href="#tahapan">Tahapan</a>
          <a class="block border-b border-white/10 py-4 text-3xl font-medium tracking-tight" href="#artikel">Artikel</a>
          <a class="block border-b border-white/10 py-4 text-3xl font-medium tracking-tight" href="#pendampingan">Pendampingan</a>
          <a class="block border-b border-white/10 py-4 text-3xl font-medium tracking-tight" href="#privasi">Privasi</a>
          <a class="block border-b border-white/10 py-4 text-3xl font-medium tracking-tight" href="#faq">FAQ</a>
        </nav>
        <div class="space-y-4">
          <a href="login.php" class="flex min-h-12 items-center justify-center rounded-full border border-white/25 px-5 text-sm font-bold tracking-wide text-[#f7f2d7]">Masuk</a>
          <a href="daftar.php" class="flex min-h-12 items-center justify-center rounded-full bg-[#f7edbd] px-5 text-sm font-bold tracking-wide text-[#173a24]">Daftar Siswa</a>
          <p class="text-xs font-semibold uppercase tracking-[.14em] text-[#d8dfc1]">Kenali diri, jelajahi pilihan, rencanakan langkah.</p>
        </div>
      </div>
    </dialog>
    <div class="karvia-progress" id="scroll-progress" aria-hidden="true"></div>
  </header>

  <main id="konten" class="flex-grow">
    <section class="relative mx-auto w-full max-w-[1440px] overflow-hidden px-4 pb-16 pt-12 sm:px-8 sm:pb-24 sm:pt-20 lg:px-12 lg:pt-24" aria-labelledby="judul-hero">
      <div class="karvia-grain absolute inset-0 -z-10 opacity-40" aria-hidden="true"></div>
      <div class="pointer-events-none absolute left-[8%] top-20 h-48 w-48 rounded-full bg-[#e5efe7]/70 blur-3xl" aria-hidden="true"></div>
      <div class="pointer-events-none absolute right-[8%] top-44 h-56 w-56 rounded-full bg-amber-100/70 blur-3xl" aria-hidden="true"></div>
      <div class="hero-orbit" aria-hidden="true"></div>
      <div class="hero-typography" aria-hidden="true">ARAH</div>
      <div class="relative mx-auto max-w-5xl text-center">
        <h1 id="judul-hero" class="hero-masuk hero-masuk-1 mx-auto max-w-5xl text-4xl font-extrabold leading-[1.08] tracking-tight text-[#173a24] sm:text-6xl lg:text-7xl">Kenali dirimu.<br class="hidden sm:block"> Temukan arah yang <span class="text-[#2d6a45]">bermakna.</span></h1>
        <p class="hero-masuk hero-masuk-2 mx-auto mt-6 max-w-3xl text-base leading-8 text-slate-600 sm:mt-7 sm:text-lg">Jelajahi potensi dan pilihan kariermu, lalu rencanakan langkah kecil berikutnya. KARVIA mendukung proses refleksi dan ruang diskusi bersama Guru BK.</p>
        <div class="hero-masuk hero-masuk-3 mt-8 flex flex-col items-stretch justify-center gap-3 sm:mt-9 sm:flex-row sm:items-center">
          <a href="login.php" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-7 py-3.5 font-bold text-white shadow-md shadow-[#173a24]/10 transition hover:-translate-y-0.5 hover:bg-[#1d482d] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2">Masuk ke KARVIA <?php karvia_icon('arrow-right', 'ml-2 h-4 w-4'); ?></a>
          <a href="#tahapan" class="inline-flex items-center justify-center rounded-xl border border-[#dfe6de] bg-white px-7 py-3.5 font-bold text-slate-700 transition hover:-translate-y-0.5 hover:border-[#c3d8c8] hover:text-[#224f35] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2">Kenali Tahapannya <?php karvia_icon('arrow-down', 'ml-2 h-4 w-4 text-[#2d6a45]'); ?></a>
        </div>
        <p class="hero-masuk hero-masuk-4 mt-5 text-sm text-slate-500">Bukan tes untuk menentukan masa depan—ini ruang untuk mengenal pilihanmu.</p>
      </div>

      <div class="hero-masuk hero-masuk-4 relative mx-auto mt-16 max-w-5xl sm:mt-20" aria-label="Ilustrasi Beranda Siswa KARVIA">
        <div class="karvia-dashboard overflow-hidden rounded-[1.5rem] border border-[#dfe6de] bg-white text-left sm:rounded-[2rem]">
          <div class="flex items-center justify-between border-b border-[#eef2ed] bg-[#fbfdfb] px-4 py-3 sm:px-6 sm:py-4">
            <div class="flex items-center gap-2" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-rose-300"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-300"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div>
            <span class="text-[10px] font-semibold tracking-wide text-slate-400 sm:text-xs">BERANDA SISWA · KARVIA</span>
            <span class="w-10"></span>
          </div>
          <div class="grid md:grid-cols-[230px_1fr] lg:grid-cols-[250px_1fr]">
            <aside class="hidden bg-[#224f35] p-5 md:block lg:p-6" aria-label="Menu siswa">
              <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-white"><?php karvia_icon('compass', 'h-5 w-5'); ?></span>
                <span><span class="block text-sm font-extrabold text-white">KARVIA</span><span class="text-[11px] text-white/75">Langkah Karierku</span></span>
              </div>
              <p class="mt-7 px-1 text-[10px] font-bold uppercase tracking-[.14em] text-white/50">Menu Utama</p>
              <nav class="mt-2 space-y-1" aria-label="Menu mockup">
                <div aria-current="page" class="flex items-center gap-3 rounded-lg border-l-[3px] border-[#f2f6f2] bg-[#e8f0e9] px-3 py-2.5 text-xs font-semibold text-[#1b3d2b] shadow-sm"><?php karvia_icon('home', 'h-4 w-4'); ?> Beranda</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('users', 'h-4 w-4 text-white/70'); ?> Kenali Diriku</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('compass', 'h-4 w-4 text-white/70'); ?> Jelajah Profesi</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('chart', 'h-4 w-4 text-white/70'); ?> Bandingkan Pilihan</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('calendar', 'h-4 w-4 text-white/70'); ?> Rencana Langkahku</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('book', 'h-4 w-4 text-white/70'); ?> Jurnal Refleksi</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('message', 'h-4 w-4 text-white/70'); ?> Ruang Konsultasi</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('map', 'h-4 w-4 text-white/70'); ?> Jalur Setelah Lulus</div>
                <div class="flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2.5 text-xs text-white/85"><?php karvia_icon('lightbulb', 'h-4 w-4 text-white/70'); ?> Tips & Edukasi</div>
              </nav>
            </aside>

            <div class="min-w-0 bg-[#f6f8f5] p-3 sm:p-5 lg:p-6">
              <div class="flex items-center justify-between rounded-xl border border-[#dfe6de] bg-white px-4 py-3 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <div><p class="text-[9px] font-semibold uppercase tracking-[0.14em] text-[#55785f]">Siswa</p><p class="text-sm font-bold text-[#203628]">Beranda Siswa</p></div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#edf3ed] px-2.5 py-1 text-[10px] font-semibold text-[#245938]"><?php karvia_icon('school', 'h-3 w-3 text-[#55785f]'); ?> SMAN 1 Bandung</span>
              </div>

              <section class="mt-3 rounded-2xl bg-gradient-to-br from-[#173a24] via-[#224f35] to-emerald-700 p-4 text-white sm:p-6" aria-label="Sapaan dan progres eksplorasi">
                <p class="mb-2 text-[9px] font-bold uppercase tracking-[.16em] text-emerald-100">Perjalanan Karierku</p>
                <p class="text-lg font-extrabold tracking-tight sm:text-2xl">Halo, Chindy.</p>
                <p class="mt-2 max-w-xl text-[10px] leading-relaxed text-emerald-50 sm:text-xs">Kamu tidak harus punya semua jawaban sekarang. Kenali dirimu, jelajahi pilihan, lalu tentukan satu langkah yang terasa mungkin.</p>
                <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                  <div class="w-full max-w-sm"><div class="mb-2 flex items-center justify-between text-[9px] font-semibold text-emerald-50 sm:text-[10px]"><span>Progres eksplorasi</span><span>2 dari 5 langkah</span></div><div class="h-1.5 overflow-hidden rounded-full bg-white/20" role="progressbar" aria-label="Ilustrasi progres eksplorasi karier, dua dari lima langkah" aria-valuemin="0" aria-valuemax="5" aria-valuenow="2"><div class="h-full w-2/5 rounded-full bg-emerald-200"></div></div></div>
                  <span class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-[10px] font-bold text-[#173a24] shadow-sm">Lanjutkan: Bandingkan Pilihan <?php karvia_icon('arrow-right', 'h-3.5 w-3.5'); ?></span>
                </div>
              </section>

              <section class="mt-3 flex items-start gap-2.5 rounded-xl border border-emerald-100 bg-white p-3 shadow-sm" aria-label="Status Ruang Konsultasi BK">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#224f35]"><?php karvia_icon('message', 'h-4 w-4'); ?></span>
                <div class="min-w-0 flex-1"><p class="text-[10px] font-bold text-[#203628]">Ruang Konsultasi BK</p><p class="mt-1 text-[9px] text-slate-600">Catatanmu belum dibagikan. Kamu yang menentukan kapan siap dibahas.</p><p class="mt-1 text-[8px] text-slate-500">Isi eksplorasi tetap pribadi sampai kamu memilih untuk membagikannya.</p></div>
                <span class="hidden shrink-0 rounded-lg border border-emerald-200 px-2.5 py-2 text-[9px] font-semibold text-[#224f35] sm:inline-flex">Siapkan konsultasi →</span>
              </section>

              <section class="mt-3 rounded-xl border-l-4 border-amber-500 bg-amber-50 px-3 py-2.5" aria-label="Pengingat tentang KARVIA">
                <p class="text-[10px] font-bold text-amber-900">Ingat, ini perjalananmu</p>
                <p class="mt-1 text-[9px] leading-relaxed text-amber-800">KARVIA adalah media pendukung bimbingan karier, bukan tes psikologis atau penentu pilihan otomatis.</p>
              </section>

              <section class="mt-4" aria-label="Langkah eksplorasi karier">
                <div class="mb-2"><h3 class="text-xs font-extrabold text-[#203628] sm:text-sm">Langkah eksplorasi karier</h3><p class="mt-1 text-[9px] text-slate-500">Pilih langkah untuk mulai atau melanjutkan. Progres berdasarkan data yang tersimpan.</p></div>
                <ol class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                  <li class="rounded-xl border border-[#dfe6de] bg-white p-3"><div class="flex items-center justify-between gap-2"><span class="font-bold text-[10px] text-slate-900">01 · Kenali Diriku</span><span class="rounded-full bg-emerald-100 px-2 py-1 text-[8px] font-semibold text-emerald-800">Sudah ada data</span></div><p class="mt-1.5 text-[9px] leading-relaxed text-slate-500">Mulai dari minat, kekuatan, dan hal yang penting bagimu.</p><p class="mt-2 text-[8px] font-semibold text-[#224f35]">Lihat atau perbarui →</p></li>
                  <li class="rounded-xl border border-[#dfe6de] bg-white p-3"><div class="flex items-center justify-between gap-2"><span class="font-bold text-[10px] text-slate-900">02 · Jelajah Profesi</span><span class="rounded-full bg-emerald-100 px-2 py-1 text-[8px] font-semibold text-emerald-800">Sudah ada data</span></div><p class="mt-1.5 text-[9px] leading-relaxed text-slate-500">Cari tahu profesi yang membuatmu penasaran dan simpan pilihanmu.</p><p class="mt-2 text-[8px] font-semibold text-[#224f35]">Lihat atau perbarui →</p></li>
                  <li class="rounded-xl border border-[#dfe6de] bg-white p-3"><div class="flex items-center justify-between gap-2"><span class="font-bold text-[10px] text-slate-900">03 · Bandingkan Pilihan</span><span class="rounded-full bg-slate-100 px-2 py-1 text-[8px] font-semibold text-slate-500">Belum dimulai</span></div><p class="mt-1.5 text-[9px] leading-relaxed text-slate-500">Timbang dua pilihan dengan alasan dan kriteria versimu.</p><p class="mt-2 text-[8px] font-semibold text-[#224f35]">Mulai langkah →</p></li>
                  <li class="rounded-xl border border-[#dfe6de] bg-white p-3"><div class="flex items-center justify-between gap-2"><span class="font-bold text-[10px] text-slate-900">04 · Rencana Langkahku</span><span class="rounded-full bg-slate-100 px-2 py-1 text-[8px] font-semibold text-slate-500">Belum dimulai</span></div><p class="mt-1.5 text-[9px] leading-relaxed text-slate-500">Ubah tujuan menjadi dua langkah kecil yang bisa dicoba.</p><p class="mt-2 text-[8px] font-semibold text-[#224f35]">Mulai langkah →</p></li>
                  <li class="rounded-xl border border-[#dfe6de] bg-white p-3"><div class="flex items-center justify-between gap-2"><span class="font-bold text-[10px] text-slate-900">05 · Jurnal Refleksi</span><span class="rounded-full bg-slate-100 px-2 py-1 text-[8px] font-semibold text-slate-500">Belum dimulai</span></div><p class="mt-1.5 text-[9px] font-semibold text-slate-500">Catat hal baru, hambatan, dan dukungan yang kamu butuhkan.</p><p class="mt-2 text-[8px] font-semibold text-[#224f35]">Mulai langkah →</p></li>
                </ol>
              </section>
            </div>
          </div>
        </div>
        <p class="mt-3 text-center text-[10px] font-medium tracking-wide text-slate-500">Ruang eksplorasi untuk mengenali diri, menjajaki pilihan, dan merencanakan langkahmu.</p>
      </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-manfaat">
      <div class="grid items-start gap-8 border-y border-[#dfe6de] py-10 md:grid-cols-[.8fr_1.5fr] md:gap-16">
        <div><span class="text-xs font-bold uppercase tracking-[.18em] text-[#2d6a45]">Kenapa KARVIA?</span><h2 id="judul-manfaat" class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-[#173a24] sm:text-3xl">Pilihannya banyak. Mulai dengan memahami dirimu.</h2></div>
        <p class="text-lg leading-relaxed text-slate-600 sm:text-xl">Eksplorasi karier tidak harus berawal dari jawaban yang pasti. Kamu bisa mulai dari pertanyaan, rasa ingin tahu, dan satu langkah kecil yang terasa mungkin dilakukan.</p>
      </div>
      <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <article class="karvia-card rounded-2xl border border-[#dfe6de] bg-white p-5"><span class="text-xs font-bold uppercase tracking-wider text-[#2d6a45]">01 · Refleksi</span><h3 class="mt-3 font-bold text-[#203628]">Apa yang penting bagiku?</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Kenali hal yang kamu sukai, kekuatanmu, dan lingkungan yang mendukungmu.</p></article>
        <article class="karvia-card rounded-2xl border border-[#dfe6de] bg-white p-5"><span class="text-xs font-bold uppercase tracking-wider text-[#2d6a45]">02 · Eksplorasi</span><h3 class="mt-3 font-bold text-[#203628]">Pilihan apa yang bisa kupelajari?</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Cari informasi tentang profesi dan jalur setelah lulus dengan rasa ingin tahu.</p></article>
        <article class="karvia-card rounded-2xl border border-[#dfe6de] bg-white p-5"><span class="text-xs font-bold uppercase tracking-wider text-[#2d6a45]">03 · Rencana</span><h3 class="mt-3 font-bold text-[#203628]">Apa langkah kecil berikutnya?</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Susun langkah sementara yang bisa dibahas dan ditinjau kembali.</p></article>
      </div>
    </section>

    <section id="privasi" class="mx-auto w-full max-w-7xl scroll-mt-24 px-4 pt-6 sm:px-6 lg:px-8" aria-labelledby="judul-privasi">
      <div class="rounded-2xl border border-[#cce0d1] bg-[#e5efe7] p-5 sm:p-6">
        <div class="flex items-start gap-4">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-[#224f35]"><?php karvia_icon('shield', 'h-5 w-5'); ?></span>
          <div>
            <h2 id="judul-privasi" class="font-bold text-[#173a24]">Eksplorasi, bukan penilaian</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-700">KARVIA adalah media pendukung bimbingan karier—bukan tes psikologis, penentu pilihan otomatis, atau pengganti Guru BK dan konselor. Catatan eksplorasi bersifat personal; Guru BK hanya dapat melihat catatan yang dibagikan siswa sesuai aturan aplikasi.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="tentang" class="mx-auto w-full max-w-7xl scroll-mt-24 px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-tentang">
      <div class="mx-auto max-w-3xl text-center">
        <span class="text-xs font-bold uppercase tracking-[0.18em] text-[#2d6a45]">Tentang KARVIA</span>
        <h2 id="judul-tentang" class="mt-3 text-3xl font-extrabold tracking-tight text-[#173a24] sm:text-4xl">Masa depan tidak harus diputuskan sekaligus.</h2>
        <p class="mt-5 text-base leading-relaxed text-slate-600">Mengenali minat dan kekuatan, mencari tahu berbagai pilihan, lalu mencoba satu langkah kecil dapat membantu menyiapkan bahan untuk berdiskusi dan mengambil keputusan dengan lebih terarah. KARVIA mendampingi proses itu tanpa menentukan pilihan untukmu.</p>
      </div>
      <div class="mx-auto mt-12 grid max-w-5xl gap-4 sm:grid-cols-3">
        <article class="karvia-card rounded-2xl border border-[#dfe6de] bg-white p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#e5efe7] text-[#224f35]"><?php karvia_icon('compass', 'h-5 w-5'); ?></span><h3 class="mt-4 font-bold text-[#203628]">Kenali dirimu</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Mulai dari minat, kekuatan, dan hal yang ingin kamu pelajari.</p></article>
        <article class="karvia-card rounded-2xl border border-[#dfe6de] bg-white p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><?php karvia_icon('search', 'h-5 w-5'); ?></span><h3 class="mt-4 font-bold text-[#203628]">Jelajahi pilihan</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Cari tahu ragam profesi dan jalur yang bisa kamu pertimbangkan.</p></article>
        <article class="karvia-card rounded-2xl border border-[#dfe6de] bg-white p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#e5efe7] text-[#245938]"><?php karvia_icon('map', 'h-5 w-5'); ?></span><h3 class="mt-4 font-bold text-[#203628]">Ambil langkah kecil</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Susun rencana sementara, lalu diskusikan bila kamu membutuhkan dukungan.</p></article>
      </div>
    </section>

    <section id="tahapan" class="scroll-mt-24 border-y border-[#dfe6de] bg-white" aria-labelledby="judul-tahapan">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
        <div class="mx-auto mb-10 max-w-2xl text-center">
          <span class="text-xs font-bold uppercase tracking-[0.18em] text-[#2d6a45]">Alur eksplorasi</span>
          <h2 id="judul-tahapan" class="mt-3 text-3xl font-extrabold tracking-tight text-[#173a24] sm:text-4xl">Langkah kecil untuk mengenal arahmu.</h2>
          <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">Jalani prosesnya secara bertahap. Kamu bisa menggunakan hasil refleksi sebagai bahan diskusi bersama Guru BK.</p>
        </div>
        <ol class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <li class="rounded-2xl border border-[#dfe6de] bg-[#fbfdfb] p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-[#e5efe7] text-[#224f35]"><?php karvia_icon('users', 'h-5 w-5'); ?></span>
            <h3 class="font-bold text-[#203628]">1. Kenali Diriku</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Refleksikan minat, kekuatan, dan hal yang penting bagimu.</p>
          </li>
          <li class="rounded-2xl border border-[#dfe6de] bg-[#fbfdfb] p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><?php karvia_icon('briefcase', 'h-5 w-5'); ?></span>
            <h3 class="font-bold text-[#203628]">2. Jelajah Profesi</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Kenali beragam profesi, aktivitas kerja, dan jalur pendidikan.</p>
          </li>
          <li class="rounded-2xl border border-[#dfe6de] bg-[#fbfdfb] p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-700"><?php karvia_icon('scale', 'h-5 w-5'); ?></span>
            <h3 class="font-bold text-[#203628]">3. Bandingkan Pilihan</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Pertimbangkan beberapa pilihan berdasarkan kriteria yang bermakna bagimu.</p>
          </li>
          <li class="rounded-2xl border border-[#dfe6de] bg-[#fbfdfb] p-5">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><?php karvia_icon('calendar', 'h-5 w-5'); ?></span>
            <h3 class="font-bold text-[#203628]">4. Rencana Langkahku</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Susun tujuan sementara dan langkah kecil yang bisa mulai dilakukan.</p>
          </li>
          <li class="rounded-2xl border border-[#dfe6de] bg-[#fbfdfb] p-5 sm:col-span-2 lg:col-span-2">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-700"><?php karvia_icon('book', 'h-5 w-5'); ?></span>
            <h3 class="font-bold text-[#203628]">5. Jurnal &amp; Konsultasi</h3><p class="mt-2 text-sm leading-relaxed text-slate-600">Refleksikan prosesmu dan bagikan catatan kepada Guru BK bila ingin berdiskusi.</p>
          </li>
        </ol>
      </div>
    </section>

    <section id="pendampingan" class="mx-auto w-full max-w-7xl scroll-mt-24 px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-guru-bk">
      <div class="grid items-center gap-8 rounded-3xl border border-[#dfe6de] bg-white p-6 shadow-sm sm:p-10 lg:grid-cols-[auto_1fr] lg:gap-10">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#e5efe7] text-[#224f35] lg:mx-0"><?php karvia_icon('message', 'h-7 w-7'); ?></span>
        <div class="text-center lg:text-left">
          <h2 id="judul-guru-bk" class="text-2xl font-extrabold tracking-tight text-[#173a24] sm:text-3xl">Perjalananmu lebih bermakna saat bisa dibicarakan.</h2>
          <p class="mt-3 max-w-3xl leading-relaxed text-slate-600">Bagikan ringkasan eksplorasimu kepada Guru BK saat siap berdiskusi. Tinjau pilihan dan langkah berikutnya bersama—keputusan tetap milikmu.</p>
        </div>
      </div>
    </section>

    <section id="artikel" class="scroll-mt-24 border-y border-[#dfe6de] bg-white" aria-labelledby="judul-artikel">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><span class="text-xs font-bold uppercase tracking-[.18em] text-[#2d6a45]">Wawasan KARVIA</span><h2 id="judul-artikel" class="mt-3 text-3xl font-extrabold tracking-tight text-[#173a24] sm:text-4xl">Artikel untuk membuka wawasan.</h2><p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">Baca gagasan dan informasi karier dari Guru BK.</p></div><span class="text-sm font-semibold text-[#2d6a45]">Dari Guru BK KARVIA</span></div>
        <?php if (!$artikel_terbaru): ?>
          <div class="rounded-2xl border border-dashed border-[#cce0d1] bg-[#f6f8f5] p-8 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-[#224f35]"><?php karvia_icon('file', 'h-5 w-5'); ?></span><p class="mt-3 font-semibold text-[#203628]">Artikel edukasi segera hadir.</p><p class="mt-1 text-sm text-slate-500">Guru BK akan membagikan wawasan karier di sini.</p></div>
        <?php else: ?>
          <div class="relative">
            <div id="artikel-track" class="artikel-track -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-3 sm:-mx-6 sm:px-6 md:mx-0 md:gap-5 md:px-0 md:pb-3" aria-label="Artikel terbaru, geser untuk melihat artikel lainnya">
              <?php foreach ($artikel_terbaru as $artikel): $ringkasan = trim(preg_replace('/\s+/u', ' ', strip_tags($artikel['isi']))); ?>
                <article class="karvia-card w-[86%] shrink-0 snap-start overflow-hidden rounded-2xl border border-[#dfe6de] bg-white sm:w-[72%] md:w-[calc((100%-1.25rem)/2)] lg:w-[calc((100%-2.5rem)/3)]">
                  <a href="<?= e(url('artikel.php?id=' . (int) $artikel['id'])) ?>" class="block h-full">
                    <img src="<?= e(url('uploads/artikel/' . rawurlencode($artikel['gambar']))) ?>" alt="Sampul artikel: <?= e($artikel['judul']) ?>" loading="lazy" class="aspect-[16/10] w-full bg-[#eef2ed] object-cover">
                    <div class="p-5">
                      <p class="text-xs font-medium text-slate-500"><?= e(tanggal_id($artikel['diterbitkan_pada'])) ?> <span class="mx-1">·</span> <?= e($artikel['nama_penulis']) ?></p>
                      <h3 class="mt-3 text-lg font-bold leading-snug text-[#203628]"><?= e($artikel['judul']) ?></h3>
                      <p class="artikel-isi mt-2 text-sm leading-relaxed text-slate-600"><?= e($ringkasan) ?></p>
                      <span class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-[#2d6a45]">Baca selengkapnya <?php karvia_icon('arrow-right', 'h-3.5 w-3.5'); ?></span>
                    </div>
                  </a>
                </article>
              <?php endforeach; ?>
            </div>
            <div class="mt-4 flex items-center justify-between">
              <p id="artikel-status" class="text-xs font-medium text-slate-500" aria-live="polite">Artikel 1 dari <?= count($artikel_terbaru) ?></p>
              <div class="flex gap-2">
                <button type="button" data-artikel-arah="-1" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#dfe6de] text-[#224f35] transition hover:bg-[#f2f7f3] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] disabled:opacity-40" aria-label="Artikel sebelumnya"><span aria-hidden="true">←</span></button>
                <button type="button" data-artikel-arah="1" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#dfe6de] text-[#224f35] transition hover:bg-[#f2f7f3] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] disabled:opacity-40" aria-label="Artikel berikutnya"><span aria-hidden="true">→</span></button>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section id="faq" class="scroll-mt-24 border-b border-[#dfe6de] bg-white" aria-labelledby="judul-faq">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[.8fr_1.2fr] lg:px-8">
        <div><span class="text-xs font-bold uppercase tracking-[.18em] text-[#2d6a45]">Pertanyaan umum</span><h2 id="judul-faq" class="mt-3 text-3xl font-extrabold tracking-tight text-[#173a24]">Yang perlu kamu tahu.</h2><p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600">Informasi singkat tentang penggunaan KARVIA, akun, dan catatan eksplorasi.</p></div>
        <div class="divide-y divide-[#dfe6de]">
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-[#203628]"><span>Apa itu KARVIA?</span><span class="text-[#2d6a45] transition-transform"><?php karvia_icon('plus', 'h-4 w-4'); ?></span></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">KARVIA adalah media pendukung bimbingan karier bagi siswa SMA/MA. Kamu dapat menggunakannya untuk refleksi diri, menjelajahi pilihan, dan merencanakan langkah untuk didiskusikan.</p></details>
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-[#203628]"><span>Apakah KARVIA menentukan profesi yang cocok untukku?</span><span class="text-[#2d6a45] transition-transform"><?php karvia_icon('plus', 'h-4 w-4'); ?></span></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">Tidak. KARVIA bukan tes psikologis dan tidak memberi vonis atau rekomendasi profesi otomatis. Pilihan tetap milikmu, dengan dukungan informasi dan diskusi bersama Guru BK.</p></details>
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-[#203628]"><span>Apakah Guru BK langsung melihat catatanku?</span><span class="text-[#2d6a45] transition-transform"><?php karvia_icon('plus', 'h-4 w-4'); ?></span></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">Tidak. Guru BK hanya dapat melihat catatan siswa yang dibagikan melalui KARVIA dan sesuai sekolahnya. Catatan tidak dibagikan otomatis.</p></details>
          <details class="karvia-faq group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-[#203628]"><span>Bagaimana cara mendapatkan akun?</span><span class="text-[#2d6a45] transition-transform"><?php karvia_icon('plus', 'h-4 w-4'); ?></span></summary><p class="mt-3 pr-8 text-sm leading-relaxed text-slate-600">Siswa dapat mendaftar sendiri dengan memilih sekolah yang tersedia di KARVIA. Akun Admin dan Guru BK tetap dikelola oleh Admin sistem.</p></details>
        </div>
      </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8" aria-labelledby="judul-ajakan">
      <div class="rounded-3xl bg-gradient-to-br from-[#173a24] to-emerald-700 px-6 py-12 text-center text-white sm:px-10 sm:py-14">
        <h2 id="judul-ajakan" class="text-2xl font-extrabold sm:text-3xl">Siap mulai mengenali pilihanmu?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-emerald-50 sm:text-base">Masuk ke akun KARVIA untuk melanjutkan perjalanan eksplorasi kariermu.</p>
        <a href="login.php" class="mt-6 inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 font-bold text-[#173a24] transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#173a24]">Masuk ke KARVIA <?php karvia_icon('arrow-right', 'ml-2 h-4 w-4'); ?></a>
      </div>
    </section>
  </main>

  <footer class="overflow-hidden border-t border-[#dfe6de] bg-white">
    <div class="mx-auto max-w-7xl px-4 pb-5 pt-9 sm:px-6 lg:px-8">
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
        <div>
          <a href="#atas" class="inline-flex items-center gap-3" aria-label="KARVIA, kembali ke atas"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#224f35] text-white"><?php karvia_icon('compass', 'h-5 w-5'); ?></span><span class="font-extrabold tracking-tight text-[#203628]">KARVIA</span></a>
          <p class="mt-4 max-w-xs text-sm leading-relaxed text-slate-500">Ruang eksplorasi karier untuk mengenali diri, menjajaki pilihan, dan merencanakan langkah berikutnya.</p>
          <p class="mt-3 text-xs text-slate-400">UIN Raden Intan Lampung</p>
        </div>
        <div><h2 class="text-xs font-bold uppercase tracking-[.14em] text-slate-400">Jelajahi</h2><ul class="mt-4 space-y-3 text-sm text-slate-600"><li><a class="hover:text-[#224f35]" href="#tentang">Tentang KARVIA</a></li><li><a class="hover:text-[#224f35]" href="#tahapan">Tahapan eksplorasi</a></li><li><a class="hover:text-[#224f35]" href="#artikel">Artikel terbaru</a></li></ul></div>
        <div><h2 class="text-xs font-bold uppercase tracking-[.14em] text-slate-400">Pendampingan</h2><ul class="mt-4 space-y-3 text-sm text-slate-600"><li><a class="hover:text-[#224f35]" href="#pendampingan">Bersama Guru BK</a></li><li><a class="hover:text-[#224f35]" href="#privasi">Privasi catatan</a></li><li><a class="hover:text-[#224f35]" href="#faq">Pertanyaan umum</a></li></ul></div>
        <div><h2 class="text-xs font-bold uppercase tracking-[.14em] text-slate-400">Akun</h2><ul class="mt-4 space-y-3 text-sm text-slate-600"><li><a class="hover:text-[#224f35]" href="login.php">Masuk ke KARVIA</a></li><li><a class="hover:text-[#224f35]" href="#atas">Kembali ke atas</a></li></ul></div>
      </div>
      <div class="mt-9 h-px bg-[#dfe6de]"></div>
      <div class="flex flex-col gap-2 py-4 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between"><span>KARVIA — Career Vision &amp; Action</span><a class="hover:text-[#224f35]" href="#faq">Pertanyaan umum</a><span>Media pendukung bimbingan karier siswa SMA/MA.</span></div>
    </div>
    <div class="select-none px-2 text-center text-[16vw] font-extrabold leading-[.8] tracking-[-.09em] text-[#173a24]/[.045] sm:text-[15vw]" aria-hidden="true">KARVIA</div>
  </footer>
  <script>
    (() => {
      document.documentElement.classList.add('motion-ready');
      const progress = document.getElementById('scroll-progress');
      if (!progress) return;
      let scheduled = false;
      const header = document.getElementById('atas');
      const update = () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.transform = `scaleX(${max > 0 ? window.scrollY / max : 0})`;
        header.classList.toggle('scrolled', window.scrollY > 12);
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

    const heroMockup = document.querySelector('#konten>section:first-child .karvia-dashboard');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (heroMockup && !reducedMotion) {
      const heroSection = heroMockup.closest('section');
      const heroType = document.querySelector('.hero-typography');
      let scheduled = false;
      let paused = document.visibilityState !== 'visible';
      const tiltMockup = () => {
        if (paused) return;
        const rect = heroSection.getBoundingClientRect();
        const progress = Math.max(0, Math.min(1, (window.innerHeight - rect.top) / (window.innerHeight + rect.height)));
        heroMockup.style.setProperty('--mockup-tilt', `${2.5 * (1 - progress)}deg`);
        if (heroType) heroType.style.setProperty('--hero-drift', `${progress * 36 - 18}px`);
        scheduled = false;
      };
      const scheduleTilt = () => {
        if (paused || scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(tiltMockup);
      };
      document.addEventListener('visibilitychange', () => {
        paused = document.visibilityState !== 'visible';
        if (!paused) scheduleTilt();
      });
      window.addEventListener('scroll', scheduleTilt, { passive: true });
      window.addEventListener('resize', scheduleTilt, { passive: true });
      tiltMockup();
    }

    const revealTargets = document.querySelectorAll('#konten>section:not(:first-child) h2, #konten>section:not(:first-child) article, #konten>section:not(:first-child) li, #konten>section:not(:first-child) details, #konten>section:not(:first-child) [aria-label="Progres eksplorasi mockup"]');
    if (!reducedMotion && 'IntersectionObserver' in window) {
      const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
      revealTargets.forEach((element, index) => {
        element.classList.add('karvia-reveal');
        element.style.setProperty('--reveal-delay', `${Math.min(index % 5, 3) * 70}ms`);
        revealObserver.observe(element);
      });
    }

    const menu = document.getElementById('menu-mobile');
    const tombolMenu = document.getElementById('menu-buka');
    const tombolTutup = document.getElementById('menu-tutup');
    if (menu && tombolMenu && tombolTutup) {
      tombolMenu.addEventListener('click', () => {
        menu.showModal();
        tombolMenu.setAttribute('aria-expanded', 'true');
        tombolTutup.focus();
      });
      const tutupMenu = () => {
        if (menu.open) menu.close();
        tombolMenu.setAttribute('aria-expanded', 'false');
        tombolMenu.focus();
      };
      tombolTutup.addEventListener('click', tutupMenu);
      menu.querySelectorAll('a').forEach((tautan) => tautan.addEventListener('click', () => {
        if (menu.open) menu.close();
        tombolMenu.setAttribute('aria-expanded', 'false');
      }));
      menu.addEventListener('click', (event) => {
        const batasDialog = menu.getBoundingClientRect();
        if (event.clientX < batasDialog.left || event.clientX > batasDialog.right || event.clientY < batasDialog.top || event.clientY > batasDialog.bottom) tutupMenu();
      });
      menu.addEventListener('close', () => {
        tombolMenu.setAttribute('aria-expanded', 'false');
        if (window.matchMedia('(min-width: 768px)').matches) return;
        tombolMenu.focus();
      });
    }

    const artikelTrack = document.getElementById('artikel-track');
    const artikelStatus = document.getElementById('artikel-status');
    if (artikelTrack && artikelStatus) {
      const kartuArtikel = [...artikelTrack.querySelectorAll('article')];
      const kontrolArtikel = [...document.querySelectorAll('[data-artikel-arah]')];
      let artikelAktif = 0;
      const perbaruiArtikel = () => {
        const tengahTrack = artikelTrack.getBoundingClientRect().left;
        artikelAktif = kartuArtikel.reduce((terdekat, kartu, indeks) => {
          const jarak = Math.abs(kartu.getBoundingClientRect().left - tengahTrack);
          return jarak < Math.abs(kartuArtikel[terdekat].getBoundingClientRect().left - tengahTrack) ? indeks : terdekat;
        }, 0);
        artikelStatus.textContent = `Artikel ${artikelAktif + 1} dari ${kartuArtikel.length}`;
        const desktop = window.matchMedia('(min-width: 768px)').matches;
        const perTampilan = desktop ? (window.matchMedia('(min-width: 1024px)').matches ? 3 : 2) : 1;
        const indeksMaks = Math.max(0, kartuArtikel.length - perTampilan);
        kontrolArtikel.forEach((tombol) => {
          const arah = Number(tombol.dataset.artikelArah);
          tombol.disabled = arah < 0 ? artikelAktif === 0 : artikelAktif >= indeksMaks;
        });
      };
      const perbaruiTampilanCarousel = () => {
        const desktop = window.matchMedia('(min-width: 768px)').matches;
        const perTampilan = desktop ? (window.matchMedia('(min-width: 1024px)').matches ? 3 : 2) : 1;
        const indeksMaks = Math.max(0, kartuArtikel.length - perTampilan);
        kontrolArtikel.forEach((tombol) => {
          const arah = Number(tombol.dataset.artikelArah);
          tombol.disabled = arah < 0 ? artikelAktif === 0 : artikelAktif >= indeksMaks;
        });
      };
      artikelTrack.addEventListener('scroll', () => window.requestAnimationFrame(perbaruiArtikel), { passive: true });
      kontrolArtikel.forEach((tombol) => tombol.addEventListener('click', () => {
        const desktop = window.matchMedia('(min-width: 768px)').matches;
        const perTampilan = desktop ? (window.matchMedia('(min-width: 1024px)').matches ? 3 : 2) : 1;
        const indeksMaks = Math.max(0, kartuArtikel.length - perTampilan);
        const tujuan = Math.max(0, Math.min(indeksMaks, artikelAktif + Number(tombol.dataset.artikelArah) * perTampilan));
        kartuArtikel[tujuan].scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'nearest', inline: 'start' });
      }));
      window.addEventListener('resize', perbaruiArtikel, { passive: true });
      perbaruiArtikel();
    }
  </script>
</body>
</html>
