<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'kenali_diri';
$judul_halaman = 'Kenali Diriku';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$mapel_opsi = ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika', 'IPA', 'IPS', 'Seni dan Budaya', 'Informatika', 'Pendidikan Jasmani'];
$kekuatan_opsi = ['Kreatif', 'Teliti', 'Komunikatif', 'Senang membantu', 'Berpikir logis', 'Bekerja sama', 'Mandiri', 'Tekun'];
$stmt = $pdo->prepare('SELECT mapel_favorit, kekuatan, catatan_minat FROM refleksi_diri WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$data = $stmt->fetch() ?: ['mapel_favorit' => '[]', 'kekuatan' => '[]', 'catatan_minat' => ''];
$mapel = json_decode($data['mapel_favorit'] ?: '[]', true) ?: [];
$kekuatan = json_decode($data['kekuatan'] ?: '[]', true) ?: [];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $mapel_masuk = $_POST['mapel_favorit'] ?? [];
    $kekuatan_masuk = $_POST['kekuatan'] ?? [];
    if (!is_array($mapel_masuk) || !is_array($kekuatan_masuk)) {
        $errors[] = 'Pilihan tidak valid.';
        $mapel_masuk = $kekuatan_masuk = [];
    }
    $mapel = array_values(array_unique(array_intersect($mapel_opsi, array_map('strval', $mapel_masuk))));
    $kekuatan = array_values(array_unique(array_intersect($kekuatan_opsi, array_map('strval', $kekuatan_masuk))));
    $minat = trim((string) ($_POST['catatan_minat'] ?? ''));
    if (mb_strlen($minat) > 5000) $errors[] = 'Catatan minat maksimal 5.000 karakter.';
    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO refleksi_diri (siswa_id, mapel_favorit, kekuatan, catatan_minat, dibuat_pada)
             VALUES (:siswa_id, :mapel, :kekuatan, :minat, NOW())
             ON DUPLICATE KEY UPDATE mapel_favorit = VALUES(mapel_favorit), kekuatan = VALUES(kekuatan), catatan_minat = VALUES(catatan_minat), diperbarui_pada = NOW()'
        );
        $stmt->execute(['siswa_id' => $siswa_id, 'mapel' => json_encode($mapel, JSON_UNESCAPED_UNICODE), 'kekuatan' => json_encode($kekuatan, JSON_UNESCAPED_UNICODE), 'minat' => $minat ?: null]);
        flash_set('sukses', 'Refleksi berhasil disimpan.');
        redirect('siswa/kenali_diri.php');
    }
    $data['catatan_minat'] = $minat;
}
?>
<div class="mb-6"><h1 class="text-2xl font-extrabold">Kenali Diriku</h1><p class="mt-1 text-sm text-slate-600">Kenali hal yang kamu sukai dan kekuatan yang ingin kamu kembangkan.</p></div>
<?php if ($errors): ?><div role="alert" class="mb-4 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form method="post" class="max-w-3xl space-y-5 rounded-2xl border border-slate-200 bg-white p-6"><?= csrf_field() ?>
<fieldset><legend class="mb-3 font-bold">Mata pelajaran yang kamu sukai</legend><div class="grid gap-2 sm:grid-cols-2"><?php foreach ($mapel_opsi as $opsi): ?><label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm"><input type="checkbox" name="mapel_favorit[]" value="<?= e($opsi) ?>" <?= in_array($opsi, $mapel, true) ? 'checked' : '' ?>><?= e($opsi) ?></label><?php endforeach; ?></div></fieldset>
<fieldset><legend class="mb-3 font-bold">Kekuatan yang kamu rasakan</legend><div class="grid gap-2 sm:grid-cols-2"><?php foreach ($kekuatan_opsi as $opsi): ?><label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm"><input type="checkbox" name="kekuatan[]" value="<?= e($opsi) ?>" <?= in_array($opsi, $kekuatan, true) ? 'checked' : '' ?>><?= e($opsi) ?></label><?php endforeach; ?></div></fieldset>
<div><label for="catatan_minat" class="mb-1 block font-bold">Minat atau hal yang ingin kamu jelajahi</label><textarea id="catatan_minat" name="catatan_minat" maxlength="5000" rows="5" class="w-full rounded-xl border border-slate-300 p-3"><?= e($data['catatan_minat']) ?></textarea></div><button class="rounded-xl bg-emerald-700 px-5 py-2.5 font-bold text-white">Simpan Refleksi</button></form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
