# 00 — Ringkasan Proyek

## 1. Identitas

| Item | Keterangan |
|------|------------|
| Nama produk | **KARVIA** (Career Vision & Action) |
| Subjudul | “Langkah Karierku” |
| Jenis | Media digital bimbingan karier untuk siswa SMA/MA |
| Pengembang awal | Chindy Theresia (2411080092) — UIN Raden Intan Lampung |
| Pengguna sistem | Admin, Guru BK, Siswa SMA/MA |
| Cakupan | Multi-sekolah (5 sekolah contoh: Bandung, Yogyakarta, dan Bandar Lampung) |
| Status | Aplikasi PHP native + MySQL aktif dikembangkan; tersedia pendaftaran mandiri siswa, pengelolaan tiga peran, data multi-sekolah, dan artikel edukasi |

> KARVIA adalah **media pendukung bimbingan karier**, bukan tes psikologis dan bukan
> alat yang otomatis memvonis pilihan siswa. Pesan ini harus tetap tampil di aplikasi.

---

## 2. Latar belakang

Prototipe `karvia_career_vision_action.html` sudah memiliki tampilan dan alur
eksplorasi karier yang baik. Namun, berdasarkan masukan pengguna (“Bunda”) dan
percakapan dengan Cindy, prototipe tersebut belum memenuhi kebutuhan utama:

1. **Tidak ada Admin** yang dapat memantau siapa saja yang memakai web.
2. **Tidak ada data nyata**: login hanya mengecek password tidak kosong, data siswa
   hanya tersimpan di variabel JavaScript, dan hilang saat refresh/logout.
3. **Dashboard Guru BK penuh angka dummy** (32 siswa, 14 catatan, 6 jadwal, 100%),
   dengan dua baris data contoh yang tidak berubah.
4. **Tidak ada konsep sekolah**, padahal web akan dipakai banyak sekolah.
5. **Tidak ada manajemen akses**: tidak bisa membuat akun, menonaktifkan, atau
   mengeluarkan pengguna.
6. **Tombol “Bagikan ke Guru BK” hanya menampilkan modal**, tidak mengirim data.

Kesimpulan: prototipe tersebut **bukan salah**, tetapi **belum menjadi sistem yang
benar-benar bisa dipakai**. Pekerjaan ini adalah membangun ulang dengan fondasi
akun, peran, sekolah, dan penyimpanan data yang benar.

---

## 3. Tujuan

Membangun aplikasi KARVIA yang:

1. Memiliki **login nyata** berbasis akun terdaftar.
2. Memiliki **tiga peran**: Admin, Guru BK, Siswa.
3. Mendukung **banyak sekolah**; setiap Guru BK dan Siswa terhubung ke satu sekolah.
4. Memberi Admin **dashboard dan rekap nyata**: jumlah pengguna, sekolah, siswa,
   Guru BK, serta status akses.
5. Memungkinkan Admin **mengelola akun dan akses** (buat, ubah, aktif/nonaktif,
   reset password, keluarkan pengguna).
6. Menyimpan data eksplorasi siswa secara **persisten** dan menghubungkannya ke
   Guru BK di sekolah yang sama ketika siswa membagikan catatan.
7. Menjaga pengalaman siswa yang sudah ada: Kenali Diriku, Jelajah Profesi,
   Bandingkan Pilihan, Rencana Langkahku, Jurnal Refleksi, Konsultasi BK.

---

## 4. Ruang lingkup MVP

### Termasuk (P0 — wajib untuk demo Rabu)

- Login/logout dengan username + password, peran otomatis dari akun.
- Tiga peran dan pembatasan akses.
- Entitas sekolah + 5 sekolah contoh (2 awal dan 3 sekolah fiktif Bandar Lampung).
- Admin:
  - dashboard ringkasan nyata;
  - daftar & pencarian pengguna (filter peran, sekolah, status);
  - tambah/ubah akun;
  - aktif/nonaktifkan akun (setara “keluarkan”);
  - reset password;
  - daftar sekolah, tambah/ubah sekolah, detail sekolah (jumlah siswa/guru BK).
- Guru BK:
  - dashboard nyata untuk sekolahnya;
  - daftar catatan siswa yang dibagikan;
  - detail catatan siswa;
  - tandai sudah dibaca / beri catatan tindak lanjut.
- Siswa:
  - lima langkah eksplorasi yang tersimpan di database;
  - simpan profesi (bookmark);
  - bagikan catatan ke Guru BK (benar-benar tersimpan);
  - cetak ringkasan konsultasi.
- Data contoh: 5 sekolah, akun admin/guru/siswa; data tambahan Bandar Lampung ada di `database/dummy_karvia2.sql`.
- Dokumentasi cara menjalankan dan penjelasan alur data untuk Cindy.

### Tidak termasuk (untuk saat ini)

