<?php
require_once 'config.php';
require_once 'lib/RSA.php';

$decryptedText = '';
$encryptedText = '';
$error = '';
$message = '';

try {
    $rsa = new RSA(KEY_PATH . 'public.pem', KEY_PATH . 'private.pem');
    if (!$rsa->getPrivateKey()) {
        $error = "Kunci privat tidak ditemukan. Silakan buat kunci terlebih dahulu.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
        $encryptedText = $_POST['encryptedtext'] ?? '';
        if (empty($encryptedText)) {
            $error = "Teks yang akan didekripsi tidak boleh kosong.";
        } else {
            $decryptedText = $rsa->decrypt($encryptedText);
            $message = "Teks berhasil didekripsi!";
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
    <title>Dekripsi Teks</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Dekripsi Teks</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="encryptedtext">Teks Terenkripsi:</label>
            <textarea id="encryptedtext" name="encryptedtext" rows="5" required><?php echo htmlspecialchars($encryptedText); ?></textarea>
            <button type="submit">Dekripsi</button>
        </form>

        <?php if ($decryptedText): ?>
            <h2>Teks Terdekripsi:</h2>
            <textarea readonly rows="5"><?php echo htmlspecialchars($decryptedText); ?></textarea>
        <?php endif; ?>

        <p><a href="index.php">Kembali ke Beranda</a></p>
    </div>
</body>
</html>