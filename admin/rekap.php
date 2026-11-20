<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'rekap';
$judul_halaman = 'Rekap KARVIA';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$ringkasan = $pdo->query(
    "SELECT
       (SELECT COUNT(*) FROM sekolah) AS jumlah_sekolah,
       (SELECT COUNT(*) FROM pengguna WHERE role = 'siswa') AS jumlah_siswa,
       (SELECT COUNT(*) FROM pengguna WHERE role = 'guru_bk') AS jumlah_guru,
       (SELECT COUNT(*) FROM berbagi_catatan b JOIN pengguna p ON p.id = b.siswa_id AND p.sekolah_id = b.sekolah_id) AS jumlah_catatan"
)->fetch();
$sekolah = $pdo->query(
    "SELECT s.id, s.nama, s.jenjang, s.kota, s.status,
       SUM(CASE WHEN p.role = 'siswa' THEN 1 ELSE 0 END) AS siswa,
       SUM(CASE WHEN p.role = 'siswa' AND p.status = 'aktif' THEN 1 ELSE 0 END) AS siswa_aktif,
       SUM(CASE WHEN p.role = 'guru_bk' THEN 1 ELSE 0 END) AS guru,
       SUM(CASE WHEN p.role = 'guru_bk' AND p.status = 'aktif' THEN 1 ELSE 0 END) AS guru_aktif,
       (SELECT COUNT(*) FROM berbagi_catatan b JOIN pengguna ps ON ps.id = b.siswa_id
        WHERE b.sekolah_id = s.id AND ps.sekolah_id = s.id) AS catatan
     FROM sekolah s LEFT JOIN pengguna p ON p.sekolah_id = s.id
     GROUP BY s.id, s.nama, s.jenjang, s.kota, s.status ORDER BY s.nama"
)->fetchAll();
?>
<div class="mb-6"><h1 class="text-2xl font-extrabold text-slate-900">Rekap KARVIA</h1><p class="mt-1 text-sm text-slate-600">Ringkasan jumlah akun dan catatan dibagikan. Isi jurnal/refleksi siswa tidak ditampilkan.</p></div>
<section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan seluruh sekolah"><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">Sekolah</p><p class="mt-2 text-3xl font-extrabold"><?= e($ringkasan['jumlah_sekolah']) ?></p></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">Siswa</p><p class="mt-2 text-3xl font-extrabold"><?= e($ringkasan['jumlah_siswa']) ?></p></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">Guru BK</p><p class="mt-2 text-3xl font-extrabold"><?= e($ringkasan['jumlah_guru']) ?></p></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">Catatan Dibagikan</p><p class="mt-2 text-3xl font-extrabold"><?= e($ringkasan['jumlah_catatan']) ?></p></article></section>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="border-b border-slate-100 p-5"><h2 class="font-bold text-slate-900">Rincian per Sekolah</h2></div><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-4">Sekolah</th><th class="p-4">Status</th><th class="p-4">Siswa aktif / total</th><th class="p-4">Guru BK aktif / total</th><th class="p-4">Catatan dibagikan</th></tr></thead><tbody><?php if (!$sekolah): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada data sekolah.</td></tr><?php else: foreach ($sekolah as $row): ?><tr class="border-t border-slate-100"><td class="p-4"><a href="<?= e(url('admin/sekolah_detail.php?id=' . (int) $row['id'])) ?>" class="font-semibold text-emerald-800 hover:underline"><?= e($row['nama']) ?></a><p class="text-xs text-slate-500"><?= e($row['jenjang'] . ' · ' . ($row['kota'] ?: 'Lokasi belum diisi')) ?></p></td><td class="p-4"><?= e(ucfirst($row['status'])) ?></td><td class="p-4"><?= e($row['siswa_aktif']) ?> / <?= e($row['siswa']) ?></td><td class="p-4"><?= e($row['guru_aktif']) ?> / <?= e($row['guru']) ?></td><td class="p-4"><?= e($row['catatan']) ?></td></tr><?php endforeach; endif; ?></tbody></table></div></section>
<div class="mt-5 flex flex-wrap gap-3 text-sm"><a class="font-semibold text-emerald-800 hover:underline" href="<?= e(url('admin/pengguna.php')) ?>">Kelola pengguna</a><a class="font-semibold text-emerald-800 hover:underline" href="<?= e(url('admin/sekolah.php')) ?>">Kelola sekolah</a></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
