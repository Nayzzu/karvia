# 03 — Rencana Database

Database: **`karvia_db`** (MySQL/MariaDB, InnoDB, `utf8mb4`, `utf8mb4_unicode_ci`).
Semua waktu memakai zona **Asia/Jakarta**.

File `database/database.sql` berisi struktur dan data demo. Struktur terdiri dari 12 tabel; data demo tetap fiktif. File SQL ini dirancang untuk database kosong. Setiap perubahan skema **wajib** memperbarui dokumen ini.

---

## 1. Prinsip perancangan

1. Satu tabel = satu hal yang jelas (sekolah, pengguna, jurnal, dst.).
2. Nama tabel dan kolom berbahasa Indonesia, `snake_case`.
3. Setiap siswa hanya punya **satu** data refleksi, satu perbandingan aktif, dan
   satu rencana aktif. Jurnal boleh banyak (riwayat).
4. Data berbagi ke Guru BK disimpan terpisah, bukan sekadar flag di tampilan.
5. Akun **tidak dihapus** saat “dikeluarkan”; cukup `status = nonaktif`.
6. Isi jurnal/refleksi tidak diduplikasi ke tabel rekap. Rekap dihitung dengan query.
7. Admin tidak memiliki `sekolah_id`; Guru BK dan Siswa wajib memiliki `sekolah_id`.

---

## 2. Relasi utama

```
sekolah (1) ───< (N) pengguna
                      │
                      ├── role = admin      → sekolah_id NULL
                      ├── role = guru_bk    → sekolah_id wajib
                      └── role = siswa      → sekolah_id wajib
                                              │
        ┌─────────────────────────────────────┼──────────────────────────┐
        │                                     │                          │
  (1) refleksi_diri                    (1) pilihan_banding         (1) rencana_karier
  (N) jurnal_refleksi                  (N) profesi_disimpan        (1) berbagi_catatan
                                                                        │
                                                                  (N) tindak_lanjut_bk
profesi (1) ───< (N) profesi_disimpan >─── (1) pengguna (siswa)

sekolah (1) ───< (N) berbagi_catatan   (sekolah saat siswa membagikan)
pengguna (admin) ───< pembuatan akun (dibuat_oleh)
pengguna (guru)  ───< tindak_lanjut_bk
```

Catatan:

- `berbagi_catatan` satu baris per siswa dan menyimpan `sekolah_id` **saat
  dibagikan**. Bagikan ulang memperbarui waktu, sekolah, dan mengembalikan status
  ke `baru`; riwayat tindak lanjut tetap tersimpan.
- Guru BK melihat data siswa hanya jika `berbagi_catatan.sekolah_id` sama dengan
  sekolahnya **dan** siswa masih berada di sekolah itu. Dengan begitu, siswa yang
  pindah sekolah tidak otomatis terlihat oleh Guru BK baru sampai ia membagikan ulang.
- `tindak_lanjut_bk` terhubung ke `berbagi_catatan` dan Guru BK pembuatnya.

---

## 3. Daftar tabel

| No | Tabel | Fungsi |
|----|-------|--------|
| 1 | `sekolah` | Data satuan pendidikan |
| 2 | `pengguna` | Akun Admin, Guru BK, Siswa |
| 3 | `refleksi_diri` | Langkah 1 — Kenali Diriku |
| 4 | `pilihan_banding` | Langkah 3 — Bandingkan Pilihan |
| 5 | `rencana_karier` | Langkah 4 — Rencana Langkahku |
| 6 | `jurnal_refleksi` | Langkah 5 — Jurnal (banyak entri) |
| 7 | `profesi` | Katalog profesi untuk Jelajah Profesi |
| 8 | `profesi_disimpan` | Bookmark profesi per siswa |
| 9 | `berbagi_catatan` | Bukti siswa membagikan catatan ke Guru BK |
| 10 | `tindak_lanjut_bk` | Catatan tindak lanjut Guru BK |
| 11 | `log_aktivitas` | Jejak aktivitas penting (login, kelola akun, bagikan) |
| 12 | `artikel` | Artikel edukasi publik yang dibuat Guru BK |

---

## 4. Rincian kolom

Tipe ditulis ringkas. Semua tabel memiliki `id BIGINT UNSIGNED AUTO_INCREMENT`
kecuali tabel penghubung yang tetap memakai `id` sendiri agar mudah dirawat.