- Pendaftaran mandiri Admin dan Guru BK (hanya siswa yang dapat mendaftar sendiri).
- Aplikasi mobile khusus.
- Tes psikologis tervalidasi atau skoring otomatis.
- Chat real-time, video konseling, atau notifikasi WhatsApp/email.
- Pembayaran/langganan.
- Pengelolaan konten profesi oleh Admin (profesi diisi dari data contoh `database.sql`, bukan CRUD).
- Multi-admin per sekolah, hak akses granular, dan audit forensik lanjutan.
- Import Excel massal (bisa ditambahkan setelah MVP).

---

## 5. Teknologi dan batasan

| Aspek | Keputusan |
|-------|-----------|
| Bahasa | PHP native (prosedural sederhana), tanpa framework |
| Database | MySQL/MariaDB melalui Laragon, diakses dengan PDO |
| Web server | Apache Laragon (folder proyek di `www/`) |
| Tampilan | HTML + Tailwind CSS (CDN pada MVP) + SVG icon KARVIA + Google Fonts Inter |
| JavaScript | Vanilla JS seperlunya; interaksi utama memakai form server-side |
| Autentikasi | Session PHP + `password_hash` / `password_verify` |
| Penyimpanan | Database relasional (bukan localStorage, bukan variabel JS) |
| Build tool | Tidak ada (tanpa Composer/npm) |
| Bahasa UI | Indonesia |
| Gaya kode | Sederhana, banyak komentar berbahasa Indonesia, mudah dipelajari |

**Batasan non-teknis**

- Cindy bukan dari bidang komputer; istilah teknis harus dijelaskan.
- Target indikatif: **Rabu, 25 November 2026** (dihitung dari Kamis, 19 November 2026).
- Data demo lokal terdiri dari 5 sekolah (2 awal dan 3 tambahan Bandar Lampung), 6 Admin termasuk Admin global, 15 Guru BK, 150 siswa, 4 profesi, 12 artikel awal + 1 artikel tambahan, serta data eksplorasi terbatas. Akun dan identitas bersifat fiktif; NPSN tambahan adalah placeholder. Untuk pemakaian nyata, ganti data dan seluruh password demo.
- Sistem harus terasa “benar-benar dipakai”, bukan sekadar tampilan.

---

## 6. Definisi sukses

MVP dianggap berhasil jika skenario berikut berjalan tanpa edit manual database:

1. Admin login, melihat 5 sekolah dan jumlah pengguna nyata setelah impor kedua file dummy.
2. Admin membuat akun Guru BK baru di salah satu sekolah.
3. Admin menonaktifkan akun siswa; siswa tersebut tidak bisa login.
4. Guru BK login dan hanya melihat siswa dari sekolahnya.
5. Siswa login, mengisi Kenali Diriku, menyimpan profesi, membuat perbandingan,
   mengisi rencana dan jurnal, lalu menekan “Bagikan ke Guru BK”.
6. Data siswa tersebut muncul di dashboard Guru BK sekolahnya setelah dibagikan.
7. Guru BK membuka detail dan menambahkan catatan tindak lanjut.
8. Semua data tetap ada setelah logout, refresh, dan server dijalankan ulang.
9. Angka di dashboard Admin dan Guru BK berasal dari database, bukan hardcoded.
10. Cindy dapat mengikuti panduan menjalankan aplikasi dan menjelaskan alur dasarnya.

---

## 7. Istilah

| Istilah | Arti di proyek ini |
|---------|--------------------|
| **Admin** | Pengelola sistem; tidak terikat satu sekolah; memantau & mengelola akses |
| **Guru BK** | Konselor di satu sekolah; melihat catatan siswa sekolahnya yang dibagikan |
| **Siswa** | Peserta didik SMA/MA yang mengisi eksplorasi karier |
| **Sekolah** | Entitas satuan pendidikan; menaungi Guru BK dan Siswa |
| **Akun aktif** | Akun dengan status `aktif` dan boleh login |
| **Dinonaktifkan** | Akun tetap ada beserta riwayatnya, tetapi tidak bisa login |
| **Dibagikan** | Siswa menekan tombol bagikan sehingga catatannya terlihat oleh Guru BK |
| **Catatan masuk** | Data berbagi dari siswa yang belum/sudah ditindaklanjuti Guru BK |
| **Pengguna terdaftar** | Akun yang ada di tabel `pengguna` |
| **Pernah login** | Akun dengan `last_login_at` terisi |
| **Aktif 30 hari** | Akun yang login dalam 30 hari terakhir (indikator pemakaian) |

---

## 8. Prinsip produk

1. **Data nyata lebih penting daripada animasi.**
2. **Satu sumber kebenaran:** semua angka dihitung dari database.
3. **Privasi siswa:** jurnal dan refleksi tidak terlihat Guru BK sebelum siswa membagikan.
4. **“Keluarkan pengguna” = nonaktifkan** secara default; riwayat tetap aman.
5. **Sederhana untuk dipelajari:** sedikit file, penamaan jelas, komentar Indonesia.
6. **Jangan merusak yang sudah bagus:** pertahankan nuansa visual hijau KARVIA,
   Inter, kartu lembut, dan alur lima langkah.
