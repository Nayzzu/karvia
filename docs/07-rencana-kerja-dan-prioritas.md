# 07 — Rencana Kerja & Prioritas

Target indikatif dari Cindy: **Rabu, 25 November 2026**.
Hari ini: Kamis, 19 November 2026. Waktu efektif ±6 hari.

Target ini **ketat** untuk membangun ulang sistem multi-peran + multi-sekolah.
Karena itu prioritas harus disiplin: selesaikan inti lebih dulu, tambah kemewahan
hanya jika waktu tersisa.

> **Dikerjakan 2 orang?** Rencana harian di dokumen ini adalah jalur satu orang.
> Untuk pembagian tugas dan timeline paralel, lihat
> [`09-pembagian-tugas-2-orang.md`](09-pembagian-tugas-2-orang.md).

---

## 1. Prinsip prioritas

| Prioritas | Arti | Contoh |
|-----------|------|--------|
| **P0** | Wajib ada agar sistem bisa dipakai & didemo | Login nyata, admin, sekolah, akun, akses, rekap, simpan data, bagikan ke guru |
| **P1** | Penting, dikerjakan jika P0 selesai | Ganti password, pencarian/pagination, log aktivitas, cetak, filter status |
| **P2** | Nice to have / setelah demo | Ekspor CSV, pengumuman, CRUD profesi, import Excel, jadwal konsultasi |

Aturan: **jangan mengerjakan P1/P2 saat P0 belum beres.**

---

## 2. Rencana harian

### Kamis, 19 November — Fondasi dokumen & kerangka ✅

- [x] Analisis prototipe.
- [x] Struktur folder PHP native.
- [x] `index.php` placeholder.
- [x] Dokumentasi `docs/00`–`09`.
- [ ] Konfirmasi keputusan penting ke Cindy (lihat `08`).

Keluaran: kerangka proyek + pemahaman bersama.

---

### Jumat, 20 November — Database & login

- [ ] Buat database `karvia_db`.
- [x] Struktur `database/database.sql`; data awal dan tambahan dipisah ke file dummy SQL.
- [ ] Import & uji relasi.
- [ ] `config/koneksi.php`.
- [ ] `includes/function.php` (koneksi, escape, flash, CSRF, auth, validasi, format).
- [ ] `includes/header.php` dan `includes/footer.php`.
- [ ] `index.php` landing, `login.php`, `logout.php`.
- [ ] Cek login & peran di `includes/header.php`.

**Milestone:** tiga peran bisa login dan diarahkan ke halaman kosong masing-masing;
akun nonaktif ditolak. Data tetap ada setelah restart.

---

### Sabtu, 21 November — Admin (bagian 1)

- [ ] Header (`includes/header.php`) + sidebar Admin.
- [ ] Dashboard: 4 kartu statistik nyata.
- [ ] Tabel rekap per sekolah.
- [ ] Daftar sekolah + pencarian.
- [ ] Form tambah/ubah sekolah.
- [ ] Detail sekolah (jumlah siswa/guru).

**Milestone historis:** Cindy melihat 2 sekolah awal; fixture saat ini bertambah menjadi 5 sekolah setelah impor SQL tambahan.

---

### Minggu, 22 November — Admin (bagian 2) + Guru BK

- [ ] Daftar pengguna + filter (peran, sekolah, status, kata kunci).
- [ ] Form tambah/ubah pengguna.
- [ ] Reset password.
- [ ] Aktif/nonaktifkan pengguna (dengan konfirmasi).
- [ ] Rekap admin sederhana.
- [ ] Sidebar + dashboard Guru BK nyata.
- [ ] Daftar catatan yang dibagikan.
- [ ] Detail catatan + tandai dibaca + tindak lanjut.

**Milestone:** siklus admin mengelola akun dan guru melihat catatan berjalan.

---

### Senin, 23 November — Siswa (inti)

- [ ] Sidebar + beranda siswa (progress langkah).
- [ ] Kenali Diriku (simpan/ubah).
- [ ] Jelajah Profesi + filter + bookmark.
- [ ] Jalur Setelah Lulus (statis).
- [ ] Bandingkan Pilihan.
- [ ] Rencana Langkahku.

**Milestone:** seluruh data langkah 1–4 tersimpan dan muncul kembali setelah login ulang.

---

### Selasa, 24 November — Siswa (lanjutan) + integrasi

- [ ] Jurnal Refleksi (riwayat).
- [ ] Ruang Konsultasi + ringkasan dari database.
- [ ] Bagikan ke Guru BK (benar-benar tersimpan).
- [ ] Status berbagi di sisi siswa.
- [ ] Profil & ganti password.
- [ ] Tips & edukasi.
- [ ] CSS cetak.
- [ ] Uji alur lintas peran (siswa → guru).
- [ ] Perbaikan bug P0.

**Milestone:** skenario demo utama berjalan dari awal sampai akhir.

---

### Rabu, 25 November — QA, demo, serah terima

- [ ] Uji checklist `05` bagian 7.
- [ ] Uji keamanan dasar (lintas sekolah, XSS, CSRF, akun nonaktif).
- [ ] Backup database ke `database/backup_YYYY-MM-DD.sql`.
- [ ] Bersihkan data uji yang tidak perlu.
- [ ] Siapkan akun demo.
- [ ] Demo ke Cindy (±15 menit).
- [ ] Catat masukan → prioritas setelah demo.
- [ ] Serahkan panduan menjalankan & penjelasan alur.

**Milestone:** Cindy dapat login sebagai Admin, melihat rekap, dan memahami alurnya.

---

## 3. Pembagian pekerjaan