### 4.1 `sekolah`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `nama` | VARCHAR(150) | NOT NULL | Nama sekolah |
| `npsn` | VARCHAR(20) | NULL, UNIQUE | Boleh kosong saat demo |
| `jenjang` | ENUM('SMA','MA') | NOT NULL, default 'SMA' | |
| `alamat` | TEXT | NULL | |
| `kota` | VARCHAR(100) | NULL | |
| `provinsi` | VARCHAR(100) | NULL | |
| `status` | ENUM('aktif','nonaktif') | NOT NULL, default 'aktif' | Sekolah nonaktif tidak muncul di form akun baru |
| `dibuat_pada` | DATETIME | NOT NULL | |
| `diperbarui_pada` | DATETIME | NULL | |

Index: `status`, `nama`.

### 4.2 `pengguna`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `sekolah_id` | BIGINT UNSIGNED | NULL, FK → `sekolah.id` RESTRICT | NULL hanya untuk Admin |
| `nama_lengkap` | VARCHAR(150) | NOT NULL | |
| `username` | VARCHAR(50) | NOT NULL, UNIQUE | Huruf kecil, tanpa spasi |
| `password_hash` | VARCHAR(255) | NOT NULL | Hasil `password_hash()` |
| `role` | ENUM('admin','guru_bk','siswa') | NOT NULL | |
| `status` | ENUM('aktif','nonaktif') | NOT NULL, default 'aktif' | |
| `nisn` | VARCHAR(20) | NULL | Khusus siswa |
| `nip` | VARCHAR(30) | NULL | Khusus Guru BK |
| `kelas` | VARCHAR(20) | NULL | Khusus siswa, mis. `X MIA 1` |
| `jurusan` | VARCHAR(50) | NULL | Khusus siswa |
| `no_telepon` | VARCHAR(20) | NULL | Opsional |
| `last_login_at` | DATETIME | NULL | Diisi saat login berhasil |
| `terakhir_aktif_pada` | DATETIME | NULL | Diperbarui berkala saat sesi aktif; NULL saat logout |
| `login_count` | INT UNSIGNED | NOT NULL, default 0 | |
| `dibuat_oleh` | BIGINT UNSIGNED | NULL, FK → `pengguna.id` SET NULL | Admin pembuat akun |
| `dibuat_pada` | DATETIME | NOT NULL | |
| `diperbarui_pada` | DATETIME | NULL | |

Index: `sekolah_id`, `role`, `status`, `last_login_at`, `terakhir_aktif_pada`.
Aturan aplikasi: `role = admin` → `sekolah_id` NULL; selain admin → wajib terisi.
Aturan aplikasi: username hanya huruf kecil, angka, titik, garis bawah (3–50 karakter).

### 4.3 `refleksi_diri` (satu baris per siswa)

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `siswa_id` | BIGINT UNSIGNED | NOT NULL, UNIQUE, FK → `pengguna.id` CASCADE | |
| `mapel_favorit` | JSON | NULL | Array pilihan mapel/bidang |
| `kekuatan` | JSON | NULL | Array kekuatan diri |
| `catatan_minat` | TEXT | NULL | Tulisan bebas siswa |
| `dibuat_pada` | DATETIME | NOT NULL | |
| `diperbarui_pada` | DATETIME | NULL | |

Pilihan mapel/kekuatan disimpan sebagai JSON karena hanya ditampilkan kembali,
tidak perlu dicari per item. Jika nanti perlu analisis statistik, bisa dinormalisasi.

### 4.4 `pilihan_banding` (satu baris per siswa)

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `siswa_id` | BIGINT UNSIGNED | NOT NULL, UNIQUE, FK → `pengguna.id` CASCADE | |
| `opsi_a` | VARCHAR(150) | NOT NULL | Nama profesi/jurusan A |
| `catatan_a` | TEXT | NULL | Kelebihan/tantangan A |
| `opsi_b` | VARCHAR(150) | NOT NULL | Nama profesi/jurusan B |
| `catatan_b` | TEXT | NULL | Kelebihan/tantangan B |
| `kriteria` | JSON | NULL | Array kriteria, mis. `["Kesesuaian Minat","Biaya & Durasi Studi"]` |
| `dibuat_pada` | DATETIME | NOT NULL | |
| `diperbarui_pada` | DATETIME | NULL | |

