<?php
/**
 * keys.example.php - Template Konfigurasi Keys
 * 
 * Copy file ini menjadi keys.php dan isi dengan konfigurasi Anda
 */

// ============================================
// DATABASE CREDENTIALS
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'siti_furniture');

// ============================================
// ENCRYPTION KEY (AES-256 - 32 characters)
// ============================================
// Generate key baru dengan:
// php -r "echo bin2hex(random_bytes(16));"
define('ENCRYPTION_KEY', 'REPLACE_WITH_YOUR_32_CHARACTER_KEY');
?>