Project ini dikerjakan **2 orang**. Pembagian detail, kepemilikan file, dan
timeline paralel ada di [`09-pembagian-tugas-2-orang.md`](09-pembagian-tugas-2-orang.md).

Ringkasnya:

| Orang | Fokus | Ketergantungan |
|-------|-------|----------------|
| **A** | Database SQL, config, auth, helper, includes inti, Admin, keamanan, rilis | Tidak ada |
| **B** | Siswa, Guru BK, tampilan, cetak, konten & data dummy | Fondasi dari A |

Rencana harian di atas adalah **jalur satu orang**. Jika dikerjakan 2 orang,
gunakan timeline paralel di `09`: B mulai dari tampilan statis dan konten sambil
menunggu fondasi A selesai.

Jika hanya satu orang: kerjakan berurutan sesuai rencana harian. Jangan membuat
tiga peran sekaligus agar tidak ada yang setengah jadi.

---

## 4. Cakupan demo minimum (jika waktu molor)

Jika Rabu mendekat dan masih ada yang belum selesai, **prioritaskan urutan ini**:

1. Login nyata + 3 peran + akun nonaktif.
2. Admin: sekolah, pengguna, tambah/nonaktif, rekap.
3. Guru: daftar catatan yang dibagikan + detail.
4. Siswa: simpan 5 langkah + bagikan.
5. Angka dashboard dari database.
6. Dokumentasi cara menjalankan.

Yang boleh ditunda:

- Ganti password (bisa direset Admin).
- Pencarian/pagination.
- Log aktivitas tampil di UI.
- Cetak.
- Profil.
- Pengumuman.
- Ekspor.

**Jangan** menunda: keamanan akses, penyimpanan data, dan kejujuran angka.

---

## 5. Skrip demo untuk Cindy (±15 menit)

### Menit 1–2 — Konteks

“Ini KARVIA versi baru. Yang berubah: sekarang ada akun nyata, ada Admin,
bisa banyak sekolah, dan data benar-benar tersimpan.”

### Menit 3–6 — Admin

1. Login `admin`.
2. Tunjukkan dashboard: jumlah sekolah, siswa, Guru BK, yang pernah login.
3. Buka daftar sekolah → 5 sekolah setelah import `dummy_karvia2.sql`.
4. Buka detail sekolah → jumlah siswa & Guru BK.
5. Tambah Guru BK baru → simpan.
6. Nonaktifkan satu akun siswa → jelaskan “keluar = nonaktif, data tetap aman”.
7. Tunjukkan akun tersebut tidak bisa login.

### Menit 7–9 — Guru BK

1. Login sebagai Guru BK sekolah A.
2. Tunjukkan dashboard dengan angka nyata.
3. Buka catatan siswa → detail.
4. Tulis tindak lanjut → simpan.
5. Jelaskan Guru BK sekolah B tidak bisa melihat data ini.

### Menit 10–13 — Siswa

1. Login sebagai siswa.
2. Isi Kenali Diriku.
3. Simpan satu profesi.
4. Isi perbandingan, rencana, jurnal.
5. Buka Konsultasi → bagikan.
6. Logout, login ulang → data masih ada.

### Menit 14–15 — Bukti & penutup

1. Kembali ke Guru BK → catatan siswa muncul.
2. Tunjukkan rekap Admin berubah.
3. Jelaskan batasan: bukan tes psikologis; pendaftaran mandiri hanya untuk siswa; nonaktif bukan hapus.
4. Sampaikan cara backup dan langkah menjalankan.

---

## 6. Risiko & mitigasi

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| Waktu terlalu singkat | Fitur setengah jadi | Disiplin P0; demo minimum di bagian 4 |
| Cindy ingin fitur baru saat demo | Scope melebar | Catat di backlog, jangan langsung kerjakan |
| Bug lintas sekolah | Data bocor | Uji IDOR setiap hari; filter dari session |
| Data hilang | Kepercayaan hilang | Backup sebelum demo; uji restart |
| Password demo tersebar | Akses tidak sah | Ganti setelah demo; jangan tampilkan di UI |
| CDN lambat/offline | Tampilan rusak | Siapkan fallback `assets/vendor` bila perlu |
| Cindy tidak paham istilah | Sulit menerima hasil | Gunakan analogi & demo alur, bukan kode |
| Database tidak siap | Semua terhambat | Selesaikan `database.sql` di hari pertama |
| Salah paham “keluarkan” | Data terhapus | Default nonaktif; jelaskan dan konfirmasi |
| Hosting belum ada | Tidak bisa diakses sekolah | Demo lokal dulu; hosting setelah persetujuan |

---

## 7. Setelah demo (backlog)

Urutkan berdasarkan masukan Cindy, bukan keinginan teknis:

1. Perbaikan bug hasil demo.
2. Ekspor rekap (CSV/Excel).
3. Import akun siswa massal.
4. Pengumuman dari Guru BK.
5. CRUD profesi oleh Admin.
6. Jadwal konsultasi.
7. Log aktivitas yang bisa dilihat Admin.
8. Hosting + domain + HTTPS.
9. Backup otomatis.
10. Panduan penggunaan bergambar untuk sekolah.

---

## 8. Checklist “siap demo”

- [ ] Database terisi 2 sekolah & akun demo.
- [ ] Tidak ada error PHP di halaman utama.
- [ ] Tidak ada password/hash yang tampil di layar.
- [ ] Semua tombol yang terlihat benar-benar berfungsi.
- [ ] Angka dashboard sesuai data.
- [ ] Alur siswa → guru berjalan.
- [ ] Akun nonaktif ditolak.
- [ ] Data bertahan setelah restart.
- [ ] Backup tersimpan.
- [ ] Dokumentasi menjalankan tersedia.
- [ ] File HTML prototipe masih utuh.