### 4.5 `rencana_karier` (satu baris per siswa)

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `siswa_id` | BIGINT UNSIGNED | NOT NULL, UNIQUE, FK → `pengguna.id` CASCADE | |
| `tujuan` | VARCHAR(255) | NOT NULL | Tujuan sementara |
| `langkah_1` | VARCHAR(255) | NOT NULL | Langkah kecil minggu ini |
| `langkah_2` | VARCHAR(255) | NOT NULL | Langkah kecil minggu ini |
| `dibuat_pada` | DATETIME | NOT NULL | |
| `diperbarui_pada` | DATETIME | NULL | |

### 4.6 `jurnal_refleksi` (banyak baris per siswa)

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `siswa_id` | BIGINT UNSIGNED | NOT NULL, FK → `pengguna.id` CASCADE | |
| `hal_baru` | TEXT | NULL | Jawaban pertanyaan 1 |
| `hambatan` | TEXT | NULL | Jawaban pertanyaan 2 |
| `dukungan` | TEXT | NULL | Jawaban pertanyaan 3 |
| `dibuat_pada` | DATETIME | NOT NULL | Waktu entri dibuat |

Index: `(siswa_id, dibuat_pada)`.
Minimal salah satu dari tiga kolom teks terisi (validasi aplikasi).

### 4.7 `profesi`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `nama` | VARCHAR(150) | NOT NULL | |
| `gambar` | VARCHAR(255) | NULL | Path file foto/ilustrasi profesi |
| `kategori` | ENUM('kesehatan','teknologi','pendidikan','bisnis','lainnya') | NOT NULL | Sesuai filter prototipe |
| `deskripsi` | TEXT | NULL | Tugas utama |
| `kemampuan` | TEXT | NULL | Kemampuan yang dibutuhkan |
| `jalur_pendidikan` | VARCHAR(255) | NULL | Mis. S1 Kedokteran |
| `sumber` | VARCHAR(255) | NULL | Rujukan konten |
| `urutan` | INT | NOT NULL, default 0 | Urutan tampil |
| `status` | ENUM('aktif','nonaktif') | NOT NULL, default 'aktif' | |
| `dibuat_pada` | DATETIME | NOT NULL | |
| `diperbarui_pada` | DATETIME | NULL | |

Index: `kategori`, `status`, `urutan`.

### 4.8 `profesi_disimpan`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `siswa_id` | BIGINT UNSIGNED | NOT NULL, FK → `pengguna.id` CASCADE | |
| `profesi_id` | BIGINT UNSIGNED | NOT NULL, FK → `profesi.id` CASCADE | |
| `dibuat_pada` | DATETIME | NOT NULL | |

Unique: `(siswa_id, profesi_id)` agar tidak dobel.
Index: `profesi_id`.

### 4.9 `berbagi_catatan` (satu baris per siswa)

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `siswa_id` | BIGINT UNSIGNED | NOT NULL, UNIQUE, FK → `pengguna.id` CASCADE | |
| `sekolah_id` | BIGINT UNSIGNED | NOT NULL, FK → `sekolah.id` RESTRICT | Sekolah siswa **saat membagikan** |
| `pesan_siswa` | TEXT | NULL | Pesan opsional saat membagikan |
| `status` | ENUM('baru','dibaca','ditindaklanjuti') | NOT NULL, default 'baru' | |
| `dibagikan_pada` | DATETIME | NOT NULL | Diperbarui saat bagikan ulang |
| `dibaca_pada` | DATETIME | NULL | Diisi saat Guru BK membuka detail |
| `diperbarui_pada` | DATETIME | NULL | |

Index: `sekolah_id`, `status`, `dibagikan_pada`.
Guru BK memfilter `berbagi_catatan.sekolah_id = sekolah Guru BK` **dan** memastikan
`pengguna.sekolah_id` siswa masih sama. Dengan demikian:
- siswa pindah sekolah → data berbagi lama tidak terlihat oleh Guru BK lama
  maupun baru;
- siswa membagikan ulang → `sekolah_id` diperbarui ke sekolah saat ini.
Bagikan ulang: perbarui `dibagikan_pada` dan `sekolah_id`, set `status = 'baru'`,
kosongkan `dibaca_pada`. Riwayat tindak lanjut tidak dihapus.

### 4.10 `tindak_lanjut_bk`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `berbagi_id` | BIGINT UNSIGNED | NOT NULL, FK → `berbagi_catatan.id` CASCADE | |
| `guru_id` | BIGINT UNSIGNED | NOT NULL, FK → `pengguna.id` RESTRICT | |
| `catatan` | TEXT | NOT NULL | Isi tindak lanjut |
| `dibuat_pada` | DATETIME | NOT NULL | |

