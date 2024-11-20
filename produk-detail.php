<!-- Produk-detail.php -->
<?php
require "./session.php";
require "../koneksi.php";

// Ambil ID produk dari parameter URL
$id = $_GET['id'];

// Ambil data produk dan kategorinya
$query = mysqli_query($conn, "SELECT a.*, b.nama AS nama_kategori 
                              FROM produk a 
                              JOIN kategori b ON a.kategori_id = b.id 
                              WHERE a.id = '$id'");
$data = mysqli_fetch_array($query);

// Ambil data kategori lain
$querykategori = mysqli_query($conn, "SELECT * FROM kategori WHERE id != '$data[kategori_id]'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Detail</title>
    <link rel="stylesheet" href="../bootstrap-4.0.0-dist/css/bootstrap.min.css">
</head>

<body>
    <!-- Navbar -->
    <?php require "./navbar.php"; ?>

    <div class="container mt-5">
        <div class="col-12 col-md-6">
            <h2 class="mb-4">Produk Detail</h2>

            <!-- Proses Update dan Hapus Produk -->
            <?php
            if (isset($_POST['btn-edit-Produk'])) {
                // Ambil data dari form
                $nama = htmlspecialchars($_POST['nama']);
                $kategori = htmlspecialchars($_POST['kategori']);
                $harga = htmlspecialchars($_POST['harga']);
                $detail = htmlspecialchars($_POST['detail']);
                $ketersediaan_stok = (int) htmlspecialchars($_POST['ketersediaan_stok']);
                $fotoLama = htmlspecialchars($_POST['fotoLama']);

                // Upload foto baru jika ada
                $namaFile = $_FILES['foto']['name'];
                $ukuranFile = $_FILES['foto']['size'];
                $error = $_FILES['foto']['error'];
                $tmpName = $_FILES['foto']['tmp_name'];
                $ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
                $namaFileBaru = uniqid() . '.' . $ekstensiGambar;

                // Jika tidak ada file baru
                if ($error === 4) {
                    $queryUpdate = mysqli_query($conn, "UPDATE produk SET 
                        kategori_id='$kategori', nama='$nama', harga='$harga', 
                        foto='$fotoLama', detail='$detail', ketersediaan_stok='$ketersediaan_stok' 
                        WHERE id=$id");
                } else {
                    // Validasi file
                    if (!in_array($ekstensiGambar, ['jpg', 'jpeg', 'png', 'jfif']) || $ukuranFile > 10000000) {
                        $alertMessage = $error === 4 ? "Pilih gambar terlebih dahulu!" :
                            ($ukuranFile > 10000000 ? "Ukuran foto tidak boleh lebih dari 10MB" : "File yang diupload bukan gambar yang didukung");
                        echo "<div class='alert alert-warning mb-3'>$alertMessage</div>";
                    } else {
                        move_uploaded_file($tmpName, "../image/$namaFileBaru");
                        $queryUpdate = mysqli_query($conn, "UPDATE produk SET 
                            kategori_id='$kategori', nama='$nama', harga='$harga', 
                            foto='$namaFileBaru', detail='$detail', ketersediaan_stok='$ketersediaan_stok' 
                            WHERE id=$id");
                    }
                }

                // Tampilkan pesan berhasil/gagal
                if (isset($queryUpdate) && $queryUpdate) {
                    echo "<div class='alert alert-success mb-3'>Produk berhasil diupdate</div>";
                    echo "<meta http-equiv='refresh' content='0; url=./produk.php'>";
                } else if (!$queryUpdate) {
                    echo "<div class='alert alert-danger mb-3'>" . mysqli_error($conn) . "</div>";
                }
            }

            // Proses Hapus Produk
            if (isset($_POST['btn-delete-Produk'])) {
                $queryDelete = mysqli_query($conn, "DELETE FROM produk WHERE id='$id'");
                if ($queryDelete) {
                    echo "<div class='alert alert-danger mb-3'>Produk berhasil dihapus</div>";
                    echo "<meta http-equiv='refresh' content='0; url=./produk.php'>";
                } else {
                    echo "<div class='alert alert-danger mb-3'>" . mysqli_error($conn) . "</div>";
                }
            }
            ?>

            <!-- Form Edit dan Hapus Produk -->
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="fotoLama" value="<?php echo $data['foto']; ?>">

                <!-- Nama Produk -->
                <div class="mb-3">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="nama" value="<?php echo $data['nama']; ?>" class="form-control" required>
                </div>

                <!-- Kategori -->
                <div class="mb-3">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control" required>
                        <option value="<?php echo $data['kategori_id']; ?>"><?php echo $data['nama_kategori']; ?></option>
                        <?php foreach ($querykategori as $datakategori) : ?>
                            <option value="<?php echo $datakategori['id']; ?>"><?php echo $datakategori['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Harga -->
                <div class="mb-3">
                    <label for="price">Harga</label>
                    <input type="number" class="form-control" name="harga" value="<?php echo $data['harga']; ?>" min="0" step="500" required>
                </div>

                <!-- Foto Produk -->
                <div class="mb-3">
                    <label for="foto">Foto</label>
                    <div class="mb-2">
                        <img src="../image/<?php echo $data['foto']; ?>" alt="Foto Produk" style="width: 150px;">
                    </div>
                    <input type="file" name="foto" id="foto" class="form-control">
                </div>

                <!-- Detail -->
                <div class="mb-3">
                    <label for="detail">Detail</label>
                    <textarea name="detail" id="detail" cols="30" rows="5" class="form-control"><?php echo htmlspecialchars_decode($data['detail']); ?></textarea>
                </div>

                <!-- Ketersediaan Stok -->
                <div class="mb-3">
                    <label for="stock">Ketersediaan Stok</label>
                    <input type="number" id="stock" name="ketersediaan_stok" class="form-control" value="<?php echo $data['ketersediaan_stok']; ?>" min="0" required>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between">
                    <a href="./produk.php" class="btn btn-secondary">Cancel</a>
                    <div>
                        <button type="submit" name="btn-edit-Produk" class="btn btn-primary">Save</button>
                        <button type="submit" name="btn-delete-Produk" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus produk ini?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../bootstrap-4.0.0-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/37.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector("#detail")).catch((error) => {
            console.error(error);
        });
    </script>
</body>

</html>
