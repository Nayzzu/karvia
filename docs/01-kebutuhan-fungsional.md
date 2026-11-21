# 01 — Kebutuhan Fungsional

Dokumen ini menjawab: **siapa boleh melakukan apa, fitur apa yang wajib ada, dan
bagaimana kita tahu fitur itu sudah jadi.**

---

## 1. Peran dan hak akses

### 1.1 Definisi peran

| Peran | Kode | Terikat sekolah? | Tugas utama |
|-------|------|------------------|-------------|
| Admin | `admin` | Tidak (global) | Memantau pemakaian, mengelola sekolah, akun, dan akses |
| Guru BK | `guru_bk` | Ya, tepat 1 sekolah | Melihat catatan siswa sekolahnya yang dibagikan & menindaklanjuti |
| Siswa | `siswa` | Ya, tepat 1 sekolah | Mengisi eksplorasi karier & membagikan catatan ke Guru BK |

### 1.2 Matriks izin

| Aksi | Admin | Guru BK | Siswa |
|------|:-----:|:-------:|:-----:|
| Login / logout | ✅ | ✅ | ✅ |
| Daftar akun sendiri | ❌ | ❌ | ✅ (siswa saja) |
| Ubah username sendiri | ❌ | ❌ | ✅ |
| Ganti password sendiri | ✅ | ✅ | ✅ |
| Lihat dashboard | ✅ global | ✅ sekolah sendiri | ✅ pribadi |
| Lihat daftar semua sekolah | ✅ | ❌ | ❌ |
| Tambah/ubah sekolah | ✅ | ❌ | ❌ |
| Lihat daftar semua pengguna | ✅ | ❌ | ❌ |
| Tambah/ubah akun pengguna | ✅ | ❌ | ❌ |
| Aktif/nonaktifkan akun | ✅ | ❌ | ❌ |
| Reset password pengguna | ✅ | ❌ | ❌ |
| Lihat jumlah siswa/guru per sekolah | ✅ | ✅ sekolah sendiri | ❌ |
| Lihat daftar siswa | ✅ (data akun saja) | ✅ hanya yang membagikan catatan | ❌ |
| Lihat isi refleksi/jurnal siswa | ❌ (default) | ✅ hanya yang dibagikan | ✅ milik sendiri |
| Isi/ubah data eksplorasi | ❌ | ❌ | ✅ milik sendiri |
| Bagikan catatan ke Guru BK | ❌ | ❌ | ✅ milik sendiri |
| Tandai catatan dibaca | ❌ | ✅ sekolah sendiri | ❌ |
| Beri catatan tindak lanjut | ❌ | ✅ sekolah sendiri | ❌ |
| Cetak ringkasan | ✅ rekap | ✅ catatan siswa | ✅ milik sendiri |

**Catatan privasi:** Admin **tidak** membaca isi jurnal/refleksi siswa secara default.
Admin hanya melihat data akun, sekolah, dan angka agregat. Jika suatu saat perlu
bantuan teknis, akses isi catatan harus menjadi keputusan terpisah dan tercatat.

### 1.3 Aturan cakupan data (wajib di kode)

- Setiap query Guru BK **wajib** difilter `sekolah_id = sekolah Guru BK yang login`.
- Setiap query Siswa **wajib** difilter `siswa_id = id siswa yang login`.
- Guru BK tidak boleh membuka ID siswa dari sekolah lain, walaupun URL-nya diubah.
- Admin boleh melihat semua **akun**, tetapi bukan isi jurnal pribadi.
- Siswa hanya melihat data miliknya; tidak ada daftar siswa lain.

---

## 2. Kebutuhan autentikasi & akun

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| AUTH-01 | Login memakai **username + password** yang terdaftar | P0 |
| AUTH-02 | Peran ditentukan otomatis dari akun (tidak ada pilihan peran di login) | P0 |
| AUTH-03 | Password disimpan sebagai hash (`password_hash`), bukan teks biasa | P0 |
| AUTH-04 | Akun `nonaktif` ditolak dengan pesan yang jelas | P0 |
| AUTH-05 | Pesan gagal login bersifat umum: “Username atau password salah” | P0 |
| AUTH-06 | Session diperbarui saat login (`session_regenerate_id`) | P0 |
| AUTH-07 | Logout menghapus session dan kembali ke halaman login | P0 |
| AUTH-08 | Setiap halaman internal memeriksa login dan peran | P0 |
| AUTH-09 | Pengguna dapat mengganti password sendiri | P1 |
| AUTH-10 | Catat `last_login_at` dan `login_count` setiap login berhasil | P0 |
| AUTH-11 | Siswa dapat mendaftar sendiri pada sekolah aktif; role dipatok `siswa` di server | P1 |
| AUTH-12 | Siswa dapat mengubah username sendiri setelah verifikasi password saat ini | P1 |

