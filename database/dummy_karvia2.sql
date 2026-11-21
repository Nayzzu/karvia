-- =====================================================================
-- KARVIA — dummy_karvia2.sql
-- Data tambahan 3 sekolah Bandar Lampung, tanpa menghapus data yang sudah ada.
-- Isi: 3 sekolah, masing-masing 1 Admin, 3 Guru BK, 30 siswa, 1 artikel.
-- Semua nama, NPSN, akun, alamat, dan konten contoh bersifat fiktif.
-- Jalankan sesudah database/database.sql dan database/dummy_karvia.sql.
-- Idempotent: sekolah/username tidak diduplikasi; akun fixture diselaraskan namanya.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';
USE `karvia_db`;

START TRANSACTION;

INSERT INTO `sekolah` (`id`, `nama`, `npsn`, `jenjang`, `alamat`, `kota`, `provinsi`, `status`, `dibuat_pada`, `diperbarui_pada`)
SELECT x.id, x.nama, x.npsn, x.jenjang, x.alamat, x.kota, x.provinsi, 'aktif', NOW(), NULL
FROM (
  SELECT 3 AS id, 'SMAN 1 Bandar Lampung' AS nama, '18880003' AS npsn, 'SMA' AS jenjang, 'Jl. Jenderal Sudirman, Bandar Lampung' AS alamat, 'Bandar Lampung' AS kota, 'Lampung' AS provinsi
  UNION ALL SELECT 4, 'SMAN 9 Bandar Lampung', '18880004', 'SMA', 'Jl. Panglima Polim, Bandar Lampung', 'Bandar Lampung', 'Lampung'
  UNION ALL SELECT 5, 'MAN 1 Bandar Lampung', '18880005', 'MA', 'Jl. Letkol H. Endro Suratmin, Bandar Lampung', 'Bandar Lampung', 'Lampung'
) x
WHERE NOT EXISTS (SELECT 1 FROM sekolah s WHERE s.id = x.id OR s.npsn = x.npsn);

-- Password demo: admin / guru / siswa. Hash kompatibel dengan dummy_karvia.sql.
INSERT INTO `pengguna` (`sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`, `no_telepon`, `dibuat_oleh`, `dibuat_pada`)
SELECT x.sekolah_id, x.nama_lengkap, x.username, x.password_hash, x.role, 'aktif', x.telepon,
       (SELECT id FROM pengguna WHERE username = 'admin' LIMIT 1), NOW()
FROM (
  SELECT 3 AS sekolah_id, 'Rafi Ahmad Pratama' AS nama_lengkap, 'admin3' AS username, '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.' AS password_hash, 'admin' AS role, '081200000103' AS telepon
  UNION ALL SELECT 4, 'Nia Kurniawati', 'admin4', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', '081200000104'
  UNION ALL SELECT 5, 'Fajar Maulana', 'admin5', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', '081200000105'
  UNION ALL SELECT 3, 'Rahmat Hidayat, S.Pd.', 'guru7', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000007'
  UNION ALL SELECT 3, 'Maya Puspitasari, M.Pd.', 'guru8', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000008'
  UNION ALL SELECT 3, 'Andi Saputra, S.Pd.', 'guru9', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000009'
  UNION ALL SELECT 4, 'Sinta Lestari, M.Pd.', 'guru10', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000010'
  UNION ALL SELECT 4, 'Deni Kurniawan, S.Pd.', 'guru11', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000011'
  UNION ALL SELECT 4, 'Lina Marlina, M.Pd.', 'guru12', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000012'
  UNION ALL SELECT 5, 'Dewi Anggraini, S.Pd.', 'guru13', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000013'
  UNION ALL SELECT 5, 'Bambang Setiawan, M.Pd.', 'guru14', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000014'
  UNION ALL SELECT 5, 'Fitri Handayani, S.Pd.', 'guru15', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000015'
) x
WHERE EXISTS (SELECT 1 FROM sekolah s WHERE s.id = x.sekolah_id)
  AND (NOT EXISTS (SELECT 1 FROM pengguna p WHERE p.username = x.username)
       OR EXISTS (SELECT 1 FROM pengguna p WHERE p.username = x.username AND p.sekolah_id = x.sekolah_id AND p.role = x.role))
ON DUPLICATE KEY UPDATE nama_lengkap = VALUES(nama_lengkap);

