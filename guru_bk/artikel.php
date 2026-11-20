<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'artikel';
$judul_halaman = 'Artikel Edukasi';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$guru_id = (int) auth_pengguna()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $stmt = $pdo->prepare('SELECT id, gambar FROM artikel WHERE id = :id AND guru_id = :guru_id');
    $stmt->execute(['id' => $id, 'guru_id' => $guru_id]);
    $artikel = $stmt->fetch();
    if (!$artikel) {
        http_response_code(404);
        exit('Artikel tidak ditemukan.');
    }
    if (($_POST['aksi'] ?? '') === 'hapus') {
        try {
            $stmt = $pdo->prepare('DELETE FROM artikel WHERE id = :id AND guru_id = :guru_id');
            $stmt->execute(['id' => $id, 'guru_id' => $guru_id]);
        } catch (PDOException $exception) {
            error_log('KARVIA: gagal menghapus artikel: ' . $exception->getMessage());
            flash_set('gagal', 'Artikel belum dapat dihapus. Coba lagi nanti.');
            redirect('guru_bk/artikel.php');
        }
        $path = __DIR__ . '/../uploads/artikel/' . basename($artikel['gambar']);
        if (is_file($path) && !unlink($path)) {
            error_log('KARVIA: gagal menghapus gambar artikel ' . $artikel['gambar']);
        }
        log_aktivitas('hapus_artikel', 'Guru BK menghapus artikel miliknya.');
        flash_set('sukses', 'Artikel berhasil dihapus.');
        redirect('guru_bk/artikel.php');
    }
    http_response_code(400);
    exit('Permintaan tidak dapat diproses.');
}

$stmt = $pdo->prepare('SELECT id, judul, gambar, diterbitkan_pada, diperbarui_pada FROM artikel WHERE guru_id = :guru_id ORDER BY diterbitkan_pada DESC');
$stmt->execute(['guru_id' => $guru_id]);
$artikel_saya = $stmt->fetchAll();
?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
  <div><h1 class="text-2xl font-extrabold text-slate-900">Artikel Edukasi</h1><p class="mt-1 text-sm text-slate-600">Artikel yang diterbitkan tampil untuk semua pengunjung landing page.</p></div>
  <a href="<?= e(url('guru_bk/artikel_form.php')) ?>" class="rounded-xl bg-karvia-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-karvia-800">Buat Artikel</a>
</div>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <?php if (!$artikel_saya): ?>
    <p class="p-8 text-center text-sm text-slate-500">Belum ada artikel. Buat artikel edukasi pertama Anda.</p>
  <?php else: ?>
    <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-4">Artikel</th><th class="p-4">Diterbitkan</th><th class="p-4">Aksi</th></tr></thead><tbody>
      <?php foreach ($artikel_saya as $artikel): ?><tr class="border-t border-slate-100"><td class="p-4"><div class="flex items-center gap-3"><img src="<?= e(url('uploads/artikel/' . rawurlencode($artikel['gambar']))) ?>" alt="" class="h-14 w-20 rounded-lg object-cover"><span class="font-semibold text-slate-800"><?= e($artikel['judul']) ?></span></div></td><td class="whitespace-nowrap p-4"><?= e(tanggal_id($artikel['diterbitkan_pada'])) ?></td><td class="whitespace-nowrap p-4"><a class="font-semibold text-karvia-700 hover:underline" href="<?= e(url('guru_bk/artikel_form.php?id=' . (int) $artikel['id'])) ?>">Ubah</a><form method="post" class="ml-3 inline" onsubmit="return confirm('Hapus artikel ini? Artikel akan hilang dari landing page.')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $artikel['id'] ?>"><input type="hidden" name="aksi" value="hapus"><button class="font-semibold text-rose-700 hover:underline" type="submit">Hapus</button></form></td></tr><?php endforeach; ?>
    </tbody></table></div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
