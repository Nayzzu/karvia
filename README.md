# KARVIA — Career Vision & Action

Aplikasi bimbingan karier untuk siswa SMA/MA. Dibangun dengan **PHP native + MySQL
(PDO, session)**, tanpa framework dan tanpa Composer. Memiliki tiga peran:
**Admin**, **Guru BK**, dan **Siswa**, serta mendukung lebih dari satu sekolah.

> Status: **kerangka + dokumentasi**. Belum ada database, SQL, atau logika aplikasi.
> Semua keputusan dan rencana ada di folder [`docs/`](docs/README.md).

## Struktur folder

```text
karvia/
├── admin/
│   └── index.php            # dashboard Admin
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── koneksi.php          # konfigurasi + koneksi database
├── database/
│   └── database.sql         # struktur + data contoh (belum dibuat)
├── guru_bk/
│   └── index.php            # dashboard Guru BK
├── includes/
│   ├── header.php           # pembuka halaman + sidebar + cek login
│   ├── footer.php           # penutup halaman
│   └── function.php         # fungsi bantu (auth, CSRF, escape, flash)
├── siswa/
│   └── index.php            # beranda Siswa
├── docs/                    # dokumentasi proyek
├── index.php                # landing page
├── login.php                # form + proses login
├── logout.php               # mengakhiri sesi
├── karvia_career_vision_action.html   # prototipe acuan (jangan diubah)
└── README.md
```

Folder peran (`admin/`, `guru_bk/`, `siswa/`) akan bertambah halaman sesuai
kebutuhan fitur. Rinciannya ada di [`docs/02`](docs/02-arsitektur-dan-struktur-folder.md)
dan [`docs/04`](docs/04-rencana-halaman-dan-alur.md).

## Menjalankan (rencana, setelah kode tersedia)

1. Letakkan folder proyek di `D:\LARAGON\laragon\www\` (nama folder bebas).
2. Jalankan **Start All** di Laragon.
3. Buat database `karvia_db`, lalu impor `database/database.sql`.
4. Sesuaikan `BASE_URL` dan kredensial database di `config/koneksi.php`.
5. Buka `http://localhost/<nama-folder>/` di browser.

## Aturan penting

- `karvia_career_vision_action.html` adalah referensi desain. **Jangan diubah,
  dipindah, atau dihapus.**
- Jangan menambah fitur di luar MVP tanpa mencatatnya di `docs/08-keputusan-terbuka.md`.
- “Keluarkan pengguna” berarti **nonaktifkan**, bukan hapus permanen.
- Semua angka dashboard dihitung dari database, bukan hardcoded.
- Semua query memakai prepared statement, output di-escape, form POST memakai CSRF.

Dokumentasi lengkap: [`docs/README.md`](docs/README.md).
