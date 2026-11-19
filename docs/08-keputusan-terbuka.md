# 08 — Keputusan Terbuka

Dokumen ini mencatat hal yang **belum final**. Selama belum dikonfirmasi, gunakan
kolom **“Default sementara”** agar pekerjaan tetap jalan. Setelah Cindy memutuskan,
ubah status menjadi **Disetujui** dan catat tanggalnya di bagian akhir.

---

## 1. Enam pertanyaan prioritas untuk Cindy

Sampaikan dengan bahasa sederhana, bukan istilah teknis.

1. **Siapa yang boleh membuat akun?**
   “Apakah semua akun (Guru BK & siswa) dibuat oleh Admin, atau Guru BK juga boleh
   menambahkan siswanya sendiri?”
   → Usulan: **hanya Admin** untuk versi pertama.

2. **Kalau ada orang yang harus dikeluarkan, apa yang dilakukan?**
   “Apakah akunnya cukup dinonaktifkan (tidak bisa masuk, data tetap ada), atau
   benar-benar dihapus permanen?”
   → Usulan: **nonaktifkan**. Data konseling tetap aman.

3. **Login pakai apa?**
   “Siswa dan Guru BK login dengan username buatan Admin, atau pakai email?”
   → Usulan: **username** agar sederhana dan tidak perlu email aktif.

4. **Apakah Admin boleh membaca isi jurnal siswa?**
   “Admin perlu melihat jumlah dan status, tetapi apakah perlu membaca tulisan
   pribadi siswa?”
   → Usulan: **tidak**. Admin hanya melihat rekap; isi hanya untuk siswa dan
   Guru BK sekolahnya setelah dibagikan.

5. **Guru BK melihat siswa yang mana?**
   “Guru BK melihat semua siswa di sekolahnya, atau hanya siswa yang menekan
   ‘Bagikan ke Guru BK’?”
   → Usulan: **hanya yang dibagikan**, agar siswa punya kendali atas privasinya.

6. **Apakah laporan perlu diunduh?**
   “Perlu tombol unduh daftar siswa/sekolah ke Excel/CSV untuk laporan?”
   → Usulan: **setelah demo** (P1), bukan penghambat MVP.

---

## 2. Daftar keputusan

| ID | Pertanyaan | Default sementara | Dampak | Status |
|----|------------|-------------------|--------|--------|
| D-01 | Siapa yang membuat akun? | Admin membuat semua akun | Menentukan menu & hak akses | Usulan |
| D-02 | Identitas login | Username unik | Kolom DB & form login | Usulan |
| D-03 | “Keluarkan pengguna” | Nonaktifkan, bukan hapus | Data & riwayat tetap ada | Usulan |
| D-04 | Admin membaca jurnal? | Tidak | Privasi & tampilan admin | Usulan |
| D-05 | Cakupan Guru BK | Hanya siswa yang membagikan | Query & daftar siswa | Usulan |
| D-06 | Bagikan ulang | Diizinkan; status kembali `baru` | Alur notifikasi guru | Usulan |
| D-07 | Guru BK boleh tambah siswa? | Tidak (hanya Admin) | Menu & validasi | Usulan |
| D-08 | Guru BK boleh reset password siswa? | Tidak (melalui Admin) | Hak akses | Usulan |
| D-09 | Cakupan Admin | Global, lintas sekolah | Struktur peran | Usulan |
| D-10 | Hapus sekolah | Tidak dihapus; nonaktifkan | Integritas data | Usulan |
| D-11 | Definisi “pengguna aktif” | Tampilkan “terdaftar”, “pernah login”, “aktif 30 hari” | Label dashboard | Usulan |
| D-12 | Ekspor CSV/Excel | P1 setelah demo | Prioritas fitur | Usulan |
| D-13 | Nama 2 sekolah contoh | SMA Nusantara 1 & MA Al-Hikmah (fiktif) | Data demo | Usulan |
| D-14 | Kelola katalog profesi | Admin (P2); MVP pakai data contoh di `database.sql` | Prioritas fitur | Usulan |
| D-15 | Pengumuman & jadwal konsultasi | Tidak masuk MVP | Scope | Usulan |
| D-16 | Hosting | Demo lokal Laragon dulu | Cara akses & biaya | Terbuka |
| D-17 | Persetujuan orang tua/siswa | Tampilkan disclaimer & kendali bagikan | Kepatuhan & kepercayaan | Terbuka |
| D-18 | Format demo Rabu | Demo langsung ±15 menit + backup SQL | Ekspektasi | Terbuka |
| D-19 | Perubahan peran akun | Peran tidak diubah setelah dibuat | Mencegah data menggantung | Usulan |
| D-20 | Siswa pindah sekolah | Data berbagi lama disembunyikan; siswa harus membagikan ulang ke sekolah baru | Privasi & visibilitas guru baru | Usulan |
| D-21 | Guru BK melihat daftar siswa yang belum membagikan? | Tidak di MVP | Privasi & pemantauan | Usulan |
| D-22 | Email/notifikasi | Tidak ada di MVP | Scope | Usulan |
| D-23 | Foto profil | Tidak ada di MVP | Scope | Usulan |
| D-24 | Password awal | Dibuat Admin, minimal 8 karakter | Keamanan | Usulan |
| D-25 | Cara menyimpan struktur & data awal | Satu file `database/database.sql` (bagian A struktur + bagian B data contoh), tanpa migration/seeder | Cara import & dokumentasi | **Disetujui** |
| D-26 | Struktur folder & lokasi | Folder per peran (`admin/`, `guru_bk/`, `siswa/`), `includes/`, `config/koneksi.php`, `database/database.sql`; lokasi bebas, path bertingkat hanya contoh | Path, `BASE_URL`, deploy | **Disetujui** |
| D-27 | Jumlah pengembang & pembagian | 2 orang: A fondasi/admin, B siswa/guru | Jobdesk & timeline | **Disetujui** |
| D-28 | Gaya routing | Satu halaman = satu file; tanpa front controller `?page=` | Struktur file & URL | **Disetujui** |

