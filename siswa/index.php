<?php

define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'index';
$judul_halaman = 'Beranda Siswa';
require_once __DIR__ . '/../includes/header.php';

$siswa = auth_pengguna();
$siswa_id = (int) $siswa['id'];
$pdo = db();
$status = [
    'refleksi' => false,
    'profesi' => false,
    'bandingkan' => false,
    'rencana' => false,
    'jurnal' => false,
];

foreach ([
    'refleksi' => 'SELECT COUNT(*) FROM refleksi_diri WHERE siswa_id = :id',
    'profesi' => 'SELECT COUNT(*) FROM profesi_disimpan WHERE siswa_id = :id',
    'bandingkan' => 'SELECT COUNT(*) FROM pilihan_banding WHERE siswa_id = :id',
    'rencana' => 'SELECT COUNT(*) FROM rencana_karier WHERE siswa_id = :id',
    'jurnal' => 'SELECT COUNT(*) FROM jurnal_refleksi WHERE siswa_id = :id',
] as $kunci => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $siswa_id]);
    $status[$kunci] = (int) $stmt->fetchColumn() > 0;
}

$langkah = [
    ['refleksi', 'Kenali Diriku', 'kenali_diri.php', 'Mulai dari minat, kekuatan, dan hal yang penting bagimu.', '01'],
    ['profesi', 'Jelajah Profesi', 'profesi.php', 'Cari tahu profesi yang membuatmu penasaran dan simpan pilihanmu.', '02'],
    ['bandingkan', 'Bandingkan Pilihan', 'bandingkan.php', 'Timbang dua pilihan dengan alasan dan kriteria versimu.', '03'],
    ['rencana', 'Rencana Langkahku', 'rencana.php', 'Ubah tujuan menjadi dua langkah kecil yang bisa dicoba.', '04'],
    ['jurnal', 'Jurnal Refleksi', 'jurnal.php', 'Catat hal baru, hambatan, dan dukungan yang kamu butuhkan.', '05'],
];
$jumlah_selesai = count(array_filter($status));
$persen_selesai = (int) round($jumlah_selesai / count($status) * 100);
$langkah_berikutnya = null;
foreach ($langkah as $item) {
    if (!$status[$item[0]]) {
        $langkah_berikutnya = $item;
        break;
    }
}

$stmt = $pdo->prepare('SELECT status, dibagikan_pada FROM berbagi_catatan WHERE siswa_id = :id LIMIT 1');
$stmt->execute(['id' => $siswa_id]);
$berbagi = $stmt->fetch() ?: null;
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 via-karvia-800 to-emerald-700 p-6 text-white shadow-lg sm:p-9">
  <p class="mb-3 text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Perjalanan Karierku</p>
  <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Halo, <?= e($siswa['nama_lengkap'] ?? '') ?>.</h2>
  <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-50 sm:text-base">Kamu tidak harus punya semua jawaban sekarang. Kenali dirimu, jelajahi pilihan, lalu tentukan satu langkah yang terasa mungkin.</p>
  <div class="mt-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div class="w-full max-w-sm">
      <div class="mb-2 flex items-center justify-between text-xs font-semibold text-emerald-50">
        <span>Progres eksplorasi</span>
        <span><?= $jumlah_selesai ?> dari 5 langkah</span>
      </div>
      <div class="h-2 overflow-hidden rounded-full bg-white/20" role="progressbar" aria-label="Progres eksplorasi karier" aria-valuemin="0" aria-valuemax="5" aria-valuenow="<?= $jumlah_selesai ?>">
        <div class="h-full rounded-full bg-emerald-200 transition-all" style="width: <?= $persen_selesai ?>%"></div>
      </div>
    </div>
    <?php if ($langkah_berikutnya): ?>
      <a href="<?= e(url('siswa/' . $langkah_berikutnya[2])) ?>" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-karvia-900 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-karvia-800">
        Lanjutkan: <?= e($langkah_berikutnya[1]) ?> <span aria-hidden="true">→</span>
      </a>
    <?php else: ?>
      <a href="<?= e(url('siswa/konsultasi.php')) ?>" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-karvia-900 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-karvia-800">
        Tinjau ringkasanmu <span aria-hidden="true">→</span>
      </a>
    <?php endif; ?>
  </div>
