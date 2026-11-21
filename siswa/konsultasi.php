<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'konsultasi';
$judul_halaman = 'Ruang Konsultasi BK';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$siswa_id = (int) auth_pengguna()['id'];
$stmt = $pdo->prepare('SELECT mapel_favorit, kekuatan, catatan_minat FROM refleksi_diri WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$refleksi = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT nama FROM profesi_disimpan d JOIN profesi p ON p.id = d.profesi_id WHERE d.siswa_id = :id ORDER BY p.nama');
$stmt->execute(['id' => $siswa_id]);
$profesi = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT opsi_a, catatan_a, opsi_b, catatan_b, kriteria FROM pilihan_banding WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$banding = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT tujuan, langkah_1, langkah_2 FROM rencana_karier WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$rencana = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT hal_baru, hambatan, dukungan, dibuat_pada FROM jurnal_refleksi WHERE siswa_id = :id ORDER BY dibuat_pada DESC, id DESC');
$stmt->execute(['id' => $siswa_id]);
$jurnal = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT id, sekolah_id, pesan_siswa, status, dibagikan_pada FROM berbagi_catatan WHERE siswa_id = :id');
$stmt->execute(['id' => $siswa_id]);
$berbagi = $stmt->fetch() ?: null;
$errors = [];
$pesan = (string) ($berbagi['pesan_siswa'] ?? '');
$stmt = $pdo->prepare('SELECT kelas FROM pengguna WHERE id = :id');
$stmt->execute(['id' => $siswa_id]);
$kelas = (string) ($stmt->fetchColumn() ?: '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $pesan = trim((string) ($_POST['pesan_siswa'] ?? ''));
    if (mb_strlen($pesan) > 2000) $errors[] = 'Pesan maksimal 2.000 karakter.';
    if (!$errors) {
        $sekolah_id = (int) auth_sekolah_id();
        $stmt = $pdo->prepare("INSERT INTO berbagi_catatan (siswa_id, sekolah_id, pesan_siswa, status, dibagikan_pada, dibaca_pada, diperbarui_pada) VALUES (:siswa_id, :sekolah_id, :pesan, 'baru', NOW(), NULL, NULL) ON DUPLICATE KEY UPDATE sekolah_id = VALUES(sekolah_id), pesan_siswa = VALUES(pesan_siswa), status = 'baru', dibagikan_pada = NOW(), dibaca_pada = NULL, diperbarui_pada = NOW()");
        $stmt->execute(['siswa_id' => $siswa_id, 'sekolah_id' => $sekolah_id, 'pesan' => $pesan ?: null]);
        log_aktivitas('bagikan_catatan', 'Siswa membagikan ringkasan karier ke Guru BK.');
        flash_set('sukses', 'Ringkasan berhasil dibagikan kepada Guru BK di sekolahmu.');
        redirect('siswa/konsultasi.php');
    }
}

function siswa_nilai($value): string
{
    if ($value === null || $value === '') return 'Belum diisi';
    if (is_array($value)) return $value ? implode(', ', array_map('strval', $value)) : 'Belum diisi';
    return (string) $value;
}

