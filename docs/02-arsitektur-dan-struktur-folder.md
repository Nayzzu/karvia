# 02 — Arsitektur & Struktur Folder

## 1. Gaya arsitektur

Aplikasi ini sengaja dibuat **sederhana**, bukan memakai framework:

- **PHP native prosedural** (fungsi-fungsi), bukan OOP/ORM yang rumit.
- **Satu file PHP = satu halaman.** Tidak ada front controller atau parameter
  `?page=`. Contoh: dashboard Admin ada di `admin/index.php`, daftar catatan
  Guru BK di `guru_bk/catatan.php`.
- **Folder per peran**: `admin/`, `guru_bk/`, `siswa/`. Halaman yang hanya boleh
  dibuka satu peran disimpan di folder peran tersebut.
- **Bagian berulang** (header, footer, sidebar, fungsi bantu) disimpan di
  `includes/` agar tidak diduplikasi.
- **PDO** untuk semua akses database.
- **Session PHP** untuk status login.
- **Server-side rendering**: form disubmit ke server, lalu tampilkan pesan hasil.
- **Post/Redirect/Get**: setelah POST yang mengubah data, redirect agar refresh
  tidak mengirim ulang.

Alasan: Cindy ingin **memahami cara kerjanya**. Satu file HTML statis tidak cukup,
tetapi framework besar juga akan menyulitkan perawatan dan pembelajaran. Pola
“satu halaman = satu file” paling mudah ditelusuri pemula.

---

## 2. Struktur folder (target akhir)

```text
karvia/                          ← root aplikasi (nama folder bebas)
│
├── admin/
│   └── index.php                ← dashboard Admin
│
├── assets/
│   ├── css/                     ← CSS tambahan (termasuk gaya cetak)
│   ├── js/                      ← JavaScript tambahan
│   └── images/                  ← logo/gambar
├── uploads/
│   └── artikel/                 ← gambar artikel tervalidasi, nama acak
│
├── config/
│   └── koneksi.php              ← konfigurasi aplikasi + koneksi PDO
│
├── database/
│   ├── database.sql             ← struktur tabel
│   ├── dummy_karvia.sql          ← data awal 2 sekolah (destructive)
│   └── dummy_karvia2.sql         ← tambahan 3 sekolah Bandar Lampung
│
├── guru_bk/
│   └── index.php                ← dashboard Guru BK
│
├── includes/
│   ├── header.php               ← pembuka HTML + sidebar + cek login/peran
│   ├── footer.php               ← penutup HTML + pemuatan JS
│   └── function.php             ← fungsi bantu (auth, CSRF, escape, flash, format)
│
├── siswa/
│   └── index.php                ← beranda Siswa
│
├── docs/                        ← dokumentasi, prototipe acuan, dan referensi desain
│
├── index.php                    ← landing page + artikel terbaru → login / dashboard
├── artikel.php                  ← baca artikel publik
├── login.php                    ← form + proses login
├── daftar.php                   ← pendaftaran mandiri khusus siswa
├── logout.php                   ← mengakhiri sesi
├── profil.php                   ← ubah profil dan kata sandi
└── AGENTS.md                    ← stub penunjuk ke docs/13
```

Dokumen, prototipe (`docs/karvia_career_vision_action.html`), dan referensi
desain (`docs/teal-treasury-landing/`) disimpan di `docs/`.

**Catatan lokasi.** Struktur ini **tidak terikat pada satu path**. Contoh
penataan bertingkat saat pengumpulan tugas:

```text
www/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18/
```

itu hanya **contoh penamaan folder bertingkat**; isi di dalamnya tetap sama
seperti pohon di atas. Saat folder dipindah, yang perlu disesuaikan hanya
`BASE_URL` di `config/koneksi.php` (lihat bagian 10).

Folder `assets/css/`, `assets/js/`, dan `assets/images/` memiliki file `.gitkeep`
selama masih kosong. File itu hanya penanda agar folder tidak hilang saat proyek
di-zip; boleh diabaikan.

> **Tidak boleh diakses langsung lewat browser:** `config/`, `database/`,
> `includes/`, dan `docs/`. Perlindungannya dibahas di bagian 7.

---

## 3. Rencana isi file

Bagian ini merangkum struktur dan fungsi halaman yang berjalan saat ini. Untuk
status verifikasi dan batas QA tiap fitur, lihat checklist `docs/05`.

