<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'rekap';
$judul_halaman = 'Rekap Sistem';
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

<div class="flex flex-col gap-1">
  <h2 class="text-xl font-bold tracking-tight text-[#203628]">Rekapitulasi Sistem Global</h2>
  <p class="text-xs text-slate-500">Ringkasan kuantitatif seluruh sekolah, akun pengguna, dan aktivitas konsultasi bimbingan.</p>
</div>

<!-- Kartu Ringkasan Attendara Style -->
<section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Ringkasan seluruh sekolah">
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sekolah Mitra</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($ringkasan['jumlah_sekolah']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Total terdata</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('school', 'h-5 w-5'); ?>
    </span>
  </article>

  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Siswa</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($ringkasan['jumlah_siswa']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Akun terdaftar</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('users', 'h-5 w-5'); ?>
    </span>
  </article>

  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Guru BK</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($ringkasan['jumlah_guru']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Konselor sekolah</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('users', 'h-5 w-5'); ?>
    </span>
  </article>

  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan Dibagikan</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($ringkasan['jumlah_catatan']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Konsultasi BK terkirim</p>
    </div>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('file', 'h-5 w-5'); ?>
    </span>
  </article>
</section>

<!-- Tabel Rincian per Sekolah -->
<section class="rounded-2xl border border-[#dfe6de] bg-white shadow-sm overflow-hidden">
  <div class="border-b border-[#eef2ed] p-5 sm:p-6">
    <h3 class="text-base font-bold text-[#203628]">Rincian Kuantitas per Sekolah</h3>
    <p class="mt-0.5 text-xs text-slate-500">Perbandingan jumlah akun aktif vs total terdaftar di tiap instansi.</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-[#f3f6f1] text-xs uppercase tracking-wide text-slate-600">
        <tr>
          <th scope="col" class="px-5 py-3.5 font-semibold">Nama Sekolah</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Status Instansi</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Siswa Aktif / Total</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Guru BK Aktif / Total</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Catatan Dibagikan</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#edf1ec] text-slate-700">
        <?php if (!$sekolah): ?>
          <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada data sekolah.</td></tr>
        <?php else: foreach ($sekolah as $row): ?>
          <tr class="transition-colors hover:bg-[#f8faf7]">
            <td class="px-5 py-3.5">
              <a href="<?= e(url('admin/sekolah_detail.php?id=' . (int) $row['id'])) ?>" class="font-bold text-[#203628] hover:text-[#245938] hover:underline">
                <?= e($row['nama']) ?>
              </a>
              <div class="mt-0.5 text-xs text-slate-500"><?= e($row['jenjang'] . ' &middot; ' . ($row['kota'] ?: 'Lokasi belum diisi')) ?></div>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $row['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                <?= e(ucfirst($row['status'])) ?>
              </span>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <span class="font-bold text-emerald-700"><?= e($row['siswa_aktif']) ?></span>
              <span class="text-slate-400">/</span>
              <span class="text-slate-600"><?= e($row['siswa']) ?></span>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <span class="font-bold text-emerald-700"><?= e($row['guru_aktif']) ?></span>
              <span class="text-slate-400">/</span>
              <span class="text-slate-600"><?= e($row['guru']) ?></span>
            </td>
            <td class="px-5 py-3.5 text-center font-bold text-[#203628]">
              <?= e($row['catatan']) ?>
            </td>
            <td class="px-5 py-3.5 text-right whitespace-nowrap">
              <a href="<?= e(url('admin/sekolah_detail.php?id=' . (int) $row['id'])) ?>" class="inline-flex items-center rounded-lg border border-[#cfe0d3] bg-[#edf4ee] px-2.5 py-1 text-xs font-semibold text-[#245938] hover:bg-[#dfeada]">
                Lihat Detail
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Pintasan Cepat -->
<div class="flex flex-wrap gap-2 pt-2">
  <a class="inline-flex items-center gap-1.5 rounded-xl border border-[#dfe6de] bg-white px-3.5 py-2 text-xs font-semibold text-[#203628] shadow-sm hover:bg-[#f6f8f5]" href="<?= e(url('admin/pengguna.php')) ?>">
    <?php karvia_icon('users', 'h-4 w-4 text-[#245938]'); ?>
    Kelola Data Pengguna
  </a>
  <a class="inline-flex items-center gap-1.5 rounded-xl border border-[#dfe6de] bg-white px-3.5 py-2 text-xs font-semibold text-[#203628] shadow-sm hover:bg-[#f6f8f5]" href="<?= e(url('admin/sekolah.php')) ?>">
    <?php karvia_icon('school', 'h-4 w-4 text-[#245938]'); ?>
    Kelola Data Sekolah
  </a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