function siswa_json($value): string
{
    if (!$value) return 'Belum diisi';
    $decoded = json_decode((string) $value, true);
    return is_array($decoded) ? siswa_nilai($decoded) : 'Belum diisi';
}
?>
<style>
  @media print {
    body { background: #fff !important; color: #111827 !important; }
    #karvia-sidebar, #karvia-backdrop, body > div > div > header, [class~="print:hidden"] { display: none !important; }
    body > div, body > div > div { min-height: 0 !important; padding: 0 !important; }
    main { padding: 0 !important; }
    [class~="print:block"] { display: block !important; }
    [class~="print:space-y-3"] > :not([hidden]) ~ :not([hidden]) { margin-top: .75rem !important; }
    article, section { break-inside: avoid; box-shadow: none !important; }
    a { color: inherit !important; text-decoration: none !important; }
  }
</style>
<section class="mb-5 hidden border-b border-slate-300 pb-4 print:block">
  <p class="text-xs font-bold uppercase tracking-wider text-slate-600">KARVIA · Persiapan obrolan dengan Guru BK</p>
  <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Ringkasan Konsultasi</h1>
</section>
<section class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 via-karvia-800 to-emerald-700 p-6 text-white shadow-lg sm:p-8 print:hidden">
  <div class="absolute -right-8 -top-10 h-44 w-44 rounded-full border-[24px] border-white/5" aria-hidden="true"></div>
  <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div class="max-w-2xl">
      <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Persiapan obrolan dengan Guru BK</p>
      <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Ruang Konsultasi</h2>
      <p class="mt-2 text-sm leading-relaxed text-emerald-50">Satukan hal-hal yang sudah kamu jelajahi. Tinjau dulu, lalu bagikan hanya saat kamu siap untuk membahasnya.</p>
    </div>
    <button type="button" onclick="window.print()" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-karvia-800"><?php karvia_icon('file', 'h-4 w-4'); ?> Cetak ringkasan</button>
  </div>
</section>

<?php if ($errors): ?>
  <div role="alert" class="mb-4 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div>
<?php endif; ?>

<div class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_19rem]">
  <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="identitas-ringkasan">
    <div class="flex items-start gap-3">
      <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700" aria-hidden="true"><?php karvia_icon('users', 'h-5 w-5'); ?></span>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Ringkasan pribadimu</p>
        <h2 id="identitas-ringkasan" class="mt-1 text-lg font-extrabold text-slate-900"><?= e(auth_pengguna()['nama_lengkap'] ?? '') ?></h2>
      </div>
    </div>
    <dl class="mt-5 grid gap-3 border-t border-slate-100 pt-4 text-sm sm:grid-cols-2">
      <div><dt class="text-xs font-semibold text-slate-500">Kelas</dt><dd class="mt-0.5 font-medium text-slate-900"><?= e($kelas !== '' ? $kelas : 'Belum diisi') ?></dd></div>
      <div><dt class="text-xs font-semibold text-slate-500">Sekolah</dt><dd class="mt-0.5 font-medium text-slate-900"><?= e(auth_pengguna()['sekolah_nama'] ?? '') ?></dd></div>
    </dl>
  </section>

  <section class="rounded-2xl border p-5 shadow-sm <?= $berbagi ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white' ?>" aria-labelledby="status-berbagi">
    <div class="flex items-start gap-3">
      <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?= $berbagi ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>" aria-hidden="true"><?php karvia_icon($berbagi ? 'check' : 'lock', 'h-5 w-5'); ?></span>
      <div class="min-w-0">
        <h2 id="status-berbagi" class="text-sm font-extrabold text-slate-900">Status berbagi</h2>
        <?php if ($berbagi): ?>
          <?php $label_status = ['baru' => 'Menunggu dibaca Guru BK', 'dibaca' => 'Sudah dibaca Guru BK', 'ditindaklanjuti' => 'Ada tindak lanjut dari Guru BK']; ?>
          <p class="mt-1 text-sm font-semibold text-emerald-900"><?= e($label_status[$berbagi['status']] ?? 'Catatan sudah dibagikan') ?></p>
          <p class="mt-1 text-xs leading-relaxed text-slate-600"><?= e(tanggal_id($berbagi['dibagikan_pada'], true)) ?></p>
        <?php else: ?>
          <p class="mt-1 text-sm font-semibold text-slate-800">Belum dibagikan</p>
          <p class="mt-1 text-xs leading-relaxed text-slate-600">Catatanmu tetap pribadi sampai kamu memilih membagikannya.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<div class="mb-4 flex flex-wrap items-end justify-between gap-2">
  <div>
    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Bahan obrolan</p>
    <h2 class="mt-1 text-xl font-extrabold text-slate-900">Periksa ringkasanmu</h2>
  </div>
  <p class="text-xs text-slate-500">Data terbaru dari halaman eksplorasimu</p>
</div>

