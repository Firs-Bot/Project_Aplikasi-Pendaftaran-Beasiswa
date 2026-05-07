<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] != 'admin') header("Location: login.php");

if (isset($_POST['tambah_pengumuman'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = date('Y-m-d');
    
    $gambar = $_FILES['gambar']['name'];
    if($gambar != "") {
        $gambar = time() . '_' . $gambar; 
        move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/".$gambar);
    }

    mysqli_query($conn, "INSERT INTO pengumuman (judul, isi, tanggal, gambar) VALUES ('$judul', '$isi', '$tanggal', '$gambar')");
    echo "<script>
        alert('Pengumuman berhasil ditambahkan.');
        window.location = 'admin_pengumuman.php';
    </script>";
}

if (isset($_POST['edit_pengumuman'])) {
    $id = $_POST['id_pengumuman'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    
    $query_update = "UPDATE pengumuman SET judul='$judul', isi='$isi'";

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/".$gambar);
        $query_update .= ", gambar='$gambar'";
    }

    $query_update .= " WHERE id='$id'";
    mysqli_query($conn, $query_update);
    
    echo "<script>
        alert('Pengumuman berhasil diperbarui.');
        window.location = 'admin_pengumuman.php';
    </script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pengumuman WHERE id='$id'");
    header("Location: admin_pengumuman.php?pesan=hapus_sukses");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Pengumuman - Admin</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-area">
                <img src="Logo UNIKU.png" alt="Logo">
                <h1 class="univ-title">ADMIN DASHBOARD</h1>
            </div>
            <nav>
                <a href="admin_dashboard.php" class="btn btn-outline-dark fw-bold me-2">Kelola Beasiswa</a>
                <a href="admin_pengumuman.php" class="btn btn-dark fw-bold me-3">Kelola Pengumuman</a>
                <a href="logout.php" class="btn btn-danger">LOGOUT</a>
            </nav>
        </div>
    </header>

    <div class="container my-5">
        
        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses'): ?>
        <script>
            alert('Pengumuman telah dihapus.');
        </script>
        <?php endif; ?>

        <div class="card mb-5 card-custom border-0 shadow">
            <div class="card-header bg-warning text-dark fw-bold"><h5 class="mb-0">Buat Pengumuman Landing Page</h5></div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Pengumuman</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Isi / Detail</label>
                        <textarea name="isi" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Poster Gambar (Opsional)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="tambah_pengumuman" class="btn btn-primary-custom px-4">Posting Pengumuman</button>
                </form>
            </div>
        </div>

        <h4 class="fw-bold mb-4">Daftar Pengumuman</h4>
        <div class="table-responsive bg-white rounded shadow-sm p-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th width="20%">Poster</th>
                        <th width="35%">Judul</th>
                        <th width="30%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY id DESC");
                    if (mysqli_num_rows($q) > 0) {
                        while ($p = mysqli_fetch_assoc($q)) {
                    ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($p['tanggal'])) ?></td>
                        <td>
                            <?php if(!empty($p['gambar'])): ?>
                                <img src="uploads/<?= $p['gambar'] ?>" alt="Poster" class="img-thumbnail" style="max-height: 80px;">
                            <?php else: ?>
                                <span class="badge bg-secondary">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($p['judul']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id'] ?>">Edit</button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $p['id'] ?>">Hapus</button>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title fw-bold">Edit Pengumuman</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_pengumuman" value="<?= $p['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Judul</label>
                                            <input type="text" name="judul" value="<?= htmlspecialchars($p['judul']) ?>" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Isi</label>
                                            <textarea name="isi" class="form-control" rows="5" required><?= htmlspecialchars($p['isi']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Ganti Poster (Biarkan kosong jika tidak diubah)</label>
                                            <input type="file" name="gambar" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit_pengumuman" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center py-4'>Belum ada data pengumuman.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>&copy; 2026 Universitas Kuningan</footer>
    
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                
                if (confirm('Apakah Anda yakin? Pengumuman ini akan dihapus permanen!')) {
                    window.location.href = 'admin_pengumuman.php?hapus=' + id;
                }
            });
        });
    </script>
</body>
</html>
