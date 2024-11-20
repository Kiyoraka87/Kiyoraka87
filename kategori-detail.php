<?php 
    require "session.php";
    require "../koneksi.php";

    $id = $_GET['p'];

    $query = mysqli_query($conn, "SELECT * FROM kategori WHERE id='$id'");
    $data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kategori</title>
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
</head>
<body>
    <?php require "navbar.php"; ?>

    <div class="container mt-5">
        <h2>Detail Kategori</h2>

        <div class="col-12 col-md-6">
            <form action="" method="post">
                <div>
                    <label for="kategori">Kategori</label>
                    <input type="text" name="kategori" id="kategori" class="form-control" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                </div>
                
                <div class="mt-5 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary" name="editBtn">Edit</button>
                    <button type="submit" class="btn btn-danger" name="deleteBtn">Delete</button>
                </div>
            </form>

            <?php
                if (isset($_POST['editBtn'])){
                    $kategori = htmlspecialchars($_POST['kategori']);

                    // Pengecekan kategori kosong
                    if (empty($kategori)) {
                        echo "<div class='alert alert-warning mt-3' role='alert'>Nama kategori tidak boleh kosong!</div>";
                    }
                    // Jika kategori sama dengan yang lama, redirect
                    elseif ($data['nama'] == $kategori) {
                        header("Location: kategori.php");
                        exit();
                    }
                    else {
                        // Cek apakah kategori sudah ada di database
                        $query = mysqli_query($conn, "SELECT * FROM kategori WHERE nama='$kategori'");
                        $jumlahData = mysqli_num_rows($query);

                        if ($jumlahData > 0) {
                            echo "<div class='alert alert-warning mt-3' role='alert'>Kategori sudah ada, silakan pilih nama lain!</div>";
                        } else {
                            // Query untuk update kategori
                            $querySimpan = mysqli_query($conn, "UPDATE kategori SET nama='$kategori' WHERE id='$id'");
                              if ($querySimpan) {
                                echo "<div class='alert alert-success mt-3' role='alert'>Kategori berhasil diupdate!</div>";
                                echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                            } else {
                                echo "<div class='alert alert-danger mt-3' role='alert'>Terjadi kesalahan saat mengupdate kategori!</div>";
                            }
                        }
                    }
                }

                if (isset($_POST['deleteBtn'])) {
                    // Query untuk delete kategori
                    $queryDelete = mysqli_query($conn, "DELETE FROM kategori WHERE id='$id'");

                    if ($queryDelete) {
                        echo "<div class='alert alert-success mt-3' role='alert'>Kategori berhasil dihapus!</div>";
                        echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                    } else {
                        echo "<div class='alert alert-danger mt-3' role='alert'>Terjadi kesalahan saat menghapus kategori!</div>";
                    }
                }
            ?>
        </div>
    </div>
    
    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
