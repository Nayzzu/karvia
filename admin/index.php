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

<!-- Banner Sambutan -->
<section class="rounded-2xl border border-[#cfe0d3] bg-gradient-to-br from-[#224f35] via-[#285d3f] to-[#1c432c] p-6 text-white shadow-sm sm:p-7">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="max-w-2xl">
      <span class="inline-block rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white">
        Panel Kendali Utama
      </span>
      <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Ringkasan Sistem KARVIA</h2>
      <p class="mt-1.5 text-sm text-white/80 leading-relaxed">
        Pantau operasional sekolah, statistik akun, dan aktivitas real-time. Data privasi jurnal siswa tetap terlindungi dan terisolasi.
      </p>
    </div>
    <div class="flex flex-wrap gap-2.5 shrink-0">
      <a href="<?= e(url('admin/sekolah_form.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-[#224f35] shadow-sm transition hover:bg-[#eff3ee]">
        <?php karvia_icon('plus', 'h-4 w-4'); ?>
        Tambah Sekolah
      </a>
      <a href="<?= e(url('admin/pengguna_form.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white/20">
        <?php karvia_icon('users', 'h-4 w-4'); ?>
        Tambah Pengguna
      </a>
    </div>
  </div>
</section>

<!-- Kartu Statistik Attendara Style -->
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" aria-label="Statistik sistem">
  <!-- Sekolah Aktif -->
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sekolah Aktif</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($stat['sekolah_aktif']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Terdaftar di sistem</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('school', 'h-5 w-5'); ?>
    </span>
  </article>

  <!-- Akun Terdaftar -->
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Akun Terdaftar</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($stat['total_akun']) ?></p>
      <p class="mt-1 text-xs text-slate-500"><?= e($stat['total_siswa']) ?> siswa &middot; <?= e($stat['total_guru']) ?> Guru BK</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('users', 'h-5 w-5'); ?>
    </span>
  </article>

  <!-- Sedang Online (Real-time) -->
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-emerald-300 bg-[#f4faf5] p-5 shadow-[0_2px_8px_rgba(36,89,56,0.06)]">
    <div>
      <div class="flex items-center gap-1.5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Sedang Online</h3>
        <span class="relative flex h-2 w-2">
          <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-600"></span>
        </span>
      </div>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-emerald-950 sm:text-3xl"><?= e($stat['sedang_online']) ?></p>
      <p class="mt-1 text-xs text-emerald-700">Aktif 5 menit terakhir</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
      <?php karvia_icon('chart', 'h-5 w-5'); ?>
    </span>
  </article>

  <!-- Pernah Masuk -->
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pernah Masuk</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($stat['pernah_login']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Login terverifikasi</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('check', 'h-5 w-5'); ?>
    </span>
  </article>

  <!-- Aktif 30 Hari -->
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Aktif 30 Hari</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($stat['aktif_30_hari']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Siswa/Guru aktif</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('calendar', 'h-5 w-5'); ?>
    </span>
  </article>
</section>

<!-- Widget Pengguna Online -->
<section class="rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-sm sm:p-6">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eef2ed] pb-4">
    <div>
      <h2 class="text-base font-bold text-[#203628]">Pengguna Sedang Aktif Sekarang</h2>
      <p class="mt-0.5 text-xs text-slate-500">Akun dengan sinyal heartbeat interaksi dalam 5 menit terakhir.</p>
    </div>
    <a href="<?= e(url('admin/pengguna.php?online=1')) ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#245938] hover:underline">
      <span>Lihat Semua di Kelola Pengguna</span>
      &rarr;
    </a>
  </div>
  <?php if (!$pengguna_online): ?>
    <div class="py-8 text-center text-sm text-slate-500">
      Tidak ada pengguna yang sedang online saat ini.
    </div>
  <?php else: ?>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($pengguna_online as $u): ?>
        <div class="flex items-center gap-3 rounded-xl border border-[#e6ece5] bg-[#f9fbf8] p-3 transition hover:border-[#ccd9cb]">
          <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#e5efe7] text-xs font-bold text-[#245938]">
            <?= e(mb_strtoupper(mb_substr($u['nama_lengkap'], 0, 1))) ?>
            <span class="absolute -right-0.5 -top-0.5 h-3 w-3 rounded-full border-2 border-white bg-emerald-500"></span>
          </span>
          <div class="min-w-0 flex-1">
            <div class="truncate text-xs font-bold text-[#203628]"><?= e($u['nama_lengkap']) ?></div>
            <div class="truncate text-[11px] text-slate-500">@<?= e($u['username']) ?> &middot; <?= e($u['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($u['role'])) ?></div>
            <div class="truncate text-[10px] font-medium text-emerald-800"><?= e($u['sekolah_nama'] ?: 'Global') ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Tabel Rekap per Sekolah -->
<section class="rounded-2xl border border-[#dfe6de] bg-white shadow-sm overflow-hidden">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eef2ed] p-5 sm:p-6">
    <div>
      <h2 class="text-base font-bold text-[#203628]">Rekap per Sekolah</h2>
      <p class="mt-0.5 text-xs text-slate-500">Ringkasan sebaran akun siswa, guru, catatan dibagikan, dan aktivitas login.</p>
    </div>
    <a href="<?= e(url('admin/sekolah.php')) ?>" class="text-xs font-bold text-[#245938] hover:underline">
      Kelola Sekolah &rarr;
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-[#f3f6f1] text-xs uppercase tracking-wide text-slate-600">
        <tr>
          <th scope="col" class="px-5 py-3.5 font-semibold">Nama Sekolah</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Jenjang / Kota</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Siswa</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Guru BK</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Catatan Dibagikan</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Aktivitas Terakhir</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#edf1ec] text-slate-700">
        <?php if (!$sekolah): ?>
          <tr>
            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">
              Belum ada sekolah yang terdaftar.
            </td>
          </tr>
        <?php else: foreach ($sekolah as $baris): ?>
          <tr class="transition-colors hover:bg-[#f8faf7]">
            <td class="px-5 py-3.5 font-semibold text-[#203628]">
              <?= e($baris['nama']) ?>
            </td>
            <td class="px-5 py-3.5 text-slate-600">
              <?= e($baris['jenjang'] . ' / ' . ($baris['kota'] ?: '—')) ?>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <?= e($baris['jumlah_siswa']) ?>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <?= e($baris['jumlah_guru']) ?>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <?= e($baris['jumlah_catatan']) ?>
            </td>
            <td class="px-5 py-3.5 text-xs text-slate-600">
              <?= e(tanggal_id($baris['aktivitas_terakhir'], true)) ?>
            </td>
            <td class="px-5 py-3.5 text-right">
              <a href="<?= e(url('admin/sekolah_detail.php?id=' . (int) $baris['id'])) ?>" class="inline-flex items-center gap-1 rounded-lg border border-[#cfe0d3] bg-[#edf4ee] px-3 py-1.5 text-xs font-semibold text-[#245938] hover:bg-[#dfeada]">
                Detail
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
