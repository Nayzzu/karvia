# 04 — Rencana Halaman & Alur

Semua halaman dirender di server. Setiap halaman adalah file PHP tersendiri di folder perannya
(lihat `02`). Setelah POST yang mengubah data, lakukan **redirect** (pola
Post/Redirect/Get) agar refresh tidak mengirim ulang data.

---

## 1. Peta halaman

```
Publik
├── Landing dengan kartu artikel
├── Baca artikel (`artikel.php`)
├── Login
└── Pendaftaran siswa (`daftar.php`)

Admin
├── Dashboard
├── Sekolah
│   ├── Daftar sekolah
│   ├── Tambah / ubah sekolah
│   └── Detail sekolah
├── Pengguna
│   ├── Daftar pengguna (filter)
│   ├── Tambah pengguna
│   └── Ubah pengguna / reset password / aktif-nonaktif
├── Rekap
└── Profil & ganti password

Guru BK
├── Dashboard
├── Catatan siswa (yang dibagikan)
│   └── Detail catatan + tindak lanjut
├── Artikel edukasi
│   ├── Daftar artikel milik sendiri
│   ├── Buat / ubah artikel (judul, gambar, isi)
│   └── Hapus artikel milik sendiri
└── Profil & ganti password

Siswa
├── Beranda & pengantar
├── 1. Kenali Diriku
├── 2. Jelajah Profesi
├── Jalur Setelah Lulus
├── 3. Bandingkan Pilihan
├── 4. Rencana Langkahku
├── 5. Jurnal Refleksi
├── Ruang Konsultasi BK
├── Tips & Edukasi Karier
└── Profil & ganti password
```

---

### 1.1 Artikel publik

- `index.php` menampilkan artikel terbaru lintas sekolah dalam kartu: gambar, judul, nama Guru BK, tanggal, potongan isi, dan tautan baca lengkap.
- `artikel.php?id={id}` menampilkan artikel lengkap: gambar, judul, penulis, tanggal terbit, isi teks, dan navigasi kembali ke landing.
- Artikel tidak memerlukan login untuk dibaca. ID tidak valid menghasilkan 404. Artikel yang telah diterbitkan tetap publik walau akun penulis kemudian dinonaktifkan.

### 1.2 Pengelolaan artikel Guru BK

- `guru_bk/artikel.php`: daftar artikel yang dimiliki Guru BK login, tautan buat/ubah, dan aksi hapus memakai POST + CSRF + konfirmasi.
- `guru_bk/artikel_form.php`: judul, gambar, isi. Nama penulis diambil dari akun aktif, bukan input pengguna. Artikel baru langsung publik setelah disimpan.
- Gambar JPG/PNG/WebP maksimum 2 MB; validasi MIME melalui `finfo`, dimensi/decoder gambar, nama penyimpanan acak, dan direktori upload tidak boleh menjalankan PHP.
- Edit artikel mempertahankan gambar lama jika gambar baru tidak dipilih. Hapus file lama hanya setelah perubahan database berhasil; hapus artikel memakai transaksi/urutan aman agar gagal operasi tidak menghilangkan data tanpa sengaja.
- Isi artikel berupa teks biasa dengan baris baru, semua output di-escape.

## 2. Login

### 2.1 Perubahan dari prototipe

Prototipe meminta pengguna memilih peran lalu mengetik nama + password apa saja.
Sistem baru:

- **Tidak ada pemilihan peran.** Peran dibaca dari akun.
- **Tidak ada nama bebas.** Login memakai `username`.
- Password diverifikasi dengan hash.
- Akun nonaktif ditolak.

Alasan: agar “siapa yang memakai web” bisa diketahui dan tidak ada orang yang
masuk hanya dengan mengetik nama.

### 2.2 Tampilan

