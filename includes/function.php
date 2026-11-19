<?php
defined('KARVIA_APP') or exit('Akses langsung tidak diizinkan.');

require_once __DIR__ . '/../config/koneksi.php';

// Cookie session dibuat hanya dapat diakses server dan tidak dikirim lintas situs.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(NAMA_SESSION);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', (string) BATAS_SESSION_DETIK);
    session_start();
}

/** Koneksi PDO tunggal untuk semua halaman. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAMA . ';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("SET time_zone = '+07:00'");
    } catch (PDOException $kesalahan) {
        error_log('KARVIA: koneksi database gagal: ' . $kesalahan->getMessage());
        if (MODE_DEBUG) {
            throw $kesalahan;
        }
        http_response_code(500);
        exit('Aplikasi sedang mengalami kendala. Silakan coba kembali nanti.');
    }

    return $pdo;
}

/** Escape teks sebelum ditampilkan di HTML. */
function e($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Buat alamat lokal berdasarkan BASE_URL. */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/** Redirect ke halaman lokal aplikasi. */
function redirect(string $path): never
{
    if (preg_match('/^https?:\/\//i', $path)) {
        $path = 'index.php';
    }
    if ($path === 'login.php') {
        $flash = $_SESSION['_flash'] ?? null;
        session_write_close();
        session_id('');
        session_start();
        if (is_array($flash)) {
            $_SESSION['_flash'] = $flash;
        }
    }
    header('Location: ' . url($path));
    exit;
}

function flash_set(string $tipe, string $pesan): void
{
    $_SESSION['_flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function flash_ambil(): ?array
{
    $flash = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return is_array($flash) ? $flash : null;
}

function flash_tampil(): void
{
    $flash = flash_ambil();
    if (!$flash) {
        return;
    }
    $gaya = match ($flash['tipe'] ?? '') {
        'sukses' => 'bg-emerald-50 border-emerald-300 text-emerald-900',
        'peringatan' => 'bg-amber-50 border-amber-300 text-amber-900',
        default => 'bg-rose-50 border-rose-300 text-rose-900',
    };
    echo '<div class="mb-5 rounded-xl border p-4 text-sm ' . $gaya . '" role="status">' . e($flash['pesan'] ?? '') . '</div>';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_cek(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return is_string($token) && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function csrf_wajib(): void
{
    if (!csrf_cek()) {
        http_response_code(403);
        exit('Permintaan tidak dapat diproses. Muat ulang halaman lalu coba lagi.');
    }
}

function auth_pengguna(): ?array
{
    return isset($_SESSION['pengguna']) && is_array($_SESSION['pengguna'])
        ? $_SESSION['pengguna']
        : null;
}

function auth_sekolah_id(): ?int
{
    $pengguna = auth_pengguna();
    return isset($pengguna['sekolah_id']) ? (int) $pengguna['sekolah_id'] : null;
}

/** Login memakai username dan hash password yang tersimpan di database. */
function auth_login(string $username, string $password): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return ['berhasil' => false, 'pesan' => 'Sesi tidak tersedia. Muat ulang halaman lalu coba kembali.'];
    }

    $stmt = db()->prepare(
        'SELECT p.id, p.sekolah_id, p.nama_lengkap, p.username, p.password_hash,
                p.role, p.status, s.nama AS sekolah_nama
         FROM pengguna p
         LEFT JOIN sekolah s ON s.id = p.sekolah_id
         WHERE p.username = :username
         LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $akun = $stmt->fetch();

    if (!$akun) {
        password_verify($password, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG');
        return ['berhasil' => false, 'pesan' => 'Username atau password salah.'];
    }
    if (!password_verify($password, $akun['password_hash'])) {
        return ['berhasil' => false, 'pesan' => 'Username atau password salah.'];
    }
    if ($akun['status'] !== 'aktif') {
        return ['berhasil' => false, 'pesan' => 'Akun Anda dinonaktifkan. Hubungi Admin.'];
    }

    session_regenerate_id(true);
    unset($akun['password_hash'], $akun['status']);
    $_SESSION['pengguna'] = $akun;
    $_SESSION['_terakhir_aktif'] = time();
    unset($_SESSION['_csrf']);

    $id = (int) $akun['id'];
    try {
        $update = db()->prepare(
            'UPDATE pengguna SET last_login_at = NOW(), login_count = login_count + 1 WHERE id = :id'
        );
        $update->execute(['id' => $id]);
        log_aktivitas('login', 'Pengguna berhasil masuk.');
    } catch (PDOException $kesalahan) {
        error_log('KARVIA: gagal memperbarui aktivitas login: ' . $kesalahan->getMessage());
    }

    return ['berhasil' => true, 'pesan' => ''];
}

function auth_logout(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    if (auth_pengguna()) {
        log_aktivitas('logout', 'Pengguna keluar.');
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $parameter = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $parameter['path'],
            'domain' => $parameter['domain'],
            'secure' => $parameter['secure'],
            'httponly' => $parameter['httponly'],
            'samesite' => 'Lax',
        ]);
    }
    session_destroy();
}

/** Muat ulang hak akses dari database untuk menolak akun yang dinonaktifkan. */
function auth_muat_ulang(): ?array
{
    $sesi = auth_pengguna();
    if (!$sesi) {
        return null;
    }

    if (!isset($_SESSION['_terakhir_aktif'])) {
        $_SESSION['_terakhir_aktif'] = time();
    }
    if (time() - (int) $_SESSION['_terakhir_aktif'] > BATAS_SESSION_DETIK) {
        auth_logout();
        return null;
    }

    $stmt = db()->prepare(
        'SELECT p.id, p.sekolah_id, p.nama_lengkap, p.username, p.role, p.status,
                s.nama AS sekolah_nama
         FROM pengguna p
         LEFT JOIN sekolah s ON s.id = p.sekolah_id
         WHERE p.id = :id LIMIT 1'
    );
    $stmt->execute(['id' => (int) $sesi['id']]);
    $akun = $stmt->fetch();
    if (!$akun || $akun['status'] !== 'aktif') {
        auth_logout();
        return null;
    }

    unset($akun['status']);
    $_SESSION['pengguna'] = $akun;
    $_SESSION['_terakhir_aktif'] = time();
    return $akun;
}

function auth_cek_peran(string $peran): void
{
    $akun = auth_muat_ulang();
    if (!$akun) {
        flash_set('peringatan', 'Silakan masuk untuk melanjutkan.');
        redirect('login.php');
    }
    if ($akun['role'] !== $peran) {
        http_response_code(403);
        exit('Anda tidak memiliki akses ke halaman ini.');
    }
}

function auth_arahkan_dashboard(array $akun): never
{
    $tujuan = match ($akun['role'] ?? '') {
        'admin' => 'admin/index.php',
        'guru_bk' => 'guru_bk/index.php',
        'siswa' => 'siswa/index.php',
        default => 'login.php',
    };
    redirect($tujuan);
}

/** Catat metadata aktivitas tanpa password, token, atau isi jurnal. */
function log_aktivitas(string $aksi, ?string $keterangan = null): void
{
    try {
        $akun = auth_pengguna();
        $stmt = db()->prepare(
            'INSERT INTO log_aktivitas (pengguna_id, aksi, keterangan, ip_address, dibuat_pada)
             VALUES (:pengguna_id, :aksi, :keterangan, :ip_address, NOW())'
        );
        $stmt->execute([
            'pengguna_id' => $akun ? (int) $akun['id'] : null,
            'aksi' => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (PDOException $kesalahan) {
        error_log('KARVIA: gagal mencatat aktivitas: ' . $kesalahan->getMessage());
    } catch (Throwable $kesalahan) {
        error_log('KARVIA: gagal mencatat aktivitas: ' . $kesalahan->getMessage());
    }
}

function tanggal_id(?string $tanggal, bool $dengan_waktu = false): string
{
    if (!$tanggal) {
        return 'Belum pernah';
    }
    $waktu = strtotime($tanggal);
    if ($waktu === false) {
        return '—';
    }
    return date($dengan_waktu ? 'd-m-Y H:i' : 'd-m-Y', $waktu);
}
