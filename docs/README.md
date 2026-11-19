# Dokumentasi Proyek KARVIA

Folder ini adalah **sumber konteks utama** untuk pengembangan ulang KARVIA.
Semua keputusan, aturan, rencana database, dan urutan pengerjaan dicatat di sini agar
pekerjaan tetap konsisten walaupun dikerjakan bertahap atau dilanjutkan di sesi lain.

**Status proyek:** Fase 0 — kerangka folder dan dokumentasi.
Belum ada kode aplikasi yang ditulis; file PHP masih berisi komentar placeholder.

**Prototipe acuan:** [`../karvia_career_vision_action.html`](../karvia_career_vision_action.html)
File tersebut **jangan diubah, dipindah, atau dihapus**. Fungsinya hanya sebagai
referensi tampilan, alur, dan isi. Kekurangannya dijelaskan di `06-peta-fitur-prototipe.md`.

---

## Urutan membaca dokumen

| No | File | Isi |
|----|------|-----|
| 1 | [`00-ringkasan-proyek.md`](00-ringkasan-proyek.md) | Latar belakang, tujuan, ruang lingkup, batasan, definisi sukses |
| 2 | [`01-kebutuhan-fungsional.md`](01-kebutuhan-fungsional.md) | Peran, hak akses, daftar kebutuhan, kriteria penerimaan |
| 3 | [`02-arsitektur-dan-struktur-folder.md`](02-arsitektur-dan-struktur-folder.md) | Teknologi, struktur folder, alur request, konvensi kode, keamanan |
| 4 | [`03-rencana-database.md`](03-rencana-database.md) | Rancangan tabel, relasi, aturan data, file SQL & data contoh 2 sekolah |
| 5 | [`04-rencana-halaman-dan-alur.md`](04-rencana-halaman-dan-alur.md) | Peta halaman, URL, alur login, alur tiap peran, empty state |
| 6 | [`05-panduan-kerja-untuk-ai.md`](05-panduan-kerja-untuk-ai.md) | Aturan kerja, urutan build, standar kode, checklist, definition of done |
| 7 | [`06-peta-fitur-prototipe.md`](06-peta-fitur-prototipe.md) | Pemetaan fitur HTML lama ke halaman/tabel baru; daftar data contoh |
| 8 | [`07-rencana-kerja-dan-prioritas.md`](07-rencana-kerja-dan-prioritas.md) | Prioritas MVP, rencana harian menuju Rabu, skrip demo, risiko |
| 9 | [`08-keputusan-terbuka.md`](08-keputusan-terbuka.md) | Pertanyaan yang perlu dikonfirmasi ke Cindy + usulan default |
| 10 | [`09-pembagian-tugas-2-orang.md`](09-pembagian-tugas-2-orang.md) | Jobdesk 2 pengembang, kepemilikan file, timeline paralel |

Dokumen yang perlu dibaca lebih dulu oleh AI/kontributor:
`README.md` → `00` → `01` → `02` → `03` → `04` → `05`.
Dokumen `06`–`09` dipakai saat mengerjakan, membagi tugas, atau mengambil keputusan.

---

## Ringkasan satu paragraf

KARVIA adalah media bimbingan karier untuk siswa SMA/MA. Prototipe saat ini baru
berupa satu file HTML statis: tampilan sudah ada, tetapi login tidak nyata, data
hanya di memori, dashboard Guru BK berisi angka dummy, dan tidak ada Admin.
Cindy (bidang keguruan, bukan programmer) membutuhkan sistem yang benar-benar bisa
dipakai: **Admin** yang memantau pengguna dan sekolah, **multi-sekolah** (awal 2
sekolah), **akun dan login nyata**, **manajemen akses**, serta **data siswa yang
tersimpan dan terhubung** ke Guru BK di sekolah yang sama.

---

## Aturan singkat untuk semua pihak

1. Dokumen di folder ini adalah **acuan**. Jika kode dan dokumen berbeda, hentikan
   pekerjaan dan perbaiki salah satunya terlebih dahulu.
2. Jangan menambah fitur di luar ruang lingkup MVP tanpa mencatatnya di
   `08-keputusan-terbuka.md`.
3. Jangan menyentuh file HTML prototipe.
4. Perubahan skema database **wajib** disertai pembaruan `03-rencana-database.md`.
5. Bahasa antarmuka: **Indonesia**. Nama tabel/kolom: **bahasa Indonesia**,
   `snake_case`, agar mudah dipahami pemilik proyek.
6. Utamakan solusi sederhana yang bisa dijelaskan ke Cindy, bukan solusi paling canggih.

---

## Struktur folder & lokasi

Struktur **tidak terikat pada satu path**. Bentuk baku ada di `02` bagian 2.
Ringkasnya:

```text
karvia/
├── admin/index.php
├── assets/{css,js,images}/
├── config/koneksi.php
├── database/database.sql
├── guru_bk/index.php
├── includes/{header,footer,function}.php
├── siswa/index.php
├── docs/
├── index.php
├── login.php
├── logout.php
├── README.md
└── karvia_career_vision_action.html
```

Folder proyek boleh diletakkan langsung di `www/` (mis. `www/karvia`) atau
bertingkat (mis. contoh `www/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18`).
Yang berubah saat folder dipindah hanyalah `BASE_URL` di `config/koneksi.php`:

| Cara akses | `BASE_URL` |
|------------|------------|
| `http://localhost/karvia` | `/karvia` |
| `http://localhost/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18` | `/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_18` |
| Virtual host `http://karvia.test` | `''` |

Database yang direncanakan (belum dibuat):

- `database/database.sql` — bagian A struktur semua tabel + bagian B data contoh.
- `database/backup_YYYY-MM-DD.sql` — hanya saat backup, bukan bagian awal.
