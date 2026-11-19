# 09 — Pembagian Tugas 2 Orang

**Status: perencanaan. Belum ada pengerjaan kode.**

Dokumen ini membagi pekerjaan untuk **2 pengembang** agar tidak saling menimpa file
dan bisa berjalan paralel. Aturan produk tetap mengikuti `00`–`08`.

Konteks teknis yang sudah ditetapkan:

- Struktur folder mengikuti pola per peran:
  `admin/`, `guru_bk/`, `siswa/`, `includes/`, `config/`, `database/`, `assets/`,
  `docs/`, plus `index.php`, `login.php`, `logout.php` di akar.
- Tidak ada front controller `?page=`; setiap halaman adalah file PHP sendiri
  (lihat `02` bagian 2–5).
- Database: satu file `database/database.sql` (struktur + data contoh).
- Tanpa folder `migrations/` atau `seeders/`.
- PHP native + MySQL/PDO + session.
- Tiga peran: Admin, Guru BK, Siswa.
- Lokasi folder bebas; path bertingkat seperti
  `www/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18` hanya contoh penataan.

---

## 1. Prinsip pembagian

1. **Bagi berdasarkan alur, bukan berdasarkan jenis file.** Orang A memegang
   fondasi + Admin; Orang B memegang Siswa + Guru BK.
2. **Satu file, satu pemilik.** File bersama hanya diedit oleh pemiliknya.
3. **Kontrak dibekukan di awal.** Nama tabel, nama file halaman, fungsi bantu, dan
   struktur session disepakati sebelum koding agar kedua orang bisa bekerja paralel.
4. **Integrasi setiap hari.** Jangan menunggu hari terakhir untuk menggabungkan.
5. **Saling menguji.** A menguji alur B, B menguji alur A.
6. **Dokumentasi adalah alat kerja.** Setiap keputusan/skema yang berubah langsung
   dicatat, bukan diingat.

---

## 2. Ringkasan peran

| | **Orang A** | **Orang B** |
|---|---|---|
| Peran | Fondasi, Autentikasi, Admin, Keamanan, Rilis | Siswa, Guru BK, Tampilan, Cetak, Konten |
| Fokus utama | Database SQL, config, includes inti, login, dashboard Admin, kelola sekolah/akun/akses, rekap | Lima langkah siswa, profesi, konsultasi, dashboard Guru BK, tindak lanjut, UI/UX |
| Beban | Lebih berat di awal | Lebih berat di tengah |
| Mulai dari | Hari 1: SQL + fondasi | Hari 1: tampilan statis + konten (menunggu fondasi) |
| Deliverable akhir | Sistem bisa login, Admin berfungsi, aman, siap demo | Alur siswa–guru berfungsi, tampilan rapi, bisa dicetak |

Alasan pembagian: Admin adalah kebutuhan baru yang paling banyak bergantung pada
fondasi (akun, sekolah, akses). Siswa + Guru BK adalah alur produk yang sudah ada
di prototipe dan bisa dikerjakan paralel setelah fondasi tersedia.

---

## 3. Detail tugas Orang A

### 3.1 Database & konfigurasi

- [ ] Tulis `database/database.sql`:
  - **Bagian A**: 11 tabel sesuai `03`, primary key, unique, index, foreign key,
    urutan aman untuk diimpor;
  - **Bagian B**: data contoh (2 sekolah, 7 akun, 4–8 profesi, data karier demo).
- [ ] Uji import di database kosong: `mysql -u root karvia_db < database/database.sql`.
- [ ] Buat `config/koneksi.php`: `BASE_URL`, zona waktu, nama session, mode debug,
  kredensial PDO, fungsi koneksi.
- [ ] Buat `database/backup_YYYY-MM-DD.sql` sebelum demo.

### 3.2 Fondasi aplikasi

- [ ] `includes/function.php`: koneksi PDO, `e()`, flash, CSRF, auth, validasi,
  format tanggal, redirect.
- [ ] `includes/header.php`: mulai session, muat akun dari database, cek login,
  cek `$peran_halaman`, tampilkan `<head>`, header, sidebar sesuai peran, flash.
- [ ] `includes/footer.php`: penutup halaman + pemuatan JS.
- [ ] Sidebar Admin di `header.php`.
- [ ] `index.php`: landing → login atau dashboard sesuai peran.
- [ ] `login.php`: form + proses login (`password_verify`).
- [ ] `logout.php`: hancurkan session & cookie.
- [ ] `profil.php`: profil + ganti password semua peran.

