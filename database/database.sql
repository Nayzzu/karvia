-- =====================================================================
-- KARVIA — database.sql
-- Aplikasi bimbingan karier SMA/MA (PHP native + MySQL)
--
-- Isi file:
--   BAGIAN A — struktur tabel (11 tabel)
--   BAGIAN B — data contoh (2 sekolah, 7 akun, 8 profesi, data karier)
--
-- Cara import (setelah MySQL Laragon berjalan):
--   mysql -u root karvia_db < database/database.sql
-- atau lewat HeidiSQL/phpMyAdmin: buat database `karvia_db`, lalu import file ini.
--
-- File ini dirancang untuk database KOSONG. Jangan menjalankannya berulang
-- pada database yang sudah berisi data tanpa backup terlebih dahulu.
-- Sebelum dipakai sekolah nyata, hapus BAGIAN B dan ganti semua password.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';

CREATE DATABASE IF NOT EXISTS `karvia_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `karvia_db`;

-- =====================================================================
-- BAGIAN A — STRUKTUR TABEL
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. sekolah
-- ---------------------------------------------------------------------
CREATE TABLE `sekolah` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(150) NOT NULL,
  `npsn` VARCHAR(20) NULL,
  `jenjang` ENUM('SMA', 'MA') NOT NULL DEFAULT 'SMA',
  `alamat` TEXT NULL,
  `kota` VARCHAR(100) NULL,
  `provinsi` VARCHAR(100) NULL,
  `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
  `dibuat_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sekolah_npsn` (`npsn`),
  KEY `idx_sekolah_status` (`status`),
  KEY `idx_sekolah_nama` (`nama`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. pengguna
--    Admin: sekolah_id NULL. Guru BK & Siswa: sekolah_id wajib.
-- ---------------------------------------------------------------------
CREATE TABLE `pengguna` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sekolah_id` BIGINT UNSIGNED NULL,
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'guru_bk', 'siswa') NOT NULL,
  `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
  `nisn` VARCHAR(20) NULL,
  `nip` VARCHAR(30) NULL,
  `kelas` VARCHAR(20) NULL,
  `jurusan` VARCHAR(50) NULL,
  `no_telepon` VARCHAR(20) NULL,
  `last_login_at` DATETIME NULL,
  `login_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `dibuat_oleh` BIGINT UNSIGNED NULL,
  `dibuat_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pengguna_username` (`username`),
  KEY `idx_pengguna_sekolah` (`sekolah_id`),
  KEY `idx_pengguna_role` (`role`),
  KEY `idx_pengguna_status` (`status`),
  KEY `idx_pengguna_last_login` (`last_login_at`),
  CONSTRAINT `fk_pengguna_sekolah`
    FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`)
    ON DELETE RESTRICT,
  CONSTRAINT `fk_pengguna_dibuat_oleh`
    FOREIGN KEY (`dibuat_oleh`) REFERENCES `pengguna` (`id`)
    ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. refleksi_diri — Langkah 1, satu baris per siswa
-- ---------------------------------------------------------------------
CREATE TABLE `refleksi_diri` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `mapel_favorit` JSON NULL,
  `kekuatan` JSON NULL,
  `catatan_minat` TEXT NULL,
  `dibuat_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_refleksi_siswa` (`siswa_id`),
  CONSTRAINT `fk_refleksi_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. pilihan_banding — Langkah 3, satu baris per siswa
-- ---------------------------------------------------------------------
CREATE TABLE `pilihan_banding` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `opsi_a` VARCHAR(150) NOT NULL,
  `catatan_a` TEXT NULL,
  `opsi_b` VARCHAR(150) NOT NULL,
  `catatan_b` TEXT NULL,
  `kriteria` JSON NULL,
  `dibuat_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_banding_siswa` (`siswa_id`),
  CONSTRAINT `fk_banding_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. rencana_karier — Langkah 4, satu baris per siswa
