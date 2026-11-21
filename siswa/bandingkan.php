<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'bandingkan';
$judul_halaman = 'Bandingkan Pilihan';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$stmt = $pdo->prepare('SELECT opsi_a, catatan_a, opsi_b, catatan_b, kriteria FROM pilihan_banding WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$data = $stmt->fetch();
$sudah_tersimpan = $data !== false;
$data = $data ?: ['opsi_a' => '', 'catatan_a' => '', 'opsi_b' => '', 'catatan_b' => '', 'kriteria' => '[]'];
$profesi_id = filter_input(INPUT_GET, 'profesi', FILTER_VALIDATE_INT) ?: 0;
if ($profesi_id > 0 && !$data['opsi_a']) {
    $q = $pdo->prepare("SELECT nama FROM profesi WHERE id = :id AND status = 'aktif'");
    $q->execute(['id' => $profesi_id]);
    $data['opsi_a'] = (string) ($q->fetchColumn() ?: '');
}
$criteria = ['Kesesuaian minat', 'Peluang belajar', 'Lama pendidikan', 'Dampak bagi orang lain'];
$chosen = json_decode($data['kriteria'] ?: '[]', true) ?: [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $data = [
        'opsi_a' => trim((string) ($_POST['opsi_a'] ?? '')),
        'catatan_a' => trim((string) ($_POST['catatan_a'] ?? '')),
        'opsi_b' => trim((string) ($_POST['opsi_b'] ?? '')),
        'catatan_b' => trim((string) ($_POST['catatan_b'] ?? '')),
    ];
    $selected = $_POST['kriteria'] ?? [];
    if (!is_array($selected)) $selected = [];
    $selected = array_filter($selected, 'is_string');
    $chosen = array_values(array_intersect($criteria, $selected));
    foreach (['opsi_a' => 150, 'catatan_a' => 5000, 'opsi_b' => 150, 'catatan_b' => 5000] as $key => $max) {
        if (mb_strlen($data[$key]) > $max) $errors[] = 'Ada isian yang terlalu panjang.';
    }
    if ($data['opsi_a'] === '' || $data['opsi_b'] === '') $errors[] = 'Kedua pilihan wajib diisi.';
    if ($data['opsi_a'] !== '' && $data['opsi_a'] === $data['opsi_b']) $errors[] = 'Pilih dua pilihan yang berbeda.';
    if (!$errors) {
        $q = $pdo->prepare('INSERT INTO pilihan_banding (siswa_id, opsi_a, catatan_a, opsi_b, catatan_b, kriteria, dibuat_pada) VALUES (:id, :a, :ca, :b, :cb, :k, NOW()) ON DUPLICATE KEY UPDATE opsi_a = VALUES(opsi_a), catatan_a = VALUES(catatan_a), opsi_b = VALUES(opsi_b), catatan_b = VALUES(catatan_b), kriteria = VALUES(kriteria), diperbarui_pada = NOW()');
        $q->execute(['id' => $siswa_id, 'a' => $data['opsi_a'], 'ca' => $data['catatan_a'] ?: null, 'b' => $data['opsi_b'], 'cb' => $data['catatan_b'] ?: null, 'k' => json_encode($chosen, JSON_UNESCAPED_UNICODE)]);
        flash_set('sukses', 'Perbandingan berhasil disimpan. Tidak ada pilihan yang otomatis ditetapkan.');
        redirect('siswa/bandingkan.php');
    }
}
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 to-emerald-700 p-6 text-white sm:p-8">
  <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Langkah 3 dari 5 · Pertimbangkan pilihan</p>
  <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Bandingkan tanpa buru-buru memilih.</h2>
  <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">Tuliskan apa yang kamu tahu dan ingin cari tahu dari dua pilihan. Ini alat bantu diskusi, bukan penilaian atau keputusan otomatis.</p>
</section>

<?php if ($sudah_tersimpan): ?>
  <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:flex-row sm:items-center sm:justify-between" role="status">
    <p class="text-sm font-medium text-emerald-900">Perbandinganmu tersimpan dan bisa diperbarui kapan saja.</p>
    <a href="<?= e(url('siswa/rencana.php')) ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-800 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-900">Lanjut ke Rencana <span class="ml-2" aria-hidden="true">→</span></a>
  </div>
<?php endif; ?>

<?php if ($errors): ?>
  <div role="alert" class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div>
<?php endif; ?>

<form method="post" class="max-w-5xl space-y-5">
  <?= csrf_field() ?>
  <div class="grid gap-4 md:grid-cols-2">
    <?php foreach (['a' => ['Pilihan A', 'text'], 'b' => ['Pilihan B', 'lightbulb']] as $key => [$label, $icon]): ?>
      <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <legend class="flex items-center gap-3 px-1 text-lg font-extrabold text-slate-900"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700" aria-hidden="true"><?php karvia_icon($icon, 'h-5 w-5'); ?></span><?= e($label) ?></legend>
        <p class="mb-4 mt-1 text-sm text-slate-500"><?= $key === 'a' ? 'Pilihan yang pertama kamu pertimbangkan.' : 'Pilihan lain yang ingin kamu bandingkan.' ?></p>
        <label for="opsi_<?= $key ?>" class="mb-1 block text-sm font-semibold text-slate-700">Nama jurusan, profesi, atau jalur *</label>
        <input id="opsi_<?= $key ?>" required maxlength="150" name="opsi_<?= $key ?>" value="<?= e($data['opsi_' . $key]) ?>" placeholder="Contoh: Keperawatan atau Desain Komunikasi Visual" class="w-full rounded-xl border border-slate-300 px-3.5 py-3 text-sm focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20">
        <label for="catatan_<?= $key ?>" class="mb-1 mt-4 block text-sm font-semibold text-slate-700">Apa yang kamu ketahui atau ingin cari tahu?</label>
        <textarea id="catatan_<?= $key ?>" maxlength="5000" name="catatan_<?= $key ?>" rows="5" placeholder="Misalnya mata pelajaran yang dipakai, kegiatan kuliah, tantangan, atau pertanyaan untuk Guru BK." class="w-full rounded-xl border border-slate-300 px-3.5 py-3 text-sm leading-relaxed focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"><?= e($data['catatan_' . $key]) ?></textarea>
      </fieldset>
    <?php endforeach; ?>
  </div>

  <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <legend class="px-1 text-base font-extrabold text-slate-900">Hal apa yang penting untuk dipertimbangkan?</legend>
    <p class="mb-4 mt-1 text-sm text-slate-500">Pilih beberapa hal yang ingin kamu gunakan saat mencari informasi dan berdiskusi.</p>
    <div class="grid gap-2 sm:grid-cols-2">
      <?php foreach ($criteria as $c): ?>
        <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:font-semibold has-[:checked]:text-emerald-900">
          <input type="checkbox" name="kriteria[]" value="<?= e($c) ?>" <?= in_array($c, $chosen, true) ? 'checked' : '' ?> class="h-4 w-4 accent-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
          <?= e($c) ?>
        </label>
      <?php endforeach; ?>
    </div>
  </fieldset>

  <div class="rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">
    Belum perlu memutuskan sekarang. Kamu bisa mencari informasi dari sumber resmi, berbicara dengan keluarga atau Guru BK, lalu memperbarui perbandingan ini.
  </div>

  <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
    <a href="<?= e(url('siswa/profesi.php')) ?>" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Jelajah Profesi</a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">Simpan perbandingan <span aria-hidden="true">→</span></button>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
