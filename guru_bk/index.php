<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'index';
$judul_halaman = 'Dashboard Guru BK';
require_once __DIR__ . '/../includes/header.php';

$sekolah_id = auth_sekolah_id();
$pdo = db();
$stmt = $pdo->prepare(
    "SELECT
       (SELECT COUNT(*) FROM pengguna WHERE sekolah_id = :sid_siswa AND role = 'siswa' AND status = 'aktif') AS jumlah_siswa,
       (SELECT COUNT(*) FROM berbagi_catatan b JOIN pengguna p ON p.id = b.siswa_id
        WHERE b.sekolah_id = :sid_baru AND p.sekolah_id = :sid_siswa_baru AND p.role = 'siswa' AND b.status = 'baru') AS catatan_baru,
       (SELECT COUNT(*) FROM berbagi_catatan b JOIN pengguna p ON p.id = b.siswa_id
        WHERE b.sekolah_id = :sid_tindak AND p.sekolah_id = :sid_siswa_tindak AND p.role = 'siswa' AND b.status = 'ditindaklanjuti') AS ditindaklanjuti,
       (SELECT COUNT(*) FROM berbagi_catatan b JOIN pengguna p ON p.id = b.siswa_id
        WHERE b.sekolah_id = :sid_total AND p.sekolah_id = :sid_siswa_total AND p.role = 'siswa') AS total_catatan"
);
$stmt->execute(['sid_siswa' => $sekolah_id, 'sid_baru' => $sekolah_id, 'sid_siswa_baru' => $sekolah_id, 'sid_tindak' => $sekolah_id, 'sid_siswa_tindak' => $sekolah_id, 'sid_total' => $sekolah_id, 'sid_siswa_total' => $sekolah_id]);
$stat = $stmt->fetch();
$stmt = $pdo->prepare(
    "SELECT b.id, b.status, b.dibagikan_pada, p.nama_lengkap, p.kelas
     FROM berbagi_catatan b JOIN pengguna p ON p.id = b.siswa_id
     WHERE b.sekolah_id = :sekolah_id AND p.sekolah_id = :sekolah_siswa AND p.role = 'siswa'
     ORDER BY b.dibagikan_pada DESC LIMIT 10"
);
$stmt->execute(['sekolah_id' => $sekolah_id, 'sekolah_siswa' => $sekolah_id]);
$catatan = $stmt->fetchAll();
?>
<div class="space-y-6">
  <!-- Banner Sambutan -->
  <section class="rounded-2xl border border-[#cfe0d3] bg-gradient-to-br from-[#173a24] via-[#224f35] to-emerald-700 p-6 text-white shadow-sm sm:p-7">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="max-w-2xl">
        <span class="inline-block rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-100">
          Ruang Kerja Guru BK
        </span>
        <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Selamat datang, <?= e(auth_pengguna()['nama_lengkap'] ?? '') ?></h2>
        <p class="mt-1.5 text-xs text-white/85 leading-relaxed sm:text-sm">
          Pantau catatan eksplorasi karier siswa di sekolah Anda dan berikan tindak lanjut bimbingan terarah.
        </p>
      </div>
      <div class="flex flex-wrap gap-2.5 shrink-0">
        <a href="<?= e(url('guru_bk/catatan.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-[#224f35] shadow-sm transition hover:bg-emerald-50">
          <?php karvia_icon('file', 'h-4 w-4'); ?>
          Catatan Siswa
        </a>
        <a href="<?= e(url('guru_bk/artikel_form.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white/20">
          <?php karvia_icon('plus', 'h-4 w-4'); ?>
          Tulis Artikel
        </a>
      </div>
    </div>
  </section>

  <!-- Peringatan Privasi -->
  <section class="flex items-center gap-3.5 rounded-2xl border border-[#dfe6de] bg-[#fbfcf8] p-4 text-xs text-slate-700 shadow-sm">
    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
      <?php karvia_icon('shield', 'h-4.5 w-4.5'); ?>
    </span>
    <div>
      <span class="font-bold text-[#203628]">Privasi Catatan Terjaga:</span>
      <span class="text-slate-600">Hanya catatan yang dibagikan secara sukarela oleh siswa di sekolah Anda yang dapat dilihat dan ditindaklanjuti. Jurnal pribadi siswa yang tidak dibagikan tetap terlindungi.</span>
    </div>
  </section>

  <!-- Kartu Statistik -->
  <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan dashboard">
    <?php
    $kartu_stat = [
        ['Siswa Aktif', $stat['jumlah_siswa'], 'users', 'Terdaftar di sekolah'],
        ['Catatan Baru', $stat['catatan_baru'], 'file', 'Menunggu tindak lanjut'],
        ['Ditindaklanjuti', $stat['ditindaklanjuti'], 'check', 'Selesai direspons'],
        ['Total Dibagikan', $stat['total_catatan'], 'book', 'Keseluruhan catatan'],
    ];
    foreach ($kartu_stat as [$label, $jumlah, $icon, $sub]):
    ?>
      <article class="flex items-start justify-between gap-3 rounded-2xl border border-[#dfe6de] bg-white p-5 shadow-[0_2px_8px_rgba(48,56,50,0.04)]">
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500"><?= e($label) ?></h3>
          <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#203628] sm:text-3xl"><?= e($jumlah) ?></p>
          <p class="mt-1 text-xs text-slate-500"><?= e($sub) ?></p>
        </div>
        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
          <?php karvia_icon($icon, 'h-5 w-5'); ?>
        </span>
      </article>
    <?php endforeach; ?>
  </section>

  <!-- Tabel Catatan yang Dibagikan -->
  <section class="overflow-hidden rounded-2xl border border-[#dfe6de] bg-white shadow-sm">
    <div class="flex flex-col gap-2 border-b border-[#eef2ed] p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
      <div>
        <h2 class="text-base font-bold text-[#203628]">Catatan Siswa Terbaru</h2>
        <p class="mt-0.5 text-xs text-slate-500">Maksimal 10 catatan yang dibagikan terkini di sekolah Anda.</p>
      </div>
      <a class="inline-flex items-center gap-1 text-xs font-bold text-[#245938] hover:underline" href="<?= e(url('guru_bk/catatan.php')) ?>">
        Lihat semua catatan &rarr;
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="border-b border-[#eef2ed] bg-[#fbfcf8] font-bold uppercase tracking-wider text-slate-500">
          <tr>
            <th class="p-4">Nama Siswa</th>
            <th class="p-4">Kelas</th>
            <th class="p-4">Dibagikan</th>
            <th class="p-4">Status</th>
            <th class="p-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#eef2ed]">
          <?php if (!$catatan): ?>
            <tr>
              <td colspan="5" class="p-8 text-center text-slate-500">
                Belum ada siswa yang membagikan catatan karier.
              </td>
            </tr>
          <?php else: foreach ($catatan as $baris): ?>
            <tr class="transition hover:bg-[#fbfdfb]">
              <td class="p-4 font-semibold text-[#203628]"><?= e($baris['nama_lengkap']) ?></td>
              <td class="p-4 text-slate-600"><?= e($baris['kelas'] ?: '—') ?></td>
              <td class="whitespace-nowrap p-4 text-slate-500"><?= e(tanggal_id($baris['dibagikan_pada'], true)) ?></td>
              <td class="p-4">
                <?php
                $badge = match ($baris['status']) {
                    'baru' => 'bg-emerald-50 text-[#224f35] border-emerald-200',
                    'dibaca' => 'bg-amber-50 text-amber-800 border-amber-200',
                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                };
                ?>
                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold <?= $badge ?>">
                  <?= e(ucfirst($baris['status'])) ?>
                </span>
              </td>
              <td class="p-4 text-right">
                <a class="inline-flex items-center gap-1 font-bold text-[#245938] hover:underline" href="<?= e(url('guru_bk/catatan_detail.php?id=' . (int) $baris['id'])) ?>">
                  Detail &rarr;
                </a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
