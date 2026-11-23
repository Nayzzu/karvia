<?php
define('KARVIA_APP', true);
$peran_halaman = 'guru_bk';
$halaman_aktif = 'catatan';
$judul_halaman = 'Detail Catatan Siswa';
require_once __DIR__ . '/../includes/header.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($id < 1) {
    http_response_code(404);
    exit('Catatan tidak ditemukan.');
}
$pdo = db();
$sekolah_id = auth_sekolah_id();
$guru_id = (int) auth_pengguna()['id'];
$stmt = $pdo->prepare(
    "SELECT b.id, b.siswa_id, b.sekolah_id, b.pesan_siswa, b.status, b.dibagikan_pada,
            p.nama_lengkap, p.username, p.kelas, p.jurusan, p.sekolah_id AS sekolah_siswa,
            s.nama AS nama_sekolah
     FROM berbagi_catatan b
     JOIN pengguna p ON p.id = b.siswa_id
     JOIN sekolah s ON s.id = b.sekolah_id
     WHERE b.id = :id AND b.sekolah_id = :sekolah_id
       AND p.sekolah_id = :sekolah_siswa AND p.role = 'siswa'
     LIMIT 1"
);
$stmt->execute(['id' => $id, 'sekolah_id' => $sekolah_id, 'sekolah_siswa' => $sekolah_id]);
$catatan = $stmt->fetch();
if (!$catatan) {
    http_response_code(404);
    exit('Catatan tidak ditemukan.');
}
if ($catatan['status'] === 'baru') {
    $stmt = $pdo->prepare("UPDATE berbagi_catatan SET status = 'dibaca', dibaca_pada = NOW(), diperbarui_pada = NOW() WHERE id = :id AND sekolah_id = :sekolah_id AND status = 'baru'");
    $stmt->execute(['id' => $id, 'sekolah_id' => $sekolah_id]);
    $catatan['status'] = 'dibaca';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $aksi = (string) ($_POST['aksi'] ?? '');
    if ((int) ($_POST['id'] ?? 0) !== $id || $aksi !== 'tindak_lanjut') {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
    $isi = trim((string) ($_POST['catatan'] ?? ''));
    if ($isi === '' || mb_strlen($isi) > 5000) {
        flash_set('gagal', 'Catatan tindak lanjut wajib diisi dan maksimal 5.000 karakter.');
        redirect('guru_bk/catatan_detail.php?id=' . $id);
    }
    try {
        $pdo->beginTransaction();
        $cek = $pdo->prepare('SELECT id FROM berbagi_catatan WHERE id = :id AND sekolah_id = :sekolah_id FOR UPDATE');
        $cek->execute(['id' => $id, 'sekolah_id' => $sekolah_id]);
        $berbagi = $cek->fetchColumn();
        $cekSiswa = $pdo->prepare("SELECT COUNT(*) FROM pengguna WHERE id = :siswa_id AND sekolah_id = :sekolah_id AND role = 'siswa'");
        $cekSiswa->execute(['siswa_id' => (int) $catatan['siswa_id'], 'sekolah_id' => $sekolah_id]);
        if (!$berbagi || !(int) $cekSiswa->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('Catatan tidak ditemukan.');
        }
        $stmt = $pdo->prepare('INSERT INTO tindak_lanjut_bk (berbagi_id, guru_id, catatan, dibuat_pada) VALUES (:berbagi_id, :guru_id, :catatan, NOW())');
        $stmt->execute(['berbagi_id' => $id, 'guru_id' => $guru_id, 'catatan' => $isi]);
        $stmt = $pdo->prepare("UPDATE berbagi_catatan SET status = 'ditindaklanjuti', diperbarui_pada = NOW() WHERE id = :id AND sekolah_id = :sekolah_id");
        $stmt->execute(['id' => $id, 'sekolah_id' => $sekolah_id]);
        $pdo->commit();
        log_aktivitas('tindak_lanjut_catatan', 'Guru BK menambahkan tindak lanjut pada catatan yang dibagikan.');
        flash_set('sukses', 'Tindak lanjut berhasil disimpan.');
        redirect('guru_bk/catatan_detail.php?id=' . $id);
    } catch (PDOException $kesalahan) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('KARVIA: gagal menyimpan tindak lanjut: ' . $kesalahan->getMessage());
        flash_set('gagal', 'Tindak lanjut belum dapat disimpan. Coba lagi nanti.');
        redirect('guru_bk/catatan_detail.php?id=' . $id);
    }
}

