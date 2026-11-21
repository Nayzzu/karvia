<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';
$halaman_aktif = 'tips';
$judul_halaman = 'Tips & Edukasi Karier';
require_once __DIR__ . '/../includes/header.php';

$artikel = [];
try {
    $stmt = db()->query('SELECT id, judul, gambar, isi, nama_penulis, diterbitkan_pada FROM artikel ORDER BY diterbitkan_pada DESC, id DESC LIMIT 6');
    $artikel = $stmt->fetchAll();
} catch (PDOException $kesalahan) {
    error_log('KARVIA: artikel edukasi siswa gagal dimuat: ' . $kesalahan->getMessage());
}

$tips = [
    [
        'nomor' => '01',
        'judul' => 'Mulai dari hal yang membuatmu penasaran',
        'isi' => 'Perhatikan pelajaran, kegiatan, atau masalah di sekitar yang membuatmu ingin tahu lebih banyak. Rasa penasaran bisa menjadi titik awal eksplorasi, bukan label tentang siapa dirimu.',
        'coba' => 'Catat satu hal yang ingin kamu cari tahu di Jurnal Refleksi.',
        'tautan' => 'siswa/jurnal.php',
        'label' => 'Tulis refleksi',
        'ikon' => 'lightbulb',
    ],
    [
        'nomor' => '02',
        'judul' => 'Periksa sumber sebelum percaya',
        'isi' => 'Cari informasi dari situs resmi sekolah, kampus, pemerintah, atau lembaga profesi. Bandingkan informasi penting, terutama syarat masuk, biaya, beasiswa, dan akreditasi.',
        'coba' => 'Temukan satu sumber resmi untuk pilihan yang sedang kamu pertimbangkan.',
        'tautan' => 'siswa/jalur.php',
        'label' => 'Kenali jalur lanjut',
        'ikon' => 'search',
    ],
    [
        'nomor' => '03',
        'judul' => 'Tanyakan seperti apa kesehariannya',
        'isi' => 'Nama profesi saja belum cukup untuk membayangkan pekerjaan. Jika ada kesempatan, tanya orang yang mengenal bidang itu tentang tugas harian, tantangan, keterampilan, dan jalur belajarnya.',
        'coba' => 'Siapkan satu pertanyaan yang ingin kamu ajukan kepada Guru BK atau orang tepercaya.',
        'tautan' => 'siswa/konsultasi.php',
        'label' => 'Siapkan obrolan',
        'ikon' => 'message',
    ],
    [
        'nomor' => '04',
        'judul' => 'Coba langkah kecil, bukan keputusan besar',
        'isi' => 'Kamu bisa mencoba proyek sekolah, membaca pengantar, mengikuti kegiatan, atau berbicara dengan seseorang. Pengalaman kecil membantu mengumpulkan informasi tanpa harus langsung menentukan masa depan.',
        'coba' => 'Pilih satu tindakan sederhana untuk ditulis di rencanamu.',
        'tautan' => 'siswa/rencana.php',
        'label' => 'Buat rencana kecil',
        'ikon' => 'check',
    ],
    [
        'nomor' => '05',
        'judul' => 'Bicarakan pertimbanganmu',
        'isi' => 'Keluarga, Guru BK, guru, atau orang dewasa tepercaya dapat membantu mencari informasi dan melihat pilihan dari sudut pandang lain. Kamu tetap berhak bertanya dan mempertimbangkan dengan waktumu sendiri.',
        'coba' => 'Tuliskan hal yang ingin kamu bicarakan sebelum konsultasi.',
        'tautan' => 'siswa/konsultasi.php',
        'label' => 'Siapkan konsultasi',
        'ikon' => 'users',
    ],
];
?>
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-karvia-900 via-karvia-800 to-emerald-700 p-6 text-white shadow-lg sm:p-8">
  <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-100">Bekal eksplorasi karier</p>
  <h2 class="mt-3 max-w-2xl text-2xl font-extrabold tracking-tight sm:text-3xl">Kumpulkan informasi. Ambil langkahmu sendiri.</h2>
  <p class="mt-2 max-w-2xl text-sm leading-relaxed text-emerald-50">Gunakan tips dan bacaan ini sebagai bahan untuk bertanya dan berdiskusi. Kamu tidak perlu menyelesaikan semuanya hari ini.</p>
</section>