### 3.1 File di akar

| File | Fungsi |
|------|--------|
| `index.php` | Belum login → landing publik termasuk kartu artikel terbaru; sudah login → dashboard sesuai peran. |
| `artikel.php` | Artikel publik lengkap berdasarkan ID (`?id=`); isi teks di-escape, artikel hanya yang terbit ditampilkan. |
| `login.php` | Form dan proses login username/password, CSRF, pesan gagal, serta pengalihan sesuai peran. |
| `logout.php` | Menghapus session, menghancurkan cookie, kembali ke login |
| `profil.php` | Ubah profil dan kata sandi pengguna yang sedang masuk |
| `AGENTS.md` | Stub penunjuk konteks proyek untuk AI di `docs/13` |
| `.htaccess` | *(tambahan, dibuat saat fase keamanan)* melindungi folder sensitif dan mematikan daftar folder |

### 3.2 Halaman per peran

Dashboard tiap peran, artikel Guru BK/publik, dan alur utama sudah tersedia dengan autentikasi serta data database. Status fitur, batas QA, dan pekerjaan tersisa dicatat di `docs/05`; tabel berikut merangkum struktur aktual halaman.

| Folder | Rencana file | Keterangan |
|--------|--------------|------------|
| `admin/` | `index.php` | Dashboard + 4 kartu statistik + rekap per sekolah |
| | `sekolah.php` | Daftar sekolah + pencarian + aktif/nonaktif |
| | `sekolah_form.php` | Tambah/ubah sekolah |
| | `sekolah_detail.php` | Detail sekolah + jumlah siswa/guru + daftar akun |
| | `pengguna.php` | Daftar pengguna + filter |
| | `pengguna_form.php` | Tambah/ubah pengguna + reset password |
| | `rekap.php` | Rekap ringkas semua sekolah |
| `guru_bk/` | `index.php` | Dashboard Guru BK + catatan yang dibagikan |
| | `catatan.php` | Daftar catatan + filter status |
| | `catatan_detail.php` | Detail catatan + tindak lanjut + cetak |
| | `artikel.php` | Daftar artikel milik Guru BK + aksi ubah/hapus |
| | `artikel_form.php` | Tambah/ubah artikel + unggah gambar |
| `siswa/` | `index.php` | Beranda + progres lima langkah eksplorasi |
| | `kenali_diri.php` | Langkah 1 |
| | `profesi.php` | Langkah 2, katalog 15 foto, filter + bookmark |
| | `jalur.php` | Jalur setelah lulus (informasi statis) |
| | `bandingkan.php` | Langkah 3 |
| | `rencana.php` | Langkah 4 |
| | `jurnal.php` | Langkah 5 + riwayat |
| | `konsultasi.php` | Ruang Konsultasi + ringkasan + bagikan + status + cetak |
| | `tips.php` | Tips & edukasi + enam artikel terbaru dari DB |
| Akar (semua peran) | `profil.php` | Lihat/ubah nama dan telepon; ganti password dengan verifikasi password lama |

Aturan:

- Halaman baru **wajib** ada di folder peran yang tepat.
- Nama file memakai `snake_case` dan menggambarkan isinya.
- Jika nama file berubah dari rencana ini, perbarui dokumen ini dan `04`.
- Halaman yang bisa dipakai semua peran (mis. `profil.php`) diletakkan di akar,
  bukan diduplikasi di tiga folder.

### 3.3 Includes

| File | Fungsi |
|------|--------|
| `includes/function.php` | Memuat `config/koneksi.php`, memulai session, dan menyediakan fungsi bantu: koneksi `db()`, escape `e()`, `auth_*`, `csrf_*`, `flash_*`, validasi, format tanggal |
| `includes/header.php` | Memuat `function.php`, memeriksa login + peran halaman, memuat ulang status akun dari database, menampilkan `<head>`, header, sidebar sesuai peran, dan flash message |
| `includes/footer.php` | Menutup area konten, memuat `assets/js/app.js`, menutup tag HTML |

**Kontrak pemanggilan halaman** (contoh `guru_bk/catatan.php`):

```php
<?php
define('KARVIA_APP', true);          // penjaga akses langsung
$peran_halaman = 'guru_bk';          // header.php menolak peran lain
$halaman_aktif = 'catatan';          // penanda menu sidebar
require __DIR__ . '/../includes/header.php';

// ... ambil data & tampilkan konten ...

require __DIR__ . '/../includes/footer.php';
```

