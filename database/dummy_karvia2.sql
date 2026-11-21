-- =====================================================================
-- KARVIA — dummy_karvia2.sql
-- Data tambahan 3 sekolah Bandar Lampung, tanpa menghapus data yang sudah ada.
-- Isi: 3 sekolah, masing-masing 1 Admin, 3 Guru BK, 30 siswa, 1 artikel.
-- Semua nama, NPSN, akun, alamat, dan konten contoh bersifat fiktif.
-- Jalankan sesudah database/database.sql dan database/dummy_karvia.sql.
-- Idempotent: baris sekolah dan akun ditambahkan hanya jika username/NPSN belum ada.
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
  SELECT 3 AS sekolah_id, 'Admin SMAN 1 Bandar Lampung' AS nama_lengkap, 'admin3' AS username, '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.' AS password_hash, 'admin' AS role, '081200000103' AS telepon
  UNION ALL SELECT 4, 'Admin SMAN 9 Bandar Lampung', 'admin4', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', '081200000104'
  UNION ALL SELECT 5, 'Admin MAN 1 Bandar Lampung', 'admin5', '$2y$10$IfKV3z/F8qTjDhWfHEqF.ORgBFPUY0bO5YNVWaYun8vRKtSp7Vki.', 'admin', '081200000105'
  UNION ALL SELECT 3, 'Budi Santoso, S.Pd.', 'guru7', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000007'
  UNION ALL SELECT 3, 'Rina Kartika Sari, M.Pd.', 'guru8', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000008'
  UNION ALL SELECT 3, 'Ahmad Fauzi, S.Pd.', 'guru9', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000009'
  UNION ALL SELECT 4, 'Dewi Anggraini, M.Pd.', 'guru10', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000010'
  UNION ALL SELECT 4, 'Hendra Wijaya, S.Pd.', 'guru11', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000011'
  UNION ALL SELECT 4, 'Sri Wahyuni, M.Pd.', 'guru12', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000012'
  UNION ALL SELECT 5, 'Nadia Putri, S.Pd.', 'guru13', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000013'
  UNION ALL SELECT 5, 'Dimas Pratama, S.Pd.', 'guru14', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000014'
  UNION ALL SELECT 5, 'Siti Rahma, M.Pd.', 'guru15', '$2y$10$LFFR9nVYh/wRJjVWcrDEEeLE7KRUDIy0D6DXmWqIDqK1Q.cUAgNFW', 'guru_bk', '081300000015'
) x
WHERE EXISTS (SELECT 1 FROM sekolah s WHERE s.id = x.sekolah_id)
  AND NOT EXISTS (SELECT 1 FROM pengguna p WHERE p.username = x.username);

