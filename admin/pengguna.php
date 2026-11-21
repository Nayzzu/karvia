<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'pengguna';
$judul_halaman = 'Kelola Pengguna';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$admin_id = (int) auth_pengguna()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $aksi = (string) ($_POST['aksi'] ?? '');
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    if ($id < 1 || !in_array($aksi, ['status', 'reset'], true)) {
        flash_set('gagal', 'Permintaan pengguna tidak valid.');
        redirect('admin/pengguna.php');
    }
    $stmt = $pdo->prepare('SELECT id, nama_lengkap, username, role, status FROM pengguna WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $target = $stmt->fetch();
    if (!$target) {
        http_response_code(404);
        exit('Pengguna tidak ditemukan.');
    }
    if ($aksi === 'status') {
        $status = (string) ($_POST['status'] ?? '');
        if (!in_array($status, ['aktif', 'nonaktif'], true) || ($id === $admin_id && $status === 'nonaktif')) {
            flash_set('gagal', 'Perubahan status tidak diizinkan. Admin tidak dapat menonaktifkan akunnya sendiri.');
            redirect('admin/pengguna.php');
        }
        $stmt = $pdo->prepare('UPDATE pengguna SET status = :status, diperbarui_pada = NOW() WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        log_aktivitas($status === 'aktif' ? 'aktif_pengguna' : 'nonaktif_pengguna', 'Status akun diperbarui oleh Admin.');
        flash_set('sukses', 'Status akun berhasil diperbarui.');
        redirect('admin/pengguna.php');
    }

    $sementara = bin2hex(random_bytes(6));
    $stmt = $pdo->prepare('UPDATE pengguna SET password_hash = :hash, diperbarui_pada = NOW() WHERE id = :id');
    $stmt->execute(['hash' => password_hash($sementara, PASSWORD_DEFAULT), 'id' => $id]);
    log_aktivitas('reset_password', 'Admin mereset password akun.');
    $_SESSION['_password_sementara'] = ['username' => $target['username'], 'password' => $sementara];
    flash_set('sukses', 'Password sementara dibuat. Salin sekarang; password hanya ditampilkan sekali.');
    redirect('admin/pengguna.php');
}

$sementara = $_SESSION['_password_sementara'] ?? null;
unset($_SESSION['_password_sementara']);
$cari = trim((string) ($_GET['cari'] ?? ''));
$role = (string) ($_GET['role'] ?? '');
$sekolah_id = filter_input(INPUT_GET, 'sekolah_id', FILTER_VALIDATE_INT) ?: 0;
$status = (string) ($_GET['status'] ?? '');
$online = (string) ($_GET['online'] ?? '');
$klausa = [];
$parameter = [];
if ($cari !== '') {
    $klausa[] = '(p.nama_lengkap LIKE :nama OR p.username LIKE :username)';
    $parameter['nama'] = '%' . $cari . '%';
    $parameter['username'] = '%' . $cari . '%';
}
if (in_array($role, ['admin', 'guru_bk', 'siswa'], true)) {
    $klausa[] = 'p.role = :role';
    $parameter['role'] = $role;
}
if ($sekolah_id > 0) {
    $klausa[] = 'p.sekolah_id = :sekolah_id';
    $parameter['sekolah_id'] = $sekolah_id;
}
if (in_array($status, ['aktif', 'nonaktif'], true)) {
    $klausa[] = 'p.status = :status';
    $parameter['status'] = $status;
}
if ($online === '1') {
    $klausa[] = "p.status = 'aktif' AND p.terakhir_aktif_pada IS NOT NULL AND p.terakhir_aktif_pada >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
} elseif ($online === '0') {
    $klausa[] = "(p.terakhir_aktif_pada IS NULL OR p.terakhir_aktif_pada < DATE_SUB(NOW(), INTERVAL 5 MINUTE))";
}
$sql = "SELECT p.id, p.nama_lengkap, p.username, p.role, p.status, p.kelas, p.last_login_at, p.terakhir_aktif_pada,
               (p.status = 'aktif' AND p.terakhir_aktif_pada IS NOT NULL AND p.terakhir_aktif_pada >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)) AS sedang_online,
               s.nama AS sekolah_nama
        FROM pengguna p LEFT JOIN sekolah s ON s.id = p.sekolah_id";
