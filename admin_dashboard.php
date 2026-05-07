<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] != 'admin') header("Location: login.php");

if (isset($_POST['tambah_beasiswa'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_beasiswa']);
    $penyedia = mysqli_real_escape_string($conn, $_POST['penyedia']);
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $foto = $_FILES['foto']['name'];
    if($foto != "") {
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/".$foto);
    }

    mysqli_query($conn, "INSERT INTO beasiswa (nama_beasiswa, penyedia, deskripsi, foto) VALUES ('$nama', '$penyedia', '$desc', '$foto')");
    echo "<script>alert('Beasiswa ditambahkan'); window.location='admin_dashboard.php';</script>";
}

if (isset($_POST['edit_beasiswa'])) {
    $id = $_POST['id_beasiswa'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_beasiswa']);
    $penyedia = mysqli_real_escape_string($conn, $_POST['penyedia']);
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $query_update = "UPDATE beasiswa SET nama_beasiswa='$nama', penyedia='$penyedia', deskripsi='$desc'";

    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/".$foto);
        $query_update .= ", foto='$foto'";
    }

    $query_update .= " WHERE id='$id'";
    
    mysqli_query($conn, $query_update);
    echo "<script>alert('Beasiswa diperbarui'); window.location='admin_dashboard.php';</script>";
}

if (isset($_POST['hapus_beasiswa'])) {
    $id = $_POST['id_beasiswa'];
    mysqli_query($conn, "DELETE FROM pendaftaran WHERE beasiswa_id='$id'");
    mysqli_query($conn, "DELETE FROM beasiswa WHERE id='$id'");
    echo "<script>alert('Beasiswa dihapus'); window.location='admin_dashboard.php';</script>";
}

if (isset($_POST['keputusan'])) {
    $id = $_POST['id_pendaftaran'];
    $keputusan_baru = $_POST['keputusan']; 

    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status_verifikasi FROM pendaftaran WHERE id='$id'"));
    $status_lama = $cek['status_verifikasi'];

    if ($status_lama == 'LOLOS' || $status_lama == 'DITOLAK') {
        echo "<script>alert('TIDAK BISA DIUBAH! Status sudah Final ($status_lama).'); window.location='admin_dashboard.php?page=kelola';</script>";
    } else {

        mysqli_query($conn, "UPDATE pendaftaran SET status_verifikasi='$keputusan_baru' WHERE id='$id'");
        echo "<script>alert('Status berhasil diubah menjadi $keputusan_baru'); window.location='admin_dashboard.php?page=kelola';</script>";
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'beasiswa';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Dashboard</title>
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
                <a href="admin_dashboard.php" class="btn btn-dark fw-bold me-2">Kelola Beasiswa</a>
                <a href="admin_pengumuman.php" class="btn btn-outline-dark fw-bold me-3">Kelola Pengumuman</a>
                <a href="logout.php" class="btn btn-danger">LOGOUT</a>
            </nav>
        </div>
    </header>

    <div class="container my-5">

        <ul class="nav nav-tabs mb-4 border-warning border-bottom border-2">
            <li class="nav-item">
                <a class="nav-link <?= ($page == 'beasiswa') ? 'active text-dark border-warning border-bottom-0 fw-bold bg-light' : 'text-secondary' ?>" href="?page=beasiswa">Kelola Beasiswa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($page == 'kelola') ? 'active text-dark border-warning border-bottom-0 fw-bold bg-light' : 'text-secondary' ?>" href="?page=kelola">Kelola Pendaftaran & Verifikasi</a>
            </li>
        </ul>

        <?php if ($page == 'beasiswa') { ?>
        
        <div class="card mb-5 card-custom border-0 shadow-sm">
            <div class="card-header bg-warning"><h5>Buat Pengumuman Beasiswa</h5></div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-2"><input type="text" name="nama_beasiswa" class="form-control" placeholder="Nama Beasiswa" required></div>
                        <div class="col-md-6 mb-2"><input type="text" name="penyedia" class="form-control" placeholder="Penyedia" required></div>
                        <div class="col-12 mb-2"><textarea name="deskripsi" class="form-control" placeholder="Deskripsi" rows="3" required></textarea></div>
                        <div class="col-12 mb-2">
                            <label class="fw-bold mb-1">Foto Banner Beasiswa</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" name="tambah_beasiswa" class="btn btn-primary-custom mt-2">Posting Beasiswa</button>
                </form>
            </div>
        </div>

        <h4 class="mb-3 border-bottom pb-2">Daftar Beasiswa Aktif</h4>
        <div class="row mb-5">
            <?php
            $q_beasiswa = mysqli_query($conn, "SELECT * FROM beasiswa ORDER BY id DESC");
            while ($b = mysqli_fetch_assoc($q_beasiswa)) {
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 card-custom">
                    <?php if(!empty($b['foto'])): ?>
                        <img src="uploads/<?= $b['foto'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Foto Beasiswa">
                    <?php else: ?>
                        <div class="bg-secondary text-white text-center py-5 d-flex align-items-center justify-content-center" style="height: 200px;">No Image</div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($b['nama_beasiswa']) ?></h5>
                        <h6 class="text-muted mb-3"><?= htmlspecialchars($b['penyedia']) ?></h6>
                        <p class="card-text small text-secondary flex-grow-1"><?= nl2br(htmlspecialchars($b['deskripsi'])) ?></p>
                        <hr>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-warning w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#editBeasiswa<?= $b['id'] ?>">Edit</button>
                            <form method="POST" class="w-100" onsubmit="return confirm('Hapus beasiswa ini? Data pendaftar terkait juga akan terhapus.');">
                                <input type="hidden" name="id_beasiswa" value="<?= $b['id'] ?>">
                                <button type="submit" name="hapus_beasiswa" class="btn btn-sm btn-danger w-100 fw-bold">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editBeasiswa<?= $b['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title fw-bold">Edit Beasiswa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="id_beasiswa" value="<?= $b['id'] ?>">
                                    <div class="mb-3"><label class="fw-bold">Nama</label><input type="text" name="nama_beasiswa" value="<?= htmlspecialchars($b['nama_beasiswa']) ?>" class="form-control" required></div>
                                    <div class="mb-3"><label class="fw-bold">Penyedia</label><input type="text" name="penyedia" value="<?= htmlspecialchars($b['penyedia']) ?>" class="form-control" required></div>
                                    <div class="mb-3"><label class="fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="5" required><?= htmlspecialchars($b['deskripsi']) ?></textarea></div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Ganti Foto (Biarkan kosong jika tidak diubah)</label>
                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_beasiswa" class="btn btn-primary-custom px-4">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php } else if ($page == 'kelola') { ?>

        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h4 class="mb-0">Data Pendaftar Beasiswa</h4>
            <div class="d-flex gap-2 align-items-center">
                <form method="GET" class="d-flex gap-2 align-items-center">
                    <input type="hidden" name="page" value="kelola">
                    <label class="fw-bold mb-0">Filter:</label>
                    <select name="filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua</option>
                        <option value="MENUNGGU" <?= (isset($_GET['filter']) && $_GET['filter'] == 'MENUNGGU') ? 'selected' : '' ?>>Menunggu</option>
                        <option value="SEDANG DITINJAU" <?= (isset($_GET['filter']) && $_GET['filter'] == 'SEDANG DITINJAU') ? 'selected' : '' ?>>Sedang Ditinjau</option>
                        <option value="LOLOS" <?= (isset($_GET['filter']) && $_GET['filter'] == 'LOLOS') ? 'selected' : '' ?>>Disetujui (Lolos)</option>
                        <option value="DITOLAK" <?= (isset($_GET['filter']) && $_GET['filter'] == 'DITOLAK') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                </form>
                <?php $current_filter = isset($_GET['filter']) ? $_GET['filter'] : ''; ?>
                <a href="cetak.php?status=<?= urlencode($current_filter) ?>" target="_blank" class="btn btn-sm btn-danger fw-bold">Cetak PDF</a>
            </div>
        </div>
        
        <div class="table-responsive bg-white shadow-sm rounded border">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>Beasiswa</th>
                        <th>IPK</th>
                        <th>
                            Status
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $where_clause = "";
                    if (isset($_GET['filter']) && !empty($_GET['filter'])) {
                        $filter = mysqli_real_escape_string($conn, $_GET['filter']);
                        $where_clause = "WHERE p.status_verifikasi = '$filter'";
                    }

                    $query_verif = mysqli_query($conn, "SELECT p.*, b.nama_beasiswa FROM pendaftaran p JOIN beasiswa b ON p.beasiswa_id = b.id $where_clause ORDER BY p.id DESC");
                    
                    if (mysqli_num_rows($query_verif) > 0) {
                        while ($d = mysqli_fetch_assoc($query_verif)) {

                            $status = $d['status_verifikasi'];
                            $badge_color = 'secondary';
                            if($status == 'MENUNGGU') $badge_color = 'secondary';
                            else if($status == 'SEDANG DITINJAU') $badge_color = 'warning text-dark';
                            else if($status == 'LOLOS') $badge_color = 'success';
                            else if($status == 'DITOLAK') $badge_color = 'danger';
                    ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($d['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($d['nim']) ?></td>
                        <td><?= htmlspecialchars($d['nama_beasiswa']) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($d['ipk']) ?></td>
                        <td><span class="badge bg-<?= $badge_color ?> w-100 py-2"><?= $status ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-outline-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#verif<?= $d['id'] ?>">Verifikasi Berkas</button>
                        </td>
                    </tr>

                    <div class="modal fade" id="verif<?= $d['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg"> 
                            <div class="modal-content">
                                <div class="modal-header bg-light border-bottom-0 shadow-sm">
                                    <h5 class="modal-title fw-bold">Detail Verifikasi: <span class="text-primary"><?= htmlspecialchars($d['nama_lengkap']) ?></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row bg-light p-3 rounded mb-4 mx-1">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Jurusan</small>
                                            <span class="fw-bold"><?= htmlspecialchars($d['jurusan']) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">IPK</small>
                                            <span class="fw-bold text-success fs-5"><?= htmlspecialchars($d['ipk']) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Email</small>
                                            <span><?= htmlspecialchars($d['email']) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">No. HP</small>
                                            <span><?= htmlspecialchars($d['no_hp']) ?></span>
                                        </div>
                                    </div>
                                    
                                    <h6 class="fw-bold border-bottom pb-2 mb-3">Lampiran Berkas (PDF)</h6>
                                    <div class="d-flex gap-2 flex-wrap mb-4">
                                        <?php if(!empty($d['file_sktm'])): ?><a href="uploads/<?= $d['file_sktm'] ?>" target="_blank" class="btn btn-dark btn-sm px-3">📄 SKTM</a><?php endif; ?>
                                        <?php if(!empty($d['file_rekomendasi'])): ?><a href="uploads/<?= $d['file_rekomendasi'] ?>" target="_blank" class="btn btn-dark btn-sm px-3">📄 Rekomendasi</a><?php endif; ?>
                                        <?php if(!empty($d['file_transkrip'])): ?><a href="uploads/<?= $d['file_transkrip'] ?>" target="_blank" class="btn btn-dark btn-sm px-3">📄 Transkrip Nilai</a><?php endif; ?>
                                    </div>
                                    
                                    <div class="p-4 bg-light rounded border text-center">
                                        <?php if($status == 'LOLOS' || $status == 'DITOLAK'): ?>
                                            <div class="alert alert-secondary mb-0">
                                                Status verifikasi pendaftar ini sudah final: <strong class="text-<?= $status == 'LOLOS' ? 'success' : 'danger' ?>"><?= $status ?></strong>.<br>
                                                <small>Data yang telah dikunci tidak dapat diubah lagi.</small>
                                            </div>
                                        <?php else: ?>
                                            <p class="mb-3 fw-bold text-dark fs-5">Tentukan Keputusan Status:</p>
                                            <form method="POST" class="d-flex justify-content-center gap-3">
                                                <input type="hidden" name="id_pendaftaran" value="<?= $d['id'] ?>">
                                                
                                                <button type="submit" name="keputusan" value="SEDANG DITINJAU" class="btn btn-warning fw-bold px-4">⚠️ Sedang Ditinjau</button>
                                                
                                                <button type="submit" name="keputusan" value="LOLOS" class="btn btn-success fw-bold px-4" onclick="return confirm('Apakah Anda yakin memberikan status LOLOS? Status ini bersifat final dan tidak dapat diubah.')">✅ LOLOS</button>
                                                
                                                <button type="submit" name="keputusan" value="DITOLAK" class="btn btn-danger fw-bold px-4" onclick="return confirm('Apakah Anda yakin memberikan status DITOLAK? Status ini bersifat final dan tidak dapat diubah.')">❌ DITOLAK</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Belum ada data pendaftar untuk saat ini.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <?php } ?>

    </div>

    <footer>&copy; 2026 Universitas Kuningan</footer>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>