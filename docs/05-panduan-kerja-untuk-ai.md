# 05 — Panduan Kerja untuk AI / Pengembang

Dokumen status kerja dan checklist rilis KARVIA. Jangan menandai ceklis tanpa bukti uji.

## 1. Status

| Area | Status | Bukti / sisa |
|---|---|---|
| Fondasi login, session, PDO, CSRF, role | Selesai lokal | Login semua peran/role guard sudah diuji sebelumnya; uji akhir wajib diulang |
| Admin P0 | Selesai lokal | Dashboard, sekolah, pengguna, reset, rekap, pemantauan online/aktif 5 menit, redesign sidebar & shell Attendara, uji HTTP |
| Guru BK P0 | Selesai lokal | Catatan, isolasi sekolah, tindak lanjut transaksi, uji HTTP sebelumnya |
| Siswa P0 | Sembilan halaman diperbarui dan smoke-tested | Beranda: progres DB/status berbagi; Kenali Diriku: prompt dan privasi; Jelajah Profesi: 15 pilihan dengan foto; Bandingkan: dua kartu responsif dan prompt; Rencana: tujuan dan dua tindakan realistis; Jurnal: prompt refleksi, privasi, riwayat; Ruang Konsultasi: identitas, ringkasan per bagian, privasi jurnal, status berbagi berlabel, pesan opsional, gaya cetak; Jalur: lima pilihan dan panduan; Tips: lima langkah edukasi dan enam artikel terbaru Guru BK dalam carousel responsif; seluruh sembilan GET 200, lint sembilan file siswa, diff check lulus; cetak/browser visual belum diuji |
| Profil/password | Terverifikasi lokal | GET semua peran, ubah profil/escape, CSRF, password lama salah, password baru login diuji; fixture demo dipulihkan |
| Registrasi siswa & username | Implementasi lokal | `daftar.php` membatasi role siswa; login/daftar memakai layout responsif KARVIA dan CSS terlingkup `assets/css/auth.css`; backend, CSRF, validasi, username di profil tetap server-side |
| Data tambahan Bandar Lampung | Diimpor lokal | `dummy_karvia2.sql`: 3 sekolah, 3 admin sekolah, 9 Guru BK, 90 siswa, 1 artikel; nama lengkap fiktif natural, username tetap bernomor; import ulang menyelaraskan nama tanpa mengubah heartbeat |
| Landing | Implementasi lokal | Navbar dialog layar penuh, carousel artikel semua layar, hero bersih dengan animasi load; PHP lint + HTTP smoke lulus; browser viewport/keyboard perlu verifikasi manual |
| Artikel D-29 | Terverifikasi alur inti lokal | Buat PNG, tolak MIME palsu, owner/non-owner, CSRF hapus, edit pertahankan/ganti gambar, hapus row/file, landing/public/XSS diuji; oversize/file rusak/DB failure belum diuji |
| Keamanan server Apache | Lokal terverifikasi | Aturan `.htaccess` menolak akses ke `docs/`, `database/`, `config/`, `includes/`, root file sensitif, dan direktori upload (403) |
| Dokumentasi/panduan | Diperbarui | Setup lokal di `docs/12`; spesifikasi dan kriteria mobile landing di `docs/10`/`docs/11`; keputusan UI di `docs/08` |
| Rilis/commit | `staging` terbarui | Alur siswa dan dokumentasi awal ada di `0954a11` pada `origin/staging`. Pembaruan dokumentasi yang sedang dikerjakan belum di-commit/push. |

Artikel edukasi Guru BK disetujui sebagai tambahan melalui D-29. Prototipe `docs/karvia_career_vision_action.html` tetap utuh dan tidak boleh diubah.

## 2. Aturan wajib

1. PHP native + PDO + session; tanpa framework, Composer, localStorage, migration, atau seeder. SQL dummy terpisah dari struktur; `dummy_karvia.sql` destructive, `dummy_karvia2.sql` additive.
2. Satu halaman = satu file; halaman peran hanya dalam `admin/`, `guru_bk/`, `siswa/`.
3. Semua query memakai prepared statement; output HTML di-escape dengan `e()`; POST memakai CSRF.
4. Otorisasi server-side dan filter `sekolah_id`/`siswa_id` wajib.
5. Admin tidak membaca isi jurnal; Guru BK hanya membaca data siswa sekolahnya setelah dibagikan. Pembagian saat ini menampilkan seluruh entri jurnal dalam ringkasan; siswa diberi tahu sebelum berbagi.
6. Akun dinonaktifkan, bukan dihapus. Password tidak pernah disimpan/dicatat sebagai teks biasa.
7. Perubahan skema wajib memperbarui `docs/03-rencana-database.md`; struktur dan file dummy terpisah.
8. Jangan buang atau menimpa perubahan lokal yang belum jelas pemiliknya. Jangan commit tanpa pemeriksaan diff.

