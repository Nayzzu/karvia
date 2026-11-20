-- =====================================================================
-- KARVIA — database.sql
-- Aplikasi bimbingan karier SMA/MA (PHP native + MySQL)
--
-- Isi file:
--   BAGIAN A — struktur tabel (12 tabel)
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

-- ---------------------------------------------------------------------
-- 12. artikel — artikel edukasi publik dari Guru BK
-- ---------------------------------------------------------------------
CREATE TABLE `artikel` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guru_id` BIGINT UNSIGNED NOT NULL,
  `nama_penulis` VARCHAR(150) NOT NULL,
  `judul` VARCHAR(200) NOT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `isi` MEDIUMTEXT NOT NULL,
  `diterbitkan_pada` DATETIME NOT NULL,
  `diperbarui_pada` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_artikel_terbit` (`diterbitkan_pada`),
  KEY `idx_artikel_guru_terbit` (`guru_id`, `diterbitkan_pada`),
  CONSTRAINT `fk_artikel_guru`
    FOREIGN KEY (`guru_id`) REFERENCES `pengguna` (`id`)
    ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- =====================================================================
-- BAGIAN B — DATA CONTOH / DEMO
-- Semua nama dan institusi di bagian ini bersifat fiktif.
-- Jangan gunakan akun atau isi berikut untuk pemakaian sekolah nyata.
-- Password contoh HANYA untuk demo lokal:
--   admin / admin
--   guru / guru
--   siswa / siswa
-- Akun tambahan untuk skenario privasi:
--   demo.admin / Admin123!
--   demo.guru1, demo.guru2 / Guru123!
--   demo.siswa1, demo.siswa2, demo.siswa3 / Siswa123!
-- Aktivitas login awal sengaja kosong (NULL/0), bukan aktivitas nyata.
-- =====================================================================

-- Dua sekolah fiktif: cukup untuk menguji pemisahan data antar-sekolah.
INSERT INTO `sekolah`
  (`id`, `nama`, `npsn`, `jenjang`, `alamat`, `kota`, `provinsi`, `status`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 'Sekolah Demo KARVIA Utara', NULL, 'SMA',
   'Alamat fiktif untuk data demo', 'Kota Contoh', 'Provinsi Contoh', 'aktif',
   '2026-11-19 08:00:00', NULL),
  (2, 'Sekolah Demo KARVIA Selatan', NULL, 'MA',
   'Alamat fiktif untuk data demo', 'Kota Contoh', 'Provinsi Contoh', 'aktif',
   '2026-11-19 08:00:00', NULL);

