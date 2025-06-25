<?php
// config.php

define('KEY_PATH', __DIR__ . '/keys/');
define('UPLOAD_PATH_ENCRYPTED', __DIR__ . '/uploads/encrypted/');
define('UPLOAD_PATH_DECRYPTED', __DIR__ . '/uploads/decrypted/');

// Pastikan direktori kunci ada
if (!is_dir(KEY_PATH)) {
    mkdir(KEY_PATH, 0755, true);
}
// Pastikan direktori upload ada
if (!is_dir(UPLOAD_PATH_ENCRYPTED)) {
    mkdir(UPLOAD_PATH_ENCRYPTED, 0755, true);
}
if (!is_dir(UPLOAD_PATH_DECRYPTED)) {
    mkdir(UPLOAD_PATH_DECRYPTED, 0755, true);
}

// Set error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set timezone (penting untuk timestamp)
date_default_timezone_set('Asia/Makassar');
?>