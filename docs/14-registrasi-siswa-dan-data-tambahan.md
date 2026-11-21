# 14 — Pendaftaran Siswa dan Data Tambahan Bandar Lampung

> Dokumen ini ditambahkan setelah panduan instalasi dan konteks AI yang sudah memakai nomor 12 dan 13.

## Kebijakan akun

- Pendaftaran mandiri hanya untuk **Siswa**. Form publik tidak menerima `role` dari pengguna; server menetapkan `role = 'siswa'` dan status aktif.
- Admin dan Guru BK tetap dibuat oleh Admin. Admin sekolah bertanggung jawab menonaktifkan akun siswa yang tidak sesuai sekolah/identitas.
- Siswa memilih sekolah dari daftar sekolah aktif, mengisi nama, kelas, username, dan password. Akun langsung dapat digunakan setelah pendaftaran.
- Username unik, lowercase, 3–50 karakter (`a-z`, angka, `.`, `_`, `-`). Password 8–72 karakter, disimpan dengan `password_hash()`.
- Siswa mengubah username dari `profil.php` setelah memverifikasi password saat ini.
- Ini keputusan untuk mendukung onboarding/uji coba siswa tanpa input massal manual. Untuk penggunaan sekolah riil, sebaiknya berikan kode undangan/validasi sekolah atau persetujuan Admin sebelum membuka pendaftaran bebas; jangan mengandalkan pilihan dropdown sebagai bukti siswa benar-benar berasal dari sekolah.

## Data contoh Bandar Lampung

`database/dummy_karvia2.sql` menambahkan tanpa menghapus data yang ada:

| Sekolah | Jenjang | Admin sekolah | Akun Guru BK | Akun Siswa |
|---|---|---|---|---|
| SMAN 1 Bandar Lampung | SMA | `admin3` | `guru7`–`guru9` | `siswa61`–`siswa90` |
| SMAN 9 Bandar Lampung | SMA | `admin4` | `guru10`–`guru12` | `siswa91`–`siswa120` |
| MAN 1 Bandar Lampung | MA | `admin5` | `guru13`–`guru15` | `siswa121`–`siswa150` |

Akun demo memakai password bersama sesuai peran: admin `admin`, guru `guru`, siswa `siswa`. Semua akun dan identitas fiktif. NPSN pada file SQL adalah placeholder dummy, bukan klaim data resmi. Satu artikel ditambahkan untuk sekolah pertama saja, dengan gambar `artikel-01.jpg` yang sudah tersedia.

### Impor

Jalankan `dummy_karvia2.sql` **setelah** struktur dan `dummy_karvia.sql`:

```text
mysql -u root karvia_db < database/dummy_karvia2.sql
```

File menggunakan transaksi dan insert bersyarat untuk mencegah duplikasi sekolah/username/artikel saat diulang. File tidak mengosongkan tabel dan tidak mengubah status online akun lama. Backup database sebelum menjalankan pada data yang penting.

## Pemeriksaan penerimaan

- [x] Pendaftaran siswa valid → auto-login dan dashboard siswa (HTTP lokal).
- [x] POST menyertakan `role=admin`, hasil akun tetap berperan `siswa`.
- [ ] Username duplikat, format salah, sekolah nonaktif/tidak ada, password mismatch: perlu uji negatif.
- [x] CSRF salah ditolak; Admin tidak mendapat form ubah username (HTTP lokal).
- [x] Username berubah dengan password benar; password salah ditolak (HTTP lokal).
- [x] Daftar sekolah berasal dari sekolah aktif; akun uji terikat pada sekolah pilihan.
- [x] SQL tambahan dijalankan dua kali tanpa duplikasi: 5 sekolah, 6 admin termasuk global, 15 Guru BK, 150 siswa; 1 artikel tambahan.
- [x] Akun lama mempertahankan `terakhir_aktif_pada`; akun tambahan punya nilai `NULL` setelah import.
- [ ] Uji akun lintas sekolah memastikan Guru BK hanya melihat catatan siswa yang dibagikan di sekolahnya.

Status online tidak termasuk pekerjaan ini; heartbeat dan widget tidak diubah.
