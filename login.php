<?php
/** Tampilan login KARVIA; autentikasi belum dihubungkan. */
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — KARVIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { karvia: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d' } } } } };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-karvia-900 via-emerald-800 to-slate-900 text-slate-800">
  <main class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 text-white text-3xl shadow-xl mb-4">
        <i class="fa-solid fa-compass text-emerald-400"></i>
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight">KARVIA</h1>
      <p class="text-xs font-semibold uppercase tracking-wider text-emerald-300 mt-1">Career Vision &amp; Action</p>
      <p class="mt-2 text-xs text-emerald-100/90 leading-relaxed">Pengembangan Media Digital Bimbingan Karier (Langkah Karierku)<br><span class="text-emerald-300">UIN Raden Intan Lampung (Chindy Theresia - 2411080092)</span></p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
      <div class="bg-white/95 backdrop-blur-xl py-8 px-6 shadow-2xl rounded-3xl sm:px-10 border border-white/50">
        <div class="mb-6">
          <h2 class="text-xl font-bold text-slate-900">Selamat datang</h2>
          <p class="text-sm text-slate-500 mt-1">Masuk menggunakan akun KARVIA Anda.</p>
        </div>
        <form class="space-y-5" onsubmit="event.preventDefault(); document.getElementById('demoNote').classList.remove('hidden');">
          <div>
            <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-user"></i></span>
              <input id="username" name="username" type="text" autocomplete="username" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:border-karvia-500" placeholder="Masukkan username" required>
            </div>
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-lock"></i></span>
              <input id="password" name="password" type="password" autocomplete="current-password" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:border-karvia-500" placeholder="Masukkan password" required>
            </div>
          </div>
          <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-karvia-600 hover:bg-karvia-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-karvia-500 transition-all">
            Masuk ke KARVIA <i class="fa-solid fa-arrow-right ml-2 mt-0.5"></i>
          </button>
          <p id="demoNote" class="hidden rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800" role="status">Ini masih tampilan prototipe. Login belum terhubung ke pemeriksaan akun.</p>
        </form>
        <div class="mt-6 border-t border-slate-200 pt-4 text-center">
          <p class="text-xs text-slate-500"><i class="fa-solid fa-shield-halved text-karvia-600 mr-1"></i>Data eksplorasi aman, dijaga kerahasiaannya, dan digunakan untuk bimbingan karier.</p>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
