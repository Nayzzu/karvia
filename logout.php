<?php
define('KARVIA_APP', true);
require_once __DIR__ . '/includes/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode permintaan tidak diizinkan.');
}

csrf_wajib();
auth_logout();

// Mulai session baru untuk menyampaikan pesan setelah data session lama dihapus.
session_name(NAMA_SESSION);
session_start();
flash_set('sukses', 'Anda berhasil keluar.');
redirect('login.php');
