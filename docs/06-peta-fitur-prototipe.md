# 06 — Peta Fitur Prototipe HTML

File acuan: `karvia_career_vision_action.html` (1.044 baris, ±79 KB).
File ini **hanya referensi** dan tidak boleh diubah.

---

## 1. Fakta teknis prototipe

| Aspek | Kondisi sekarang |
|-------|------------------|
| Bentuk | Satu file HTML berisi struktur, gaya, dan logika |
| CSS | Tailwind CSS via CDN |
| Ikon/font | FontAwesome 6 via CDN, Google Fonts Inter |
| JavaScript | Inline di akhir file |
| Backend | Tidak ada |
| Database | Tidak ada |
| Penyimpanan | Variabel `currentUser` dan `studentDataStore` di memori |
| Peran | Hanya `siswa` dan `guru` |
| Login | Nama + password apa pun yang tidak kosong |
| Data bertahan setelah refresh? | Tidak |
| Data terhubung siswa   guru? | Tidak; tombol hanya memunculkan modal |
| Angka dashboard guru | Hardcoded |
| Artefak | Baris terakhir berisi ``` dan `^^^eof` (sisa tempelan Markdown) |

Kesimpulan: prototipe cocok sebagai **acuan visual dan alur**, tetapi bukan
fondasi teknis yang bisa dipakai langsung.

---

## 2. Pemetaan tab siswa

| Tab prototipe | ID konten | Route baru | Data baru |
|---------------|-----------|------------|-----------|
| Beranda & Pengantar | `content-beranda` | `siswa_beranda` | Ringkasan dari beberapa tabel |
| 1. Kenali Diriku | `content-kenaliDiri` | `siswa_kenali_diri` | `refleksi_diri` |
| 2. Jelajah Profesi | `content-jelajahProfesi` | `siswa_profesi` | `profesi`, `profesi_disimpan` |
| Jalur Setelah Lulus | `content-jalurLulus` | `siswa_jalur` | Statis (tanpa tabel) |
| 3. Bandingkan Pilihan | `content-bandingkan` | `siswa_bandingkan` | `pilihan_banding` |
| 4. Rencana Langkahku | `content-rencana` | `siswa_rencana` | `rencana_karier` |
| 5. Jurnal Refleksi | `content-refleksi` | `siswa_jurnal` | `jurnal_refleksi` |
| Ruang Konsultasi BK | `content-konsultasi` | `siswa_konsultasi` | Ringkasan + `berbagi_catatan` |
| Tips & Edukasi Karier | `content-tips` | `siswa_tips` | Statis (tanpa tabel) |

Catatan urutan: di prototipe, “Jalur Setelah Lulus” berada di antara Jelajah
Profesi dan Bandingkan. Boleh dipertahankan di sidebar agar siswa tidak bingung.

---

## 3. Pemetaan fungsi JavaScript

| Fungsi prototipe | Perilaku sekarang | Pengganti di sistem baru |
|------------------|-------------------|--------------------------|
| `currentUser` | Objek di memori | `$_SESSION['pengguna']` + tabel `pengguna` |
| `studentDataStore` | Objek di memori | Tabel `refleksi_diri`, `pilihan_banding`, `rencana_karier`, `jurnal_refleksi` |
| `setLoginRole()` | Memilih peran dan mengisi nama contoh | **Dihapus**; peran dari akun |
| `handleLogin()` | Cek password tidak kosong | `auth_login()` + `password_verify` + cek status |
| `logout()` | Sembunyikan dashboard | Hapus session + redirect |
| `switchTab()` | Sembunyikan/tampilkan div | Route server per halaman |
| `filterProfesi()` | Filter kartu via JS, memakai `event` global | Filter server via `?kategori=...` atau JS yang aman |
| `simpanProfesi()` | Hanya modal | Insert/hapus `profesi_disimpan` |
| `tambahKeBandingkan()` | Isi form A lalu pindah tab | Link `siswa_bandingkan?profesi=ID`, prefill dari DB |
| `saveKenaliDiri()` | Simpan 1 string minat | Upsert `refleksi_diri` (mapel[], kekuatan[], catatan) |
| `simpanPerbandingan()` | Simpan string “A vs B” | Upsert `pilihan_banding` |
| `simpanRencanaLangkah()` | Simpan tujuan + langkah 1 | Upsert `rencana_karier` (2 langkah) |
| `simpanJurnal()` | Simpan 1 field | Insert `jurnal_refleksi` (3 field) |
| `updateRingkasanKonsultasi()` | Ambil dari objek memori | Query ringkasan dari database |
| `kirimKeGuruBK()` | Hanya modal sukses | Insert/update `berbagi_catatan` |
| `lihatDetailSiswa()` | Modal dengan argumen statis | Halaman `guru_catatan_detail` + query |
| `bukaModalPengumuman()` | Modal sukses palsu | **Disembunyikan** sampai fitur benar-benar ada |
| `refreshDataGuru()` | Modal “berhasil disinkronkan” | Hapus; data selalu dari database saat halaman dimuat |
| `showModal()` | Notifikasi serba guna | Flash message + modal konfirmasi aksi berisiko |
| `closeModal()` | Menutup modal | Tetap dipakai untuk modal konfirmasi |

---

## 4. Data hardcoded yang wajib diganti

### 4.1 Dashboard Guru BK

| Lokasi | Nilai dummy | Pengganti |
|--------|-------------|-----------|
| Kartu “Total Siswa Aktif” | `32 Siswa` | Hitung siswa di sekolah Guru BK |
| Kartu “Catatan Masuk” | `14 Siswa` | Hitung `berbagi_catatan` status baru |
| Kartu “Jadwal Konsultasi” | `6 Permintaan` | **Hapus** (fitur tidak ada) |
| Kartu “Status Sistem” | `Aktif 100%` | **Hapus** atau ganti metrik nyata |
| Tabel | Ahmad Fauzan, Siti Rahma | Query dari `berbagi_catatan` + `pengguna` |
| Detail | Argumen modal statis | Data dari database |

### 4.2 Login

| Lokasi | Nilai dummy | Pengganti |
|--------|-------------|-----------|
| Pilihan peran | siswa/guru | Dihapus |
| Nama default | “Ahmad Fauzan” / “Mega Aria Monica” | Placeholder saja |
| Petunjuk password | “siswa123 atau bebas” | Dihapus |
| Validasi | password tidak kosong | Akun + hash + status |

### 4.3 Form siswa

| Lokasi | Nilai dummy | Pengganti |
|--------|-------------|-----------|
| Bandingkan A | “Dokter / Tenaga Medis” + catatan | Kosong/placeholder, atau prefill dari profesi yang dipilih |
| Bandingkan B | “Software Engineer” + catatan | Kosong/placeholder |
| Kriteria | Semua checkbox tercentang | Default tidak dicentang, siswa memilih |
| Rencana tujuan | “Mendaftar kuliah…” | Kosong/placeholder |
| Langkah 1 & 2 | Teks contoh | Kosong/placeholder |
| Mapel/kekuatan | Checkbox tidak tersimpan | Simpan array ke DB |
| Jurnal | Hanya field `jurnalBaru` | Tiga field: baru, hambatan, dukungan |

### 4.4 Konten yang boleh dipertahankan

- Teks pengantar KARVIA dan disclaimer “bukan tes psikologis”.
- Deskripsi 4 profesi (Dokter, Software Engineer, Guru BK, Advokat) diisi ke tabel `profesi` pada bagian B `database.sql`.
- Kategori filter profesi.
- Konten “Jalur Setelah Lulus SMA/MA”.
- Konten “Tips & Edukasi Karier”.
- Kredit “UIN Raden Intan Lampung (Chindy Theresia - 2411080092)”.
- Warna, font, ikon, dan gaya kartu.

---

## 5. Fitur yang dipertahankan, diubah, atau dibuang

| Fitur | Keputusan | Alasan |
|-------|-----------|--------|
| Warna hijau KARVIA, Inter, FontAwesome | **Pertahankan** | Identitas visual sudah baik |
| Sidebar & kartu langkah | **Pertahankan** | Familiar untuk siswa |
| Lima langkah eksplorasi | **Pertahankan** | Inti produk |
| Filter profesi | **Pertahankan** | Berguna, mudah dipindah ke server |
| Bookmark profesi | **Perbaiki** | Harus benar-benar tersimpan |
| Bandingkan & rencana | **Perbaiki** | Simpan ke database |
| Jurnal | **Perbaiki** | Simpan banyak entri, bukan satu string |
| Cetak ringkasan | **Pertahankan** | Berguna untuk sesi konseling |
| Login pilih peran | **Buang** | Peran harus dari akun |
| Password bebas | **Buang** | Tidak aman |
| Angka dashboard guru | **Buang** | Menyesatkan |
| Baris dummy Ahmad/Siti | **Buang** | Bukan data nyata |
| Modal “berhasil dikirim” | **Buang** | Tidak benar-benar mengirim |
| Modal pengumuman | **Tunda** | Fitur belum ada |
| Tombol refresh data | **Buang** | Data selalu segar dari DB |
| Status sistem 100% | **Buang** | Tidak bermakna |
| Data di memori | **Buang** | Harus persisten |

---

## 6. Catatan kualitas kode prototipe

Hal-hal ini **jangan ditiru** saat menulis kode baru:

1. **Password bebas**: `if(!pass)` hanya memeriksa kosong.
2. **Peran dari tombol**: pengguna bisa memilih jadi guru tanpa akun.
3. **`event` global** di `filterProfesi()`: rapuh dan tidak konsisten antar-browser.
4. **Data di memori**: hilang saat refresh.
5. **Modal sukses palsu**: tombol tidak melakukan aksi nyata.
6. **Statistik hardcoded**: angka tidak berhubungan dengan data.
7. **innerHTML** untuk ikon: aman karena statis, tetapi jangan dipakai untuk data user.
8. **Artefak Markdown** di akhir file: baris ``` dan `^^^eof` harus diabaikan.
9. **Tidak ada validasi server**: memang karena belum ada server.
10. **Tidak ada pemisahan file**: satu file 1.044 baris sulit dirawat.