-- 30 siswa per sekolah; nama fiktif natural, username demo berurutan.
INSERT INTO `pengguna` (`sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`, `nisn`, `kelas`, `jurusan`, `no_telepon`, `dibuat_oleh`, `dibuat_pada`)
SELECT x.sekolah_id, x.nama_lengkap, CONCAT('siswa', x.nomor),
       '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif',
       CONCAT('1088', LPAD(x.nomor, 6, '0')),
       ELT(((x.nomor - 1) MOD 3) + 1, 'X', 'XI', 'XII'),
       ELT(((x.nomor - 1) MOD 3) + 1, 'MIPA', 'IPS', 'Bahasa'),
       CONCAT('0814', LPAD(x.nomor, 8, '0')),
       (SELECT id FROM pengguna WHERE username = 'admin' LIMIT 1), NOW()
FROM (
  SELECT 3 AS sekolah_id, 61 AS nomor, 'Aditya Pratama' AS nama_lengkap UNION ALL SELECT 3,62,'Aulia Rahmawati' UNION ALL SELECT 3,63,'Bagas Saputra' UNION ALL SELECT 3,64,'Citra Maharani' UNION ALL SELECT 3,65,'Daffa Ramadhan' UNION ALL SELECT 3,66,'Dinda Lestari' UNION ALL SELECT 3,67,'Ega Firmansyah' UNION ALL SELECT 3,68,'Fani Oktaviani' UNION ALL SELECT 3,69,'Farhan Hidayat' UNION ALL SELECT 3,70,'Gita Permatasari' UNION ALL SELECT 3,71,'Hana Safitri' UNION ALL SELECT 3,72,'Ilham Maulana' UNION ALL SELECT 3,73,'Intan Puspita' UNION ALL SELECT 3,74,'Jihan Anggraini' UNION ALL SELECT 3,75,'Kevin Kurniawan' UNION ALL SELECT 3,76,'Laila Fitriani' UNION ALL SELECT 3,77,'M. Rizky Setiawan' UNION ALL SELECT 3,78,'Nadia Aprilia' UNION ALL SELECT 3,79,'Naufal Akbar' UNION ALL SELECT 3,80,'Nisa Amelia' UNION ALL SELECT 3,81,'Putra Wijaya' UNION ALL SELECT 3,82,'Rania Salsabila' UNION ALL SELECT 3,83,'Reza Firmansyah' UNION ALL SELECT 3,84,'Rizka Amalia' UNION ALL SELECT 3,85,'Salsabila Putri' UNION ALL SELECT 3,86,'Tegar Saputra' UNION ALL SELECT 3,87,'Tiara Pertiwi' UNION ALL SELECT 3,88,'Vino Pratama' UNION ALL SELECT 3,89,'Yuni Kartika' UNION ALL SELECT 3,90,'Zahra Nurfadila'
  UNION ALL SELECT 4,91,'Abdul Aziz' UNION ALL SELECT 4,92,'Adelia Putri' UNION ALL SELECT 4,93,'Alif Fadillah' UNION ALL SELECT 4,94,'Anisa Rahma' UNION ALL SELECT 4,95,'Ardiansyah Putra' UNION ALL SELECT 4,96,'Ayu Wulandari' UNION ALL SELECT 4,97,'Bintang Saputra' UNION ALL SELECT 4,98,'Dewi Sartika' UNION ALL SELECT 4,99,'Dimas Prakoso' UNION ALL SELECT 4,100,'Elsa Novitasari' UNION ALL SELECT 4,101,'Fajar Nugroho' UNION ALL SELECT 4,102,'Fitria Ningsih' UNION ALL SELECT 4,103,'Hafiz Ramadhan' UNION ALL SELECT 4,104,'Indah Permata' UNION ALL SELECT 4,105,'Lukman Hakim' UNION ALL SELECT 4,106,'Mutiara Zahra' UNION ALL SELECT 4,107,'Nanda Pratama' UNION ALL SELECT 4,108,'Niken Larasati' UNION ALL SELECT 4,109,'Rafi Alfarizi' UNION ALL SELECT 4,110,'Rara Apriliani' UNION ALL SELECT 4,111,'Rendi Kurniawan' UNION ALL SELECT 4,112,'Siti Aisyah' UNION ALL SELECT 4,113,'Syifa Khairunnisa' UNION ALL SELECT 4,114,'Wahyu Setiawan' UNION ALL SELECT 4,115,'Wulan Puspitasari' UNION ALL SELECT 4,116,'Yogi Pratama' UNION ALL SELECT 4,117,'Zaki Firmansyah' UNION ALL SELECT 4,118,'Zaskia Amelia' UNION ALL SELECT 4,119,'Dio Saputra' UNION ALL SELECT 4,120,'Nabila Putri'
  UNION ALL SELECT 5,121,'Achmad Fauzan' UNION ALL SELECT 5,122,'Aisyah Nabila' UNION ALL SELECT 5,123,'Andika Saputra' UNION ALL SELECT 5,124,'Annisa Fitri' UNION ALL SELECT 5,125,'Ari Wibowo' UNION ALL SELECT 5,126,'Azizah Rahmawati' UNION ALL SELECT 5,127,'Bima Prasetyo' UNION ALL SELECT 5,128,'Della Safira' UNION ALL SELECT 5,129,'Erlangga Putra' UNION ALL SELECT 5,130,'Fadli Akbar' UNION ALL SELECT 5,131,'Fauzia Nurfadila' UNION ALL SELECT 5,132,'Gilang Ramadhan' UNION ALL SELECT 5,133,'Hani Mulyani' UNION ALL SELECT 5,134,'Iqbal Maulana' UNION ALL SELECT 5,135,'Jasmine Azzahra' UNION ALL SELECT 5,136,'Khalid Hidayat' UNION ALL SELECT 5,137,'Maya Sari' UNION ALL SELECT 5,138,'Melati Puspita' UNION ALL SELECT 5,139,'Muhammad Farrel' UNION ALL SELECT 5,140,'Naufal Rizki' UNION ALL SELECT 5,141,'Nazwa Putri' UNION ALL SELECT 5,142,'Rangga Saputra' UNION ALL SELECT 5,143,'Rania Putri' UNION ALL SELECT 5,144,'Rizal Firmansyah' UNION ALL SELECT 5,145,'Salma Aulia' UNION ALL SELECT 5,146,'Taufik Hidayat' UNION ALL SELECT 5,147,'Vania Kurnia' UNION ALL SELECT 5,148,'Yasmin Safitri' UNION ALL SELECT 5,149,'Zahra Azzahra' UNION ALL SELECT 5,150,'Zulfa Rahma'
) x
WHERE EXISTS (SELECT 1 FROM sekolah s WHERE s.id = x.sekolah_id)
  AND (NOT EXISTS (SELECT 1 FROM pengguna p WHERE p.username = CONCAT('siswa', x.nomor))
       OR EXISTS (SELECT 1 FROM pengguna p WHERE p.username = CONCAT('siswa', x.nomor) AND p.sekolah_id = x.sekolah_id AND p.role = 'siswa'))