- Layout responsif dua kolom: panel hijau KARVIA dengan pesan eksplorasi karier, form putih bersih; pada layar sempit panel bertumpuk di atas form.
- Judul KARVIA, subjudul “Career Vision & Action”, dan kredit pengembang tetap tampil.
- Field berlabel dan terhubung: **Username**, **Password**.
- Link **Daftar di sini** membuka registrasi siswa; tidak tersedia untuk role Admin/Guru BK.
- Tombol “Masuk ke KARVIA”; autentikasi tetap memakai proses server-side yang ada.
- CSS khusus halaman berada di `assets/css/auth.css`, dibatasi di `.auth-page` agar tidak mengubah halaman lain.
- Tautan pendaftaran tersedia khusus untuk siswa; peran pendaftar dipatok menjadi siswa oleh server. Akun Admin/Guru BK tetap dibuat Admin.
- Tidak ada petunjuk password demo di halaman produksi.

### 2.3 Alur

```
Buka halaman
  → sudah login? → redirect ke dashboard sesuai peran
  → belum → tampilkan form

Submit username + password
  → validasi kosong
  → cari pengguna berdasarkan username
  → password_verify?
       tidak → pesan umum "Username atau password salah"
       ya → cek status
              nonaktif → pesan "Akun Anda dinonaktifkan. Hubungi Admin."
              aktif   → simpan session, perbarui last_login_at & login_count,
                        catat log login, redirect sesuai peran
```

Redirect tujuan:

| Peran | Halaman awal |
|-------|--------------|
| Admin | `admin/index.php` |
| Guru BK | `guru_bk/index.php` |
| Siswa | `siswa/index.php` |

Jika pengguna membuka halaman lain saat belum login, simpan tujuan awal
(opsional) lalu arahkan ke login.

### 2.4 Pendaftaran mandiri Siswa

- `daftar.php` hanya membuat akun dengan peran `siswa`; request tidak dapat memilih role.
- Pendaftar memilih sekolah aktif dan mengisi nama, kelas, username, password, konfirmasi password.
- Username tervalidasi unik dengan format 3–50 karakter (`a-z`, angka, `.`, `_`, `-`); password 8–72 karakter dan disimpan sebagai hash.
- Setelah berhasil, siswa langsung login dan diarahkan ke dashboard.
- Admin/Guru BK tidak memiliki pendaftaran publik.

### 2.5 Logout

- Hapus data session, hancurkan cookie session, redirect ke login.
- Tampilkan flash “Anda telah keluar.”

---

## 3. Admin

### 3.1 Dashboard (`admin/index.php`)

Susunan yang disarankan:

1. Banner sambutan: “Panel Admin KARVIA”, nama admin, tanggal.
2. Lima kartu statistik **nyata**:
   - Sekolah aktif
   - Akun terdaftar (rincian: x siswa, y Guru BK)
   - Sedang online (aktif dalam 5 menit terakhir)
   - Pernah masuk
   - Aktif 30 hari terakhir
3. Widget **Pengguna Sedang Aktif Sekarang** (daftar interaksi online ≤5 menit).
4. Tabel **Rekap per sekolah**:
   - Nama sekolah
   - Jenjang / kota
   - Jumlah siswa
   - Jumlah Guru BK
   - Catatan dibagikan
   - Aktivitas login terakhir
   - Aksi “Detail”
4. Panel **Aktivitas terbaru** (login/akun baru/nonaktif) — P1.
5. Pintasan: “Tambah sekolah”, “Tambah pengguna”.

Empty state: jika belum ada sekolah, tampilkan ajakan “Tambahkan sekolah pertama
untuk mulai.” Jangan menampilkan angka palsu.

### 3.2 Daftar sekolah (`admin/sekolah.php`)

Status implementasi: daftar sekolah, pencarian, jumlah akun, ubah status aktif/nonaktif
(POST + CSRF + konfirmasi), dan tautan ke form/detail tersedia pada `admin/sekolah.php`.

Kolom tabel:

| Kolom | Isi |
|-------|-----|
| Nama sekolah | Nama + jenjang |
| Kota/Provinsi | |
| Siswa | Jumlah akun siswa |
| Guru BK | Jumlah akun guru |
| Status | Badge aktif/nonaktif |
| Aksi | Detail, Ubah, Aktif/Nonaktif |

