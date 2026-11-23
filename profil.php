<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';
$pengguna = auth_muat_ulang();
if (!$pengguna) {
    flash_set('peringatan', 'Silakan masuk untuk melanjutkan.');
    redirect('login.php');
}
$peran_halaman = $pengguna['role'];
$halaman_aktif = 'profil';
$judul_halaman = 'Profil & Ganti Password';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$id = (int) $pengguna['id'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $aksi = (string) ($_POST['aksi'] ?? '');
    if ($aksi === 'profil') {
        $nama = trim((string) ($_POST['nama_lengkap'] ?? ''));
        $telepon = trim((string) ($_POST['no_telepon'] ?? ''));
        if (mb_strlen($nama) < 2 || mb_strlen($nama) > 150) $errors[] = 'Nama wajib diisi (2–150 karakter).';
        if (mb_strlen($telepon) > 20) $errors[] = 'Nomor telepon maksimal 20 karakter.';
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE pengguna SET nama_lengkap = :nama, no_telepon = :telepon, diperbarui_pada = NOW() WHERE id = :id');
            $stmt->execute(['nama' => $nama, 'telepon' => $telepon ?: null, 'id' => $id]);
            $_SESSION['pengguna']['nama_lengkap'] = $nama;
            flash_set('sukses', 'Profil berhasil diperbarui.');
            redirect('profil.php');
        }
    } elseif ($aksi === 'username') {
        $username = strtolower(trim((string) ($_POST['username'] ?? '')));
        $password = (string) ($_POST['password_verifikasi'] ?? '');
        if (!username_valid($username)) $errors[] = 'Username harus 3–50 karakter: huruf kecil, angka, titik, garis bawah, atau tanda hubung.';
        $stmt = $pdo->prepare('SELECT password_hash FROM pengguna WHERE id = :id AND status = \'aktif\'');
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();
        if (!$hash || !password_verify($password, $hash)) $errors[] = 'Password saat ini tidak sesuai.';
        if (!$errors) {
            try {
                $stmt = $pdo->prepare('UPDATE pengguna SET username = :username, diperbarui_pada = NOW() WHERE id = :id');
                $stmt->execute(['username' => $username, 'id' => $id]);
                $_SESSION['pengguna']['username'] = $username;
                log_aktivitas('ubah_username', 'Pengguna memperbarui username mandiri.');
                flash_set('sukses', 'Username berhasil diperbarui.');
                redirect('profil.php');
            } catch (PDOException $kesalahan) {
                if ($kesalahan->getCode() === '23000') $errors[] = 'Username sudah digunakan. Pilih username lain.';
                else throw $kesalahan;
            }
        }
    } elseif ($aksi === 'password') {
        $lama = (string) ($_POST['password_lama'] ?? '');
        $baru = (string) ($_POST['password_baru'] ?? '');
        $konfirmasi = (string) ($_POST['konfirmasi_password'] ?? '');
        $stmt = $pdo->prepare('SELECT password_hash FROM pengguna WHERE id = :id AND status = \'aktif\'');
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();
        if (!$hash || !password_verify($lama, $hash)) $errors[] = 'Password saat ini tidak sesuai.';
        if (strlen($baru) < 8 || strlen($baru) > 72) $errors[] = 'Password baru harus 8–72 karakter.';
        if (!hash_equals($baru, $konfirmasi)) $errors[] = 'Konfirmasi password tidak sama.';
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE pengguna SET password_hash = :hash, diperbarui_pada = NOW() WHERE id = :id');
            $stmt->execute(['hash' => password_hash($baru, PASSWORD_DEFAULT), 'id' => $id]);
            flash_set('sukses', 'Password berhasil diganti. Gunakan password baru saat masuk berikutnya.');
            redirect('profil.php');
        }
    } else {
        http_response_code(400);
        exit('Permintaan tidak dapat diproses.');
    }
}
$stmt = $pdo->prepare('SELECT nama_lengkap, username, role, sekolah_id, kelas, no_telepon FROM pengguna WHERE id = :id');
$stmt->execute(['id' => $id]);
$profil = $stmt->fetch();
?>
<div class="flex flex-col gap-1">
  <h2 class="text-xl font-bold tracking-tight text-[#203628]">Profil Akun &amp; Kata Sandi</h2>
  <p class="text-xs text-slate-500">Perbarui profil dan kredensial akun Anda. Data peran dan sekolah dikelola oleh sistem/Admin.</p>
</div>

