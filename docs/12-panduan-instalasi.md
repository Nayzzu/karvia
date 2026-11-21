# Panduan Instalasi & Menjalankan KARVIA

Aplikasi bimbingan karier SMA/MA berbasis PHP native + MySQL (PDO, session),
tanpa framework atau Composer. Tiga peran: Admin, Guru BK, dan Siswa; data
dipisahkan per sekolah.

## Menjalankan lokal

1. Pasang dan jalankan Laragon (**Start All**).
2. Buat database MySQL `karvia_db`.
3. Impor `database/database.sql` (struktur tabel) ke database kosong.
4. Impor `database/dummy_karvia.sql` untuk 2 sekolah awal. **File ini TRUNCATE tabel; gunakan hanya pada database kosong/demo.**
5. Impor `database/dummy_karvia2.sql` untuk menambahkan 3 sekolah Bandar Lampung tanpa menghapus data lama.
6. Periksa `BASE_URL` dan koneksi database di `config/koneksi.php`.
7. Buka `http://localhost/karvia/` (sesuaikan URL bila nama folder berbeda).
8. Masuk dengan akun demo di bawah.

Lewat terminal:

```text
mysql -u root < database/database.sql
mysql -u root karvia_db < database/dummy_karvia.sql
mysql -u root karvia_db < database/dummy_karvia2.sql
```

## Akun demo

| Peran | Username | Password |
|---|---|---|
| Admin global | `admin` | `admin` |
| Admin sekolah 1–5 | `admin1` … `admin5` | `admin` |
| Guru BK (15 akun) | `guru1` … `guru15` | `guru` |
| Siswa (150 akun) | `siswa1` … `siswa150` | `siswa` |

Setiap sekolah memiliki 30 akun siswa. Akun `admin3`–`admin5`, `guru7`–`guru15`,
dan `siswa61`–`siswa150` ditambahkan oleh `dummy_karvia2.sql`; nama lengkap tampil
natural dan fiktif, username tetap bernomor. Jalankan ulang file untuk menyelaraskan
nama akun Bandar Lampung yang sudah ada. Artikel tambahan hanya dibuat pada SMAN 1 Bandar Lampung.

Akun demo hanya untuk lokal. Jangan gunakan kredensial demo atau konfigurasi
database bawaan untuk penggunaan nyata. Buat akun Admin baru dan ganti password
semua akun demo sebelum sistem dibuka ke pengguna.

## Fitur

- Admin: dashboard dan rekap database, kelola sekolah dan akun,
  nonaktifkan/aktifkan, reset password.
- Guru BK: dashboard sekolah sendiri, catatan yang dibagikan siswa, tindak
  lanjut, artikel edukasi publik.
- Siswa: dapat mendaftar mandiri dengan memilih sekolah aktif, mengganti username
  setelah verifikasi password, serta mengelola refleksi diri, profesi/bookmark,
  perbandingan, rencana, jurnal pribadi, konsultasi/bagikan, informasi karier.
- Semua halaman internal memeriksa sesi dan peran; formulir perubahan memakai
  CSRF; query memakai PDO prepared statements; teks pengguna di-escape.

KARVIA media pendukung bimbingan karier, bukan tes psikologis atau penentu
pilihan otomatis. Guru BK hanya melihat catatan yang dibagikan siswa di
sekolahnya. Admin melihat rekap, bukan isi jurnal pribadi.

## Backup database

Sebelum perubahan besar atau demo, jalankan dari terminal Laragon:

```text
mysqldump -u root karvia_db > database/backup_YYYY-MM-DD.sql
```

Jangan commit backup yang berisi data nyata. Simpan backup di lokasi aman, bukan
pada folder publik web.

## Catatan

- `docs/karvia_career_vision_action.html` adalah prototipe acuan; jangan ubah
  atau hapus.
- Detail kebutuhan, alur, keamanan, dan status build ada di
  [`docs/README.md`](README.md).
- Instalasi database dirancang untuk database kosong; backup sebelum impor ulang.
  Jangan jalankan ulang SQL pada database berisi data tanpa rencana pemulihan.