Fitur:

- Pencarian nama/kota.
- Tombol “Tambah sekolah”.
- Pagination bila > 25 baris.

### 3.3 Form sekolah (`admin/sekolah_form.php`)

Status implementasi: tambah/ubah dengan validasi server, pemeriksaan NPSN unik,
CSRF, prepared statement, flash, dan Post/Redirect/Get tersedia pada
`admin/sekolah_form.php`.

Field:

| Field | Wajib | Validasi |
|-------|:-----:|----------|
| Nama sekolah | Ya | 3–150 karakter |
| NPSN | Tidak | maks. 20, unik bila diisi |
| Jenjang | Ya | `SMA` / `MA` |
| Alamat | Tidak | maks. 500 |
| Kota | Tidak | maks. 100 |
| Provinsi | Tidak | maks. 100 |
| Status | Ya | `aktif` / `nonaktif` |

Aksi: Simpan, Batal. Setelah simpan → kembali ke daftar dengan flash.
Ubah sekolah tidak boleh mengubah data historis siswa.

### 3.4 Detail sekolah (`admin/sekolah_detail.php`)

Status implementasi: profil sekolah, jumlah siswa/Guru BK/catatan, dan daftar akun
terkait tanpa isi jurnal tersedia pada `admin/sekolah_detail.php`.

- Kartu profil sekolah.
- Tiga angka: siswa, Guru BK, catatan dibagikan.
- Tabel daftar Guru BK sekolah tersebut.
- Tabel daftar siswa (nama, kelas, status, terakhir login).
- Tombol “Ubah sekolah” dan “Tambah pengguna di sekolah ini”.
- Jika menonaktifkan sekolah: konfirmasi menjelaskan bahwa akun di dalamnya tidak
  otomatis nonaktif (keputusan terpisah) — atau jika disepakati, nonaktifkan semua.
  **Usulan MVP:** sekolah nonaktif hanya menyembunyikan pilihan di form akun baru;
  akun yang ada tetap seperti semula sampai Admin menonaktifkannya.

### 3.5 Daftar pengguna (`admin/pengguna.php`)

Status implementasi: daftar/filter, ubah status via POST+CSRF, reset password sementara,
konfirmasi aksi, larangan Admin menonaktifkan akunnya sendiri, dan escape output sudah
tersedia. Uji HTTP membuktikan GET, CSRF invalid ditolak (403), XSS di-escape, payload
SQL tidak merusak pencarian, reset tampil sekali dan password sementara dapat dipakai,
password lama ditolak, aktivasi/nonaktifasi memblokir/mengizinkan login, serta Admin
tidak dapat menonaktifkan dirinya.

Filter:

- Kata kunci (nama/username).
- Peran: semua / admin / guru_bk / siswa.
- Sekolah: semua / per sekolah.
- Status akun: semua / aktif / nonaktif.
- Aktivitas: semua / sedang online / offline.
- Tombol “Terapkan” dan “Reset”.

Kolom:

| Kolom | Isi |
|-------|-----|
| Nama / Username | Nama lengkap + badge status online/offline + username |
| Peran | Label peran |
| Sekolah | Nama sekolah / “Global” untuk admin |
| Status Akun | Badge aktif/nonaktif |
| Terakhir login | Tanggal + jam, atau “Belum pernah” |
| Aksi | Ubah, Reset password, Nonaktifkan/Aktifkan |

Aturan:

- Admin tidak bisa menonaktifkan dirinya sendiri.
- Tombol “Nonaktifkan” berwarna merah dan memakai konfirmasi.
- Tidak ada tombol “Hapus permanen” di MVP.
- Sorting default: terbaru dibuat / nama.

### 3.6 Form pengguna (`admin/pengguna_form.php`)

Status implementasi: tambah/ubah akun, validasi server, username unik, hash password,
CSRF, prepared statement, dan peran immutable saat ubah sudah dibuat. Uji HTTP berhasil
membuat/mengubah akun, mengabaikan percobaan ubah peran, menolak CSRF invalid, dan
menampilkan input XSS sebagai teks. Login dengan password sementara berhasil; password
lama ditolak dan password sementara hanya tampil satu kali.

