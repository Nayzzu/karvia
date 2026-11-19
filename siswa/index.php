<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'index';
$judul_halaman = 'Beranda Siswa';
require_once __DIR__ . '/../includes/header.php';

$siswa_id = (int) auth_pengguna()['id'];
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
$jumlah_selesai = count(array_filter($status));
?>
<section class="mb-6 rounded-3xl bg-gradient-to-r from-karvia-900 to-emerald-700 p-6 text-white shadow-xl sm:p-10">
  <span class="mb-3 inline-block rounded-full border border-emerald-400/30 bg-emerald-500/30 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-100">KARVIA · Langkah Karierku</span>
  <h1 class="mb-3 text-3xl font-extrabold sm:text-4xl">Halo, <?= e(auth_pengguna()['nama_lengkap'] ?? '') ?>!</h1>
  <p class="mb-6 max-w-2xl text-sm leading-relaxed text-emerald-100 sm:text-base">Kenali dirimu, jelajahi pilihan, bandingkan secara terstruktur, dan rancang langkah konkret bersama Guru BK.</p>
  <a href="<?= e(url('siswa/kenali_diri.php')) ?>" class="inline-flex rounded-xl bg-white px-6 py-3 font-bold text-karvia-900 shadow-lg hover:bg-emerald-50">Mulai Eksplorasi <i class="fa-solid fa-arrow-right ml-2 mt-1"></i></a>
</section>
<section class="mb-6 rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-5"><h2 class="text-sm font-bold text-amber-900">Tentang KARVIA</h2><p class="mt-1 text-xs leading-relaxed text-amber-800">KARVIA adalah media pendukung bimbingan karier, bukan tes psikologis atau penentu pilihan otomatis. Catatan eksplorasi bersifat personal dan dapat dibagikan kepada Guru BK.</p></section>
<section>
  <div class="mb-4"><h2 class="text-xl font-extrabold text-slate-900">Langkah Eksplorasi Karier</h2><p class="mt-1 text-sm text-slate-500"><?= e($jumlah_selesai) ?> dari 5 langkah sudah memiliki data.</p></div>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <?php foreach ([
        ['refleksi', 'Kenali Diriku', 'siswa/kenali_diri.php', 'Refleksi minat, kekuatan, dan nilai pribadi.'],
        ['profesi', 'Jelajah Profesi', 'siswa/profesi.php', 'Eksplorasi profesi dan simpan yang menarik.'],
        ['bandingkan', 'Bandingkan Pilihan', 'siswa/bandingkan.php', 'Pertimbangkan dua pilihan berdasarkan kriteria.'],
        ['rencana', 'Rencana Langkahku', 'siswa/rencana.php', 'Susun tujuan dan langkah kecil yang dapat dilakukan.'],
        ['jurnal', 'Jurnal Refleksi', 'siswa/jurnal.php', 'Catat hal baru, hambatan, dan dukungan.'],
    ] as [$kunci, $nama, $tujuan, $deskripsi]): ?>
      <a href="<?= e(url($tujuan)) ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-emerald-300"><div class="mb-3 flex items-center justify-between"><span class="font-bold text-slate-900"><?= e($nama) ?></span><span class="rounded-full px-2 py-1 text-xs <?= $status[$kunci] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' ?>"><?= $status[$kunci] ? 'Terisi' : 'Belum diisi' ?></span></div><p class="text-xs leading-relaxed text-slate-500"><?= e($deskripsi) ?></p></a>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