Halaman publik `login.php` dan `index.php` memakai `function.php` langsung,
tanpa `header.php`, karena tidak memerlukan sidebar peran.

### 3.4 Config

| File | Fungsi |
|------|--------|
| `config/koneksi.php` | Konstanta aplikasi (`BASE_URL`, zona waktu, nama session, mode debug) + kredensial database + fungsi koneksi PDO |

`config/koneksi.php` berisi kredensial lokal. Jangan dibagikan ke publik dan
jangan pernah menampilkan isinya di halaman. Nilai default Laragon: host
`localhost`, user `root`, password kosong, database `karvia_db`.

### 3.5 Database

| File | Fungsi |
|------|--------|
| `database/database.sql` | Struktur tabel, index, dan foreign key |
| `database/dummy_karvia.sql` | Data awal 2 sekolah; mengosongkan tabel dan hanya untuk setup demo baru |
| `database/dummy_karvia2.sql` | Tambahan 3 sekolah Bandar Lampung, akun demo, dan 1 artikel; non-destructive |
| `database/backup_YYYY-MM-DD.sql` | Hanya dibuat saat backup sebelum demo/perubahan besar |

Tidak ada folder `migrations/` atau `seeders/`. Struktur dan fixture demo dipisah
agar penambahan data tidak perlu mengosongkan database. Rincian tabel ada di
[`03-rencana-database.md`](03-rencana-database.md).

### 3.6 Assets

| File | Fungsi |
|------|--------|
| `assets/css/app.css` | Gaya tambahan: cetak, sidebar mobile, detail kecil |
| `assets/js/app.js` | Toggle sidebar, konfirmasi, tampil/sembunyi field |
| `assets/images/` | Logo KARVIA bila ada |

Halaman awal yang sudah dibuat memakai CDN seperti prototipe (Tailwind, FontAwesome,
Google Fonts Inter). Ini memerlukan internet saat halaman dibuka; jika koneksi sekolah
tidak stabil, aset dapat dipindahkan ke `assets/` pada tahap UI.

---

## 4. Alur satu request

Contoh: Guru BK membuka daftar catatan.

```text
1. Browser        →  /karvia/guru_bk/catatan.php
2. catatan.php    →  define('KARVIA_APP', true)
                     $peran_halaman = 'guru_bk'
                     require ../includes/header.php
3. header.php     →  require function.php
   function.php   →  require config/koneksi.php, session_start()
4. header.php     →  cek login, cek peran, muat ulang akun dari database
                     (akun nonaktif → keluar)
5. catatan.php    →  ambil data lewat fungsi (PDO prepared statement)
6. catatan.php    →  tampilkan tabel + form filter
7. footer.php     →  tutup halaman
8. Browser        →  menerima HTML jadi
```

Untuk form (mis. simpan tindak lanjut):

```text
POST ke file yang sama → validasi → CSRF → simpan → flash → redirect (GET)
```

JavaScript tidak menjadi tempat penyimpanan data penting; data aplikasi disimpan di database. JavaScript mendukung interaksi antarmuka, sedangkan pengiriman dan validasi utama tetap dilakukan server-side.

---

## 5. Peta URL

Tidak ada parameter `page`. URL mengikuti nama file.

| Halaman | URL (relatif `BASE_URL`) | Peran |
|---------|--------------------------|-------|
| Landing | `/index.php` | publik |
| Login | `/login.php` | publik |
| Proses login | `POST /login.php` | publik |
| Logout | `/logout.php` | semua yang login |
| Dashboard Admin | `/admin/index.php` | admin |
| Daftar sekolah | `/admin/sekolah.php` | admin |
| Tambah/ubah sekolah | `/admin/sekolah_form.php` | admin |
| Detail sekolah | `/admin/sekolah_detail.php` | admin |
| Daftar pengguna | `/admin/pengguna.php` | admin |
| Tambah/ubah pengguna | `/admin/pengguna_form.php` | admin |
| Rekap | `/admin/rekap.php` | admin |
| Dashboard Guru BK | `/guru_bk/index.php` | guru_bk |
| Daftar catatan | `/guru_bk/catatan.php` | guru_bk |
| Detail catatan | `/guru_bk/catatan_detail.php` | guru_bk |
| Beranda siswa | `/siswa/index.php` | siswa |
| Kenali Diriku | `/siswa/kenali_diri.php` | siswa |
| Jelajah Profesi | `/siswa/profesi.php` | siswa |
| Jalur Setelah Lulus | `/siswa/jalur.php` | siswa |
| Bandingkan | `/siswa/bandingkan.php` | siswa |
| Rencana | `/siswa/rencana.php` | siswa |
| Jurnal | `/siswa/jurnal.php` | siswa |
| Konsultasi | `/siswa/konsultasi.php` | siswa |
| Tips | `/siswa/tips.php` | siswa |
| Profil | `/profil.php` | semua |

