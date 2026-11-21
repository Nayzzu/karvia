# Dokumentasi Proyek KARVIA

Folder ini adalah sumber konteks utama untuk pengembangan KARVIA. Aturan produk,
rencana database, dan urutan pengerjaan dicatat di sini agar pekerjaan dapat
dilanjutkan bertahap.

**Status:** aplikasi PHP native memiliki alur Admin, Guru BK, Siswa, pendaftaran mandiri, lima sekolah demo, dan artikel Guru BK. Login/daftar memakai layout responsif KARVIA. Pengalaman siswa bertahap diperbarui pada beranda, Kenali Diriku, Jelajah Profesi, Bandingkan Pilihan, Rencana Langkahku, Jurnal Refleksi, Ringkasan Konsultasi, Jalur Setelah Lulus, dan Tips & Edukasi Karier; katalog memuat 15 profesi dengan foto lokal. Verifikasi dan status kerja tercatat di `docs/03`, `docs/04`, `docs/05`, `docs/06`, dan `docs/14`.

**Prototipe acuan:** [`karvia_career_vision_action.html`](karvia_career_vision_action.html)
File tersebut jangan diubah atau dihapus.

## Urutan membaca dokumen

| No | File | Isi |
|----|------|-----|
| 1 | [`00-ringkasan-proyek.md`](00-ringkasan-proyek.md) | Latar belakang, tujuan, ruang lingkup, batasan, definisi sukses |
| 2 | [`01-kebutuhan-fungsional.md`](01-kebutuhan-fungsional.md) | Peran, hak akses, kebutuhan, kriteria penerimaan |
| 3 | [`02-arsitektur-dan-struktur-folder.md`](02-arsitektur-dan-struktur-folder.md) | Teknologi, struktur folder, request, keamanan |
| 4 | [`03-rencana-database.md`](03-rencana-database.md) | Tabel, relasi, aturan data, SQL dan demo |
| 5 | [`04-rencana-halaman-dan-alur.md`](04-rencana-halaman-dan-alur.md) | Peta halaman dan alur tiap peran |
| 6 | [`05-panduan-kerja-untuk-ai.md`](05-panduan-kerja-untuk-ai.md) | Urutan build, standar kode, checklist, definition of done |
| 7 | [`06-peta-fitur-prototipe.md`](06-peta-fitur-prototipe.md) | Pemetaan prototipe ke halaman dan data |
| 8 | [`07-rencana-kerja-dan-prioritas.md`](07-rencana-kerja-dan-prioritas.md) | Prioritas MVP dan risiko |
| 9 | [`08-keputusan-terbuka.md`](08-keputusan-terbuka.md) | Keputusan yang perlu dikonfirmasi |
| 10 | [`09-pembagian-tugas-2-orang.md`](09-pembagian-tugas-2-orang.md) | Pembagian kepemilikan file |
| 11 | [`10-rancangan-landing-page.md`](10-rancangan-landing-page.md) | Rancangan UI landing dan kriteria revisi mobile navbar/carousel |
| 12 | [`11-rancangan-landing-page-utuh.md`](11-rancangan-landing-page-utuh.md) | Konteks, susunan aktual, verifikasi, dan revisi mobile disetujui |
| 13 | [`12-panduan-instalasi.md`](12-panduan-instalasi.md) | Cara menjalankan lokal, akun demo, backup |
| 14 | [`13-konteks-proyek-untuk-ai.md`](13-konteks-proyek-untuk-ai.md) | Konteks proyek dan aturan kerja untuk AI |
| 15 | [`14-registrasi-siswa-dan-data-tambahan.md`](14-registrasi-siswa-dan-data-tambahan.md) | Kebijakan pendaftaran siswa dan SQL tambahan Bandar Lampung |

## Ringkasan produk

KARVIA adalah media pendukung bimbingan karier SMA/MA, bukan tes psikologis atau
penentu pilihan otomatis. Aplikasi PHP native + MySQL menggunakan PDO dan session,
dengan tiga peran: Admin, Guru BK, dan Siswa. Guru BK hanya melihat catatan yang
dibagikan siswa di sekolahnya, serta dapat menerbitkan artikel edukasi publik. Admin
mengelola akun/sekolah dan melihat rekap, bukan isi jurnal pribadi.

## Aturan kerja

1. Dokumen `00`–`09` adalah acuan; selesaikan perbedaan kode/dokumen sebelum lanjut.
2. Jangan menambah fitur di luar MVP tanpa persetujuan dan pencatatan di `08`.
3. Jangan mengubah file HTML prototipe.
4. Perubahan skema wajib memperbarui `03`; SQL tetap di `database/` (struktur +
   data contoh).
5. Gunakan PHP native, PDO prepared statement, escape output, CSRF untuk POST,
   dan pemeriksaan akses server-side.
6. Antarmuka berbahasa Indonesia dan memakai istilah sederhana.

## Struktur ringkas

```text
karvia/
├── admin/
├── assets/img/profesi/
├── config/koneksi.php
├── database/{database,dummy_karvia,dummy_karvia2}.sql
├── docs/
├── guru_bk/
├── includes/{header,footer,function}.php
├── siswa/
├── uploads/artikel/
├── index.php
├── daftar.php
├── login.php
├── logout.php
└── profil.php
```

Jika alamat lokal berubah, sesuaikan `BASE_URL` di `config/koneksi.php`. Database
demo lokal bernama `karvia_db`.
