-- =====================================================================
-- KARVIA — database.sql
-- Aplikasi bimbingan karier SMA/MA (PHP native + MySQL)
--
-- Isi file:
--   BAGIAN A — struktur tabel (12 tabel)
--
-- Data contoh TIDAK lagi ada di file ini. Import database/dummy_karvia.sql
-- setelah file ini jika membutuhkan data demo.
--
-- Cara import (setelah MySQL Laragon berjalan):
--   mysql -u root < database/database.sql
--   mysql -u root < database/dummy_karvia.sql
-- atau lewat HeidiSQL/phpMyAdmin: buat database `karvia_db`, lalu import kedua file.
--
-- File ini dirancang untuk database KOSONG. Jangan menjalankannya berulang
-- pada database yang sudah berisi data tanpa backup terlebih dahulu.
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
  `terakhir_aktif_pada` DATETIME NULL,
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
  KEY `idx_pengguna_terakhir_aktif` (`terakhir_aktif_pada`),
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
  `gambar` VARCHAR(255) NULL,
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
-- SELESAI
--
-- File ini hanya berisi struktur. Data contoh ada di database/dummy_karvia.sql
-- =====================================================================
