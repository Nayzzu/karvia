<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'jalur';
$judul_halaman = 'Jalur Setelah Lulus';
require_once __DIR__ . '/../includes/header.php';

// Konten statis: tidak disimpan di database karena hanya bahan baca, bukan data siswa.
$jalur = [
    [
        'ikon' => 'school',
        'nama' => 'Perguruan Tinggi',
        'ringkas' => 'Melanjutkan studi akademik di program sarjana (S1).',
        'cocok' => 'Kamu ingin mendalami satu bidang secara luas dan menikmati belajar teori, riset, atau diskusi.',
        'cek' => ['Program studi dan kurikulumnya, bukan hanya nama kampus.', 'Akreditasi program studi, bukan hanya akreditasi kampus.', 'Biaya kuliah, biaya hidup, dan jalur beasiswa yang tersedia.', 'Syarat masuk: seleksi nasional, mandiri, atau jalur lain.'],
        'langkah' => 'Buka satu situs resmi kampus dan catat syarat masuk program studi yang membuatmu penasaran.',
    ],
    [
        'ikon' => 'book',
        'nama' => 'Pendidikan Vokasi',
        'ringkas' => 'Program diploma (D3/D4) yang menekankan praktik dan keterampilan terapan.',
        'cocok' => 'Kamu lebih nyaman belajar lewat praktik langsung dan ingin keterampilan yang jelas terpakai saat bekerja.',
        'cek' => ['Bidang keahlian dan laboratorium/praktik yang dipakai.', 'Akreditasi, izin, dan kerja sama industri sekolah.', 'Pengalaman magang atau praktik kerja di lapangan.', 'Gambaran pekerjaan awal lulusan.'],
        'langkah' => 'Tanyakan ke Guru BK atau alumni satu sekolah vokasi: bagaimana rasanya belajar praktik setiap hari?',
    ],
    [
        'ikon' => 'file',
        'nama' => 'Pelatihan dan Sertifikasi',
        'ringkas' => 'Kursus atau sertifikasi untuk keterampilan tertentu, biasanya lebih singkat.',
        'cocok' => 'Kamu ingin menguasai keterampilan khusus dulu, lalu memutuskan kuliah atau bekerja setelah punya pengalaman.',
        'cek' => ['Penyelenggara: badan resmi, industri, atau lembaga pelatihan biasa.', 'Biaya, durasi, dan syarat mengikuti.', 'Kompetensi yang benar-benar disampaikan, bukan sekadar sertifikat.', 'Apakah sertifikatnya diakui pemberi kerja di bidang itu.'],
        'langkah' => 'Tulis keterampilan yang ingin kamu kuasai, lalu cari satu pelatihan yang benar-benar mengajarkannya.',
    ],
    [
        'ikon' => 'briefcase',
        'nama' => 'Bekerja',
        'ringkas' => 'Langsung masuk dunia kerja setelah lulus dengan bekal keterampilan yang ada.',
        'cocok' => 'Kamu lebih belajar dari pengalaman nyata, perlu penghasilan, atau ingin melihat dunia kerja sebelum menentukan studi.',
        'cek' => ['Syarat pendidikan dan keterampilan yang diminta, bukan hanya nama jabatannya.', 'Jam kerja, jenis kontrak, upah, dan hak dasar pekerja.', 'Aturan usia kerja dan perlindungan bagi pekerja muda.', 'Kemungkinan belajar sambil bekerja di masa depan.'],
        'langkah' => 'Baca tiga iklan lowongan di bidang yang menarik dan catat keterampilan seperti apa yang paling sering diminta.',
    ],
    [
        'ikon' => 'lightbulb',
        'nama' => 'Wirausaha',
        'ringkas' => 'Membangun usaha sendiri atau bersama orang lain.',
        'cocok' => 'Kamu senang melihat masalah di sekitar, mencoba solusi, dan siap belajar dari hasil yang belum berjalan.',
        'cek' => ['Kebutuhan nyata dan siapa yang akan membeli, bukan hanya ide yang terasa bagus.', 'Modal, risiko, dan berapa lama bisa bertahan sebelum untung.', 'Izin usaha dan aturan yang berlaku untuk bidangnya.', 'Mentor atau pengalaman yang bisa dipelajari lebih dulu.'],
        'langkah' => 'Tanyakan ke satu pedagang atau pemilik usaha kecil: apa bagian tersulit dari awal membuka usaha?',
    ],
];
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 via-karvia-800 to-emerald-700 p-6 text-white shadow-lg sm:p-8">
  <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Panduan · Bukan keputusan akhir</p>
  <h2 class="mt-3 max-w-2xl text-2xl font-extrabold tracking-tight sm:text-3xl">Kenali jalurnya dulu, sebelum memilih satu.</h2>
  <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">Ada beberapa jalur setelah lulus SMA/MA. Kamu tidak harus memilih sekarang, tetapi kamu bisa mulai mengenali perbedaannya: apa yang dipelajari, siapa yang cocok, dan apa yang perlu dicek.</p>
