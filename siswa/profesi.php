<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'profesi';
$judul_halaman = 'Jelajah Profesi';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$kategori = (string) ($_GET['kategori'] ?? '');
$kategori_opsi = ['kesehatan' => 'Kesehatan', 'teknologi' => 'Teknologi', 'pendidikan' => 'Pendidikan', 'bisnis' => 'Bisnis', 'lainnya' => 'Lainnya'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id = filter_input(INPUT_POST, 'profesi_id', FILTER_VALIDATE_INT) ?: 0;
    $aksi = (string) ($_POST['aksi'] ?? '');
    if ($id < 1 || !in_array($aksi, ['simpan', 'hapus'], true)) {
        flash_set('gagal', 'Permintaan profesi tidak valid.');
        redirect('siswa/profesi.php');
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM profesi WHERE id = :id AND status = 'aktif'");
    $stmt->execute(['id' => $id]);
    if (!(int) $stmt->fetchColumn()) {
        http_response_code(404);
        exit('Profesi tidak ditemukan.');
    }
    if ($aksi === 'simpan') {
        $stmt = $pdo->prepare('INSERT IGNORE INTO profesi_disimpan (siswa_id, profesi_id, dibuat_pada) VALUES (:siswa_id, :profesi_id, NOW())');
        $stmt->execute(['siswa_id' => $siswa_id, 'profesi_id' => $id]);
    } else {
        $stmt = $pdo->prepare('DELETE FROM profesi_disimpan WHERE siswa_id = :siswa_id AND profesi_id = :profesi_id');
        $stmt->execute(['siswa_id' => $siswa_id, 'profesi_id' => $id]);
    }
    redirect('siswa/profesi.php' . (in_array($kategori, array_keys($kategori_opsi), true) ? '?kategori=' . rawurlencode($kategori) : ''));
}
if (!isset($kategori_opsi[$kategori])) $kategori = '';
$sql = "SELECT p.id, p.nama, p.kategori, p.deskripsi, p.kemampuan, p.jalur_pendidikan, (pd.id IS NOT NULL) AS tersimpan
        FROM profesi p LEFT JOIN profesi_disimpan pd ON pd.profesi_id = p.id AND pd.siswa_id = :siswa_id
        WHERE p.status = 'aktif'";
$parameter = ['siswa_id' => $siswa_id];
if ($kategori !== '') { $sql .= ' AND p.kategori = :kategori'; $parameter['kategori'] = $kategori; }
$sql .= ' ORDER BY p.urutan, p.nama';
$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$profesi = $stmt->fetchAll();
?>
<div class="mb-6"><h1 class="text-2xl font-extrabold">Jelajah Profesi</h1><p class="mt-1 text-sm text-slate-600">Cari tahu tugas, kemampuan, dan jalur belajar berbagai profesi.</p></div>
<form method="get" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4"><div><label for="kategori" class="mb-1 block text-sm font-semibold">Kategori</label><select id="kategori" name="kategori" class="rounded-lg border border-slate-300 px-3 py-2"><option value="">Semua kategori</option><?php foreach ($kategori_opsi as $nilai => $label): ?><option value="<?= e($nilai) ?>" <?= $kategori === $nilai ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div><button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-bold text-white">Terapkan</button><a href="<?= e(url('siswa/profesi.php')) ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Reset</a></form>
<?php if (!$profesi): ?><p class="rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-500">Belum ada profesi pada kategori ini.</p><?php else: ?><div class="grid gap-4 lg:grid-cols-2"><?php foreach ($profesi as $item): ?><article class="rounded-2xl border border-slate-200 bg-white p-5"><div class="flex items-start justify-between gap-3"><div><span class="text-xs font-bold uppercase text-emerald-700"><?= e($kategori_opsi[$item['kategori']] ?? $item['kategori']) ?></span><h2 class="mt-1 text-lg font-extrabold"><?= e($item['nama']) ?></h2></div><form method="post"><?= csrf_field() ?><input type="hidden" name="profesi_id" value="<?= (int) $item['id'] ?>"><input type="hidden" name="aksi" value="<?= $item['tersimpan'] ? 'hapus' : 'simpan' ?>"><button class="rounded-lg border px-3 py-2 text-xs font-bold <?= $item['tersimpan'] ? 'border-amber-300 text-amber-800' : 'border-emerald-300 text-emerald-800' ?>"><?= $item['tersimpan'] ? 'Hapus dari simpanan' : 'Simpan profesi' ?></button></form></div><p class="mt-3 text-sm text-slate-600"><?= e($item['deskripsi'] ?: 'Deskripsi belum tersedia.') ?></p><p class="mt-3 text-sm"><strong>Kemampuan:</strong> <?= e($item['kemampuan'] ?: 'Belum tersedia') ?></p><p class="mt-2 text-sm"><strong>Jalur belajar:</strong> <?= e($item['jalur_pendidikan'] ?: 'Belum tersedia') ?></p><a class="mt-4 inline-block text-sm font-semibold text-emerald-800 hover:underline" href="<?= e(url('siswa/bandingkan.php?profesi=' . (int) $item['id'])) ?>">Bandingkan pilihan ini</a></article><?php endforeach; ?></div><?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
