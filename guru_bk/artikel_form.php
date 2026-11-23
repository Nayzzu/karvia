<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'artikel';
$judul_halaman = 'Buat Artikel';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$guru = auth_pengguna();
$guru_id = (int) $guru['id'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$artikel = ['judul' => '', 'isi' => '', 'gambar' => ''];
if ($id) {
    $stmt = $pdo->prepare('SELECT id, judul, isi, gambar FROM artikel WHERE id = :id AND guru_id = :guru_id');
    $stmt->execute(['id' => $id, 'guru_id' => $guru_id]);
    $artikel = $stmt->fetch();
    if (!$artikel) {
        http_response_code(404);
        exit('Artikel tidak ditemukan.');
    }
    $judul_halaman = 'Ubah Artikel';
}
$kesalahan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    if ($id && (!isset($_POST['id']) || (int) $_POST['id'] !== $id)) {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
    if (!$id && !empty($_POST['id'])) {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
    $judul = trim((string) ($_POST['judul'] ?? ''));
    $isi = trim((string) ($_POST['isi'] ?? ''));
    $artikel['judul'] = $judul;
    $artikel['isi'] = $isi;
    if ($judul === '' || mb_strlen($judul) > 200) {
        $kesalahan = 'Judul wajib diisi dan maksimal 200 karakter.';
    } elseif ($isi === '' || mb_strlen($isi) > 100000) {
        $kesalahan = 'Isi artikel wajib diisi dan maksimal 100.000 karakter.';
    }

    $file = $_FILES['gambar'] ?? null;
    $gambar_baru = null;
    if (!$kesalahan && $file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'])) {
            $kesalahan = 'Gambar gagal diunggah atau ukurannya melebihi 2 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $ekstensi = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
            $info_gambar = @getimagesize($file['tmp_name']);
            if (!$ekstensi || !$info_gambar || $info_gambar[0] < 1 || $info_gambar[1] < 1 || $info_gambar[0] > 6000 || $info_gambar[1] > 6000) {
                $kesalahan = 'Gunakan gambar JPG, PNG, atau WebP yang valid, maksimal 6000 × 6000 piksel.';
            } else {
                $direktori = __DIR__ . '/../uploads/artikel';
                if (!is_dir($direktori) && !mkdir($direktori, 0755, true) && !is_dir($direktori)) {
                    $kesalahan = 'Penyimpanan gambar belum tersedia. Coba lagi nanti.';
                } else {
                    $gambar_baru = bin2hex(random_bytes(16)) . '.' . $ekstensi;
                }
            }
        }
    }
    if (!$kesalahan && !$id && !$gambar_baru) {
        $kesalahan = 'Gambar sampul wajib dipilih.';
    }

    if (!$kesalahan) {
        $tujuan = $gambar_baru ? $direktori . '/' . $gambar_baru : null;
        if ($gambar_baru && !move_uploaded_file($file['tmp_name'], $tujuan)) {
            $kesalahan = 'Gambar gagal disimpan. Coba lagi.';
        } else {
            try {
                if ($id) {
                    $sql = 'UPDATE artikel SET judul = :judul, isi = :isi, diperbarui_pada = NOW()';
                    if ($gambar_baru) {
                        $sql .= ', gambar = :gambar';
                    }
                    $sql .= ' WHERE id = :id AND guru_id = :guru_id';
                    $params = ['judul' => $judul, 'isi' => $isi, 'id' => $id, 'guru_id' => $guru_id];
                    if ($gambar_baru) {
                        $params['gambar'] = $gambar_baru;
                    }
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    if ($gambar_baru && $artikel['gambar'] !== $gambar_baru) {
                        $lama = __DIR__ . '/../uploads/artikel/' . basename($artikel['gambar']);
                        if (is_file($lama) && !unlink($lama)) {
                            error_log('KARVIA: gagal menghapus gambar artikel lama.');
                        }
                    }
                    log_aktivitas('ubah_artikel', 'Guru BK mengubah artikel miliknya.');
                    flash_set('sukses', 'Artikel berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare('INSERT INTO artikel (guru_id, nama_penulis, judul, gambar, isi, diterbitkan_pada) VALUES (:guru_id, :nama_penulis, :judul, :gambar, :isi, NOW())');
                    $stmt->execute(['guru_id' => $guru_id, 'nama_penulis' => $guru['nama_lengkap'], 'judul' => $judul, 'gambar' => $gambar_baru, 'isi' => $isi]);
                    log_aktivitas('terbit_artikel', 'Guru BK menerbitkan artikel.');
                    flash_set('sukses', 'Artikel berhasil diterbitkan.');
                }
                redirect('guru_bk/artikel.php');
            } catch (PDOException $exception) {
                if ($gambar_baru && is_file($tujuan)) unlink($tujuan);
                error_log('KARVIA: gagal menyimpan artikel: ' . $exception->getMessage());
                $kesalahan = 'Artikel belum dapat disimpan. Coba lagi nanti.';
            }
        }
    }
}
?>
<div class="space-y-5">
  <div class="flex flex-col gap-1">
    <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#245938] hover:underline" href="<?= e(url('guru_bk/artikel.php')) ?>">
      &larr; Kembali ke Daftar Artikel
    </a>
    <h2 class="mt-1 text-xl font-bold tracking-tight text-[#203628]"><?= $id ? 'Ubah Artikel Edukasi' : 'Tulis Artikel Edukasi Baru' ?></h2>
    <p class="text-xs text-slate-500">Artikel akan langsung terbit pada landing page KARVIA setelah disimpan.</p>
  </div>

  <?php if ($kesalahan): ?>
    <div class="max-w-3xl rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs text-rose-900 shadow-sm" role="alert">
      <div class="font-bold mb-1">Periksa kembali:</div>
      <p><?= e($kesalahan) ?></p>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="max-w-3xl space-y-5 rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <div>
      <label for="judul" class="mb-1 block text-xs font-bold text-slate-700">Judul Artikel *</label>
      <input id="judul" name="judul" required maxlength="200" value="<?= e($artikel['judul']) ?>" placeholder="Contoh: Menemukan Minat Karier Lewat Hal yang Disukai Sehari-hari" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
    </div>

    <div>
      <label for="gambar" class="mb-1 block text-xs font-bold text-slate-700">
        Gambar Sampul <?= $id ? '<span class="font-normal text-slate-500">(opsional jika tidak diganti)</span>' : '*' ?>
      </label>
      <input id="gambar" name="gambar" type="file" accept="image/jpeg,image/png,image/webp" <?= $id ? '' : 'required' ?> class="block w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] p-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#e8f0e9] file:px-3 file:py-1 file:text-xs file:font-semibold file:text-[#245938] hover:file:bg-[#d8e7da]">
      <p class="mt-1 text-[11px] text-slate-500">Format: JPG, PNG, atau WebP. Ukuran maksimal 2 MB.</p>
      <?php if ($id && !empty($artikel['gambar'])): ?>
        <div class="mt-3">
          <p class="text-[11px] font-semibold text-slate-500 mb-1.5">Gambar saat ini:</p>
          <img src="<?= e(url('uploads/artikel/' . rawurlencode($artikel['gambar']))) ?>" alt="Gambar artikel saat ini" class="max-h-48 rounded-xl object-cover border border-[#dfe6de]">
        </div>
      <?php endif; ?>
    </div>

    <div>
      <label for="isi" class="mb-1 block text-xs font-bold text-slate-700">Isi Artikel Edukasi *</label>
      <textarea id="isi" name="isi" required maxlength="100000" rows="12" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] p-3 text-xs leading-relaxed text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]" placeholder="Tuliskan materi edukasi, wawasan, atau tips bimbingan karier..."><?= e($artikel['isi']) ?></textarea>
      <p class="mt-1 text-[11px] text-slate-400">Gunakan format paragraf biasa. Penulis otomatis tercantum: <strong><?= e($guru['nama_lengkap']) ?></strong>.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3 pt-2">
      <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1a4029]">
        <?= $id ? 'Simpan Perubahan' : 'Terbitkan Artikel' ?>
      </button>
      <a href="<?= e(url('guru_bk/artikel.php')) ?>" class="inline-flex items-center justify-center rounded-xl border border-[#dfe6de] bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-[#f6f8f5]">
        Batal
      </a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
