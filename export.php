<?php
require("../koneksi.php");
require "../vendor/autoload.php";

// Fetch transactions
$stmt = $conn->prepare("
    SELECT t.*, p.nama AS nama_produk, k.nama AS kategori_produk
    FROM transaksi t
    JOIN produk p ON t.produk_id = p.id
    JOIN kategori k ON p.kategori_id = k.id
    ORDER BY t.tanggal_transaksi DESC
");

$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);

// Extend TCPDF to customize header and footer
class CustomPDF extends TCPDF {
    // Page footer
    public function Footer() {
        $this->SetY(-15); // Position at 15 mm from bottom
        $this->SetFont('helvetica', 'I', 8); // Set font
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Create a new PDF instance
$pdf = new CustomPDF();

// Set document title
$pdf->SetCreator('TCPDF');
$pdf->SetAuthor('PJBL KEL 6');
$pdf->SetTitle('Laporan Transaksi');

// Add a page
$pdf->AddPage();

// Set font for header
$pdf->SetFont('helvetica', 'B', 16);

// Company name in header
$pdf->Cell(0, 10, 'PJBL KEL 6', 0, 1, 'C');
$pdf->Ln(5);

// Title of the report
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Laporan Transaksi', 0, 1, 'C');
$pdf->Ln(5);

// Adjust column widths
$widths = [
    'no' => 10,       // Nomor urut
    'produk' => 50,   // Nama produk
    'kategori' => 40, // Kategori produk
    'stok' => 20,     // Jumlah stok
    'harga' => 35,    // Total harga
    'tanggal' => 35   // Tanggal transaksi
];

// Set table header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell($widths['no'], 10, 'No.', 1, 0, 'C');
$pdf->Cell($widths['produk'], 10, 'Produk', 1, 0, 'C');
$pdf->Cell($widths['kategori'], 10, 'Kategori', 1, 0, 'C');
$pdf->Cell($widths['stok'], 10, 'Stok', 1, 0, 'C');
$pdf->Cell($widths['harga'], 10, 'Harga', 1, 0, 'C');
$pdf->Cell($widths['tanggal'], 10, 'Tanggal', 1, 1, 'C');

// Set table content
$pdf->SetFont('helvetica', '', 10);
foreach ($transactions as $index => $transaction) {
    $pdf->Cell($widths['no'], 10, $index + 1, 1, 0, 'C');
    $pdf->Cell($widths['produk'], 10, $transaction['nama_produk'], 1, 0, 'C');
    $pdf->Cell($widths['kategori'], 10, $transaction['kategori_produk'], 1, 0, 'C');
    $pdf->Cell($widths['stok'], 10, $transaction['jumlah'] . ' pcs', 1, 0, 'C');
    $pdf->Cell($widths['harga'], 10, 'Rp ' . number_format($transaction['total_harga'], 2, ',', '.'), 1, 0, 'C');
    $tanggal_transaksi = date('d-m-Y', strtotime($transaction['tanggal_transaksi'])); // Tanggal saja untuk menghemat ruang
    $pdf->Cell($widths['tanggal'], 10, $tanggal_transaksi, 1, 1, 'C');
}

// Output PDF to browser
$pdf->Output();
exit;
?>
