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
<div class="space-y-6">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <h2 class="text-xl font-bold tracking-tight text-[#203628]">Artikel Edukasi Karier</h2>
      <p class="mt-0.5 text-xs text-slate-500">Artikel yang Anda tulis akan terbit di landing page dan dapat dibaca oleh siswa maupun publik.</p>
    </div>
    <a href="<?= e(url('guru_bk/artikel_form.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-[#224f35] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1a4029]">
      <?php karvia_icon('plus', 'h-4 w-4'); ?>
      Tulis Artikel Baru
    </a>
  </div>

  <section class="overflow-hidden rounded-2xl border border-[#dfe6de] bg-white shadow-sm">
    <?php if (!$artikel_saya): ?>
      <div class="p-12 text-center">
        <span class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#e8f0e9] text-[#245938]">
          <?php karvia_icon('book', 'h-6 w-6'); ?>
        </span>
        <h3 class="text-sm font-bold text-[#203628]">Belum Ada Artikel</h3>
        <p class="mt-1 text-xs text-slate-500">Bagikan wawasan dan tips persiapan karier pertama Anda untuk siswa.</p>
        <a href="<?= e(url('guru_bk/artikel_form.php')) ?>" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#224f35] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#1a4029]">
          <?php karvia_icon('plus', 'h-3.5 w-3.5'); ?> Buat Sekarang
        </a>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-[#eef2ed] bg-[#fbfcf8] font-bold uppercase tracking-wider text-slate-500">
            <tr>
              <th class="p-4">Artikel</th>
              <th class="p-4">Diterbitkan</th>
              <th class="p-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#eef2ed]">
            <?php foreach ($artikel_saya as $artikel): ?>
              <tr class="transition hover:bg-[#fbfdfb]">
                <td class="p-4">
                  <div class="flex items-center gap-3.5">
                    <img src="<?= e(url('uploads/artikel/' . rawurlencode($artikel['gambar']))) ?>" alt="" class="h-12 w-16 shrink-0 rounded-xl object-cover border border-[#dfe6de]">
                    <div>
                      <span class="font-bold text-[#203628] leading-snug line-clamp-2"><?= e($artikel['judul']) ?></span>
                      <?php if (!empty($artikel['diperbarui_pada'])): ?>
                        <span class="text-[10px] text-slate-400">Diperbarui: <?= e(tanggal_id($artikel['diperbarui_pada'])) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td class="whitespace-nowrap p-4 text-slate-500">
                  <?= e(tanggal_id($artikel['diterbitkan_pada'])) ?>
                </td>
                <td class="whitespace-nowrap p-4 text-right">
                  <a class="inline-flex items-center font-bold text-[#245938] hover:underline" href="<?= e(url('guru_bk/artikel_form.php?id=' . (int) $artikel['id'])) ?>">
                    Ubah
                  </a>
                  <form method="post" class="ml-3.5 inline" onsubmit="return confirm('Hapus artikel ini? Artikel akan hilang dari landing page.')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $artikel['id'] ?>">
                    <input type="hidden" name="aksi" value="hapus">
                    <button class="font-bold text-rose-600 hover:underline" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
