<?php
define('KARVIA_APP', true);
$peran_halaman = 'admin';
$halaman_aktif = 'pengguna';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$judul_halaman = $id ? 'Ubah Pengguna' : 'Tambah Pengguna';
require_once __DIR__ . '/../includes/header.php';

$pdo = db();
$sekolah_awal = filter_input(INPUT_GET, 'sekolah_id', FILTER_VALIDATE_INT) ?: '';
$data = ['nama_lengkap' => '', 'username' => '', 'role' => 'siswa', 'sekolah_id' => $sekolah_awal, 'kelas' => '', 'jurusan' => '', 'nisn' => '', 'nip' => '', 'no_telepon' => ''];
if (!$id && $sekolah_awal) {
    $cek = $pdo->prepare("SELECT COUNT(*) FROM sekolah WHERE id = :id AND status = 'aktif'");
    $cek->execute(['id' => $sekolah_awal]);
    if (!(int) $cek->fetchColumn()) {
        http_response_code(404);
        exit('Sekolah tidak ditemukan atau sedang nonaktif.');
    }
}
if ($id) {
    $stmt = $pdo->prepare('SELECT id, nama_lengkap, username, role, sekolah_id, kelas, jurusan, nisn, nip, no_telepon FROM pengguna WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $data = $stmt->fetch();
    if (!$data) {
        http_response_code(404);
        exit('Pengguna tidak ditemukan.');
    }
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    if ($id_post !== $id) {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
    $data = [
        'nama_lengkap' => trim((string) ($_POST['nama_lengkap'] ?? '')),
        'username' => strtolower(trim((string) ($_POST['username'] ?? ''))),
        'role' => $id ? $data['role'] : (string) ($_POST['role'] ?? ''),
        'sekolah_id' => (string) ($_POST['sekolah_id'] ?? ''),
        'kelas' => trim((string) ($_POST['kelas'] ?? '')),
        'jurusan' => trim((string) ($_POST['jurusan'] ?? '')),
        'nisn' => trim((string) ($_POST['nisn'] ?? '')),
        'nip' => trim((string) ($_POST['nip'] ?? '')),
        'no_telepon' => trim((string) ($_POST['no_telepon'] ?? '')),
    ];
    $sekolah_id = filter_var($data['sekolah_id'], FILTER_VALIDATE_INT) ?: null;
    if (mb_strlen($data['nama_lengkap']) < 2 || mb_strlen($data['nama_lengkap']) > 150) $errors[] = 'Nama lengkap wajib diisi (2–150 karakter).';
    if (!username_valid($data['username'])) $errors[] = 'Username harus 3–50 karakter: huruf kecil, angka, titik, garis bawah, atau tanda hubung.';
    if (!in_array($data['role'], ['admin', 'guru_bk', 'siswa'], true)) $errors[] = 'Peran tidak valid.';
    if ($data['role'] !== 'admin' && !$sekolah_id) $errors[] = 'Pilih sekolah untuk Guru BK atau Siswa.';
    if ($id && $data['role'] !== 'admin' && !$errors) {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM sekolah WHERE id = :id AND status = 'aktif'");
        $cek->execute(['id' => $sekolah_id]);
        if (!(int) $cek->fetchColumn()) $errors[] = 'Pilih sekolah aktif yang tersedia.';
    }
    foreach (['kelas' => 20, 'jurusan' => 50, 'nisn' => 20, 'nip' => 30, 'no_telepon' => 20] as $kolom => $maksimum) {
        if (mb_strlen($data[$kolom]) > $maksimum) $errors[] = ucfirst(str_replace('_', ' ', $kolom)) . " maksimal {$maksimum} karakter.";
    }
    if ($data['role'] === 'admin') {
        $sekolah_id = null;
        $data['kelas'] = $data['jurusan'] = $data['nisn'] = $data['nip'] = '';
    } elseif (!$id && !$errors) {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM sekolah WHERE id = :id AND status = 'aktif'");
        $cek->execute(['id' => $sekolah_id]);
        if (!(int) $cek->fetchColumn()) $errors[] = 'Sekolah tidak ditemukan atau sedang nonaktif.';
    }
    if ($data['role'] !== 'siswa') {
        $data['kelas'] = $data['jurusan'] = $data['nisn'] = '';
    }
    if ($data['role'] !== 'guru_bk') {
        $data['nip'] = '';
    }
    if (!$id) {
        $password = (string) ($_POST['password'] ?? '');
        $konfirmasi = (string) ($_POST['konfirmasi_password'] ?? '');
        if (strlen($password) < 8 || strlen($password) > 72) $errors[] = 'Password awal harus 8–72 karakter.';
        if (!hash_equals($password, $konfirmasi)) $errors[] = 'Konfirmasi password tidak sama.';
    }
    if (!$errors) {
        $cek = $pdo->prepare('SELECT COUNT(*) FROM pengguna WHERE username = :username AND id <> :id');
        $cek->execute(['username' => $data['username'], 'id' => $id]);
        if ((int) $cek->fetchColumn()) $errors[] = 'Username sudah digunakan. Pilih username lain.';
    }
    if (!$errors) {
        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE pengguna SET nama_lengkap = :nama, username = :username, sekolah_id = :sekolah_id, kelas = :kelas, jurusan = :jurusan, nisn = :nisn, nip = :nip, no_telepon = :telepon, diperbarui_pada = NOW() WHERE id = :id');
                $stmt->execute(['nama' => $data['nama_lengkap'], 'username' => $data['username'], 'sekolah_id' => $data['role'] === 'admin' ? null : $sekolah_id, 'kelas' => $data['kelas'] ?: null, 'jurusan' => $data['jurusan'] ?: null, 'nisn' => $data['nisn'] ?: null, 'nip' => $data['nip'] ?: null, 'telepon' => $data['no_telepon'] ?: null, 'id' => $id]);
                log_aktivitas('ubah_pengguna', 'Data akun diperbarui oleh Admin.');
                flash_set('sukses', 'Data pengguna berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO pengguna (sekolah_id, nama_lengkap, username, password_hash, role, status, nisn, nip, kelas, jurusan, no_telepon, dibuat_oleh, dibuat_pada) VALUES (:sekolah_id, :nama, :username, :password, :role, \'aktif\', :nisn, :nip, :kelas, :jurusan, :telepon, :admin_id, NOW())');
                $stmt->execute(['sekolah_id' => $data['role'] === 'admin' ? null : $sekolah_id, 'nama' => $data['nama_lengkap'], 'username' => $data['username'], 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => $data['role'], 'nisn' => $data['role'] === 'siswa' ? ($data['nisn'] ?: null) : null, 'nip' => $data['role'] === 'guru_bk' ? ($data['nip'] ?: null) : null, 'kelas' => $data['role'] === 'siswa' ? ($data['kelas'] ?: null) : null, 'jurusan' => $data['role'] === 'siswa' ? ($data['jurusan'] ?: null) : null, 'telepon' => $data['no_telepon'] ?: null, 'admin_id' => (int) auth_pengguna()['id']]);
                log_aktivitas('buat_pengguna', 'Admin membuat akun baru.');
                flash_set('sukses', 'Akun baru berhasil dibuat.');
            }
            redirect('admin/pengguna.php');
        } catch (PDOException $kesalahan) {
            error_log('KARVIA: penyimpanan pengguna gagal: ' . $kesalahan->getMessage());
            $errors[] = 'Akun tidak dapat disimpan. Periksa username dan sekolah.';
        }
    }
}
$sekolah = $pdo->query("SELECT id, nama FROM sekolah WHERE status = 'aktif' ORDER BY nama")->fetchAll();
?>
<div class="flex flex-col gap-1">
  <a href="<?= e(url('admin/pengguna.php')) ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#245938] hover:underline">
    &larr; Kembali ke Kelola Pengguna
  </a>
  <h2 class="mt-2 text-xl font-bold tracking-tight text-[#203628]"><?= e($judul_halaman) ?></h2>
  <p class="text-xs text-slate-500">Peran akun tidak dapat diubah setelah dibuat demi menjaga integritas data riwayat.</p>
