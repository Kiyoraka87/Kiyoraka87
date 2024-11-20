<?php
require "./session.php";
require "../koneksi.php";

// Hitung total produk untuk pagination
$stmt = $conn->prepare("SELECT COUNT(*) FROM produk");
$stmt->execute();
$stmt->bind_result($countproduk);
$stmt->fetch();
$stmt->close();

// Pagination
$jumlahDataPerHalaman = 10;
$jumlahHalaman = ceil($countproduk / $jumlahDataPerHalaman);
$halamanAktif = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

// Query produk sesuai halaman saat ini
$stmtproduk = $conn->prepare("SELECT a.*, b.nama AS nama_kategori FROM produk a JOIN kategori b ON a.kategori_id = b.id LIMIT ?, ?");
$stmtproduk->bind_param("ii", $awalData, $jumlahDataPerHalaman);
$stmtproduk->execute();
$result = $stmtproduk->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk</title>
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/fontawesome.min.css">
    <style>
        /* Background gradient */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10vh;
        }
    </style>
</head>

<body>
    <?php require "./navbar.php"; ?>
    <div class="container" style="margin-top: 10vh;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel/" class="text-decoration-none text-muted"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">produk</li>
            </ol>
        </nav>

        <div class="mt-3">
            <h2>List Produk</h2>

            <a href="./Tambah-produk.php" class="btn btn-primary mt-3 mb-2">Tambah produk</a>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary">
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($countproduk < 1) {
                            echo "<tr><td colspan='6' class='text-center'>Data produk tidak tersedia</td></tr>";
                        } else {
                            $number = ($halamanAktif - 1) * $jumlahDataPerHalaman + 1;
                            while ($data = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$number}</td>
                                        <td>{$data['nama']}</td>
                                        <td>{$data['nama_kategori']}</td>
                                        <td>{$data['harga']}</td>
                                        <td>{$data['ketersediaan_stok']} pcs</td>
                                        <td><a href='./produk-detail.php?id={$data['id']}' class='btn btn-info px-2'><i class='fa-solid fa-circle-info fa-xl'></i></a></td>
                                    </tr>";
                                $number++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Section Start -->
    <div class="text-center mb-4" <?= ($countproduk < 1) ? 'hidden' : ''; ?>>
        <?php if ($halamanAktif > 1) : ?>
            <a href="./produk.php?page=<?= $halamanAktif - 1; ?>" class="text-decoration-none text-dark fs-2">&laquo;</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
            <a href="./produk.php?page=<?= $i; ?>" class="text-decoration-none fw-bolder fs-5 <?= ($i == $halamanAktif) ? 'text-light' : 'text-dark'; ?>" 
               style="padding: 3px 10px; background-color: <?= ($i == $halamanAktif) ? 'blue' : 'white'; ?>; border: 1px solid black;">
                <?= $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($halamanAktif < $jumlahHalaman) : ?>
            <a href="./produk.php?page=<?= $halamanAktif + 1; ?>" class="text-decoration-none text-dark fs-2">&raquo;</a>
        <?php endif; ?>
    </div>
    <!-- Pagination Section End -->

    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../fontawesome-free-6.6.0-web/js/all.min.js"></script>
</body>

</html>
