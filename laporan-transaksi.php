<?php
require "./session.php";
require "../koneksi.php";

// Fetch transactions
$stmt = $conn->prepare("
    SELECT t.*, p.nama AS nama_produk 
    FROM transaksi t 
    JOIN produk p ON t.produk_id = p.id 
    ORDER BY t.tanggal_transaksi DESC
");
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel/" class="text-decoration-none text-muted"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
            </ol>
        </nav>
        <h2>Laporan Transaksi</h2>

        <!-- Export PDF Button -->
        <a href="export.php" class="btn btn-success mb-3">Export to PDF</a>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Tanggal Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transactions) < 1): ?>
                        <tr><td colspan="5" class="text-center">Tidak ada transaksi</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $index => $transaction): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= $transaction['nama_produk']; ?></td>
                                <td><?= $transaction['jumlah']; ?></td>
                                <td><?= $transaction['total_harga']; ?></td>
                                <td><?= $transaction['tanggal_transaksi']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
