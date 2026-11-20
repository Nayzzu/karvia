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
<section class="mb-6 rounded-3xl bg-gradient-to-r from-slate-900 to-karvia-900 p-6 text-white shadow-xl sm:p-8">
  <span class="mb-2 inline-block rounded-full border border-karvia-400/30 bg-karvia-500/30 px-3 py-1 text-xs font-bold uppercase tracking-wider text-karvia-100">Dashboard khusus Guru BK</span>
  <h1 class="text-2xl font-extrabold sm:text-3xl">Selamat datang, <?= e(auth_pengguna()['nama_lengkap'] ?? '') ?></h1>
  <p class="mt-2 max-w-2xl text-sm text-slate-300">Pantau catatan eksplorasi yang dibagikan siswa di sekolah Anda dan berikan tindak lanjut.</p>
</section>
<section class="mb-6 rounded-xl border-l-4 border-blue-500 bg-blue-50 p-4"><p class="text-xs text-blue-900"><i class="fa-solid fa-shield-halved mr-2"></i>Hanya catatan yang dibagikan oleh siswa di sekolah Anda yang dapat dilihat.</p></section>
<section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan dashboard">
  <?php foreach ([['Siswa aktif', $stat['jumlah_siswa']], ['Catatan baru', $stat['catatan_baru']], ['Ditindaklanjuti', $stat['ditindaklanjuti']], ['Total dibagikan', $stat['total_catatan']]] as [$label, $jumlah]): ?>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-400"><?= e($label) ?></div><div class="mt-1 text-2xl font-extrabold text-slate-900"><?= e($jumlah) ?></div></article>
  <?php endforeach; ?>
</section>
<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
  <div class="border-b border-slate-100 p-6"><h2 class="text-lg font-bold">Catatan yang Dibagikan</h2><p class="mt-1 text-xs text-slate-500">Data dibatasi ke sekolah Anda.</p></div>
  <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-4">Nama Siswa</th><th class="p-4">Kelas</th><th class="p-4">Dibagikan</th><th class="p-4">Status</th><th class="p-4">Aksi</th></tr></thead><tbody>
  <?php if (!$catatan): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada siswa yang membagikan catatan karier.</td></tr><?php else: foreach ($catatan as $baris): ?>
    <tr class="border-t border-slate-100"><td class="p-4 font-semibold"><?= e($baris['nama_lengkap']) ?></td><td class="p-4"><?= e($baris['kelas'] ?: '—') ?></td><td class="p-4"><?= e(tanggal_id($baris['dibagikan_pada'], true)) ?></td><td class="p-4"><?= e(ucfirst($baris['status'])) ?></td><td class="p-4"><a class="font-semibold text-emerald-800 hover:underline" href="<?= e(url('guru_bk/catatan_detail.php?id=' . (int) $baris['id'])) ?>">Buka detail</a></td></tr>
  <?php endforeach; endif; ?></tbody></table></div>
</section>
<p class="mt-4 text-right text-sm"><a class="font-semibold text-emerald-800 hover:underline" href="<?= e(url('guru_bk/catatan.php')) ?>">Lihat semua catatan →</a></p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