ON DUPLICATE KEY UPDATE nama_lengkap = VALUES(nama_lengkap);

-- Satu artikel untuk sekolah 1 (SMAN 1 Bandar Lampung), gunakan gambar yang sudah ada.
INSERT INTO `artikel` (`guru_id`, `nama_penulis`, `judul`, `gambar`, `isi`, `diterbitkan_pada`, `diperbarui_pada`)
SELECT p.id, p.nama_lengkap, 'Mengenali Potensi Diri untuk Merencanakan Masa Depan', 'artikel-01.jpg',
       'Setiap siswa memiliki pengalaman, minat, dan kekuatan yang berbeda. Luangkan waktu untuk mencatat kegiatan yang membuatmu tertarik, pelajaran yang ingin kamu dalami, serta kemampuan yang ingin dikembangkan. Informasi sederhana ini dapat menjadi bahan awal untuk mengeksplorasi pilihan karier. Tidak perlu menentukan satu profesi sekarang. Cari informasi dari sumber tepercaya dan bicarakan langkah berikutnya bersama Guru BK atau orang dewasa yang kamu percaya.',
       '2026-11-01 09:00:00', NULL
FROM pengguna p
WHERE p.username = 'guru7' AND p.role = 'guru_bk'
  AND NOT EXISTS (SELECT 1 FROM artikel a WHERE a.guru_id = p.id AND a.judul = 'Mengenali Potensi Diri untuk Merencanakan Masa Depan');

COMMIT;

-- Akun tambahan: admin3–admin5 / admin; guru7–guru15 / guru; siswa61–siswa150 / siswa. Nama lengkap natural, semua identitas fiktif.
-- NPSN dan identitas pada file ini adalah data dummy, bukan data resmi sekolah.