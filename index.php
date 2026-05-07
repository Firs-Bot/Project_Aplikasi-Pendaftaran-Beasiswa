<?php
session_start();
include 'koneksi.php';

$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'pengumuman'");
$pengumuman_ada = (mysqli_num_rows($cek_tabel) > 0);

$query_pengumuman = false;
if ($pengumuman_ada) {
    $query_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 6");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beasiswa UNIKU - Universitas Kuningan</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #1a429c 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--primary-yellow);
        }
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 40px;
            font-weight: 300;
        }
        .announcement-section {
            padding: 80px 0;
        }
        .pengumuman-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-area">
                <img src="Logo UNIKU.png" alt="Logo UNIKU">
                <h1 class="univ-title">UNIVERSITAS KUNINGAN</h1>
            </div>
            <nav>
                <a href="index.php" class="btn btn-outline-dark fw-bold me-2">Home</a>
                <a href="login.php" class="btn btn-dark fw-bold">Login</a>
            </nav>
        </div>
    </header>

    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Wujudkan Mimpimu Bersama Beasiswa UNIKU</h1>
            <p class="hero-subtitle">Kami mendukung penuh prestasi dan semangat belajarmu. Dapatkan akses beasiswa terbaik yang ditawarkan oleh Universitas Kuningan.</p>
            <div>
                <a href="register.php" class="btn btn-warning btn-lg fw-bold px-4 me-3">Daftar Sekarang</a>
                <a href="#pengumuman" class="btn btn-outline-light btn-lg fw-bold px-4">Lihat Pengumuman</a>
            </div>
        </div>
    </section>

    <section id="pengumuman" class="announcement-section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--accent-blue);">PENGUMUMAN TERBARU</h2>
                <div style="width: 60px; height: 4px; background-color: var(--primary-yellow); margin: 10px auto;"></div>
            </div>

            <div class="row g-4">
                <?php
                if ($pengumuman_ada && $query_pengumuman && mysqli_num_rows($query_pengumuman) > 0) {
                    while ($p = mysqli_fetch_assoc($query_pengumuman)) {
                ?>
                <div class="col-md-4">
                    <div class="card card-custom h-100 overflow-hidden">
                        <?php if (!empty($p['gambar'])) : ?>
                            <img src="uploads/<?= $p['gambar'] ?>" class="pengumuman-img" alt="Poster Beasiswa">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex justify-content-center align-items-center pengumuman-img">
                                <span class="fw-bold">Tidak ada poster</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted mb-2 d-block">
                                📅 <?= date('d M Y', strtotime($p['tanggal'])) ?>
                            </small>
                            <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($p['judul']) ?></h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?= nl2br(htmlspecialchars(substr($p['isi'], 0, 120))) ?>...
                            </p>
                            <button class="btn btn-outline-primary btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalPengumuman<?= $p['id'] ?>">Baca Selengkapnya</button>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalPengumuman<?= $p['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title fw-bold text-dark"><?= htmlspecialchars($p['judul']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">Dipublikasikan pada: <?= date('d M Y', strtotime($p['tanggal'])) ?></p>
                                <?php if (!empty($p['gambar'])) : ?>
                                    <div class="text-center mb-4">
                                        <img src="uploads/<?= $p['gambar'] ?>" class="img-fluid rounded shadow-sm" alt="Poster Beasiswa" style="max-height: 400px;">
                                    </div>
                                <?php endif; ?>
                                <div class="fs-6" style="line-height: 1.6;">
                                    <?= nl2br(htmlspecialchars($p['isi'])) ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <h5 class="mb-0">Belum ada pengumuman saat ini.</h5>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer>
        &copy; 2026 Universitas Kuningan
    </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>