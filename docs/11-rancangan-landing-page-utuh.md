# Rancangan Landing Page KARVIA — Utuh

**Halaman:** `index.php`  
**Status:** Implementasi saat ini ada di `index.php` pada branch `staging`; revisi mobile navbar dan carousel artikel sedang dikerjakan.
**Cakupan:** halaman publik lengkap dari header sampai footer  
**Acuan layout hero:** gambar referensi dari pengguna

---

## 1. Konteks produk

KARVIA (Career Vision & Action) adalah media digital pendukung bimbingan karier bagi siswa SMA/MA. KARVIA membantu siswa mengenali diri, menjelajahi pilihan profesi dan jalur setelah lulus, membandingkan pilihan, merencanakan langkah, menulis refleksi, dan berdiskusi dengan Guru BK.

KARVIA **bukan tes psikologis**, bukan penentu jurusan atau karier otomatis, dan bukan pengganti Guru BK/konselor. Siswa dapat mendaftar sendiri melalui halaman terpisah; Admin dan Guru BK tetap dibuat oleh Admin. Catatan siswa bersifat personal; Guru BK hanya melihat catatan yang dibagikan siswa dan sesuai cakupan sekolahnya.

Landing page memperkenalkan produk kepada siswa, Guru BK, dan pihak sekolah, lalu mengarahkan pengguna yang sudah memiliki akun untuk masuk.

## 2. Tujuan landing page

- Menjelaskan KARVIA dan manfaatnya dengan bahasa sederhana.
- Memperlihatkan alur eksplorasi karier siswa secara ringkas.
- Menjelaskan peran Guru BK dan batas privasi catatan.
- Mengarahkan pengguna terdaftar ke `login.php`.
- Menampilkan informasi yang benar tanpa statistik, testimoni, atau klaim yang belum diverifikasi.

## 3. Pengguna sasaran

- **Siswa SMA/MA:** ingin memahami proses mengenali diri dan merencanakan karier.
- **Guru BK:** ingin mengetahui bagaimana KARVIA mendukung diskusi bimbingan.
- **Pihak sekolah:** ingin memahami tujuan serta batas penggunaan aplikasi.

## 4. Prinsip desain dan batasan

- UI clean, teratur, mudah dipindai, dan nyaman di desktop maupun ponsel.
- Pertahankan warna dan identitas visual KARVIA yang sudah digunakan.
- Pertahankan font Inter; jangan mengganti atau menambah font.
- Gambar referensi menjadi acuan komposisi hero, bukan warna atau tipografinya.
- Hero memakai konten terpusat dengan hierarki judul, deskripsi, dan tombol yang jelas.
- Section lain memakai komponen sederhana yang konsisten, tidak ramai dekorasi.
- Bahasa antarmuka Indonesia.
- Jangan menambah library atau dependensi baru.
- Jangan mengubah prototipe `karvia_career_vision_action.html`.
- Pengguna yang sudah login tetap diarahkan ke dashboard sesuai peran oleh alur `index.php`.

## 5. Susunan section saat ini

Urutan yang sudah diterapkan di `index.php`:

1. **Header sticky:** identitas KARVIA, navigasi anchor Tentang/Tahapan/Pendampingan/Privasi, tombol Masuk, dan garis progres scroll.
2. **Hero:** label Langkah Karierku, judul dan deskripsi terpusat, tombol Masuk dan Kenali Tahapannya, keterangan batasan, serta mockup UI KARVIA.
3. **Kenapa KARVIA:** pengantar eksplorasi karier dan tiga gagasan: kenali diri, jelajahi pilihan, ambil langkah kecil.
4. **Privasi dan batasan produk:** KARVIA bukan tes psikologis/penentu otomatis; catatan hanya terlihat Guru BK setelah dibagikan siswa.
5. **Tahapan eksplorasi:** lima langkah dalam kartu bernomor.
6. **Dukungan Guru BK:** penjelasan diskusi dan kontrol siswa atas pembagian catatan.
7. **FAQ:** empat pertanyaan tentang KARVIA, rekomendasi karier, privasi catatan, dan akun.
8. **Ajakan masuk:** tombol menuju `login.php`.
9. **Footer:** identitas KARVIA, UIN Raden Intan Lampung, tautan section dan FAQ.

Urutan header-to-footer di atas adalah implementasi saat ini. Perubahan visual atau susunan berikutnya belum dikerjakan dan akan ditentukan pengguna kemudian.

## 6. Isi acuan tiap section