-- 30 siswa per sekolah: siswa61–90, 91–120, 121–150.
INSERT INTO `pengguna` (`sekolah_id`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`, `nisn`, `kelas`, `jurusan`, `no_telepon`, `dibuat_oleh`, `dibuat_pada`)
SELECT x.sekolah_id, CONCAT('Siswa Demo ', LPAD(x.nomor, 3, '0')), CONCAT('siswa', x.nomor),
       '$2y$10$dTwjeeo8kcGBBW14bU5jL.WAWUOUTxaVYkLfGMQDy.a/fwtYw4md6', 'siswa', 'aktif',
       CONCAT('1088', LPAD(x.nomor, 6, '0')),
       ELT(((x.nomor - 1) MOD 3) + 1, 'X', 'XI', 'XII'),
       ELT(((x.nomor - 1) MOD 3) + 1, 'MIPA', 'IPS', 'Bahasa'),
       CONCAT('0814', LPAD(x.nomor, 8, '0')),
       (SELECT id FROM pengguna WHERE username = 'admin' LIMIT 1), NOW()
FROM (
  SELECT 3 AS sekolah_id, 61 AS nomor UNION ALL SELECT 3,62 UNION ALL SELECT 3,63 UNION ALL SELECT 3,64 UNION ALL SELECT 3,65 UNION ALL SELECT 3,66 UNION ALL SELECT 3,67 UNION ALL SELECT 3,68 UNION ALL SELECT 3,69 UNION ALL SELECT 3,70 UNION ALL SELECT 3,71 UNION ALL SELECT 3,72 UNION ALL SELECT 3,73 UNION ALL SELECT 3,74 UNION ALL SELECT 3,75 UNION ALL SELECT 3,76 UNION ALL SELECT 3,77 UNION ALL SELECT 3,78 UNION ALL SELECT 3,79 UNION ALL SELECT 3,80 UNION ALL SELECT 3,81 UNION ALL SELECT 3,82 UNION ALL SELECT 3,83 UNION ALL SELECT 3,84 UNION ALL SELECT 3,85 UNION ALL SELECT 3,86 UNION ALL SELECT 3,87 UNION ALL SELECT 3,88 UNION ALL SELECT 3,89 UNION ALL SELECT 3,90
  UNION ALL SELECT 4,91 UNION ALL SELECT 4,92 UNION ALL SELECT 4,93 UNION ALL SELECT 4,94 UNION ALL SELECT 4,95 UNION ALL SELECT 4,96 UNION ALL SELECT 4,97 UNION ALL SELECT 4,98 UNION ALL SELECT 4,99 UNION ALL SELECT 4,100 UNION ALL SELECT 4,101 UNION ALL SELECT 4,102 UNION ALL SELECT 4,103 UNION ALL SELECT 4,104 UNION ALL SELECT 4,105 UNION ALL SELECT 4,106 UNION ALL SELECT 4,107 UNION ALL SELECT 4,108 UNION ALL SELECT 4,109 UNION ALL SELECT 4,110 UNION ALL SELECT 4,111 UNION ALL SELECT 4,112 UNION ALL SELECT 4,113 UNION ALL SELECT 4,114 UNION ALL SELECT 4,115 UNION ALL SELECT 4,116 UNION ALL SELECT 4,117 UNION ALL SELECT 4,118 UNION ALL SELECT 4,119 UNION ALL SELECT 4,120
  UNION ALL SELECT 5,121 UNION ALL SELECT 5,122 UNION ALL SELECT 5,123 UNION ALL SELECT 5,124 UNION ALL SELECT 5,125 UNION ALL SELECT 5,126 UNION ALL SELECT 5,127 UNION ALL SELECT 5,128 UNION ALL SELECT 5,129 UNION ALL SELECT 5,130 UNION ALL SELECT 5,131 UNION ALL SELECT 5,132 UNION ALL SELECT 5,133 UNION ALL SELECT 5,134 UNION ALL SELECT 5,135 UNION ALL SELECT 5,136 UNION ALL SELECT 5,137 UNION ALL SELECT 5,138 UNION ALL SELECT 5,139 UNION ALL SELECT 5,140 UNION ALL SELECT 5,141 UNION ALL SELECT 5,142 UNION ALL SELECT 5,143 UNION ALL SELECT 5,144 UNION ALL SELECT 5,145 UNION ALL SELECT 5,146 UNION ALL SELECT 5,147 UNION ALL SELECT 5,148 UNION ALL SELECT 5,149 UNION ALL SELECT 5,150
) x
WHERE EXISTS (SELECT 1 FROM sekolah s WHERE s.id = x.sekolah_id)
  AND NOT EXISTS (SELECT 1 FROM pengguna p WHERE p.username = CONCAT('siswa', x.nomor));

-- Satu artikel untuk sekolah 1 (SMAN 1 Bandar Lampung), gunakan gambar yang sudah ada.
INSERT INTO `artikel` (`guru_id`, `nama_penulis`, `judul`, `gambar`, `isi`, `diterbitkan_pada`, `diperbarui_pada`)
SELECT p.id, p.nama_lengkap, 'Mengenali Potensi Diri untuk Merencanakan Masa Depan', 'artikel-01.jpg',
       'Setiap siswa memiliki pengalaman, minat, dan kekuatan yang berbeda. Luangkan waktu untuk mencatat kegiatan yang membuatmu tertarik, pelajaran yang ingin kamu dalami, serta kemampuan yang ingin dikembangkan. Informasi sederhana ini dapat menjadi bahan awal untuk mengeksplorasi pilihan karier. Tidak perlu menentukan satu profesi sekarang. Cari informasi dari sumber tepercaya dan bicarakan langkah berikutnya bersama Guru BK atau orang dewasa yang kamu percaya.',
       '2026-11-01 09:00:00', NULL
FROM pengguna p
WHERE p.username = 'guru7' AND p.role = 'guru_bk'
  AND NOT EXISTS (SELECT 1 FROM artikel a WHERE a.guru_id = p.id AND a.judul = 'Mengenali Potensi Diri untuk Merencanakan Masa Depan');

COMMIT;

-- Akun tambahan: admin3–admin5 / admin; guru7–guru15 / guru; siswa61–siswa150 / siswa.
-- NPSN dan identitas pada file ini adalah data dummy, bukan data resmi sekolah.