</div>

<?php if ($errors): ?>
  <div class="max-w-3xl rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs text-rose-900 shadow-sm">
    <div class="font-bold mb-1">Terdapat kesalahan pengisian:</div>
    <ul class="list-inside list-disc space-y-0.5">
      <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="<?= e(url('admin/pengguna_form.php' . ($id ? '?id=' . $id : ''))) ?>" class="max-w-3xl space-y-5 rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int) $id ?>">

  <div>
    <label for="nama_lengkap" class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap *</label>
    <input id="nama_lengkap" name="nama_lengkap" required maxlength="150" value="<?= e($data['nama_lengkap']) ?>" placeholder="Contoh: Budi Prasetyo" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
  </div>

  <div class="grid gap-4 sm:grid-cols-2">
    <div>
      <label for="username" class="mb-1 block text-xs font-bold text-slate-700">Username *</label>
      <input id="username" name="username" required minlength="3" maxlength="50" pattern="[a-z0-9._-]+" value="<?= e($data['username']) ?>" placeholder="huruf kecil, angka, titik, strip" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
    </div>
    <div>
      <label for="role" class="mb-1 block text-xs font-bold text-slate-700">Peran Akun *</label>
      <select id="role" name="role" <?= $id ? 'disabled' : '' ?> class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="guru_bk" <?= $data['role'] === 'guru_bk' ? 'selected' : '' ?>>Guru BK</option>
        <option value="siswa" <?= $data['role'] === 'siswa' ? 'selected' : '' ?>>Siswa</option>
      </select>
      <?php if ($id): ?>
        <input type="hidden" name="role" value="<?= e($data['role']) ?>">
      <?php endif; ?>
    </div>
  </div>

  <div>
    <label for="sekolah_id" class="mb-1 block text-xs font-bold text-slate-700">
      Sekolah <span id="sekolah-wajib" class="text-rose-600">*</span>
    </label>
    <select id="sekolah_id" name="sekolah_id" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
      <option value="">— Tidak ada (Admin Global) —</option>
      <?php foreach ($sekolah as $row): ?>
        <option value="<?= (int) $row['id'] ?>" <?= (string) $data['sekolah_id'] === (string) $row['id'] ? 'selected' : '' ?>>
          <?= e($row['nama']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php if (!$id): ?>
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label for="password" class="mb-1 block text-xs font-bold text-slate-700">Password Awal *</label>
        <input id="password" name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
      </div>
      <div>
        <label for="konfirmasi_password" class="mb-1 block text-xs font-bold text-slate-700">Konfirmasi Password *</label>
        <input id="konfirmasi_password" name="konfirmasi_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
      </div>
    </div>
  <?php endif; ?>

  <fieldset class="rounded-xl border border-[#eef2ed] bg-[#fcfdfc] p-4">
    <legend class="px-2 text-xs font-bold text-slate-600">Data Tambahan (Opsional)</legend>
    <div class="grid gap-3 sm:grid-cols-2">
      <div>
        <label for="kelas" class="mb-1 block text-xs font-semibold text-slate-600">Kelas (Khusus Siswa)</label>
        <input id="kelas" name="kelas" maxlength="20" value="<?= e($data['kelas']) ?>" placeholder="Misal: XII IPA 1" class="w-full rounded-xl border border-[#dfe6de] bg-white px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:outline-none">
      </div>
      <div>
        <label for="jurusan" class="mb-1 block text-xs font-semibold text-slate-600">Jurusan (Khusus Siswa)</label>
        <input id="jurusan" name="jurusan" maxlength="50" value="<?= e($data['jurusan']) ?>" placeholder="Misal: MIPA" class="w-full rounded-xl border border-[#dfe6de] bg-white px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:outline-none">
      </div>
      <div>
        <label for="nisn" class="mb-1 block text-xs font-semibold text-slate-600">NISN (Khusus Siswa)</label>
        <input id="nisn" name="nisn" maxlength="20" value="<?= e($data['nisn']) ?>" class="w-full rounded-xl border border-[#dfe6de] bg-white px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:outline-none">
      </div>
      <div>
        <label for="nip" class="mb-1 block text-xs font-semibold text-slate-600">NIP (Khusus Guru BK)</label>
        <input id="nip" name="nip" maxlength="30" value="<?= e($data['nip']) ?>" class="w-full rounded-xl border border-[#dfe6de] bg-white px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:outline-none">
      </div>
      <div class="sm:col-span-2">
        <label for="no_telepon" class="mb-1 block text-xs font-semibold text-slate-600">Nomor Telepon / WhatsApp</label>
        <input id="no_telepon" name="no_telepon" maxlength="20" value="<?= e($data['no_telepon']) ?>" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-[#dfe6de] bg-white px-3 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:outline-none">
      </div>
    </div>
  </fieldset>

  <div class="flex flex-wrap gap-2.5 pt-2 border-t border-[#eef2ed]">
    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-[#1a4029]">
      Simpan Akun
    </button>
    <a href="<?= e(url('admin/pengguna.php')) ?>" class="inline-flex items-center justify-center rounded-xl border border-[#dfe6de] bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-[#f6f8f5]">
      Batal
    </a>
  </div>
</form>
<script>
(() => {
  const role = document.getElementById('role');
  const sekolah = document.getElementById('sekolah_id');
  const wajib = document.getElementById('sekolah-wajib');
  const sync = () => {
    const admin = role.value === 'admin';
    sekolah.disabled = admin;
    sekolah.required = !admin;
    wajib.hidden = admin;
  };
  if (role && sekolah) { role.addEventListener('change', sync); sync(); }
})();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
