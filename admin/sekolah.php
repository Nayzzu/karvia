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
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
  <div><h1 class="text-2xl font-extrabold text-slate-900">Daftar Sekolah</h1><p class="mt-1 text-sm text-slate-500">Cari dan kelola sekolah yang menggunakan KARVIA.</p></div>
  <a href="<?= e(url('admin/sekolah_form.php')) ?>" class="inline-flex justify-center rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-800"><i class="fa-solid fa-plus mr-2"></i>Tambah Sekolah</a>
</div>
<form method="get" action="<?= e(url('admin/sekolah.php')) ?>" class="mb-5 flex flex-col gap-2 sm:flex-row">
  <label class="sr-only" for="cari">Cari nama atau lokasi</label>
  <input id="cari" name="cari" value="<?= e($cari) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm sm:max-w-md" placeholder="Cari nama, kota, atau provinsi">
  <button class="rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white">Terapkan</button>
  <?php if ($cari !== ''): ?><a href="<?= e(url('admin/sekolah.php')) ?>" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold">Reset</a><?php endif; ?>
</form>
<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
  <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="p-4">Sekolah</th><th class="p-4">Kota / Provinsi</th><th class="p-4">Siswa</th><th class="p-4">Guru BK</th><th class="p-4">Status</th><th class="p-4">Aksi</th></tr></thead><tbody>
  <?php if (!$sekolah): ?><tr><td colspan="6" class="p-10 text-center text-slate-500"><?= $cari !== '' ? 'Tidak ada sekolah yang cocok dengan pencarian.' : 'Belum ada sekolah. Tambahkan sekolah pertama.' ?></td></tr><?php else: foreach ($sekolah as $baris): ?>
    <tr class="border-b border-slate-100 align-top"><td class="p-4"><a class="font-semibold text-emerald-800 hover:underline" href="<?= e(url('admin/sekolah_detail.php?id=' . $baris['id'])) ?>"><?= e($baris['nama']) ?></a><div class="mt-1 text-xs text-slate-500"><?= e($baris['jenjang']) ?><?= $baris['npsn'] ? ' · NPSN ' . e($baris['npsn']) : '' ?></div></td><td class="p-4"><?= e($baris['kota'] ?: '—') ?> / <?= e($baris['provinsi'] ?: '—') ?></td><td class="p-4"><?= e($baris['jumlah_siswa']) ?></td><td class="p-4"><?= e($baris['jumlah_guru']) ?></td><td class="p-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>"><?= e(ucfirst($baris['status'])) ?></span></td>
      <td class="p-4"><div class="flex flex-wrap gap-2"><a class="text-xs font-semibold text-blue-700 hover:underline" href="<?= e(url('admin/sekolah_form.php?id=' . $baris['id'])) ?>">Ubah</a><form method="post" action="<?= e(url('admin/sekolah.php')) ?>" onsubmit="return confirm('<?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> sekolah ini? Akun pengguna di dalam sekolah tidak ikut berubah.');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($baris['id']) ?>"><input type="hidden" name="status_baru" value="<?= $baris['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>"><button class="text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'text-rose-700' : 'text-emerald-700' ?> hover:underline"><?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?></button></form></div></td>
    </tr>
  <?php endforeach; endif; ?></tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
