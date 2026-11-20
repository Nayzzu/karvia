-- =====================================================================
-- KARVIA — dummy_karvia.sql
-- Data contoh untuk pengembangan lokal (2 sekolah).
--
-- Isi:
--   - 2 sekolah
--   - 1 admin global + 1 admin sekolah per sekolah
--   - 3 Guru BK per sekolah (6 total)
--   - 30 siswa per sekolah (60 total)
--   - 12 artikel (2 per Guru BK) + gambar di uploads/artikel/
--   - 4 profesi katalog beserta fotonya
--
-- Akun demo (username / password):
--   admin   / admin   -> admin global
--   admin1  / admin   -> admin SMAN 1 Bandung
--   admin2  / admin   -> admin MAN 2 Yogyakarta
--   guru1 .. guru6  / guru
--   siswa1 .. siswa60 / siswa
--
-- Cara import:
--   mysql -u root karvia_db < database/dummy_karvia.sql
-- File ini menghapus seluruh isi tabel terlebih dahulu (TRUNCATE).
-- Semua nama, NISN, NIP, dan nomor telepon bersifat fiktif.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';
USE `karvia_db`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `artikel`;
TRUNCATE TABLE `berbagi_catatan`;
TRUNCATE TABLE `jurnal_refleksi`;
TRUNCATE TABLE `log_aktivitas`;
TRUNCATE TABLE `pengguna`;
TRUNCATE TABLE `pilihan_banding`;
TRUNCATE TABLE `profesi`;
TRUNCATE TABLE `profesi_disimpan`;
TRUNCATE TABLE `refleksi_diri`;
TRUNCATE TABLE `rencana_karier`;
TRUNCATE TABLE `sekolah`;
TRUNCATE TABLE `tindak_lanjut_bk`;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `sekolah`
  (`id`, `nama`, `npsn`, `jenjang`, `alamat`, `kota`, `provinsi`, `status`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 'SMAN 1 Bandung', '20219181', 'SMA', 'Jl. Ir. H. Juanda No. 93', 'Bandung', 'Jawa Barat', 'aktif', '2026-09-01 08:00:00', NULL),
  (2, 'MAN 2 Yogyakarta', '20404157', 'MA', 'Jl. K.H.A. Dahlan No. 130', 'Yogyakarta', 'DI Yogyakarta', 'aktif', '2026-09-01 08:00:00', NULL);

