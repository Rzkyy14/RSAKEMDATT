<?php
require_once 'config.php';
require_once 'lib/RSA.php';

$message = '';
$error = '';

try {
    $rsa = new RSA();
    $keys = $rsa->generateKeys();
    $rsa->saveKeys(KEY_PATH . 'public.pem', KEY_PATH . 'private.pem');
    $message = "Pasangan kunci RSA berhasil dibuat dan disimpan!";
} catch (Exception $e) {
    $error = "Gagal membuat kunci: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kunci RSA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Buat Kunci RSA</h1>
        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
            <p>Kunci publik dan privat Anda telah disimpan di folder `keys/`.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <p><a href="index.php">Kembali ke Beranda</a></p>
    </div>
</body>
</html>