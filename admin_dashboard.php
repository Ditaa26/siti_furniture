<?php
require_once 'config.php';
requireAdmin();

// Get all orders with decryption
$conn = getDBConnection();

$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decrypt sensitive data
        $row['customer_name_decrypted'] = decryptData($row['customer_name']);
        $row['customer_phone_decrypted'] = decryptData($row['customer_phone']);
        $row['customer_address_decrypted'] = decryptData($row['customer_address']);
        
        $orders[] = $row;
    }
}

// Get statistics
$totalOrders = count($orders);
$totalRevenue = 0;

foreach ($orders as $order) {
    $totalRevenue += $order['total_price'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Siti Furniture</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Header Responsif */
        .header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 1.5rem;
            margin: 0;
            flex: 1 1 auto;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .user-info strong {
            font-size: 0.95rem;
        }

        .user-info span {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Content Area */
        .content {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Stats Grid - Responsif */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 2rem;
            margin: 0 0 10px 0;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        /* Card */
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.3rem;
        }

        /* Table Container dengan Scroll Horizontal */
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 0.9rem;
        }

        table th {
            background: #8B4513;
            color: white;
            font-weight: bold;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }

        table tr:hover {
            background: #f9f9f9;
        }

        table td {
            word-wrap: break-word;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            margin-top: 40px;
        }

        .footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        /* Mobile First - Tablet (768px ke atas) */
        @media (min-width: 768px) {
            .header {
                padding: 20px 30px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .content {
                padding: 30px;
            }

            .stat-card h3 {
                font-size: 2.5rem;
            }

            .card {
                padding: 30px;
            }

            .card h2 {
                font-size: 1.5rem;
            }

            table th, table td {
                padding: 15px;
                font-size: 1rem;
            }
        }

        /* Desktop (1024px ke atas) */
        @media (min-width: 1024px) {
            .header {
                padding: 25px 40px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                gap: 20px;
            }

            .stat-card {
                padding: 30px;
            }
        }

        /* Mobile Extra Small (dibawah 480px) */
        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.2rem;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .user-info {
                text-align: left;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card h3 {
                font-size: 1.8rem;
            }

            .card {
                padding: 15px;
            }

            .card h2 {
                font-size: 1.1rem;
            }

            table {
                min-width: 600px;
                font-size: 0.85rem;
            }

            table th, table td {
                padding: 10px 8px;
            }
        }

        /* Print Styles */
        @media print {
            .header-right, .btn {
                display: none;
            }

            .stat-card {
                break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Admin Dashboard - Siti Furniture</h1>
            <div class="header-right">
                <div class="user-info">
                    <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>
                    <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
                <a href="admin_logout.php" class="btn">Logout</a>
            </div>
        </div>

        <div class="content">
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $totalOrders; ?></h3>
                    <p>Total Pesanan</p>
                </div>
                <div class="stat-card">
                    <h3>Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <h2>📋 Daftar Pesanan (Data Terdekripsi)</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        Belum ada pesanan
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name_decrypted']); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_phone_decrypted']); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_address_decrypted']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><strong>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 Siti Furniture Admin Panel</p>
        </div>
    </div>
</body>
</html>