**Kebijakan akun:** pendaftaran mandiri dibatasi untuk siswa dan hanya mengaitkan
akun ke sekolah aktif yang terdaftar. Admin dan Guru BK tetap dibuat Admin. Admin
sekolah dapat menonaktifkan akun yang tidak sesuai; form publik tidak menerima role.

---

## 3. Kebutuhan Admin

### 3.1 Dashboard

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| ADM-01 | Kartu total sekolah aktif | P0 |
| ADM-02 | Kartu total pengguna terdaftar (dengan rincian per peran) | P0 |
| ADM-03 | Kartu total siswa dan total Guru BK | P0 |
| ADM-04 | Kartu “pengguna pernah login” dan “aktif 30 hari” | P0 |
| ADM-05 | Tabel rekap per sekolah: nama sekolah, jumlah siswa, jumlah Guru BK, jumlah catatan dibagikan, aktivitas terakhir | P0 |
| ADM-06 | Daftar 5–10 pengguna terbaru / aktivitas login terbaru | P1 |
| ADM-07 | Semua angka dihitung dari database, bukan hardcoded | P0 |

### 3.2 Manajemen sekolah

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| SCH-01 | Daftar sekolah dengan pencarian | P0 |
| SCH-02 | Tambah sekolah (nama wajib; NPSN, jenjang, alamat, kota, provinsi opsional) | P0 |
| SCH-03 | Ubah data sekolah | P0 |
| SCH-04 | Aktif/nonaktifkan sekolah | P1 |
| SCH-05 | Detail sekolah: profil + jumlah siswa + jumlah Guru BK + daftar akun terkait | P0 |
| SCH-06 | Larang hapus sekolah yang masih memiliki pengguna | P1 |
| SCH-07 | Lima sekolah demo tersedia setelah impor `dummy_karvia.sql` + `dummy_karvia2.sql` | P0 |

### 3.3 Manajemen pengguna & akses

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| USR-01 | Daftar pengguna: nama, username, peran, sekolah, status, terakhir login | P0 |
| USR-02 | Filter: kata kunci, peran, sekolah, status | P0 |
| USR-03 | Tambah akun (peran, sekolah, nama, username, password awal) | P0 |
| USR-04 | Ubah data akun (nama, sekolah, kelas/jurusan, status) | P0 |
| USR-05 | Reset password oleh Admin | P0 |
| USR-06 | Nonaktifkan akun = mencabut akses login tanpa menghapus riwayat | P0 |
| USR-07 | Aktifkan kembali akun | P0 |
| USR-08 | Username unik; validasi duplikat dengan pesan jelas | P0 |
| USR-09 | Admin tidak dapat menonaktifkan akunnya sendiri | P0 |
| USR-10 | “Keluarkan pengguna” memakai konfirmasi; default nonaktif, bukan hapus | P0 |
| USR-11 | Hapus permanen hanya untuk akun tanpa data & dengan konfirmasi ganda | P2 |
| USR-12 | Ekspor daftar pengguna ke CSV | P1 |

**Kebijakan “keluarkan orang dari web” (usulan default):**
nonaktifkan akun. Data tetap tersimpan, riwayat konseling tidak hilang, dan Admin
bisa mengaktifkan kembali. Hapus permanen tidak ditawarkan pada MVP.

### 3.4 Rekap

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| REP-01 | Jumlah siswa per sekolah | P0 |
| REP-02 | Jumlah Guru BK per sekolah | P0 |
| REP-03 | Jumlah catatan dibagikan per sekolah | P0 |
| REP-04 | Status akses pengguna per sekolah (aktif/nonaktif) | P1 |
| REP-05 | Cetak/ekspor rekap | P2 |

---

## 4. Kebutuhan Guru BK

