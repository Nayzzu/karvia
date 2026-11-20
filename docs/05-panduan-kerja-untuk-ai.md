# 05 — Panduan Kerja untuk AI / Pengembang

Dokumen ini adalah **aturan main** saat melanjutkan proyek. Baca bersama
`README.md` dan dokumen `00`–`04` sebelum menulis kode.

---

## 1. Status saat ini (per 19 November 2026)

| Hal | Status |
|-----|--------|
| Kerangka folder dan database | ✅ tersedia; 11 tabel, 13 FK, data demo fiktif |
| Landing, login, dashboard Admin/Guru BK/Siswa | 🟡 tampilan awal masih berbasis prototipe; login dan dashboard sudah mulai dihubungkan ke backend pada branch `feat/foundation-auth` |
| Fondasi backend | 🟡 PDO, helper keamanan/session, autentikasi, sidebar bersama, dan dashboard berbasis data sedang dikerjakan; belum di-commit/push |
| Halaman CRUD Admin, fitur Guru BK, fitur eksplorasi Siswa | ⬜ belum selesai; dikerjakan pada branch fitur terpisah setelah fondasi diuji |
| Landing publik | ✅ hero dirapikan dengan komposisi terpusat, warna KARVIA dan font Inter dipertahankan; branch `feat/landing-page` |
| File HTML prototipe | ✅ utuh, **jangan diubah** |

Jangan menganggap fitur apa pun sudah jadi hanya karena dokumennya ada.

---

## 2. Aturan yang tidak boleh dilanggar

1. **Jangan mengubah, memindahkan, atau menghapus**
   `karvia_career_vision_action.html`. File itu referensi historis.
2. **Jangan menulis ulang dokumen ini tanpa alasan.** Perbarui bagian yang berubah.
3. **Jangan menambah fitur di luar MVP** tanpa mencatatnya di
   `08-keputusan-terbuka.md` dan mendapat persetujuan.
4. **Jangan memakai data dummy/hardcoded** untuk angka dashboard. Semua dari database.
5. **Jangan menyimpan password sebagai teks biasa.**
6. **Jangan memakai localStorage** untuk data utama.
7. **Jangan memilih peran di halaman login.** Peran berasal dari akun.
8. **Jangan menampilkan tombol yang belum berfungsi** seolah-olah berhasil.
   Lebih baik sembunyikan atau beri label “belum tersedia”.
9. **Jangan menghapus akun** untuk “mengeluarkan” pengguna. Gunakan nonaktif.
10. **Jangan mengakses data lintas sekolah** hanya karena tahu ID di URL.
11. **Jangan memakai framework/Composer** pada MVP.
12. **Jangan mengubah skema database** tanpa memperbarui `03-rencana-database.md`.
13. **Database memakai satu file SQL**: `database/database.sql` (bagian A struktur,
    bagian B data contoh). Jangan membuat folder `migrations/` atau `seeders/`.
14. **Satu halaman = satu file.** Jangan membuat front controller dengan `?page=`
    atau menyalin satu file untuk beberapa halaman.
15. **Halaman peran hanya di folder perannya**: `admin/`, `guru_bk/`, `siswa/`.

---

## 3. Bahasa dan gaya

- Bahasa UI: **Indonesia**.
- Nama tabel/kolom/fungsi: **Indonesia**, `snake_case`.
- Komentar kode: Indonesia, menjelaskan tujuan.
- Pesan error ke pengguna: ramah, tidak menyebut nama tabel/SQL.
- Contoh: “Username sudah dipakai. Silakan pilih username lain.”
- Hindari istilah “endpoint”, “payload”, “foreign key” di UI. Boleh di dokumen teknis.

---

## 4. Urutan build yang disarankan

Kerjakan berurutan. Jangan melompat ke tampilan sebelum fondasi data siap.

### Fase 1 — Fondasi (P0)

- [x] Buat database `karvia_db`.
- [x] Tulis `database/database.sql` sesuai `03`: bagian A (struktur, index,
  foreign key) + bagian B (data demo fiktif minimal).