$stmt = $pdo->prepare('SELECT mapel_favorit, kekuatan, catatan_minat FROM refleksi_diri WHERE siswa_id = :siswa_id LIMIT 1');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$refleksi = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT pr.nama FROM profesi_disimpan pd JOIN profesi pr ON pr.id = pd.profesi_id WHERE pd.siswa_id = :siswa_id ORDER BY pr.nama');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$profesi = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT opsi_a, catatan_a, opsi_b, catatan_b, kriteria FROM pilihan_banding WHERE siswa_id = :siswa_id LIMIT 1');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$banding = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT tujuan, langkah_1, langkah_2 FROM rencana_karier WHERE siswa_id = :siswa_id LIMIT 1');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$rencana = $stmt->fetch() ?: [];
$stmt = $pdo->prepare('SELECT hal_baru, hambatan, dukungan, dibuat_pada FROM jurnal_refleksi WHERE siswa_id = :siswa_id ORDER BY dibuat_pada DESC, id DESC');
$stmt->execute(['siswa_id' => $catatan['siswa_id']]);
$jurnal = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT t.catatan, t.dibuat_pada, p.nama_lengkap AS nama_guru FROM tindak_lanjut_bk t JOIN pengguna p ON p.id = t.guru_id WHERE t.berbagi_id = :berbagi_id ORDER BY t.dibuat_pada DESC, t.id DESC');
$stmt->execute(['berbagi_id' => $id]);
$tindak_lanjut = $stmt->fetchAll();
function guru_nilai($nilai): string
{
    if ($nilai === null || $nilai === '') return 'Belum diisi';
    if (is_array($nilai)) return $nilai ? implode(', ', array_map('strval', $nilai)) : 'Belum diisi';
    return (string) $nilai;
}
function guru_json_daftar($nilai): string
{
    if (!$nilai) return 'Belum diisi';
    $data = json_decode((string) $nilai, true);
    return is_array($data) ? guru_nilai($data) : 'Belum diisi';
}
?>
<div class="space-y-6">
  <!-- Navigasi & Aksi Cetak -->
  <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
    <a href="<?= e(url('guru_bk/catatan.php')) ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#245938] hover:underline">
      &larr; Kembali ke Catatan Siswa
    </a>
    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-[#dfe6de] bg-white px-3.5 py-2 text-xs font-bold text-[#203628] shadow-sm transition hover:bg-[#f6f8f5]">
      <?php karvia_icon('file', 'h-4 w-4'); ?>
      Cetak Ringkasan
    </button>
  </div>

  <!-- Kartu Profil Catatan Siswa -->
  <section class="rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <div class="flex flex-wrap items-center gap-2">
          <h1 class="text-2xl font-bold tracking-tight text-[#203628]"><?= e($catatan['nama_lengkap']) ?></h1>
          <?php
          $badge = match ($catatan['status']) {
              'baru' => 'bg-emerald-50 text-[#224f35] border-emerald-200',
              'dibaca' => 'bg-amber-50 text-amber-800 border-amber-200',
              default => 'bg-slate-50 text-slate-700 border-slate-200',
          };
          ?>
          <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold <?= $badge ?>">
            <?= e(ucfirst($catatan['status'])) ?>
          </span>
        </div>
        <p class="mt-1 text-xs text-slate-500">
          <?= e($catatan['kelas'] ?: 'Kelas belum diisi') ?><?= $catatan['jurusan'] ? ' · ' . e($catatan['jurusan']) : '' ?> · <?= e($catatan['nama_sekolah']) ?> · @<?= e($catatan['username']) ?>
        </p>
        <p class="mt-2 text-xs text-slate-400">
          Dibagikan pada: <span class="font-medium text-slate-700"><?= e(tanggal_id($catatan['dibagikan_pada'], true)) ?></span>
        </p>
      </div>
    </div>

    <?php if ($catatan['pesan_siswa']): ?>
      <div class="mt-4 rounded-xl border border-emerald-100 bg-[#fbfdfb] p-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-[#245938]">Pesan dari Siswa</h2>
        <p class="mt-1.5 whitespace-pre-wrap text-xs leading-relaxed text-slate-700"><?= e($catatan['pesan_siswa']) ?></p>
      </div>
    <?php endif; ?>
  </section>

  <!-- Grid Detail Eksplorasi Karier -->
  <div class="grid gap-6 xl:grid-cols-2">
    <!-- 01. Kenali Diriku -->
    <section class="flex flex-col justify-between rounded-2xl border border-[#dfe6de] bg-white p-5 sm:p-6 shadow-sm">
      <div>
        <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
          <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
            <?php karvia_icon('heart', 'h-4.5 w-4.5'); ?>
          </span>
          <div>
            <h2 class="text-sm font-bold text-[#203628]">01. Kenali Diriku</h2>
            <p class="text-[11px] text-slate-500">Minat, pelajaran favorit, dan kekuatan diri</p>
          </div>
        </div>
        <dl class="space-y-3 text-xs">
          <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
            <dt class="font-semibold text-slate-500">Mata Pelajaran Favorit</dt>
            <dd class="mt-1 font-medium text-[#203628]"><?= e(guru_json_daftar($refleksi['mapel_favorit'] ?? null)) ?></dd>
          </div>
          <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
            <dt class="font-semibold text-slate-500">Kekuatan yang Dirasakan</dt>
            <dd class="mt-1 font-medium text-[#203628]"><?= e(guru_json_daftar($refleksi['kekuatan'] ?? null)) ?></dd>
          </div>
          <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
            <dt class="font-semibold text-slate-500">Catatan Minat &amp; Aspirasi</dt>
            <dd class="mt-1 whitespace-pre-wrap font-medium text-[#203628] leading-relaxed"><?= e(guru_nilai($refleksi['catatan_minat'] ?? null)) ?></dd>
          </div>
        </dl>
      </div>
    </section>

    <!-- 02. Profesi Disimpan -->
    <section class="flex flex-col justify-between rounded-2xl border border-[#dfe6de] bg-white p-5 sm:p-6 shadow-sm">
      <div>
        <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
          <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
            <?php karvia_icon('compass', 'h-4.5 w-4.5'); ?>
          </span>
          <div>
            <h2 class="text-sm font-bold text-[#203628]">02. Profesi yang Disimpan</h2>
            <p class="text-[11px] text-slate-500">Katalog profesi yang diminati siswa</p>
          </div>
        </div>
        <?php if (!$profesi): ?>
          <p class="p-6 text-center text-xs text-slate-500">Belum ada profesi yang disimpan oleh siswa.</p>
        <?php else: ?>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($profesi as $row): ?>
              <span class="inline-flex items-center gap-2 rounded-xl border border-[#dfe6de] bg-[#fbfdfb] px-3.5 py-2 text-xs font-semibold text-[#203628]">
                <span class="h-2 w-2 rounded-full bg-[#224f35]"></span>
                <?= e($row['nama']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- 03. Bandingkan Pilihan -->
    <section class="flex flex-col justify-between rounded-2xl border border-[#dfe6de] bg-white p-5 sm:p-6 shadow-sm">
      <div>
        <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
          <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
            <?php karvia_icon('scale', 'h-4.5 w-4.5'); ?>
          </span>
          <div>
            <h2 class="text-sm font-bold text-[#203628]">03. Bandingkan Pilihan</h2>
            <p class="text-[11px] text-slate-500">Pertimbangan dua opsi karier / pendidikan</p>
          </div>
        </div>
        <?php if (!$banding): ?>
          <p class="p-6 text-center text-xs text-slate-500">Siswa belum mengisi lembar perbandingan.</p>
        <?php else: ?>
          <div class="space-y-3 text-xs">
            <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#245938]">Pilihan A</span>
              <p class="mt-1 font-bold text-[#203628]"><?= e($banding['opsi_a']) ?></p>
              <p class="mt-1 text-slate-600 leading-relaxed"><?= e(guru_nilai($banding['catatan_a'])) ?></p>
            </div>
            <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#245938]">Pilihan B</span>
              <p class="mt-1 font-bold text-[#203628]"><?= e($banding['opsi_b']) ?></p>
              <p class="mt-1 text-slate-600 leading-relaxed"><?= e(guru_nilai($banding['catatan_b'])) ?></p>
            </div>
            <div class="rounded-xl border border-[#eef2ed] bg-white p-3">
              <span class="font-semibold text-slate-500">Kriteria Utama yang Ditimbang:</span>
              <p class="mt-1 font-medium text-[#203628]"><?= e(guru_json_daftar($banding['kriteria'])) ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- 04. Rencana Karier -->
    <section class="flex flex-col justify-between rounded-2xl border border-[#dfe6de] bg-white p-5 sm:p-6 shadow-sm">
      <div>
        <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
          <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
            <?php karvia_icon('calendar', 'h-4.5 w-4.5'); ?>
          </span>
          <div>
            <h2 class="text-sm font-bold text-[#203628]">04. Rencana Langkahku</h2>
            <p class="text-[11px] text-slate-500">Target terdekat dan dua langkah nyata siswa</p>
          </div>
        </div>
        <?php if (!$rencana): ?>
          <p class="p-6 text-center text-xs text-slate-500">Siswa belum menyusun rencana langkah konkret.</p>
        <?php else: ?>
          <dl class="space-y-3 text-xs">
            <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3.5">
              <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tujuan Terdekat</dt>
              <dd class="mt-1 font-bold text-[#203628]"><?= e($rencana['tujuan']) ?></dd>
            </div>
            <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
              <dt class="font-semibold text-[#245938]">Langkah 1</dt>
              <dd class="mt-1 text-slate-700 leading-relaxed"><?= e($rencana['langkah_1']) ?></dd>
            </div>
            <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
              <dt class="font-semibold text-[#245938]">Langkah 2</dt>
              <dd class="mt-1 text-slate-700 leading-relaxed"><?= e($rencana['langkah_2']) ?></dd>
            </div>
          </dl>
        <?php endif; ?>
      </div>
    </section>

    <!-- 05. Jurnal Refleksi (Lebar Penuh) -->
    <section class="rounded-2xl border border-[#dfe6de] bg-white p-5 sm:p-6 shadow-sm xl:col-span-2">
      <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
          <?php karvia_icon('book', 'h-4.5 w-4.5'); ?>
        </span>
        <div>
          <h2 class="text-sm font-bold text-[#203628]">05. Jurnal Refleksi Siswa</h2>
          <p class="text-[11px] text-slate-500">Catatan perkembangan, tantangan, dan dukungan yang dibutuhkan</p>
        </div>
      </div>
      <?php if (!$jurnal): ?>
        <p class="p-6 text-center text-xs text-slate-500">Belum ada catatan jurnal refleksi yang tersimpan.</p>
      <?php else: ?>
        <div class="divide-y divide-[#eef2ed]">
          <?php foreach ($jurnal as $entri): ?>
            <article class="py-4 first:pt-0 last:pb-0 text-xs">
              <div class="flex items-center justify-between text-slate-400 text-[11px] mb-2">
                <span>Refleksi tercatat</span>
                <span><?= e(tanggal_id($entri['dibuat_pada'], true)) ?></span>
              </div>
              <div class="grid gap-3 sm:grid-cols-3">
                <?php foreach (['hal_baru' => ['Hal Baru Dipelajari', '#224f35'], 'hambatan' => ['Hambatan Dirasakan', '#b45309'], 'dukungan' => ['Dukungan Dibutuhkan', '#1d4ed8']] as $kolom => [$label, $warna]): ?>
                  <?php if ($entri[$kolom] !== null && $entri[$kolom] !== ''): ?>
                    <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
                      <span class="font-bold text-[10px] uppercase tracking-wider" style="color: <?= $warna ?>"><?= e($label) ?></span>
                      <p class="mt-1 whitespace-pre-wrap text-slate-700 leading-relaxed"><?= e($entri[$kolom]) ?></p>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <!-- Tindak Lanjut Guru BK (Lebar Penuh) -->
    <section class="rounded-2xl border border-[#dfe6de] bg-white p-5 sm:p-6 shadow-sm xl:col-span-2">
      <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
          <?php karvia_icon('message', 'h-4.5 w-4.5'); ?>
        </span>
        <div>
          <h2 class="text-sm font-bold text-[#203628]">Tindak Lanjut &amp; Bimbingan Konseling</h2>
          <p class="text-[11px] text-slate-500">Catatan arahan dari Guru BK untuk siswa ini</p>
        </div>
      </div>

      <?php if ($tindak_lanjut): ?>
        <ol class="space-y-3 mb-6">
          <?php foreach ($tindak_lanjut as $item): ?>
            <li class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-4 text-xs">
              <div class="flex items-center justify-between text-slate-400 text-[11px] mb-1.5">
                <span class="font-bold text-[#245938]"><?= e($item['nama_guru']) ?></span>
                <span><?= e(tanggal_id($item['dibuat_pada'], true)) ?></span>
              </div>
              <p class="whitespace-pre-wrap text-slate-700 leading-relaxed"><?= e($item['catatan']) ?></p>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php else: ?>
        <p class="mb-5 text-xs text-slate-500">Belum ada catatan tindak lanjut bimbingan untuk siswa ini.</p>
      <?php endif; ?>

      <!-- Form Tambah Tindak Lanjut -->
      <form method="post" class="space-y-3 border-t border-[#eef2ed] pt-5 print:hidden">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <input type="hidden" name="aksi" value="tindak_lanjut">
        <div>
          <label for="catatan" class="mb-1 block text-xs font-bold text-slate-700">
            Tambah Catatan Tindak Lanjut
          </label>
          <textarea id="catatan" name="catatan" required maxlength="5000" rows="4" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] p-3 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]" placeholder="Tuliskan umpan balik, rekomendasi eksplorasi, atau jadwal sesi tatap muka berikutnya..."></textarea>
          <p class="mt-1 text-[11px] text-slate-400">Maksimal 5.000 karakter. Catatan dapat dibaca kembali oleh Anda dan Guru BK di sekolah ini.</p>
        </div>
        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1a4029]">
          Simpan Tindak Lanjut
        </button>
      </form>
    </section>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