-- ---------------------------------------------------------------------
CREATE TABLE `rencana_karier` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `tujuan` VARCHAR(255) NOT NULL,
  `langkah_1` VARCHAR(255) NOT NULL,
  `langkah_2` VARCHAR(255) NOT NULL,
  `dibuat_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rencana_siswa` (`siswa_id`),
  CONSTRAINT `fk_rencana_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. jurnal_refleksi — Langkah 5, banyak baris per siswa
-- ---------------------------------------------------------------------
CREATE TABLE `jurnal_refleksi` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `hal_baru` TEXT NULL,
  `hambatan` TEXT NULL,
  `dukungan` TEXT NULL,
  `dibuat_pada` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_jurnal_siswa_waktu` (`siswa_id`, `dibuat_pada`),
  CONSTRAINT `fk_jurnal_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. profesi — katalog Jelajah Profesi
-- ---------------------------------------------------------------------
CREATE TABLE `profesi` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(150) NOT NULL,
  `kategori` ENUM('kesehatan', 'teknologi', 'pendidikan', 'bisnis', 'lainnya') NOT NULL,
  `deskripsi` TEXT NULL,
  `kemampuan` TEXT NULL,
  `jalur_pendidikan` VARCHAR(255) NULL,
  `sumber` VARCHAR(255) NULL,
  `urutan` INT NOT NULL DEFAULT 0,
  `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
  `dibuat_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_profesi_kategori` (`kategori`),
  KEY `idx_profesi_status` (`status`),
  KEY `idx_profesi_urutan` (`urutan`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. profesi_disimpan — bookmark siswa
-- ---------------------------------------------------------------------
CREATE TABLE `profesi_disimpan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `profesi_id` BIGINT UNSIGNED NOT NULL,
  `dibuat_pada` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_simpan_siswa_profesi` (`siswa_id`, `profesi_id`),
  KEY `idx_simpan_profesi` (`profesi_id`),
  CONSTRAINT `fk_simpan_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_simpan_profesi`
    FOREIGN KEY (`profesi_id`) REFERENCES `profesi` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. berbagi_catatan — bukti siswa membagikan catatan ke Guru BK
--    sekolah_id = sekolah siswa SAAT membagikan.
-- ---------------------------------------------------------------------
CREATE TABLE `berbagi_catatan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `sekolah_id` BIGINT UNSIGNED NOT NULL,
  `pesan_siswa` TEXT NULL,
  `status` ENUM('baru', 'dibaca', 'ditindaklanjuti') NOT NULL DEFAULT 'baru',
  `dibagikan_pada` DATETIME NOT NULL,
  `dibaca_pada` DATETIME NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_berbagi_siswa` (`siswa_id`),
  KEY `idx_berbagi_sekolah` (`sekolah_id`),
  KEY `idx_berbagi_status` (`status`),
  KEY `idx_berbagi_waktu` (`dibagikan_pada`),
  CONSTRAINT `fk_berbagi_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_berbagi_sekolah`
    FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`)
    ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. tindak_lanjut_bk — catatan Guru BK
-- ---------------------------------------------------------------------
CREATE TABLE `tindak_lanjut_bk` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `berbagi_id` BIGINT UNSIGNED NOT NULL,
  `guru_id` BIGINT UNSIGNED NOT NULL,
  `catatan` TEXT NOT NULL,
  `dibuat_pada` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tindak_berbagi_waktu` (`berbagi_id`, `dibuat_pada`),
  KEY `idx_tindak_guru` (`guru_id`),
  CONSTRAINT `fk_tindak_berbagi`
    FOREIGN KEY (`berbagi_id`) REFERENCES `berbagi_catatan` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_tindak_guru`
    FOREIGN KEY (`guru_id`) REFERENCES `pengguna` (`id`)
    ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. log_aktivitas — jejak aktivitas penting
-- ---------------------------------------------------------------------
CREATE TABLE `log_aktivitas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pengguna_id` BIGINT UNSIGNED NULL,
  `aksi` VARCHAR(50) NOT NULL,
  `keterangan` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `dibuat_pada` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_log_pengguna_waktu` (`pengguna_id`, `dibuat_pada`),
  KEY `idx_log_aksi` (`aksi`),
  KEY `idx_log_waktu` (`dibuat_pada`),
  CONSTRAINT `fk_log_pengguna`
    FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`)
    ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- =====================================================================
-- BAGIAN B — DATA CONTOH
-- Boleh dihapus sebelum pemakaian nyata.
-- Password contoh HANYA untuk demo lokal:
--   admin          / Admin123!
--   guru.nusantara / Guru123!
--   guru.alhikmah  / Guru123!
--   siswa.*        / Siswa123!
-- =====================================================================

-- 2 sekolah contoh (nama fiktif, bukan afiliasi sekolah nyata)
INSERT INTO `sekolah`
  (`id`, `nama`, `npsn`, `jenjang`, `alamat`, `kota`, `provinsi`, `status`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 'SMA Nusantara 1 Bandar Lampung', '10800001', 'SMA',
   'Jl. Contoh Pendidikan No. 1', 'Bandar Lampung', 'Lampung', 'aktif',
   '2026-11-19 08:00:00', NULL),
  (2, 'MA Al-Hikmah Bandar Lampung', '10800002', 'MA',
   'Jl. Contoh Keagamaan No. 2', 'Bandar Lampung', 'Lampung', 'aktif',
   '2026-11-19 08:00:00', NULL);

-- 7 akun contoh (hash dibuat dengan password_hash(), bukan teks biasa)
INSERT INTO `pengguna`
  (`id`, `sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`,
   `nisn`, `nip`, `kelas`, `jurusan`, `no_telepon`, `last_login_at`, `login_count`,
   `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, NULL, 'Administrator KARVIA', 'admin',
   '$2y$10$vINplh3hI7eVmV68TI4d4ugayAVv5WsBep6.RODvrRoqm4pQHCbk2',
   'admin', 'aktif', NULL, NULL, NULL, NULL, NULL,
   '2026-11-18 08:30:00', 12, NULL, '2026-11-19 08:05:00', NULL),

  (2, 1, 'Mega Aria Monica, M.Pd.', 'guru.nusantara',
   '$2y$10$AByhEf1LpBOgc4oGg1QVQu0n6p5DawZntitkwMqWWJ4yXMXFkc9d2',
   'guru_bk', 'aktif', NULL, '198501012010011001', NULL, NULL,
   '0812-0000-0001', '2026-11-18 10:00:00', 8, 1, '2026-11-19 08:05:00', NULL),

  (3, 2, 'Rina Kurniasih, S.Pd.', 'guru.alhikmah',
   '$2y$10$AByhEf1LpBOgc4oGg1QVQu0n6p5DawZntitkwMqWWJ4yXMXFkc9d2',
   'guru_bk', 'aktif', NULL, '199002022015012002', NULL, NULL,
   '0812-0000-0002', '2026-11-17 13:20:00', 4, 1, '2026-11-19 08:05:00', NULL),

  (4, 1, 'Ahmad Fauzan', 'siswa.ahmad',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', '0071234567', NULL, 'XI MIPA 1', 'MIPA',
   '0813-0000-0001', '2026-11-19 07:45:00', 6, 1, '2026-11-19 08:05:00', NULL),

  (5, 1, 'Siti Rahma', 'siswa.siti',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', '0071234568', NULL, 'X IPS 1', 'IPS',
   '0813-0000-0002', '2026-11-19 08:10:00', 3, 1, '2026-11-19 08:05:00', NULL),

  (6, 2, 'Raka Pratama', 'siswa.raka',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', '0071234569', NULL, 'XI MIPA 2', 'MIPA',
   NULL, NULL, 0, 1, '2026-11-19 08:05:00', NULL),

  (7, 2, 'Nabila Putri', 'siswa.nabila',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', '0071234570', NULL, 'X MIPA 1', 'MIPA',
   NULL, NULL, 0, 1, '2026-11-19 08:05:00', NULL);