---

## 3. Detail keputusan yang paling penting

### D-03 — Nonaktif vs hapus

**Nonaktif (disarankan):**

- Akun tidak bisa login.
- Riwayat jurnal, tindak lanjut, dan rekap tetap ada.
- Admin bisa mengaktifkan kembali.
- Data siswa tidak hilang saat sekolah berganti tahun.

**Hapus permanen:**

- Data hilang dan tidak bisa dikembalikan.
- Rekap masa lalu berubah.
- Berisiko jika salah klik.

**Keputusan yang diminta:** apakah Cindy setuju “keluarkan = nonaktif”?
Jika ya, tombol hapus tidak perlu ada di MVP.

### D-04 — Privasi jurnal

Jurnal berisi tulisan pribadi siswa (hambatan, dukungan, kebingungan).
Membuka isi jurnal ke Admin dapat menurunkan kepercayaan siswa.

**Disarankan:**

- Admin melihat jumlah akun, jumlah catatan, status, dan aktivitas login.
- Isi jurnal hanya untuk siswa dan Guru BK sekolahnya setelah dibagikan.
- Jika ada kebutuhan darurat, akses harus melalui persetujuan dan tercatat.

### D-05 — Guru BK melihat semua siswa atau hanya yang dibagikan

**Hanya yang dibagikan (disarankan):**

- Sesuai tombol “Bagikan Catatan ke Dashboard Guru BK” pada prototipe.
- Siswa punya kendali.
- Dashboard guru tidak penuh data yang belum siap dikonsultasikan.

**Semua siswa sekolah:**

- Guru bisa memantau siapa yang belum mengisi.
- Tetapi siswa merasa selalu diawasi.
- Membutuhkan aturan tambahan.

Jika Cindy memilih “semua siswa”, tambahkan daftar siswa terpisah dengan status
“belum membagikan”, tetapi **isi jurnal tetap tidak terlihat** sampai dibagikan.

### D-16 — Hosting

Demo Rabu sebaiknya di **laptop lokal dengan Laragon**. Hosting sekolah
membutuhkan domain, server, HTTPS, dan biaya. Jangan menjanjikan alamat online
sebelum disepakati.

Pertanyaan: apakah Cindy butuh akses online untuk demo, atau cukup lokal?
Jika online, siapkan hosting minimal sehari sebelumnya.

---

## 4. Asumsi yang dipakai sampai ada keputusan lain

Selama Cindy belum menjawab, pengembangan memakai asumsi berikut:

1. Akun dibuat Admin.
2. Login memakai username.
3. “Keluar” = nonaktif.
4. Admin tidak membaca jurnal.
5. Guru BK hanya melihat siswa yang membagikan.
6. Satu siswa punya satu refleksi, satu perbandingan, satu rencana; jurnal banyak.
7. Data awal: 2 sekolah fiktif, 1 admin, 2 guru, 4 siswa.
8. Tidak ada email, foto profil, pengumuman, atau jadwal di MVP.
9. Bahasa Indonesia, warna hijau KARVIA, font Inter.
10. Demo lokal lebih dulu.

---

## 5. Riwayat keputusan

Isi saat keputusan diambil.

| Tanggal | ID | Keputusan | Diputuskan oleh | Catatan |
|---------|----|-----------|-----------------|---------|
| 19 Nov 2026 | D-01…D-24 | Default sementara dipakai untuk mulai build | Tim pengembang | Menunggu konfirmasi Cindy |
| 19 Nov 2026 | D-25 | Database memakai satu file `database/database.sql` (struktur + data contoh) | Pemilik proyek | Menggantikan rencana dua file |
| 19 Nov 2026 | D-26 | Struktur folder per peran; lokasi tidak terikat path tertentu | Pemilik proyek | Path bertingkat hanya contoh |
| 19 Nov 2026 | D-27 | Project dikerjakan 2 orang | Pemilik proyek | Jobdesk di `09` |
| 19 Nov 2026 | D-28 | Satu halaman = satu file, tanpa `?page=` | Pemilik proyek | Struktur `admin/`, `guru_bk/`, `siswa/` |

---

## 6. Cara mencatat keputusan baru

1. Tambahkan baris di tabel bagian 2.
2. Tulis dampaknya ke dokumen lain:
   - kebutuhan → `01`
   - struktur data → `03`
   - halaman/alur → `04`
3. Catat di bagian 5 dengan tanggal dan nama pemutus.
4. Jika keputusan membatalkan default, perbarui juga bagian 4.

Tujuan dokumen ini bukan birokrasi, tetapi agar **tidak ada fitur yang dibangun
dua kali atau salah arah** karena asumsi yang berbeda.
