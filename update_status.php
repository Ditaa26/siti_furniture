<?php
require_once 'config.php';
requireAdmin();

$conn = getDBConnection();

$id = (int)$_POST['order_id'];
$newStatus = $_POST['status'];

$allowed = ['pending','processing','completed','cancelled'];

if (!in_array($newStatus, $allowed)) {
    die('Status tidak valid');
}

// Ambil status lama
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$old = $stmt->get_result()->fetch_assoc();

if (!$old) {
    die('Pesanan tidak ditemukan');
}

// VALIDASI
if (in_array($old['status'], ['completed','cancelled'])) {
    die('Pesanan sudah final dan tidak dapat diubah');
}

// Update status
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $id);
$stmt->execute();

header("Location: admin_dashboard.php");