-- 8 profesi untuk Jelajah Profesi
INSERT INTO `profesi`
  (`id`, `nama`, `kategori`, `deskripsi`, `kemampuan`, `jalur_pendidikan`, `sumber`, `urutan`, `status`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 'Dokter', 'kesehatan',
   'Memeriksa pasien, menegakkan diagnosis, dan memberi pengobatan atau rujukan.',
   'Analitis, teliti, empati, komunikasi, dan mengambil keputusan cepat.',
   'S1 Kedokteran, profesi dokter, spesialis (opsional)',
   'Prototipe KARVIA', 1, 'aktif', '2026-11-19 08:10:00', NULL),

  (2, 'Software Engineer', 'teknologi',
   'Merancang dan menulis program, memperbaiki bug, serta membangun aplikasi.',
   'Logika, pemecahan masalah, ketekunan, dan kerja sama tim.',
   'S1 Informatika/Teknik Komputer, bootcamp, sertifikasi',
   'Prototipe KARVIA', 2, 'aktif', '2026-11-19 08:10:00', NULL),

  (3, 'Guru BK', 'pendidikan',
   'Membantu siswa mengenali diri, memilih arah karier, dan mengatasi hambatan belajar.',
   'Empati, mendengarkan, komunikasi, dan menjaga kerahasiaan.',
   'S1 Bimbingan dan Konseling, PPG',
   'Prototipe KARVIA', 3, 'aktif', '2026-11-19 08:10:00', NULL),

  (4, 'Advokat', 'lainnya',
   'Mendampingi klien, menyusun argumen hukum, dan menyelesaikan sengketa.',
   'Membaca, menulis, berargumen, negosiasi, dan etika profesi.',
   'S1 Hukum, pendidikan advokat',
   'Prototipe KARVIA', 4, 'aktif', '2026-11-19 08:10:00', NULL),

  (5, 'Perawat', 'kesehatan',
   'Merawat pasien, memantau kondisi, dan membantu proses penyembuhan.',
   'Teliti, sabar, empati, dan siap bekerja dalam tim.',
   'D3/S1 Keperawatan, uji kompetensi',
   'Data contoh KARVIA', 5, 'aktif', '2026-11-19 08:10:00', NULL),

  (6, 'Analis Data', 'teknologi',
   'Mengolah data menjadi informasi yang membantu pengambilan keputusan.',
   'Statistika, logika, ketelitian, dan visualisasi data.',
   'S1 Statistika/Ilmu Data, sertifikasi analisis data',
   'Data contoh KARVIA', 6, 'aktif', '2026-11-19 08:10:00', NULL),

  (7, 'Wirausaha Digital', 'bisnis',
   'Membangun dan mengelola usaha berbasis internet.',
   'Kreativitas, komunikasi, pengelolaan keuangan, dan keberanian mengambil risiko.',
   'S1 Bisnis/Manajemen, pelatihan kewirausahaan',
   'Data contoh KARVIA', 7, 'aktif', '2026-11-19 08:10:00', NULL),

  (8, 'Dosen', 'pendidikan',
   'Mengajar, meneliti, dan membimbing mahasiswa di perguruan tinggi.',
   'Penguasaan bidang ilmu, komunikasi, menulis, dan penelitian.',
   'S2/S3 sesuai bidang, publikasi ilmiah',
   'Data contoh KARVIA', 8, 'aktif', '2026-11-19 08:10:00', NULL);