### 3.3 Autentikasi

- [ ] Proses login: cek status, update `last_login_at`, `login_count`, catat log.
- [ ] Redirect sesuai peran.
- [ ] Tolak akun nonaktif dengan pesan jelas.
- [ ] Session timeout 2 jam.

### 3.4 Panel Admin

- [ ] `admin/index.php`: sekolah aktif, akun terdaftar, pernah login, aktif 30 hari.
- [ ] Tabel rekap per sekolah (siswa, Guru BK, catatan, aktivitas terakhir).
- [ ] `admin/sekolah.php`: daftar + pencarian.
- [ ] `admin/sekolah_form.php`: tambah/ubah sekolah.
- [ ] `admin/sekolah_detail.php`: detail + jumlah akun + daftar akun.
- [ ] `admin/pengguna.php`: daftar + filter peran/sekolah/status/kata kunci.
- [ ] `admin/pengguna_form.php`: tambah/ubah, reset password (tampil sekali),
  aktif/nonaktifkan + konfirmasi.
- [ ] Larang Admin menonaktifkan dirinya sendiri.
- [ ] `admin/rekap.php`: rekap sederhana.

### 3.5 Keamanan & rilis

- [ ] `.htaccess`: tolak akses `config/`, `database/`, `includes/`, `docs/`.
- [ ] Pastikan semua query prepared statement.
- [ ] Pastikan semua output di-escape.
- [ ] Pastikan semua POST memakai CSRF.
- [ ] Uji IDOR: guru tidak bisa membuka siswa sekolah lain.
- [ ] Uji akun nonaktif yang sedang login.
- [ ] Uji XSS & SQL injection dasar.
- [ ] Backup database & panduan menjalankan.
- [ ] Bantu integrasi & perbaikan bug lintas peran.

### 3.6 File yang menjadi milik Orang A

| Area | File |
|------|------|
| Database | `database/database.sql`, `database/backup_*.sql` |
| Config | `config/koneksi.php` |
| Includes | `includes/function.php`, `includes/header.php`, `includes/footer.php` |
| Akar | `index.php`, `login.php`, `logout.php`, `profil.php`, `.htaccess`, `README.md` |
| Admin | seluruh `admin/*` |
| Aset | tidak memiliki `assets/*` (milik B) |
| Dokumen | `docs/02`, `docs/03`, `docs/05`, `docs/07`, `docs/08`, `AGENTS.md` |

---

## 4. Detail tugas Orang B

### 4.1 Tampilan & konten (bisa mulai lebih dulu)

- [ ] Pindahkan desain prototipe ke halaman siswa & guru (tanpa query dulu).
- [ ] Susun markup sidebar siswa & guru, serahkan ke A untuk dipasang di
  `includes/header.php` (A pemilik file).
- [ ] Siapkan konten statis:
  - Jalur Setelah Lulus;
  - Tips & Edukasi Karier;
  - disclaimer “bukan tes psikologis”.
- [ ] Siapkan daftar 4–8 profesi + deskripsi untuk bagian B `database.sql`
  (serahkan teksnya ke A).
- [ ] Siapkan data contoh karier (Ahmad/Siti) untuk bagian B `database.sql`.
- [ ] Siapkan `assets/css/app.css` (termasuk cetak & responsif mobile).
- [ ] Siapkan `assets/js/app.js`.
- [ ] Pastikan bahasa Indonesia dan konsisten dengan warna KARVIA.

### 4.2 Fitur Siswa

- [ ] `siswa/index.php`: beranda + status kelengkapan lima langkah.
- [ ] `siswa/kenali_diri.php`: mapel, kekuatan, catatan minat.
- [ ] `siswa/profesi.php`: filter kategori + bookmark.
- [ ] `siswa/jalur.php`: jalur setelah lulus (statis).
- [ ] `siswa/bandingkan.php`: opsi A/B + kriteria.
- [ ] `siswa/rencana.php`: tujuan + 2 langkah.
- [ ] `siswa/jurnal.php`: banyak entri + riwayat.
- [ ] `siswa/konsultasi.php`: ringkasan dari database + bagikan + cetak.
- [ ] Status berbagi di sisi siswa.
- [ ] `siswa/tips.php`: tips & edukasi.
- [ ] Koordinasi dengan A untuk tautan profil/ganti password.

### 4.3 Fitur Guru BK