**Tambah:**

| Field | Wajib | Catatan |
|-------|:-----:|---------|
| Peran | Ya | admin / guru_bk / siswa |
| Sekolah | Ya untuk guru & siswa | Otomatis kosong/disabled untuk admin |
| Nama lengkap | Ya | |
| Username | Ya | 3–50, huruf kecil/angka/titik/garis bawah, unik |
| Password awal | Ya | minimal 8 karakter |
| Konfirmasi password | Ya | harus sama |
| Kelas | Tidak | tampil untuk siswa |
| Jurusan | Tidak | tampil untuk siswa |
| NISN | Tidak | tampil untuk siswa |
| NIP | Tidak | tampil untuk guru |
| No. telepon | Tidak | |

**Ubah:**

- Peran boleh diubah hanya jika tidak menimbulkan data menggantung. **Usulan MVP:**
  peran tidak dapat diubah setelah dibuat; jika salah, nonaktifkan dan buat akun baru.
  Ini mencegah kebingungan data.
- Sekolah boleh diubah (mis. siswa pindah).
- Password tidak diubah di form ini; gunakan “Reset password”.

**Reset password:**

- Admin menekan tombol → konfirmasi → sistem membuat password sementara.
- Password sementara ditampilkan **sekali** di halaman hasil, dengan tombol salin.
- Simpan hanya hash-nya.
- Catat aksi di `log_aktivitas`.

**Nonaktifkan / aktifkan:**

- POST + CSRF + konfirmasi.
- Nonaktif: akun tidak bisa login; session aktif akan ditolak pada request berikutnya.
- Aktif: akun bisa login lagi.
- Data karier dan riwayat tetap utuh.

### 3.7 Rekap (`admin/rekap.php`)

Status implementasi: ringkasan total dan rincian per sekolah tersedia dari database;
halaman tidak menampilkan isi jurnal siswa. GET lolos uji HTTP lokal.

- Ringkasan total: sekolah, siswa, guru BK, catatan.
- Tabel per sekolah dengan kolom jumlah dan status akses.
- Filter jenjang/kota (P1).
- Tombol cetak (P2).

---

## 4. Guru BK

### 4.1 Dashboard (`guru_bk/index.php`)

Status implementasi: empat statistik DB sekolah sendiri (siswa aktif, catatan baru,
ditindaklanjuti, total dibagikan), tabel ringkas, dan tautan detail tersedia. Query
membatasi sekolah catatan serta sekolah siswa.

Banner: “Panel Guru BK — [Nama Sekolah]”.

Kartu statistik (semua dihitung untuk sekolah Guru BK):

| Kartu | Sumber |
|-------|--------|
| Siswa di sekolah | `pengguna` role siswa, sekolah sama |
| Catatan baru | `berbagi_catatan.status = baru` |
| Sudah ditindaklanjuti | `status = ditindaklanjuti` |
| Total catatan dibagikan | semua baris berbagi di sekolah |

Tabel “Catatan Siswa”:

- Nama siswa + kelas
- Dibagikan pada
- Status (baru/dibaca/ditindaklanjuti)
- Jumlah tindak lanjut
- Aksi “Buka detail”

Filter status + pencarian nama.
Empty state: “Belum ada siswa yang membagikan catatan karier.”

Status implementasi: filter/pencarian, daftar hanya catatan yang dibagikan di sekolah
Guru BK, jumlah tindak lanjut, dan tautan detail tersedia. Uji HTTP untuk sekolah
terpisah menunjukkan daftar sekolah lain tidak mengungkap siswa sekolah pertama.

**Dihapus dari prototipe:**

- “32 Siswa Aktif” hardcoded.
- “6 Permintaan Jadwal” (tidak ada fitur jadwal).
- “Status Sistem Aktif 100%” (tidak bermakna).
- Dua baris dummy Ahmad/Siti yang tidak berubah.