-- Ahmad (id 4) — data langkah 1-5 + berbagi ke Guru BK
INSERT INTO `refleksi_diri`
  (`id`, `siswa_id`, `mapel_favorit`, `kekuatan`, `catatan_minat`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 4,
   '["Matematika & Sains", "Teknologi & Kewirausahaan"]',
   '["Analitis & Memecahkan Masalah", "Teliti & Terstruktur"]',
   'Tertarik pada teknologi kesehatan dan ingin tahu cara kerja alat medis.',
   '2026-11-19 08:20:00', NULL);

INSERT INTO `pilihan_banding`
  (`id`, `siswa_id`, `opsi_a`, `catatan_a`, `opsi_b`, `catatan_b`, `kriteria`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 4,
   'Dokter',
   'Sesuai minat pada sains dan kesehatan, tetapi masa studi panjang dan biaya besar.',
   'Software Engineer',
   'Cepat mendapat pengalaman kerja dan banyak peluang, tetapi perlu terus belajar teknologi baru.',
   '["Kesesuaian Minat", "Biaya & Durasi Studi", "Peluang Kerja"]',
   '2026-11-19 08:25:00', NULL);

INSERT INTO `rencana_karier`
  (`id`, `siswa_id`, `tujuan`, `langkah_1`, `langkah_2`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 4,
   'Masuk jurusan Kedokteran di PTN pilihan.',
   'Belajar Biologi 30 menit setiap hari.',
   'Mengikuti try out UTBK bulan depan.',
   '2026-11-19 08:30:00', NULL);

