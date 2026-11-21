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
$data = $stmt->fetch();
$sudah_tersimpan = $data !== false;
$data = $data ?: ['mapel_favorit' => '[]', 'kekuatan' => '[]', 'catatan_minat' => ''];
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
    $mapel_masuk = array_filter($mapel_masuk, 'is_string');
    $kekuatan_masuk = array_filter($kekuatan_masuk, 'is_string');
    $mapel = array_values(array_unique(array_intersect($mapel_opsi, $mapel_masuk)));
    $kekuatan = array_values(array_unique(array_intersect($kekuatan_opsi, $kekuatan_masuk)));
    $minat = trim((string) ($_POST['catatan_minat'] ?? ''));
    if (mb_strlen($minat) > 5000) $errors[] = 'Catatan minat maksimal 5.000 karakter.';
    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO refleksi_diri (siswa_id, mapel_favorit, kekuatan, catatan_minat, dibuat_pada)
             VALUES (:siswa_id, :mapel, :kekuatan, :minat, NOW())
             ON DUPLICATE KEY UPDATE mapel_favorit = VALUES(mapel_favorit), kekuatan = VALUES(kekuatan), catatan_minat = VALUES(catatan_minat), diperbarui_pada = NOW()'
        );
        $stmt->execute(['siswa_id' => $siswa_id, 'mapel' => json_encode($mapel, JSON_UNESCAPED_UNICODE), 'kekuatan' => json_encode($kekuatan, JSON_UNESCAPED_UNICODE), 'minat' => $minat ?: null]);
        flash_set('sukses', 'Refleksi berhasil disimpan. Kamu bisa mengubahnya kapan saja.');
        redirect('siswa/kenali_diri.php');
    }
    $data['catatan_minat'] = $minat;
}
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 to-emerald-700 p-6 text-white sm:p-8">
  <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Langkah 1 dari 5 · Kenali dirimu</p>
  <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Mulai dari hal yang kamu suka.</h2>
  <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">Tidak ada jawaban benar atau salah. Pilih hal yang terasa dekat dengan dirimu sekarang; kamu boleh mengubahnya nanti.</p>
</section>

<?php if ($sudah_tersimpan): ?>
  <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:flex-row sm:items-center sm:justify-between" role="status">
    <p class="text-sm font-medium text-emerald-900">Refleksimu sudah tersimpan. Lanjutkan kapan kamu siap.</p>
    <a href="<?= e(url('siswa/profesi.php')) ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-800 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-900">Lanjut ke Jelajah Profesi <span class="ml-2" aria-hidden="true">→</span></a>
  </div>
<?php endif; ?>

<section class="mb-5 rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm sm:p-5" aria-label="Privasi refleksi">
  <p class="text-sm leading-relaxed text-slate-700"><strong class="text-karvia-800">Catatan ini milikmu.</strong> Jawabanmu pribadi dan belum terlihat oleh Guru BK sampai kamu memilih membagikan ringkasan konsultasi.</p>
</section>

<?php if ($errors): ?>
  <div role="alert" class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div>
<?php endif; ?>

<form method="post" class="max-w-4xl space-y-5">
  <?= csrf_field() ?>
  <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <legend class="px-1 text-base font-extrabold text-slate-900">Mata pelajaran yang kamu sukai</legend>
    <p class="mb-4 mt-1 text-sm text-slate-500">Pilih semua yang kamu nikmati atau ingin pelajari lebih jauh.</p>
    <div class="grid gap-2 sm:grid-cols-2">
      <?php foreach ($mapel_opsi as $opsi): ?>
        <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:font-semibold has-[:checked]:text-emerald-900">
          <input type="checkbox" name="mapel_favorit[]" value="<?= e($opsi) ?>" <?= in_array($opsi, $mapel, true) ? 'checked' : '' ?> class="h-4 w-4 shrink-0 accent-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
          <?= e($opsi) ?>
        </label>
      <?php endforeach; ?>
    </div>
  </fieldset>

  <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <legend class="px-1 text-base font-extrabold text-slate-900">Kekuatan yang kamu rasakan</legend>
    <p class="mb-4 mt-1 text-sm text-slate-500">Pilih hal yang pernah kamu lakukan dengan baik, meski masih ingin mengembangkannya.</p>
    <div class="grid gap-2 sm:grid-cols-2">
      <?php foreach ($kekuatan_opsi as $opsi): ?>
        <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:font-semibold has-[:checked]:text-emerald-900">
          <input type="checkbox" name="kekuatan[]" value="<?= e($opsi) ?>" <?= in_array($opsi, $kekuatan, true) ? 'checked' : '' ?> class="h-4 w-4 shrink-0 accent-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
          <?= e($opsi) ?>
        </label>
      <?php endforeach; ?>
    </div>
  </fieldset>

  <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <label for="catatan_minat" class="block text-base font-extrabold text-slate-900">Ada hal yang ingin kamu jelajahi?</label>
    <p class="mb-3 mt-1 text-sm leading-relaxed text-slate-500">Misalnya kegiatan yang membuatmu bersemangat, masalah yang ingin kamu bantu pecahkan, atau pekerjaan yang membuatmu penasaran.</p>
    <textarea id="catatan_minat" name="catatan_minat" maxlength="5000" rows="5" placeholder="Tulis dengan kata-katamu sendiri. Tidak harus panjang." class="w-full rounded-xl border border-slate-300 p-3 text-sm leading-relaxed text-slate-800 placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"><?= e($data['catatan_minat']) ?></textarea>
    <p class="mt-2 text-xs text-slate-500">Opsional · Maksimal 5.000 karakter.</p>
  </fieldset>

  <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
    <a href="<?= e(url('siswa/index.php')) ?>" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke beranda</a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">Simpan refleksi <span aria-hidden="true">→</span></button>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