Index: `(berbagi_id, dibuat_pada)`, `guru_id`.
Saat tindak lanjut disimpan, `berbagi_catatan.status` diubah menjadi
`ditindaklanjuti` (kecuali sudah lebih tinggi).

### 4.11 `log_aktivitas`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `pengguna_id` | BIGINT UNSIGNED | NULL, FK → `pengguna.id` SET NULL | Pelaku |
| `aksi` | VARCHAR(50) | NOT NULL | `login`, `logout`, `buat_pengguna`, `nonaktif_pengguna`, `bagikan_catatan`, `tindak_lanjut`, dll. |
| `keterangan` | TEXT | NULL | Ringkasan yang aman ditampilkan |
| `ip_address` | VARCHAR(45) | NULL | IPv4/IPv6 |
| `dibuat_pada` | DATETIME | NOT NULL | |

Index: `(pengguna_id, dibuat_pada)`, `aksi`, `dibuat_pada`.
Jangan pernah mencatat password, token, atau isi jurnal mentah di sini.

### 4.12 `artikel`

| Kolom | Tipe | Aturan | Keterangan |
|-------|------|--------|------------|
| `id` | BIGINT UNSIGNED | PK, AI | |
| `guru_id` | BIGINT UNSIGNED | NOT NULL, FK → `pengguna.id` RESTRICT | Akun Guru BK pembuat |
| `nama_penulis` | VARCHAR(150) | NOT NULL | Snapshot nama akun saat publikasi; tetap tersedia bila akun berubah/nonaktif |
| `judul` | VARCHAR(200) | NOT NULL | Judul kartu dan halaman artikel |
| `gambar` | VARCHAR(255) | NOT NULL | Nama file acak tersimpan di `uploads/artikel/`, bukan URL kiriman pengguna |
| `isi` | MEDIUMTEXT | NOT NULL | Teks biasa; HTML tidak dirender |
| `diterbitkan_pada` | DATETIME | NOT NULL | Waktu publikasi dari server |
| `diperbarui_pada` | DATETIME | NULL | Waktu perubahan terakhir |

Index: `(diterbitkan_pada)`, `(guru_id, diterbitkan_pada)`.
Artikel langsung publik dan berlaku lintas sekolah. Guru BK hanya dapat mengubah/menghapus baris dengan `guru_id` sama dengan akun login. Penghapusan artikel milik sendiri permanen; penghapusan akun tidak menghapus artikel (FK RESTRICT juga melindungi riwayat penulis). Ringkasan landing dihasilkan dari `isi` saat dibaca, tidak disimpan terpisah.

---

## 5. Aturan integritas & penghapusan

| Aturan | Perilaku |
|--------|----------|
| Sekolah memiliki pengguna | Sekolah tidak boleh dihapus; nonaktifkan atau pindahkan pengguna dulu |
| Pengguna dinonaktifkan | Data tetap ada; login ditolak |
| Pengguna benar-benar dihapus | Tidak tersedia di MVP; jika nanti ada, data siswa ikut terhapus (CASCADE) dengan konfirmasi ganda |
| Admin menonaktifkan diri sendiri | Dilarang |
| Siswa pindah sekolah | Data berbagi lama tidak terlihat oleh Guru BK lama maupun baru; siswa membagikan ulang agar terhubung ke sekolah baru |
| Guru BK pindah sekolah | Riwayat tindak lanjut tetap tercatat atas nama guru tersebut |
| Bagikan ulang | Status kembali `baru`, waktu & sekolah diperbarui, tindak lanjut lama tetap ada |
| Duplikat username | Ditolak database + pesan validasi |
| Role/sekolah tidak cocok | Ditolak validasi aplikasi |

Karena akun tidak dihapus, gunakan `status` sebagai kontrol akses utama.
Hindari `DELETE FROM pengguna` di kode MVP.

---

## 6. Definisi metrik dashboard

Angka harus diberi label yang jujur agar tidak menyesatkan seperti prototipe.

