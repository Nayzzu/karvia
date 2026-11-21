<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'rencana';
$judul_halaman = 'Rencana Langkahku';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$stmt = $pdo->prepare('SELECT tujuan, langkah_1, langkah_2 FROM rencana_karier WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$data = $stmt->fetch();
$sudah_tersimpan = $data !== false;
$data = $data ?: ['tujuan' => '', 'langkah_1' => '', 'langkah_2' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $data = [
        'tujuan' => trim((string) ($_POST['tujuan'] ?? '')),
        'langkah_1' => trim((string) ($_POST['langkah_1'] ?? '')),
        'langkah_2' => trim((string) ($_POST['langkah_2'] ?? '')),
    ];
    foreach ($data as $isi) {
        if ($isi === '') {
            $errors[] = 'Tujuan dan kedua langkah wajib diisi.';
            break;
        }
    }
    foreach (['tujuan' => 255, 'langkah_1' => 255, 'langkah_2' => 255] as $kunci => $maksimal) {
        if (mb_strlen($data[$kunci]) > $maksimal) $errors[] = 'Setiap isian maksimal 255 karakter.';
    }
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO rencana_karier (siswa_id, tujuan, langkah_1, langkah_2, dibuat_pada) VALUES (:id, :tujuan, :l1, :l2, NOW()) ON DUPLICATE KEY UPDATE tujuan = VALUES(tujuan), langkah_1 = VALUES(langkah_1), langkah_2 = VALUES(langkah_2), diperbarui_pada = NOW()');
        $stmt->execute(['id' => $siswa_id, 'tujuan' => $data['tujuan'], 'l1' => $data['langkah_1'], 'l2' => $data['langkah_2']]);
        flash_set('sukses', 'Rencana langkahmu berhasil disimpan. Kamu dapat memperbaruinya kapan saja.');
        redirect('siswa/rencana.php');
    }
}
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 to-emerald-700 p-6 text-white sm:p-8">
  <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Langkah 4 dari 5 · Susun langkah kecil</p>
  <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Rencana yang bisa dimulai, bukan harus sempurna.</h2>
  <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">Pilih satu arah yang ingin kamu jelajahi, lalu pecah menjadi dua tindakan yang realistis. Rencana boleh berubah saat kamu mendapat informasi baru.</p>
</section>

<?php if ($sudah_tersimpan): ?>
  <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:flex-row sm:items-center sm:justify-between" role="status">
    <p class="text-sm font-medium text-emerald-900">Rencanamu tersimpan. Kamu bisa meninjau dan memperbaruinya kapan saja.</p>
    <a href="<?= e(url('siswa/jurnal.php')) ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-800 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-900">Lanjut ke Jurnal Refleksi <span class="ml-2" aria-hidden="true">→</span></a>
  </div>
<?php endif; ?>

<section class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="panduan-rencana">
  <h3 id="panduan-rencana" class="font-bold text-slate-900">Cara membuat langkah yang terasa mungkin</h3>
  <ol class="mt-3 grid gap-3 text-sm leading-relaxed text-slate-600 sm:grid-cols-3">
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">1</span><span>Pilih hal yang ingin dipelajari atau dicoba, bukan tuntutan untuk langsung menentukan masa depan.</span></li>
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">2</span><span>Buat tindakan yang jelas dan kecil, misalnya mencari satu sumber resmi atau bertanya kepada seseorang.</span></li>
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">3</span><span>Jika terasa terlalu berat, sederhanakan. Rencana ini milikmu dan boleh disesuaikan.</span></li>
  </ol>
</section>

<?php if ($errors): ?>
  <div role="alert" class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div>
<?php endif; ?>

<form method="post" class="max-w-4xl space-y-5">
  <?= csrf_field() ?>
  <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <legend class="px-1 text-base font-extrabold text-slate-900">Arah yang ingin aku jelajahi</legend>
    <p class="mb-3 mt-1 text-sm leading-relaxed text-slate-500">Boleh berupa jurusan, bidang, keterampilan, atau pertanyaan yang masih ingin kamu cari tahu.</p>
    <label for="tujuan" class="mb-1 block text-sm font-semibold text-slate-700">Tujuan sementara *</label>
    <textarea id="tujuan" name="tujuan" required maxlength="255" rows="2" placeholder="Contoh: mencari tahu apakah bidang desain digital cocok untukku" class="w-full rounded-xl border border-slate-300 p-3 text-sm leading-relaxed focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"><?= e($data['tujuan']) ?></textarea>
  </fieldset>

  <div class="grid gap-4 md:grid-cols-2">
    <?php foreach ([1 => ['langkah_1', 'Langkah kecil pertama', 'Apa satu hal sederhana yang bisa kamu lakukan untuk mulai?', 'Contoh: cari kurikulum jurusan dari situs resmi kampus.'], 2 => ['langkah_2', 'Langkah berikutnya', 'Setelah itu, siapa atau sumber apa yang bisa membantumu?', 'Contoh: tanyakan pengalaman belajar kepada Guru BK atau mahasiswa.']] as $nomor => [$kunci, $judul, $pertanyaan, $contoh]): ?>
      <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <legend class="flex items-center gap-3 px-1 text-base font-extrabold text-slate-900"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-sm text-emerald-900" aria-hidden="true"><?= $nomor ?></span><?= e($judul) ?></legend>
        <p class="mb-3 mt-2 text-sm leading-relaxed text-slate-500"><?= e($pertanyaan) ?></p>
        <label for="<?= e($kunci) ?>" class="sr-only"><?= e($judul) ?></label>
        <textarea id="<?= e($kunci) ?>" name="<?= e($kunci) ?>" required maxlength="255" rows="4" placeholder="<?= e($contoh) ?>" class="w-full rounded-xl border border-slate-300 p-3 text-sm leading-relaxed focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"><?= e($data[$kunci]) ?></textarea>
      </fieldset>
    <?php endforeach; ?>
  </div>

  <aside class="rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">
    Kamu tidak harus mengerjakan semuanya sendirian. Kalau ada langkah yang terasa sulit, catat hal yang kamu butuhkan dan bicarakan dengan orang dewasa tepercaya atau Guru BK.
  </aside>

  <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
    <a href="<?= e(url('siswa/bandingkan.php')) ?>" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Bandingkan Pilihan</a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">Simpan rencana <span aria-hidden="true">→</span></button>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
