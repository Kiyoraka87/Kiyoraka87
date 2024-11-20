<?php
include("session.php");
require "../koneksi.php";

$querykategori = mysqli_query($conn, "SELECT * FROM kategori");
$jumlahkategori = mysqli_num_rows($querykategori);
$queryproduk = mysqli_query($conn, "SELECT * FROM produk");
$jumlahproduk = mysqli_num_rows($queryproduk);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Toko Online</title>
    <!-- Link to Bootstrap 4 CSS -->
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <!-- Link to FontAwesome CSS -->
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
</head>
<style>
    body {
        background: linear-gradient(135deg, #2193b0, #6dd5ed); /* Warna sama seperti login */
        font-family: 'Arial', sans-serif;
        color: #333;
    }

    .kotak {
        border: solid;
    }

    .summary-box {
        border-radius: 15px;
        padding: 25px;
        margin: 15px 0;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .summary-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .summary-kategori {
        background: #ffecb3;
    }

    .summary-produk {
        background: #b2dfdb;
    }

    .summary-box h3 {
        margin-top: 10px;
        font-size: 22px;
        color: #333;
    }

    .summary-box p {
        font-size: 16px;
        color: #666;
    }

    .summary-box a {
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .summary-box a:hover {
        color: #0056b3;
    }

    .breadcrumb {
        background-color: transparent;
        font-size: 14px;
    }

    h2 {
        font-weight: bold;
    }
</style>
<body>
    <?php require "navbar.php"; ?>

    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-home"></i> Dashboard
                </li>
            </ol>
        </nav>
        <h2>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Kelola toko Anda dengan mudah melalui dashboard ini.</p>

        <div class="row mt-5">
            <!-- Summary Kategori -->
            <div class="col-lg-6 col-md-6">
                <div class="summary-box summary-kategori">
                    <i class="fas fa-th-list fa-4x"></i>
                    <h3>Kategori</h3>
                    <p><?php echo $jumlahkategori; ?> Kategori Tersedia</p>
                    <p><a href="kategori.php">Lihat Detail</a></p>
                </div>
            </div>

            <!-- Summary Produk -->
            <div class="col-lg-6 col-md-6">
                <div class="summary-box summary-produk">
                    <i class="fas fa-boxes fa-4x"></i>
                    <h3>Produk</h3>
                    <p><?php echo $jumlahproduk; ?> Produk Dijual</p>
                    <p><a href="produk.php">Lihat Detail</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery (required for Bootstrap 4) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include FontAwesome JS -->
    <script src="../fontawesome-free-6.6.0-web/js/all.min.js"></script>
    <!-- Include Bootstrap 4 JavaScript -->
    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>