Aturan:

- Semua perubahan data memakai **POST**, bukan GET.
- Setiap halaman setelah login memuat `header.php`; header yang menolak pengguna
  tanpa hak (403 sederhana).
- Jangan memakai input user sebagai nama file yang di-`include`. File yang
  dipanggil selalu ditulis tetap di kode.
- Halaman yang tidak ada → 404 sederhana dengan tombol kembali.

---

## 6. Session dan data login

Saat login berhasil, simpan minimal:

```php
$_SESSION['pengguna'] = [
  'id', 'nama', 'username', 'role', 'sekolah_id', 'sekolah_nama'
];
```

Aturan:

- `session_regenerate_id(true)` setelah login.
- Setiap request, `header.php` memuat ulang status akun dari database. Jika akun
  dinonaktifkan Admin, session langsung ditolak (sesuai kebutuhan “cabut akses”).
- Jangan menyimpan password/hash di session.
- Jangan percaya `role` atau `sekolah_id` yang dikirim lewat form; selalu ambil
  dari session/database.
- Timeout session: 2 jam tidak aktif (diatur di `config/koneksi.php`).

---

## 7. Keamanan dasar (wajib)

### 7.1 Perlindungan folder

Rencana `.htaccess` di akar:

- Tolak akses langsung ke `config/`, `database/`, `includes/`, `docs/`.
- `Options -Indexes` agar isi folder tidak terdaftar.
- Jangan mengarahkan semua request ke satu file; aplikasi memang memakai file
  per halaman.

Setiap file PHP di folder tersebut juga diberi penjaga:

```php
defined('KARVIA_APP') or exit('Akses langsung tidak diizinkan.');
```

Halaman pemanggil mendefinisikan `KARVIA_APP` sebelum memuat file lain.

### 7.2 Database

- Selalu PDO prepared statement.
- Jangan menyusun query dengan menempel input user.
- `PDO::ATTR_ERRMODE => EXCEPTION`, `ATTR_EMULATE_PREPARES => false`.
- Transaksi untuk operasi multi-tabel (mis. buat akun + profil).
- Tampilkan pesan ramah; detail error dicatat lewat `error_log()` (masuk ke log
  PHP/Apache Laragon), bukan ditampilkan ke pengguna.

### 7.3 Output

- Semua teks dari database/input di-escape dengan `e()` (`htmlspecialchars`).
- Jangan memakai `innerHTML` untuk data user.
- Atribut `value` pada form juga di-escape.

### 7.4 CSRF & validasi

- Setiap form POST menyertakan token CSRF.
- Validasi server: wajib isi, panjang, format username, peran yang diizinkan,
  sekolah harus ada, status hanya `aktif`/`nonaktif`.
- Jangan mengandalkan `required` HTML saja.

### 7.5 Password

- `password_hash($password, PASSWORD_DEFAULT)`.
- `password_verify()` untuk login.
- Minimal 8 karakter.
- Reset password menghasilkan password sementara yang ditampilkan **sekali** ke
  Admin untuk diteruskan ke pengguna.
- Jangan pernah menampilkan hash.

### 7.6 Otorisasi

- Cek peran di server (`header.php`), bukan hanya menyembunyikan menu.
- Cek kepemilikan data: siswa hanya `id = session.id`; guru hanya
  `sekolah_id = session.sekolah_id`.
- Halaman di folder `admin/` tidak boleh bisa dibuka guru/siswa.

---

## 8. Konvensi penamaan