- [ ] `guru_bk/index.php`: dashboard dengan angka nyata sekolah sendiri.
- [ ] Daftar catatan yang dibagikan (bukan semua siswa).
- [ ] `guru_bk/catatan.php`: filter status + pencarian nama.
- [ ] `guru_bk/catatan_detail.php`: identitas, minat, bookmark, perbandingan,
  rencana, jurnal, pesan.
- [ ] Tandai `dibaca` saat detail dibuka.
- [ ] Form tindak lanjut + riwayat, ubah status `ditindaklanjuti`.
- [ ] Cetak ringkasan siswa.
- [ ] Pastikan tidak bisa membuka siswa sekolah lain.

### 4.4 File yang menjadi milik Orang B

| Area | File |
|------|------|
| Siswa | seluruh `siswa/*` |
| Guru BK | seluruh `guru_bk/*` |
| Assets | `assets/css/*`, `assets/js/*`, `assets/images/*` |
| Konten | teks Jalur, Tips, profesi, data contoh karier |
| Dokumen | `docs/04` (bagian siswa/guru), `docs/06`, panduan pengguna |

---

## 5. File bersama & aturan konflik

| File | Pemilik | Aturan |
|------|---------|--------|
| `includes/header.php` | A | B mengusulkan item sidebar; A memasang & menautkan halaman baru |
| `includes/function.php` | A | B memakai fungsi yang sudah ada; perubahan diusulkan ke A |
| `includes/footer.php` | A | B tidak mengedit |
| `config/koneksi.php` | A | B tidak mengedit |
| `database/database.sql` | A | B menyerahkan teks profesi/data contoh untuk digabung |
| `assets/css/app.css` | B | A tidak mengedit |
| `assets/js/app.js` | B | A tidak mengedit |
| `docs/05` progres | A | B melapor; A memperbarui tabel progres |
| `docs/08` keputusan | A | B boleh mengusulkan; A mencatat |

**Aturan keras:**

- Jangan mengedit file milik orang lain tanpa persetujuan.
- Jika butuh perubahan di file bersama, tulis permintaan singkat (file, alasan,
  usulan) lalu pemilik yang mengerjakan.
- Jangan menyalin file lalu membuat versi kedua (`index2.php`, `siswa_baru.php`).
- Sebelum mulai kerja, ambil versi terbaru (pull/salinan terbaru).
- Jangan menimpa pekerjaan orang lain saat menyelesaikan konflik.
- Halaman baru hanya ditambahkan di folder peran masing-masing.

---

## 6. Kontrak kerja yang harus disepakati di Hari 1

### 6.1 Database

- Skema mengikuti `03` tanpa perubahan sepihak.
- Nama tabel/kolom tidak diganti di tengah jalan.
- Jika perlu kolom baru, A memperbarui `database.sql` + `03`, lalu memberi tahu B.

### 6.2 Halaman & URL

- Satu halaman = satu file. Tidak memakai `?page=`.
- Nama file mengikuti peta di `02` bagian 5 dan `04`.
- Halaman baru di folder peran harus diberi tahu ke A agar ditautkan di sidebar.
- B tidak memindahkan atau mengganti nama file yang sudah dipakai A.

### 6.3 Session

```php
$_SESSION['pengguna'] = [
    'id', 'nama', 'username', 'role', 'sekolah_id', 'sekolah_nama'
];
```

B mengambil data dari `auth_pengguna()`, bukan dari `$_SESSION` langsung, agar
mudah dirawat.

### 6.4 Helper bersama

Nama fungsi yang disepakati (A menyediakan, B memakai):

| Fungsi | Kegunaan |
|--------|----------|
| `db()` | Koneksi PDO |
| `e($teks)` | Escape HTML |
| `flash_set($tipe, $pesan)` | Set pesan sekali tampil |
| `flash_tampil()` | Tampilkan pesan |
| `csrf_field()` | Input token CSRF |
| `csrf_cek()` | Validasi token |
| `auth_pengguna()` | Data pengguna login |
| `auth_cek_peran($peran)` | Guard peran |
| `auth_sekolah_id()` | Sekolah pengguna login |
| `redirect($path)` | Redirect memakai `BASE_URL` |

Jika nama berubah, A wajib memberi tahu B dan memperbarui dokumen ini.

### 6.5 Pola halaman

Setiap halaman setelah login mengikuti pola:

```php
<?php
define('KARVIA_APP', true);
$peran_halaman = 'siswa';      // atau 'guru_bk' / 'admin'
$halaman_aktif = 'profesi';    // penanda menu sidebar
require __DIR__ . '/../includes/header.php';

// logika + konten halaman

require __DIR__ . '/../includes/footer.php';
```

