# 📖 Dokumentasi & Panduan Pengembangan Website Karvia

Dokumen ini berisi penjelasan lengkap mengenai arsitektur sistem, struktur database, tata letak folder *coding*, serta panduan instalasi/penggunaan website **Karvia** yang siap dipelajari atau diserahkan kepada pihak klien.

---

## 1. 💡 Gambaran Umum Website

Website **Karvia** adalah platform sistem informasi Bimbingan Karir & Konseling Sekolah berbasis web. Website ini dirancang menggunakan **PHP Native** tanpa framework tambahan agar performa aplikasi ringan, cepat diakses, dan mudah di-hosting serta dikustomisasi.

Sistem ini mendukung **3 Hak Akses (Multi-Role)**:
1. **Siswa:**
   * Membaca artikel & tips pengembangan karir.
   * Menjelajahi katalog profesi dan jalur pendidikan.
   * Membandingkan jalur pendidikan.
   * Mengisi jurnal kenali diri & rencana karir masa depan.
   * Mengajukan pesan konseling ke Guru BK.
2. **Guru BK:**
   * Mengelola & mempublikasikan artikel bimbingan karir.
   * Memantau dan merespons pesan konseling dari siswa.
   * Melihat catatan dan perkembangan perencanaan karir siswa.
3. **Admin:**
   * Mengelola data master pengguna (Siswa, Guru BK, Admin).
   * Mengelola data sekolah.
   * Melihat rekapitulasi data dan laporan aktivitas secara keseluruhan.

---

## 2. 🗄️ Struktur & Pembuatan Database

Database dibuat menggunakan **MySQL / MariaDB** dengan relasi terstruktur untuk mengintegrasikan seluruh fitur platform. 

### Skema Tabel Utama:
* `pengguna` / `user`: Menyimpan kredensial login (username, password hash, nama, peran/role, dan status).
* `sekolah`: Menyimpan data referensi sekolah yang terhubung dengan akun pengguna.
* `profesi` & `jalur`: Menampung katalog pekerjaan/karir, deskripsi, persyaratannya, serta opsi jalur pendidikan.
* `artikel`: Menampung judul, isi, kategori, gambar, serta pembuat artikel (Guru BK).
* `jurnal` / `kenali_diri` / `rencana`: Menyimpan data eksplorasi diri dan perencanaan masa depan yang diinput oleh siswa.
* `konseling` / `catatan`: Menyimpan histori komunikasi/diskusi bimbingan antara siswa dan Guru BK.

*File skrip database lengkap sudah tersedia pada direktori `database/database.sql` dan `database/dummy_karvia.sql`.*

---

## 3. 📂 Struktur Direktori & Coding

Aplikasi ini menggunakan alur modul berbasis struktur folder sederhana yang teratur:

```text
karvia/
├── admin/               # Halaman dashboard & pengelolaan data untuk Admin
│   ├── index.php        # Ringkasan dashboard admin
│   ├── pengguna.php     # Manajemen akun siswa & guru BK
│   ├── sekolah.php      # Manajemen data sekolah
│   └── rekap.php        # Laporan & rekapitulasi sistem
├── guru_bk/             # Halaman fitur khusus Guru BK
│   ├── index.php        # Dashboard Guru BK
│   ├── artikel.php      # Kelola artikel bimbingan karir
│   └── catatan.php      # Memantau & membalas konseling siswa
├── siswa/               # Halaman fitur interaktif Siswa
│   ├── index.php        # Dashboard Siswa
│   ├── profesi.php      # Katalog profesi
│   ├── kenali_diri.php  # Jurnal analisis diri
│   ├── rencana.php      # Perencanaan karir masa depan
│   └── konsultasi.php   # Fitur bimbingan dengan Guru BK
├── config/              # Berkas konfigurasi sistem
│   └── koneksi.php      # Pengaturan koneksi database MySQL
├── includes/            # Komponen template universal
│   ├── header.php       # Navigasi & bagian atas halaman
│   ├── footer.php       # Bagian bawah halaman
│   └── function.php     # Helper functions & validasi sesi
├── assets/              # Berkas statis (CSS, JS, dan Gambar Pendukung)
├── uploads/             # Direktori penyimpanan unggahan berkas/gambar
├── index.php            # Landing page / Halaman utama publik
├── login.php            # Form dan proses autentikasi login
└── logout.php           # Pengakhiran sesi pengguna
```

---

## 4. 🚀 Panduan Instalasi & Jalankan di Localhost

Berikut adalah langkah-langkah untuk menjalankan website **Karvia** di komputer/laptop:

1. **Persiapan Server Lokal:**
   * Pastikan aplikasi server lokal seperti **XAMPP**, **WAMP**, atau **Laragon** sudah terinstal dan aktif (Apache & MySQL).

2. **Memindahkan File:**
   * Ekstrak / letakkan folder `karvia` ke dalam direktori publik server lokal:
     * *XAMPP:* `C:/xampp/htdocs/karvia`
     * *Laragon:* `C:/laragon/www/karvia`

3. **Impor Database:**
   * Buka browser dan akses **phpMyAdmin** (`http://localhost/phpmyadmin`).
   * Buat database baru bernama `karvia`.
   * Klik nama database `karvia`, lalu pilih tab **Import**.
   * Upload dan jalankan file `database.sql` (atau `dummy_karvia.sql` jika ingin menyertakan data sampel) dari folder `karvia/database/`.

4. **Konfigurasi Koneksi Database:**
   * Buka file `config/koneksi.php` menggunakan editor teks (VS Code, Notepad++, dll).
   * Pastikan nama database disesuaikan dengan yang dibuat di phpMyAdmin:
     ```php
     $host = "localhost";
     $user = "root";
     $pass = "";
     $db   = "karvia";

     $koneksi = mysqli_connect($host, $user, $pass, $db);
     if (!$koneksi) {
         die("Koneksi gagal: " . mysqli_connect_error());
     }
     ```

5. **Akses Website:**
   * Buka browser dan kunjungi `http://localhost/karvia`.
   * Login menggunakan kredensial akun Admin, Guru BK, atau Siswa yang telah terdaftar di database.