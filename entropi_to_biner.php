<?php
// 1. Data ciphertext dalam format Base64
$cipher_base64 = "0rNRheRVXG1KNpvLAM/IOhAYvfKi9HpJq7inyPsBeSd7r5SsOarYty05zRjFMpS0";

// 2. Decode Base64 ke bytes
$cipher_bytes = base64_decode($cipher_base64, true);
if ($cipher_bytes === false) {
    die("Error: Format Base64 tidak valid");
}

// Fungsi konversi bytes ke Hex format rapi (per 16 byte per baris)
function bytes_to_formatted_hex($data) {
    $hex_str = strtoupper(bin2hex($data));
    $formatted = '';
    $len = strlen($hex_str);
    for ($i = 0; $i < $len; $i += 2) {
        $formatted .= $hex_str[$i] . $hex_str[$i+1] . ' ';
        // Baris baru setiap 16 byte (32 hex chars)
        if ((($i/2 + 1) % 16) == 0) {
            $formatted .= PHP_EOL;
        }
    }
    return trim($formatted);
}

// 3. Fungsi hitung entropi Shannon berbasis byte - DENGAN DEBUG
function shannon_entropy($data) {
    $length = strlen($data);
    if ($length == 0) return 0;

    $counts = array_count_values(str_split($data));
    
    // DEBUG: Tampilkan distribusi frekuensi
    echo "<h3>DEBUG: Distribusi Frekuensi Byte</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Byte (Hex)</th><th>Frekuensi</th><th>Probabilitas</th><th>Kontribusi Entropi</th></tr>";
    
    arsort($counts); // Urutkan dari frekuensi tertinggi
    $total_entropy = 0;
    
    foreach ($counts as $byte => $count) {
        $hex = strtoupper(bin2hex($byte));
        $p = $count / $length;
        $contribution = -$p * log($p, 2);
        $total_entropy += $contribution;
        
        echo "<tr>";
        echo "<td>0x{$hex}</td>";
        echo "<td>{$count}</td>";
        echo "<td>" . number_format($p, 6) . "</td>";
        echo "<td>" . number_format($contribution, 6) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p><strong>Total unique bytes: " . count($counts) . "</strong></p>";
    echo "<p><strong>Total data length: {$length} bytes</strong></p>";
    
    // Verifikasi: hitung entropi dengan cara standar
    $entropy = 0;
    foreach ($counts as $count) {
        $p = $count / $length;
        $entropy -= $p * log($p, 2);
    }
    
    echo "<p><strong>Total Entropi (dari loop): " . number_format($total_entropy, 6) . " bits</strong></p>";
    
    return $entropy;
}

// 4. Hitung entropi
echo "<h2>Perhitungan Entropi Shannon</h2>";

echo "<h3>Langkah 1: Ciphertext awal dalam format Base64:</h3>";
echo "<code>" . $cipher_base64 . "</code><br><br>";

echo "<h3>Langkah 2: Setelah decoding Base64 menjadi data biner (bytes)</h3>";
echo "<p>Panjang data: <strong>" . strlen($cipher_bytes) . " bytes</strong></p>";

echo "<h3>Data Hex:</h3>";
echo "<pre>" . bytes_to_formatted_hex($cipher_bytes) . "</pre><br>";

echo "<h3>Langkah 3: Hasil perhitungan entropi Shannon (berbasis byte):</h3>";
$entropy = shannon_entropy($cipher_bytes);

echo "<h2 style='color: red;'>Hasil Akhir: " . round($entropy, 4) . " bits per byte</h2>";

// Tambahan: Cek byte yang muncul lebih dari 1x
echo "<hr><h3>Byte yang Muncul Lebih dari 1 Kali:</h3>";
$counts = array_count_values(str_split($cipher_bytes));
$repeated = array_filter($counts, function($count) { return $count > 1; });

if (count($repeated) > 0) {
    echo "<ul>";
    foreach ($repeated as $byte => $count) {
        $hex = strtoupper(bin2hex($byte));
        echo "<li>Byte 0x{$hex}: muncul <strong>{$count} kali</strong></li>";
    }
    echo "</ul>";
    echo "<p>Total byte yang berulang: <strong>" . count($repeated) . " jenis</strong></p>";
} else {
    echo "<p>Tidak ada byte yang berulang (semua unique)</p>";
}
?>