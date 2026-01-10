<?php
/**
 * config.php - Main Configuration
 * Studi Kasus: UMKM Siti Furniture
 */

// ============================================
// LOAD KEYS FROM SEPARATE FILE
// ============================================
require_once __DIR__ . '/keys.php';

// ============================================
// DATABASE CONNECTION
// ============================================
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die('Database Error: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}

// ============================================
// ENCRYPTION FUNCTIONS (AES-256-CBC)
// ============================================
function encryptData($data) {
    $method = 'AES-256-CBC';
    $key = ENCRYPTION_KEY;
    
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    
    return base64_encode($iv . $encrypted);
}

function decryptData($encryptedData) {
    $method = 'AES-256-CBC';
    $key = ENCRYPTION_KEY;
    
    $data = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length($method);
    
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    
    return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
}

// ============================================
// INPUT SANITIZATION
// ============================================
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// ============================================
// SESSION & AUTH
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin_login.php');
        exit;
    }
}

// ============================================
// UTILITIES
// ============================================
function generateSecureKey($length = 32) {
    return bin2hex(openssl_random_pseudo_bytes($length / 2));
}
?>