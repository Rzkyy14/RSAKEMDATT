<?php
require_once 'config.php';
require_once 'lib/RSA.php';

$encryptedText = '';
$originalText = '';
$error = '';
$message = '';

try {
    $rsa = new RSA(KEY_PATH . 'public.pem', KEY_PATH . 'private.pem');
    if (!$rsa->getPublicKey()) {
        $error = "Kunci publik tidak ditemukan. Silakan buat kunci terlebih dahulu.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
        $originalText = $_POST['plaintext'] ?? '';
        if (empty($originalText)) {
            $error = "Teks yang akan dienkripsi tidak boleh kosong.";
        } else {
            $encryptedText = $rsa->encrypt($originalText);
            $message = "Teks berhasil dienkripsi!";
        }
    }
} catch (Exception $e) {
    $error = "Terjadi kesalahan: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enkripsi Teks</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Enkripsi Teks</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="plaintext">Teks Asli:</label>
            <textarea id="plaintext" name="plaintext" rows="5" required><?php echo htmlspecialchars($originalText); ?></textarea>
            <button type="submit">Enkripsi</button>
        </form>

        <?php if ($encryptedText): ?>
            <h2>Teks Terenkripsi:</h2>
            <textarea readonly rows="5"><?php echo htmlspecialchars($encryptedText); ?></textarea>
            <p>Panjang teks terenkripsi: <?php echo strlen($encryptedText); ?> byte</p>
        <?php endif; ?>

        <p><a href="index.php">Kembali ke Beranda</a></p>
    </div>
</body>
</html>