| Objek | Aturan | Contoh |
|-------|--------|--------|
| Tabel | Indonesia, singular, snake_case | `sekolah`, `pengguna`, `berbagi_catatan` |
| Kolom | Indonesia, snake_case | `nama_lengkap`, `sekolah_id`, `dibuat_pada` |
| Primary key | `id` | `id` |
| Foreign key | `nama_tabel_id` | `sekolah_id`, `siswa_id` |
| Status | enum huruf kecil | `aktif`, `nonaktif` |
| Peran | enum huruf kecil | `admin`, `guru_bk`, `siswa` |
| File halaman | snake_case | `pengguna_form.php`, `catatan_detail.php` |
| Fungsi | domain_aksi | `pengguna_cari()`, `sekolah_semua()` |
| CSS class | mengikuti Tailwind | `bg-karvia-600` |
| Variabel halaman | snake_case | `$peran_halaman`, `$halaman_aktif` |

Penamaan Indonesia dipilih agar Cindy dapat membaca nama tabel/fungsi dan
mengaitkannya dengan fitur yang ia lihat.

---

## 9. Konvensi tampilan

Ambil gaya dari prototipe:

- Warna utama hijau KARVIA: `#22c55e`, `#16a34a`, `#15803d`, `#14532d`.
- Font **Inter**, ikon **FontAwesome 6**.
- Latar `slate-50`, kartu putih, sudut `rounded-2xl`/`rounded-3xl`, bayangan lembut.
- Sidebar kiri untuk desktop; tombol menu untuk mobile.
- Badge peran:
  - Admin: `bg-slate-800 text-white`
  - Guru BK: `bg-purple-100 text-purple-800`
  - Siswa: `bg-emerald-100 text-emerald-800`
- Badge status:
  - `aktif`: hijau
  - `nonaktif`: abu/merah
  - catatan `baru`: biru
  - `dibaca`: kuning
  - `ditindaklanjuti`: hijau
- Tombol utama hijau; tombol sekunder abu; tindakan berbahaya merah.
- Gunakan **flash message** setelah simpan/ubah, bukan modal sukses palsu.
- Konfirmasi aksi berisiko memakai modal/konfirmasi yang jelas.
- Semua teks UI berbahasa Indonesia, hindari istilah teknis (“query”, “foreign key”).
- Tabel dibungkus `overflow-x-auto` agar tetap nyaman di layar kecil.
- Cetak: sembunyikan sidebar/tombol, tampilkan ringkasan rapi (CSS `@media print`).

---

## 10. Konfigurasi & menjalankan di Laragon

1. Letakkan folder proyek di bawah `D:\LARAGON\laragon\www\`. Nama dan kedalaman
   folder bebas; contoh penataan bertingkat:
   `www\TUBES_PRK_PEMWEB_2026\kelompok\kelompok_18`.
2. Akses:
   - `http://localhost/<nama-folder>/`
   - contoh bila memakai penataan bertingkat:
     `http://localhost/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18/`
   - atau virtual host, mis. `http://karvia.test`, dengan document root diarahkan
     ke folder proyek.
3. Buat database `karvia_db` (utf8mb4).
4. Import `database/database.sql`, lalu `database/dummy_karvia.sql` dan `database/dummy_karvia2.sql` sesuai panduan `docs/12`. File dummy pertama mengosongkan tabel; file kedua hanya menambah data.
5. Isi `config/koneksi.php` sesuai Laragon (default user `root`, password kosong).
6. Jalankan Apache & MySQL dari Laragon.

`BASE_URL` di `config/koneksi.php` harus menyesuaikan cara akses:

| Cara akses | `BASE_URL` |
|------------|------------|
| `http://localhost/karvia` | `/karvia` |
| `http://localhost/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18` | `/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18` |
| Virtual host `http://karvia.test` | `''` (kosong) |

Semua link dan asset memakai `BASE_URL` agar tidak rusak saat folder dipindah atau
di-hosting.

---

## 11. Hal yang sengaja TIDAK dipakai

- Framework Laravel/CodeIgniter (terlalu berat untuk pembelajaran awal).
- Composer/npm/build step.
- ORM.
- API terpisah/SPA.
- localStorage untuk data utama.
- Login dengan Google/OAuth.
- WebSocket/chat real-time.
- Front controller dengan parameter `?page=` (diganti file per halaman).

Jika suatu saat dibutuhkan, keputusan itu harus dicatat di `08-keputusan-terbuka.md`
dan disetujui pemilik proyek.