Yang **layak ditiru**:

- Struktur visual dan hierarki informasi.
- Konsistensi warna dan ikon.
- Alur lima langkah yang mudah diikuti.
- Bahasa yang ramah siswa.
- Pemisahan konten statis (jalur, tips) dari data pribadi.

---

## 7. Referensi tampilan

Saat membangun halaman baru, cocokkan dengan prototipe untuk:

| Elemen | Lokasi di prototipe |
|--------|---------------------|
| Login | baris 55–128 |
| Layout utama & sidebar | baris 130–207 |
| Beranda siswa | baris 208–279 |
| Kenali Diriku | baris 281–354 |
| Jelajah Profesi | baris 356–458 |
| Jalur Setelah Lulus | baris 460–518 |
| Bandingkan | baris 519–564 |
| Rencana | baris 566–605 |
| Jurnal | baris 607–637 |
| Konsultasi | baris 639–666 |
| Tips | baris 668–697 |
| Dashboard Guru | baris 700–794 |
| JavaScript | baris 802–1039 |

Gunakan referensi ini hanya untuk **melihat**, bukan menyalin logika.

---

## 8. Ringkasan satu kalimat

Prototipe memberi kita **desain dan alur yang sudah teruji secara tampilan**;
tugas kita adalah mengganti seluruh “pura-pura”-nya (login, data, angka, berbagi)
dengan **akun, database, dan otorisasi yang nyata**, lalu menambahkan **Admin dan
multi-sekolah** yang belum ada sama sekali.
