# 05 — Panduan Kerja untuk AI / Pengembang

Dokumen status kerja dan checklist rilis KARVIA. Jangan menandai ceklis tanpa bukti uji.

## 1. Status

| Area | Status | Bukti / sisa |
|---|---|---|
| Fondasi login, session, PDO, CSRF, role | Selesai lokal | Login semua peran/role guard sudah diuji sebelumnya; uji akhir wajib diulang |
| Admin P0 | Selesai lokal | Dashboard, sekolah, pengguna, reset, rekap, pemantauan online/aktif 5 menit, uji HTTP |
| Guru BK P0 | Selesai lokal | Catatan, isolasi sekolah, tindak lanjut transaksi, uji HTTP sebelumnya |
| Siswa P0 | Selesai lokal | Form eksplorasi, jurnal, sharing; uji HTTP dan persistensi sebelumnya |
| Profil/password | Terverifikasi lokal | GET semua peran, ubah profil/escape, CSRF, password lama salah, password baru login diuji; fixture demo dipulihkan |
| Artikel D-29 | Terverifikasi alur inti lokal | Buat PNG, tolak MIME palsu, owner/non-owner, CSRF hapus, edit pertahankan/ganti gambar, hapus row/file, landing/public/XSS diuji; oversize/file rusak/DB failure belum diuji |
| Keamanan server Apache | Lokal terverifikasi | Aturan `.htaccess` menolak akses ke `docs/`, `database/`, `config/`, `includes/`, root file sensitif, dan direktori upload (403) |
| Dokumentasi/panduan | Diperbarui | `README.md` berisi setup lokal, akun demo, backup dan peringatan |
| Rilis/commit | Selesai pada branch fitur | Commit `2e1e0ce` mencakup alur Admin, Guru BK, Siswa, profil, artikel, dan `.htaccess`. Folder `teal-treasury-landing/` sengaja ditinggal terpisah |

Artikel edukasi Guru BK disetujui sebagai tambahan melalui D-29. Prototipe `karvia_career_vision_action.html` tetap utuh dan tidak boleh diubah.

## 2. Aturan wajib

1. PHP native + PDO + session; tanpa framework, Composer, localStorage, migration, atau seeder.
2. Satu halaman = satu file; halaman peran hanya dalam `admin/`, `guru_bk/`, `siswa/`.
3. Semua query memakai prepared statement; output HTML di-escape dengan `e()`; POST memakai CSRF.
4. Otorisasi server-side dan filter `sekolah_id`/`siswa_id` wajib.
5. Admin tidak membaca isi jurnal; Guru BK hanya membaca data siswa sekolahnya setelah dibagikan.
6. Akun dinonaktifkan, bukan dihapus. Password tidak pernah disimpan/dicatat sebagai teks biasa.
7. Perubahan skema wajib memperbarui `docs/03-rencana-database.md`; database tetap satu file SQL.
8. Jangan buang atau menimpa perubahan lokal yang belum jelas pemiliknya. Jangan commit tanpa pemeriksaan diff.

## 3. Checklist penutupan

### Profil dan password
- [x] Profil dapat dibuka oleh Admin, Guru BK, Siswa.
- [x] Ubah nama/telepon terproteksi CSRF dan output escape diuji.
- [x] Password lama salah ditolak; password baru dapat dipakai login.
- [x] CSRF invalid → 403. Password lama salah → 200 dengan pesan error. Password baru valid → 302 profil.php; login baru berhasil (302 dashboard); login lama gagal (302 login.php). Profil nama/telepon tersimpan, output di-escape. Fixture demo dipulihkan.

### Artikel D-29
- [x] Guru BK membuat artikel uji dengan PNG valid ukuran <2 MB.
- [x] MIME palsu dan CSRF invalid ditolak.
- [ ] File rusak, format selain JPG/PNG/WebP, dan oversize ditolak (belum diuji).
- [x] Edit pemilik mempertahankan gambar tanpa upload; gambar pengganti menghapus lama.
- [x] Guru lain mendapat 404 saat edit/hapus ID.
- [x] Hapus artikel memakai POST+CSRF dan menghapus row serta gambar.
- [x] Artikel publik tampil di landing/`artikel.php`; XSS di-escape.
- [ ] ID invalid dan kegagalan DB/filesystem perlu diuji.

### QA aplikasi dan keamanan
- [x] PHP lint semua 30 file PHP; `git diff --check` bersih (peringatan hanya line-ending Git).
- [x] Smoke test GET halaman Admin/Guru BK/Siswa utama; semua 200, akses file sensitif 403.
- [x] Ulang akun nonaktif: login ditolak, sesi aktif yang dinonaktifkan mid-session redirect ke login.
- [x] IDOR lintas sekolah: guru sekolah 1 tidak bisa baca catatan siswa sekolah 2 (404).
- [x] Peran silang: siswa/guru/admin masing-masing diblokir dari halaman peran lain (403).
- [x] Admin rekap tidak mengandung isi jurnal.
- [ ] Uji tindak lanjut, status dibaca/ditindaklanjuti, dashboard DB (hasil integrasi ada di sesi ini, uji gabungan ulang opsional).
- [ ] Uji desktop/mobile dan cetak ringkasan.
- [ ] Uji restart Laragon dan persistensi data lintas tabel.
- [x] Uji akses HTTP ke `AGENTS.md`, `docs/`, `database/`, `config/`, `includes/`, `.env`, dan direktori upload; Apache lokal mengembalikan 403. Upload PHP juga ditolak oleh `uploads/artikel/.htaccess` (uji runtime PHP upload belum dilakukan).
- [ ] Pastikan log error tidak menampilkan ke pengguna dan tidak ada kredensial nyata.

### Data demo dan rilis
- [ ] Backup database ke lokasi privat sebelum demo.
- [ ] Bersihkan hanya data uji, pertahankan fixture demo yang disepakati.
- [x] Review `git status`, diff tiap file; biarkan landing `teal-treasury-landing/` belum siap terpisah.
- [x] Perbarui `docs/04` dan status dokumen ini berdasarkan tes nyata.
- [x] Commit per batas fitur pada branch `feat/guru-articles` (`2e1e0ce`).
- [x] Jalankan smoke test lagi setelah commit (14/14 lulus).

## 4. Jalankan proyek

1. Buka Laragon, pilih **Start All**.
2. Buat database `karvia_db`, lalu impor `database/database.sql` hanya pada database kosong.
3. Cek `BASE_URL` dan kredensial di `config/koneksi.php`.
4. Buka `http://localhost/karvia/`.
5. Akun demo lokal: `admin/admin`, `guru/guru`, `siswa/siswa`. Ganti sebelum penggunaan nyata.
6. Backup dengan `mysqldump -u root karvia_db > database/backup_YYYY-MM-DD.sql`; simpan backup privat.

## 5. Progres historis

- Fase fondasi, Admin, Guru BK, Siswa P0 dan alur siswa → Guru BK pernah lolos pengujian HTTP lokal.
- Pengujian profil dan password dilakukan pada sesi terakhir; test lama/salah CSRF serta login password baru berhasil. Akun demo harus dipastikan kembali ke fixture SQL.
- Fitur artikel belum diuji end-to-end. Jangan menyatakan rilis final sebelum checklist artikel dan QA keamanan selesai.