- [x] Import `database/database.sql`; verifikasi 11 tabel, 13 foreign key,
  InnoDB, `utf8mb4_unicode_ci`, jumlah data demo, dan aktivitas login contoh kosong.
- [x] Buat `config/koneksi.php` (konstanta aplikasi + koneksi PDO).
- [x] Buat `includes/function.php`: koneksi, escape, flash, CSRF, auth, sesi,
  format tanggal, redirect, dan log aktivitas.
- [x] Buat `includes/header.php` dan `includes/footer.php` dengan navigasi per peran.
- [x] Hubungkan `index.php`, `login.php`, dan `logout.php` ke alur autentikasi.
- [x] Cek login & peran di `header.php` lewat `$peran_halaman`.
- [x] Uji HTTP login ketiga peran, akses salah peran, CSRF, logout, dan akun nonaktif.

**Selesai bila:** ketiga peran bisa login/logout dan diarahkan ke dashboard
masing-masing; akun nonaktif ditolak; akses lintas peran dibatasi. Halaman yang
belum dibuat cukup berupa placeholder dengan judul. HTTP test lokal telah
memverifikasi login/logout tiga peran, CSRF invalid, akses salah peran, dan akun
nonaktif. Pemeriksaan checklist keamanan lain tetap dijalankan pada fase terkait.

### Fase 2 — Admin (P0)

- [x] Sidebar Admin di `includes/header.php`.
- [x] `admin/index.php`: dashboard dengan statistik & rekap nyata.
- [x] `admin/sekolah.php`: daftar + pencarian + aktif/nonaktif.
- [x] `admin/sekolah_form.php`: tambah/ubah sekolah.
- [x] `admin/sekolah_detail.php`: detail + jumlah siswa/guru.
- [ ] `admin/pengguna.php`: daftar pengguna + filter.
- [ ] `admin/pengguna_form.php`: tambah/ubah + reset password.
- [ ] Aktif/nonaktifkan pengguna.
- [ ] `admin/rekap.php`: rekap sederhana.
- [x] Buat kerangka file halaman Admin yang ditautkan di sidebar; isi CRUD pengguna dan rekap masih placeholder.

**Selesai bila:** Admin dapat mengelola 2 sekolah dan seluruh akun, serta angka
dashboard berubah sesuai data. Dashboard dan pengelolaan sekolah sudah dibuat;
pengelolaan akun dan halaman rekap masih menyusul.

### Fase 3 — Guru BK (P0)

- [ ] Sidebar Guru BK di `includes/header.php`.
- [ ] `guru_bk/index.php`: dashboard nyata untuk sekolah sendiri.
- [ ] `guru_bk/catatan.php`: daftar catatan yang dibagikan.
- [ ] `guru_bk/catatan_detail.php`: detail + penandaan dibaca.
- [x] Buat kerangka halaman daftar dan detail sesuai tautan sidebar.
- [ ] Form tindak lanjut + riwayat.
- [ ] Cetak ringkasan.

**Selesai bila:** Guru BK sekolah A tidak bisa melihat siswa sekolah B, dan
tindak lanjut tersimpan.

### Fase 4 — Siswa (P0)

- [ ] Sidebar Siswa di `includes/header.php`.
- [ ] `siswa/index.php`: beranda + status langkah.
- [ ] `siswa/kenali_diri.php` (simpan/ubah).
- [ ] `siswa/profesi.php`: jelajah profesi + filter + bookmark.
- [ ] `siswa/jalur.php`: jalur setelah lulus (statis).
- [ ] `siswa/bandingkan.php`.
- [ ] `siswa/rencana.php`.
- [ ] `siswa/jurnal.php`: riwayat.
- [ ] `siswa/konsultasi.php` + bagikan ke Guru BK.
- [ ] `siswa/tips.php` (statis).
- [ ] `profil.php`: profil & ganti password.
- [x] Buat kerangka semua halaman siswa dan profil bersama; formulir/konten dinamis masih placeholder.

**Selesai bila:** data siswa bertahan setelah logout/refresh dan muncul di
dashboard Guru BK setelah dibagikan.

### Fase 5 — Penyempurnaan (P1)

