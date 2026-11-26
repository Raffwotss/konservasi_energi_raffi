-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Nov 2026 pada 07.52
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
-- Database: `konservasi_energi_raffi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `checklist`
--

CREATE TABLE `checklist` (
  `id_checklist` int(11) NOT NULL,
  `nis` int(10) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `checklist`
--

INSERT INTO `checklist` (`id_checklist`, `nis`, `tanggal`, `jam`) VALUES
(1, 1, '2026-11-04', '08:14:00'),
(2, 2, '2026-11-04', '08:15:00'),
(3, 3, '2026-11-04', '08:21:00'),
(4, 2, '2026-11-04', '09:50:00'),
(5, 4, '2026-11-04', '09:51:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_checklist`
--

CREATE TABLE `detail_checklist` (
  `id_detail` int(11) NOT NULL,
  `id_checklist` int(11) NOT NULL,
  `id_perangkat` int(11) NOT NULL,
  `tahapan` enum('SEBELUM','SELAMA','SESUDAH','5R') NOT NULL,
  `kondisi` varchar(100) NOT NULL,
  `status` enum('Ya','Tidak') NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_checklist`
--

INSERT INTO `detail_checklist` (`id_detail`, `id_checklist`, `id_perangkat`, `tahapan`, `kondisi`, `status`, `keterangan`) VALUES
(1, 1, 2, 'SELAMA', 'Baik', 'Ya', 'lampu berhasil dinyalakan'),
(2, 2, 1, '5R', 'Baik', 'Ya', 'Laptop sudah dimatikan'),
(3, 3, 3, 'SESUDAH', 'Tidak Baik', 'Tidak', 'AC tidak dimatikan'),
(4, 5, 1, 'SELAMA', 'Baik', 'Ya', 'AC menyala');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perangkat`
--

CREATE TABLE `perangkat` (
  `id_perangkat` int(11) NOT NULL,
  `nama_perangkat` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `perangkat`
--

INSERT INTO `perangkat` (`id_perangkat`, `nama_perangkat`, `jenis`, `keterangan`) VALUES
(1, 'Laptop', 'Elektronik', 'Alat elektronik untuk mengerjakan project siswa'),
(2, 'Lampu', 'Cahaya', 'Alat untuk menerangi ruangan'),
(3, 'AC', 'Elektronik', 'Pendingin ruangan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `nis` int(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`) VALUES
(1, 'Nina titut', 'XI RPL 4'),
(2, 'Nala Rawr', 'XI RPL 4'),
(3, 'Jekin', 'XI RPL 1'),
(4, 'Jazzlyn Trisha', 'XI RPL 4');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `level` enum('Admin','Guru','Siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama`, `level`) VALUES
(1, 'raffwots', 'raffi', 'raff', 'Admin'),
(2, '12trisha', 'trisha1234', 'Jazzlyn Trisha', 'Siswa'),
(3, 'Indahshi', 'Indah1234', 'Indah Cahya', 'Guru'),
(4, 'Raffi', 'raffwots', 'raffi', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id_checklist`);

--
-- Indeks untuk tabel `detail_checklist`
--
ALTER TABLE `detail_checklist`
  ADD PRIMARY KEY (`id_detail`);

--
-- Indeks untuk tabel `perangkat`
--
ALTER TABLE `perangkat`
  ADD PRIMARY KEY (`id_perangkat`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id_checklist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `detail_checklist`
--
ALTER TABLE `detail_checklist`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `perangkat`
--
ALTER TABLE `perangkat`
  MODIFY `id_perangkat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `nis` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
