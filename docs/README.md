# Dokumentasi Proyek KARVIA

Folder ini adalah sumber konteks utama untuk pengembangan KARVIA. Aturan produk,
rencana database, dan urutan pengerjaan dicatat di sini agar pekerjaan bisa
dilanjutkan bertahap.

**Status saat ini:** fondasi backend dan autentikasi sedang dikerjakan pada branch
`feat/foundation-auth`. Landing/login/dashboard awal berbasis prototipe telah ada;
fitur data tiap peran akan diselesaikan pada branch terpisah sesuai `docs/05`.

**Prototipe acuan:** [`../karvia_career_vision_action.html`](../karvia_career_vision_action.html)
File tersebut jangan diubah, dipindah, atau dihapus. Fungsinya sebagai referensi
tampilan, alur, dan isi.

---

## Urutan membaca dokumen

| No | File | Isi |
|----|------|-----|
| 1 | [`00-ringkasan-proyek.md`](00-ringkasan-proyek.md) | Latar belakang, tujuan, ruang lingkup, batasan, definisi sukses |
| 2 | [`01-kebutuhan-fungsional.md`](01-kebutuhan-fungsional.md) | Peran, hak akses, daftar kebutuhan, kriteria penerimaan |
| 3 | [`02-arsitektur-dan-struktur-folder.md`](02-arsitektur-dan-struktur-folder.md) | Teknologi, struktur folder, request, konvensi kode, keamanan |
| 4 | [`03-rencana-database.md`](03-rencana-database.md) | Tabel, relasi, aturan data, file SQL dan demo |
| 5 | [`04-rencana-halaman-dan-alur.md`](04-rencana-halaman-dan-alur.md) | Peta halaman, alur login, tiap peran, empty state |
| 6 | [`05-panduan-kerja-untuk-ai.md`](05-panduan-kerja-untuk-ai.md) | Urutan build, standar kode, checklist dan definition of done |
| 7 | [`06-peta-fitur-prototipe.md`](06-peta-fitur-prototipe.md) | Pemetaan prototipe ke halaman dan data |
| 8 | [`07-rencana-kerja-dan-prioritas.md`](07-rencana-kerja-dan-prioritas.md) | Prioritas MVP dan risiko |
| 9 | [`08-keputusan-terbuka.md`](08-keputusan-terbuka.md) | Keputusan yang perlu dikonfirmasi |
| 10 | [`09-pembagian-tugas-2-orang.md`](09-pembagian-tugas-2-orang.md) | Pembagian kepemilikan file |

Dokumen utama untuk kontributor: `README.md` → `00` → `01` → `02` → `03` → `04` →
`05`. Gunakan `06`–`09` saat mengerjakan atau mengambil keputusan.

---

## Ringkasan produk

KARVIA adalah media pendukung bimbingan karier SMA/MA, bukan tes psikologis atau
penentu pilihan otomatis. Aplikasi PHP native + MySQL menggunakan PDO dan session,
dengan tiga peran: Admin, Guru BK, dan Siswa. Sistem membatasi akses Guru BK ke
catatan yang dibagikan siswa di sekolahnya, sementara Admin melihat akun dan rekap
agregat tanpa isi jurnal pribadi.

## Aturan singkat

1. Dokumen `00`–`09` adalah acuan; jika kode berbeda dari dokumen, selesaikan
   perbedaannya sebelum lanjut.
2. Jangan menambah fitur di luar MVP tanpa mencatat dan menyetujui di `08`.
3. Jangan menyentuh file HTML prototipe.
4. Perubahan skema wajib memperbarui `03`; SQL tetap satu file `database/database.sql`.
5. Gunakan PHP native, prepared statements, output escaping, CSRF pada POST, dan
   otorisasi server-side.
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

Folder aplikasi boleh dipindah. Sesuaikan `BASE_URL` di `config/koneksi.php` jika
alamat lokal berubah. Database lokal yang dipakai adalah `karvia_db`.
