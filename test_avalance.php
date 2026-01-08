<?php
/**
 * avalanche_test.php
 * Pengujian Avalanche Effect AES-256-CBC
 * Digunakan untuk keperluan penelitian
 */

require_once 'config.php'; // pakai KEY & algoritma yang sama

// ===================================================
// Fungsi enkripsi khusus pengujian (IV dibuat tetap)
// ===================================================
function encryptAvalanche($plaintext, $iv) {
    $method = 'AES-256-CBC';
    $key = ENCRYPTION_KEY;

    return openssl_encrypt(
        $plaintext,
        $method,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
}

// ===================================================
// Fungsi menghitung perbedaan bit
// ===================================================
function countBitDifference($cipher1, $cipher2) {
    $diff = 0;
    $length = min(strlen($cipher1), strlen($cipher2));

    for ($i = 0; $i < $length; $i++) {
        $xor = ord($cipher1[$i]) ^ ord($cipher2[$i]);
        $diff += substr_count(decbin($xor), '1');
    }
    return $diff;
}

// ===================================================
// DATA UJI (beda 1 karakter)
// ===================================================
$plaintext1 = "Ahmad Fauzi";
$plaintext2 = "Achmad Fauzi."; // beda 1 karakter

$method = 'AES-256-CBC';
$ivLength = openssl_cipher_iv_length($method);

// IV dibuat tetap (nol) agar hasil murni dari plaintext
$fixedIV = str_repeat("\0", $ivLength);

// ===================================================
// PROSES ENKRIPSI
// ===================================================
$cipher1 = encryptAvalanche($plaintext1, $fixedIV);
$cipher2 = encryptAvalanche($plaintext2, $fixedIV);

// Encode cipher ke HEX agar bisa ditampilkan
$cipherHex1 = bin2hex($cipher1);
$cipherHex2 = bin2hex($cipher2);


// ===================================================
// PERHITUNGAN AVALANCHE
// ===================================================
$totalBits = strlen($cipher1) * 8;
$bitChanged = countBitDifference($cipher1, $cipher2);
$avalanche = ($bitChanged / $totalBits) * 100;

// ===================================================
// OUTPUT
// ===================================================
echo "<h3>Hasil Pengujian Avalanche Effect</h3>";

echo "Plaintext 1 : <b>$plaintext1</b><br>";
echo "Plaintext 2 : <b>$plaintext2</b><br><br>";

echo "<b>Ciphertext C1 (HEX):</b><br>";
echo "<code>$cipherHex1</code><br><br>";

echo "<b>Ciphertext C2 (HEX):</b><br>";
echo "<code>$cipherHex2</code><br><br>";

echo "Total Bit Ciphertext : <b>$totalBits</b><br>";
echo "Bit Berubah : <b>$bitChanged</b><br>";
echo "Avalanche Effect : <b>" . round($avalanche, 2) . "%</b>";

?>