- [ ] CSS cetak.
- [ ] Sidebar mobile.
- [ ] Pagination/pencarian.
- [ ] Ganti password semua peran.
- [ ] Log aktivitas ditampilkan (opsional).
- [ ] Backup database.
- [ ] Panduan menjalankan untuk Cindy.

---

## 5. Standar kode

### 5.1 File PHP

- Mulai `<?php`, **tanpa** `?>` di akhir file murni PHP.
- Setiap file di `config/`, `includes/`, `database/` diawali penjaga:

```php
defined('KARVIA_APP') or exit('Akses langsung tidak diizinkan.');
```

- Gunakan `require_once __DIR__ . '/...'`.
- Satu file = satu halaman atau satu tanggung jawab.
- Maksimal sekitar 400 baris per file halaman; pecah fungsi data ke
  `includes/function.php` bila lebih.

### 5.2 Database

- Selalu PDO prepared statement dengan parameter bernama.
- Jangan menyusun SQL dari input langsung.
- Gunakan transaksi untuk operasi beberapa tabel.
- Tangkap `PDOException`, catat lewat `error_log()`, tampilkan pesan ramah.
- Jangan `SELECT *` lalu menampilkan semua kolom tanpa filter.

### 5.3 Halaman (file PHP per peran)

Pola umum satu halaman:

```php
1. define('KARVIA_APP', true)
2. tentukan $peran_halaman dan $halaman_aktif
3. require includes/header.php   (cek login & peran)
4. ambil input
5. validasi + CSRF (untuk POST)
6. panggil fungsi data (prepared statement)
7. set flash
8. redirect (setelah POST) atau tampilkan HTML
9. require includes/footer.php
```

Jangan mencampur logika data yang panjang dengan HTML. Letakkan fungsi query di
`includes/function.php` atau blok logika di bagian atas file, lalu HTML di bawah.

### 5.4 Tampilan

- Semua data dinamis di-escape dengan `e()`.
- Gunakan `includes/header.php` + `includes/footer.php` agar sidebar dan `<head>`
  tidak diduplikasi.
- Form POST selalu menyertakan `csrf_field()`.
- Flash ditampilkan `header.php` di atas konten.
- Sediakan empty state.

### 5.5 JavaScript

- Hanya untuk interaksi kecil: toggle sidebar, konfirmasi, tampil/sembunyi field.
- Jangan menyimpan data penting di JS.
- Jangan memakai `alert()` untuk sukses.
- Pastikan halaman tetap berfungsi tanpa JS untuk alur utama.

### 5.6 Penamaan fungsi

| Domain | Contoh |
|--------|--------|
| Auth | `auth_login()`, `auth_logout()`, `auth_pengguna()` |
| Sekolah | `sekolah_semua()`, `sekolah_simpan()`, `sekolah_rekap()` |
| Pengguna | `pengguna_cari()`, `pengguna_buat()`, `pengguna_set_status()` |
| Berbagi | `berbagi_simpan()`, `berbagi_sekolah()` |
| Escape | `e()` |
| Flash | `flash_set()`, `flash_tampil()` |
| Navigasi | `redirect($path)` |

---

## 6. Checklist keamanan sebelum demo

- [ ] Tidak ada password teks biasa di database.
- [ ] Login memakai `password_verify`.
- [ ] Semua form POST memiliki token CSRF dan token diperiksa.
- [ ] Semua output user di-escape.
- [ ] Semua query memakai prepared statement.
- [ ] Guru BK tidak bisa membuka siswa sekolah lain (uji ubah ID di URL).
- [ ] Siswa tidak bisa membuka data siswa lain.
- [ ] Admin tidak bisa menonaktifkan dirinya sendiri.
- [x] Akun nonaktif tidak bisa login (uji HTTP); penolakan session aktif diperiksa pada setiap halaman internal lewat pemuatan ulang status akun.
- [ ] Folder `config/`, `includes/`, `database/`, `docs/` tidak bisa diakses via browser (perlu aturan Apache `.htaccess` pada konfigurasi hosting; uji belum dilakukan).
- [ ] Pesan error database tidak tampil ke pengguna.
- [ ] File HTML prototipe masih utuh.
- [ ] Tidak ada kredensial asli yang di-commit/dibagikan.