Artikel edukasi adalah konten publik lintas sekolah. Ringkasan kartu dipotong otomatis dari isi; halaman baca menampilkan gambar, judul, nama penulis, tanggal terbit, dan isi teks. Isi artikel ditampilkan sebagai teks biasa yang di-escape, bukan HTML aktif.

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| GRU-01 | Dashboard hanya untuk sekolah sendiri | P0 |
| GRU-02 | Statistik nyata: total siswa sekolah, catatan baru, catatan ditindaklanjuti | P0 |
| GRU-03 | Daftar siswa yang membagikan catatan (bukan semua siswa) | P0 |
| GRU-04 | Pencarian & filter status catatan (baru/dibaca/ditindaklanjuti) | P1 |
| GRU-05 | Detail catatan: identitas, minat, kekuatan, bookmark profesi, perbandingan, rencana, jurnal, pesan siswa | P0 |
| GRU-06 | Tandai catatan “dibaca” saat detail dibuka | P1 |
| GRU-07 | Tambah catatan tindak lanjut | P0 |
| GRU-08 | Riwayat tindak lanjut tampil di detail | P1 |
| GRU-09 | Cetak ringkasan siswa | P1 |
| GRU-10 | Tidak dapat melihat siswa/guru sekolah lain | P0 |
| GRU-11 | Tidak dapat membuat/menonaktifkan akun | P0 |
| GRU-12 | Pengumuman/siaran pesan | P2 (belum MVP) |
| GRU-13 | Membuat artikel edukasi dengan judul, gambar JPG/PNG/WebP maks. 2 MB, dan isi teks; nama penulis otomatis dari akun Guru BK | P1 (tambahan disetujui) |
| GRU-14 | Artikel langsung terbit untuk publik seluruh sekolah; Guru BK hanya dapat mengubah/menghapus artikel sendiri | P1 (tambahan disetujui) |

**Perubahan dari prototipe:** kartu “Jadwal Konsultasi 6 Permintaan” dan
“Status Sistem Aktif 100%” **dihapus** karena tidak ada fitur jadwal dan angka itu
menyesatkan. Diganti dengan metrik yang benar-benar dihitung.

---

## 5. Kebutuhan Siswa

| ID | Kebutuhan | Prioritas |
|----|-----------|:---------:|
| SIS-01 | Beranda & pengantar dengan pintasan lima langkah | P0 |
| SIS-02 | Kenali Diriku: mapel favorit (bisa lebih dari satu), kekuatan, catatan minat; tersimpan | P0 |
| SIS-03 | Jelajah Profesi: kartu profesi, filter kategori, simpan/batalkan simpan | P0 |
| SIS-04 | Jalur Setelah Lulus: konten informasi statis dari prototipe | P0 |
| SIS-05 | Bandingkan Pilihan: opsi A, opsi B, catatan kriteria; tersimpan | P0 |
| SIS-06 | Rencana Langkahku: tujuan + dua langkah kecil; tersimpan | P0 |
| SIS-07 | Jurnal Refleksi: hal baru, hambatan, dukungan; tersimpan sebagai entri baru | P0 |
| SIS-08 | Ringkasan Konsultasi menampilkan data terbaru dari database | P0 |
| SIS-09 | Tombol “Bagikan ke Guru BK” benar-benar menyimpan data berbagi | P0 |
| SIS-10 | Status berbagi terlihat (“Terakhir dibagikan …”, status baca/tindak lanjut) | P1 |
| SIS-11 | Cetak ringkasan konsultasi | P0 |
| SIS-12 | Ubah password sendiri | P1 |
| SIS-13 | Data tetap ada setelah logout/refresh | P0 |
| SIS-14 | Indikator langkah mana yang sudah diisi | P1 |

**Aturan berbagi:**
- Sebelum menekan bagikan, Guru BK **tidak** melihat isi catatan siswa.
- Saat bagikan, sistem menyimpan satu baris `berbagi_catatan` berisi siswa,
  sekolah, waktu, dan pesan opsional.
- Siswa boleh membagikan ulang; daftar Guru BK memakai data berbagi terbaru.
- Data yang terlihat Guru BK adalah data terkini milik siswa, bukan salinan beku.

---

## 6. Kebutuhan non-fungsional

### 6.1 Keamanan

