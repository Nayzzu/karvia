# Konteks Proyek KARVIA (untuk AI)

> Dipindah dari `AGENTS.md` di akar repo. Akar repo hanya menyimpan stub
> penunjuk ke file ini.

Proyek: **KARVIA** — aplikasi bimbingan karier SMA/MA, PHP native + MySQL (Laragon),
tiga peran: **Admin, Guru BK, Siswa**, multi-sekolah (5 sekolah contoh; tambahan 3 sekolah Bandar Lampung).

Struktur folder mengikuti pola per peran: `admin/`, `guru_bk/`, `siswa/`,
`includes/`, `config/`, `database/`, `assets/`, `docs/`, plus `index.php`,
`login.php`, `logout.php` di akar. Lokasi folder bebas; path bertingkat seperti
`www\TUBES_PRK_PEMWEB_2026\kelompok\kelompok_18` hanya contoh penataan.
Database: `database/database.sql` (struktur), `database/dummy_karvia.sql` (data awal),
`database/dummy_karvia2.sql` (data tambahan Bandar Lampung), tanpa folder migration/seeder.

## Wajib dibaca sebelum mengubah kode

1. `docs/README.md`
2. `docs/00-ringkasan-proyek.md`
3. `docs/01-kebutuhan-fungsional.md`
4. `docs/02-arsitektur-dan-struktur-folder.md`
5. `docs/03-rencana-database.md`
6. `docs/04-rencana-halaman-dan-alur.md`
7. `docs/05-panduan-kerja-untuk-ai.md`
8. `docs/09-pembagian-tugas-2-orang.md` (jobdesk 2 pengembang)

Status saat ini: **aplikasi berjalan** — alur Admin, Guru BK, dan Siswa tersedia; siswa dapat daftar mandiri; data demo lima sekolah tersedia. Nama lengkap pada data dummy Bandar Lampung diperbarui menjadi nama fiktif natural; username demo dipertahankan. Rincian data ada di `docs/03`, `docs/12`, dan `docs/14`.

## Aturan utama

- `docs/karvia_career_vision_action.html` adalah prototipe acuan. **Jangan diubah
  atau dihapus.**
- Jangan menambah fitur di luar ruang lingkup MVP tanpa mencatatnya di
  `docs/08-keputusan-terbuka.md`; keputusan yang sudah disetujui ada di tabel D-30/D-31.
- Gunakan PHP native + PDO + session. Tanpa framework, Composer, atau localStorage.
- Login nyata (username + password hash); peran berasal dari akun, bukan pilihan user. Pendaftaran mandiri hanya untuk siswa; Admin dan Guru BK dibuat Admin. Siswa dapat mengganti username setelah verifikasi password.
- “Keluarkan pengguna” = **nonaktifkan**, bukan hapus permanen.
- Guru BK hanya melihat data siswa di sekolahnya yang sudah dibagikan.
- Admin tidak membaca isi jurnal siswa (hanya rekap).
- Semua angka dashboard dihitung dari database, bukan hardcoded.
- Semua query memakai prepared statement, output di-escape, form POST memakai CSRF.
- Bahasa UI dan nama tabel/fungsi: Indonesia, sederhana, mudah dipelajari Cindy.
- Setiap perubahan skema database wajib memperbarui `docs/03`.
- Database memakai `database/database.sql` (struktur), `database/dummy_karvia.sql`
  (data awal, destructive), dan `database/dummy_karvia2.sql` (data tambahan non-destructive).
  Jangan membuat folder `migrations/` atau `seeders/`.
- Satu halaman = satu file; halaman peran hanya di `admin/`, `guru_bk/`, `siswa/`.
  Jangan membuat front controller dengan `?page=`.
- Ikuti urutan build dan checklist di `docs/05`; untuk pembagian kerja lihat `docs/09`.

## Cara melanjutkan

Baca `docs/05` bagian 1 (status), bagian 4 (urutan build), dan bagian 12
(ringkasan konteks). Lanjutkan dari fase yang belum selesai. Jika ada keputusan
yang mengubah data/hak akses, tanyakan dulu; jika hanya detail tampilan, gunakan
default di `docs/08`.
