<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'sekolah';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$judul_halaman = $id ? 'Ubah Sekolah' : 'Tambah Sekolah';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$data = ['nama' => '', 'npsn' => '', 'jenjang' => 'SMA', 'alamat' => '', 'kota' => '', 'provinsi' => '', 'status' => 'aktif'];
if ($id) {
    $stmt = $pdo->prepare('SELECT id, nama, npsn, jenjang, alamat, kota, provinsi, status FROM sekolah WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $sekolah = $stmt->fetch();
    if (!$sekolah) {
        http_response_code(404);
        exit('Sekolah tidak ditemukan.');
    }
    $data = $sekolah;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $data = [
        'nama' => trim((string) ($_POST['nama'] ?? '')),
        'npsn' => trim((string) ($_POST['npsn'] ?? '')),
        'jenjang' => (string) ($_POST['jenjang'] ?? ''),
        'alamat' => trim((string) ($_POST['alamat'] ?? '')),
        'kota' => trim((string) ($_POST['kota'] ?? '')),
        'provinsi' => trim((string) ($_POST['provinsi'] ?? '')),
        'status' => (string) ($_POST['status'] ?? 'aktif'),
    ];
    if (mb_strlen($data['nama']) < 3 || mb_strlen($data['nama']) > 150) $errors[] = 'Nama sekolah wajib diisi (3–150 karakter).';
    if (mb_strlen($data['npsn']) > 20) $errors[] = 'NPSN maksimal 20 karakter.';
    if (!in_array($data['jenjang'], ['SMA', 'MA'], true)) $errors[] = 'Jenjang harus SMA atau MA.';
    if (mb_strlen($data['alamat']) > 500) $errors[] = 'Alamat maksimal 500 karakter.';
    if (mb_strlen($data['kota']) > 100 || mb_strlen($data['provinsi']) > 100) $errors[] = 'Kota dan provinsi maksimal 100 karakter.';
    if (!in_array($data['status'], ['aktif', 'nonaktif'], true)) $errors[] = 'Status sekolah tidak valid.';
    if (!$errors && $data['npsn'] !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM sekolah WHERE npsn = :npsn AND id <> :id');
        $stmt->execute(['npsn' => $data['npsn'], 'id' => $id_post]);
        if ((int) $stmt->fetchColumn() > 0) $errors[] = 'NPSN sudah digunakan oleh sekolah lain.';
    }
    if (!$errors) {
        try {
            if ($id_post) {
                $stmt = $pdo->prepare('UPDATE sekolah SET nama=:nama,npsn=:npsn,jenjang=:jenjang,alamat=:alamat,kota=:kota,provinsi=:provinsi,status=:status,diperbarui_pada=NOW() WHERE id=:id');
                $stmt->execute(['nama'=>$data['nama'],'npsn'=>$data['npsn'] ?: null,'jenjang'=>$data['jenjang'],'alamat'=>$data['alamat'] ?: null,'kota'=>$data['kota'] ?: null,'provinsi'=>$data['provinsi'] ?: null,'status'=>$data['status'],'id'=>$id_post]);
                log_aktivitas('ubah_sekolah', 'Data sekolah diperbarui.');
                flash_set('sukses', 'Data sekolah berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO sekolah (nama,npsn,jenjang,alamat,kota,provinsi,status,dibuat_pada) VALUES (:nama,:npsn,:jenjang,:alamat,:kota,:provinsi,:status,NOW())');
                $stmt->execute(['nama'=>$data['nama'],'npsn'=>$data['npsn'] ?: null,'jenjang'=>$data['jenjang'],'alamat'=>$data['alamat'] ?: null,'kota'=>$data['kota'] ?: null,'provinsi'=>$data['provinsi'] ?: null,'status'=>$data['status']]);
                log_aktivitas('buat_sekolah', 'Sekolah baru ditambahkan.');
                flash_set('sukses', 'Sekolah baru berhasil ditambahkan.');
            }
            redirect('admin/sekolah.php');
        } catch (PDOException $kesalahan) {
            error_log('KARVIA: penyimpanan sekolah gagal: ' . $kesalahan->getMessage());
            $errors[] = 'Data sekolah tidak dapat disimpan. Periksa NPSN dan coba kembali.';
        }
    }
}
$judul_halaman = $id ? 'Ubah Sekolah' : 'Tambah Sekolah';
?>
<div class="mb-6"><a href="<?= e(url('admin/sekolah.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline">&larr; Kembali ke daftar sekolah</a><h1 class="mt-3 text-2xl font-extrabold text-slate-900"><?= e($judul_halaman) ?></h1><p class="mt-1 text-sm text-slate-500">Informasi sekolah digunakan untuk pengelompokan akun dan rekap.</p></div>
<?php if ($errors): ?><div class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><ul class="list-inside list-disc"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e(url('admin/sekolah_form.php' . ($id ? '?id=' . $id : ''))) ?>" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($id) ?>">
  <div><label for="nama" class="mb-1 block text-sm font-semibold">Nama sekolah <span class="text-rose-600">*</span></label><input id="nama" name="nama" required maxlength="150" value="<?= e($data['nama']) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"><p class="mt-1 text-xs text-slate-500">3–150 karakter.</p></div>
  <div class="grid gap-5 sm:grid-cols-2"><div><label for="npsn" class="mb-1 block text-sm font-semibold">NPSN</label><input id="npsn" name="npsn" maxlength="20" value="<?= e($data['npsn']) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"><p class="mt-1 text-xs text-slate-500">Opsional, harus unik jika diisi.</p></div><div><label for="jenjang" class="mb-1 block text-sm font-semibold">Jenjang</label><select id="jenjang" name="jenjang" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"><option value="SMA" <?= $data['jenjang']==='SMA'?'selected':'' ?>>SMA</option><option value="MA" <?= $data['jenjang']==='MA'?'selected':'' ?>>MA</option></select></div></div>
  <div><label for="alamat" class="mb-1 block text-sm font-semibold">Alamat</label><textarea id="alamat" name="alamat" rows="3" maxlength="500" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"><?= e($data['alamat']) ?></textarea></div>
  <div class="grid gap-5 sm:grid-cols-2"><div><label for="kota" class="mb-1 block text-sm font-semibold">Kota</label><input id="kota" name="kota" maxlength="100" value="<?= e($data['kota']) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"></div><div><label for="provinsi" class="mb-1 block text-sm font-semibold">Provinsi</label><input id="provinsi" name="provinsi" maxlength="100" value="<?= e($data['provinsi']) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"></div></div>
  <div><label for="status" class="mb-1 block text-sm font-semibold">Status</label><select id="status" name="status" class="w-full rounded-xl border border-slate-300 px-4 py-2.5"><option value="aktif" <?= $data['status']==='aktif'?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= $data['status']==='nonaktif'?'selected':'' ?>>Nonaktif</option></select></div>
  <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row"><button class="rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800">Simpan Sekolah</button><a href="<?= e(url('admin/sekolah.php')) ?>" class="rounded-xl border border-slate-300 px-5 py-3 text-center font-semibold">Batal</a></div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