### 4.2 Detail catatan (`guru_bk/catatan_detail.php`)

Status implementasi: data eksplorasi, jurnal, dan pesan hanya diambil setelah catatan
terbukti dibagikan ke sekolah Guru BK dan siswa masih terdaftar di sekolah itu. Tindak
lanjut tersimpan bersama perubahan status dalam transaksi. Uji HTTP lintas sekolah
menghasilkan 404; CSRF invalid 403; XSS di-escape; tindak lanjut tersimpan dan status
berubah. Cetak memakai `window.print()`.

Saat dibuka:

- Validasi bahwa siswa berada di sekolah Guru BK.
- Jika status `baru`, ubah menjadi `dibaca` dan isi `dibaca_pada`.
- Jika bukan sekolahnya → 403.

Bagian halaman:

1. **Identitas**: nama, kelas, sekolah, waktu dibagikan, status.
2. **Pesan siswa** (jika ada).
3. **Kenali Diriku**: mapel favorit, kekuatan, catatan minat.
4. **Profesi disimpan**: daftar bookmark.
5. **Bandingkan Pilihan**: A vs B + catatan + kriteria.
6. **Rencana Langkahku**: tujuan + dua langkah.
7. **Jurnal Refleksi**: daftar entri terbaru (tanggal + isi).
8. **Tindak Lanjut**:
   - riwayat catatan Guru BK;
   - form tambah catatan (wajib isi);
   - setelah simpan → status `ditindaklanjuti`, flash sukses.
9. Tombol “Cetak ringkasan”.

Jika siswa belum mengisi sebagian data, tampilkan “Belum diisi”, bukan halaman error.

### 4.3 Batas akses

- Guru BK **tidak** memiliki menu daftar semua siswa sekolah (hanya yang dibagikan).
  Ini menjaga privasi. Jika Cindy ingin melihat semua siswa, keputusan harus dicatat
  di `08`.
- Guru BK tidak bisa mengubah data siswa.
- Guru BK tidak bisa melihat sekolah lain, termasuk lewat URL ID.

---

## 5. Siswa

### 5.1 Beranda (`siswa/index.php`)

Status implementasi: beranda merangkum progres dari data DB, memilih langkah eksplorasi pertama yang belum terisi, dan menampilkan status berbagi catatan tanpa membuka isi pribadi.

- Sapaan personal dan pengantar suportif; tidak memberi skor atau label psikologis.
- Progres 0–5 langkah memakai keberadaan data tersimpan: Kenali Diriku, Jelajah Profesi, Bandingkan Pilihan, Rencana Langkahku, Jurnal Refleksi.
- Tombol utama melanjutkan langkah pertama yang belum memiliki data; jika semua terisi, arahkan ke ringkasan konsultasi.
- Kartu langkah menunjukkan status dan tautan untuk mulai, melihat, atau memperbarui.
- Status konsultasi menunjukkan belum dibagikan atau status terbaru (`baru`, `dibaca`, `ditindaklanjuti`) dan waktu berbagi.
- Tegaskan catatan tetap privat sampai siswa memilih membagikannya; akses menuju `siswa/konsultasi.php` untuk meninjau ringkasan.

### 5.2 Kenali Diriku (`siswa/kenali_diri.php`)

Status implementasi: formulir tersimpan dengan upsert per siswa, pilihan tervalidasi
terhadap allowlist, catatan dibatasi 5.000 karakter, dan output di-escape. Tampilan
menyertakan prompt refleksi, kontrol pilihan interaktif, keterangan privasi, status
isian tersimpan, dan tautan ke langkah berikutnya; tidak memberi skor/label psikologis.

Field:

- Mapel/bidang favorit (checkbox, boleh lebih dari satu):
  - Matematika & Sains
  - Bahasa & Sastra
  - Ilmu Sosial & Keagamaan
  - Teknologi & Kewirausahaan
- Kekuatan (checkbox):
  - Komunikasi & Berdiskusi
  - Analitis & Memecahkan Masalah
  - Kreatif & Desain Visual
  - Kepemimpinan & Organisasi
  - Empati & Membantu Sesama
  - Teliti & Terstruktur
