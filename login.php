<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE nim_username='$username' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['nama'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nim'] = $data['nim_username'];

        if ($data['role'] == 'admin') header("Location: admin_dashboard.php");
        else if ($data['role'] == 'mahasiswa') header("Location: mhs_dashboard.php");
    } else {
        echo "<script>alert('Username atau Password Salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Beasiswa UNIKU</title>
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
            <div>
                <a href="index.php" class="btn btn-outline-dark fw-bold">Home</a>
            </div>
        </div>
    </header>

    <div class="container d-flex justify-content-center align-items-center flex-grow-1">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-4" style="color: var(--accent-blue);">LOGIN SISTEM</h3>
            <form method="POST">
                <div class="mb-3">
                    <label>NIM / Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary-custom w-100">MASUK</button>
            </form>
            <hr>
            <div class="text-center">
                <p>Belum punya akun?</p>
                <a href="register.php" class="btn btn-outline-warning w-100">Daftar Mahasiswa</a>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2026 Universitas Kuningan
    </footer>
</body>
</html>