---

## 7. Checklist pengujian manual

### Autentikasi

- [x] Login admin benar → dashboard admin (uji HTTP lokal).
- [x] Login guru benar → dashboard guru (uji HTTP lokal).
- [x] Login siswa benar → beranda siswa (uji HTTP lokal).
- [ ] Password salah → pesan umum, tidak membocorkan username ada/tidak.
- [x] Akun nonaktif → pesan jelas, tidak masuk (uji HTTP lokal).
- [ ] Logout → tidak bisa kembali dengan tombol Back (cache aman; POST logout teruji).

### Admin

- [ ] Tambah sekolah → muncul di daftar & form akun.
- [ ] Tambah guru di sekolah A → guru hanya melihat sekolah A.
- [ ] Username duplikat → ditolak dengan pesan.
- [ ] Nonaktifkan siswa → login siswa gagal.
- [ ] Aktifkan kembali → login berhasil.
- [ ] Reset password → password lama gagal, password baru berhasil.
- [ ] Angka dashboard berubah setelah menambah akun.

### Guru BK

- [ ] Hanya melihat catatan yang dibagikan.
- [ ] Detail siswa sekolah lain via URL → 403/404.
- [ ] Buka detail → status berubah `dibaca`.
- [ ] Simpan tindak lanjut → status `ditindaklanjuti`, riwayat muncul.
- [ ] Cetak ringkasan rapi.

### Siswa

- [ ] Simpan refleksi → refresh data tetap.
- [ ] Bookmark profesi → bertambah/berkurang dengan benar.
- [ ] Simpan perbandingan & rencana.
- [ ] Jurnal baru menambah entri, bukan menimpa.
- [ ] Bagikan → muncul di guru sekolah yang sama, tidak di sekolah lain.
- [ ] Bagikan ulang → status kembali `baru`.
- [ ] Cetak ringkasan.

### Ketahanan

- [ ] Restart Laragon → data tetap.
- [ ] Refresh setelah POST tidak menggandakan data.
- [ ] Input `<script>alert(1)</script>` tampil sebagai teks.
- [ ] Input `' OR 1=1 --` tidak merusak query.
- [ ] Halaman kosong menampilkan empty state, bukan error.
- [ ] Tampilan mobile tidak berantakan.

---

## 8. Definition of Done (per fitur)

Sebuah fitur dianggap selesai bila:

1. File halaman dan hak aksesnya jelas.
2. Halaman + fungsi data + tampilan tersedia.
3. Validasi server berjalan.
4. CSRF diterapkan pada POST.
5. Data benar-benar tersimpan di database.
6. Ada flash sukses/gagal.
7. Ada empty state.
8. Tidak ada data hardcoded.
9. Sudah diuji untuk peran yang berhak **dan** peran yang tidak berhak.
10. Dokumentasi terkait diperbarui (terutama `03` bila skema berubah).

---

## 9. Cara mengambil keputusan saat ragu

1. Cek `01` (kebutuhan) dan `03` (data).
2. Jika belum dijawab, gunakan **usulan default** di `08`.
3. Catat keputusan yang diambil di `08` pada tabel “Riwayat keputusan”.
4. Jangan berhenti total hanya karena satu detail kosmetik; kerjakan bagian lain.
5. Tanyakan ke pengguna hanya jika keputusan itu **mengubah struktur data atau
   hak akses**.

Contoh keputusan yang boleh diambil sendiri:
- Urutan tombol, warna badge, teks empty state.
- Nama file halaman.
- Pagination 25 baris.

Contoh yang harus dikonfirmasi:
- Menghapus permanen akun.
- Admin boleh membaca jurnal siswa.
- Guru BK boleh membuat akun siswa.
- Menambah fitur jadwal konsultasi.

---

## 10. Cara menjelaskan ke Cindy

Cindy adalah pendidik, bukan programmer. Saat menyerahkan hasil:

1. Mulai dari **masalah yang diselesaikan**, bukan teknologi.
2. Gunakan analogi sederhana:
   - Sekolah = folder besar.
   - Akun = kunci masuk.
   - Nonaktif = kunci dinonaktifkan, isi folder tetap ada.
   - Database = buku catatan permanen.
3. Tunjukkan alur 3 peran dengan urutan demo, bukan menjelaskan kode.
4. Jelaskan batasan:
   - bukan tes psikologis;
   - data hanya untuk bimbingan karier;
   - akun dibuat Admin;
   - “keluarkan” berarti nonaktif.
5. Beri langkah menjalankan:
   - buka Laragon → Start All;
   - buka browser → alamat aplikasi;
   - login dengan akun demo;
   - cara backup database.
6. Simpan penjelasan dalam bahasa Indonesia di `docs` atau file panduan terpisah.
7. Jangan menampilkan istilah seperti “foreign key”, “CSRF”, atau “query” tanpa
   penjelasan awam.

---

## 11. Jebakan yang harus dihindari

| Jebakan | Akibat | Pencegahan |
|---------|--------|------------|
| Menyalin JS prototipe | Data hilang, angka palsu | Server-side + database |
| Lupa filter sekolah | Guru melihat siswa sekolah lain | Selalu `sekolah_id` dari session |
| Memakai `$_POST['role']` | Siswa bisa jadi admin | Ambil peran dari database/session |
| Hard delete pengguna | Riwayat konseling hilang | Nonaktifkan |
| Menampilkan “berhasil” tanpa simpan | Kepercayaan hilang | Simpan dulu, baru flash |
| Query bercampur HTML | Sulit dirawat | Fungsi data di `includes/function.php` |
| Tidak escape output | XSS | Helper `e()` |
| Password di log | Kebocoran | Jangan catat input password |
| Mengubah skema tanpa docs | Data kacau | Update `03` |
| Terlalu banyak fitur | Tidak selesai Rabu | Patuhi prioritas P0 |

---

## 12. Progres

Perbarui tabel ini setiap menyelesaikan fase atau bagian yang dapat diuji dan dikirim dalam branch terpisah.

| Fase | Status | Tanggal | Catatan |
|------|--------|---------|---------|
| 0 — Dokumentasi & kerangka | ✅ selesai | 19 Nov 2026 | Folder & docs dibuat |
| 1 — Fondasi & login | ✅ selesai | 20 Nov 2026 | HTTP test lokal: login/logout tiga peran, akun nonaktif, CSRF invalid dan akses salah peran; branch `feat/foundation-auth` telah dipush |
| 2 — Admin | 🟡 berjalan | 20 Nov 2026 | Dashboard dan kelola sekolah (daftar, pencarian, tambah/ubah, detail, aktif/nonaktif) dikerjakan pada branch `feat/admin-schools`; pengelolaan pengguna dan rekap masih menyusul |
| 3 — Guru BK | ⬜ belum | | |
| 4 — Siswa | ⬜ belum | | |
| 5 — Penyempurnaan & demo | ⬜ belum | | |

---

## 13. Ringkasan konteks untuk sesi baru

Jika memulai sesi baru, cukup berikan instruksi:

> Baca `docs/README.md`, lalu `docs/00` sampai `docs/05`. Lanjutkan proyek KARVIA
> dari status terakhir di `docs/05` bagian 12. Patuhi aturan di `docs/05`.
> Untuk pembagian kerja 2 orang, baca `docs/09`.
> Jangan mengubah `karvia_career_vision_action.html`.

Ringkasan satu paragraf:

> KARVIA adalah aplikasi bimbingan karier PHP native + MySQL untuk siswa SMA/MA,
> dengan tiga peran (Admin, Guru BK, Siswa), multi-sekolah, login nyata, dan data
> persisten. Admin mengelola sekolah/akun/akses dan melihat rekap. Guru BK melihat
> catatan siswa sekolahnya yang dibagikan. Siswa mengisi lima langkah eksplorasi
> dan membagikan catatan. Prototipe HTML hanya acuan tampilan dan tidak boleh diubah.