- Catatan minat/bayangan karier (textarea).

Simpan → upsert `refleksi_diri` → flash → arahkan ke Jelajah Profesi (seperti
prototipe) atau tetap di halaman. **Usulan:** tetap di halaman dengan flash dan
tombol “Lanjut ke Jelajah Profesi” agar siswa bisa memperbaiki.

### 5.3 Jelajah Profesi (`siswa/profesi.php`)

Status implementasi: daftar dari DB, filter kategori, dan tambah/hapus bookmark via
POST+CSRF tersedia.

- Filter kategori: Semua, Kesehatan, Teknologi, Pendidikan, Bisnis.
- Kartu profesi dari tabel `profesi` (hanya status aktif).
- Tombol bookmark:
  - jika belum disimpan: ikon bookmark abu;
  - jika sudah: ikon terisi hijau;
  - aksi POST toggle, halaman kembali ke posisi filter yang sama.
- Tombol “Pilih untuk Bandingkan” → `siswa/bandingkan.php?profesi=ID`.
- Empty state jika belum ada profesi.

### 5.4 Jalur Setelah Lulus (`siswa/jalur.php`)

Status implementasi: halaman informasi statis dengan kartu ringkas lima jalur
(perguruan tinggi, vokasi, pelatihan/sertifikasi, bekerja, wirausaha), rincian
"Coba pertimbangkan bila", daftar hal yang perlu dicek, satu langkah kecil per
jalur, panduan membandingkan, dan pengingat memeriksa sumber resmi.

Konten statis dari prototipe (PTU/PTKIN, vokasi, kerja, wirausaha, sertifikasi).
Tidak perlu database pada MVP. Tidak menampilkan angka biaya, daya tampung, atau
peluang kerja yang tidak dapat dipastikan.

### 5.5 Bandingkan (`siswa/bandingkan.php`)

Status implementasi: form dua pilihan dengan catatan dan kriteria allowlist tersimpan
lewat upsert per siswa. Tampilan kini memakai dua kartu responsif, prompt refleksi,
status data tersimpan, tautan lanjut ke rencana, dan pengingat bahwa sistem tidak
menilai atau menetapkan pilihan.

Field:

- Opsi A: nama + catatan kelebihan/tantangan.
- Opsi B: nama + catatan.
- Kriteria: checkbox (Kesesuaian Minat, Peluang Kerja, Biaya & Durasi, Restu Orang Tua).

Jika datang dari tombol profesi, isi otomatis opsi A dengan nama profesi dan jalur
pendidikannya. Simpan → upsert `pilihan_banding` → flash → arahkan ke Rencana.

### 5.6 Rencana Langkahku (`siswa/rencana.php`)

Status implementasi: tujuan dan dua langkah wajib divalidasi, dibatasi 255 karakter,
dan disimpan dengan upsert per siswa. Tampilan memberi panduan memecah arah menjadi
tindakan realistis, contoh pertanyaan, pengingat dukungan BK, status tersimpan,
dan tautan ke Jurnal; tanpa checklist palsu karena skema belum menyimpan progres
per langkah.

- Tujuan sementara berupa jurusan, bidang, keterampilan, atau pertanyaan untuk dijelajahi.
- Langkah kecil pertama dan langkah berikutnya; siswa boleh mengubah rencana kapan saja.
- Simpan → upsert `rencana_karier` → flash → tetap di halaman; tombol lanjut tersedia menuju Jurnal.

### 5.7 Jurnal (`siswa/jurnal.php`)

Status implementasi: jurnal pribadi memakai form append dan riwayat terbaru di atas.
Tampilan menjelaskan privasi, memberi prompt refleksi, menghitung entri tersimpan,
dan menautkan ke Konsultasi; minimal satu bagian wajib terisi. CSRF, batas panjang,
prepared statement, dan escaping tetap aktif.