</section>

<section class="mb-8 rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-6" aria-labelledby="status-konsultasi">
  <div class="flex items-start gap-3">
    <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700" aria-hidden="true">
      <?php karvia_icon('message', 'h-5 w-5'); ?>
    </span>
    <div>
      <h2 id="status-konsultasi" class="font-bold text-slate-900">Ruang Konsultasi BK</h2>
      <?php if ($berbagi): ?>
        <?php $label_status = ['baru' => 'Menunggu dibaca Guru BK', 'dibaca' => 'Sudah dibaca Guru BK', 'ditindaklanjuti' => 'Ada tindak lanjut dari Guru BK']; ?>
        <p class="mt-1 text-sm text-slate-600"><?= e($label_status[$berbagi['status']] ?? 'Catatan sudah dibagikan') ?> · <?= e(tanggal_id($berbagi['dibagikan_pada'], true)) ?></p>
        <p class="mt-1 text-xs leading-relaxed text-slate-500">Catatan yang kamu bagikan dapat dilihat Guru BK di sekolahmu.</p>
      <?php else: ?>
        <p class="mt-1 text-sm text-slate-600">Catatanmu belum dibagikan. Kamu yang menentukan kapan siap dibahas.</p>
        <p class="mt-1 text-xs leading-relaxed text-slate-500">Isi eksplorasi tetap pribadi sampai kamu memilih untuk membagikannya.</p>
      <?php endif; ?>
    </div>
  </div>
  <a href="<?= e(url('siswa/konsultasi.php')) ?>" class="mt-4 inline-flex min-h-10 shrink-0 items-center justify-center rounded-lg border border-emerald-200 px-4 py-2 text-sm font-semibold text-karvia-800 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-karvia-500 sm:mt-0">
    <?= $berbagi ? 'Lihat ringkasan' : 'Siapkan konsultasi' ?> <span class="ml-2" aria-hidden="true">→</span>
  </a>
</section>

<section class="mb-6 rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-5">
  <h2 class="text-sm font-bold text-amber-900">Ingat, ini perjalananmu</h2>
  <p class="mt-1 text-xs leading-relaxed text-amber-800">KARVIA adalah media pendukung bimbingan karier, bukan tes psikologis atau penentu pilihan otomatis. Kamu boleh kembali dan memperbarui jawaban kapan saja.</p>
</section>

<section aria-labelledby="langkah-title">
  <div class="mb-4">
    <h2 id="langkah-title" class="text-xl font-extrabold text-slate-900">Langkah eksplorasi karier</h2>
    <p class="mt-1 text-sm text-slate-500">Pilih langkah untuk mulai atau melanjutkan. Progres berdasarkan data yang tersimpan.</p>
  </div>
  <ol class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($langkah as $item): [$kunci, $nama, $tujuan, $deskripsi, $nomor] = $item; ?>
      <li>
        <a href="<?= e(url('siswa/' . $tujuan)) ?>" class="group flex h-full gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-karvia-500 focus:ring-offset-2">
          <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl <?= $status[$kunci] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' ?> text-sm font-extrabold" aria-hidden="true">
            <?= $status[$kunci] ? '✓' : e($nomor) ?>
          </span>
          <span class="min-w-0 flex-1">
            <span class="flex flex-wrap items-center justify-between gap-2">
              <span class="font-bold text-slate-900 group-hover:text-karvia-800"><?= e($nama) ?></span>
              <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold <?= $status[$kunci] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>"><?= $status[$kunci] ? 'Sudah ada data' : 'Belum dimulai' ?></span>
            </span>
            <span class="mt-2 block text-xs leading-relaxed text-slate-500"><?= e($deskripsi) ?></span>
            <span class="mt-3 block text-xs font-semibold text-karvia-700"><?= $status[$kunci] ? 'Lihat atau perbarui' : 'Mulai langkah' ?> <span aria-hidden="true">→</span></span>
          </span>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
