<?php
/** Halaman awal KARVIA; navigasi autentikasi akan ditambahkan kemudian. */
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
    <section class="bg-gradient-to-r from-karvia-900 to-emerald-700 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10"><i class="fa-solid fa-compass text-[220px]"></i></div>
      <div class="relative z-10 max-w-2xl">
        <span class="bg-emerald-500/30 text-emerald-100 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-3 border border-emerald-400/30">KARVIA · Langkah Karierku</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-3 leading-tight">Kenali diri, jelajahi pilihan, mulai dari satu langkah.</h1>
        <p class="text-emerald-100 text-sm sm:text-base leading-relaxed mb-6">Media digital bimbingan karier untuk peserta didik SMA/MA. Temukan visi masa depanmu, eksplorasi potensi diri, bandingkan pilihan secara terstruktur, dan rancang langkah konkret bersama Guru BK.</p>
        <a href="login.php" class="inline-flex items-center px-6 py-3 bg-white text-karvia-900 font-bold rounded-xl shadow-lg hover:bg-emerald-50 transition">Masuk ke KARVIA <i class="fa-solid fa-arrow-right ml-2"></i></a>
      </div>
    </section>
    <section class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-2xl shadow-sm">
      <div class="flex items-start"><i class="fa-solid fa-circle-info text-amber-500 text-xl mr-3 mt-0.5"></i><div><h2 class="font-bold text-amber-900 text-sm">Landasan &amp; Batasan Produk</h2><p class="text-xs text-amber-800 mt-1 leading-relaxed">KARVIA adalah media pendukung layanan bimbingan karier, bukan alat tes psikologis, bukan penentu jurusan otomatis, dan bukan pengganti konselor. Catatan eksplorasi bersifat personal dan dapat didiskusikan dengan Guru BK.</p></div></div>
    </section>
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Tahapan eksplorasi">
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-karvia-50 text-karvia-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-user-check"></i></div><h2 class="font-bold mb-1">1. Kenali Diriku</h2><p class="text-xs text-slate-500 leading-relaxed">Refleksi minat, kekuatan, dan nilai pribadi.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-briefcase"></i></div><h2 class="font-bold mb-1">2. Jelajah Profesi</h2><p class="text-xs text-slate-500 leading-relaxed">Kenali tugas dan jalur pendidikan berbagai profesi.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-scale-balanced"></i></div><h2 class="font-bold mb-1">3. Bandingkan Pilihan</h2><p class="text-xs text-slate-500 leading-relaxed">Pertimbangkan pilihan berdasarkan kriteria pribadi.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-list-check"></i></div><h2 class="font-bold mb-1">4. Rencanakan Langkah</h2><p class="text-xs text-slate-500 leading-relaxed">Susun tujuan dan langkah kecil yang bisa dilakukan.</p></article>
    </section>
  </main>
  <footer class="text-center text-xs text-slate-500 py-5">KARVIA — Career Vision &amp; Action · UIN Raden Intan Lampung</footer>
</body>
</html>