- Form: hal baru, hambatan, dukungan.
- Simpan → **insert** baris baru di `jurnal_refleksi`.
- Riwayat berisi tanggal dan bagian yang diisi, terbaru di atas.
- Jurnal tidak terlihat oleh Guru BK sebelum siswa membagikan catatan; Admin tidak dapat membaca isi.
- Tidak ada edit/hapus pada MVP; entri baru menambahkan catatan, bukan mengganti catatan lama.

### 5.8 Konsultasi (`siswa/konsultasi.php`)

Status implementasi: halaman **Ruang Konsultasi** merangkum data eksplorasi dan
seluruh jurnal dari DB, menampilkan identitas serta status berbagi dengan label yang
mudah dipahami, dan menyediakan tautan pembaruan tiap bagian. Siswa dapat membagikan
ulang dengan pesan opsional melalui POST + CSRF; ringkasan dan seluruh jurnal yang
tampil ikut dibagikan, dan Guru BK melihat isi terbaru saat membuka catatan. Tombol
cetak memanggil `window.print()` dengan gaya cetak khusus; uji visual/cetak browser
belum dilakukan. Output teks di-escape.

- Ringkasan dari database:
  - Nama, kelas, sekolah
  - Minat/kekuatan dan catatan minat
  - Profesi yang disimpan
  - Perbandingan dan kriteria
  - Tujuan serta dua langkah rencana
  - Seluruh entri jurnal yang tersimpan (terbaru lebih dulu)
- Kotak status berbagi:
  - Belum pernah dibagikan.
  - Terakhir dibagikan [tanggal], status [baru/dibaca/ditindaklanjuti].
- Pesan opsional untuk Guru BK (textarea).
- Tombol “Bagikan catatan ke Guru BK” / “Bagikan ulang ringkasan”:
  - konfirmasi yang menyebut ringkasan dan seluruh jurnal yang tampil;
  - pesan opsional, maksimum 2.000 karakter;
  - POST + CSRF;
  - upsert `berbagi_catatan` dan redirect setelah berhasil.
- Tombol “Cetak / Simpan PDF” → `window.print()` dengan CSS cetak.
- Jangan pernah menampilkan modal sukses tanpa benar-benar menyimpan.

### 5.9 Tips (`siswa/tips.php`)

Status implementasi: lima tips ringkas dengan tindakan kecil dan tautan ke halaman
refleksi/jalur/konsultasi tersedia. Artikel edukasi yang diterbitkan Guru BK dimuat
dari tabel `artikel` (maksimal 6 terbaru) dalam carousel scroll-snap yang responsif,
dilengkapi tombol sebelumnya/berikutnya, indikator posisi, sampul, penulis, tanggal,
dan tautan baca. Empty state tampil jika belum ada artikel; disclaimer bahwa KARVIA
bukan tes psikologis tetap ada.


### 5.10 Profil (`profil.php`)

Status implementasi: semua peran dapat melihat profil, mengubah nama/telepon, serta
mengganti password dengan verifikasi password lama, validasi konfirmasi, CSRF, dan
hash password. Username, peran, sekolah, dan kelas tetap dikelola Admin.

---

## 6. Alur lintas peran

### 6.1 Admin membuat akun Guru BK

```
Admin → Pengguna → Tambah
  → pilih peran Guru BK
  → pilih sekolah
  → isi nama, username, password awal
  → Simpan
  → akun muncul di daftar (status aktif)
  → sampaikan username/password ke Guru BK
```

### 6.2 Admin mencabut akses

```
Admin → Pengguna → cari akun → Nonaktifkan
  → konfirmasi ("Akun tidak bisa login, data tetap tersimpan")
  → status menjadi nonaktif
  → pengguna yang sedang login ditolak pada request berikutnya
```

### 6.3 Siswa membagikan catatan

```
Siswa isi langkah 1–5
  → Konsultasi
  → (opsional) tulis pesan
  → Bagikan
  → simpan/update berbagi_catatan (status baru)
  → Guru BK sekolah yang sama melihat di dashboard
```

### 6.4 Guru BK menindaklanjuti

