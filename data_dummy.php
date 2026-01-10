<?php
require_once 'config/config.php';

$conn = getDBConnection();

/* ===== DATA MASTER ===== */

$names = [
    "Rizal Fahmi Kurniawan",
    "Sari Lestari Dewi Putri",
    "Aisyah Nur Amalia",
    "Achmad Fauzi",
    "Putri Ayu Lestari",
    "Muhammad Rizky",
    "Ahmad Fauzan",
    "Nurul Hidayah",
    "Indah Permata Sari",
    "Rahmawati Putri",
    "Sabrina Maharani Putri Lestari"
];

$streets = [
    "Flamboyan Indah",
    "Kenanga Permai",
    "Anggrek Timur",
    "Melati Raya",
    "Merdeka Utama"
];

$kelurahan = [
    "Kenari Baru",
    "Melur Abadi",
    "Mawar Sari",
    "Cempaka Putih",
    "Teratai Indah"
];

$kecamatan = [
    "Aurora",
    "Falsafah",
    "Imaginary",
    "Sentosa",
    "Harmoni"
];

$kota = [
    "Kota Pelita",
    "Kota Mandiri",
    "Kota Tirta",
    "Kota Nusantara",
    "Kota Sejahtera"
];

$provinsi = [
    "Provinsi Harapan",
    "Provinsi Makmur",
    "Provinsi Nusantara",
    "Provinsi Sejahtera"
];

$products = [
    ["Kursi Kerja Ergonomis", 650000],
    ["Kursi Teras Klasik", 550000],
    ["Kursi Makan Minimalis", 450000]
];

/* ===== PREPARE QUERY (SESUAI STRUKTUR TABEL) ===== */

$stmt = $conn->prepare("
    INSERT INTO orders
    (customer_name, customer_phone, customer_address,
     product_name, quantity, price, total_price)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

/* ===== GENERATE DATA ===== */

for ($i = 1; $i <= 50; $i++) {

    // Pilih data acak
    $name = $names[array_rand($names)];
    $phone = "08" . rand(1000000000, 9999999999);

    // Alamat lengkap digabung
    $address =
        "Jl. " . $streets[array_rand($streets)] .
        " No. " . rand(1, 500) .
        ", RT " . rand(1, 20) . "/RW " . rand(1, 20) .
        ", Kelurahan " . $kelurahan[array_rand($kelurahan)] .
        ", Kecamatan " . $kecamatan[array_rand($kecamatan)] .
        ", " . $kota[array_rand($kota)] .
        ", " . $provinsi[array_rand($provinsi)] .
        ", Kode Pos " . rand(10000, 99999);

    // Produk
    $product = $products[array_rand($products)];
    $product_name = $product[0];
    $price = $product[1];
    $qty = rand(1, 3);
    $total = $price * $qty;

    // ENKRIPSI (WAJIB JADI VARIABEL)
    $enc_name    = encryptData($name);
    $enc_phone   = encryptData($phone);
    $enc_address = encryptData($address);

    // BIND PARAMETER (7 PARAMETER → 7 TIPE)
    $stmt->bind_param(
        "ssssiii",
        $enc_name,
        $enc_phone,
        $enc_address,
        $product_name,
        $qty,
        $price,
        $total
    );

    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "✓ 50 data dummy terenkripsi berhasil dibuat";