<?php if ($errors): ?>
  <div role="alert" class="max-w-4xl rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs text-rose-900 shadow-sm">
    <div class="font-bold mb-1">Periksa kembali:</div>
    <ul class="list-inside list-disc space-y-0.5">
      <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="grid max-w-4xl gap-6 lg:grid-cols-2">
  <!-- Kartu Data Profil -->
  <section class="flex flex-col justify-between rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm">
    <div>
      <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
          <?php karvia_icon('users', 'h-4 w-4'); ?>
        </span>
        <div>
          <h3 class="text-base font-bold text-[#203628]">Informasi Akun</h3>
          <p class="text-[11px] text-slate-500">Detail identitas Anda dalam sistem</p>
        </div>
      </div>

      <dl class="grid gap-3 text-xs sm:grid-cols-2">
        <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
          <dt class="font-semibold text-slate-500">Username</dt>
          <dd class="mt-0.5 font-bold text-[#203628]">@<?= e($profil['username']) ?></dd>
        </div>
        <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3">
          <dt class="font-semibold text-slate-500">Peran</dt>
          <dd class="mt-0.5 font-bold text-[#203628]"><?= e($profil['role'] === 'guru_bk' ? 'Guru BK' : ucfirst($profil['role'])) ?></dd>
        </div>
        <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3 sm:col-span-2">
          <dt class="font-semibold text-slate-500">Sekolah</dt>
          <dd class="mt-0.5 font-bold text-[#203628]"><?= e($pengguna['sekolah_nama'] ?? 'Admin Global') ?></dd>
        </div>
        <?php if ($profil['role'] === 'siswa'): ?>
          <div class="rounded-xl border border-[#eef2ed] bg-[#fbfdfb] p-3 sm:col-span-2">
            <dt class="font-semibold text-slate-500">Kelas Terdaftar</dt>
            <dd class="mt-0.5 font-bold text-[#203628]"><?= e($profil['kelas'] ?: '—') ?></dd>
          </div>
        <?php endif; ?>
      </dl>

      <form method="post" class="mt-5 space-y-4 border-t border-[#eef2ed] pt-4">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="profil">
        <div>
          <label for="nama_lengkap" class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap *</label>
          <input id="nama_lengkap" name="nama_lengkap" required maxlength="150" value="<?= e($profil['nama_lengkap']) ?>" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        </div>
        <div>
          <label for="no_telepon" class="mb-1 block text-xs font-bold text-slate-700">Nomor Telepon / WhatsApp</label>
          <input id="no_telepon" name="no_telepon" maxlength="20" value="<?= e($profil['no_telepon']) ?>" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        </div>
        <div class="pt-2">
          <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#1a4029]">
            Simpan Perubahan
          </button>
        </div>
      </form>
      <form method="post" class="mt-5 space-y-4 border-t border-[#eef2ed] pt-4">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="username">
        <div>
          <label for="username_baru" class="mb-1 block text-xs font-bold text-slate-700">Ubah Username</label>
          <input id="username_baru" name="username" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]{3,50}" value="<?= e($profil['username']) ?>" autocomplete="username" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
          <p class="mt-1 text-[11px] text-slate-500">3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.</p>
        </div>
        <div>
          <label for="password_verifikasi" class="mb-1 block text-xs font-bold text-slate-700">Password Saat Ini *</label>
          <input id="password_verifikasi" name="password_verifikasi" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        </div>
        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#224f35] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#1a4029]">Simpan Username</button>
      </form>
    </div>
  </section>

  <!-- Kartu Ganti Password -->
  <section class="flex flex-col justify-between rounded-2xl border border-[#dfe6de] bg-white p-6 shadow-sm">
    <div>
      <div class="flex items-center gap-3 border-b border-[#eef2ed] pb-3 mb-4">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e8f0e9] text-[#245938]">
          <?php karvia_icon('settings', 'h-4 w-4'); ?>
        </span>
        <div>
          <h3 class="text-base font-bold text-[#203628]">Keamanan Sandi</h3>
          <p class="text-[11px] text-slate-500">Perbarui kata sandi akun secara berkala</p>
        </div>
      </div>

      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="password">
        <div>
          <label for="password_lama" class="mb-1 block text-xs font-bold text-slate-700">Password Saat Ini *</label>
          <input id="password_lama" name="password_lama" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        </div>
        <div>
          <label for="password_baru" class="mb-1 block text-xs font-bold text-slate-700">Password Baru *</label>
          <input id="password_baru" name="password_baru" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
          <p class="mt-1 text-[11px] text-slate-500">Minimal 8 karakter.</p>
        </div>
        <div>
          <label for="konfirmasi_password" class="mb-1 block text-xs font-bold text-slate-700">Ulangi Password Baru *</label>
          <input id="konfirmasi_password" name="konfirmasi_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full rounded-xl border border-[#dfe6de] bg-[#fcfdfc] px-3.5 py-2 text-xs text-slate-800 focus:border-[#224f35] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#224f35]">
        </div>
        <div class="pt-2">
          <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#203628] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#122319]">
            Ganti Password
          </button>
        </div>
      </form>
    </div>
  </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
