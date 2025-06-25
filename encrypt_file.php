<?php
require_once 'config.php';
require_once 'lib/RSA.php';
require_once 'lib/AES.php'; // Gunakan AES class jika Anda membuatnya

$message = '';
$error = '';
$downloadLink = '';

try {
    $rsa = new RSA(KEY_PATH . 'public.pem', KEY_PATH . 'private.pem');
    if (!$rsa->getPublicKey()) {
        $error = "Kunci publik tidak ditemukan. Silakan buat kunci terlebih dahulu.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
        if (isset($_FILES['file_to_encrypt']) && $_FILES['file_to_encrypt']['error'] === UPLOAD_ERR_OK) {
            $originalFileName = basename($_FILES['file_to_encrypt']['name']);
            $tempFilePath = $_FILES['file_to_encrypt']['tmp_name'];
            $fileContent = file_get_contents($tempFilePath);

            // 1. Generate AES Key and IV
            $aesKey = AES::generateKey();
            $aesIV = AES::generateIV();

            // 2. Encrypt File Content with AES
            $encryptedFileContent = AES::encrypt($fileContent, $aesKey, $aesIV);

            // 3. Encrypt AES Key and IV with RSA Public Key
            $encryptedAesKey = $rsa->encrypt($aesKey);
            $encryptedAesIV = $rsa->encrypt($aesIV);

            // 4. Combine encrypted AES key, IV, and encrypted file content
            // Format: RSA_encrypted_AES_Key::RSA_encrypted_AES_IV::AES_encrypted_File_Content
            $combinedEncryptedData = $encryptedAesKey . '::' . $encryptedAesIV . '::' . $encryptedFileContent;

            $encryptedFileName = $originalFileName . '.encrypted';
            $outputPath = UPLOAD_PATH_ENCRYPTED . $encryptedFileName;

            if (file_put_contents($outputPath, $combinedEncryptedData)) {
                $message = "File berhasil dienkripsi!";
                $downloadLink = "download_file.php?type=encrypted&file=" . urlencode($encryptedFileName);
            } else {
                $error = "Gagal menyimpan file terenkripsi.";
            }
        } else if (isset($_FILES['file_to_encrypt']) && $_FILES['file_to_encrypt']['error'] !== UPLOAD_ERR_NO_FILE) {
            $error = "Terjadi kesalahan saat mengunggah file: " . $_FILES['file_to_encrypt']['error'];
        } else {
            // No file uploaded on initial load or if user didn't select one
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
    <title>Enkripsi File</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Enkripsi File</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="file_to_encrypt">Pilih File:</label>
            <input type="file" id="file_to_encrypt" name="file_to_encrypt" required>
            <button type="submit">Enkripsi File</button>
        </form>

        <?php if ($downloadLink): ?>
            <p>File terenkripsi tersedia: <a href="<?php echo $downloadLink; ?>" download><?php echo htmlspecialchars(basename($downloadLink)); ?></a></p>
        <?php endif; ?>

        <p><a href="index.php">Kembali ke Beranda</a></p>
    </div>
</body>
</html>