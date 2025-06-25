<?php
// lib/RSA.php

// Kelas RSA ini digunakan untuk melakukan enkripsi dan dekripsi menggunakan algoritma RSA di PHP
class RSA {
    private $publicKey;   // Menyimpan public key RSA
    private $privateKey;  // Menyimpan private key RSA

    // Konstruktor: jika diberikan path file public/private key, maka kunci dibaca dari file tersebut
    public function __construct($publicKeyPath = null, $privateKeyPath = null) {
        if ($publicKeyPath && file_exists($publicKeyPath)) {
            $this->publicKey = file_get_contents($publicKeyPath); // Ambil isi file public key
        }
        if ($privateKeyPath && file_exists($privateKeyPath)) {
            $this->privateKey = file_get_contents($privateKeyPath); // Ambil isi file private key
        }
    }

    // Fungsi untuk membuat pasangan kunci RSA baru
    public function generateKeys($bits = 2048) {
        $config = array(
            "digest_alg" => "sha512",              // Algoritma hash untuk digital signature
            "private_key_bits" => $bits,           // Jumlah bit kunci RSA (misal 2048 bit)
            "private_key_type" => OPENSSL_KEYTYPE_RSA // Tipe kunci: RSA
        );

        $res = openssl_pkey_new($config); // Buat pasangan kunci baru

        if (!$res) {
            throw new Exception("Error generating RSA keys: " . openssl_error_string()); // Tangani error jika gagal
        }

        // openssl_pkey_export($res, $privateKey);  // Ekspor private key ke variabel
        $publicKey = openssl_pkey_get_details($res)["key"]; // Ambil public key dari pasangan kunci

        // Simpan ke properti class
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;

        return ['public' => $publicKey, 'private' => $privateKey]; // Kembalikan hasil dalam bentuk array
    }

    // Fungsi untuk menyimpan kunci ke file
    public function saveKeys($publicKeyPath, $privateKeyPath) {
        if ($this->publicKey && $this->privateKey) {
            file_put_contents($publicKeyPath, $this->publicKey);   // Simpan public key ke file
            file_put_contents($privateKeyPath, $this->privateKey); // Simpan private key ke file
            return true;
        }
        return false;
    }

    // Getter untuk public key
    public function getPublicKey() {
        return $this->publicKey;
    }

    // Getter untuk private key
    public function getPrivateKey() {
        return $this->privateKey;
    }

    // Fungsi untuk mengenkripsi data dengan public key
    public function encrypt($data, $publicKey = null) {
        $key = $publicKey ?? $this->publicKey; // Gunakan parameter jika ada, jika tidak pakai properti class
        if (!$key) {
            throw new Exception("Public key not set for encryption."); // Tangani error jika tidak ada kunci
        }

        $encrypted = '';

        // Ukuran maksimum data yang bisa dienkripsi per blok tergantung pada ukuran kunci.
        // Untuk kunci 2048 bit, maksimum sekitar 245 byte karena ada overhead padding PKCS#1 (11 byte)
        $chunkSize = openssl_pkey_get_details(openssl_pkey_get_public($key))['bits'] / 8 - 11;
        $chunks = str_split($data, $chunkSize); // Potong data menjadi blok kecil

        $output = '';

        // Enkripsi setiap blok secara terpisah
        foreach ($chunks as $chunk) {
            if (!openssl_public_encrypt($chunk, $encryptedChunk, $key)) {
                throw new Exception("RSA Encryption failed: " . openssl_error_string()); // Jika gagal enkripsi
            }
            $output .= $encryptedChunk; // Gabungkan hasil enkripsi
        }

        return base64_encode($output); // Encode base64 untuk penyimpanan/transmisi
    }

    // Fungsi untuk mendekripsi data dengan private key
    public function decrypt($encryptedData, $privateKey = null) {
        $key = $privateKey ?? $this->privateKey; // Gunakan parameter jika ada, jika tidak pakai properti class
        if (!$key) {
            throw new Exception("Private key not set for decryption."); // Tangani error jika tidak ada kunci
        }

        $encryptedData = base64_decode($encryptedData); // Decode dari base64
        $decrypted = '';

        // Ukuran blok terenkripsi adalah ukuran kunci RSA, misal 256 byte untuk 2048 bit
        $chunkSize = openssl_pkey_get_details(openssl_pkey_get_private($key))['bits'] / 8;
        $chunks = str_split($encryptedData, $chunkSize); // Bagi data terenkripsi jadi blok kecil

        $output = '';

        // Dekripsi tiap blok
        foreach ($chunks as $chunk) {
            if (!openssl_private_decrypt($chunk, $decryptedChunk, $key)) {
                throw new Exception("RSA Decryption failed: " . openssl_error_string()); // Jika gagal dekripsi
            }
            $output .= $decryptedChunk; // Gabungkan hasil dekripsi
        }

        return $output;
    }
}
?>