Bagian berikut menyimpan isi dan batasan produk yang menjadi acuan. Teks final yang tampil di halaman dapat berbeda; implementasi aktual dirangkum pada bagian 10.

### 6.1 Header / navigasi

**Isi:**
- Ikon/identitas KARVIA dan teks `KARVIA`.
- Subjudul `Career Vision & Action` bila ruang cukup.
- Tautan navigasi menuju section halaman: `Tentang`, `Tahapan`, `Privasi`.
- Tombol **Masuk** menuju `login.php`.

**Perilaku:**
- Tautan navigasi memakai anchor section yang benar-benar tersedia.
- Mobile tertutup: tampilkan logo KARVIA dan tombol menu bulat saja.
- Mobile terbuka: overlay layar penuh hijau-krem KARVIA, tautan besar vertikal, pemisah tipis, tombol tutup, CTA Masuk dan Daftar Siswa.
- Gunakan `aria-expanded`, label tombol, fokus keyboard, Escape untuk menutup, dan tutup menu setelah anchor dipilih.
- Desktop tetap memakai navigasi horizontal dan tombol Masuk.
- Tautan daftar siswa menuju `daftar.php`; role lain tidak boleh mendaftar mandiri.

### 6.2 Hero

**Layout:** konten terpusat, lebar teks dibatasi, jarak lega, responsif.

**Teks usulan:**
- Label: **KARVIA · Langkah Karierku**
- Judul: **Kenali dirimu. Temukan pilihanmu. Rencanakan langkah berikutnya.**
- Deskripsi: **KARVIA membantu siswa SMA/MA mengenali potensi diri, menjelajahi pilihan karier, dan menyusun langkah masa depan—dengan ruang untuk berdiskusi bersama Guru BK.**
- Tombol utama: **Masuk ke KARVIA** → `login.php`
- Tombol kedua: **Lihat Tahapan** → anchor ke section tahapan
- Keterangan pendukung: **Perjalanan karier dimulai dengan mengenali diri sendiri.**

### 6.3 Batasan dan privasi

**Judul usulan:** **Eksplorasi, bukan penilaian**

**Isi usulan:**
> KARVIA adalah media pendukung bimbingan karier. KARVIA bukan tes psikologis, bukan penentu pilihan otomatis, dan bukan pengganti Guru BK atau konselor.

> Catatan eksplorasi bersifat personal. Guru BK hanya dapat melihat catatan yang dibagikan siswa sesuai aturan aplikasi.

**Tampilan:** section informatif yang mudah dikenali, kontras memadai, dan memakai warna KARVIA. Hindari bahasa yang menjanjikan kerahasiaan absolut di luar kebijakan aplikasi.

### 6.4 Mengapa eksplorasi karier?

**Judul usulan:** **Masa depan tidak harus diputuskan sekaligus**

**Isi usulan:**
> Mengenali minat dan kekuatan, mencari tahu pilihan, lalu mencoba satu langkah kecil dapat membantu siswa menyiapkan bahan untuk berdiskusi dan mengambil keputusan dengan lebih terarah.

Hindari klaim bahwa KARVIA menjamin pekerjaan, keberhasilan, atau pilihan karier tertentu.

### 6.5 Tahapan KARVIA

**Judul usulan:** **Langkah kecil untuk mengenal arahmu**

Tampilkan lima langkah sebagai kartu bernomor atau urutan visual yang konsisten:

1. **Kenali Diriku** — refleksikan minat, kekuatan, dan hal yang penting bagimu.
2. **Jelajah Profesi** — kenali beragam profesi, aktivitas kerja, dan jalur pendidikan.
3. **Bandingkan Pilihan** — pertimbangkan pilihan berdasarkan kriteria yang bermakna bagimu.
4. **Rencana Langkahku** — susun tujuan sementara dan langkah kecil yang dapat dilakukan.
5. **Jurnal & Konsultasi** — refleksikan prosesmu dan bagikan catatan kepada Guru BK bila ingin berdiskusi.

**Catatan akurasi:** bagian ini menjelaskan rancangan alur KARVIA. Jangan memberi kesan seluruh tahap sudah aktif sebelum implementasinya tersedia. Tautan ke halaman internal hanya ditampilkan jika akses dan fitur terkait benar-benar siap.

### 6.6 Dukungan Guru BK

**Judul usulan:** **Ruang untuk berdiskusi bersama Guru BK**

**Isi usulan:**
> Siswa dapat membagikan ringkasan eksplorasinya ketika ingin berdiskusi. Guru BK membantu siswa meninjau pilihan dan langkah berikutnya; keputusan tetap berada pada siswa dengan dukungan orang dewasa yang dipercaya.

