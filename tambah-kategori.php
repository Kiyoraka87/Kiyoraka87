<?php
require "./session.php";
require "../koneksi.php";

// Pesan untuk validasi
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $namaKategori = isset($_POST['nama']) ? trim($_POST['nama']) : "";

    // Validasi input
    if (empty($namaKategori)) {
        $message = "Nama kategori tidak boleh kosong!";
    } else {
        // Cek apakah kategori sudah ada di database
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM kategori WHERE nama = ?");
        $stmtCheck->bind_param("s", $namaKategori);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close(); 

        if ($count > 0) {
            // Jika kategori sudah ada
            $message = "Kategori sudah ada, silakan pilih nama lain.";    
        } else {
            // Query untuk menyimpan data ke database
            $stmt = $conn->prepare("INSERT INTO kategori (nama) VALUES (?)");
            $stmt->bind_param("s", $namaKategori);

            if ($stmt->execute()) {
                // Setelah berhasil tambah data, redirect ke kategori.php
                header("Location: kategori.php?status=success");
                exit();
            } else {
                $message = "Terjadi kesalahan saat menambahkan kategori!";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
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
                <li class="breadcrumb-item"><a href="./kategori.php" class="text-decoration-none text-muted">Kategori</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Kategori</li>
            </ol>
        </nav>

        <div class="mt-3">
            <h2>Tambah Kategori</h2>

            <?php if (!empty($message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="tambah-kategori.php" method="POST">
                <div class="form-group">
                    <label for="namaKategori">Nama Kategori</label>
                    <input type="text" class="form-control" id="namaKategori" name="nama" placeholder="Masukkan nama kategori" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Tambah</button>
                <a href="./kategori.php" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>

    <!-- FontAwesome JS -->
    <script src="../fontawesome-free-6.6.0-web/js/all.min.js"></script>
    <!-- Bootstrap 4 JavaScript -->
    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
