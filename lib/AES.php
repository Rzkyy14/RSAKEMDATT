<?php
// lib/AES.php (Opsional, jika ingin memisahkan fungsi AES ke dalam kelas)

class AES {
    const CYPHER = 'aes-256-cbc'; // Algoritma AES
    const OPTIONS = 0; // Flags untuk openssl_encrypt/decrypt

    public static function generateKey() {
        return bin2hex(random_bytes(32)); // 32 bytes = 256 bits
    }

    public static function generateIV() {
        return bin2hex(random_bytes(openssl_cipher_iv_length(self::CYPHER)));
    }

    public static function encrypt($plaintext, $key, $iv) {
        $ciphertext = openssl_encrypt($plaintext, self::CYPHER, hex2bin($key), self::OPTIONS, hex2bin($iv));
        if ($ciphertext === false) {
            throw new Exception("AES Encryption failed: " . openssl_error_string());
        }
        return base64_encode($ciphertext);
    }

    public static function decrypt($ciphertext, $key, $iv) {
        $plaintext = openssl_decrypt(base64_decode($ciphertext), self::CYPHER, hex2bin($key), self::OPTIONS, hex2bin($iv));
        if ($plaintext === false) {
            throw new Exception("AES Decryption failed: " . openssl_error_string());
        }
        return $plaintext;
    }
}
?>