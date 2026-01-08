<?php
// test_password.php
$password = 'password';
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "Verify: " . (password_verify($password, $hash) ? '✅ COCOK' : '❌ TIDAK COCOK') . "<br>";

// Test koneksi database
require_once 'config.php';
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT username, password FROM admin_users WHERE username = ?");
$username = 'admin';
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<br>Username dari DB: " . $user['username'] . "<br>";
    echo "Password Hash dari DB: " . $user['password'] . "<br>";
    echo "Verifikasi DB: " . (password_verify($password, $user['password']) ? '✅ COCOK' : '❌ TIDAK COCOK');
} else {
    echo "<br>❌ User tidak ditemukan di database!";
}
?>