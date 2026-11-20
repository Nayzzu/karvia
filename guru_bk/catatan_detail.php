<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'catatan';
$judul_halaman = 'Detail Catatan Siswa';
require_once __DIR__ . '/../includes/header.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($id < 1) {
    http_response_code(404);
    exit('Catatan tidak ditemukan.');
}
$pdo = db();
$sekolah_id = auth_sekolah_id();
$guru_id = (int) auth_pengguna()['id'];
$stmt = $pdo->prepare(
    "SELECT b.id, b.siswa_id, b.sekolah_id, b.pesan_siswa, b.status, b.dibagikan_pada,
            p.nama_lengkap, p.username, p.kelas, p.jurusan, p.sekolah_id AS sekolah_siswa,
            s.nama AS nama_sekolah
     FROM berbagi_catatan b
     JOIN pengguna p ON p.id = b.siswa_id
     JOIN sekolah s ON s.id = b.sekolah_id
     WHERE b.id = :id AND b.sekolah_id = :sekolah_id
       AND p.sekolah_id = :sekolah_siswa AND p.role = 'siswa'
     LIMIT 1"
);
$stmt->execute(['id' => $id, 'sekolah_id' => $sekolah_id, 'sekolah_siswa' => $sekolah_id]);
$catatan = $stmt->fetch();
if (!$catatan) {
    http_response_code(404);
    exit('Catatan tidak ditemukan.');
}
if ($catatan['status'] === 'baru') {
    $stmt = $pdo->prepare("UPDATE berbagi_catatan SET status = 'dibaca', dibaca_pada = NOW(), diperbarui_pada = NOW() WHERE id = :id AND sekolah_id = :sekolah_id AND status = 'baru'");
    $stmt->execute(['id' => $id, 'sekolah_id' => $sekolah_id]);
    $catatan['status'] = 'dibaca';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $aksi = (string) ($_POST['aksi'] ?? '');
    if ((int) ($_POST['id'] ?? 0) !== $id || $aksi !== 'tindak_lanjut') {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
    $isi = trim((string) ($_POST['catatan'] ?? ''));
    if ($isi === '' || mb_strlen($isi) > 5000) {
        flash_set('gagal', 'Catatan tindak lanjut wajib diisi dan maksimal 5.000 karakter.');
        redirect('guru_bk/catatan_detail.php?id=' . $id);
    }
    try {
        $pdo->beginTransaction();
        $cek = $pdo->prepare('SELECT id FROM berbagi_catatan WHERE id = :id AND sekolah_id = :sekolah_id FOR UPDATE');
        $cek->execute(['id' => $id, 'sekolah_id' => $sekolah_id]);
        $berbagi = $cek->fetchColumn();
        $cekSiswa = $pdo->prepare("SELECT COUNT(*) FROM pengguna WHERE id = :siswa_id AND sekolah_id = :sekolah_id AND role = 'siswa'");
        $cekSiswa->execute(['siswa_id' => (int) $catatan['siswa_id'], 'sekolah_id' => $sekolah_id]);
        if (!$berbagi || !(int) $cekSiswa->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('Catatan tidak ditemukan.');
        }
        $stmt = $pdo->prepare('INSERT INTO tindak_lanjut_bk (berbagi_id, guru_id, catatan, dibuat_pada) VALUES (:berbagi_id, :guru_id, :catatan, NOW())');
        $stmt->execute(['berbagi_id' => $id, 'guru_id' => $guru_id, 'catatan' => $isi]);
        $stmt = $pdo->prepare("UPDATE berbagi_catatan SET status = 'ditindaklanjuti', diperbarui_pada = NOW() WHERE id = :id AND sekolah_id = :sekolah_id");
        $stmt->execute(['id' => $id, 'sekolah_id' => $sekolah_id]);
        $pdo->commit();
        log_aktivitas('tindak_lanjut_catatan', 'Guru BK menambahkan tindak lanjut pada catatan yang dibagikan.');
        flash_set('sukses', 'Tindak lanjut berhasil disimpan.');
        redirect('guru_bk/catatan_detail.php?id=' . $id);
    } catch (PDOException $kesalahan) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('KARVIA: gagal menyimpan tindak lanjut: ' . $kesalahan->getMessage());
        flash_set('gagal', 'Tindak lanjut belum dapat disimpan. Coba lagi nanti.');
        redirect('guru_bk/catatan_detail.php?id=' . $id);
    }
}