<div class="grid gap-4 print:block print:space-y-3">
  <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-2"><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800" aria-hidden="true"><?php karvia_icon('users', 'h-4 w-4'); ?></span><h3 class="font-extrabold text-slate-900">Kenali Diriku</h3></div><a href="<?= e(url('siswa/kenali_diri.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline print:hidden">Perbarui</a></div>
    <dl class="mt-4 grid gap-3 text-sm leading-relaxed text-slate-700 sm:grid-cols-3">
      <div><dt class="text-xs font-semibold text-slate-500">Mata pelajaran favorit</dt><dd class="mt-1 whitespace-pre-wrap"><?= e(siswa_json($refleksi['mapel_favorit'] ?? null)) ?></dd></div>
      <div><dt class="text-xs font-semibold text-slate-500">Kekuatan</dt><dd class="mt-1 whitespace-pre-wrap"><?= e(siswa_json($refleksi['kekuatan'] ?? null)) ?></dd></div>
      <div><dt class="text-xs font-semibold text-slate-500">Catatan minat</dt><dd class="mt-1 whitespace-pre-wrap"><?= e(siswa_nilai($refleksi['catatan_minat'] ?? null)) ?></dd></div>
    </dl>
  </section>

  <div class="grid gap-4 md:grid-cols-2">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-2"><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800" aria-hidden="true"><?php karvia_icon('compass', 'h-4 w-4'); ?></span><h3 class="font-extrabold text-slate-900">Profesi yang disimpan</h3></div><a href="<?= e(url('siswa/profesi.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline print:hidden">Jelajahi</a></div>
      <?php if (!$profesi): ?>
        <p class="mt-3 text-sm text-slate-500">Belum ada profesi disimpan.</p>
      <?php else: ?>
        <ul class="mt-3 flex flex-wrap gap-2" aria-label="Daftar profesi yang disimpan">
          <?php foreach ($profesi as $p): ?><li class="rounded-full bg-emerald-50 px-3 py-1.5 text-sm text-emerald-900"><?= e($p['nama']) ?></li><?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-2"><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800" aria-hidden="true"><?php karvia_icon('scale', 'h-4 w-4'); ?></span><h3 class="font-extrabold text-slate-900">Bandingkan pilihan</h3></div><a href="<?= e(url('siswa/bandingkan.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline print:hidden">Perbarui</a></div>
      <?php if (!$banding): ?>
        <p class="mt-3 text-sm text-slate-500">Belum diisi.</p>
      <?php else: ?>
        <div class="mt-3 space-y-2">
          <?php foreach ([['A', 'opsi_a', 'catatan_a'], ['B', 'opsi_b', 'catatan_b']] as [$label, $opsi, $catatan]): ?>
            <div class="rounded-xl border border-slate-200 p-3">
              <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Pilihan <?= e($label) ?></p>
              <p class="mt-1 text-sm font-semibold text-slate-900"><?= e(siswa_nilai($banding[$opsi] ?? '')) ?></p>
              <?php if (trim((string) ($banding[$catatan] ?? '')) !== ''): ?><p class="mt-1 whitespace-pre-wrap text-xs leading-relaxed text-slate-600"><?= e($banding[$catatan]) ?></p><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="mt-3 text-xs leading-relaxed text-slate-600"><strong class="text-slate-800">Hal yang dipertimbangkan:</strong> <?= e(siswa_json($banding['kriteria'])) ?></p>
      <?php endif; ?>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-2"><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800" aria-hidden="true"><?php karvia_icon('calendar', 'h-4 w-4'); ?></span><h3 class="font-extrabold text-slate-900">Rencana langkahku</h3></div><a href="<?= e(url('siswa/rencana.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline print:hidden">Perbarui</a></div>
      <?php if (!$rencana): ?>
        <p class="mt-3 text-sm text-slate-500">Belum diisi.</p>
      <?php else: ?>
        <p class="mt-3 rounded-xl bg-slate-50 p-3 text-sm leading-relaxed"><strong class="text-slate-900">Tujuan sementara:</strong> <?= e(siswa_nilai($rencana['tujuan'] ?? '')) ?></p>
        <ol class="mt-3 space-y-2 text-sm leading-relaxed text-slate-700">
          <li class="flex gap-2"><span class="font-bold text-emerald-800">1.</span><span><?= e(siswa_nilai($rencana['langkah_1'] ?? '')) ?></span></li>
          <li class="flex gap-2"><span class="font-bold text-emerald-800">2.</span><span><?= e(siswa_nilai($rencana['langkah_2'] ?? '')) ?></span></li>
        </ol>
      <?php endif; ?>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-2"><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800" aria-hidden="true"><?php karvia_icon('book', 'h-4 w-4'); ?></span><h3 class="font-extrabold text-slate-900">Jurnal refleksi</h3></div><a href="<?= e(url('siswa/jurnal.php')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline print:hidden">Tambah entri</a></div>
      <?php if (!$jurnal): ?>
        <p class="mt-3 text-sm text-slate-500">Belum ada jurnal.</p>
      <?php else: ?>
        <p class="mt-2 text-xs text-slate-500"><?= count($jurnal) ?> entri · terbaru ditampilkan lebih dulu</p>
        <?php foreach ($jurnal as $j): ?>
          <article class="mt-3 border-t border-slate-100 pt-3">
            <p class="text-[11px] font-semibold text-slate-500"><?= e(tanggal_id($j['dibuat_pada'], true)) ?></p>
            <?php foreach (['hal_baru' => 'Hal baru', 'hambatan' => 'Hambatan', 'dukungan' => 'Dukungan'] as $kunci => $label): ?>
              <?php if ($j[$kunci]): ?><p class="mt-1 whitespace-pre-wrap text-xs leading-relaxed text-slate-700"><strong class="text-slate-900"><?= e($label) ?>:</strong> <?= e($j[$kunci]) ?></p><?php endif; ?>
            <?php endforeach; ?>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