if ($klausa) $sql .= ' WHERE ' . implode(' AND ', $klausa);
$sql .= ' ORDER BY p.dibuat_pada DESC, p.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$pengguna = $stmt->fetchAll();
$sekolah = $pdo->query("SELECT id, nama FROM sekolah WHERE status = 'aktif' ORDER BY nama")->fetchAll();
?>
<?php if ($sementara): ?>
  <section class="rounded-2xl border border-amber-300 bg-amber-50/80 p-5 text-amber-950 shadow-sm" role="status">
    <div class="flex items-start gap-3">
      <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-amber-900">
        <?php karvia_icon('lightbulb', 'h-4 w-4'); ?>
      </span>
      <div>
        <h2 class="font-bold text-sm">Password Sementara — Harap Segera Salin</h2>
        <div class="mt-2 flex flex-wrap gap-4 text-xs">
          <div>Username: <code class="rounded bg-white/80 px-2 py-0.5 font-bold border border-amber-200"><?= e($sementara['username']) ?></code></div>
          <div>Password: <code class="rounded bg-white px-2 py-0.5 font-bold text-amber-900 border border-amber-300"><?= e($sementara['password']) ?></code></div>
        </div>
        <p class="mt-2 text-[11px] text-amber-800">Password ini hanya tampil sekali dan tidak akan ditampilkan lagi setelah halaman ini ditutup atau dimuat ulang.</p>
      </div>
    </div>
  </section>
<?php endif; ?>

<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
  <div>
    <h2 class="text-xl font-bold tracking-tight text-[#203628]">Daftar Pengguna Sistem</h2>
    <p class="mt-0.5 text-xs text-slate-500">Kelola kredensial akun dan pantau status aktif/online. Data isi bimbingan/jurnal siswa tidak dibuka di sini.</p>
  </div>
  <a href="<?= e(url('admin/pengguna_form.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-[#224f35] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1a4029]">
    <?php karvia_icon('plus', 'h-4 w-4'); ?>
    Tambah Pengguna
  </a>
</div>

