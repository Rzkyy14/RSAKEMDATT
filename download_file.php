<?php
require_once 'config.php';

if (isset($_GET['type']) && isset($_GET['file'])) {
    $type = $_GET['type'];
    $fileName = basename($_GET['file']); // Sanitize filename

    $filePath = '';
    if ($type === 'encrypted') {
        $filePath = UPLOAD_PATH_ENCRYPTED . $fileName;
    } elseif ($type === 'decrypted') {
        $filePath = UPLOAD_PATH_DECRYPTED . $fileName;
    }

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "Parameter tidak lengkap.";
}
?>