<?php
require 'config.php';

/* =========================================
   FUNGSI HITUNG ENTROPI SHANNON
   
   Entropi Shannon mengukur tingkat keacakan data
   Rumus: H(X) = -Σ P(xi) × log₂(P(xi))
   
   Dimana:
   - H(X) = Entropi (dalam bit/byte)
   - P(xi) = Probabilitas kemunculan byte ke-i
   - log₂ = Logaritma basis 2
   - Σ = Sigma (penjumlahan untuk semua byte unik)
   
   Hasil entropi:
   - Nilai maksimal: 8.0 bit/byte (data sangat acak)
   - Nilai minimal: 0.0 bit/byte (data tidak acak/seragam)
   - Untuk ciphertext AES yang baik: 7.9-8.0 bit/byte
========================================= */
function calculateEntropy($binaryData) {
    // Hitung panjang total data dalam byte
    $length = strlen($binaryData);
    
    // Jika data kosong, entropi = 0
    if ($length === 0) return 0;
    
    // Array untuk menyimpan frekuensi kemunculan setiap byte (0-255)
    $freq = [];
    
    // Loop untuk menghitung frekuensi setiap byte
    for ($i = 0; $i < $length; $i++) {
        // Ambil nilai byte (0-255) dari karakter
        $byte = ord($binaryData[$i]);
        
        // Inisialisasi jika byte belum ada di array
        if (!isset($freq[$byte])) {
            $freq[$byte] = 0;
        }
        
        // Tambah counter frekuensi byte ini
        $freq[$byte]++;
    }
    
    // Variabel untuk menyimpan hasil perhitungan entropi
    $entropy = 0.0;
    
    // Loop untuk menghitung entropi menggunakan rumus Shannon
    foreach ($freq as $count) {
        // Hitung probabilitas: P(xi) = frekuensi / total data
        $p = $count / $length;
        
        // Terapkan rumus: H(X) = -Σ P(xi) × log₂(P(xi))
        // log($p, 2) = logaritma basis 2 dari probabilitas
        // Tanda minus (-) karena log dari nilai < 1 menghasilkan negatif
        $entropy -= $p * log($p, 2);
    }
    
    // Kembalikan nilai entropi dalam bit/byte
    return $entropy;
}

/* =========================================
   AMBIL DATA DARI DATABASE
========================================= */
$conn = getDBConnection();

// Variasi jumlah data yang akan diuji
$dataLimits = [10, 20, 30, 40, 50];

// Array untuk menyimpan hasil pengujian
$results = [];