-- 69 akun: 1 admin global, 2 admin sekolah, 6 Guru BK, 60 siswa.
INSERT INTO `pengguna`
  (`id`, `sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`,
   `nisn`, `nip`, `kelas`, `jurusan`, `no_telepon`, `last_login_at`, `terakhir_aktif_pada`,
   `login_count`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, NULL, 'Agus Prasetyo', 'admin', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', 'aktif', NULL, NULL, NULL, NULL, '081200000101', NULL, NULL, 0, NULL, '2026-09-01 08:10:00', NULL),
  (2, '1', 'Nurul Hidayah, S.Pd.', 'admin1', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', 'aktif', NULL, NULL, NULL, NULL, '081200000102', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (3, '2', 'Bambang Sutrisno, S.Pd.', 'admin2', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', 'aktif', NULL, NULL, NULL, NULL, '081200000103', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL);

INSERT INTO `pengguna`
  (`id`, `sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`,
   `nisn`, `nip`, `kelas`, `jurusan`, `no_telepon`, `last_login_at`, `terakhir_aktif_pada`,
   `login_count`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (4, '1', 'Budi Santoso, S.Pd.', 'guru1', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', 'aktif', NULL, '19801015201001001', NULL, NULL, '081300000001', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (5, '1', 'Rina Kartika Sari, M.Pd.', 'guru2', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', 'aktif', NULL, '19811116201101002', NULL, NULL, '081300000002', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (6, '1', 'Ahmad Fauzi, S.Pd.', 'guru3', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', 'aktif', NULL, '19821217201201003', NULL, NULL, '081300000003', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (7, '2', 'Dewi Anggraini, M.Pd.', 'guru4', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', 'aktif', NULL, '19831318201301004', NULL, NULL, '081300000004', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (8, '2', 'Hendra Wijaya, S.Pd.', 'guru5', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', 'aktif', NULL, '19841419201401005', NULL, NULL, '081300000005', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (9, '2', 'Sri Wahyuni, M.Pd.', 'guru6', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', 'aktif', NULL, '19851520201501006', NULL, NULL, '081300000006', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (10, '1', 'Eko Simanjuntak', 'siswa1', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000001', NULL, 'X', 'MIPA', '081400010000', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (11, '1', 'Galih Wijaya', 'siswa2', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000002', NULL, 'XI', 'MIPA', '081400020001', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (12, '1', 'Bayu Setiawan', 'siswa3', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000003', NULL, 'XII', 'MIPA', '081400030002', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (13, '1', 'Kartika Setiawan', 'siswa4', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000004', NULL, 'X', 'IPS', '081400040003', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (14, '1', 'Rizky Simanjuntak', 'siswa5', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000005', NULL, 'XI', 'IPS', '081400050004', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (15, '1', 'Kartika Simanjuntak', 'siswa6', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000006', NULL, 'XII', 'IPS', '081400060005', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (16, '1', 'Dimas Anggraini', 'siswa7', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000007', NULL, 'X', 'Bahasa', '081400070006', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (17, '1', 'Kevin Wijaya', 'siswa8', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000008', NULL, 'XI', 'Bahasa', '081400080007', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (18, '1', 'Citra Wijaya', 'siswa9', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000009', NULL, 'XII', 'Bahasa', '081400090008', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (19, '1', 'Taufik Kurniawan', 'siswa10', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000010', NULL, 'X', 'MIPA', '081400100009', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (20, '1', 'Aisyah Rahayu', 'siswa11', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000011', NULL, 'XI', 'MIPA', '081400110010', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (21, '1', 'Putri Halim', 'siswa12', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000012', NULL, 'XII', 'MIPA', '081400120011', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (22, '1', 'Aisyah Kurniawan', 'siswa13', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000013', NULL, 'X', 'IPS', '081400130012', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (23, '1', 'Rizky Setiawan', 'siswa14', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000014', NULL, 'XI', 'IPS', '081400140013', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (24, '1', 'Eko Permata', 'siswa15', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000015', NULL, 'XII', 'IPS', '081400150014', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (25, '1', 'Kartika Purnama', 'siswa16', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000016', NULL, 'X', 'Bahasa', '081400160015', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (26, '1', 'Salma Wulandari', 'siswa17', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000017', NULL, 'XI', 'Bahasa', '081400170016', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (27, '1', 'Maya Firmansyah', 'siswa18', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000018', NULL, 'XII', 'Bahasa', '081400180017', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (28, '1', 'Dimas Permata', 'siswa19', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000019', NULL, 'X', 'MIPA', '081400190018', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (29, '1', 'Joko Hartono', 'siswa20', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000020', NULL, 'XI', 'MIPA', '081400200019', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (30, '1', 'Laras Rahmawati', 'siswa21', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000021', NULL, 'XII', 'MIPA', '081400210020', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (31, '1', 'Bella Prasetya', 'siswa22', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000022', NULL, 'X', 'IPS', '081400220021', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (32, '1', 'Salma Santoso', 'siswa23', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000023', NULL, 'XI', 'IPS', '081400230022', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (33, '1', 'Zahra Hartono', 'siswa24', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000024', NULL, 'XII', 'IPS', '081400240023', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (34, '1', 'Hafiz Nugroho', 'siswa25', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000025', NULL, 'X', 'Bahasa', '081400250024', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (35, '1', 'Jihan Maulana', 'siswa26', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000026', NULL, 'XI', 'Bahasa', '081400260025', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (36, '1', 'Bayu Maulana', 'siswa27', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000027', NULL, 'XII', 'Bahasa', '081400270026', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (37, '1', 'Eko Pratama', 'siswa28', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000028', NULL, 'X', 'MIPA', '081400280027', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (38, '1', 'Kartika Rahmawati', 'siswa29', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000029', NULL, 'XI', 'MIPA', '081400290028', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (39, '1', 'Jihan Pratama', 'siswa30', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000030', NULL, 'XII', 'MIPA', '081400300029', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (40, '2', 'Vina Yuliana', 'siswa31', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000031', NULL, 'X', 'IPS', '081400310030', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (41, '2', 'Iqbal Halim', 'siswa32', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000032', NULL, 'XI', 'IPS', '081400320031', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (42, '2', 'Lukman Halim', 'siswa33', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000033', NULL, 'XII', 'IPS', '081400330032', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (43, '2', 'Fitri Situmorang', 'siswa34', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000034', NULL, 'X', 'Bahasa', '081400340033', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (44, '2', 'Bella Situmorang', 'siswa35', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000035', NULL, 'XI', 'Bahasa', '081400350034', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (45, '2', 'Ayu Purnama', 'siswa36', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000036', NULL, 'XII', 'Bahasa', '081400360035', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (46, '2', 'Fitri Santoso', 'siswa37', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000037', NULL, 'X', 'MIPA', '081400370036', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (47, '2', 'Tari Pratama', 'siswa38', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000038', NULL, 'XI', 'MIPA', '081400380037', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (48, '2', 'Rizky Ramadhan', 'siswa39', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000039', NULL, 'XII', 'MIPA', '081400390038', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (49, '2', 'Sandi Permata', 'siswa40', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000040', NULL, 'X', 'IPS', '081400400039', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (50, '2', 'Sari Oktaviani', 'siswa41', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000041', NULL, 'XI', 'IPS', '081400410040', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (51, '2', 'Kevin Santoso', 'siswa42', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000042', NULL, 'XII', 'IPS', '081400420041', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (52, '2', 'Sari Rahayu', 'siswa43', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000043', NULL, 'X', 'Bahasa', '081400430042', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (53, '2', 'Vina Prasetya', 'siswa44', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000044', NULL, 'XI', 'Bahasa', '081400440043', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (54, '2', 'Anisa Susanti', 'siswa45', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000045', NULL, 'XII', 'Bahasa', '081400450044', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (55, '2', 'Nurul Wijaya', 'siswa46', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000046', NULL, 'X', 'MIPA', '081400460045', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (56, '2', 'Fajar Hartono', 'siswa47', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000047', NULL, 'XI', 'MIPA', '081400470046', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (57, '2', 'Wahyu Kurniawan', 'siswa48', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000048', NULL, 'XII', 'MIPA', '081400480047', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (58, '2', 'Rizky Safitri', 'siswa49', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000049', NULL, 'X', 'IPS', '081400490048', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (59, '2', 'Agus Saputra', 'siswa50', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000050', NULL, 'XI', 'IPS', '081400500049', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (60, '2', 'Hafiz Nasution', 'siswa51', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000051', NULL, 'XII', 'IPS', '081400510050', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (61, '2', 'Dedi Setiawan', 'siswa52', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000052', NULL, 'X', 'Bahasa', '081400520051', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (62, '2', 'Fajar Yuliana', 'siswa53', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000053', NULL, 'XI', 'Bahasa', '081400530052', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (63, '2', 'Gita Setiawan', 'siswa54', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000054', NULL, 'XII', 'Bahasa', '081400540053', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (64, '2', 'Zaki Safitri', 'siswa55', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000055', NULL, 'X', 'MIPA', '081400550054', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (65, '2', 'Naufal Oktaviani', 'siswa56', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000056', NULL, 'XI', 'MIPA', '081400560055', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (66, '2', 'Gita Yuliana', 'siswa57', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000057', NULL, 'XII', 'MIPA', '081400570056', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (67, '2', 'Elsa Handayani', 'siswa58', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000058', NULL, 'X', 'IPS', '081400580057', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (68, '2', 'Putri Ramadhan', 'siswa59', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000059', NULL, 'XI', 'IPS', '081400590058', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL),
  (69, '2', 'Taufik Firmansyah', 'siswa60', '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif', '0061000060', NULL, 'XII', 'IPS', '081400600059', NULL, NULL, 0, 1, '2026-09-01 08:10:00', NULL);

INSERT INTO `profesi`
  (`id`, `nama`, `gambar`, `kategori`, `deskripsi`, `kemampuan`, `jalur_pendidikan`, `sumber`,
   `urutan`, `status`, `dibuat_pada`, `diperbarui_pada`)
VALUES
  (1, 'Dokter', 'assets/img/profesi/dokter.jpg', 'kesehatan', 'Memeriksa pasien, menegakkan diagnosis, dan memberi pengobatan atau rujukan.', 'Analitis, teliti, empati, komunikasi, dan mengambil keputusan cepat.', 'S1 Kedokteran, profesi dokter, spesialis (opsional)', 'Katalog KARVIA', 1, 'aktif', '2026-09-02 08:00:00', NULL),
  (2, 'Software Engineer', 'assets/img/profesi/software-engineer.jpg', 'teknologi', 'Merancang dan menulis program, memperbaiki bug, serta membangun aplikasi.', 'Logika, pemecahan masalah, ketekunan, dan kerja sama tim.', 'S1 Informatika/Teknik Komputer, bootcamp, sertifikasi', 'Katalog KARVIA', 2, 'aktif', '2026-09-02 08:00:00', NULL),
  (3, 'Guru BK', 'assets/img/profesi/guru-bk.jpg', 'pendidikan', 'Membantu siswa mengenali diri, memilih arah karier, dan mengatasi hambatan belajar.', 'Empati, mendengarkan, komunikasi, dan menjaga kerahasiaan.', 'S1 Bimbingan dan Konseling, PPG', 'Katalog KARVIA', 3, 'aktif', '2026-09-02 08:00:00', NULL),
  (4, 'Advokat', 'assets/img/profesi/advokat.jpg', 'lainnya', 'Mendampingi klien, menyusun argumen hukum, dan menyelesaikan sengketa.', 'Membaca, menulis, berargumen, negosiasi, dan etika profesi.', 'S1 Hukum, pendidikan advokat', 'Katalog KARVIA', 4, 'aktif', '2026-09-02 08:00:00', NULL);

-- Dua artikel untuk setiap Guru BK; gambar ada di uploads/artikel/.
INSERT INTO `artikel`
  (`id`, `guru_id`, `nama_penulis`, `judul`, `gambar`, `isi`, `diterbitkan_pada`, `diperbarui_pada`)
VALUES
  (1, 4, 'Budi Santoso, S.Pd.', 'Mengenali Minat dan Bakat Sejak Kelas X', 'artikel-01.jpg', 'Banyak siswa mengira minat dan bakat adalah sesuatu yang sudah pasti sejak lahir. Kenyataannya, minat terbentuk dari pengalaman yang kita jalani setiap hari. Semakin sering kamu mencoba hal baru, semakin jelas gambaran tentang apa yang benar-benar membuatmu bersemangat.

Mulailah dari hal sederhana. Catat kegiatan yang membuatmu lupa waktu, mata pelajaran yang paling mudah kamu pahami, dan tugas kelompok yang paling kamu nikmati. Dari catatan kecil itu, kamu akan melihat pola yang tidak terlihat sebelumnya.

Jangan takut mencoba bidang yang belum kamu kuasai. Nilai rendah pada satu mata pelajaran bukan berarti kamu tidak berbakat di bidang itu. Bisa jadi cara belajarnya belum cocok, bukan kemampuanmu yang kurang.

Diskusikan temuanmu dengan Guru BK atau orang tua. Mereka bisa membantu melihat sisi lain dari dirimu yang mungkin belum kamu sadari. Ingat, mengenali diri adalah proses panjang, bukan ujian yang harus selesai dalam satu malam.', '2026-10-05 09:15:00', NULL),
  (2, 4, 'Budi Santoso, S.Pd.', 'Menyusun Rencana Karier yang Realistis', 'artikel-02.jpg', 'Rencana karier yang baik bukan sekadar menulis cita-cita besar di atas kertas. Rencana yang baik memuat langkah kecil yang bisa kamu mulai minggu ini, bukan hanya impian yang jauh di ujung jalan.

Mulai dengan tujuan yang jelas. Alih-alih menulis ingin menjadi dokter, tulis ingin masuk jurusan kedokteran di universitas negeri. Tujuan yang spesifik membuat langkah berikutnya lebih mudah ditentukan.

Setelah itu, pecah tujuan besar menjadi target tahunan, bulanan, dan mingguan. Contohnya, minggu ini mencari informasi syarat masuk, bulan depan mulai memperbaiki nilai biologi. Langkah kecil terasa ringan, tetapi hasilnya menumpuk.

Terakhir, siapkan rencana cadangan. Rencana cadangan bukan tanda menyerah, melainkan tanda kamu berpikir matang. Siswa yang punya dua jalur biasanya lebih tenang menghadapi hasil seleksi karena tahu masih ada pilihan lain yang tetap sejalan dengan minatnya.', '2026-10-19 10:30:00', NULL),
  (3, 5, 'Rina Kartika Sari, M.Pd.', 'Memilih Jurusan Kuliah Sesuai Kepribadian', 'artikel-03.jpg', 'Jurusan kuliah yang tepat bukan hanya soal nilai rapor atau gengsi nama kampus. Jurusan yang tepat adalah tempat kamu bisa bertahan belajar selama empat tahun tanpa kehilangan semangat.

Kenali cara kerjamu. Apakah kamu lebih nyaman bekerja dengan angka, dengan manusia, dengan mesin, atau dengan gagasan? Jawaban itu membantu menyaring puluhan pilihan menjadi beberapa yang paling masuk akal.

Jangan lupa memeriksa isi kurikulum. Nama jurusan sering terdengar menarik, tetapi mata kuliahnya bisa sangat berbeda dari bayangan. Baca daftar mata kuliah, lalu bayangkan apakah kamu menikmati empat tahun belajar hal tersebut.

Bicaralah dengan mahasiswa yang sudah menjalani jurusan incaranmu. Cerita mereka tentang tugas, praktikum, dan suasana kelas jauh lebih jujur daripada brosur kampus. Keputusan yang diambil setelah mencari informasi biasanya lebih tahan lama.', '2026-10-08 08:45:00', NULL),
  (4, 5, 'Rina Kartika Sari, M.Pd.', 'Mengelola Rasa Cemas Menjelang UTBK', 'artikel-04.jpg', 'Rasa cemas sebelum ujian besar adalah hal normal. Tubuh sedang bersiap menghadapi tantangan, dan itu bukan tanda kelemahan. Masalahnya hanya ketika cemas tumbuh terlalu besar sampai mengganggu belajar dan tidur.

Salah satu cara paling sederhana adalah menuliskan kekhawatiranmu. Setelah tertulis, kekhawatiran terlihat lebih kecil dan lebih jelas. Kamu bisa memilah mana yang benar-benar bisa dikendalikan dan mana yang harus diterima.

Jaga pola tidur dan makan. Belajar sampai tengah malam sering terasa produktif, tetapi daya ingat justru menurun. Otak butuh istirahat untuk menyimpan apa yang sudah dipelajari sepanjang hari.

Latihan soal tetap penting, tetapi jangan mengukur harga dirimu dari skor try out. Skor adalah alat untuk menemukan bagian yang perlu diperbaiki, bukan vonis tentang masa depanmu. Jika cemas terasa berat berkepanjangan, ceritakan kepada Guru BK. Kamu tidak harus menghadapinya sendiri.', '2026-10-22 13:20:00', NULL),
  (5, 6, 'Ahmad Fauzi, S.Pd.', 'Jurnal Refleksi Diri: Cermin Pertumbuhanmu', 'artikel-05.jpg', 'Jurnal refleksi sering dianggap tugas tambahan yang membosankan. Padahal, menulis beberapa baris setiap hari bisa menjadi cara paling murah untuk memahami dirimu sendiri.

Kamu tidak perlu menulis dengan bahasa yang indah. Cukup jawab tiga pertanyaan: apa yang saya pelajari hari ini, apa yang membuat saya kesulitan, dan apa yang ingin saya coba besok. Tiga kalimat sudah cukup untuk memulai kebiasaan.

Setelah beberapa minggu, baca kembali tulisanmu. Kamu akan terkejut melihat bagaimana cara berpikirmu berubah. Masalah yang dulu terasa besar mungkin sekarang terlihat biasa karena kamu sudah menemukan cara menghadapinya.

Jurnal juga membantu saat kamu berkonsultasi dengan Guru BK. Kamu datang bukan dengan kepala kosong, tetapi dengan catatan yang jelas. Percakapan menjadi lebih terarah dan solusi yang ditemukan lebih sesuai dengan keadaanmu yang sebenarnya.', '2026-10-11 09:00:00', NULL),
  (6, 6, 'Ahmad Fauzi, S.Pd.', 'Berbicara dengan Orang Tua tentang Cita-cita', 'artikel-06.jpg', 'Tidak semua siswa merasa mudah membicarakan cita-cita dengan orang tua. Kadang ada rasa takut dianggap belum serius, atau khawatir pilihanmu tidak disetujui. Padahal, percakapan yang terbuka bisa mencegah salah paham bertahun-tahun.

Pilih waktu yang tenang, bukan saat orang tua baru pulang kerja atau sedang sibuk. Mulailah dengan menceritakan apa yang kamu temukan tentang dirimu, bukan langsung meminta persetujuan atas satu pilihan.

Bawa bukti yang mendukung. Misalnya informasi jurusan, prospek kerja, atau pengalaman mengikuti kegiatan yang berkaitan. Orang tua lebih mudah percaya ketika melihat bahwa kamu sudah mencari tahu, bukan sekadar ikut teman.

Dengarkan juga kekhawatiran mereka. Sering kali keberatan orang tua muncul dari rasa khawatir tentang masa depanmu, bukan dari keinginan menghalangi. Jika belum ada kesepakatan, tidak apa-apa. Percakapan pertama cukup menjadi pembuka, dan kamu bisa melanjutkannya di lain waktu.', '2026-10-25 15:40:00', NULL),
  (7, 7, 'Dewi Anggraini, M.Pd.', 'Mengenal Dunia Kerja di Bidang Teknologi', 'artikel-07.jpg', 'Dunia teknologi sering terdengar seperti dunia yang hanya cocok untuk siswa yang pandai matematika. Kenyataannya, bidang ini sangat luas dan membutuhkan banyak jenis kemampuan, termasuk komunikasi, desain, dan kemampuan mengatur pekerjaan.

Ada banyak peran di dalamnya. Ada yang menulis program, ada yang merancang tampilan aplikasi, ada yang menguji kualitas produk, dan ada yang menghubungkan kebutuhan pengguna dengan tim teknis. Setiap peran punya tantangan dan cara belajar yang berbeda.

Yang paling penting bukan hafal bahasa pemrograman tertentu, tetapi kebiasaan berpikir runtut dan kemauan belajar terus-menerus. Teknologi berubah cepat, sehingga kemampuan menyesuaikan diri lebih berharga daripada sekadar nilai tinggi di satu mata pelajaran.

Bagi siswa yang tertarik, mulailah dari proyek kecil. Membuat situs sederhana, mengotomatiskan tugas sekolah, atau ikut komunitas belajar daring bisa menjadi langkah awal yang nyata. Pengalaman kecil ini jauh lebih berharga daripada hanya membaca berita tentang perusahaan teknologi besar.', '2026-10-06 08:30:00', NULL),
  (8, 7, 'Dewi Anggraini, M.Pd.', 'Peluang Karier di Sektor Kesehatan', 'artikel-08.jpg', 'Sektor kesehatan tidak hanya tentang dokter dan perawat. Di balik layanan yang kita terima, ada ahli gizi, analis laboratorium, radiografer, apoteker, terapis, dan banyak profesi lain yang saling melengkapi.

Setiap profesi punya jalur pendidikan dan tanggung jawab berbeda. Ada yang menempuh pendidikan sarjana lalu profesi, ada yang melalui pendidikan vokasi tiga atau empat tahun. Memahami jalur ini penting agar kamu tidak memilih berdasarkan nama profesi saja.

Selain kemampuan akademik, sektor ini menuntut ketelitian, kesabaran, dan kemampuan berkomunikasi dengan pasien. Nilai tinggi tidak cukup jika kamu tidak nyaman berhadapan dengan orang yang sedang kesulitan.

Mulailah dengan mencari informasi resmi tentang syarat masuk dan masa studi. Jika memungkinkan, ikuti kegiatan pengenalan kampus atau berbincang dengan tenaga kesehatan di sekitar tempat tinggalmu. Gambaran nyata dari lapangan akan membantumu menilai apakah bidang ini benar-benar cocok.', '2026-10-20 11:10:00', NULL),
  (9, 8, 'Hendra Wijaya, S.Pd.', 'Belajar Efektif ala Siswa Berprestasi', 'artikel-09.jpg', 'Siswa berprestasi biasanya bukan siswa yang belajar paling lama, melainkan siswa yang belajar dengan cara paling teratur. Mereka tahu kapan harus fokus, kapan harus beristirahat, dan bagian mana yang perlu diulang.

Salah satu kebiasaan penting adalah belajar aktif. Membaca ulang catatan terasa nyaman, tetapi kurang menguji pemahaman. Cobalah menutup buku lalu menjelaskan materi dengan bahasamu sendiri, atau mengerjakan soal tanpa melihat contoh.

Buat jadwal yang masuk akal. Belajar empat puluh lima menit lalu istirahat sepuluh menit sering lebih efektif daripada duduk tiga jam sambil terus membuka ponsel. Kualitas perhatian lebih menentukan daripada jumlah waktu.

Jangan lupakan tidur cukup dan olahraga ringan. Keduanya berpengaruh langsung pada kemampuan mengingat dan berkonsentrasi. Prestasi yang dibangun dengan mengorbankan kesehatan biasanya tidak bertahan lama.', '2026-10-09 14:00:00', NULL),
  (10, 8, 'Hendra Wijaya, S.Pd.', 'Mengelola Waktu antara Sekolah dan Les', 'artikel-10.jpg', 'Jadwal sekolah yang padat ditambah les dan kegiatan ekstrakurikuler bisa membuat siswa kehabisan waktu. Tanpa pengelolaan yang baik, semua kegiatan berjalan setengah-setengah dan istirahat pun hilang.

Langkah pertama adalah mencatat semua kegiatan dalam satu minggu. Setelah terlihat di kertas, biasanya akan tampak celah waktu yang selama ini terlewat, misalnya perjalanan atau waktu menunggu.

Tentukan prioritas dengan jujur. Tidak semua kegiatan harus dipertahankan. Jika ada les yang tidak lagi membantu, tidak ada salahnya mengurangi. Mengurangi satu kegiatan bisa menyelamatkan kualitas belajar dan kesehatanmu.

Gunakan waktu kecil dengan bijak. Lima belas menit untuk mengulang kosakata atau membaca satu halaman tetap berarti jika dilakukan rutin. Yang penting bukan menunggu waktu luang besar, tetapi memanfaatkan waktu kecil yang tersedia setiap hari.', '2026-10-23 16:25:00', NULL),
  (11, 9, 'Sri Wahyuni, M.Pd.', 'Kiat Memilih Sekolah Lanjutan yang Tepat', 'artikel-11.jpg', 'Memilih sekolah lanjutan setelah lulus bukan hanya soal peringkat atau jarak dari rumah. Sekolah yang tepat adalah sekolah yang mendukung cara belajarmu dan membuka jalan menuju tujuan yang kamu inginkan.

Perhatikan program yang ditawarkan. Sebuah sekolah mungkin terkenal, tetapi belum tentu memiliki jurusan atau kegiatan yang sesuai dengan minatmu. Kumpulkan informasi tentang kurikulum, fasilitas, dan kegiatan pendukung.

Kunjungi sekolah jika memungkinkan. Suasana kelas, cara guru menyapa siswa, dan kebersihan lingkungan memberi gambaran yang tidak didapat dari situs web. Bila tidak bisa datang, tanyakan kepada alumni atau siswa yang sekarang belajar di sana.

Diskusikan pilihanmu dengan keluarga, tetapi jangan menyerahkan keputusan sepenuhnya kepada orang lain. Kamu yang akan menjalani hari-hari di sekolah tersebut. Keputusan yang diambil dengan pemahaman penuh biasanya lebih mudah dijalani, termasuk ketika muncul kesulitan di tengah jalan.', '2026-10-13 09:50:00', NULL),
  (12, 9, 'Sri Wahyuni, M.Pd.', 'Membangun Kepercayaan Diri di Depan Publik', 'artikel-12.jpg', 'Tampil di depan kelas atau forum sekolah membuat banyak siswa gugup. Jantung berdebar dan suara bergetar adalah reaksi alami, bukan tanda bahwa kamu tidak mampu. Kepercayaan diri bukan bawaan, melainkan hasil latihan.

Mulailah dari persiapan yang sederhana. Tulis tiga poin utama yang ingin disampaikan, lalu latih dengan suara keras di kamar. Mengucapkan materi dengan suara sendiri membantu mulut dan pikiran terbiasa dengan alurnya.

Saat tampil, fokuslah pada pesan yang ingin disampaikan, bukan pada penilaian orang lain. Melihat satu atau dua wajah yang ramah di antara penonton juga membantu menenangkan pikiran.

Setelah selesai, catat apa yang berjalan baik dan satu hal yang bisa diperbaiki. Jangan menunggu sempurna untuk berani tampil lagi. Setiap kesempatan berbicara adalah latihan yang membuatmu sedikit lebih tenang daripada sebelumnya.', '2026-10-27 10:05:00', NULL);

-- =====================================================================
-- SELESAI
-- =====================================================================
