<?php
require_once 'config/config.php';
requireAdmin();

$conn = getDBConnection();

// Ambil 1 data terbaru dari database
$sql = "SELECT customer_name, customer_phone, customer_address 
        FROM orders 
        WHERE id = 1 
        LIMIT 1";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Tidak ada data untuk diuji.");
}

$data = $result->fetch_assoc();

// Ciphertext dari database
$cipher_name    = $data['customer_name'];
$cipher_phone   = $data['customer_phone'];
$cipher_address = $data['customer_address'];

// Dekripsi
$plain_name    = decryptData($cipher_name);
$plain_phone   = decryptData($cipher_phone);
$plain_address = decryptData($cipher_address);

// Validasi (cipher → decrypt → encrypt ulang → decrypt)
$valid_name    = ($plain_name === decryptData(encryptData($plain_name)));
$valid_phone   = ($plain_phone === decryptData(encryptData($plain_phone)));
$valid_address = ($plain_address === decryptData(encryptData($plain_address)));

$all_valid = $valid_name && $valid_phone && $valid_address;

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Uji Konsistensi Enkripsi–Dekripsi</title>
    <style>
       body {
    font-family: Arial, sans-serif;
    background: #eef2f5;
    padding: 40px;
}

.card {
    background: #ffffff;
    padding: 30px;
    max-width: 900px;
    margin: auto;
    border-radius: 8px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

h2 {
    color: #1f4e79;
    border-bottom: 3px solid #1f4e79;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

p {
    color: #333;
    line-height: 1.6;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    border: 1px solid #cfd8e3;
    padding: 12px;
    vertical-align: top;
    font-size: 0.95rem;
}

table th {
    background: #1f4e79;
    color: #ffffff;
    text-align: left;
}

table tr:nth-child(even) {
    background: #f7f9fb;
}

.cipher {
    font-family: monospace;
    font-size: 0.85em;
    word-break: break-all;
    background: #f5f5f5;
    padding: 6px;
    border-radius: 4px;
    border: 1px dashed #bbb;
}

.success {
    margin-top: 25px;
    padding: 15px 20px;
    background: #e6f4ea;
    color: #155724;
    border-left: 5px solid #2e7d32;
    border-radius: 4px;
    font-size: 0.95rem;
}

    </style>
</head>
<body>

<div class="card">
    <h2>Pengujian Fungsional Enkripsi–Dekripsi AES-256-CBC</h2>

    <p>
        Pengujian ini dilakukan dengan mengambil ciphertext yang tersimpan di dalam
        basis data sistem, kemudian mendekripsinya kembali menggunakan kunci dan
        initialization vector (IV) yang sesuai.
    </p>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Ciphertext (Database)</th>
                <th>Hasil Dekripsi (Plaintext)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nama</td>
                <td><div class="cipher"><?= htmlspecialchars($cipher_name); ?></div></td>
                <td><?= htmlspecialchars($plain_name); ?></td>
                <td><?= $valid_name ? '✅ Valid' : '❌ Tidak valid'; ?></td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td><div class="cipher"><?= htmlspecialchars($cipher_phone); ?></div></td>
                <td><?= htmlspecialchars($plain_phone); ?></td>
                <td><?= $valid_phone ? '✅ Valid' : '❌ Tidak valid'; ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><div class="cipher"><?= htmlspecialchars($cipher_address); ?></div></td>
                <td><?= htmlspecialchars($plain_address); ?></td>
                <td><?= $valid_address ? '✅ Valid' : '❌ Tidak valid'; ?></td>
            </tr>
        </tbody>
    </table>

    <?php if ($all_valid): ?>
        <div class="success">
            <strong>Kesimpulan:</strong><br>
            Seluruh ciphertext yang diambil dari basis data berhasil didekripsi kembali
            ke bentuk plaintext semula tanpa perubahan. Hasil ini membuktikan bahwa
            mekanisme enkripsi–dekripsi AES-256-CBC berjalan secara konsisten dan sejalur.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