```
Guru BK → Dashboard → Catatan Siswa → Buka detail
  → status otomatis "dibaca"
  → baca ringkasan
  → tulis tindak lanjut → Simpan
  → status "ditindaklanjuti"
  → siswa melihat status di halaman Konsultasi
```

### 6.5 Siswa pindah sekolah

```
Admin → Pengguna → Ubah siswa → ganti sekolah
  → data karier tetap
  → data berbagi lama tidak lagi terlihat oleh Guru BK lama (sekolah berbeda)
  → Guru BK baru BELUM melihat data sampai siswa membagikan ulang
  → saat siswa membagikan ulang, sekolah pada data berbagi diperbarui
```

Alasan: persetujuan berbagi diberikan untuk sekolah saat itu. Perpindahan sekolah
tidak boleh otomatis membuka data pribadi siswa ke Guru BK baru.

---

## 7. Empty state & pesan

| Kondisi | Tampilan |
|---------|----------|
| Belum ada sekolah | “Belum ada sekolah. Tambahkan sekolah pertama.” + tombol |
| Belum ada pengguna | “Belum ada akun. Tambahkan akun pertama.” |
| Guru belum punya catatan | “Belum ada siswa yang membagikan catatan karier.” |
| Siswa belum isi langkah | “Belum diisi” + tombol menuju langkah |
| Belum ada profesi | “Katalog profesi belum tersedia.” |
| Jurnal kosong | “Belum ada jurnal. Tulis refleksi pertamamu.” |
| Akun nonaktif | “Akun Anda dinonaktifkan. Silakan hubungi Admin sekolah.” |
| Akses salah peran | Halaman 403: “Anda tidak memiliki akses ke halaman ini.” |
| Data tidak ditemukan | Halaman 404 sederhana + tombol kembali |

Jangan gunakan `alert()` untuk pesan sukses. Gunakan flash message di halaman.

---

## 8. Validasi form (ringkas)

| Form | Aturan utama |
|------|--------------|
| Login | username & password wajib |
| Sekolah | nama wajib, jenjang valid, NPSN unik bila diisi |
| Pengguna | role valid, sekolah wajib untuk non-admin, username unik, password ≥ 8 |
| Refleksi | minimal satu pilihan atau catatan |
| Bandingkan | opsi A & B wajib, tidak boleh sama |
| Rencana | tujuan + dua langkah wajib |
| Jurnal | minimal satu dari tiga field |
| Tindak lanjut | catatan wajib, maks. 2000 karakter |
| Ganti password | password lama benar, baru ≥ 8, konfirmasi sama |

Semua validasi diulang di server meskipun HTML sudah memakai `required`.

---

## 9. Cetak / PDF

Halaman yang perlu gaya cetak:

1. Ruang Konsultasi Siswa.
2. Detail Catatan Siswa (Guru BK).
3. Rekap Admin (P2).

Aturan CSS cetak:

- Sembunyikan sidebar, tombol, dan navigasi.
- Tampilkan nama sekolah, nama siswa, tanggal cetak.
- Warna teks hitam, latar putih.
- Jangan memotong tabel di tengah baris.

---

## 10. Navigasi & tampilan responsif

- Desktop: sidebar kiri tetap.
- Mobile: tombol menu di header, sidebar muncul sebagai panel.
- Header: nama pengguna, badge peran, nama sekolah, tombol keluar.
- Breadcrumb sederhana pada halaman detail:
  `Dashboard / Pengguna / Ubah Pengguna`.
- Fokus utama setiap halaman terlihat jelas (judul + aksi utama).
- Gunakan ikon FontAwesome yang sama dengan prototipe agar terasa familiar.

---

## 11. Halaman yang tidak dibuat

- Landing page marketing terpisah (login langsung menjadi halaman awal).
- Chat/konseling online.
- Jadwal konsultasi.
- Pengumuman (sampai fitur benar-benar ada).
- Manajemen konten profesi oleh Admin.
- Pendaftaran mandiri.
- Dashboard orang tua.

Jika Cindy meminta salah satunya, catat di `08` dan evaluasi setelah MVP stabil.
