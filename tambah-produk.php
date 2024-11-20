<?php
require "./session.php";
require "../koneksi.php";

// Pesan untuk validasi
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $namaProduk = isset($_POST['nama_produk']) ? trim($_POST['nama_produk']) : "";
    $kategoriId = isset($_POST['kategori_id']) ? $_POST['kategori_id'] : "";
    $harga = isset($_POST['harga']) ? trim($_POST['harga']) : "";
    $stok = isset($_POST['ketersediaan_stok']) ? $_POST['ketersediaan_stok'] : "";
    $detail = isset($_POST['detail']) ? trim($_POST['detail']) : null; // Default null jika tidak diisi

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // Konfigurasi untuk file upload
    $target_dir = "../image/";
    $fotoUploaded = !empty($_FILES["foto"]["name"]);
    $new_name = null;

    if ($fotoUploaded) {
        $nama_file = basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $image_size = $_FILES["foto"]["size"];
        $random_name = generateRandomString(20);
        $new_name = $random_name . "." . $imageFileType;

        if (!in_array($imageFileType, ["jpg", "png"])) {
            $message = "Format file yang diperbolehkan hanya JPG dan PNG!";
        } elseif ($image_size > 1000000) { // 1000 KB
            $message = "Ukuran file gambar tidak boleh lebih dari 1000 KB!";
        } elseif (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)) {
            $message = "Terjadi kesalahan saat mengunggah file gambar!";
        }
    }

    // Validasi input
    if (empty($namaProduk) || empty($kategoriId) || empty($harga) || empty($stok)) {
        $message = "Semua kolom harus diisi!";
    } elseif (empty($message)) {
        // Query untuk menyimpan data produk
        $stmt = $conn->prepare("INSERT INTO produk (nama, kategori_id, harga, ketersediaan_stok, detail, foto) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $namaProduk, $kategoriId, $harga, $stok, $detail, $new_name);

        if ($stmt->execute()) {
            // Setelah berhasil tambah data, redirect ke produk.php
            header("Location: produk.php?status=success");
            exit();
        } else {
            $message = "Terjadi kesalahan saat menambahkan produk!";
        }
        $stmt->close();
    }
}

// Ambil data kategori untuk dropdown
$queryKategori = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
</head>
<body>
    <?php require "./navbar.php"; ?>

    <div class="container" style="margin-top: 10vh;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel/" class="text-decoration-none text-muted"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="./produk.php" class="text-decoration-none text-muted">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Produk</li>
            </ol>
        </nav>

        <div class="mt-3">
            <h2>Tambah Produk</h2>

            <?php if (!empty($message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="tambah-produk.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="form-group">
                    <label for="namaProduk">Nama Produk</label>
                    <input type="text" class="form-control" id="namaProduk" name="nama_produk" placeholder="Masukkan nama produk" required>
                </div>
                
                <div class="form-group">
                    <label for="kategoriId">Kategori Produk</label>
                    <select class="form-control" id="kategoriId" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($row = mysqli_fetch_assoc($queryKategori)) : ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" placeholder="Masukkan harga produk" required>
                </div>

                <div class="form-group">
                    <label for="foto">Foto</label>
                    <input type="file" name="foto" id="foto" class="form-control">
                    <small class="text-muted">Format yang diperbolehkan: JPG, PNG. Ukuran maksimum: 1000 KB. (Opsional)</small>
                </div>

                <div class="form-group">
                    <label for="detail">Detail</label>
                    <textarea name="detail" id="detail" cols="30" rows="10" class="form-control" placeholder="Masukkan Detail Produk (Opsional)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="stok">Ketersediaan Stok (dalam pcs)</label>
                    <input type="number" name="ketersediaan_stok" id="ketersediaan_stok" class="form-control" placeholder="Masukkan jumlah stok dalam pcs" required>
                </div>


                <button type="submit" class="btn btn-primary mt-3">Tambah</button>
                <a href="./produk.php" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>

    <!-- FontAwesome JS -->
    <script src="../fontawesome-free-6.6.0-web/js/all.min.js"></script>
    <!-- Bootstrap 4 JavaScript -->
    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