// Loop untuk setiap variasi jumlah data
foreach ($dataLimits as $limit) {
    // Query untuk mengambil data terenkripsi dari database
    // Hanya ambil data yang tidak NULL
    $sql = "SELECT customer_name, customer_phone, customer_address
            FROM orders
            WHERE customer_name IS NOT NULL
              AND customer_phone IS NOT NULL
              AND customer_address IS NOT NULL
            LIMIT $limit";
            
    $query = $conn->query($sql);
    
    // String untuk menggabungkan semua ciphertext
    $combinedBinary = "";
    
    // Counter jumlah baris data
    $totalRows = 0;
    
    // Loop setiap baris hasil query
    while ($row = $query->fetch_assoc()) {
        // Loop untuk setiap kolom (name, phone, address)
        foreach (['customer_name', 'customer_phone', 'customer_address'] as $col) {
            // Ambil ciphertext yang disimpan dalam format Base64
            $cipherBase64 = $row[$col];
            
            // Decode Base64 ke binary (ciphertext asli)
            $binary = base64_decode($cipherBase64, true);
            
            // Validasi: pastikan decode berhasil dan ada isinya
            if ($binary !== false && strlen($binary) > 0) {
                // Gabungkan semua ciphertext menjadi satu string binary
                $combinedBinary .= $binary;
            }
        }
        
        // Tambah counter baris
        $totalRows++;
    }
    
    // Jika tidak ada data yang valid
    if (strlen($combinedBinary) === 0) {
        $entropy = 0;
        $percentage = 0;
        $totalBytes = 0;
    } else {
        // Hitung entropi dari gabungan semua ciphertext
        $entropy = calculateEntropy($combinedBinary);
        
        // Hitung persentase keacakan (entropi / 8 × 100%)
        // Dibagi 8 karena entropi maksimal = 8 bit/byte
        $percentage = ($entropy / 8) * 100;
        
        // Hitung total byte yang diproses
        $totalBytes = strlen($combinedBinary);
    }
    
    // Simpan hasil pengujian ke array
    $results[] = [
        'jumlah_data' => $totalRows,                    // Jumlah data order
        'total_ciphertext' => $totalRows * 3,           // Total field terenkripsi (3 field per row)
        'total_bytes' => $totalBytes,                   // Total byte ciphertext
        'entropy' => $entropy,                          // Nilai entropi (bit/byte)
        'percentage' => $percentage                     // Persentase keacakan (%)
    ];
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengujian Entropi Shannon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        
        /* Tabel */
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 80%;
            background: white;
        }
        th, td {
            padding: 10px 12px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: 14px;
        }
        th {
            background: #333;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        
        /* Container Grafik */
        .chart-wrapper {
            width: 700px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        /* Tombol Download */
        .download-btn {
            display: block;
            width: 200px;
            margin: 15px auto;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .download-btn:hover {
            background: #2980b9;
        }
        
        /* Box Penjelasan */
        .info-box {
            width: 80%;
            margin: 20px auto;
            padding: 15px;
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #2980b9;
        }
        .info-box p {
            margin: 5px 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<h2>Hasil Pengujian Entropi Shannon Ciphertext AES-256-CBC</h2>

<!-- BOX PENJELASAN ENTROPI SHANNON -->
<div class="info-box">
    <h3>📊 Apa itu Entropi Shannon?</h3>
    <p><strong>Entropi Shannon</strong> adalah ukuran untuk mengukur tingkat <strong>keacakan</strong> atau <strong>ketidakpastian</strong> dalam data.</p>
    <p><strong>Rumus:</strong> H(X) = -Σ P(xi) × log₂(P(xi))</p>
    <p><strong>Interpretasi Hasil:</strong></p>
    <ul style="margin-top: 5px;">
        <li><strong>8.0 bit/byte (100%)</strong> = Data sangat acak (ideal untuk ciphertext)</li>
        <li><strong>7.9-8.0 bit/byte (98-100%)</strong> = Enkripsi AES yang baik</li>
        <li><strong>< 7.5 bit/byte (< 94%)</strong> = Data memiliki pola/tidak cukup acak</li>
        <li><strong>0.0 bit/byte (0%)</strong> = Data seragam/tidak ada keacakan</li>
    </ul>
</div>

<!-- TABEL HASIL PENGUJIAN -->
<table>
    <tr>
        <th>No</th>
        <th>Jumlah Data</th>
        <th>Total Ciphertext</th>
        <th>Total Byte</th>
        <th>Entropi (bit/byte)</th>
        <th>Persentase (%)</th>
    </tr>
    <?php $no = 1; foreach ($results as $r): ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $r['jumlah_data']; ?></td>
        <td><?= $r['total_ciphertext']; ?></td>
        <td><?= number_format($r['total_bytes']); ?></td>
        <td><?= number_format($r['entropy'], 4); ?></td>
        <td><?= number_format($r['percentage'], 2); ?>%</td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- GRAFIK -->
<div class="chart-wrapper">
    <div class="chart-title">Grafik Peningkatan Nilai Entropi Shannon</div>
    <canvas id="entropyChart" width="660" height="400"></canvas>
    <button class="download-btn" onclick="downloadChart()">📥 Download Grafik (PNG)</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Ambil konteks canvas untuk menggambar grafik
const ctx = document.getElementById('entropyChart').getContext('2d');

// Data dari PHP: jumlah data dan nilai entropi
const labels = <?= json_encode(array_column($results, 'jumlah_data')); ?>;
const entropyData = <?= json_encode(array_column($results, 'entropy')); ?>;

// Konfigurasi dan pembuatan grafik menggunakan Chart.js
const chart = new Chart(ctx, {
    type: 'line',  // Jenis grafik: garis
    data: {
        // Label sumbu X (jumlah data)
        labels: labels.map(x => x + ' Data'),
        
        // Dataset yang akan ditampilkan
        datasets: [{
            label: 'Entropi (bit/byte)',
            data: entropyData,
            borderColor: '#3498db',                      // Warna garis
            backgroundColor: 'rgba(52, 152, 219, 0.1)',  // Warna area di bawah garis
            borderWidth: 2,                              // Ketebalan garis
            tension: 0.2,                                // Kelengkungan garis
            fill: true,                                  // Isi area di bawah garis
            pointRadius: 5,                              // Ukuran titik data
            pointHoverRadius: 7,                         // Ukuran titik saat hover
            pointBackgroundColor: '#3498db',             // Warna titik
            pointBorderColor: '#fff',                    // Warna border titik
            pointBorderWidth: 2                          // Ketebalan border titik
        }]
    },
    options: {
        responsive: false,  // Grafik tidak otomatis menyesuaikan ukuran
        plugins: {
            // Konfigurasi legend (keterangan grafik)
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: { size: 12 }
                }
            },
            // Konfigurasi tooltip (popup saat hover)
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Entropi: ' + context.parsed.y.toFixed(4) + ' bit/byte';
                    }
                }
            }
        },
        scales: {
            // Konfigurasi sumbu X (horizontal)
            x: {
                grid: { display: false },  // Sembunyikan grid vertikal
                ticks: { font: { size: 11 } }
            },
            // Konfigurasi sumbu Y (vertikal)
            y: {
                min: 7.8,   // Nilai minimum sumbu Y
                max: 8.0,   // Nilai maksimum sumbu Y
                ticks: {
                    font: { size: 11 },
                    callback: function(value) {
                        return value.toFixed(2);  // Format 2 desimal
                    }
                },
                title: {
                    display: true,
                    text: 'Entropi (bit/byte)',
                    font: { size: 12 }
                }
            }
        }
    }
});

/**
 * Fungsi untuk mendownload grafik sebagai gambar PNG
 * Menggunakan method toBase64Image() dari Chart.js
 */
function downloadChart() {
    const link = document.createElement('a');
    link.download = 'grafik-entropi-shannon.png';
    link.href = chart.toBase64Image();  // Konversi canvas ke base64 image
    link.click();  // Trigger download
}
</script>

</body>
</html>