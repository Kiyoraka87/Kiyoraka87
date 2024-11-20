<?php
require "./session.php";
require "../koneksi.php";

// Mengambil data produk
$stmt = $conn->prepare("
    SELECT produk.id, produk.nama, produk.harga, produk.ketersediaan_stok, kategori.nama AS kategori
    FROM produk
    LEFT JOIN kategori ON produk.kategori_id = kategori.id
");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan']; // Keterangan sumber transaksi (penjelasan tentang transaksi)
    
    // Fetch product details (harga dan stok tersedia)
    $stmt = $conn->prepare("SELECT harga, ketersediaan_stok FROM produk WHERE id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $stmt->bind_result($harga, $stok);
    $stmt->fetch();
    $stmt->close();

    // Periksa apakah jumlah yang diminta valid
    if ($jumlah == 0) {
        $error = "Jumlah transaksi tidak boleh nol!";
    } else {
        $total_harga = $harga * abs($jumlah); // Menghitung total harga berdasarkan nilai absolut jumlah

        // Menangani transaksi pengurangan atau penambahan stok
        if ($jumlah < 0) {
            // Pengurangan stok
            if (abs($jumlah) > $stok) {
                $error = "Jumlah melebihi stok yang tersedia!";
            } else {
                // Insert ke tabel transaksi
                $stmt = $conn->prepare("INSERT INTO transaksi (produk_id, jumlah, total_harga, keterangan) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iids", $produk_id, $jumlah, $total_harga, $keterangan);
                $stmt->execute();
                $stmt->close();

                // Update stok produk
                $new_stock = $stok + $jumlah; // Kurangi stok
                $stmt = $conn->prepare("UPDATE produk SET ketersediaan_stok = ? WHERE id = ?");
                $stmt->bind_param("ii", $new_stock, $produk_id);
                $stmt->execute();
                $stmt->close();

                $success = "Transaksi pengurangan stok berhasil disimpan!";
            }
        } elseif ($jumlah > 0) {
            // Penambahan stok
            // Insert ke tabel transaksi
            $stmt = $conn->prepare("INSERT INTO transaksi (produk_id, jumlah, total_harga, keterangan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iids", $produk_id, $jumlah, $total_harga, $keterangan);
            $stmt->execute();
            $stmt->close();

            // Update stok produk
            $new_stock = $stok + $jumlah; // Tambah stok
            $stmt = $conn->prepare("UPDATE produk SET ketersediaan_stok = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_stock, $produk_id);
            $stmt->execute();
            $stmt->close();

            $success = "Transaksi penambahan stok berhasil disimpan!";
        } else {
            $error = "Jumlah transaksi tidak valid!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <style>
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
    <div class="container">
        <h2>Transaksi</h2>

        <!-- Menampilkan pesan error dan sukses -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <!-- Form untuk transaksi -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="produk_id">Pilih Produk</label>
                <select name="produk_id" id="produk_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id']; ?>">
                            <?= $product['nama']; ?> (Kategori: <?= $product['kategori']; ?>, Stok: <?= $product['ketersediaan_stok']; ?>, Harga: <?= $product['harga']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="-9999" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan Transaksi</label>
                <textarea name="keterangan" id="keterangan" class="form-control" required placeholder="Masukkan detail transaksi"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </form>
    </div>

    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
