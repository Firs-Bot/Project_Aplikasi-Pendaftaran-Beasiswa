<?php
include 'koneksi.php';

header('Content-Type: application/json');

if (isset($_GET['nim'])) {
    $nim = mysqli_real_escape_string($conn, $_GET['nim']);
    
    if (empty($nim)) {
        echo json_encode(['status' => 'error', 'message' => 'NIM kosong']);
        exit;
    }

    $query = mysqli_query($conn, "SELECT nama FROM master_mahasiswa WHERE nim = '$nim'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        echo json_encode([
            'status' => 'success',
            'nama' => $row['nama']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'NIM tidak ditemukan di database Universitas'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