-- Sembilan akun fiktif: 2 Admin, 3 Guru BK, dan 4 siswa.
-- Akun admin/guru/siswa disediakan sebagai kombinasi singkat khusus demo lokal.
-- Hash dibuat dengan password_hash(); last_login_at/login_count tidak dipalsukan.
INSERT INTO `pengguna`
  (`id`, `sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`,
   `nisn`, `nip`, `kelas`, `jurusan`, `no_telepon`, `last_login_at`, `login_count`,
   `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, NULL, 'Admin Demo KARVIA', 'demo.admin',
   '$2y$10$vINplh3hI7eVmV68TI4d4ugayAVv5WsBep6.RODvrRoqm4pQHCbk2',
   'admin', 'aktif', NULL, NULL, NULL, NULL, NULL,
   NULL, 0, NULL, '2026-11-19 08:05:00', NULL),

  (7, NULL, 'Admin KARVIA', 'admin',
    '$2y$10$RxQu9WDMO.wE4Vl7mzK.jeb9PBus/KA/T.jFNCHPWZUzzGz/K7t8u',
    'admin', 'aktif', NULL, NULL, NULL, NULL, NULL,
    NULL, 0, NULL, '2026-11-20 08:05:00', NULL),

  (2, 1, 'Guru Demo Utara', 'demo.guru1',
   '$2y$10$AByhEf1LpBOgc4oGg1QVQu0n6p5DawZntitkwMqWWJ4yXMXFkc9d2',
   'guru_bk', 'aktif', NULL, NULL, NULL, NULL, NULL,
   NULL, 0, 1, '2026-11-19 08:05:00', NULL),

  (3, 2, 'Guru Demo Selatan', 'demo.guru2',
   '$2y$10$AByhEf1LpBOgc4oGg1QVQu0n6p5DawZntitkwMqWWJ4yXMXFkc9d2',
   'guru_bk', 'aktif', NULL, NULL, NULL, NULL, NULL,
   NULL, 0, 1, '2026-11-19 08:05:00', NULL),

  (8, 1, 'Guru BK KARVIA', 'guru',
    '$2y$10$ozWdTLk4VKfJnEMaNrzRaOY84XTHXo7ECNbegRG7VwohvHCU9N0o6',
    'guru_bk', 'aktif', NULL, 'DEMO-GURU', NULL, NULL, NULL,
    NULL, 0, 1, '2026-11-20 08:05:00', NULL),

  (9, 1, 'Siswa KARVIA', 'siswa',
    '$2y$10$B1Vua9CIJ6OmShi4VZfmtOG6utAl78SNvvJ8FWafiuakT71kFTqgC',
    'siswa', 'aktif', NULL, NULL, 'XI Demo', 'Contoh', NULL,
    NULL, 0, 1, '2026-11-20 08:05:00', NULL),

  (4, 1, 'Siswa Demo Berbagi', 'demo.siswa1',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', NULL, NULL, 'XI Demo', 'Contoh', NULL,
   NULL, 0, 1, '2026-11-19 08:05:00', NULL),

  (5, 1, 'Siswa Demo Privat', 'demo.siswa2',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', NULL, NULL, 'X Demo', 'Contoh', NULL,
   NULL, 0, 1, '2026-11-19 08:05:00', NULL),

  (10, 2, 'Siswa Demo Kosong', 'demo.siswa3',
   '$2y$10$bSx7Qno.tX9rCD/0OqZmNeIrxVxvHRR4B0Fe1UMxQiSxGe7NEpUOK',
   'siswa', 'aktif', NULL, NULL, 'XI Demo', 'Contoh', NULL,
   NULL, 0, 1, '2026-11-19 08:05:00', NULL);

-- Empat profesi inti yang ada di prototipe; bukan katalog produksi lengkap.
INSERT INTO `profesi`
  (`id`, `nama`, `kategori`, `deskripsi`, `kemampuan`, `jalur_pendidikan`, `sumber`, `urutan`, `status`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 'Dokter', 'kesehatan',
   'Memeriksa pasien, menegakkan diagnosis, dan memberi pengobatan atau rujukan.',
   'Analitis, teliti, empati, komunikasi, dan mengambil keputusan cepat.',
   'S1 Kedokteran, profesi dokter, spesialis (opsional)',
   'Data demo berdasarkan prototipe KARVIA', 1, 'aktif', '2026-11-19 08:10:00', NULL),
  (2, 'Software Engineer', 'teknologi',
   'Merancang dan menulis program, memperbaiki bug, serta membangun aplikasi.',
   'Logika, pemecahan masalah, ketekunan, dan kerja sama tim.',
   'S1 Informatika/Teknik Komputer, bootcamp, sertifikasi',
   'Data demo berdasarkan prototipe KARVIA', 2, 'aktif', '2026-11-19 08:10:00', NULL),
  (3, 'Guru BK', 'pendidikan',
   'Membantu siswa mengenali diri, memilih arah karier, dan mengatasi hambatan belajar.',
   'Empati, mendengarkan, komunikasi, dan menjaga kerahasiaan.',
   'S1 Bimbingan dan Konseling, PPG',
   'Data demo berdasarkan prototipe KARVIA', 3, 'aktif', '2026-11-19 08:10:00', NULL),
  (4, 'Advokat', 'lainnya',
   'Mendampingi klien, menyusun argumen hukum, dan menyelesaikan sengketa.',
   'Membaca, menulis, berargumen, negosiasi, dan etika profesi.',
   'S1 Hukum, pendidikan advokat',
   'Data demo berdasarkan prototipe KARVIA', 4, 'aktif', '2026-11-19 08:10:00', NULL);

-- Siswa Demo Berbagi: data eksplorasi secukupnya dan sudah membagikan ke Guru BK.
INSERT INTO `refleksi_diri`
  (`id`, `siswa_id`, `mapel_favorit`, `kekuatan`, `catatan_minat`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 4,
   '["Matematika & Sains"]',
   '["Analitis & Memecahkan Masalah"]',
   'Data fiktif demo: tertarik pada bidang kesehatan dan teknologi.',
   '2026-11-19 08:20:00', NULL);

INSERT INTO `pilihan_banding`
  (`id`, `siswa_id`, `opsi_a`, `catatan_a`, `opsi_b`, `catatan_b`, `kriteria`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 4, 'Dokter', 'Data fiktif demo: minat pada sains dan kesehatan.',
   'Software Engineer', 'Data fiktif demo: tertarik membangun teknologi.',
   '["Kesesuaian Minat", "Peluang Belajar"]', '2026-11-19 08:25:00', NULL);

INSERT INTO `rencana_karier`
  (`id`, `siswa_id`, `tujuan`, `langkah_1`, `langkah_2`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 4, 'Contoh tujuan karier untuk pengujian tampilan.',
   'Mencari informasi bidang yang diminati.', 'Berdiskusi dengan Guru BK.',
   '2026-11-19 08:30:00', NULL);

INSERT INTO `jurnal_refleksi`
  (`id`, `siswa_id`, `hal_baru`, `hambatan`, `dukungan`, `dibuat_pada`)
VALUES
  (1, 4, 'Data fiktif demo: saya belajar membandingkan beberapa pilihan.',
   'Contoh hambatan untuk pengujian.', 'Contoh dukungan untuk pengujian.',
   '2026-11-19 08:35:00');

INSERT INTO `profesi_disimpan`
  (`id`, `siswa_id`, `profesi_id`, `dibuat_pada`)
VALUES
  (1, 4, 1, '2026-11-19 08:22:00');

INSERT INTO `berbagi_catatan`
  (`id`, `siswa_id`, `sekolah_id`, `pesan_siswa`, `status`, `dibagikan_pada`, `dibaca_pada`, `diperbarui_pada`)
VALUES
  (1, 4, 1, 'Data fiktif demo: saya ingin mendiskusikan pilihan karier.',
   'baru', '2026-11-19 08:40:00', NULL, NULL);

-- Siswa Demo Privat: mempunyai refleksi, tetapi belum membagikannya.
-- Siswa Demo Kosong: sengaja tanpa data eksplorasi untuk menguji empty state.

-- =====================================================================
-- SELESAI
-- =====================================================================