- `header.php` yang memilih sidebar berdasarkan `$peran_halaman`.
- Flash ditampilkan otomatis oleh `header.php`.
- B tidak mengubah header/footer; B hanya membuat halaman + sidebar markup.

### 6.6 Enum

| Jenis | Nilai |
|-------|-------|
| Role | `admin`, `guru_bk`, `siswa` |
| Status akun | `aktif`, `nonaktif` |
| Status berbagi | `baru`, `dibaca`, `ditindaklanjuti` |
| Kategori profesi | `kesehatan`, `teknologi`, `pendidikan`, `bisnis`, `lainnya` |

---

## 7. Timeline paralel

Rencana ini lebih agresif daripada `07` karena ada 2 orang. `07` tetap menjadi
fallback jika hanya satu orang yang tersedia.

### Kamis, 19 November — Perencanaan

| A | B |
|---|---|
| Finalkan skema, daftar halaman, helper, include | Inventaris konten prototipe, siapkan daftar profesi/data contoh |
| Tulis kontrak di `09` | Baca `00`–`08`, tandai kebutuhan halaman |

**Keluaran:** kontrak disepakati, belum ada kode.

### Jumat, 20 November — Fondasi + tampilan statis

| A | B |
|---|---|
| `database.sql`, `config/koneksi.php`, `includes/*`, login/logout, `index.php` | Halaman siswa/guru statis, konten, CSS cetak, daftar data contoh |
| Target 12.00: login 3 peran berjalan | Target 15.00: halaman statis siap dipasangi data |

**Checkpoint 16.00:** B bisa memakai helper & database dari A.

### Sabtu, 21 November — Admin awal + siswa inti

| A | B |
|---|---|
| Dashboard Admin, daftar/tambah/ubah sekolah, detail sekolah | Kenali Diriku, Jelajah Profesi, bookmark, Bandingkan, Rencana |

### Minggu, 22 November — Admin akses + guru

| A | B |
|---|---|
| Daftar/filter pengguna, tambah/ubah, reset password, aktif/nonaktif, rekap | Jurnal, Konsultasi, Bagikan, dashboard Guru, detail, tindak lanjut |

### Senin, 23 November — Penyelesaian + integrasi

| A | B |
|---|---|
| Profil/ganti password, `.htaccess`, keamanan, bug admin | Cetak, mobile, empty state, filter guru, bug siswa/guru |
| Uji alur Admin | Uji alur siswa → guru |

**Checkpoint besar:** siswa bagikan → guru melihat → guru tindak lanjut → Admin
melihat angka berubah.

### Selasa, 24 November — QA & data demo

| A | B |
|---|---|
| Uji keamanan, backup, perbaikan bug, panduan teknis | Uji UI, data demo, panduan siswa/guru, perbaikan bug |
| Bersama: uji checklist `05` bagian 7 | Bersama: perbaikan P0 |

### Rabu, 25 November — Demo & serah terima

| A | B |
|---|---|
| Pastikan server, database, backup, akun demo siap | Pastikan tampilan & alur demo lancar |
| Demo bagian Admin & teknis | Demo bagian siswa & guru |

---

## 8. Ritme kerja harian

| Waktu | Aktivitas |
|-------|-----------|
| 09.00 | Sync 15 menit: selesai apa, lanjut apa, blocker apa |
| 12.00 | Cek progres setengah hari |
| 16.00 | Integrasi: gabungkan perubahan, uji alur bersama |
| 17.00 | Perbarui `docs/05` bagian 12 & commit/salinan harian |

Aturan komunikasi:

- Laporkan **blocker**, bukan hanya “masih dikerjakan”.
- Jika menunggu file orang lain, kerjakan bagian yang tidak bergantung.
- Jika menemukan bug di file orang lain, tulis langkah reproduksi; jangan langsung
  menimpa file.
- Jangan mengubah keputusan produk tanpa mencatat di `08`.

---

## 9. Strategi versi (disarankan)

Project belum berupa Git repository. Untuk 2 orang, sangat disarankan:

1. `git init` di folder project.
2. Buat repository privat (GitHub/GitLab) atau minimal simpan di folder bersama.
3. Branch:
   - `main` — versi stabil;
   - `fitur/admin` — Orang A;
   - `fitur/siswa-guru` — Orang B.