<!-- Filter Box Attendara Style -->
<form method="get" action="<?= e(url('admin/pengguna.php')) ?>" class="rounded-2xl border border-[#dfe6de] bg-white p-4 shadow-sm sm:p-5">
  <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
    <div class="lg:col-span-2">
      <label for="cari" class="mb-1 block text-xs font-semibold text-slate-700">Cari Nama / Username</label>
      <input id="cari" name="cari" value="<?= e($cari) ?>" placeholder="Ketik nama atau @username..." class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
    </div>
    <div>
      <label for="role" class="mb-1 block text-xs font-semibold text-slate-700">Peran</label>
      <select id="role" name="role" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        <option value="">Semua Peran</option>
        <?php foreach (['admin' => 'Admin', 'guru_bk' => 'Guru BK', 'siswa' => 'Siswa'] as $nilai => $label): ?>
          <option value="<?= e($nilai) ?>" <?= $role === $nilai ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="sekolah_id" class="mb-1 block text-xs font-semibold text-slate-700">Sekolah</label>
      <select id="sekolah_id" name="sekolah_id" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        <option value="">Semua Sekolah</option>
        <?php foreach ($sekolah as $row): ?>
          <option value="<?= (int) $row['id'] ?>" <?= $sekolah_id === (int) $row['id'] ? 'selected' : '' ?>><?= e($row['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="status" class="mb-1 block text-xs font-semibold text-slate-700">Status Akun</label>
      <select id="status" name="status" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        <option value="">Semua Status</option>
        <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
      </select>
    </div>
    <div>
      <label for="online" class="mb-1 block text-xs font-semibold text-slate-700">Aktivitas</label>
      <select id="online" name="online" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        <option value="">Semua</option>
        <option value="1" <?= $online === '1' ? 'selected' : '' ?>>Sedang Online</option>
        <option value="0" <?= $online === '0' ? 'selected' : '' ?>>Offline</option>
      </select>
    </div>
  </div>
  <div class="mt-4 flex items-center justify-end gap-2 border-t border-[#eef2ed] pt-3">
    <a href="<?= e(url('admin/pengguna.php')) ?>" class="rounded-xl border border-[#dfe6de] px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-[#f6f8f5]">
      Reset
    </a>
    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-[#224f35] px-4 py-2 text-xs font-bold text-white hover:bg-[#1a4029]">
      <?php karvia_icon('search', 'h-3.5 w-3.5'); ?>
      Terapkan Filter
    </button>
  </div>
</form>

<!-- Tabel Pengguna Attendara Style -->
<section class="rounded-2xl border border-[#dfe6de] bg-white shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-[#f3f6f1] text-xs uppercase tracking-wide text-slate-600">
        <tr>
          <th scope="col" class="px-5 py-3.5 font-semibold">Nama / Username</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Peran</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Sekolah</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Status Akun</th>
          <th scope="col" class="px-5 py-3.5 font-semibold">Terakhir Login</th>
          <th scope="col" class="px-5 py-3.5 font-semibold text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#edf1ec] text-slate-700">
        <?php if (!$pengguna): ?>
          <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada pengguna yang cocok dengan kriteria filter.</td></tr>
        <?php else: foreach ($pengguna as $baris): ?>
          <tr class="transition-colors hover:bg-[#f8faf7]">
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                <span class="font-bold text-[#203628]"><?= e($baris['nama_lengkap']) ?></span>
                <?php if (!empty($baris['sedang_online'])): ?>
                  <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800" title="Aktif dalam 5 menit terakhir">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Online
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 text-[10px] text-slate-400" title="Offline">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                    Offline
                  </span>
                <?php endif; ?>
              </div>
              <div class="text-xs text-slate-500">@<?= e($baris['username']) ?></div>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-block rounded-md bg-[#eef3ee] px-2.5 py-1 text-xs font-semibold text-[#245938]">
                <?= e($baris['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($baris['role'])) ?>
              </span>
            </td>
            <td class="px-5 py-3.5 text-xs text-slate-700">
              <?= e($baris['sekolah_nama'] ?: 'Global (Semua Sekolah)') ?>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                <?= e(ucfirst($baris['status'])) ?>
              </span>
            </td>
            <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
              <?= e(tanggal_id($baris['last_login_at'], true)) ?>
            </td>
            <td class="px-5 py-3.5 text-right whitespace-nowrap">
              <div class="inline-flex items-center gap-2">
                <a class="inline-flex items-center rounded-lg border border-[#cfe0d3] bg-[#edf4ee] px-2.5 py-1 text-xs font-semibold text-[#245938] hover:bg-[#dfeada]" href="<?= e(url('admin/pengguna_form.php?id=' . (int) $baris['id'])) ?>">
                  Ubah
                </a>
                <form method="post" class="inline" onsubmit="return confirm('Buat password sementara baru untuk akun ini?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="aksi" value="reset">
                  <input type="hidden" name="id" value="<?= (int) $baris['id'] ?>">
                  <button class="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 hover:bg-amber-100">
                    Reset
                  </button>
                </form>
                <?php if ((int) $baris['id'] !== $admin_id): ?>
                  <form method="post" class="inline" onsubmit="return confirm('<?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> akun ini?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="status">
                    <input type="hidden" name="id" value="<?= (int) $baris['id'] ?>">
                    <input type="hidden" name="status" value="<?= $baris['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>">
                    <button class="inline-flex items-center rounded-lg border px-2.5 py-1 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' ?>">
                      <?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