$stmt = $pdo->prepare('SELECT mapel_favorit, kekuatan, catatan_minat FROM refleksi_diri WHERE siswa_id = :siswa_id LIMIT 1');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$refleksi = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT pr.nama FROM profesi_disimpan pd JOIN profesi pr ON pr.id = pd.profesi_id WHERE pd.siswa_id = :siswa_id ORDER BY pr.nama');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$profesi = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT opsi_a, catatan_a, opsi_b, catatan_b, kriteria FROM pilihan_banding WHERE siswa_id = :siswa_id LIMIT 1');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$banding = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT tujuan, langkah_1, langkah_2 FROM rencana_karier WHERE siswa_id = :siswa_id LIMIT 1');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$rencana = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT hal_baru, hambatan, dukungan, dibuat_pada FROM jurnal_refleksi WHERE siswa_id = :siswa_id ORDER BY dibuat_pada DESC, id DESC');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$jurnal = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT t.catatan, t.dibuat_pada, p.nama_lengkap AS nama_guru FROM tindak_lanjut_bk t JOIN pengguna p ON p.id = t.guru_id WHERE t.berbagi_id = :berbagi_id ORDER BY t.dibuat_pada DESC, t.id DESC');
$stmt->execute(['berbagi_id' => $id]);
$tindak_lanjut = $stmt->fetchAll();
function guru_nilai($nilai): string
{
    if ($nilai === null || $nilai === '') return 'Belum diisi';
    if (is_array($nilai)) return $nilai ? implode(', ', array_map('strval', $nilai)) : 'Belum diisi';
    return (string) $nilai;
}
function guru_json_daftar($nilai): string
{
    if (!$nilai) return 'Belum diisi';
    $data = json_decode((string) $nilai, true);
    return is_array($data) ? guru_nilai($data) : 'Belum diisi';
}
?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3 print:hidden"><a href="<?= e(url('guru_bk/catatan.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline">← Kembali ke catatan</a><button type="button" onclick="window.print()" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">Cetak ringkasan</button></div>
<section class="mb-5 rounded-2xl border border-slate-200 bg-white p-5"><h1 class="text-2xl font-extrabold"><?= e($catatan['nama_lengkap']) ?></h1><p class="mt-1 text-sm text-slate-600"><?= e($catatan['kelas'] ?: 'Kelas belum diisi') ?><?= $catatan['jurusan'] ? ' · ' . e($catatan['jurusan']) : '' ?> · <?= e($catatan['nama_sekolah']) ?></p><p class="mt-2 text-sm">Dibagikan <?= e(tanggal_id($catatan['dibagikan_pada'], true)) ?> · Status: <strong><?= e(ucfirst($catatan['status'])) ?></strong></p><?php if ($catatan['pesan_siswa']): ?><div class="mt-4 rounded-xl bg-emerald-50 p-4"><h2 class="text-sm font-bold">Pesan siswa</h2><p class="mt-1 whitespace-pre-wrap text-sm"><?= e($catatan['pesan_siswa']) ?></p></div><?php endif; ?></section>
<div class="grid gap-5 xl:grid-cols-2">
<section class="rounded-2xl border border-slate-200 bg-white p-5"><h2 class="font-bold">Kenali Diriku</h2><dl class="mt-3 space-y-3 text-sm"><div><dt class="font-semibold text-slate-500">Mapel favorit</dt><dd><?= e(guru_json_daftar($refleksi['mapel_favorit'] ?? null)) ?></dd></div><div><dt class="font-semibold text-slate-500">Kekuatan</dt><dd><?= e(guru_json_daftar($refleksi['kekuatan'] ?? null)) ?></dd></div><div><dt class="font-semibold text-slate-500">Catatan minat</dt><dd class="whitespace-pre-wrap"><?= e(guru_nilai($refleksi['catatan_minat'] ?? null)) ?></dd></div></dl></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5"><h2 class="font-bold">Profesi yang Disimpan</h2><?php if (!$profesi): ?><p class="mt-3 text-sm text-slate-500">Belum ada profesi disimpan.</p><?php else: ?><ul class="mt-3 list-inside list-disc text-sm"><?php foreach ($profesi as $row): ?><li><?= e($row['nama']) ?></li><?php endforeach; ?></ul><?php endif; ?></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5"><h2 class="font-bold">Bandingkan Pilihan</h2><?php if (!$banding): ?><p class="mt-3 text-sm text-slate-500">Belum diisi.</p><?php else: ?><div class="mt-3 space-y-3 text-sm"><p><strong>A:</strong> <?= e($banding['opsi_a']) ?><br><?= e(guru_nilai($banding['catatan_a'])) ?></p><p><strong>B:</strong> <?= e($banding['opsi_b']) ?><br><?= e(guru_nilai($banding['catatan_b'])) ?></p><p><strong>Kriteria:</strong> <?= e(guru_json_daftar($banding['kriteria'])) ?></p></div><?php endif; ?></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5"><h2 class="font-bold">Rencana Karier</h2><?php if (!$rencana): ?><p class="mt-3 text-sm text-slate-500">Belum diisi.</p><?php else: ?><dl class="mt-3 space-y-2 text-sm"><div><dt class="font-semibold">Tujuan</dt><dd><?= e($rencana['tujuan']) ?></dd></div><div><dt class="font-semibold">Langkah 1</dt><dd><?= e($rencana['langkah_1']) ?></dd></div><div><dt class="font-semibold">Langkah 2</dt><dd><?= e($rencana['langkah_2']) ?></dd></div></dl><?php endif; ?></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 xl:col-span-2"><h2 class="font-bold">Jurnal Refleksi</h2><?php if (!$jurnal): ?><p class="mt-3 text-sm text-slate-500">Belum ada jurnal.</p><?php else: ?><div class="mt-3 space-y-4"><?php foreach ($jurnal as $entri): ?><article class="border-t border-slate-100 pt-3"><p class="text-xs text-slate-500"><?= e(tanggal_id($entri['dibuat_pada'], true)) ?></p><?php foreach (['hal_baru' => 'Hal baru', 'hambatan' => 'Hambatan', 'dukungan' => 'Dukungan'] as $kolom => $label): ?><?php if ($entri[$kolom] !== null && $entri[$kolom] !== ''): ?><p class="mt-2 whitespace-pre-wrap text-sm"><strong><?= e($label) ?>:</strong> <?= e($entri[$kolom]) ?></p><?php endif; ?><?php endforeach; ?></article><?php endforeach; ?></div><?php endif; ?></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 xl:col-span-2"><h2 class="font-bold">Tindak Lanjut Guru BK</h2><?php if ($tindak_lanjut): ?><ol class="mt-3 space-y-3"><?php foreach ($tindak_lanjut as $item): ?><li class="rounded-xl bg-slate-50 p-4"><p class="whitespace-pre-wrap text-sm"><?= e($item['catatan']) ?></p><p class="mt-2 text-xs text-slate-500"><?= e($item['nama_guru']) ?> · <?= e(tanggal_id($item['dibuat_pada'], true)) ?></p></li><?php endforeach; ?></ol><?php else: ?><p class="mt-3 text-sm text-slate-500">Belum ada tindak lanjut.</p><?php endif; ?><form method="post" class="mt-5 space-y-3 print:hidden"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $id ?>"><input type="hidden" name="aksi" value="tindak_lanjut"><label for="catatan" class="block text-sm font-semibold">Tambah catatan tindak lanjut</label><textarea id="catatan" name="catatan" required maxlength="5000" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2" placeholder="Tuliskan tindak lanjut untuk sesi bimbingan berikutnya"></textarea><p class="text-xs text-slate-500">Maksimal 5.000 karakter.</p><button class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">Simpan tindak lanjut</button></form></section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