| Metrik | Cara menghitung | Label yang disarankan |
|--------|-----------------|------------------------|
| Total sekolah | `COUNT(*) FROM sekolah WHERE status='aktif'` | “Sekolah aktif” |
| Total pengguna | `COUNT(*) FROM pengguna` | “Akun terdaftar” |
| Total siswa | `COUNT(*) WHERE role='siswa'` | “Siswa terdaftar” |
| Siswa aktif | `role='siswa' AND status='aktif'` | “Siswa dengan akses aktif” |
| Total Guru BK | `COUNT(*) WHERE role='guru_bk'` | “Guru BK terdaftar” |
| Pernah login | `last_login_at IS NOT NULL` | “Pernah masuk” |
| Sedang online | `terakhir_aktif_pada >= NOW() - 5 menit` | “Sedang online” |
| Aktif 30 hari | `last_login_at >= NOW() - 30 hari` | “Aktif 30 hari terakhir” |
| Catatan baru | `berbagi_catatan.status='baru'` | “Catatan baru” |
| Catatan ditindaklanjuti | `status='ditindaklanjuti'` | “Sudah ditindaklanjuti” |
| Siswa per sekolah | join `pengguna` group by `sekolah_id` | “Siswa” |
| Guru per sekolah | join `pengguna` role guru group by sekolah | “Guru BK” |

Untuk angka **per sekolah**, hitung catatan hanya jika
`berbagi_catatan.sekolah_id = sekolah.id` **dan** `pengguna.sekolah_id = sekolah.id`.
Ini mencegah data berbagi lama ikut terhitung setelah siswa pindah sekolah.

Jangan memakai istilah “siswa aktif” untuk sekadar jumlah akun. Jika ingin
mengukur pemakaian, gunakan “pernah login” atau “aktif 30 hari”.

---

## 7. Rencana file SQL

Project ini **tidak memakai migration/seeder**. Seluruh database disimpan dalam
**satu file** di folder `database/`:

| File | Isi | Kapan dijalankan |
|------|-----|------------------|
| `database/database.sql` | **Bagian A:** struktur semua tabel, index, dan foreign key. **Bagian B:** data contoh (2 sekolah, akun demo, profesi, data karier) | Sekali, saat database masih kosong |
| `database/backup_YYYY-MM-DD.sql` | Salinan database saat backup | Hanya sebelum demo/perubahan besar |

Satu file dipilih agar proses impor cukup satu langkah dan mudah diulang di
komputer lain. Bagian A dan B diberi penanda komentar yang jelas.

Urutan pembuatan tabel **di bagian A `database.sql`** (agar foreign key valid):

1. `sekolah`
2. `pengguna`
3. `refleksi_diri`
4. `pilihan_banding`
5. `rencana_karier`
6. `jurnal_refleksi`
7. `profesi`
8. `profesi_disimpan`
9. `berbagi_catatan`
10. `tindak_lanjut_bk`
11. `log_aktivitas`
12. `artikel`

Cara menjalankan (setelah database `karvia_db` dibuat):

```
mysql -u root karvia_db < database/database.sql
```

Atau lewat HeidiSQL/phpMyAdmin: buat database `karvia_db`, lalu import
`database/database.sql`.

**Catatan:** `database.sql` dirancang untuk database kosong. Jangan menjalankannya
berulang di database yang sudah berisi data tanpa backup, karena definisi tabel
dapat bentrok. Jika ingin mengulang dari awal, hapus database lalu buat ulang.

---

## 8. Rencana data contoh (bagian B `database.sql`)

Data demo bertujuan menguji alur dasar dan tampilan dengan jumlah kecil. Semua
nama sekolah, nama akun, dan isi catatan bersifat fiktif; bukan klaim afiliasi
atau identitas orang nyata. Kombinasi sederhana `admin/admin`, `guru/guru`, dan
`siswa/siswa` hanya untuk demo lokal; password tetap disimpan sebagai hash.

### 8.1 Dua sekolah demo

Nama dan lokasi berikut sengaja generik/fiktif, tanpa NPSN, dan bukan afiliasi
sekolah nyata.

| Nama | Jenjang | Kota | Provinsi |
|------|---------|------|----------|
| Sekolah Demo KARVIA Utara | SMA | Kota Contoh | Provinsi Contoh |
| Sekolah Demo KARVIA Selatan | MA | Kota Contoh | Provinsi Contoh |

### 8.2 Akun demo

Semua nama pengguna adalah label fiktif untuk pengujian, bukan identitas orang. Password sederhana hanya untuk demo lokal; jangan gunakan pada pemakaian nyata.