<section class="mb-8" aria-labelledby="tips-praktis">
  <div class="mb-4">
    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Bisa dicoba sekarang</p>
    <h2 id="tips-praktis" class="mt-1 text-xl font-extrabold text-slate-900">Lima langkah ringan</h2>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <?php foreach ($tips as $tip): ?>
      <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start gap-3">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-karvia-700" aria-hidden="true"><?php karvia_icon($tip['ikon'], 'h-5 w-5'); ?></span>
          <div>
            <p class="text-[11px] font-bold tracking-wider text-emerald-800">LANGKAH <?= e($tip['nomor']) ?></p>
            <h3 class="mt-1 font-extrabold leading-snug text-slate-900"><?= e($tip['judul']) ?></h3>
          </div>
        </div>
        <p class="mt-3 text-sm leading-relaxed text-slate-600"><?= e($tip['isi']) ?></p>
        <div class="mt-auto pt-4">
          <p class="rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-700"><strong class="text-slate-900">Coba:</strong> <?= e($tip['coba']) ?></p>
          <a href="<?= e(url($tip['tautan'])) ?>" class="mt-3 inline-flex min-h-10 items-center gap-2 text-sm font-bold text-emerald-800 hover:text-emerald-950 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2"><?= e($tip['label']) ?> <span aria-hidden="true">→</span></a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="mb-6" aria-labelledby="bacaan-karier">
  <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
    <div>
      <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Bacaan pilihan</p>
      <h2 id="bacaan-karier" class="mt-1 text-xl font-extrabold text-slate-900">Artikel edukasi karier</h2>
      <p class="mt-1 max-w-2xl text-sm leading-relaxed text-slate-600">Bacaan yang diterbitkan di KARVIA dapat membantu membuka pertanyaan baru. Perhatikan nama penulis dan tanggal terbit.</p>
    </div>
    <a href="<?= e(url('index.php#artikel')) ?>" class="text-sm font-semibold text-emerald-800 hover:underline">Lihat artikel publik <span aria-hidden="true">→</span></a>
  </div>

  <?php if (!$artikel): ?>
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center">
      <p class="font-semibold text-slate-800">Belum ada artikel edukasi yang diterbitkan.</p>
      <p class="mt-1 text-sm text-slate-500">Tips di atas tetap bisa digunakan. Coba kembali nanti untuk melihat bacaan terbaru.</p>
    </div>
  <?php else: ?>
    <div class="relative">
      <div id="artikel-carousel" class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-4" aria-label="Artikel edukasi karier" tabindex="0">
        <?php foreach ($artikel as $indeks => $bacaan): ?>
          <?php $ringkasan = trim(preg_replace('/\s+/u', ' ', strip_tags($bacaan['isi']))); ?>
          <article id="artikel-slide-<?= (int) $indeks ?>" class="w-[88%] shrink-0 snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:w-[calc(50%-0.5rem)] xl:w-[calc(33.333%-0.67rem)]">
            <a href="<?= e(url('artikel.php?id=' . (int) $bacaan['id'])) ?>" class="group flex h-full flex-col focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-700">
              <img src="<?= e(url('uploads/artikel/' . rawurlencode($bacaan['gambar']))) ?>" alt="Sampul artikel: <?= e($bacaan['judul']) ?>" loading="lazy" class="aspect-[16/9] w-full bg-slate-100 object-cover">
              <div class="flex flex-1 flex-col p-5">
                <p class="text-xs font-medium text-slate-500"><?= e(tanggal_id($bacaan['diterbitkan_pada'])) ?> <span aria-hidden="true">·</span> <?= e($bacaan['nama_penulis']) ?></p>
                <h3 class="mt-2 font-extrabold leading-snug text-slate-900 group-hover:text-emerald-800"><?= e($bacaan['judul']) ?></h3>
                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600"><?= e(mb_substr($ringkasan, 0, 190)) ?><?= mb_strlen($ringkasan) > 190 ? '…' : '' ?></p>
                <span class="mt-auto inline-flex items-center gap-2 pt-4 text-sm font-bold text-emerald-800">Baca artikel <span aria-hidden="true">→</span></span>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
      <?php if (count($artikel) > 1): ?>
        <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
          <p class="text-xs text-slate-500">Geser artikel untuk melihat bacaan lainnya.</p>
          <div class="flex items-center gap-2">
            <button type="button" data-carousel-dir="-1" aria-label="Artikel sebelumnya" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-700">
              <span aria-hidden="true">←</span>
            </button>
            <p id="artikel-status" class="min-w-24 text-center text-xs font-medium text-slate-600" aria-live="polite">Artikel 1 dari <?= count($artikel) ?></p>
            <button type="button" data-carousel-dir="1" aria-label="Artikel berikutnya" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-700">
              <span aria-hidden="true">→</span>
            </button>
          </div>
        </div>
        <div class="mt-3 flex justify-center gap-2" aria-label="Pilih artikel">
          <?php foreach ($artikel as $indeks => $bacaan): ?>
            <button type="button" data-carousel-slide="<?= (int) $indeks ?>" aria-label="Tampilkan artikel <?= (int) ($indeks + 1) ?>" class="h-2.5 w-2.5 rounded-full bg-slate-300 transition-colors aria-[current=true]:w-6 aria-[current=true]:bg-emerald-700"></button>
          <?php endforeach; ?>
        </div>
        <script>
          (() => {
            const track = document.getElementById('artikel-carousel');
            const status = document.getElementById('artikel-status');
            if (!track || !status) return;
            const slides = [...track.querySelectorAll('article')];
            const dots = [...document.querySelectorAll('[data-carousel-slide]')];
            let current = 0;
            const show = (index, scroll = true) => {
              current = (index + slides.length) % slides.length;
              if (scroll) slides[current].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
              status.textContent = `Artikel ${current + 1} dari ${slides.length}`;
              dots.forEach((dot, i) => dot.setAttribute('aria-current', i === current ? 'true' : 'false'));
            };
            document.querySelectorAll('[data-carousel-dir]').forEach(button => button.addEventListener('click', () => show(current + Number(button.dataset.carouselDir))));
            dots.forEach((dot, i) => dot.addEventListener('click', () => show(i)));
            let timer;
            track.addEventListener('scroll', () => {
              clearTimeout(timer);
              timer = setTimeout(() => {
                const left = track.getBoundingClientRect().left;
                const nearest = slides.reduce((best, slide, i) => Math.abs(slide.getBoundingClientRect().left - left) < Math.abs(slides[best].getBoundingClientRect().left - left) ? i : best, 0);
                show(nearest, false);
              }, 100);
            }, { passive: true });
            show(0, false);
          })();
        </script>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</section>

<section class="rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">
  <strong>Ingat:</strong> KARVIA adalah media pendukung bimbingan karier, bukan tes psikologis dan bukan penentu pilihan otomatis. Artikel merupakan bahan informasi; cocokkan keterangan penting dengan sumber resmi terbaru dan diskusikan jika perlu.
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