- Password di-hash; tidak pernah ditampilkan/di-log.
- Semua query memakai prepared statement (PDO).
- Semua output teks pengguna di-escape (`htmlspecialchars`).
- Form yang mengubah data memakai token CSRF.
- Session: `HttpOnly`, `SameSite=Lax`; regenerasi saat login.
- Otorisasi diperiksa di server, bukan hanya menyembunyikan tombol.
- File konfigurasi dan folder `config/`, `includes/`, `database/`, `docs/` tidak boleh
  diakses langsung dari browser (lihat `02`).
- Pesan error database tidak ditampilkan ke pengguna; dicatat ke log.

### 6.2 Privasi

- Isi jurnal adalah data pribadi; hanya siswa dan Guru BK sekolahnya (setelah
  dibagikan) yang dapat melihat.
- Admin melihat angka, bukan isi jurnal.
- Cetak ringkasan hanya oleh pemilik data atau Guru BK yang berhak.
- Tidak ada data dummy yang menyamar sebagai data nyata.

### 6.3 Kegunaan (untuk Cindy & pengguna awam)

- Semua label, pesan sukses, dan pesan error berbahasa Indonesia.
- Konfirmasi sebelum tindakan berisiko (nonaktifkan akun, bagikan catatan).
- Empty state jelas, misalnya “Belum ada siswa yang membagikan catatan.”
- Jangan menampilkan tombol yang belum berfungsi sebagai seolah-olah berhasil.
- Navigasi konsisten: sidebar per peran, breadcrumb sederhana di halaman detail.

### 6.4 Kinerja & kompatibilitas

- Data demo berisi lima sekolah dan ratusan akun; tetap gunakan query terindeks, tanpa optimasi khusus skala besar.
- Daftar pengguna memakai pencarian dan (bila perlu) pagination 25 baris.
- Berjalan di Chrome/Edge/Firefox modern, desktop dan mobile.
- Halaman utama dimuat < 2 detik pada data demo.

### 6.5 Pemeliharaan & pembelajaran

- Struktur file sederhana, satu tanggung jawab per file.
- Nama fungsi dan tabel berbahasa Indonesia.
- Komentar menjelaskan “mengapa”, bukan sekadar “apa”.
- Setiap perubahan skema dicatat di dokumentasi.
- Sediakan penjelasan alur data dalam bahasa non-teknis untuk Cindy.

---

## 7. Kriteria penerimaan utama (demo)

| No | Skenario | Hasil yang diharapkan |
|----|----------|-----------------------|
| 1 | Login admin dengan akun contoh | Masuk ke dashboard admin, melihat 5 sekolah setelah impor kedua file dummy |
| 2 | Tambah Guru BK baru di SMA contoh | Akun muncul di daftar, bisa login |
| 3 | Nonaktifkan siswa | Siswa gagal login dengan pesan “akun dinonaktifkan” |
| 4 | Aktifkan kembali siswa | Siswa bisa login lagi |
| 5 | Login Guru BK sekolah A | Hanya melihat data siswa sekolah A |
| 6 | Siswa mengisi lima langkah lalu bagikan | Data tersimpan setelah logout |
| 7 | Guru BK membuka dashboard | Catatan siswa muncul dengan status “baru” |
| 8 | Guru BK membuka detail & menambah tindak lanjut | Status berubah, riwayat tersimpan |
| 9 | Admin melihat rekap | Jumlah siswa/Guru BK sesuai data, bukan angka dummy |
| 10 | Refresh/restart Laragon | Semua data tetap ada |

---

## 8. Pertanyaan yang masih perlu diputuskan

Rincian dan usulan default ada di [`08-keputusan-terbuka.md`](08-keputusan-terbuka.md).
Yang paling memengaruhi pekerjaan:

1. Apakah akun hanya dibuat Admin, atau Guru BK boleh menambah siswa?
2. “Keluarkan pengguna” = nonaktif (disarankan) atau hapus permanen?
3. Login memakai username (disarankan) atau email?
4. Apakah Admin boleh membaca isi jurnal siswa? (disarankan: tidak)
5. Apakah Guru BK boleh melihat **semua** siswa sekolahnya, atau hanya yang dibagikan?
   (disarankan: hanya yang dibagikan)
6. Apakah perlu ekspor CSV/Excel pada MVP?
