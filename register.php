<?php
include 'koneksi.php';
if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $pass = $_POST['password'];


    $cek = mysqli_query($conn, "SELECT * FROM users WHERE nim_username='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIM sudah terdaftar!');</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (nama_lengkap, nim_username, password, role) VALUES ('$nama', '$nim', '$pass', 'mahasiswa')");
        if ($insert) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar - Beasiswa UNIKU</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-area">
                <img src="Logo UNIKU.png" alt="Logo UNIKU">
                <h1 class="univ-title">UNIVERSITAS KUNINGAN</h1>
            </div>
        </div>
    </header>
    <div class="container d-flex justify-content-center align-items-center flex-grow-1">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-4">DAFTAR MAHASISWA</h3>
            <form method="POST">
                <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                <div class="mb-3"><label>NIM</label><input type="text" name="nim" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <button type="submit" name="register" class="btn btn-primary-custom w-100">DAFTAR</button>
            </form>
            <div class="text-center mt-3"><a href="login.php">Sudah punya akun? Login</a></div>
        </div>
    </div>
    <footer>&copy; 2026 Universitas Kuningan</footer>
</body>
</html>