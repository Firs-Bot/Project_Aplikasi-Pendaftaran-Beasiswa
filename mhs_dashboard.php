<?php
session_start();
include 'koneksi.php';
if ($_SESSION['role'] != 'mahasiswa') header("Location: login.php");

if (isset($_POST['submit_daftar'])) {
    $user_id = $_SESSION['user_id'];
    $id_beasiswa = $_POST['beasiswa_id'];
    $nama = $_POST['nama_lengkap'];
    $nim = $_POST['nim'];
    $jk = $_POST['jk'];
    $jurusan = $_POST['jurusan'];
    $ipk = $_POST['ipk'];
    $email = $_POST['email'];
    $hp = $_POST['no_hp'];

    if(empty($_FILES['sktm']['name']) || empty($_FILES['rekomendasi']['name']) || empty($_FILES['transkrip']['name'])){
        echo "<script>alert('Semua file (SKTM, Rekomendasi, Transkrip) WAJIB diunggah!'); window.location='mhs_dashboard.php';</script>";
        exit;
    }

    $sktm = $_FILES['sktm']['name'];
    $rek = $_FILES['rekomendasi']['name'];
    $trans = $_FILES['transkrip']['name'];

    $ext_sktm = strtolower(pathinfo($sktm, PATHINFO_EXTENSION));
    $ext_rek = strtolower(pathinfo($rek, PATHINFO_EXTENSION));
    $ext_trans = strtolower(pathinfo($trans, PATHINFO_EXTENSION));

    if($ext_sktm != "pdf" || $ext_rek != "pdf" || $ext_trans != "pdf") {
        echo "<script>alert('Hanya format file PDF yang diperbolehkan!'); window.location='mhs_dashboard.php';</script>";
        exit;
    }

    move_uploaded_file($_FILES['sktm']['tmp_name'], "uploads/".$sktm);
    move_uploaded_file($_FILES['rekomendasi']['tmp_name'], "uploads/".$rek);
    move_uploaded_file($_FILES['transkrip']['tmp_name'], "uploads/".$trans);

    $sql = "INSERT INTO pendaftaran (user_id, beasiswa_id, nama_lengkap, nim, jenis_kelamin, jurusan, ipk, email, no_hp, file_sktm, file_rekomendasi, file_transkrip, status_verifikasi) 
            VALUES ('$user_id', '$id_beasiswa', '$nama', '$nim', '$jk', '$jurusan', '$ipk', '$email', '$hp', '$sktm', '$rek', '$trans', 'MENUNGGU')";
    
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Pendaftaran Berhasil!'); window.location='mhs_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal Mendaftar');</script>";
    }
}

