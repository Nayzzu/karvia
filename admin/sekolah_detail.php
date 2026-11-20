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
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"><div><a href="<?= e(url('admin/sekolah.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline">&larr; Daftar Sekolah</a><h1 class="mt-3 text-2xl font-extrabold text-slate-900"><?= e($sekolah['nama']) ?></h1><p class="mt-1 text-sm text-slate-500">Detail profil dan akun yang terhubung ke sekolah ini.</p></div><a href="<?= e(url('admin/sekolah_form.php?id=' . $id)) ?>" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold">Ubah Sekolah</a></div>
<section class="mb-6 grid gap-4 sm:grid-cols-3"><article class="rounded-2xl border border-slate-200 bg-white p-5"><div class="text-xs font-semibold uppercase text-slate-400">Siswa</div><div class="mt-2 text-2xl font-extrabold"><?= e($sekolah['jumlah_siswa']) ?></div></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><div class="text-xs font-semibold uppercase text-slate-400">Guru BK</div><div class="mt-2 text-2xl font-extrabold"><?= e($sekolah['jumlah_guru']) ?></div></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><div class="text-xs font-semibold uppercase text-slate-400">Catatan Dibagikan</div><div class="mt-2 text-2xl font-extrabold"><?= e($sekolah['jumlah_catatan']) ?></div></article></section>
<section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6"><h2 class="mb-4 text-lg font-bold">Profil Sekolah</h2><dl class="grid gap-4 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Jenjang</dt><dd class="font-semibold"><?= e($sekolah['jenjang']) ?></dd></div><div><dt class="text-slate-500">NPSN</dt><dd class="font-semibold"><?= e($sekolah['npsn'] ?: '—') ?></dd></div><div><dt class="text-slate-500">Kota / Provinsi</dt><dd class="font-semibold"><?= e($sekolah['kota'] ?: '—') ?> / <?= e($sekolah['provinsi'] ?: '—') ?></dd></div><div><dt class="text-slate-500">Status</dt><dd class="font-semibold"><?= e(ucfirst($sekolah['status'])) ?></dd></div><div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd class="font-semibold"><?= nl2br(e($sekolah['alamat'] ?: '—')) ?></dd></div></dl></section>
<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"><div class="flex items-center justify-between border-b border-slate-100 p-6"><div><h2 class="text-lg font-bold">Akun Terkait</h2><p class="mt-1 text-xs text-slate-500">Tidak menampilkan catatan pribadi siswa.</p></div><a href="<?= e(url('admin/pengguna_form.php?sekolah_id=' . $id)) ?>" class="text-sm font-semibold text-emerald-800 hover:underline">Tambah Pengguna</a></div><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-4">Nama / Username</th><th class="p-4">Peran</th><th class="p-4">Kelas</th><th class="p-4">Status</th><th class="p-4">Login Terakhir</th></tr></thead><tbody><?php if (!$akun): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada akun yang terhubung.</td></tr><?php else: foreach ($akun as $baris): ?><tr class="border-t border-slate-100"><td class="p-4"><div class="flex items-center gap-2"><div class="font-semibold"><?= e($baris['nama_lengkap']) ?></div><?php if (!empty($baris['sedang_online'])): ?><span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>Online</span><?php endif; ?></div><div class="text-xs text-slate-500">@<?= e($baris['username']) ?></div></td><td class="p-4"><?= e($baris['role']==='guru_bk'?'Guru BK':ucfirst($baris['role'])) ?></td><td class="p-4"><?= e($baris['kelas'] ?: '—') ?></td><td class="p-4"><?= e(ucfirst($baris['status'])) ?></td><td class="p-4"><?= e(tanggal_id($baris['last_login_at'],true)) ?></td></tr><?php endforeach; endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
