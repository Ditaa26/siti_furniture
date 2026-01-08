<?php
// config.php - Database Configuration (SECURE VERSION)

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'siti_furniture');

// AES-CBC Encryption Configuration
// Key harus 32 bytes (256 bit) untuk AES-256
define('ENCRYPTION_KEY', '@S1FuRNn1*Ture!ME83&l8+10JAPNL@'); // 32 characters = 256 bit

// Database Connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database Error: " . $e->getMessage());
    }
}

// AES-CBC Encryption Function (SECURE VERSION dengan Random IV)
function encryptData($data) {
    $key = ENCRYPTION_KEY;
    $method = 'AES-256-CBC';
    
    // Generate RANDOM IV untuk setiap enkripsi
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    
    // Encrypt data
    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    
    // Gabungkan IV + encrypted data, lalu encode ke base64
    // IV disimpan di depan supaya bisa didekripsi nanti
    return base64_encode($iv . $encrypted);
}

// AES-CBC Decryption Function (SECURE VERSION)
function decryptData($encryptedData) {
    $key = ENCRYPTION_KEY;
    $method = 'AES-256-CBC';
    
    // Decode Base64 → kembali ke binary (IV + ciphertext)
    $data = base64_decode($encryptedData);
    
    // Ambil panjang IV
    $iv_length = openssl_cipher_iv_length($method);
    
    // Pisahkan IV dan encrypted data
    $iv = substr($data, 0, $iv_length);           // IV ada di depan
    $encrypted = substr($data, $iv_length);       // Data terenkripsi
    
    // Decrypt dengan IV yang sama
    return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
}

// =========================================
// SANITASI INPUT
// Membersihkan input untuk keamanan
// =========================================
function sanitizeInput($data) {
    $data = trim($data);              // Hapus spasi berlebih
    $data = stripslashes($data);      // Hapus backslash
    $data = htmlspecialchars($data);  // Konversi karakter HTML (anti-XSS)
    return $data;
}

// Session Configuration
session_start();

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect if not logged in
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin_login.php');
        exit();
    }
}

// Generate Secure Random Key (untuk keperluan testing/generate key baru)
function generateSecureKey($length = 32) {
    return bin2hex(openssl_random_pseudo_bytes($length / 2));
}
?>