INSERT INTO `jurnal_refleksi`
  (`id`, `siswa_id`, `hal_baru`, `hambatan`, `dukungan`, `dibuat_pada`)
VALUES
  (1, 4,
   'Saya baru tahu bahwa kedokteran dan teknologi bisa bertemu di bidang kesehatan digital.',
   'Sulit membagi waktu antara belajar Biologi dan latihan coding.',
   'Orang tua mendukung selama saya bisa menjelaskan rencananya.',
   '2026-11-18 19:10:00'),
  (2, 4,
   'Saya belajar membandingkan pilihan dengan kriteria, bukan hanya mengikuti teman.',
   'Masih ragu antara dua pilihan yang sama-sama menarik.',
   'Guru BK bersedia mendengarkan rencana saya.',
   '2026-11-19 07:50:00');

INSERT INTO `profesi_disimpan`
  (`id`, `siswa_id`, `profesi_id`, `dibuat_pada`)
VALUES
  (1, 4, 1, '2026-11-19 08:22:00'),
  (2, 4, 2, '2026-11-19 08:23:00');

INSERT INTO `berbagi_catatan`
  (`id`, `siswa_id`, `sekolah_id`, `pesan_siswa`, `status`, `dibagikan_pada`, `dibaca_pada`, `diperbarui_pada`)
VALUES
  (1, 4, 1,
   'Saya ingin mendiskusikan pilihan antara kedokteran dan teknologi.',
   'baru', '2026-11-19 08:35:00', NULL, NULL);

-- Siti (id 5) — sudah mengisi data, tetapi BELUM membagikan (contoh privasi)
INSERT INTO `refleksi_diri`
  (`id`, `siswa_id`, `mapel_favorit`, `kekuatan`, `catatan_minat`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (2, 5,
   '["Ilmu Sosial & Keagamaan", "Bahasa & Sastra"]',
   '["Empati & Membantu Sesama", "Komunikasi & Berdiskusi"]',
   'Ingin menjadi guru atau konselor yang bisa membantu teman.',
   '2026-11-19 08:40:00', NULL);

INSERT INTO `pilihan_banding`
  (`id`, `siswa_id`, `opsi_a`, `catatan_a`, `opsi_b`, `catatan_b`, `kriteria`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (2, 5,
   'Guru BK',
   'Bisa mendampingi siswa, tetapi perlu belajar mendengarkan dan menjaga rahasia.',
   'Psikolog',
   'Pendalaman ilmu lebih dalam, tetapi masa studi dan praktik lebih panjang.',
   '["Kesesuaian Minat", "Peluang Kerja"]',
   '2026-11-19 08:42:00', NULL);

INSERT INTO `rencana_karier`
  (`id`, `siswa_id`, `tujuan`, `langkah_1`, `langkah_2`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (2, 5,
   'Mengenal profesi bimbingan dan konseling lebih dekat.',
   'Mewawancarai Guru BK di sekolah.',
   'Membaca satu artikel tentang psikologi pendidikan.',
   '2026-11-19 08:45:00', NULL);

INSERT INTO `jurnal_refleksi`
  (`id`, `siswa_id`, `hal_baru`, `hambatan`, `dukungan`, `dibuat_pada`)
VALUES
  (3, 5,
   'Saya baru sadar bahwa mendengarkan juga butuh latihan.',
   'Kadang saya terlalu cepat memberi saran.',
   'Teman-teman senang bercerita, jadi saya bisa berlatih.',
   '2026-11-19 08:15:00');

INSERT INTO `profesi_disimpan`
  (`id`, `siswa_id`, `profesi_id`, `dibuat_pada`)
VALUES
  (3, 5, 3, '2026-11-19 08:43:00'),
  (4, 5, 8, '2026-11-19 08:44:00');

-- Raka (id 6) dan Nabila (id 7) sengaja dibiarkan tanpa data
-- agar empty state di halaman siswa dan Guru BK bisa diuji.

-- =====================================================================
-- SELESAI
-- =====================================================================
