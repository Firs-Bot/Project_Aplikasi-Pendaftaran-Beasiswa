-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Bulan Mei 2026 pada 16.28
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_beasiswa_uniku`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `beasiswa`
--

CREATE TABLE `beasiswa` (
  `id` int(11) NOT NULL,
  `nama_beasiswa` varchar(100) DEFAULT NULL,
  `penyedia` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `beasiswa`
--

INSERT INTO `beasiswa` (`id`, `nama_beasiswa`, `penyedia`, `deskripsi`, `created_at`, `foto`) VALUES
(10, 'Dicoding', 'Telkom University', 'Persyaratan:\r\n1. IPK minimal 3.00\r\n2. Memiliki SKTM\r\n3. Masasiswa Aktif(Tidak cuti)\r\n4. aaa\r\n5. bbb', '2025-12-27 08:26:45', 'dicoding-header-logo.png'),
(12, 'KIP Kuliah', 'Kemendikbudristek', '-', '2026-01-06 12:02:26', 'logo-kemendikbud.jpg'),
(13, 'Beasiswa Bank Indonesia', 'Bank Indonesia', '-', '2026-01-06 12:02:41', 'bi-b.png'),
(14, 'Beasiswa Prestasi Akademik Universitas', 'Biro Kemahasiswaan', '-', '2026-01-06 12:02:54', 'channels4_profile.jpg'),
(15, 'Biro Kemahasiswaan', 'BAZNAS', '-', '2026-01-06 12:03:09', 'Logo_Website_BAZNAS_Ok.jpg'),
(16, 'Beasiswa CSR', 'PT Telkom Indonesia', '-', '2026-01-06 12:03:23', 'Telkom_Indonesia_2013.svg.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_mahasiswa`
--

CREATE TABLE `master_mahasiswa` (
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(50) DEFAULT NULL,
  `fakultas` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_mahasiswa`
--

INSERT INTO `master_mahasiswa` (`nim`, `nama`, `jurusan`, `fakultas`) VALUES
('20240810034', 'Muhammad Fahmi Firmansyah', 'Teknik Informatika', 'Ilmu Komputer'),
('20240810091', 'Arie Muhamad Syahrial', 'Teknik Informatika', 'Ilmu Komputer'),
('20240810129', 'Salwa Hamdunah', 'Teknik Informatika', 'Ilmu Komputer');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `beasiswa_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `jenis_kelamin` varchar(20) DEFAULT NULL,
  `jurusan` varchar(50) DEFAULT NULL,
  `ipk` decimal(3,2) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `file_sktm` varchar(255) DEFAULT NULL,
  `file_rekomendasi` varchar(255) DEFAULT NULL,
  `file_transkrip` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('MENUNGGU','LOLOS','DITOLAK','SEDANG DITINJAU') DEFAULT 'MENUNGGU'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id`, `user_id`, `beasiswa_id`, `nama_lengkap`, `nim`, `jenis_kelamin`, `jurusan`, `ipk`, `email`, `no_hp`, `file_sktm`, `file_rekomendasi`, `file_transkrip`, `status_verifikasi`) VALUES
(13, 3, 16, 'Muhammad Fahmi Firmansyah', '20240810034', 'Laki-laki', 'Teknik Informatika', 3.00, 'firman@gmail.com', '08153', 'FILE_SKTM.pdf', 'FILE_REKOMENDASI.pdf', 'FILE_TRANSKRIP.pdf', 'SEDANG DITINJAU'),
(14, 3, 10, 'Muhammad Fahmi Firmansyah', '20240810034', 'Perempuan', 'Sistem Informasi', 3.00, 'fahmi@gmail.com', '081234', 'FILE_SKTM.pdf', 'FILE_REKOMENDASI.pdf', 'FILE_TRANSKRIP.pdf', 'DITOLAK'),
(15, 3, 13, 'Muhammad Fahmi Firmansyah', '20240810034', 'Laki-laki', 'Desain Komunikasi Visual', 3.00, 'muh@gmail.com', '0899923', 'FILE_SKTM.pdf', 'FILE_REKOMENDASI.pdf', 'FILE_TRANSKRIP.pdf', 'LOLOS'),
(16, 3, 14, 'Muhammad Fahmi Firmansyah', '20240810034', 'Perempuan', 'Sistem Informasi', 3.00, 'popmie@gmail.com', '0098765', 'FILE_SKTM.pdf', 'FILE_REKOMENDASI.pdf', 'FILE_TRANSKRIP.pdf', 'MENUNGGU'),
(17, 8, 14, 'Salwa Hamdunah', '20240810129', 'Perempuan', 'Teknik Informatika', 3.63, '20240810129@uniku.ac.id', '0812344', 'FILE_SKTM.pdf', 'FILE_REKOMENDASI.pdf', 'FILE_TRANSKRIP.pdf', 'MENUNGGU');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` date NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `tanggal`, `gambar`) VALUES
(2, 'Test Pengumuman', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2026-05-07', '1778160196_logo-kemendikbud.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nim_username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','mahasiswa') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `nim_username`, `password`, `role`) VALUES
(1, 'Staff Admin', 'admin', '123', 'admin'),
(3, 'Muhammad Fahmi Firmansyah', '20240810034', 'f123', 'mahasiswa'),
(5, 'Arie Muhamad Syahrial', '20240810091', 'Arie123', 'mahasiswa'),
(8, 'Salwa Hamdunah', '20240810129', 's123', 'mahasiswa');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `beasiswa`
--
ALTER TABLE `beasiswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_mahasiswa`
--
ALTER TABLE `master_mahasiswa`
  ADD PRIMARY KEY (`nim`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `beasiswa_id` (`beasiswa_id`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim_username` (`nim_username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `beasiswa`
--
ALTER TABLE `beasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`beasiswa_id`) REFERENCES `beasiswa` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
