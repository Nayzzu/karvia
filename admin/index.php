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
       (SELECT COUNT(*) FROM pengguna WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS aktif_30_hari,
       (SELECT COUNT(*) FROM pengguna WHERE status = 'aktif' AND terakhir_aktif_pada >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)) AS sedang_online"
)->fetch();
$pengguna_online = $pdo->query(
    "SELECT p.id, p.nama_lengkap, p.username, p.role, p.terakhir_aktif_pada, s.nama AS sekolah_nama
     FROM pengguna p LEFT JOIN sekolah s ON s.id = p.sekolah_id
     WHERE p.status = 'aktif' AND p.terakhir_aktif_pada >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
     ORDER BY p.terakhir_aktif_pada DESC LIMIT 8"
)->fetchAll();
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
<section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" aria-label="Ringkasan sistem">
  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <span class="text-xs font-semibold uppercase text-slate-400">Sekolah Aktif</span>
    <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e($stat['sekolah_aktif']) ?></div>
  </article>
  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <span class="text-xs font-semibold uppercase text-slate-400">Akun Terdaftar</span>
    <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e($stat['total_akun']) ?></div>
    <p class="mt-1 text-xs text-slate-500"><?= e($stat['total_siswa']) ?> siswa · <?= e($stat['total_guru']) ?> Guru BK</p>
  </article>
  <article class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold uppercase text-emerald-800">Sedang Online</span>
      <span class="flex h-2.5 w-2.5 items-center justify-center">
        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
      </span>
    </div>
    <div class="mt-2 text-2xl font-extrabold text-emerald-950"><?= e($stat['sedang_online']) ?></div>
    <p class="mt-1 text-xs text-emerald-700">Aktif 5 menit terakhir</p>
  </article>
  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <span class="text-xs font-semibold uppercase text-slate-400">Pernah Masuk</span>
    <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e($stat['pernah_login']) ?></div>
  </article>
  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <span class="text-xs font-semibold uppercase text-slate-400">Aktif 30 Hari</span>
    <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e($stat['aktif_30_hari']) ?></div>
  </article>
</section>

<section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Pengguna Sedang Aktif Sekarang</h2>
      <p class="mt-0.5 text-xs text-slate-500">Daftar pengguna dengan interaksi dalam 5 menit terakhir.</p>
    </div>
    <a href="<?= e(url('admin/pengguna.php?online=1')) ?>" class="text-xs font-bold text-emerald-700 hover:underline">Lihat Semua di Kelola Pengguna &rarr;</a>
  </div>
  <?php if (!$pengguna_online): ?>
    <p class="py-6 text-center text-sm text-slate-500">Tidak ada pengguna yang sedang online saat ini.</p>
  <?php else: ?>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($pengguna_online as $u): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3.5">
          <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-sm font-bold text-emerald-800">
            <?= e(mb_strtoupper(mb_substr($u['nama_lengkap'], 0, 1))) ?>
            <span class="absolute -right-0.5 -top-0.5 h-3 w-3 rounded-full border-2 border-white bg-emerald-500"></span>
          </span>
          <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-bold text-slate-900"><?= e($u['nama_lengkap']) ?></div>
            <div class="truncate text-xs text-slate-500">@<?= e($u['username']) ?> · <?= e($u['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($u['role'])) ?></div>
            <div class="truncate text-[11px] text-emerald-700"><?= e($u['sekolah_nama'] ?: 'Global') ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
  <div class="border-b border-slate-100 p-6"><h2 class="text-lg font-bold text-slate-900">Rekap per Sekolah</h2><p class="mt-1 text-xs text-slate-500">Ringkasan akun, catatan berbagi, dan login terakhir.</p></div>
  <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-600"><tr><th class="p-4">Nama Sekolah</th><th class="p-4">Jenjang / Kota</th><th class="p-4">Siswa</th><th class="p-4">Guru BK</th><th class="p-4">Catatan Dibagikan</th><th class="p-4">Aktivitas Terakhir</th></tr></thead><tbody>
  <?php if (!$sekolah): ?><tr><td colspan="6" class="p-10 text-center text-slate-500">Belum ada sekolah yang terdaftar.</td></tr><?php else: foreach ($sekolah as $baris): ?>
    <tr class="border-b border-slate-100"><td class="p-4 font-semibold"><?= e($baris['nama']) ?></td><td class="p-4"><?= e($baris['jenjang'] . ' / ' . ($baris['kota'] ?: '—')) ?></td><td class="p-4"><?= e($baris['jumlah_siswa']) ?></td><td class="p-4"><?= e($baris['jumlah_guru']) ?></td><td class="p-4"><?= e($baris['jumlah_catatan']) ?></td><td class="p-4"><?= e(tanggal_id($baris['aktivitas_terakhir'], true)) ?></td></tr>
  <?php endforeach; endif; ?></tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
