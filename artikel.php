<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(404);
    exit('Artikel tidak ditemukan.');
}
$stmt = db()->prepare('SELECT id, judul, gambar, isi, nama_penulis, diterbitkan_pada FROM artikel WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$artikel = $stmt->fetch();
if (!$artikel) {
    http_response_code(404);
    exit('Artikel tidak ditemukan.');
}

try {
    $stmt_lain = db()->prepare('SELECT id, judul, gambar, nama_penulis, diterbitkan_pada FROM artikel WHERE id <> :id ORDER BY diterbitkan_pada DESC LIMIT 3');
    $stmt_lain->execute(['id' => $id]);
    $artikel_lain = $stmt_lain->fetchAll();
} catch (PDOException $kesalahan) {
    error_log('KARVIA: artikel lain gagal dimuat: ' . $kesalahan->getMessage());
    $artikel_lain = [];
}

$gambar_url = url('uploads/artikel/' . rawurlencode($artikel['gambar']));
$skema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$alamat_absolut = $skema . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('artikel.php?id=' . (int) $artikel['id']);
$gambar_absolut = $skema . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $gambar_url;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e(mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($artikel['isi']))), 0, 155)) ?>">
  <meta name="theme-color" content="#224f35">
  <title><?= e($artikel['judul']) ?> — KARVIA</title>
  <meta property="og:type" content="article">
  <meta property="og:site_name" content="KARVIA">
  <meta property="og:title" content="<?= e($artikel['judul']) ?>">
  <meta property="og:description" content="<?= e(mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($artikel['isi']))), 0, 155)) ?>">
  <meta property="og:url" content="<?= e($alamat_absolut) ?>">
  <meta property="og:image" content="<?= e($gambar_absolut) ?>">
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
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="flex min-h-screen flex-col bg-[#f6f8f5] text-slate-900 antialiased">
  <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-[#224f35] focus:px-4 focus:py-2.5 focus:text-sm focus:font-bold focus:text-white">Lewati ke konten</a>

  <header class="sticky top-0 z-40 border-b border-[#dfe6de] bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex min-h-[72px] max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
      <a href="<?= e(url('index.php')) ?>" class="flex shrink-0 items-center gap-3" aria-label="KARVIA, kembali ke beranda">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#224f35] text-white shadow-sm"><?php karvia_icon('compass', 'h-5 w-5'); ?></span>
        <span><strong class="block leading-tight tracking-tight text-[#203628]">KARVIA</strong><small class="text-xs text-slate-500">Career Vision &amp; Action</small></span>
      </a>
      <nav class="hidden items-center gap-7 text-sm font-medium text-slate-600 sm:flex" aria-label="Navigasi halaman">
        <a class="transition hover:text-[#224f35]" href="<?= e(url('index.php#tentang')) ?>">Tentang</a>
        <a class="transition hover:text-[#224f35]" href="<?= e(url('index.php#artikel')) ?>">Artikel</a>
        <a class="transition hover:text-[#224f35]" href="<?= e(url('index.php#faq')) ?>">FAQ</a>
      </nav>
      <a href="<?= e(url('login.php')) ?>" class="inline-flex shrink-0 items-center rounded-xl bg-[#224f35] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#1d482d] focus:outline-none focus:ring-2 focus:ring-[#2d6a45] focus:ring-offset-2">Masuk <?php karvia_icon('arrow-right', 'ml-2 h-4 w-4'); ?></a>
    </div>
  </header>

  <main id="konten" class="flex-grow">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-16">
      <nav class="mb-6 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
        <a class="hover:text-[#224f35]" href="<?= e(url('index.php')) ?>">Beranda</a>
        <span class="mx-1.5">/</span>
        <a class="hover:text-[#224f35]" href="<?= e(url('index.php#artikel')) ?>">Artikel</a>
        <span class="mx-1.5">/</span>
        <span class="text-slate-700"><?= e($artikel['judul']) ?></span>
      </nav>

      <article>
        <img src="<?= e($gambar_url) ?>" alt="Sampul artikel: <?= e($artikel['judul']) ?>" class="aspect-[16/9] w-full rounded-2xl border border-[#dfe6de] bg-[#eef2ed] object-cover">
        <p class="mt-7 text-xs font-bold uppercase tracking-[.16em] text-[#2d6a45]">Wawasan KARVIA</p>
        <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-[#173a24] sm:text-4xl"><?= e($artikel['judul']) ?></h1>
        <p class="mt-4 text-sm text-slate-500">Oleh <span class="font-semibold text-slate-700"><?= e($artikel['nama_penulis']) ?></span> <span class="mx-1">·</span> <?= e(tanggal_id($artikel['diterbitkan_pada'])) ?></p>
        <div class="mt-8 whitespace-pre-line text-base leading-8 text-slate-700"><?= e($artikel['isi']) ?></div>
      </article>

      <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-[#dfe6de] pt-6">
        <a href="<?= e(url('index.php#artikel')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-[#dfe6de] bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-[#c3d8c8] hover:text-[#224f35]"><?php karvia_icon('arrow-right', 'h-4 w-4 rotate-180'); ?> Kembali ke artikel</a>
        <a href="<?= e(url('login.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-[#224f35] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#1d482d]">Masuk ke KARVIA <?php karvia_icon('arrow-right', 'h-4 w-4'); ?></a>
      </div>
    </div>

    <?php if ($artikel_lain): ?>
      <section class="border-t border-[#dfe6de] bg-white" aria-labelledby="judul-lain">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
          <h2 id="judul-lain" class="text-2xl font-extrabold tracking-tight text-[#173a24]">Artikel lainnya</h2>
          <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($artikel_lain as $lain): ?>
              <article class="overflow-hidden rounded-2xl border border-[#dfe6de] bg-white transition hover:border-[#c3d8c8] hover:shadow-md">
                <a href="<?= e(url('artikel.php?id=' . (int) $lain['id'])) ?>" class="block">
                  <img src="<?= e(url('uploads/artikel/' . rawurlencode($lain['gambar']))) ?>" alt="Sampul artikel: <?= e($lain['judul']) ?>" loading="lazy" class="aspect-[16/10] w-full bg-[#eef2ed] object-cover">
                  <div class="p-5">
                    <p class="text-xs font-medium text-slate-500"><?= e(tanggal_id($lain['diterbitkan_pada'])) ?> <span class="mx-1">·</span> <?= e($lain['nama_penulis']) ?></p>
                    <h3 class="mt-2 font-bold leading-snug text-[#203628]"><?= e($lain['judul']) ?></h3>
                    <span class="mt-3 inline-flex items-center gap-2 text-sm font-bold text-[#2d6a45]">Baca <?php karvia_icon('arrow-right', 'h-3.5 w-3.5'); ?></span>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </main>

  <footer class="border-t border-[#dfe6de] bg-white">
    <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
      <span>KARVIA — Career Vision &amp; Action · UIN Raden Intan Lampung</span>
      <div class="flex flex-wrap items-center gap-4">
        <a class="hover:text-[#224f35]" href="<?= e(url('index.php#artikel')) ?>">Artikel</a>
        <a class="hover:text-[#224f35]" href="<?= e(url('index.php#faq')) ?>">FAQ</a>
        <a class="hover:text-[#224f35]" href="<?= e(url('login.php')) ?>">Masuk</a>
      </div>
    </div>
  </footer>
</body>
</html>
