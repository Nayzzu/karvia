<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(404);
    exit('Artikel tidak ditemukan.');
}
$stmt = db()->prepare('SELECT judul, gambar, isi, nama_penulis, diterbitkan_pada FROM artikel WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$artikel = $stmt->fetch();
if (!$artikel) {
    http_response_code(404);
    exit('Artikel tidak ditemukan.');
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e(mb_substr(trim(strip_tags($artikel['isi'])), 0, 155)) ?>">
  <title><?= e($artikel['judul']) ?> — KARVIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{karvia:{50:'#f0fdf4',100:'#dcfce7',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
<header class="border-b border-slate-200 bg-white"><div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6"><a href="<?= e(url('index.php')) ?>" class="flex items-center gap-3 font-extrabold text-karvia-800"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-karvia-700 text-white"><i class="fa-solid fa-compass" aria-hidden="true"></i></span>KARVIA</a><a href="<?= e(url('index.php#artikel')) ?>" class="text-sm font-semibold text-karvia-700 hover:underline">← Kembali ke artikel</a></div></header>
<main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-16"><article><img src="<?= e(url('uploads/artikel/' . rawurlencode($artikel['gambar']))) ?>" alt="Sampul: <?= e($artikel['judul']) ?>" class="mb-8 aspect-[16/9] w-full rounded-3xl bg-slate-200 object-cover"><p class="text-xs font-bold uppercase tracking-[.16em] text-karvia-700">Wawasan KARVIA</p><h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl"><?= e($artikel['judul']) ?></h1><p class="mt-5 text-sm text-slate-500">Oleh <span class="font-semibold text-slate-700"><?= e($artikel['nama_penulis']) ?></span> · <?= e(tanggal_id($artikel['diterbitkan_pada'])) ?></p><div class="mt-8 whitespace-pre-line text-base leading-8 text-slate-700"><?= e($artikel['isi']) ?></div></article><a href="<?= e(url('index.php#artikel')) ?>" class="mt-10 inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:border-karvia-500 hover:text-karvia-800">← Kembali ke artikel</a></main>
<footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">KARVIA — Career Vision &amp; Action · UIN Raden Intan Lampung</footer>
</body></html>