4. Gabung ke `main` setiap hari setelah lolos uji dasar.
5. Jangan commit:
   - password asli;
   - `database/backup_*.sql` berisi data nyata;
   - file sementara/editor;
   - folder `.git` milik orang lain.

Jika Git belum memungkinkan, minimal:

- zip harian dengan nama tanggal;
- salin folder sebelum perubahan besar;
- jangan simpan dua versi file di folder yang sama.

---

## 10. Pembagian pengujian

| Area | Penguji utama | Penguji silang |
|------|---------------|----------------|
| Login, akun nonaktif, peran | A | B |
| Admin sekolah & pengguna | A | B |
| Reset password & akses | A | B |
| Siswa lima langkah | B | A |
| Bookmark, banding, rencana, jurnal | B | A |
| Bagikan ke Guru BK | B | A |
| Guru melihat & tindak lanjut | B | A |
| Lintas sekolah (IDOR) | A | B |
| XSS, CSRF, SQL injection dasar | A | B |
| Cetak & mobile | B | A |
| Data bertahan setelah restart | A | B |

**Aturan:** fitur tidak dianggap selesai hanya karena pembuatnya bisa memakainya.
Harus ada satu orang lain yang mencoba merusaknya.

---

## 11. Definition of Done per orang

### Orang A

- [ ] Database bisa diimpor dari nol dengan `database/database.sql`.
- [ ] Tiga peran bisa login/logout; akun nonaktif ditolak.
- [ ] Admin bisa mengelola sekolah, akun, akses, dan melihat rekap nyata.
- [ ] Semua query aman dan semua form POST memakai CSRF.
- [ ] Tidak ada akses lintas sekolah.
- [ ] Backup & panduan menjalankan tersedia.
- [ ] File HTML prototipe tetap utuh.

### Orang B

- [ ] Siswa bisa mengisi lima langkah dan datanya bertahan.
- [ ] Bookmark, perbandingan, rencana, dan jurnal tersimpan benar.
- [ ] Tombol bagikan benar-benar menyimpan dan terlihat oleh guru sekolah yang sama.
- [ ] Guru BK hanya melihat siswa yang dibagikan di sekolahnya.
- [ ] Tindak lanjut tersimpan dan status berubah.
- [ ] Tampilan konsisten, mobile nyaman, cetak rapi.
- [ ] Tidak ada tombol yang menampilkan sukses palsu.

### Bersama

- [ ] Alur demo `07` bagian 5 berjalan tanpa error.
- [ ] Angka dashboard berasal dari database.
- [ ] Data bertahan setelah logout/refresh/restart.
- [ ] Checklist keamanan `05` lolos.
- [ ] Dokumentasi diperbarui.

---

## 12. Jika salah satu berhalangan

| Kondisi | Tindakan |
|---------|----------|
| A sakit/berhalangan | B lanjut halaman, konten, CSS, dan query yang sudah punya skema; jangan mengubah fondasi tanpa A |
| B sakit/berhalangan | A lanjut Admin & keamanan; halaman siswa/guru bisa diselesaikan setelah B kembali |
| Fondasi A terlambat | B jangan menunggu pasif: selesaikan halaman statis, konten, data contoh, cetak |
| Database berubah | A wajib memberi tahu B sebelum B menulis query baru |
| Waktu mepet | Ikuti cakupan demo minimum `07` bagian 4 |

Karena semua keputusan dan skema ada di `docs/`, pekerjaan bisa dilanjutkan oleh
orang lain tanpa menebak-nebak.

---

## 13. Yang tidak boleh dilakukan

- Memulai koding sebelum kontrak Hari 1 disepakati.
- Membuat dua versi file untuk menghindari konflik.
- Mengubah skema database sepihak.
- Mengedit file milik orang lain tanpa koordinasi.
- Menyalin logika JavaScript prototipe untuk data utama.
- Menampilkan angka dummy agar demo terlihat penuh.
- Menghapus akun untuk “mengeluarkan” pengguna.
- Menganggap fitur selesai tanpa diuji orang lain.

---

## 14. Ringkasan satu paragraf

**Orang A** membangun fondasi yang membuat sistem bisa dipercaya: database,
login, hak akses, dan panel Admin. **Orang B** membangun pengalaman yang membuat
sistem berguna: lima langkah siswa, berbagi catatan, dan panel Guru BK. Keduanya
bekerja paralel dengan kontrak yang jelas, file yang tidak tumpang tindih, dan
integrasi harian. Targetnya bukan sekadar tampil pada Rabu, tetapi **benar-benar
bisa dipakai dan dipahami Cindy**.
