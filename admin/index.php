<?php
/** Tampilan dashboard Admin; angka belum dihubungkan ke database. */
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin — KARVIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{karvia:{50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
  <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="../index.php" class="flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center shadow-md"><i class="fa-solid fa-compass text-lg"></i></span><span><strong class="block leading-tight">KARVIA</strong><small class="text-xs text-slate-500">Panel Administrasi</small></span></a>
      <div class="flex items-center gap-3"><div class="text-right hidden sm:block"><div class="text-sm font-semibold">Administrator</div><span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-white">Admin</span></div><a href="../logout.php" class="p-2.5 rounded-xl bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-600" title="Keluar"><i class="fa-solid fa-right-from-bracket"></i></a></div>
    </div>
  </header>
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <section class="bg-gradient-to-r from-slate-900 to-slate-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl"><span class="bg-white/10 text-slate-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-2 border border-white/20">Administrasi KARVIA</span><h1 class="text-2xl sm:text-3xl font-extrabold">Panel Admin</h1><p class="text-slate-300 text-sm mt-2 max-w-2xl">Pantau sekolah dan akun pengguna KARVIA. Informasi yang ditampilkan di panel ini berupa data akun dan rekap, bukan isi jurnal pribadi siswa.</p></section>
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Ringkasan sistem">
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="flex justify-between"><span class="text-slate-400 text-xs font-semibold uppercase">Sekolah Aktif</span><i class="fa-solid fa-school text-emerald-600"></i></div><div class="text-2xl font-extrabold text-slate-900 mt-2">—</div><p class="text-xs text-slate-500 mt-1">Akan dihitung dari database.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="flex justify-between"><span class="text-slate-400 text-xs font-semibold uppercase">Akun Terdaftar</span><i class="fa-solid fa-users text-blue-600"></i></div><div class="text-2xl font-extrabold text-slate-900 mt-2">—</div><p class="text-xs text-slate-500 mt-1">Rincian siswa dan Guru BK.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="flex justify-between"><span class="text-slate-400 text-xs font-semibold uppercase">Pernah Masuk</span><i class="fa-solid fa-right-to-bracket text-purple-600"></i></div><div class="text-2xl font-extrabold text-slate-900 mt-2">—</div><p class="text-xs text-slate-500 mt-1">Akun yang pernah login.</p></article>
      <article class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="flex justify-between"><span class="text-slate-400 text-xs font-semibold uppercase">Aktif 30 Hari</span><i class="fa-solid fa-chart-line text-amber-600"></i></div><div class="text-2xl font-extrabold text-slate-900 mt-2">—</div><p class="text-xs text-slate-500 mt-1">Akan dihitung dari aktivitas login.</p></article>
    </section>
    <section class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="p-6 border-b border-slate-100"><h2 class="font-bold text-slate-900 text-lg">Rekap per Sekolah</h2><p class="text-xs text-slate-500 mt-1">Ringkasan akun dan aktivitas, tanpa menampilkan isi refleksi atau jurnal siswa.</p></div>
      <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200"><th class="p-4">Nama Sekolah</th><th class="p-4">Jenjang / Kota</th><th class="p-4">Siswa</th><th class="p-4">Guru BK</th><th class="p-4">Catatan Dibagikan</th><th class="p-4">Aktivitas Terakhir</th></tr></thead><tbody><tr><td colspan="6" class="p-10 text-center"><div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl"><i class="fa-solid fa-school"></i></div><p class="font-semibold text-slate-700">Rekap sekolah akan tampil di sini</p><p class="text-xs text-slate-500 mt-1">Data belum dimuat karena tampilan ini belum terhubung ke database.</p></td></tr></tbody></table></div>
    </section>
    <p class="text-xs text-slate-400 text-center">Tampilan dashboard saja — angka dan rekap belum mengambil data nyata.</p>
  </main>
</body>
</html>
