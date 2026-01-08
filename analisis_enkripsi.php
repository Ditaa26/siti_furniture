<?php
require_once 'config.php';

// Function untuk analisis data terenkripsi
function analyzeEncryption($encryptedData) {
    $method = 'AES-256-CBC';
    
    // Decode Base64
    $data = base64_decode($encryptedData);
    
    // Ambil IV length
    $iv_length = openssl_cipher_iv_length($method);
    
    // Ekstrak IV dan encrypted data
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    
    return [
        'base64' => $encryptedData,
        'base64_length' => strlen($encryptedData),
        'binary_length' => strlen($data),
        'iv_binary' => $iv,
        'iv_hex' => bin2hex($iv),
        'iv_base64' => base64_encode($iv),
        'iv_length' => $iv_length,
        'encrypted_hex' => bin2hex($encrypted),
        'encrypted_length' => strlen($encrypted)
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Enkripsi AES-256-CBC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            background: #667eea;
            color: white;
        }
        .hex-display {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            margin: 10px 0;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
        }
        .btn:hover {
            background: #5568d3;
        }
        .highlight {
            background: #fff3cd;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>🔍 Analisis Data Terenkripsi AES-256-CBC</h2>
        <p>Masukkan data terenkripsi (Base64) dari database untuk melihat IV-nya:</p>
        
        <form method="POST">
            <label><strong>Data Terenkripsi (Base64):</strong></label>
            <textarea name="encrypted_data" rows="4" required placeholder="Paste data terenkripsi dari database di sini..."></textarea>
            <br><br>
            <button type="submit" class="btn">🔓 Analisis</button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['encrypted_data'])) {
        $encryptedInput = trim($_POST['encrypted_data']);
        
        try {
            $analysis = analyzeEncryption($encryptedInput);
            ?>
            
            <div class="card">
                <h2>📊 Hasil Analisis</h2>
                
                <h3>1️⃣ Data Input</h3>
                <table>
                    <tr>
                        <th>Parameter</th>
                        <th>Nilai</th>
                    </tr>
                    <tr>
                        <td>Data Terenkripsi (Base64)</td>
                        <td><code><?php echo htmlspecialchars(substr($analysis['base64'], 0, 50)) . '...'; ?></code></td>
                    </tr>
                    <tr>
                        <td>Panjang Base64</td>
                        <td><?php echo $analysis['base64_length']; ?> karakter</td>
                    </tr>
                    <tr>
                        <td>Panjang Binary (setelah decode)</td>
                        <td><?php echo $analysis['binary_length']; ?> bytes</td>
                    </tr>
                </table>

                <h3>2️⃣ Initialization Vector (IV)</h3>
                <p><span class="highlight">IV adalah 16 bytes PERTAMA dari data terenkripsi!</span></p>
                <table>
                    <tr>
                        <th>Format</th>
                        <th>Nilai</th>
                    </tr>
                    <tr>
                        <td><strong>IV (Hexadecimal)</strong></td>
                        <td>
                            <div class="hex-display">
                                <?php echo $analysis['iv_hex']; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>IV (Base64)</strong></td>
                        <td><code><?php echo $analysis['iv_base64']; ?></code></td>
                    </tr>
                    <tr>
                        <td>Panjang IV</td>
                        <td><?php echo $analysis['iv_length']; ?> bytes (128 bit)</td>
                    </tr>
                </table>

                <h3>3️⃣ Encrypted Data (Ciphertext)</h3>
                <table>
                    <tr>
                        <th>Parameter</th>
                        <th>Nilai</th>
                    </tr>
                    <tr>
                        <td>Encrypted Data (Hex)</td>
                        <td>
                            <div class="hex-display">
                                <?php echo substr($analysis['encrypted_hex'], 0, 100); ?>...
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Panjang Encrypted Data</td>
                        <td><?php echo $analysis['encrypted_length']; ?> bytes</td>
                    </tr>
                </table>

                <h3>4️⃣ Visualisasi Struktur</h3>
                <div style="background: #e8eaf6; padding: 20px; border-radius: 5px;">
                    <pre style="margin: 0; font-family: monospace;">
<strong>Struktur Data Terenkripsi:</strong>

┌────────────────────────────────────────────────────┐
│                  Base64 String                     │
│  <?php echo substr($analysis['base64'], 0, 50); ?>...  │
└────────────────────────────────────────────────────┘
                         ↓ (base64_decode)
┌────────────────────────────────────────────────────┐
│              Binary Data (<?php echo $analysis['binary_length']; ?> bytes)              │
├──────────────────────┬─────────────────────────────┤
│   IV (16 bytes)      │   Encrypted Data            │
│   <?php echo substr($analysis['iv_hex'], 0, 20); ?>... │   <?php echo substr($analysis['encrypted_hex'], 0, 20); ?>...      │
└──────────────────────┴─────────────────────────────┘
    ↑ Ini yang kita      ↑ Ini ciphertext
      ekstrak!              (data terenkripsi)
                    </pre>
                </div>

                <h3>5️⃣ Verifikasi Dekripsi</h3>
                <?php
                try {
                    $key = ENCRYPTION_KEY;
                    $method = 'AES-256-CBC';
                    $decrypted = openssl_decrypt(
                        substr(base64_decode($analysis['base64']), $analysis['iv_length']),
                        $method,
                        $key,
                        OPENSSL_RAW_DATA,
                        $analysis['iv_binary']
                    );
                    
                    if ($decrypted !== false && !empty($decrypted)) {
                        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 5px solid #28a745;'>";
                        echo "<strong>✅ Dekripsi Berhasil!</strong><br>";
                        echo "Plaintext: <strong style='font-size: 1.2em;'>" . htmlspecialchars($decrypted) . "</strong><br><br>";
                        echo "Ini membuktikan IV yang diekstrak adalah <span class='highlight'>BENAR</span>!";
                        echo "</div>";
                    } else {
                        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
                        echo "❌ Dekripsi gagal. Mungkin kunci enkripsi salah.";
                        echo "</div>";
                    }
                } catch (Exception $e) {
                    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
                    echo "❌ Error: " . $e->getMessage();
                    echo "</div>";
                }
                ?>
            </div>

            <div class="card">
                <h2>📝 Kesimpulan</h2>
                <ul style="line-height: 2;">
                    <li>✅ IV <strong>tersimpan</strong> di 16 bytes pertama data terenkripsi</li>
                    <li>✅ IV <strong>bisa dilihat/diekstrak</strong> kapan saja dari ciphertext</li>
                    <li>✅ IV <strong>tidak rahasia</strong> - boleh diketahui publik</li>
                    <li>✅ Yang rahasia adalah <strong>ENCRYPTION_KEY</strong>, bukan IV</li>
                    <li>✅ Tanpa KEY yang benar, data tetap tidak bisa didekripsi meski IV diketahui</li>
                </ul>
            </div>

            <?php
        } catch (Exception $e) {
            echo "<div class='card'>";
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
            echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
            echo "</div>";
            echo "</div>";
        }
    }
    ?>

    <div class="card">
        <h2>💡 Cara Menggunakan</h2>
        <ol style="line-height: 2;">
            <li>Buka <strong>phpMyAdmin</strong></li>
            <li>Pilih database <strong>siti_furniture</strong></li>
            <li>Buka tabel <strong>orders</strong></li>
            <li>Copy isi kolom <strong>customer_name</strong> (yang terenkripsi)</li>
            <li>Paste di form di atas</li>
            <li>Klik <strong>Analisis</strong></li>
            <li>Lihat IV-nya!</li>
        </ol>
    </div>
</body>
</html>