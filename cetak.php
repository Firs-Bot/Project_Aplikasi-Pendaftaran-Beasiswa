<?php
session_start();
include 'koneksi.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak");
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where_clause = "";
$judul_status = "SEMUA STATUS";

if (!empty($status_filter)) {
    $status_safe = mysqli_real_escape_string($conn, $status_filter);
    $where_clause = "WHERE p.status_verifikasi = '$status_safe'";
    $judul_status = strtoupper($status_filter);
}

$query = "SELECT p.*, b.nama_beasiswa FROM pendaftaran p JOIN beasiswa b ON p.beasiswa_id = b.id $where_clause ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);

$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendaftaran Beasiswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; padding: 0; font-size: 18px; }
        .header h3 { margin: 5px 0 0 0; font-size: 14px; font-weight: normal; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .footer-ttd { width: 100%; margin-top: 50px; }
        .ttd-box-right { width: 250px; float: right; text-align: center; }
        .ttd-box-left { width: 250px; float: left; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <h2>UNIVERSITAS KUNINGAN</h2>
        <h3>Biro Administrasi Akademik dan Kemahasiswaan (BAAK)</h3>
        <p style="margin:2px; font-size:10px;">Jl. Cut Nyak Dhien No. 36 A, Cijoho, Kec. Kuningan, Kab. Kuningan, Jawa Barat</p>
    </div>

    <div class="title">
        LAPORAN DATA PENDAFTAR BEASISWA<br>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIM</th>
                <th width="30%">Nama Lengkap</th>
                <th width="30%">Beasiswa</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>';

if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '
            <tr>
                <td class="text-center">' . $no++ . '</td>
                <td class="text-center">' . htmlspecialchars($row['nim']) . '</td>
                <td>' . htmlspecialchars($row['nama_lengkap']) . '</td>
                <td>' . htmlspecialchars($row['nama_beasiswa']) . '</td>
                <td class="text-center">' . htmlspecialchars($row['status_verifikasi']) . '</td>
            </tr>';
    }
} else {
    $html .= '
            <tr>
                <td colspan="5" class="text-center">Tidak ada data pendaftar.</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
    <div class="footer-ttd">
        <div class="ttd-box-left">
            <br>
            Mengetahui,<br>
            Wakil Rektor
            <br><br><br><br><br>
            ( .......................................... )
        </div>
        <div class="ttd-box-right">
            Kuningan, ' . date('d F Y') . '<br>
            Mengetahui,<br>
            Kepala Biro
            <br><br><br><br><br>
            ( .......................................... )
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("Laporan_Beasiswa_Uniku.pdf", array("Attachment" => 0));
?>
