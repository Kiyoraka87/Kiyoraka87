<?php
require "./session.php";
require "../koneksi.php";

// Sanitasi input halaman
$halamanAktif = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Ambil jumlah total kategori
$queryCount = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori");
$countResult = mysqli_fetch_assoc($queryCount);
$countkategori = $countResult['total'] ?? 0;

// Pagination
$jumlahDataPerHalaman = 10;
$jumlahHalaman = ceil($countkategori / $jumlahDataPerHalaman);
$halamanAktif = max(1, min($halamanAktif, $jumlahHalaman)); // Validasi halaman aktif
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

// Ambil data kategori sesuai pagination
$stmt = $conn->prepare("SELECT * FROM kategori LIMIT ?, ?");
$stmt->bind_param("ii", $awalData, $jumlahDataPerHalaman);
$stmt->execute();
$querykategoriNew = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kategori</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
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

    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel/" class="text-decoration-none text-muted"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kategori</li>
            </ol>
        </nav>

        <div class="mt-3">
            <h2>Kategori List</h2>
            <a href="./tambah-kategori.php" class="btn btn-primary mt-3 mb-2">Tambah Kategori</a>

            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary">
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($countkategori < 1) : ?>
                            <tr>
                                <td colspan="3" class="text-center">Data kategori tidak tersedia</td>
                            </tr>
                        <?php else : ?>
                            <?php
                            $number = 1;
                            foreach ($querykategoriNew as $datakategori) :
                                $nomorKategori = ($halamanAktif - 1) * $jumlahDataPerHalaman + $number;
                            ?>
                                <tr>
                                    <td><?php echo $nomorKategori; ?></td>
                                    <td><?php echo htmlspecialchars($datakategori['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                    <a href="./kategori-detail.php?p=<?php echo $datakategori['id']; ?>" class="btn btn-info px-2" title="Detail"><i class="fa-solid fa-circle-info"></i></a>
                                    </td>
                                </tr>
                            <?php
                                $number++;
                            endforeach;
                            ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <?php if ($countkategori > 0) : ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item <?php echo ($halamanAktif == 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="./kategori.php?page=<?php echo $halamanAktif - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
                        <li class="page-item <?php echo ($i == $halamanAktif) ? 'active' : ''; ?>">
                            <a class="page-link" href="./kategori.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Next Button -->
                    <li class="page-item <?php echo ($halamanAktif == $jumlahHalaman) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="./kategori.php?page=<?php echo $halamanAktif + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- FontAwesome JS -->
    <script src="../fontawesome-free-6.6.0-web/js/all.min.js"></script>
    <!-- Bootstrap 4 JavaScript -->
    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
