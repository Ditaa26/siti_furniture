# Pengamanan Data Pemesanan Pelanggan Menggunakan Algoritma AES-256-CBC  
(Studi Kasus: UMKM Siti Furniture)    
<img width="445" height="298" alt="Screenshot 2026-01-08 205523" src="https://github.com/user-attachments/assets/325f4b17-8046-4576-8960-69ca340594a7" />

Repository ini berisi source code aplikasi berbasis web yang digunakan sebagai pendukung penelitian dengan judul *Pengamanan Data Pemesanan Pelanggan
Menggunakan Algoritma AES-256-CBC (Studi Kasus pada UMKM Siti Furniture)*. Aplikasi ini dirancang untuk mengamankan data pemesanan pelanggan dengan
menerapkan algoritma kriptografi AES-256-CBC pada proses penyimpanan data ke dalam basis data. 

## Tujuan
- Mengimplementasikan algoritma AES-256-CBC untuk pengamanan data pelanggan
- Meningkatkan kerahasiaan data pemesanan pada UMKM
- Mendukung pembuktian konsep (prototype) pada penelitian/jurnal
- Mendemonstrasikan penerapan kriptografi dalam aplikasi web nyata

## Teknologi yang Digunakan
- **Backend:** PHP 
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Server:** XAMPP (Apache + MySQL)
- **Enkripsi:** OpenSSL Library (AES-256-CBC)

## Fitur Utama
- Manajemen data pemesanan pelanggan
- Enkripsi data sensitif menggunakan AES-256-CBC
- Penyimpanan data terenkripsi ke database MySQL
- Dekripsi data untuk tampilan admin
- Sistem autentikasi admin
- Dashboard admin untuk monitoring pesanan
- Analisis keamanan dengan Entropi Shannon

## **Data yang Dienkripsi:**
- Nama pelanggan
- Nomor telepon
- Alamat pengiriman

## **Algoritma: AES-256-CBC**
- **Key Size:** 256 bit (32 karakter)
- **Block Size:** 128 bit
- **Mode:** CBC (Cipher Block Chaining)
- **IV:** Random (16 bytes) untuk setiap enkripsi

  
  