| Peran | Nama demo | Username | Password demo | Sekolah |
|-------|-----------|----------|---------------|---------|
| Admin | Admin KARVIA | `admin` | `admin` | — |
| Guru BK | Guru BK KARVIA | `guru` | `guru` | Sekolah Demo KARVIA Utara |
| Siswa | Siswa KARVIA | `siswa` | `siswa` | Sekolah Demo KARVIA Utara |
| Admin | Admin Demo KARVIA | `demo.admin` | `Admin123!` | — |
| Guru BK | Guru Demo Utara | `demo.guru1` | `Guru123!` | Sekolah Demo KARVIA Utara |
| Guru BK | Guru Demo Selatan | `demo.guru2` | `Guru123!` | Sekolah Demo KARVIA Selatan |
| Siswa | Siswa Demo Berbagi | `demo.siswa1` | `Siswa123!` | Sekolah Demo KARVIA Utara |
| Siswa | Siswa Demo Privat | `demo.siswa2` | `Siswa123!` | Sekolah Demo KARVIA Utara |
| Siswa | Siswa Demo Kosong | `demo.siswa3` | `Siswa123!` | Sekolah Demo KARVIA Selatan |

**Penting:** akun dan kata sandi ini hanya untuk demo lokal. Ganti/hapus sebelum
pemakaian nyata. Database hanya menyimpan hash kata sandi. Semua `last_login_at`
dibiarkan `NULL` dan `login_count` bernilai `0` agar tidak menggambarkan aktivitas
login fiktif.

### 8.3 Data demo dalam Bagian B

- Katalog berisi 4 profesi inti dari prototipe: Dokter, Software Engineer, Guru BK,
  dan Advokat.
- Siswa Demo Berbagi: satu refleksi, satu perbandingan, satu rencana, satu jurnal,
  satu bookmark, dan satu berbagi berstatus `baru`.
- Siswa Demo Privat: refleksi tersedia, tetapi belum dibagikan untuk menguji privasi.
- Siswa Demo Kosong: tidak memiliki data eksplorasi untuk menguji empty state.
- Semua isi eksplorasi ditandai sebagai data fiktif/demo; jumlahnya sengaja dibatasi.

### 8.4 Isi bagian B dan verifikasi lokal

Urutan insert:

1. `sekolah` — 2 baris fiktif.
2. `pengguna` — 2 Admin, 3 Guru BK, 4 siswa (password sudah di-hash; aktivitas login kosong).
3. `profesi` — 4 profesi inti.
4. Data karier demo terbatas — satu siswa berbagi, satu siswa belum berbagi, dan
   satu siswa tanpa data eksplorasi.

Skema dan data demo diuji pada MySQL 8.0.30 memakai database sementara kosong: 12 tabel, 14 foreign key, 2 sekolah, 9 pengguna, 4 profesi, dan 3 akun login singkat. Database lokal `karvia_db` yang sudah ada tidak diubah; untuk mencoba perubahan, backup lalu impor SQL pada database kosong. Semua akun memiliki `last_login_at = NULL` dan `login_count = 0`.

Bagian B harus dijalankan pada database kosong setelah bagian A. File SQL tidak
menjalankan `TRUNCATE` atau menghapus data yang sudah ada. Untuk memperbarui
instalasi demo yang sudah pernah diimpor, ekspor/backup bila perlu, lalu kosongkan
atau buat ulang database `karvia_db` secara sengaja sebelum mengimpor ulang file.
Jangan menjalankan ulang pada database sekolah nyata. Sebelum pemakaian nyata,
hapus bagian B dan ganti semua password akun.

---

## 9. Backup

Sebelum demo atau perubahan besar:

1. Ekspor database ke `database/backup_YYYY-MM-DD.sql`.
2. Simpan juga salinan folder proyek (zip) tanpa file backup lama.
3. Catat perubahan skema di dokumen ini.

Perintah cadangan (nanti):

```
mysqldump -u root karvia_db > database/backup_2026-11-25.sql
```

---

## 10. Pertanyaan database yang belum final

1. Apakah satu siswa boleh punya banyak rencana/perbandingan (riwayat), atau cukup
   satu data terbaru? **Usulan MVP: satu data terbaru, jurnal tetap banyak.**
2. Apakah Guru BK boleh mengubah data siswa? **Usulan: tidak; hanya menambah
   tindak lanjut.**
3. Apakah perlu tabel `pengaturan` untuk nama aplikasi/logo? **Usulan: belum.**
4. Apakah perlu import siswa dari Excel? **Usulan: setelah MVP.**
5. Apakah isi jurnal perlu enkripsi tambahan? **Usulan: belum untuk MVP lokal,
   tetapi akses dibatasi ketat dan backup dijaga.**
