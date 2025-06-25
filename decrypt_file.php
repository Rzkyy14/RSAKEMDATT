<?php
require_once 'config.php';
require_once 'lib/RSA.php';
require_once 'lib/AES.php';

$message = '';
$error = '';
$downloadLink = '';

try {
    $rsa = new RSA(KEY_PATH . 'public.pem', KEY_PATH . 'private.pem');
    if (!$rsa->getPrivateKey()) {
        $error = "Kunci privat tidak ditemukan. Silakan buat kunci terlebih dahulu.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
        if (isset($_FILES['file_to_decrypt']) && $_FILES['file_to_decrypt']['error'] === UPLOAD_ERR_OK) {
            $originalEncryptedFileName = basename($_FILES['file_to_decrypt']['name']);
            $tempFilePath = $_FILES['file_to_decrypt']['tmp_name'];
            $combinedEncryptedData = file_get_contents($tempFilePath);

            // Parse combined data: RSA_encrypted_AES_Key::RSA_encrypted_AES_IV::AES_encrypted_File_Content
            $parts = explode('::', $combinedEncryptedData, 3);

            if (count($parts) === 3) {
                list($encryptedAesKey, $encryptedAesIV, $encryptedFileContent) = $parts;

                // 1. Decrypt AES Key and IV with RSA Private Key
                $aesKey = $rsa->decrypt($encryptedAesKey);
                $aesIV = $rsa->decrypt($encryptedAesIV);

                // 2. Decrypt File Content with AES Key and IV
                $decryptedFileContent = AES::decrypt($encryptedFileContent, $aesKey, $aesIV);

                // Determine original file name
                $originalFileName = preg_replace('/\.encrypted$/', '', $originalEncryptedFileName);
                if ($originalFileName == $originalEncryptedFileName) { // If it didn't have .encrypted extension
                    $originalFileName = $originalEncryptedFileName . '.decrypted'; // Add .decrypted instead
                }

                $outputPath = UPLOAD_PATH_DECRYPTED . $originalFileName;

                if (file_put_contents($outputPath, $decryptedFileContent)) {
                    $message = "File berhasil didekripsi!";
                    $downloadLink = "download_file.php?type=decrypted&file=" . urlencode($originalFileName);
                } else {
                    $error = "Gagal menyimpan file terdekripsi.";
                }
            } else {
                $error = "Format file terenkripsi tidak valid. Pastikan ini adalah file terenkripsi yang dibuat oleh aplikasi ini.";
            }
        } else if (isset($_FILES['file_to_decrypt']) && $_FILES['file_to_decrypt']['error'] !== UPLOAD_ERR_NO_FILE) {
            $error = "Terjadi kesalahan saat mengunggah file: " . $_FILES['file_to_decrypt']['error'];
        } else {
            // No file uploaded
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
    <meta name="viewport" content="width=device-width, initial="1.0">
    <title>Dekripsi File</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Dekripsi File</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="file_to_decrypt">Pilih File Terenkripsi:</label>
            <input type="file" id="file_to_decrypt" name="file_to_decrypt" required>
            <button type="submit">Dekripsi File</button>
        </form>

        <?php if ($downloadLink): ?>
            <p>File terdekripsi tersedia: <a href="<?php echo $downloadLink; ?>" download><?php echo htmlspecialchars(basename($downloadLink)); ?></a></p>
        <?php endif; ?>

        <p><a href="index.php">Kembali ke Beranda</a></p>
    </div>
</body>
</html>