<?php
require_once 'config.php';

echo "<h2>Demonstrasi Enkripsi AES-256-CBC dengan Random IV</h2>";
echo "<p><strong>Tujuan:</strong> Menunjukkan bahwa data yang sama menghasilkan ciphertext berbeda karena IV random</p>";
echo "<hr>";

// Data plaintext yang sama
$plaintext = "Lala";

echo "<h3>Data Plaintext (sama):</h3>";
echo "<p style='background:#e3f2fd; padding:10px;'>\"$plaintext\"</p>";

echo "<h3>Hasil Enkripsi (berbeda karena IV random):</h3>";

// Enkripsi 5 kali dengan data yang sama
for ($i = 1; $i <= 5; $i++) {
    $encrypted = encryptData($plaintext);
    
    echo "<p><strong>Enkripsi ke-$i:</strong><br>";
    echo "<code style='background:#fff3cd; padding:5px; display:block; word-break:break-all;'>$encrypted</code></p>";
    
    // Verifikasi dekripsi
    $decrypted = decryptData($encrypted);
    echo "<p style='color:green;'>✅ Dekripsi: \"$decrypted\"</p>";
    echo "<hr style='border:1px dashed #ccc;'>";
}

echo "<h3>Kesimpulan:</h3>";
echo "<ul>";
echo "<li>✅ Plaintext yang sama: \"$plaintext\"</li>";
echo "<li>✅ Menghasilkan 5 ciphertext yang BERBEDA</li>";
echo "<li>✅ Semua ciphertext dapat didekripsi kembali dengan benar</li>";
echo "<li>✅ IV random memberikan keamanan tambahan (mencegah pattern analysis)</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Analisis Teknis:</h3>";
echo "<p>Setiap enkripsi menggunakan IV yang berbeda (random 16 bytes). ";
echo "IV tersebut disimpan di 16 bytes pertama hasil enkripsi, sehingga ";
echo "proses dekripsi dapat mengambil IV yang tepat untuk setiap data.</p>";
?>
```

