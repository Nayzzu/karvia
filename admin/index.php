<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'index';
$judul_halaman = 'Dashboard Admin';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$stat = $pdo->query(
    "SELECT
       (SELECT COUNT(*) FROM sekolah WHERE status = 'aktif') AS sekolah_aktif,
       (SELECT COUNT(*) FROM pengguna) AS total_akun,
       (SELECT COUNT(*) FROM pengguna WHERE role = 'siswa') AS total_siswa,
       (SELECT COUNT(*) FROM pengguna WHERE role = 'guru_bk') AS total_guru,
       (SELECT COUNT(*) FROM pengguna WHERE last_login_at IS NOT NULL) AS pernah_login,
       (SELECT COUNT(*) FROM pengguna WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS aktif_30_hari"
)->fetch();
$sekolah = $pdo->query(
    "SELECT s.id, s.nama, s.jenjang, s.kota, s.status,
       SUM(CASE WHEN p.role = 'siswa' THEN 1 ELSE 0 END) AS jumlah_siswa,
       SUM(CASE WHEN p.role = 'guru_bk' THEN 1 ELSE 0 END) AS jumlah_guru,
       (SELECT COUNT(*) FROM berbagi_catatan b WHERE b.sekolah_id = s.id) AS jumlah_catatan,
       (SELECT MAX(p2.last_login_at) FROM pengguna p2 WHERE p2.sekolah_id = s.id) AS aktivitas_terakhir
     FROM sekolah s LEFT JOIN pengguna p ON p.sekolah_id = s.id
     GROUP BY s.id, s.nama, s.jenjang, s.kota, s.status ORDER BY s.nama"
)->fetchAll();
?>
<section class="mb-6 rounded-3xl bg-gradient-to-r from-slate-900 to-slate-700 p-6 text-white shadow-xl sm:p-8">
  <span class="mb-2 inline-block rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-slate-200">Administrasi KARVIA</span>
  <h1 class="text-2xl font-extrabold sm:text-3xl">Panel Admin</h1>
  <p class="mt-2 max-w-2xl text-sm text-slate-300">Pantau sekolah dan akun pengguna. Panel ini menampilkan data akun dan rekap, bukan isi jurnal pribadi siswa.</p>
</section>
<section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan sistem">
  <?php foreach ([['Sekolah Aktif', $stat['sekolah_aktif']], ['Akun Terdaftar', $stat['total_akun'], $stat['total_siswa'] . ' siswa · ' . $stat['total_guru'] . ' Guru BK'], ['Pernah Masuk', $stat['pernah_login']], ['Aktif 30 Hari', $stat['aktif_30_hari']]] as $kartu): ?>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><span class="text-xs font-semibold uppercase text-slate-400"><?= e($kartu[0]) ?></span><div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e($kartu[1]) ?></div><?php if (isset($kartu[2])): ?><p class="mt-1 text-xs text-slate-500"><?= e($kartu[2]) ?></p><?php endif; ?></article>
  <?php endforeach; ?>
</section>
<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
  <div class="border-b border-slate-100 p-6"><h2 class="text-lg font-bold text-slate-900">Rekap per Sekolah</h2><p class="mt-1 text-xs text-slate-500">Ringkasan akun, catatan berbagi, dan login terakhir.</p></div>
  <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-600"><tr><th class="p-4">Nama Sekolah</th><th class="p-4">Jenjang / Kota</th><th class="p-4">Siswa</th><th class="p-4">Guru BK</th><th class="p-4">Catatan Dibagikan</th><th class="p-4">Aktivitas Terakhir</th></tr></thead><tbody>
  <?php if (!$sekolah): ?><tr><td colspan="6" class="p-10 text-center text-slate-500">Belum ada sekolah yang terdaftar.</td></tr><?php else: foreach ($sekolah as $baris): ?>
    <tr class="border-b border-slate-100"><td class="p-4 font-semibold"><?= e($baris['nama']) ?></td><td class="p-4"><?= e($baris['jenjang'] . ' / ' . ($baris['kota'] ?: '—')) ?></td><td class="p-4"><?= e($baris['jumlah_siswa']) ?></td><td class="p-4"><?= e($baris['jumlah_guru']) ?></td><td class="p-4"><?= e($baris['jumlah_catatan']) ?></td><td class="p-4"><?= e(tanggal_id($baris['aktivitas_terakhir'], true)) ?></td></tr>
  <?php endforeach; endif; ?></tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
