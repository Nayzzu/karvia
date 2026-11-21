<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'jurnal';
$judul_halaman = 'Jurnal Refleksi';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$errors = [];
$data = ['hal_baru' => '', 'hambatan' => '', 'dukungan' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    foreach ($data as $kunci => $_) {
        $data[$kunci] = trim((string) ($_POST[$kunci] ?? ''));
    }
    if (!array_filter($data, static fn($isi) => $isi !== '')) {
        $errors[] = 'Isi minimal satu bagian sebelum menyimpan jurnal.';
    }
    foreach ($data as $isi) {
        if (mb_strlen($isi) > 10000) {
            $errors[] = 'Setiap bagian maksimal 10.000 karakter.';
            break;
        }
    }
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO jurnal_refleksi (siswa_id, hal_baru, hambatan, dukungan, dibuat_pada) VALUES (:id, :baru, :hambatan, :dukungan, NOW())');
        $stmt->execute([
            'id' => $siswa_id,
            'baru' => $data['hal_baru'] ?: null,
            'hambatan' => $data['hambatan'] ?: null,
            'dukungan' => $data['dukungan'] ?: null,
        ]);
        flash_set('sukses', 'Entri jurnal berhasil disimpan. Jurnal ini bersifat pribadi sampai kamu membagikan catatan ke Guru BK.');
        redirect('siswa/jurnal.php');
    }
}

$stmt = $pdo->prepare('SELECT hal_baru, hambatan, dukungan, dibuat_pada FROM jurnal_refleksi WHERE siswa_id = :id ORDER BY dibuat_pada DESC, id DESC');
$stmt->execute(['id' => $siswa_id]);
$jurnal = $stmt->fetchAll();
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 to-emerald-700 p-6 text-white sm:p-8">
  <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Langkah 5 dari 5 · Refleksikan perjalananmu</p>
  <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Catatan kecil untuk memahami dirimu.</h2>
  <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">Tulis hal yang kamu pelajari, tantangan yang kamu hadapi, atau dukungan yang kamu perlukan. Tidak perlu menulis semuanya sekaligus.</p>
</section>

<div class="mb-5 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-relaxed text-sky-950" role="note">
  <strong>Privasi:</strong> Jurnal ini hanya terlihat olehmu sampai kamu memilih membagikan catatan melalui halaman Konsultasi. Admin tidak dapat membaca isi jurnal pribadimu.
</div>

<?php if ($errors): ?>
  <div role="alert" class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div>
<?php endif; ?>

<div class="grid max-w-5xl gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
  <form method="post" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <?= csrf_field() ?>
    <p class="text-sm text-slate-600">Pilih satu atau beberapa pertanyaan. Entri baru akan ditambahkan ke riwayat, tidak menggantikan catatan sebelumnya.</p>
    <?php foreach ([
        'hal_baru' => ['Hal baru yang saya pelajari', 'Apa hal baru tentang jurusan, profesi, atau diriku yang saya temukan?'],
        'hambatan' => ['Hal yang terasa menantang', 'Apa yang masih membingungkan atau terasa sulit?'],
        'dukungan' => ['Dukungan yang saya butuhkan', 'Informasi atau bantuan dari siapa yang bisa membantu langkah berikutnya?'],
    ] as $kunci => [$label, $prompt]): ?>
      <div>
        <label for="<?= e($kunci) ?>" class="mb-1 block text-sm font-bold text-slate-800"><?= e($label) ?></label>
        <p class="mb-2 text-sm text-slate-500"><?= e($prompt) ?></p>
        <textarea id="<?= e($kunci) ?>" name="<?= e($kunci) ?>" maxlength="10000" rows="3" placeholder="Tulis dengan kata-katamu sendiri…" class="w-full rounded-xl border border-slate-300 p-3 text-sm leading-relaxed focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"><?= e($data[$kunci]) ?></textarea>
      </div>
    <?php endforeach; ?>
    <p class="text-xs text-slate-500">Isi minimal satu bagian. Maksimal 10.000 karakter per bagian.</p>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">Simpan entri jurnal <span aria-hidden="true">→</span></button>
  </form>

  <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h3 class="font-bold text-slate-900">Butuh teman berdiskusi?</h3>
    <p class="mt-2 text-sm leading-relaxed text-slate-600">Kamu bisa membawa pertanyaan atau kebutuhan dukungan ini ke Guru BK. Jurnal tidak dibagikan otomatis.</p>
    <a href="<?= e(url('siswa/konsultasi.php')) ?>" class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-emerald-800 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-50">Tinjau halaman Konsultasi</a>
  </aside>
</div>

<section class="mt-8 max-w-5xl" aria-labelledby="riwayat-jurnal">
  <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
    <div>
      <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Catatan pribadimu</p>
      <h2 id="riwayat-jurnal" class="mt-1 text-xl font-extrabold text-slate-900">Riwayat refleksi</h2>
    </div>
    <p class="text-sm text-slate-500"><?= count($jurnal) ?> entri tersimpan</p>
  </div>
  <?php if (!$jurnal): ?>
    <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada entri. Kamu bisa mulai dengan menulis satu hal yang baru dipelajari.</p>
  <?php else: ?>
    <div class="space-y-4">
      <?php foreach ($jurnal as $entry): ?>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <p class="mb-3 text-xs font-semibold text-slate-500"><?= e(tanggal_id($entry['dibuat_pada'], true)) ?></p>
          <?php foreach (['hal_baru' => 'Hal baru', 'hambatan' => 'Hambatan', 'dukungan' => 'Dukungan yang dibutuhkan'] as $kunci => $label): ?>
            <?php if ($entry[$kunci] !== null && $entry[$kunci] !== ''): ?>
              <p class="mb-3 whitespace-pre-wrap text-sm leading-relaxed text-slate-700"><strong class="text-slate-900"><?= e($label) ?>:</strong> <?= e($entry[$kunci]) ?></p>
            <?php endif; ?>
          <?php endforeach; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
