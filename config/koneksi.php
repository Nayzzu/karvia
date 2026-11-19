<?php
defined('KARVIA_APP') or exit('Akses langsung tidak diizinkan.');

// Konfigurasi lokal KARVIA. Sesuaikan BASE_URL jika folder aplikasi berubah.
const BASE_URL = '/karvia';
const NAMA_APLIKASI = 'KARVIA';
const ZONA_WAKTU = 'Asia/Jakarta';
const NAMA_SESSION = 'karvia_session';
const MODE_DEBUG = false;
const DB_HOST = '127.0.0.1';
const DB_NAMA = 'karvia_db';
const DB_USER = 'root';
const DB_PASSWORD = '';
const BATAS_SESSION_DETIK = 7200;

date_default_timezone_set(ZONA_WAKTU);
