<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'catatan';
$judul_halaman = 'Catatan Siswa';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$sekolah_id = auth_sekolah_id();
$status = (string) ($_GET['status'] ?? '');
$cari = trim((string) ($_GET['cari'] ?? ''));
$kondisi = ['b.sekolah_id = :sekolah_id', 'p.sekolah_id = :sekolah_siswa', "p.role = 'siswa'"];
$parameter = ['sekolah_id' => $sekolah_id, 'sekolah_siswa' => $sekolah_id];
if (in_array($status, ['baru', 'dibaca', 'ditindaklanjuti'], true)) {
    $kondisi[] = 'b.status = :status';
    $parameter['status'] = $status;
}
if ($cari !== '') {
    $kondisi[] = '(p.nama_lengkap LIKE :nama OR p.username LIKE :username)';
    $parameter['nama'] = '%' . $cari . '%';
    $parameter['username'] = '%' . $cari . '%';
}
$sql = 'SELECT b.id, b.status, b.dibagikan_pada, p.nama_lengkap, p.username, p.kelas,
          (SELECT COUNT(*) FROM tindak_lanjut_bk t WHERE t.berbagi_id = b.id) AS jumlah_tindak_lanjut
        FROM berbagi_catatan b JOIN pengguna p ON p.id = b.siswa_id
        WHERE ' . implode(' AND ', $kondisi) . ' ORDER BY b.dibagikan_pada DESC, b.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$catatan = $stmt->fetchAll();
?>
<div class="space-y-5">
  <div class="flex flex-col gap-1">
    <h2 class="text-xl font-bold tracking-tight text-[#203628]">Catatan Eksplorasi Siswa</h2>
    <p class="text-xs text-slate-500">Daftar catatan karier yang dibagikan oleh siswa di sekolah Anda untuk ditindaklanjuti.</p>
  </div>

  <!-- Form Filter Attendara Style -->
  <form method="get" action="<?= e(url('guru_bk/catatan.php')) ?>" class="rounded-2xl border border-[#dfe6de] bg-white p-4 shadow-sm sm:p-5">
    <div class="grid gap-3 sm:grid-cols-3">
      <div>
        <label for="cari" class="mb-1 block text-xs font-bold text-slate-700">Cari Siswa</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <?php karvia_icon('search', 'h-4 w-4'); ?>
          </span>
          <input id="cari" name="cari" value="<?= e($cari) ?>" placeholder="Nama lengkap atau username..." class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] py-2 pl-9 pr-3 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        </div>
      </div>
      <div>
        <label for="status" class="mb-1 block text-xs font-bold text-slate-700">Status Catatan</label>
        <select id="status" name="status" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
          <option value="">Semua status</option>
          <?php foreach (['baru' => 'Baru', 'dibaca' => 'Dibaca', 'ditindaklanjuti' => 'Ditindaklanjuti'] as $nilai => $label): ?>
            <option value="<?= e($nilai) ?>" <?= $status === $nilai ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-end gap-2">
        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#1a4029]">
          Terapkan Filter
        </button>
        <?php if ($cari !== '' || $status !== ''): ?>
          <a href="<?= e(url('guru_bk/catatan.php')) ?>" class="inline-flex items-center justify-center rounded-xl border border-[#dfe6de] bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-[#f6f8f5]">
            Reset
          </a>
        <?php endif; ?>
      </div>
    </div>
  </form>

  <!-- Tabel Catatan Siswa -->
  <section class="overflow-hidden rounded-2xl border border-[#dfe6de] bg-white shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="border-b border-[#eef2ed] bg-[#fbfcf8] font-bold uppercase tracking-wider text-slate-500">
          <tr>
            <th class="p-4">Siswa</th>
            <th class="p-4">Dibagikan</th>
            <th class="p-4">Status</th>
            <th class="p-4">Tindak Lanjut</th>
            <th class="p-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#eef2ed]">
          <?php if (!$catatan): ?>
            <tr>
              <td colspan="5" class="p-8 text-center text-slate-500">
                Belum ada catatan yang cocok dengan kriteria pencarian.
              </td>
            </tr>
          <?php else: foreach ($catatan as $baris): ?>
            <tr class="transition hover:bg-[#fbfdfb]">
              <td class="p-4">
                <div class="font-bold text-[#203628]"><?= e($baris['nama_lengkap']) ?></div>
                <div class="text-[11px] text-slate-500"><?= e($baris['kelas'] ?: 'Kelas belum diisi') ?> · @<?= e($baris['username']) ?></div>
              </td>
              <td class="whitespace-nowrap p-4 text-slate-500">
                <?= e(tanggal_id($baris['dibagikan_pada'], true)) ?>
              </td>
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
              <td class="p-4">
                <span class="inline-flex items-center rounded-lg bg-[#f0f4f1] px-2.5 py-1 text-[11px] font-semibold text-[#203628]">
                  <?= (int) $baris['jumlah_tindak_lanjut'] ?> catatan
                </span>
              </td>
              <td class="p-4 text-right">
                <a class="inline-flex items-center gap-1 font-bold text-[#245938] hover:underline" href="<?= e(url('guru_bk/catatan_detail.php?id=' . (int) $baris['id'])) ?>">
                  Buka Detail &rarr;
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
