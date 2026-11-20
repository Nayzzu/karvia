# KARVIA — Career Vision & Action

Aplikasi bimbingan karier SMA/MA berbasis PHP native + MySQL (PDO, session), tanpa framework atau Composer. Tiga peran: Admin, Guru BK, dan Siswa; data dipisahkan per sekolah.

## Menjalankan lokal

1. Pasang dan jalankan Laragon (**Start All**).
2. Buat database MySQL `karvia_db`.
3. Impor `database/database.sql` satu kali ke database kosong. File berisi struktur dan data demo.
4. Periksa `BASE_URL` dan koneksi database di `config/koneksi.php`.
5. Buka `http://localhost/karvia/` (sesuaikan URL bila nama folder berbeda).
6. Masuk dengan akun demo di bawah.

| Peran | Username | Password |
|---|---|---|
| Admin | `admin` | `admin` |
| Guru BK | `guru` | `guru` |
| Siswa | `siswa` | `siswa` |

Akun demo hanya untuk lokal. Jangan gunakan kredensial demo atau konfigurasi database bawaan untuk penggunaan nyata. Buat akun Admin baru dan ganti password semua akun demo sebelum sistem dibuka ke pengguna.

## Fitur

- Admin: dashboard dan rekap database, kelola sekolah dan akun, nonaktifkan/aktifkan, reset password.
- Guru BK: dashboard sekolah sendiri, catatan yang dibagikan siswa, tindak lanjut, artikel edukasi publik.
- Siswa: refleksi diri, profesi/bookmark, bandingkan pilihan, rencana, jurnal pribadi, konsultasi/bagikan, informasi karier.
- Semua halaman internal memeriksa sesi dan peran; formulir perubahan memakai CSRF; query memakai PDO prepared statements; teks pengguna di-escape.

KARVIA media pendukung bimbingan karier, bukan tes psikologis atau penentu pilihan otomatis. Guru BK hanya melihat catatan yang dibagikan siswa di sekolahnya. Admin melihat rekap, bukan isi jurnal pribadi.

## Backup database

Sebelum perubahan besar atau demo, jalankan dari terminal Laragon:

```text
mysqldump -u root karvia_db > database/backup_YYYY-MM-DD.sql
```

Jangan commit backup yang berisi data nyata. Simpan backup di lokasi aman, bukan pada folder publik web.

## Catatan

- `karvia_career_vision_action.html` adalah prototipe acuan; jangan ubah, pindah, atau hapus.
- Detail kebutuhan, alur, keamanan, dan status build ada di [`docs/`](docs/README.md).
- Instalasi database di desain untuk database kosong; backup sebelum impor ulang. Jangan jalankan ulang SQL pada database berisi data tanpa rencana pemulihan.
