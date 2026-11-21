# 10 — Rancangan Landing Page KARVIA

**Halaman:** `index.php`  
**Status:** revisi mobile navbar dan artikel disetujui; implementasi berjalan di `staging`
**Acuan visual:** screenshot menu layar penuh dan carousel editorial clean dari pengguna
**Batas rancangan:** fokus mobile; pertahankan identitas KARVIA, font Inter, konten dan perilaku desktop yang ada

---

## 1. Tujuan halaman

Landing page memperkenalkan KARVIA kepada siswa, Guru BK, dan pihak sekolah. Pengunjung harus cepat memahami bahwa KARVIA mendukung eksplorasi karier siswa dan menyediakan jalan masuk bagi pengguna yang sudah memiliki akun.

Landing page bukan halaman pendaftaran. Siswa dapat mendaftar melalui `daftar.php`; Admin dan Guru BK tetap dibuat oleh Admin. Pengunjung masuk melalui halaman login.

## 2. Konteks produk

KARVIA (Career Vision & Action) adalah media pendukung bimbingan karier bagi siswa SMA/MA. KARVIA membantu siswa mengenali potensi diri, mengeksplorasi profesi dan jalur setelah lulus, membandingkan pilihan, menyusun rencana, serta melakukan refleksi.

KARVIA bukan tes psikologis, bukan penentu jurusan atau karier otomatis, dan bukan pengganti Guru BK/konselor. Catatan siswa bersifat personal. Guru BK hanya dapat melihat catatan yang dibagikan siswa sesuai aturan akses aplikasi.

## 3. Pengguna sasaran

- **Siswa:** memahami langkah eksplorasi karier dan masuk ke akun.
- **Guru BK:** memahami bahwa aplikasi mendukung proses bimbingan dan diskusi.
- **Pihak sekolah:** memahami tujuan aplikasi tanpa menerima klaim statistik atau fitur yang belum tersedia.

## 4. Susunan halaman

1. Header dan navigasi sederhana.
2. Hero utama dengan layout terpusat, clean, mengikuti komposisi gambar referensi.
3. Penjelasan batasan produk.
4. Ringkasan tahapan eksplorasi karier yang sudah ada.
5. Footer.

Hanya hero yang mendapat perubahan layout utama. Section lain tetap mengikuti isi dan gaya yang sudah ada, kecuali penyesuaian kecil diperlukan agar transisi halaman tetap rapi.

## 5. Rancangan isi dan UI

### 5.1 Header

- Identitas: logo/ikon KARVIA dan teks `KARVIA`.
- Subjudul merek: `Career Vision & Action` bila ruang memungkinkan.
- Navigasi ringkas, tidak menambah menu yang belum memiliki tujuan.
- Tombol **Masuk** menuju `login.php`.
- Tampilan header tetap bersih dan responsif.

### 5.2 Hero

**Layout:**

- Konten berada di tengah secara horizontal.
- Urutan konten: label kecil, judul, deskripsi, tombol, keterangan pendukung.
- Lebar teks dibatasi agar nyaman dibaca.
- Ruang antar-elemen cukup; dekorasi tidak mengganggu pesan.
- Pada layar kecil, konten tetap terpusat dan tombol dapat tersusun vertikal.

**Teks yang diusulkan:**

- Label: **KARVIA · Langkah Karierku**
- Judul: **Kenali dirimu. Temukan pilihanmu. Rencanakan langkah berikutnya.**
- Deskripsi: **KARVIA membantu siswa SMA/MA mengenali potensi diri, menjelajahi pilihan karier, dan menyusun langkah masa depan—dengan ruang untuk berdiskusi bersama Guru BK.**
- Tombol utama: **Masuk ke KARVIA** → `login.php`
- Tombol kedua: **Lihat Tahapan** → menuju bagian tahapan eksplorasi pada halaman yang sama.
- Keterangan kecil: **Perjalanan karier dimulai dengan mengenali diri sendiri.**

Tombol kedua hanya navigasi ke konten yang memang tersedia di landing page; bukan pendaftaran atau klaim fitur baru.

### 5.3 Batasan produk

Pertahankan pesan inti dengan bahasa yang mudah dipahami:

> KARVIA adalah media pendukung bimbingan karier, bukan tes psikologis, bukan penentu pilihan otomatis, dan bukan pengganti Guru BK atau konselor.

Tambahkan penjelasan privasi singkat:

> Catatan eksplorasi bersifat personal. Guru BK hanya melihat catatan yang dibagikan siswa.

Gunakan tampilan informasi yang mudah dikenali dan tetap selaras dengan warna KARVIA.

### 5.4 Tahapan eksplorasi

Tampilkan tahapan dalam kartu atau susunan visual yang sudah digunakan halaman saat ini. Gunakan deskripsi singkat:

1. **Kenali Diriku** — refleksikan minat, kekuatan, dan hal yang penting bagimu.
2. **Jelajah Profesi** — kenali beragam pekerjaan dan jalur pendidikan.
3. **Bandingkan Pilihan** — pertimbangkan pilihan dengan kriteria yang bermakna bagimu.
4. **Rencana Langkahku** — tentukan tujuan dan langkah kecil yang bisa dimulai.
5. **Jurnal & Konsultasi** — refleksikan perjalanan dan bagikan catatan kepada Guru BK bila ingin berdiskusi.

Deskripsi tidak menyatakan bahwa seluruh fitur sudah tersedia bila implementasinya masih bertahap. Bagian ini menjelaskan rancangan alur produk.

### 5.5 Footer

- Identitas `KARVIA — Career Vision & Action`.
- Atribusi institusi `UIN Raden Intan Lampung` sesuai isi yang sudah ada.
- Tidak menambah tautan yang belum disiapkan.

## 6. Aturan visual

- Gunakan komposisi hero pada gambar referensi: konten terpusat dan hierarki tipografi jelas.
- **Pertahankan palet warna KARVIA yang sekarang.** Jangan menyalin warna gelap, warna teks, atau warna tombol dari gambar referensi.
- **Pertahankan font yang sekarang (Inter).** Jangan mengimpor atau mengganti font.
- Pertahankan identitas warna hijau KARVIA dan gaya komponen yang sudah ada.
- Gaya keseluruhan clean: kurangi elemen dekoratif yang tidak membantu pemahaman.
- Pastikan kontras teks dan fokus keyboard tetap terbaca.
- Tidak menambahkan library atau dependensi baru.

## 7. Interaksi dan perilaku

- Tombol **Masuk** dan **Masuk ke KARVIA** membuka `login.php`.
- Tombol **Lihat Tahapan** berpindah ke section tahapan pada halaman yang sama.
- Pengguna yang sudah login tetap diarahkan ke dashboard sesuai peran melalui alur `index.php` saat ini.
- Tautan daftar siswa boleh tampil di halaman login; registrasi akun tetap khusus siswa.
- Tidak menampilkan statistik, testimoni, atau klaim yang tidak memiliki sumber nyata.

## 8. Batas implementasi

- Fokus perubahan berikutnya pada `index.php` dan kebutuhan anchor section di file yang sama.
- Jangan mengubah autentikasi, hak akses, database, atau halaman internal.
- Jangan mengubah, memindahkan, atau menghapus `karvia_career_vision_action.html`.
- Jangan menganggap rancangan halaman sebagai bukti bahwa fitur siswa atau Guru BK telah selesai.

## 9. Revisi mobile navbar dan artikel

### Navbar
- Pada mobile, header tertutup hanya menampilkan identitas KARVIA dan tombol menu bulat, supaya baris navigasi tidak padat.
- Tombol membuka overlay menu layar penuh bernuansa hijau-krem KARVIA; tautan besar tersusun vertikal dengan pemisah halus.
- Tombol menu berubah menjadi tutup (`×`), memakai `aria-expanded`, label aksesibel, fokus keyboard yang jelas, dan target sentuh nyaman.
- Menu memuat anchor Tentang, Tahapan, Artikel, Pendampingan, Privasi, FAQ, serta CTA Masuk dan Daftar Siswa. Daftar menuju `daftar.php`; Admin/Guru BK tetap tidak memiliki registrasi mandiri.
- Pilih tautan menutup menu. Escape menutup menu. Jangan menambah dependensi.
- Desktop mempertahankan navigasi horizontal saat ini.

### Carousel artikel
- Semua ukuran layar menyajikan hingga 6 artikel sebagai carousel horizontal CSS scroll-snap, bukan grid/kolom panjang.
- Kartu clean/editorial: gambar konsisten, metadata ringkas, judul mudah dipindai, ringkasan pendek, CTA jelas; palet hijau-krem dan font Inter tetap.
- Kartu berikutnya tampak sebagian sebagai petunjuk geser; kontrol/indikator posisi sederhana dan aksesibel melengkapi gestur sentuh atau mouse.
- Tidak autoplay. Gerak hormati `prefers-reduced-motion`.
- Desktop memakai kartu lebih lebar dan kontrol carousel yang sama; tidak kembali ke grid.

### Kriteria penerimaan
- Pengunjung memahami tujuan KARVIA dan batasannya.
- Navbar mobile tertutup ringkas; overlay berfungsi dengan sentuh dan keyboard, tertutup setelah anchor dipilih atau Escape.
- Carousel pada mobile dan desktop menampung sampai 6 artikel tanpa menumpuk semua kartu; semua kartu dapat dicapai dengan geser dan kontrol aksesibel.
- Gambar, judul, ringkasan, dan tautan tiap artikel tetap benar; artikel kosong tetap memakai empty state.
- Desktop juga memakai carousel, dengan ukuran kartu responsif dan navigasi sebelumnya/berikutnya.
- Warna KARVIA dan font Inter tidak berubah; tidak ada dependensi baru.
- Login, pendaftaran siswa, anchor, dan redirect pengguna login tetap benar.
- Uji viewport mobile dan desktop, keyboard, serta PHP lint sebelum selesai.