## 3. Checklist penutupan

### Registrasi siswa dan username
- [x] Data valid → akun role `siswa`, session aktif, dashboard; request role `admin` diabaikan (HTTP lokal).
- [x] CSRF invalid → 403; Admin tidak mendapat form ubah username (HTTP lokal).
- [ ] Sekolah invalid/nonaktif, username format salah/duplikat, password pendek/mismatch perlu uji negatif.
- [x] Ubah username dengan password benar berhasil; password salah ditolak (HTTP lokal).

### Profil dan password
- [x] Profil dapat dibuka oleh Admin, Guru BK, Siswa.
- [x] Ubah nama/telepon terproteksi CSRF dan output escape diuji.
- [x] Password lama salah ditolak; password baru dapat dipakai login.
- [x] CSRF invalid → 403. Password lama salah → 200 dengan pesan error. Password baru valid → 302 profil.php; login baru berhasil (302 dashboard); login lama gagal (302 login.php). Profil nama/telepon tersimpan, output di-escape. Fixture demo dipulihkan.

### Revisi landing
- [x] Navbar mobile menampilkan logo + tombol; dialog memuat tautan section dan CTA daftar siswa.
- [x] Artikel memakai carousel scroll-snap dan kontrol sebelumnya/berikutnya pada mobile dan desktop; desktop menampilkan beberapa kartu per langkah.
- [x] Hero tanpa label kecil; animasi load bertahap dan menghormati reduced-motion.
- [ ] Verifikasi manual viewport sempit/lebar, keyboard Escape/fokus, dan swipe/scroll pada browser.

### Artikel D-29
- [x] Guru BK membuat artikel uji dengan PNG valid ukuran <2 MB.
- [x] MIME palsu dan CSRF invalid ditolak.
- [ ] File rusak, format selain JPG/PNG/WebP, dan oversize: validasi ada; uji negatif belum dilakukan.
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
- [x] Alur siswa → Guru BK diuji: bagikan, detail berubah `baru` → `dibaca`, tindak lanjut mengubah status menjadi `ditindaklanjuti`, siswa melihat status; guru sekolah lain mendapat 404; data uji dibersihkan.
- [ ] Uji visual desktop/mobile dan cetak ringkasan di browser.
- [ ] Uji restart Laragon dan persistensi data lintas tabel.
- [x] Uji akses HTTP ke `AGENTS.md`, `docs/`, `database/`, `config/`, `includes/`, `.env`, dan direktori upload; Apache lokal mengembalikan 403. Upload PHP juga ditolak oleh `uploads/artikel/.htaccess` (uji runtime PHP upload belum dilakukan).
- [ ] Pastikan log error tidak menampilkan ke pengguna dan tidak ada kredensial nyata.

### Data demo dan rilis
- [ ] Backup database ke lokasi privat sebelum demo.
- [x] Data uji berbagi/tindak lanjut dibersihkan; fixture demo dipertahankan.
- [x] Review `git status`, diff tiap file; biarkan landing `teal-treasury-landing/` belum siap terpisah.
- [x] Perbarui `docs/04` dan status dokumen ini berdasarkan tes nyata.
- [x] Alur siswa diperbarui pada commit `0954a11` dan sudah ada di `origin/staging`.
- [x] Sembilan halaman siswa merespons HTTP 200; lint sembilan file siswa dan `git diff --check` lulus.
- [x] Uji berbagi → dibaca → tindak lanjut lulus; akses guru beda sekolah menghasilkan 404; data uji dibersihkan.

## 4. Jalankan proyek

1. Buka Laragon, pilih **Start All**.
2. Buat database `karvia_db`, lalu impor `database/database.sql` hanya pada database kosong.
3. Impor `database/dummy_karvia.sql` hanya untuk DB kosong/demo (file ini TRUNCATE tabel).
4. Impor `database/dummy_karvia2.sql` untuk tambahan Bandar Lampung; non-destructive dan dapat dijalankan ulang.
5. Cek `BASE_URL` dan kredensial di `config/koneksi.php`.
6. Buka `http://localhost/karvia/`.
5. Akun demo lokal: `admin/admin`, `guru/guru`, `siswa/siswa`. Ganti sebelum penggunaan nyata.
6. Backup dengan `mysqldump -u root karvia_db > database/backup_YYYY-MM-DD.sql`; simpan backup privat.

## 5. Progres historis

- Commit `0954a11` di `origin/staging` memuat revisi alur siswa; sembilan halaman siswa smoke-tested dan alur berbagi hingga tindak lanjut diverifikasi lokal.
- Uji visual browser untuk mobile, desktop, dan cetak ringkasan masih terbuka.
- Checklist keamanan/artikel dan sisa pemeriksaan rilis tercatat di bagian 3; jangan menyatakan QA final sebelum item terbuka selesai.
