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
<div class="mb-6"><h1 class="text-2xl font-extrabold text-slate-900">Catatan Siswa</h1><p class="mt-1 text-sm text-slate-600">Hanya catatan yang dibagikan siswa di sekolah Anda.</p></div>
<form method="get" action="<?= e(url('guru_bk/catatan.php')) ?>" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-3">
  <div><label for="cari" class="mb-1 block text-xs font-semibold">Cari siswa</label><input id="cari" name="cari" value="<?= e($cari) ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
  <div><label for="status" class="mb-1 block text-xs font-semibold">Status</label><select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Semua status</option><?php foreach (['baru' => 'Baru', 'dibaca' => 'Dibaca', 'ditindaklanjuti' => 'Ditindaklanjuti'] as $nilai => $label): ?><option value="<?= e($nilai) ?>" <?= $status === $nilai ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
  <div class="flex items-end gap-2"><button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Terapkan</button><a href="<?= e(url('guru_bk/catatan.php')) ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Reset</a></div>
</form>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-4">Siswa</th><th class="p-4">Dibagikan</th><th class="p-4">Status</th><th class="p-4">Tindak lanjut</th><th class="p-4">Aksi</th></tr></thead><tbody>
<?php if (!$catatan): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada catatan yang cocok.</td></tr><?php else: foreach ($catatan as $baris): ?><tr class="border-t border-slate-100"><td class="p-4"><div class="font-semibold"><?= e($baris['nama_lengkap']) ?></div><div class="text-xs text-slate-500"><?= e($baris['kelas'] ?: 'Kelas belum diisi') ?> · <?= e($baris['username']) ?></div></td><td class="whitespace-nowrap p-4"><?= e(tanggal_id($baris['dibagikan_pada'], true)) ?></td><td class="p-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold"><?= e(ucfirst($baris['status'])) ?></span></td><td class="p-4"><?= e($baris['jumlah_tindak_lanjut']) ?></td><td class="p-4"><a class="font-semibold text-emerald-800 hover:underline" href="<?= e(url('guru_bk/catatan_detail.php?id=' . (int) $baris['id'])) ?>">Buka detail</a></td></tr><?php endforeach; endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
