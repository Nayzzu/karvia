<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'sekolah';
$judul_halaman = 'Kelola Sekolah';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status_baru = (string) ($_POST['status_baru'] ?? '');
    if (!$id || !in_array($status_baru, ['aktif', 'nonaktif'], true)) {
        flash_set('gagal', 'Permintaan perubahan status sekolah tidak valid.');
        redirect('admin/sekolah.php');
    }

    $ubah = $pdo->prepare('UPDATE sekolah SET status = :status, diperbarui_pada = NOW() WHERE id = :id');
    $ubah->execute(['status' => $status_baru, 'id' => $id]);
    if ($ubah->rowCount() === 0) {
        $cek = $pdo->prepare('SELECT COUNT(*) FROM sekolah WHERE id = :id');
        $cek->execute(['id' => $id]);
        if (!(int) $cek->fetchColumn()) {
            http_response_code(404);
            exit('Sekolah tidak ditemukan.');
        }
    }
    log_aktivitas($status_baru === 'aktif' ? 'aktif_sekolah' : 'nonaktif_sekolah', 'Status sekolah diperbarui.');
    flash_set('sukses', 'Status sekolah berhasil diperbarui. Akun yang terhubung tidak ikut dinonaktifkan.');
    redirect('admin/sekolah.php');
}

$cari = trim((string) ($_GET['cari'] ?? ''));
$sql = "SELECT s.id, s.nama, s.npsn, s.jenjang, s.kota, s.provinsi, s.status,
          COALESCE(SUM(CASE WHEN p.role = 'siswa' THEN 1 ELSE 0 END), 0) AS jumlah_siswa,
          COALESCE(SUM(CASE WHEN p.role = 'guru_bk' THEN 1 ELSE 0 END), 0) AS jumlah_guru
        FROM sekolah s LEFT JOIN pengguna p ON p.sekolah_id = s.id";
$parameter = [];
if ($cari !== '') {
    $sql .= ' WHERE s.nama LIKE :cari_nama OR s.kota LIKE :cari_kota OR s.provinsi LIKE :cari_provinsi';
    $pola = '%' . $cari . '%';
    $parameter = ['cari_nama' => $pola, 'cari_kota' => $pola, 'cari_provinsi' => $pola];
}
$sql .= ' GROUP BY s.id, s.nama, s.npsn, s.jenjang, s.kota, s.provinsi, s.status ORDER BY s.nama';
$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$sekolah = $stmt->fetchAll();
?>

<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
  <div>
    <h2 class="text-xl font-bold tracking-tight text-[#203628]">Daftar Sekolah Mitra</h2>
    <p class="mt-0.5 text-xs text-slate-500">Kelola master instansi sekolah dan pantau jumlah akun terdaftar.</p>
  </div>
  <a href="<?= e(url('admin/sekolah_form.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-[#224f35] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1a4029]">
    <?php karvia_icon('plus', 'h-4 w-4'); ?>
    Tambah Sekolah
  </a>
</div>

<!-- Form Cari Attendara Style -->
<form method="get" action="<?= e(url('admin/sekolah.php')) ?>" class="rounded-2xl border border-[#dfe6de] bg-white p-4 shadow-sm sm:p-5">
  <div class="flex flex-col gap-2.5 sm:flex-row">
    <div class="relative flex-1">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
        <?php karvia_icon('search', 'h-4 w-4'); ?>
      </span>
      <input id="cari" name="cari" value="<?= e($cari) ?>" placeholder="Cari nama sekolah, kota, atau provinsi..." class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] py-2 pl-10 pr-3.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
    </div>
    <div class="flex items-center gap-2">
      <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-4 py-2 text-xs font-bold text-white hover:bg-[#1a4029]">
        Cari
      </button>
      <?php if ($cari !== ''): ?>
        <a href="<?= e(url('admin/sekolah.php')) ?>" class="rounded-xl border border-[#dfe6de] px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-[#f6f8f5]">
          Reset
        </a>
      <?php endif; ?>
    </div>
  </div>
</form>

<!-- Tabel Sekolah Attendara Style -->
<section class="rounded-2xl border border-[#dfe6de] bg-white shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-[#f3f6f1] text-xs uppercase tracking-wide text-slate-600">
        <tr>
          <th scope="col" class="px-5 py-3.5 font-semibold">Sekolah</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Kota / Provinsi</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Siswa</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-center">Guru BK</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#edf1ec] text-slate-700">
        <?php if (!$sekolah): ?>
          <tr>
            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">
              <?= $cari !== '' ? 'Tidak ada sekolah yang cocok dengan kata kunci pencarian.' : 'Belum ada data sekolah. Silakan tambahkan sekolah pertama.' ?>
            </td>
          </tr>
        <?php else: foreach ($sekolah as $baris): ?>
          <tr class="transition-colors hover:bg-[#f8faf7] align-middle">
            <td class="px-5 py-3.5">
              <a class="font-bold text-[#203628] hover:text-[#245938] hover:underline" href="<?= e(url('admin/sekolah_detail.php?id=' . $baris['id'])) ?>">
                <?= e($baris['nama']) ?>
              </a>
              <div class="mt-0.5 text-xs text-slate-500">
                <?= e($baris['jenjang']) ?><?= $baris['npsn'] ? ' &middot; NPSN ' . e($baris['npsn']) : '' ?>
              </div>
            </td>
            <td class="px-5 py-3.5 text-xs text-slate-600">
              <?= e($baris['kota'] ?: '—') ?> / <?= e($baris['provinsi'] ?: '—') ?>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <?= e($baris['jumlah_siswa']) ?>
            </td>
            <td class="px-5 py-3.5 text-center font-medium">
              <?= e($baris['jumlah_guru']) ?>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                <?= e(ucfirst($baris['status'])) ?>
              </span>
            </td>
            <td class="px-5 py-3.5 text-right whitespace-nowrap">
              <div class="inline-flex items-center gap-2">
                <a class="inline-flex items-center rounded-lg border border-[#cfe0d3] bg-[#edf4ee] px-2.5 py-1 text-xs font-semibold text-[#245938] hover:bg-[#dfeada]" href="<?= e(url('admin/sekolah_detail.php?id=' . $baris['id'])) ?>">
                  Detail
                </a>
                <a class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50" href="<?= e(url('admin/sekolah_form.php?id=' . $baris['id'])) ?>">
                  Ubah
                </a>
                <form method="post" action="<?= e(url('admin/sekolah.php')) ?>" onsubmit="return confirm('<?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> sekolah ini? Akun pengguna di dalam sekolah tidak ikut berubah.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= e($baris['id']) ?>">
                  <input type="hidden" name="status_baru" value="<?= $baris['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>">
                  <button type="submit" class="inline-flex items-center rounded-lg border px-2.5 py-1 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' ?>">
                    <?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
