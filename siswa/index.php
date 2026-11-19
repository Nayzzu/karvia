<?php
/** Tampilan beranda Siswa; belum terhubung ke data/session. */
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beranda Siswa — KARVIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{karvia:{50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
  <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="../index.php" class="flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-karvia-600 text-white flex items-center justify-center shadow-md"><i class="fa-solid fa-compass text-lg"></i></span><span><strong class="block leading-tight">KARVIA</strong><small class="text-xs text-slate-500">Career Vision &amp; Action</small></span></a>
      <div class="flex items-center gap-3"><div class="text-right hidden sm:block"><div class="text-sm font-semibold">Nama Siswa</div><span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Siswa SMA/MA</span></div><a href="../logout.php" class="p-2.5 rounded-xl bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-600" title="Keluar"><i class="fa-solid fa-right-from-bracket"></i></a></div>
    </div>
  </header>
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row min-h-[calc(100vh-4rem)]">
    <aside class="md:w-64 bg-white border-r border-slate-200 p-4 shrink-0">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 py-2">Menu Eksplorasi</p>
      <nav class="space-y-1 text-sm">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-karvia-50 text-karvia-700 font-semibold"><i class="fa-solid fa-house w-5 text-center"></i>Beranda &amp; Pengantar</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-user-check w-5 text-center"></i>1. Kenali Diriku</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-briefcase w-5 text-center"></i>2. Jelajah Profesi</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-graduation-cap w-5 text-center"></i>Jalur Setelah Lulus</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-scale-balanced w-5 text-center"></i>3. Bandingkan Pilihan</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-list-check w-5 text-center"></i>4. Rencana Langkahku</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-book-bookmark w-5 text-center"></i>5. Jurnal Refleksi</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-comments w-5 text-center"></i>Ruang Konsultasi BK</a>
        <a href="#langkah" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-lightbulb w-5 text-center"></i>Tips &amp; Edukasi Karier</a>
      </nav>
    </aside>
    <main class="flex-grow p-4 sm:p-8 space-y-6">
      <section class="bg-gradient-to-r from-karvia-900 to-emerald-700 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10"><i class="fa-solid fa-compass text-[200px]"></i></div>
        <div class="relative z-10 max-w-2xl"><span class="bg-emerald-500/30 text-emerald-100 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-3 border border-emerald-400/30">KARVIA · Langkah Karierku</span><h1 class="text-3xl sm:text-4xl font-extrabold mb-3">Kenali diri, jelajahi pilihan, mulai dari satu langkah.</h1><p class="text-emerald-100 text-sm sm:text-base leading-relaxed mb-6">Selamat datang di media digital bimbingan karier untuk peserta didik SMA/MA. Temukan visi masa depanmu, eksplorasi potensi diri, bandingkan pilihan, dan rancang langkah konkret bersama Guru BK.</p><a href="#langkah" class="inline-flex px-6 py-3 bg-white text-karvia-900 font-bold rounded-xl shadow-lg hover:bg-emerald-50">Mulai Eksplorasi <i class="fa-solid fa-arrow-right ml-2 mt-1"></i></a></div>
      </section>
      <section class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-2xl shadow-sm"><div class="flex items-start"><i class="fa-solid fa-circle-info text-amber-500 text-xl mr-3 mt-0.5"></i><div><h2 class="font-bold text-amber-900 text-sm">Tentang KARVIA</h2><p class="text-xs text-amber-800 mt-1 leading-relaxed">KARVIA adalah media pendukung bimbingan karier, bukan tes psikologis atau penentu pilihan otomatis. Catatan eksplorasi bersifat personal dan dapat didiskusikan dengan Guru BK.</p></div></div></section>
      <section id="langkah" class="space-y-4"><div><h2 class="text-xl font-extrabold text-slate-900">Langkah Eksplorasi Karier</h2><p class="text-sm text-slate-500 mt-1">Isi setiap bagian sesuai pemikiranmu. Tampilan langkah lain akan tersedia di halaman masing-masing.</p></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
          <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-karvia-50 text-karvia-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-user-check"></i></div><p class="text-xs font-bold text-karvia-700 mb-1">LANGKAH 1</p><h3 class="font-bold mb-1">Kenali Diriku</h3><p class="text-xs text-slate-500 leading-relaxed">Refleksi minat, kekuatan, dan nilai pribadi.</p></article>
          <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-briefcase"></i></div><p class="text-xs font-bold text-blue-700 mb-1">LANGKAH 2</p><h3 class="font-bold mb-1">Jelajah Profesi</h3><p class="text-xs text-slate-500 leading-relaxed">Eksplorasi tugas, kemampuan, dan jalur pendidikan.</p></article>
          <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-scale-balanced"></i></div><p class="text-xs font-bold text-purple-700 mb-1">LANGKAH 3</p><h3 class="font-bold mb-1">Bandingkan Pilihan</h3><p class="text-xs text-slate-500 leading-relaxed">Pertimbangkan dua pilihan dengan kriteria pribadi.</p></article>
          <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-list-check"></i></div><p class="text-xs font-bold text-amber-700 mb-1">LANGKAH 4</p><h3 class="font-bold mb-1">Rencana Langkahku</h3><p class="text-xs text-slate-500 leading-relaxed">Susun tujuan dan langkah kecil yang dapat dilakukan.</p></article>
        </div>
        <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-start gap-4"><div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0"><i class="fa-solid fa-book-bookmark"></i></div><div><p class="text-xs font-bold text-rose-700 mb-1">LANGKAH 5</p><h3 class="font-bold mb-1">Jurnal Refleksi</h3><p class="text-xs text-slate-500 leading-relaxed">Catat hal baru, hambatan, dan dukungan yang kamu temukan.</p></div></article>
      </section>
      <p class="text-xs text-slate-400 text-center">Ini masih tampilan awal. Form dan tautan belum menyimpan atau memuat data.</p>
    </main>
  </div>
</body>
</html>
