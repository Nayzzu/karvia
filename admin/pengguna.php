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
<?php if ($sementara): ?><section class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-5 text-amber-950" role="status"><h2 class="font-bold">Password sementara — salin sekarang</h2><p class="mt-1 text-sm">Username: <code><?= e($sementara['username']) ?></code></p><p class="mt-1 text-sm">Password: <code class="font-bold"><?= e($sementara['password']) ?></code></p><p class="mt-2 text-xs">Password tidak akan ditampilkan lagi setelah halaman ini ditutup atau dimuat ulang.</p></section><?php endif; ?>
<div class="mb-6 flex flex-wrap items-end justify-between gap-3"><div><h1 class="text-2xl font-extrabold text-slate-900">Kelola Pengguna</h1><p class="mt-1 text-sm text-slate-600">Kelola akses akun dan pantau status aktif/online. Isi jurnal siswa tidak ditampilkan di halaman ini.</p></div><a href="<?= e(url('admin/pengguna_form.php')) ?>" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Tambah Pengguna</a></div>
<form method="get" action="<?= e(url('admin/pengguna.php')) ?>" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-6">
  <div><label for="cari" class="mb-1 block text-xs font-semibold">Nama / username</label><input id="cari" name="cari" value="<?= e($cari) ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
  <div><label for="role" class="mb-1 block text-xs font-semibold">Peran</label><select id="role" name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Semua peran</option><?php foreach (['admin' => 'Admin', 'guru_bk' => 'Guru BK', 'siswa' => 'Siswa'] as $nilai => $label): ?><option value="<?= e($nilai) ?>" <?= $role === $nilai ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
  <div><label for="sekolah_id" class="mb-1 block text-xs font-semibold">Sekolah</label><select id="sekolah_id" name="sekolah_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Semua sekolah</option><?php foreach ($sekolah as $row): ?><option value="<?= (int) $row['id'] ?>" <?= $sekolah_id === (int) $row['id'] ? 'selected' : '' ?>><?= e($row['nama']) ?></option><?php endforeach; ?></select></div>
  <div><label for="status" class="mb-1 block text-xs font-semibold">Status akun</label><select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Semua status</option><option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option><option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option></select></div>
  <div><label for="online" class="mb-1 block text-xs font-semibold">Aktivitas</label><select id="online" name="online" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Semua</option><option value="1" <?= $online === '1' ? 'selected' : '' ?>>Sedang Online</option><option value="0" <?= $online === '0' ? 'selected' : '' ?>>Offline</option></select></div>
  <div class="flex items-end gap-2"><button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Terapkan</button><a href="<?= e(url('admin/pengguna.php')) ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Reset</a></div>
</form>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-4">Nama / Username</th><th class="p-4">Peran</th><th class="p-4">Sekolah</th><th class="p-4">Status Akun</th><th class="p-4">Terakhir Login</th><th class="p-4">Aksi</th></tr></thead><tbody>
<?php if (!$pengguna): ?>
  <tr><td colspan="6" class="p-8 text-center text-slate-500">Tidak ada pengguna yang cocok.</td></tr>
<?php else: foreach ($pengguna as $baris): ?>
  <tr class="border-t border-slate-100">
    <td class="p-4">
      <div class="flex items-center gap-2">
        <div class="font-semibold"><?= e($baris['nama_lengkap']) ?></div>
        <?php if (!empty($baris['sedang_online'])): ?>
          <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800" title="Aktif dalam 5 menit terakhir">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>Online
          </span>
        <?php else: ?>
          <span class="inline-flex items-center gap-1 text-[11px] text-slate-400" title="Offline">
            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>Offline
          </span>
        <?php endif; ?>
      </div>
      <div class="text-xs text-slate-500">@<?= e($baris['username']) ?></div>
    </td>
    <td class="p-4"><?= e($baris['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($baris['role'])) ?></td>
    <td class="p-4"><?= e($baris['sekolah_nama'] ?: 'Global') ?></td>
    <td class="p-4">
      <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $baris['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
        <?= e(ucfirst($baris['status'])) ?>
      </span>
    </td>
    <td class="whitespace-nowrap p-4"><?= e(tanggal_id($baris['last_login_at'], true)) ?></td>
    <td class="p-4">
      <div class="flex flex-wrap gap-x-3 gap-y-2">
        <a class="font-semibold text-blue-700 hover:underline" href="<?= e(url('admin/pengguna_form.php?id=' . (int) $baris['id'])) ?>">Ubah</a>
        <form method="post" class="inline" onsubmit="return confirm('Buat password sementara baru untuk akun ini?')">
          <?= csrf_field() ?>
          <input type="hidden" name="aksi" value="reset">
          <input type="hidden" name="id" value="<?= (int) $baris['id'] ?>">
          <button class="font-semibold text-amber-700 hover:underline">Reset password</button>
        </form>
        <?php if ((int) $baris['id'] !== $admin_id): ?>
          <form method="post" class="inline" onsubmit="return confirm('<?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> akun ini?')">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="status">
            <input type="hidden" name="id" value="<?= (int) $baris['id'] ?>">
            <input type="hidden" name="status" value="<?= $baris['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>">
            <button class="font-semibold <?= $baris['status'] === 'aktif' ? 'text-rose-700' : 'text-emerald-700' ?> hover:underline">
              <?= $baris['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
            </button>
          </form>
        <?php endif; ?>
      </div>
    </td>
  </tr>
<?php endforeach; endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