Jelaskan bahwa Guru BK tidak otomatis melihat seluruh catatan pribadi sebelum siswa membagikannya. Hindari menyiratkan adanya chat, jadwal konsultasi, atau notifikasi bila fitur tersebut belum tersedia.

### 6.7 Ajakan masuk

**Judul usulan:** **Siap mulai mengenali pilihanmu?**

**Deskripsi:** **Masuk ke akun KARVIA untuk melanjutkan perjalanan eksplorasi kariermu.**

**Tombol:** **Masuk ke KARVIA** → `login.php`

Jangan tambahkan tombol pendaftaran akun.

### 6.8 Footer

**Isi minimum:**
- `KARVIA — Career Vision & Action`.
- Atribusi `UIN Raden Intan Lampung`, mengikuti identitas proyek yang sudah digunakan.
- Tautan kembali ke bagian atas atau anchor section hanya bila berguna.

Jangan menambahkan alamat kontak, akun media sosial, tahun hak cipta, atau tautan kebijakan yang belum disediakan/ditetapkan.

## 7. Interaksi dan perilaku aplikasi

- Tombol Masuk pada header, hero, dan ajakan akhir membuka `login.php`.
- Tombol Lihat Tahapan menuju section tahapan di halaman yang sama.
- Navigasi anchor tidak mengubah autentikasi atau status sesi.
- Bila pengguna sudah login, server tetap mengarahkan pengguna ke dashboard berdasarkan peran.
- Tidak ada formulir pendaftaran publik.
- Tidak ada angka statistik atau konten testimoni hardcoded.

## 8. Batas implementasi

- Implementasi adaptasi desain template dilakukan pada `index.php`; tidak memindahkan React/TSX ke runtime PHP.
- Hero memakai mockup UI KARVIA buatan HTML/CSS; seluruh isi mockup adalah ilustrasi, bukan data akun/database.
- Navigasi sticky, garis progres scroll, animasi CSS ringan, FAQ native `<details>`, section manfaat, tahapan, privasi, dukungan BK, CTA, dan footer diimplementasikan pada halaman.
- Fokus pembaruan adalah menyusun landing lengkap dari header sampai footer dengan konten di dokumen ini.
- Pertahankan alur login yang ada.
- Jangan mengubah halaman internal, skema database, atau hak akses.
- Jangan menampilkan tautan fitur internal yang belum siap.

## 9. Verifikasi implementasi

Pada commit `23bdd0c` di branch `feat/landing-teal`:

- `php -l index.php` lulus tanpa kesalahan sintaks.
- Uji HTTP lokal mengembalikan `200`.
- Header, hero, section manfaat, lima tahapan, privasi, FAQ, CTA, dan footer ditemukan pada hasil HTML.
- Seluruh tautan anchor memiliki tujuan yang tersedia; tautan login menuju `login.php`.
- Pemeriksaan memastikan teks template treasury/FDIC dan warning PHP tidak muncul.
- Flow redirect pengguna login tetap ada sebelum HTML landing dirender.

## 10. Kriteria penerimaan

- Semua section yang direncanakan ada dalam urutan yang jelas, dari header sampai footer.
- Hero mengikuti komposisi terpusat, tetap clean, serta mempertahankan warna KARVIA dan font Inter.
- Teks menjelaskan produk tanpa klaim yang tidak didukung.
- Batas privasi siswa dan peran Guru BK dinyatakan secara akurat.
- Tombol Masuk menuju `login.php`; anchor navigasi menuju section yang benar.
- Landing nyaman digunakan pada desktop dan ponsel, dengan heading semantik, label tautan jelas, dan fokus keyboard terlihat.
- Pengguna yang sudah login tetap diarahkan ke dashboard sesuai peran.
- Tidak ada pendaftaran palsu, tombol mati, atau tautan ke halaman fitur yang belum tersedia.

## 11. Revisi mobile disetujui

Navbar mobile mengikuti referensi pengguna: logo dan tombol menu bulat saat tertutup; overlay layar penuh saat dibuka dengan tautan vertikal besar, tombol tutup, serta CTA. Gunakan palet KARVIA hijau-krem, bukan warna marun pada referensi.

Artikel mobile berubah menjadi carousel horizontal clean/editorial dengan scroll-snap, cuplikan kartu berikutnya, metadata ringkas, kontrol/indikator yang aksesibel, tanpa autoplay. Desktop tetap memakai grid artikel. Detail dan kriteria penerimaan tercatat di `docs/10-rancangan-landing-page.md`.
