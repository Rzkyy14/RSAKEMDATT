<?php
require_once 'config.php';
require_once 'lib/RSA.php';
require_once 'lib/AES.php'; // Jika Anda menggunakan kelas AES terpisah

$message = '';
$error = '';

try {
    $rsa = new RSA(KEY_PATH . 'public.pem', KEY_PATH . 'private.pem');
} catch (Exception $e) {
    $error = "Error loading RSA keys: " . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'generate_keys':
                header('Location: generate_keys.php');
                exit();
            case 'encrypt_text':
                header('Location: encrypt_text.php');
                exit();
            case 'decrypt_text':
                header('Location: decrypt_text.php');
                exit();
            case 'encrypt_file':
                header('Location: encrypt_file.php');
                exit();
            case 'decrypt_file':
                header('Location: decrypt_file.php');
                exit();
            default:
                $error = "Invalid action.";
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Implementasi Algoritma RSA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Implementasi Algoritma RSA</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>Manajemen Kunci RSA</h2>
            <p>Pastikan Anda telah memiliki pasangan kunci publik dan privat.</p>
            <form action="" method="POST">
                <input type="hidden" name="action" value="generate_keys">
                <button type="submit">Buat Kunci Baru</button>
            </form>
            <?php if ($rsa->getPublicKey() && $rsa->getPrivateKey()): ?>
                <div class="key-display">
                    <h3>Kunci Publik:</h3>
                    <textarea readonly><?php echo htmlspecialchars($rsa->getPublicKey()); ?></textarea>
                    <h3>Kunci Privat:</h3>
                    <textarea readonly><?php echo htmlspecialchars($rsa->getPrivateKey()); ?></textarea>
                </div>
            <?php else: ?>
                <p>Kunci belum dibuat atau tidak ditemukan. Silakan buat kunci baru.</p>
            <?php endif; ?>
        </div> 

        <div class="section">
            <h2>Enkripsi & Dekripsi Teks</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="encrypt_text">
                <button type="submit">Enkripsi Teks</button>
            </form>
            <form action="" method="POST">
                <input type="hidden" name="action" value="decrypt_text">
                <button type="submit">Dekripsi Teks</button>
            </form>
        </div>

        <div class="section">
            <h2>Enkripsi & Dekripsi File</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="encrypt_file">
                <button type="submit">Enkripsi File</button>
            </form>
            <form action="" method="POST">
                <input type="hidden" name="action" value="decrypt_file">
                <button type="submit">Dekripsi File</button>
            </form>
        </div>
    </div>
</body>
</html>