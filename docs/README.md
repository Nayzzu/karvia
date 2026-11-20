# Dokumentasi Proyek KARVIA

Folder ini adalah sumber konteks utama untuk pengembangan KARVIA. Aturan produk,
rencana database, dan urutan pengerjaan dicatat di sini agar pekerjaan dapat
dilanjutkan bertahap.

**Status:** fondasi backend/autentikasi telah dipush pada `feat/foundation-auth`.
Pengelolaan sekolah Admin sedang dikerjakan pada `feat/admin-schools`. Fitur Admin
lainnya, Guru BK, dan Siswa akan dilanjutkan pada branch fitur terpisah sesuai
`docs/05`.

**Prototipe acuan:** [`../karvia_career_vision_action.html`](../karvia_career_vision_action.html)
File tersebut jangan diubah, dipindah, atau dihapus.

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
| 11 | [`10-rancangan-landing-page.md`](10-rancangan-landing-page.md) | Konteks, isi, dan rancangan UI landing page |
| 12 | [`11-rancangan-landing-page-utuh.md`](11-rancangan-landing-page-utuh.md) | Konteks, susunan aktual, verifikasi, dan rencana perubahan landing page |

## Ringkasan produk

KARVIA adalah media pendukung bimbingan karier SMA/MA, bukan tes psikologis atau
penentu pilihan otomatis. Aplikasi PHP native + MySQL menggunakan PDO dan session,
dengan tiga peran: Admin, Guru BK, dan Siswa. Guru BK hanya melihat catatan yang
dibagikan siswa di sekolahnya. Admin mengelola akun/sekolah dan melihat rekap,
bukan isi jurnal pribadi.

## Aturan kerja

1. Dokumen `00`–`09` adalah acuan; selesaikan perbedaan kode/dokumen sebelum lanjut.
2. Jangan menambah fitur di luar MVP tanpa persetujuan dan pencatatan di `08`.
3. Jangan mengubah file HTML prototipe.
4. Perubahan skema wajib memperbarui `03`; SQL tetap di satu file.
5. Gunakan PHP native, PDO prepared statement, escape output, CSRF untuk POST,
   dan pemeriksaan akses server-side.
6. Antarmuka berbahasa Indonesia dan memakai istilah sederhana.

## Struktur ringkas

```text
karvia/
├── admin/
├── assets/{css,js,images}/
├── config/koneksi.php
├── database/database.sql
├── docs/
├── guru_bk/
├── includes/{header,footer,function}.php
├── siswa/
├── index.php
├── login.php
└── logout.php
```

Jika alamat lokal berubah, sesuaikan `BASE_URL` di `config/koneksi.php`. Database
demo lokal bernama `karvia_db`.