</section>

<section class="mb-6 grid gap-3 sm:grid-cols-2" aria-label="Ringkasan jalur setelah lulus">
  <?php foreach ($jalur as $item): ?>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700" aria-hidden="true"><?php karvia_icon($item['ikon'], 'h-5 w-5'); ?></span>
        <h3 class="font-bold text-slate-900"><?= e($item['nama']) ?></h3>
      </div>
      <p class="mt-2 text-sm leading-relaxed text-slate-600"><?= e($item['ringkas']) ?></p>
    </div>
  <?php endforeach; ?>
</section>

<section class="space-y-4" aria-labelledby="detail-jalur">
  <div class="flex flex-wrap items-end justify-between gap-2">
    <div>
      <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Lihat lebih dekat</p>
      <h2 id="detail-jalur" class="mt-1 text-xl font-extrabold text-slate-900">Apa yang perlu kamu tahu?</h2>
    </div>
    <p class="text-xs text-slate-500">Baca satu, lewati yang belum terasa relevan.</p>
  </div>

  <div class="space-y-4">
    <?php foreach ($jalur as $item): ?>
      <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex items-start gap-3">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700" aria-hidden="true"><?php karvia_icon($item['ikon'], 'h-5 w-5'); ?></span>
          <div class="min-w-0">
            <h3 class="text-base font-extrabold text-slate-900"><?= e($item['nama']) ?></h3>
            <p class="mt-1 text-sm leading-relaxed text-slate-600"><?= e($item['ringkas']) ?></p>
          </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div class="rounded-xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Coba pertimbangkan bila</p>
            <p class="mt-1.5 text-sm leading-relaxed text-slate-700"><?= e($item['cocok']) ?></p>
          </div>
          <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Satu langkah kecil</p>
            <p class="mt-1.5 text-sm leading-relaxed text-emerald-900"><?= e($item['langkah']) ?></p>
          </div>
        </div>

        <div class="mt-4">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Yang perlu dicek</p>
          <ul class="mt-2 grid gap-1.5 text-sm leading-relaxed text-slate-700 sm:grid-cols-2">
            <?php foreach ($item['cek'] as $poin): ?>
              <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-600" aria-hidden="true"></span><span><?= e($poin) ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="cara-membandingkan-jalur">
  <h2 id="cara-membandingkan-jalur" class="font-extrabold text-slate-900">Cara membandingkan jalur</h2>
  <p class="mt-1 text-sm leading-relaxed text-slate-600">Jalur yang cocok biasanya terasa masuk akal di lebih dari satu hal, bukan hanya karena banyak yang memilihnya.</p>
  <ol class="mt-3 grid gap-3 text-sm leading-relaxed text-slate-700 sm:grid-cols-2">
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">1</span><span><strong class="text-slate-900">Waktu dan cara belajar:</strong> apakah kamu lebih nyaman belajar bertahap di kelas atau langsung praktik?</span></li>
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">2</span><span><strong class="text-slate-900">Biaya dan durasi:</strong> berapa yang perlu disiapkan, dan berapa lama sebelum boleh mulai bekerja?</span></li>
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">3</span><span><strong class="text-slate-900">Kegiatan sehari-hari:</strong> bayangkan tugas hariannya, bukan hanya gelar atau nama jabatannya.</span></li>
    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-900">4</span><span><strong class="text-slate-900">Dukungan yang bisa kamu dapat:</strong> siapa yang bisa diajak bertanya, dan bantuan apa yang tersedia?</span></li>
  </ol>
  <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap">
    <a href="<?= e(url('siswa/bandingkan.php')) ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100">Bandingkan dua jalur</a>
    <a href="<?= e(url('siswa/profesi.php')) ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Lihat profesi terkait</a>
    <a href="<?= e(url('siswa/konsultasi.php')) ?>" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Diskusikan dengan Guru BK</a>
  </div>
</section>

<section class="mt-5 rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">
  <strong>Perlu diingat:</strong> biaya, beasiswa, syarat masuk, akreditasi, dan aturan kerja bisa berubah tiap tahun. Periksa informasi terbaru dari sumber resmi — situs sekolah, kampus, pemerintah, atau lembaga yang berwenang — sebelum menentukan pilihan. KARVIA hanya membantu kamu memahami pilihan, bukan menetapkan satu jalur terbaik.
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