if (isset($_POST['edit_full_mhs'])) {
    $id_p = $_POST['id_pendaftaran'];

    $nama = $_POST['nama_lengkap'];
    $jk = $_POST['jk'];
    $jurusan = $_POST['jurusan'];
    $ipk = $_POST['ipk'];
    $email = $_POST['email'];
    $hp = $_POST['no_hp'];

    $query = "UPDATE pendaftaran SET nama_lengkap='$nama', jenis_kelamin='$jk', jurusan='$jurusan', ipk='$ipk', email='$email', no_hp='$hp'";

    function cek_upload_pdf($input_name) {
        if (!empty($_FILES[$input_name]['name'])) {
            $fname = $_FILES[$input_name]['name'];
            $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
            if($ext != "pdf") return false;
            move_uploaded_file($_FILES[$input_name]['tmp_name'], "uploads/".$fname);
            return $fname;
        }
        return null;
    }

    $new_sktm = cek_upload_pdf('sktm');
    $new_rek = cek_upload_pdf('rekomendasi');
    $new_trans = cek_upload_pdf('transkrip');


    if((!empty($_FILES['sktm']['name']) && !$new_sktm) || 
       (!empty($_FILES['rekomendasi']['name']) && !$new_rek) || 
       (!empty($_FILES['transkrip']['name']) && !$new_trans)) {
        echo "<script>alert('Gagal Update: File harus PDF!'); window.location='mhs_dashboard.php';</script>";
        exit;
    }

    if($new_sktm) $query .= ", file_sktm='$new_sktm'";
    if($new_rek) $query .= ", file_rekomendasi='$new_rek'";
    if($new_trans) $query .= ", file_transkrip='$new_trans'";

    $query .= " WHERE id='$id_p'";

    if(mysqli_query($conn, $query)){
        echo "<script>alert('Data diperbarui!'); window.location='mhs_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal update data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Mahasiswa</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-area">
                <img src="Logo UNIKU.png" alt="Logo">
                <h1 class="univ-title">PORTAL MAHASISWA</h1>
            </div>
            <a href="logout.php" class="btn btn-danger">LOGOUT</a>
        </div>
    </header>

    <div class="container my-5">
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item"><button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info">Info Beasiswa</button></li>
            <li class="nav-item"><button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status">Status & Edit Pendaftaran</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="info">
                <div class="row">
                    <?php
                    $q_bea = mysqli_query($conn, "SELECT * FROM beasiswa ORDER BY id DESC");
                    while ($b = mysqli_fetch_assoc($q_bea)) {
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom h-100">
                            <?php if(!empty($b['foto'])): ?>
                                <img src="uploads/<?= $b['foto'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <h4><?= htmlspecialchars($b['nama_beasiswa']) ?></h4>
                                <p class="text-muted">Penyedia: <?= htmlspecialchars($b['penyedia']) ?></p>
                                <p class="small"><?= nl2br(htmlspecialchars($b['deskripsi'])) ?></p>
                                <button class="btn btn-primary-custom mt-auto" data-bs-toggle="modal" data-bs-target="#modalDaftar<?= $b['id'] ?>">Daftar Beasiswa</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalDaftar<?= $b['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Daftar: <?= $b['nama_beasiswa'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="beasiswa_id" value="<?= $b['id'] ?>">
                                        <div class="row">
                                            <div class="col-md-6 mb-2"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" value="<?= $_SESSION['nama'] ?>" class="form-control" required></div>
                                            <div class="col-md-6 mb-2"><label>NIM</label><input type="text" name="nim" value="<?= $_SESSION['nim'] ?>" class="form-control" readonly></div>
                                        </div>
                                        
                                        <div class="mb-2"><label>Jenis Kelamin</label><br>
                                            <input type="radio" name="jk" value="Laki-laki" required> Laki-laki
                                            <input type="radio" name="jk" value="Perempuan"> Perempuan
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-2"><label>Jurusan</label>
                                                <select name="jurusan" class="form-select" required>
                                                    <option value="">Pilih Jurusan</option>
                                                    <option value="Teknik Informatika">Teknik Informatika</option>
                                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                                    <option value="Teknik Sipil">Teknik Sipil</option>
                                                    <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2"><label>IPK</label><input type="number" step="0.01" name="ipk" class="form-control" required></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-2"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                                            <div class="col-md-6 mb-2"><label>No HP</label><input type="text" name="no_hp" class="form-control" required></div>
                                        </div>
                                        
                                        <hr>
                                        <p class="text-danger small fw-bold">*Semua file WAJIB diisi & Format HARUS PDF</p>
                                        <div class="mb-2">
                                            <label>SKTM (PDF)</label>
                                            <input type="file" name="sktm" class="form-control" accept=".pdf" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Surat Rekomendasi (PDF)</label>
                                            <input type="file" name="rekomendasi" class="form-control" accept=".pdf" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Transkrip Nilai (PDF)</label>
                                            <input type="file" name="transkrip" class="form-control" accept=".pdf" required>
                                        </div>

                                        <div class="mt-3 text-end">
                                            <button type="submit" name="submit_daftar" class="btn btn-success">Submit Pendaftaran</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="tab-pane fade" id="status">
                <h3>Riwayat Pendaftaran Saya</h3>
                <div class="list-group">
                    <?php
                    $uid = $_SESSION['user_id'];
                    $q_stat = mysqli_query($conn, "SELECT p.*, b.nama_beasiswa FROM pendaftaran p JOIN beasiswa b ON p.beasiswa_id = b.id WHERE user_id='$uid'");
                    while ($s = mysqli_fetch_assoc($q_stat)) {
                        $st = $s['status_verifikasi'];
                        $warna = "list-group-item-secondary"; 
                        if($st == 'SEDANG DITINJAU') $warna = "list-group-item-warning"; 
                        else if($st == 'LOLOS') $warna = "list-group-item-success";
                        else if($st == 'DITOLAK') $warna = "list-group-item-danger";
                    ?>
                    <div class="list-group-item list-group-item-action <?= $warna ?> mb-2">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h5 class="mb-1 fw-bold"><?= $s['nama_beasiswa'] ?></h5>
                            <span class="badge bg-dark"><?= $st ?></span>
                        </div>
                        <p class="mb-1">Nama: <?= $s['nama_lengkap'] ?> | IPK: <?= $s['ipk'] ?></p>
                        
                        <button class="btn btn-sm btn-dark mt-2" data-bs-toggle="modal" data-bs-target="#editFull<?= $s['id'] ?>">Edit Data</button>
                    </div>

                    <div class="modal fade" id="editFull<?= $s['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header"><h5>Edit Data Pendaftaran Saya</h5></div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_pendaftaran" value="<?= $s['id'] ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-2"><label>Nama</label><input type="text" name="nama_lengkap" value="<?= $s['nama_lengkap'] ?>" class="form-control"></div>
                                            <div class="col-md-6 mb-2"><label>IPK</label><input type="number" step="0.01" name="ipk" value="<?= $s['ipk'] ?>" class="form-control"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-2"><label>Email</label><input type="email" name="email" value="<?= $s['email'] ?>" class="form-control"></div>
                                            <div class="col-md-6 mb-2"><label>No HP</label><input type="text" name="no_hp" value="<?= $s['no_hp'] ?>" class="form-control"></div>
                                        </div>
                                        
                                        <input type="hidden" name="jk" value="<?= $s['jenis_kelamin'] ?>">
                                        <input type="hidden" name="jurusan" value="<?= $s['jurusan'] ?>">

                                        <hr>
                                        <p class="text-info small">Upload file baru HANYA jika ingin mengubah. (Harus PDF)</p>
                                        
                                        <div class="mb-2">
                                            <label>SKTM</label>
                                            <input type="file" name="sktm" class="form-control" accept=".pdf">
                                        </div>
                                        <div class="mb-2">
                                            <label>Rekomendasi</label>
                                            <input type="file" name="rekomendasi" class="form-control" accept=".pdf">
                                        </div>
                                        <div class="mb-2">
                                            <label>Transkrip</label>
                                            <input type="file" name="transkrip" class="form-control" accept=".pdf">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="edit_full_mhs" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    
    <footer>&copy; 2026 Universitas Kuningan</footer>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>