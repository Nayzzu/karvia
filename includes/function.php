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
            'UPDATE pengguna SET last_login_at = NOW(), terakhir_aktif_pada = NOW(), login_count = login_count + 1 WHERE id = :id'
        );
        $update->execute(['id' => $id]);
        $_SESSION['_terakhir_ping_db'] = time();
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
        $id_keluar = (int) auth_pengguna()['id'];
        try {
            db()->prepare('UPDATE pengguna SET terakhir_aktif_pada = NULL WHERE id = :id')->execute(['id' => $id_keluar]);
        } catch (PDOException) {
            // abaikan
        }
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

    // Perbarui heartbeat online di database berkala (maksimal sekali per 60 detik)
    $sekarang = time();
    if (!isset($_SESSION['_terakhir_ping_db']) || ($sekarang - (int) $_SESSION['_terakhir_ping_db'] >= 60)) {
        $_SESSION['_terakhir_ping_db'] = $sekarang;
        try {
            db()->prepare('UPDATE pengguna SET terakhir_aktif_pada = NOW() WHERE id = :id')->execute(['id' => (int) $akun['id']]);
        } catch (PDOException $kesalahan) {
            error_log('KARVIA: gagal memperbarui aktivitas online: ' . $kesalahan->getMessage());
        }
    }

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

/** Render SVG icon standar aplikasi. */
function karvia_icon(string $name, string $class = 'h-5 w-5'): void
{
    static $icons = [
        'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/><path d="M3 10h18"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'school' => '<path d="m3 10 9-5 9 5-9 5z"/><path d="M7 12v5c3 2 7 2 10 0v-5M21 10v6"/>',
        'book' => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 1 4 17.5z"/><path d="M4 17.5A2.5 2.5 0 0 1 6.5 15H20M8 7h8M8 10h8"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>',
        'chart' => '<path d="M4 19V5M4 19h17"/><path d="m7 15 4-4 3 2 5-6"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
        'logout' => '<path d="M13 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8"/><path d="M13 12h8m-4-4 4 4-4 4"/><path d="M13 4v16"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.7 1l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.7-1l-1.7.7-1.4-2.4L7.3 15a8 8 0 0 1 0-2l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.7-1l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.7 1l1.7-.7 1.4 2.4-1.4 1.1a8 8 0 0 1-.1 2z"/>',
        'compass' => '<circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>',
        'map' => '<polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/>',
        'message' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'lightbulb' => '<path d="M9 18h6M10 22h4M15 13a6 6 0 1 0-6 0c0 2 1.5 3 2 4h2c.5-1 2-2 2-4z"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'plus' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'check' => '<polyline points="20 6 9 17 4 12"/>',
        'arrow-right' => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        'arrow-down' => '<line x1="12" y1="5" x2="12" y2="19"/><polyline points="5 12 12 19 19 12"/>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2M2 13h20"/>',
        'scale' => '<path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"/><path d="M7 21h10M12 3v18M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>',
        'lock' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1L12 21l7.7-7.6 1.1-1a5.5 5.5 0 0 0 0-7.8z"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    ];
    $svg = $icons[$name] ?? $icons['file'];
    echo '<svg aria-hidden="true" class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $svg . '</svg>';
}