</div>

<section class="mt-6 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm print:hidden" aria-labelledby="bagikan-catatan">
  <div class="border-b border-emerald-100 bg-emerald-50 px-5 py-4 sm:px-6">
    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Kamu yang menentukan</p>
    <h2 id="bagikan-catatan" class="mt-1 text-lg font-extrabold text-emerald-950">Bagikan ringkasan ke Guru BK</h2>
    <p class="mt-1 text-sm leading-relaxed text-emerald-900">Belum siap? Tidak apa-apa. Ringkasan tetap pribadi sampai kamu membagikannya.</p>
  </div>
  <div class="p-5 sm:p-6">
    <div class="grid gap-4 md:grid-cols-2">
      <div>
        <h3 class="text-sm font-bold text-slate-900">Apa yang dapat dilihat Guru BK?</h3>
        <p class="mt-1 text-sm leading-relaxed text-slate-600">Setelah dibagikan, Guru BK di sekolahmu dapat melihat ringkasan eksplorasi, seluruh entri jurnal yang tampil di atas, dan pesanmu. Data yang dibuka mengikuti isi terbaru.</p>
        <p class="mt-2 text-sm leading-relaxed text-amber-900"><strong>Sebelum berbagi:</strong> periksa jurnal lama juga. Jurnal ikut dibagikan seluruhnya dan tidak dapat disembunyikan satu per satu di halaman ini.</p>
      </div>
      <form method="post" class="space-y-3">
        <?= csrf_field() ?>
        <label for="pesan_siswa" class="block text-sm font-semibold text-slate-800">Pesan untuk Guru BK <span class="font-normal text-slate-500">(opsional)</span></label>
        <p id="petunjuk-pesan" class="text-xs leading-relaxed text-slate-500">Tulis hal yang ingin kamu bicarakan lebih dulu, misalnya pertanyaan tentang jurusan atau informasi yang masih dibutuhkan.</p>
        <textarea id="pesan_siswa" name="pesan_siswa" maxlength="2000" rows="4" aria-describedby="petunjuk-pesan" class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm leading-relaxed focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20" placeholder="Apa yang ingin kamu bicarakan?"><?= e($pesan) ?></textarea>
        <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2" onclick="return confirm('Bagikan ringkasan dan seluruh jurnal yang tampil kepada Guru BK di sekolahmu?')"><?= $berbagi ? 'Bagikan ulang ringkasan' : 'Bagikan catatan ke Guru BK' ?> <span aria-hidden="true">→</span></button>
      </form>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
