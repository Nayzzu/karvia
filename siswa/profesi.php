<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'profesi';
$judul_halaman = 'Jelajah Profesi';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$kategori = (string) ($_GET['kategori'] ?? '');
$kategori_opsi = [
    'kesehatan' => 'Kesehatan',
    'teknologi' => 'Teknologi',
    'pendidikan' => 'Pendidikan',
    'bisnis' => 'Bisnis',
    'lainnya' => 'Lainnya',
];

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
        flash_set('sukses', 'Profesi berhasil ditambahkan ke daftar simpananmu.');
    } else {
        $stmt = $pdo->prepare('DELETE FROM profesi_disimpan WHERE siswa_id = :siswa_id AND profesi_id = :profesi_id');
        $stmt->execute(['siswa_id' => $siswa_id, 'profesi_id' => $id]);
        flash_set('sukses', 'Profesi dihapus dari simpanan.');
    }
    redirect('siswa/profesi.php' . (in_array($kategori, array_keys($kategori_opsi), true) ? '?kategori=' . rawurlencode($kategori) : ''));
}

if (!isset($kategori_opsi[$kategori])) {
    $kategori = '';
}

$sql = "SELECT p.id, p.nama, p.gambar, p.kategori, p.deskripsi, p.kemampuan, p.jalur_pendidikan, (pd.id IS NOT NULL) AS tersimpan
        FROM profesi p LEFT JOIN profesi_disimpan pd ON pd.profesi_id = p.id AND pd.siswa_id = :siswa_id
        WHERE p.status = 'aktif'";
$parameter = ['siswa_id' => $siswa_id];
if ($kategori !== '') {
    $sql .= ' AND p.kategori = :kategori';
    $parameter['kategori'] = $kategori;
}
$sql .= ' ORDER BY p.urutan, p.nama';
$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$profesi = $stmt->fetchAll();
?>

<!-- Header Halaman -->
<div class="flex flex-col gap-1">
  <h2 class="text-xl font-bold tracking-tight text-[#203628]">Eksplorasi Katalog Profesi</h2>
  <p class="text-xs text-slate-500">Kenali tugas harian, keterampilan yang dibutuhkan, dan jalur pendidikan tinggi untuk berbagai karier masa depan.</p>
</div>

<!-- Filter Kategori Attendara Style -->
<form method="get" action="<?= e(url('siswa/profesi.php')) ?>" class="rounded-2xl border border-[#dfe6de] bg-white p-4 shadow-sm sm:p-5">
  <div class="flex flex-wrap items-center gap-2">
    <span class="mr-2 text-xs font-bold text-slate-700">Filter Kategori:</span>
    <a href="<?= e(url('siswa/profesi.php')) ?>" class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition <?= $kategori === '' ? 'bg-[#224f35] text-white' : 'border border-[#dfe6de] bg-[#fcfdfc] text-slate-700 hover:bg-[#f2f6f2]' ?>">
      Semua Kategori
    </a>
    <?php foreach ($kategori_opsi as $nilai => $label): ?>
      <a href="<?= e(url('siswa/profesi.php?kategori=' . urlencode($nilai))) ?>" class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition <?= $kategori === $nilai ? 'bg-[#224f35] text-white' : 'border border-[#dfe6de] bg-[#fcfdfc] text-slate-700 hover:bg-[#f2f6f2]' ?>">
        <?= e($label) ?>
      </a>
    <?php endforeach; ?>
  </div>
</form>

<!-- Grid Katalog Profesi -->
<?php if (!$profesi): ?>
  <div class="rounded-2xl border border-[#dfe6de] bg-white p-10 text-center text-sm text-slate-500 shadow-sm">
    Belum ada profesi pada kategori ini.
  </div>
<?php else: ?>
  <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-2">
    <?php foreach ($profesi as $item): ?>
      <article class="flex flex-col justify-between overflow-hidden rounded-2xl border border-[#dfe6de] bg-white shadow-sm transition duration-200 hover:border-[#ccd9cb] hover:shadow-md">
        <div>
          <!-- Foto Profesi Realistis -->
          <div class="relative h-52 w-full bg-[#f2f5f1] overflow-hidden">
            <?php if (!empty($item['gambar']) && file_exists(__DIR__ . '/../' . $item['gambar'])): ?>
              <img src="<?= e(url($item['gambar'])) ?>" alt="Profesi <?= e($item['nama']) ?>" class="h-full w-full object-cover transition-transform duration-500 hover:scale-105" loading="lazy">
            <?php else: ?>
              <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#e8f0e9] to-[#d4e4d6] text-[#245938]">
                <?php karvia_icon('compass', 'h-12 w-12 opacity-60'); ?>
              </div>
            <?php endif; ?>

            <!-- Badge Kategori -->
            <span class="absolute top-3.5 left-3.5 inline-flex items-center rounded-lg bg-black/60 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-white backdrop-blur-md">
              <?= e($kategori_opsi[$item['kategori']] ?? $item['kategori']) ?>
            </span>

            <?php if ($item['tersimpan']): ?>
              <span class="absolute top-3.5 right-3.5 inline-flex items-center gap-1 rounded-lg bg-amber-500/90 px-2.5 py-1 text-[11px] font-bold text-white shadow-sm backdrop-blur-md">
                <?php karvia_icon('check', 'h-3.5 w-3.5'); ?>
                Tersimpan
              </span>
            <?php endif; ?>
          </div>

          <!-- Deskripsi & Info Profesi -->
          <div class="p-5 sm:p-6">
            <h3 class="text-lg font-bold text-[#203628]"><?= e($item['nama']) ?></h3>
            <p class="mt-2 text-xs leading-relaxed text-slate-600">
              <?= e($item['deskripsi'] ?: 'Deskripsi tugas profesi belum ditambahkan.') ?>
            </p>

            <div class="mt-4 space-y-2.5 border-t border-[#eef2ed] pt-4 text-xs">
              <div class="flex items-start gap-2">
                <span class="font-bold text-slate-700 shrink-0">Kemampuan Utama:</span>
                <span class="text-slate-600"><?= e($item['kemampuan'] ?: '—') ?></span>
              </div>
              <div class="flex items-start gap-2">
                <span class="font-bold text-slate-700 shrink-0">Jalur Pendidikan:</span>
                <span class="text-slate-600"><?= e($item['jalur_pendidikan'] ?: '—') ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tombol Aksi di Bawah Kartu -->
        <div class="flex items-center justify-between gap-3 border-t border-[#eef2ed] bg-[#fbfdfb] px-5 py-3.5 sm:px-6">
          <a href="<?= e(url('siswa/bandingkan.php?profesi=' . (int) $item['id'])) ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-[#245938] hover:underline">
            <?php karvia_icon('chart', 'h-3.5 w-3.5'); ?>
            Bandingkan Pilihan
          </a>

          <form method="post" action="<?= e(url('siswa/profesi.php' . ($kategori ? '?kategori=' . rawurlencode($kategori) : ''))) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="profesi_id" value="<?= (int) $item['id'] ?>">
            <input type="hidden" name="aksi" value="<?= $item['tersimpan'] ? 'hapus' : 'simpan' ?>">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-bold transition <?= $item['tersimpan'] ? 'border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100' : 'border border-[#cfe0d3] bg-[#edf4ee] text-[#245938] hover:bg-[#dfeada]' ?>">
              <?php if ($item['tersimpan']): ?>
                Hapus dari Simpanan
              <?php else: ?>
                <?php karvia_icon('plus', 'h-3 w-3'); ?>
                Simpan Profesi
              <?php endif; ?>
            </button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
