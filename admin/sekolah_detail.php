<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'sekolah';
$judul_halaman = 'Detail Sekolah';
require_once __DIR__ . '/../includes/header.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    exit('Sekolah tidak ditemukan.');
}
$pdo = db();
$stmt = $pdo->prepare(
    "SELECT s.id,s.nama,s.npsn,s.jenjang,s.alamat,s.kota,s.provinsi,s.status,s.dibuat_pada,
       COALESCE(SUM(CASE WHEN p.role='siswa' THEN 1 ELSE 0 END),0) AS jumlah_siswa,
       COALESCE(SUM(CASE WHEN p.role='guru_bk' THEN 1 ELSE 0 END),0) AS jumlah_guru,
       (SELECT COUNT(*) FROM berbagi_catatan b JOIN pengguna ps ON ps.id=b.siswa_id
        WHERE b.sekolah_id=s.id AND ps.sekolah_id=s.id) AS jumlah_catatan
     FROM sekolah s LEFT JOIN pengguna p ON p.sekolah_id=s.id
     WHERE s.id=:id GROUP BY s.id"
);
$stmt->execute(['id' => $id]);
$sekolah = $stmt->fetch();
if (!$sekolah) {
    http_response_code(404);
    exit('Sekolah tidak ditemukan.');
}
$stmt = $pdo->prepare(
    "SELECT id,nama_lengkap,username,role,status,kelas,last_login_at,
            (status = 'aktif' AND terakhir_aktif_pada IS NOT NULL AND terakhir_aktif_pada >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)) AS sedang_online
     FROM pengguna
     WHERE sekolah_id=:id ORDER BY role,nama_lengkap"
);
$stmt->execute(['id' => $id]);
$akun = $stmt->fetchAll();
?>

<!-- Header & Navigasi Balik -->
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
  <div>
    <a href="<?= e(url('admin/sekolah.php')) ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#245938] hover:underline">
      &larr; Kembali ke Daftar Sekolah
    </a>
    <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#203628]"><?= e($sekolah['nama']) ?></h2>
    <p class="mt-0.5 text-xs text-slate-500">Detail instansi sekolah dan daftar akun yang terhubung.</p>
  </div>
  <a href="<?= e(url('admin/sekolah_form.php?id=' . $id)) ?>" class="inline-flex items-center gap-2 rounded-xl border border-[#dfe6de] bg-white px-4 py-2.5 text-xs font-bold text-[#203628] shadow-sm hover:bg-[#f6f8f5]">
    Ubah Profil Sekolah
  </a>
</div>

<!-- Kartu Statistik Sekolah -->
<section class="grid gap-4 sm:grid-cols-3">
  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa Terdaftar</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628]"><?= e($sekolah['jumlah_siswa']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Akun siswa aktif/nonaktif</p>
    </div>
    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('users', 'h-5 w-5'); ?>
    </span>
  </article>

  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Guru BK</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628]"><?= e($sekolah['jumlah_guru']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Tenaga pembimbing sekolah</p>
    </div>
    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('school', 'h-5 w-5'); ?>
    </span>
  </article>

  <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
    <div>
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan Dibagikan</h3>
      <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628]"><?= e($sekolah['jumlah_catatan']) ?></p>
      <p class="mt-1 text-xs text-slate-500">Konsultasi siswa ke Guru BK</p>
    </div>
    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('file', 'h-5 w-5'); ?>
    </span>
  </article>
</section>

<!-- Profil Sekolah -->
<section class="rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-sm sm:p-6">
  <h3 class="text-base font-bold text-[#203628] border-b border-[#eef2ed] pb-3 mb-4">Informasi Instansi</h3>
  <dl class="grid gap-4 text-xs sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
      <dt class="font-semibold text-slate-500">Jenjang</dt>
      <dd class="mt-1 text-sm font-bold text-[#203628]"><?= e($sekolah['jenjang']) ?></dd>
    </div>
    <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
      <dt class="font-semibold text-slate-500">NPSN</dt>
      <dd class="mt-1 text-sm font-bold text-[#203628]"><?= e($sekolah['npsn'] ?: '—') ?></dd>
    </div>
    <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
      <dt class="font-semibold text-slate-500">Kota / Provinsi</dt>
      <dd class="mt-1 text-sm font-bold text-[#203628]"><?= e($sekolah['kota'] ?: '—') ?> / <?= e($sekolah['provinsi'] ?: '—') ?></dd>
    </div>
    <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
      <dt class="font-semibold text-slate-500">Status Instansi</dt>
      <dd class="mt-1">
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $sekolah['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
          <?= e(ucfirst($sekolah['status'])) ?>
        </span>
      </dd>
    </div>
    <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5 sm:col-span-2 lg:col-span-4">
      <dt class="font-semibold text-slate-500">Alamat Lengkap</dt>
      <dd class="mt-1 text-xs text-slate-700 leading-relaxed"><?= nl2br(e($sekolah['alamat'] ?: '—')) ?></dd>
    </div>
  </dl>
</section>

<!-- Tabel Akun Terkait -->
<section class="rounded-2xl border border-[#dfe6de] bg-white shadow-sm overflow-hidden">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eef2ed] p-5 sm:p-6">
    <div>
      <h3 class="text-base font-bold text-[#203628]">Akun Terdaftar di Sekolah Ini</h3>
      <p class="mt-0.5 text-xs text-slate-500">Daftar Guru BK dan Siswa. Catatan pribadi siswa tidak dapat diakses admin.</p>
    </div>
    <a href="<?= e(url('admin/pengguna_form.php?sekolah_id=' . $id)) ?>" class="inline-flex items-center gap-1.5 rounded-xl bg-[#224f35] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#1a4029]">
      <?php karvia_icon('plus', 'h-3.5 w-3.5'); ?>
      Tambah Akun
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-[#f3f6f1] text-xs uppercase tracking-wide text-slate-600">
        <tr>
          <th scope="col" class="px-5 py-3.5 font-semibold">Nama / Username</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Peran</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Kelas</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Login Terakhir</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#edf1ec] text-slate-700">
        <?php if (!$akun): ?>
          <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada akun yang terhubung ke sekolah ini.</td></tr>
        <?php else: foreach ($akun as $baris): ?>
          <tr class="transition-colors hover:bg-[#f8faf7]">
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                <span class="font-bold text-[#203628]"><?= e($baris['nama_lengkap']) ?></span>
                <?php if (!empty($baris['sedang_online'])): ?>
                  <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800" title="Aktif dalam 5 menit terakhir">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Online
                  </span>
                <?php endif; ?>
              </div>
              <div class="text-xs text-slate-500">@<?= e($baris['username']) ?></div>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-block rounded-md bg-[#eef3ee] px-2.5 py-1 text-xs font-semibold text-[#245938]">
                <?= e($baris['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($baris['role'])) ?>
              </span>
            </td>
            <td class="px-5 py-3.5 text-xs text-slate-600">
              <?= e($baris['kelas'] ?: '—') ?>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                <?= e(ucfirst($baris['status'])) ?>
              </span>
            </td>
            <td class="px-5 py-3.5 text-xs text-slate-600 whitespace-nowrap">
              <?= e(tanggal_id($baris['last_login_at'], true)) ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
