<?php
// Auto-select product if coming from product page
$selectedProduct = isset($_GET['product']) ? $_GET['product'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan - Siti Furniture</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="admin_login.php" class="admin-link">🔐 Admin Login</a>
            <h1>🪑 Siti Furniture</h1>
            <p>Mebel Kayu Berkualitas untuk Rumah Anda</p>
        </header>

        <nav>
            <a href="beranda.php" class="nav-btn">Beranda</a>
            <a href="produk.php" class="nav-btn">Produk</a>
            <a href="pemesanan.php" class="nav-btn active">Pemesanan</a>
            <a href="tentang.php" class="nav-btn">Tentang</a>
        </nav>

        <div class="content">
            <h2 style="color: #8B4513; margin-bottom: 20px;">Form Pemesanan</h2>

            <div class="order-form">
                <form id="orderForm">
                    <div class="input-group">
                        <label>Nama Lengkap: *</label>
                        <input type="text" id="customerName" name="name" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="input-group">
                        <label>Nomor Telepon: *</label>
                        <input type="text" id="customerPhone" name="phone" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="input-group">
                        <label>Alamat Lengkap: *</label>
                        <textarea id="customerAddress" name="address" placeholder="Masukkan alamat pengiriman lengkap" required></textarea>
                    </div>

                    <div class="input-group">
                        <label>Pilih Produk: *</label>
                        <select id="productSelect" required>
                            <option value="">-- Pilih Produk --</option>
                            <option value="K001|Kursi Makan Minimalis|450000" <?php echo ($selectedProduct == 'K001') ? 'selected' : ''; ?>>K001 - Kursi Makan Minimalis (Rp 450.000)</option>
                            <option value="K002|Kursi Teras Klasik|550000" <?php echo ($selectedProduct == 'K002') ? 'selected' : ''; ?>>K002 - Kursi Teras Klasik (Rp 550.000)</option>
                            <option value="K003|Kursi Kerja Ergonomis|650000" <?php echo ($selectedProduct == 'K003') ? 'selected' : ''; ?>>K003 - Kursi Kerja Ergonomis (Rp 650.000)</option>
                            <option value="M001|Meja Makan 4 Kursi|2500000" <?php echo ($selectedProduct == 'M001') ? 'selected' : ''; ?>>M001 - Meja Makan 4 Kursi (Rp 2.500.000)</option>
                            <option value="M002|Meja Belajar Minimalis|850000" <?php echo ($selectedProduct == 'M002') ? 'selected' : ''; ?>>M002 - Meja Belajar Minimalis (Rp 850.000)</option>
                            <option value="M003|Meja Tamu Ukir|1200000" <?php echo ($selectedProduct == 'M003') ? 'selected' : ''; ?>>M003 - Meja Tamu Ukir (Rp 1.200.000)</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Jumlah: *</label>
                        <input type="number" id="quantity" name="quantity" min="" value="" required>
                    </div>

                    <button type="submit" class="btn" id="submitBtn">Proses Pesanan</button>
                </form>

                <div id="orderResult"></div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 Siti Furniture. Website Katalog dan Pemesanan Online.</p>
        </div>
    </div>

    <script>
        // Process Order dengan AJAX
        document.getElementById('orderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const resultDiv = document.getElementById('orderResult');
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            
            // Get form data
            const name = document.getElementById('customerName').value;
            const phone = document.getElementById('customerPhone').value;
            const address = document.getElementById('customerAddress').value;
            const product = document.getElementById('productSelect').value;
            const quantity = document.getElementById('quantity').value;
            
            if (!product) {
                alert('Mohon pilih produk!');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Proses Pesanan';
                return;
            }
            
            const [productCode, productName, price] = product.split('|');
            
            // Prepare form data
            const formData = new FormData();
            formData.append('name', name);
            formData.append('phone', phone);
            formData.append('address', address);
            formData.append('product_code', productCode);
            formData.append('product_name', productName);
            formData.append('quantity', quantity);
            formData.append('price', price);
            
            try {
                const response = await fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
    const totalPrice = parseInt(result.data.price) * parseInt(result.data.quantity);
    
    resultDiv.innerHTML = `
        <div class="result-box success-box" style="margin-top: 30px;">
            <h3 style="color: #155724; margin-bottom: 20px;">✅ Pesanan Berhasil Diproses!</h3>
            
            <table style="background: white;">
                <tr><td><strong>Nama:</strong></td><td>${result.data.name}</td></tr>
                <tr><td><strong>Telepon:</strong></td><td>${result.data.phone}</td></tr>
                <tr><td><strong>Alamat:</strong></td><td>${result.data.address}</td></tr>
                <tr><td><strong>Produk:</strong></td><td>${result.data.product_name}</td></tr>
                <tr><td><strong>Jumlah:</strong></td><td>${result.data.quantity} unit</td></tr>
                <tr><td><strong>Total Harga:</strong></td><td>Rp ${totalPrice.toLocaleString('id-ID')}</td></tr>
            </table>
            
            <p style="margin-top: 20px; color: #155724;"><strong>🔒 Data Anda telah dienkripsi dengan AES-256-CBC dan tersimpan dengan aman!</strong></p>
            <p style="margin-top: 10px;"><strong>Terima kasih telah memesan di Siti Furniture! Pesanan Anda akan segera diproses.</strong></p>
        </div>
    `;
    
    // Reset form
    document.getElementById('orderForm').reset();

                    
                    // Scroll to result
                    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    resultDiv.innerHTML = `
                        <div class="result-box error-box" style="margin-top: 30px;">
                            <h3>❌ Gagal Memproses Pesanan</h3>
                            <p style="margin-top: 10px;">${result.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="result-box error-box" style="margin-top: 30px;">
                        <h3>❌ Terjadi Kesalahan</h3>
                        <p style="margin-top: 10px;">Mohon coba lagi nanti. Error: ${error.message}</p>
                    </div>
                `;
            }
            
            // Enable button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Proses Pesanan';
        });
    </script>
</body>
</html>