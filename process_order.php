<?php
// process_order.php - Process Order with AES-CBC Encryption

require_once 'config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    // Get POST data
    $name = sanitizeInput($_POST['name'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $productName = sanitizeInput($_POST['product_name'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    
    // Validation
    if (empty($name) || empty($phone) || empty($address) || empty($productName) || $quantity <= 0) {
        throw new Exception('Data tidak lengkap!');
    }
    
    // Calculate total
    $totalPrice = $price * $quantity;
    
    // Encrypt sensitive data (nama, telepon, alamat)
    $encryptedName = encryptData($name);
    $encryptedPhone = encryptData($phone);
    $encryptedAddress = encryptData($address);
    
    // Save to database
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, product_name, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "ssssidd",
        $encryptedName,
        $encryptedPhone,
        $encryptedAddress,
        $productName,
        $quantity,
        $price,
        $totalPrice
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Pesanan berhasil diproses!',
            'data' => [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'product_name' => $productName,
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $totalPrice
            ]
        ]);
    } else {
        throw new Exception('Gagal menyimpan pesanan');
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>