-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 04:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nuptk` varchar(30) DEFAULT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL,
  `kelas_yang_diajar` varchar(50) DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `user_id`, `nama`, `nuptk`, `mata_pelajaran`, `kelas_yang_diajar`, `telepon`, `alamat`, `created_at`) VALUES
(2, 1, 'Bu Siti Rahayu', 'NUPTK0012346', 'Fisika', 'XI dan XII IPA', '081234567891', 'Jl. Ilmuwan No. 45, Kota Edukasi', '2025-08-15 08:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `mata_pelajaran` varchar(100) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `guru_nama` varchar(100) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_siswa`
--

CREATE TABLE `jawaban_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `soal_id` int(11) NOT NULL,
  `jawaban_pilihan` char(1) DEFAULT NULL,
  `is_benar` tinyint(1) DEFAULT NULL,
  `kuis_id` int(11) NOT NULL,
  `waktu_jawab` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jawaban_siswa`
--

INSERT INTO `jawaban_siswa` (`id`, `siswa_id`, `soal_id`, `jawaban_pilihan`, `is_benar`, `kuis_id`, `waktu_jawab`) VALUES
(3, 1, 3, 'B', 0, 1, '2025-08-15 09:01:53'),
(4, 1, 4, 'A', 0, 1, '2025-08-15 09:01:53');

-- --------------------------------------------------------

--
-- Table structure for table `kuis`
--

CREATE TABLE `kuis` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `guru_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuis`
--

INSERT INTO `kuis` (`id`, `judul`, `deskripsi`, `guru_id`, `created_at`) VALUES
(1, 'Kuis Fisika 1', 'Kuis tentang dasar-dasar relativitas.', 2, '2025-08-15 08:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `lencana`
--

CREATE TABLE `lencana` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar_path` varchar(255) DEFAULT 'lencana/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lencana_siswa`
--

CREATE TABLE `lencana_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `lencana_id` int(11) NOT NULL,
  `tanggal_diterima` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `poin_syarat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materi`
--

CREATE TABLE `materi` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `guru_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materi`
--

INSERT INTO `materi` (`id`, `judul`, `isi`, `file_path`, `guru_id`, `created_at`) VALUES
(3, 'Dasar Fisika Modern', 'Materi tentang relativitas dan kuantum dasar.', 'materi_4_2_1754885000.pdf', 2, '2025-08-15 08:44:49');

-- --------------------------------------------------------

--
-- Table structure for table `pengumpulan_tugas`
--

CREATE TABLE `pengumpulan_tugas` (
  `id` int(11) NOT NULL,
  `tugas_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `waktu_kumpul` timestamp NOT NULL DEFAULT current_timestamp(),
  `nilai` decimal(5,2) DEFAULT NULL,
  `dikomentari` tinyint(1) DEFAULT 0,
  `komentar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengumpulan_tugas`
--

INSERT INTO `pengumpulan_tugas` (`id`, `tugas_id`, `siswa_id`, `file_path`, `waktu_kumpul`, `nilai`, `dikomentari`, `komentar`) VALUES
(2, 2, 1, 'tugas_4_t3_1754885200.pdf', '2025-08-15 08:47:34', 90.00, 1, 'Jawaban sangat baik!'),
(3, 3, 1, 'tugas_1_t3_1755397768.docx', '2025-08-17 02:29:28', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `poin`
--

CREATE TABLE `poin` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `jenis` enum('kuis','tugas','hadiah') NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `poin`
--

INSERT INTO `poin` (`id`, `siswa_id`, `jenis`, `deskripsi`, `jumlah`, `created_at`) VALUES
(1, 1, 'tugas', 'Tugas ID: 3', 20, '2025-08-15 08:47:34'),
(2, 1, 'kuis', 'Kuis ID: 2', 15, '2025-08-15 08:47:34'),
(3, 1, 'hadiah', 'Nilai bagus: Tugas ID 3', 10, '2025-08-15 08:47:34'),
(4, 1, 'kuis', 'Kuis ID: 1', 0, '2025-08-15 09:01:53'),
(5, 1, 'tugas', 'Tugas ID: 3', 20, '2025-08-17 02:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `nama`, `nis`, `kelas`, `angkatan`, `jenis_kelamin`, `telepon`, `alamat`, `created_at`) VALUES
(1, 2, 'Ani Setiawan', '12346', 'X IPA 2', '2024', 'P', '082123456790', 'Jl. Pelajar No. 46, Kota Edukasi', '2025-08-15 08:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `soal`
--

CREATE TABLE `soal` (
  `id` int(11) NOT NULL,
  `kuis_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` text DEFAULT NULL,
  `pilihan_b` text DEFAULT NULL,
  `pilihan_c` text DEFAULT NULL,
  `pilihan_d` text DEFAULT NULL,
  `jawaban_benar` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `soal`
--

INSERT INTO `soal` (`id`, `kuis_id`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `jawaban_benar`) VALUES
(3, 1, 'Apa yang dimaksud dengan relativitas khusus?', 'Teori gravitasi Newton', 'Teori gerak benda jatuh', 'Teori tentang ruang dan waktu tanpa gravitasi', 'Teori tentang cahaya saja', 'C'),
(4, 1, 'Siapa penemu teori relativitas?', 'Isaac Newton', 'Albert Einstein', 'Galileo Galilei', 'Nikola Tesla', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `tugas`
--

CREATE TABLE `tugas` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `batas_waktu` datetime DEFAULT NULL,
  `guru_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tugas`
--

INSERT INTO `tugas` (`id`, `judul`, `deskripsi`, `file_path`, `batas_waktu`, `guru_id`, `created_at`) VALUES
(2, 'Tugas Fisika - Relativitas', 'Kerjakan soal-soal tentang relativitas khusus.', 'instruksi_4_3_1754885100.pdf', '2025-08-20 15:00:00', 2, '2025-08-15 08:45:36'),
(3, 'Tugas Fisika II', 'tugas duaaa', 'instruksi_2_3_1755397741.docx', '2025-08-19 12:12:00', 2, '2025-08-17 02:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('siswa','guru','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'guru2@sekolah.com', '$2y$10$hBbGUtVNYRGeeztMI1/KduiBu.xjAMElhyPUJbGpK6pc63CAAGPYS', 'guru', '2025-08-15 08:42:38'),
(2, 'siswa2@sekolah.com', '$2y$10$hBbGUtVNYRGeeztMI1/KduiBu.xjAMElhyPUJbGpK6pc63CAAGPYS', 'siswa', '2025-08-15 08:42:38'),
(3, 'admin@sekolah.com', '$2y$10$hBbGUtVNYRGeeztMI1/KduiBu.xjAMElhyPUJbGpK6pc63CAAGPYS', 'admin', '2025-09-09 13:58:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `nuptk` (`nuptk`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jawaban_siswa`
--
ALTER TABLE `jawaban_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `soal_id` (`soal_id`),
  ADD KEY `kuis_id` (`kuis_id`);

--
-- Indexes for table `kuis`
--
ALTER TABLE `kuis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `lencana`
--
ALTER TABLE `lencana`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lencana_siswa`
--
ALTER TABLE `lencana_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `lencana_id` (`lencana_id`);

--
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materi`
--
ALTER TABLE `materi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_id` (`tugas_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `poin`
--
ALTER TABLE `poin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indexes for table `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kuis_id` (`kuis_id`);

--
-- Indexes for table `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jawaban_siswa`
--
ALTER TABLE `jawaban_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kuis`
--
ALTER TABLE `kuis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lencana`
--
ALTER TABLE `lencana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lencana_siswa`
--
ALTER TABLE `lencana_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materi`
--
ALTER TABLE `materi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `poin`
--
ALTER TABLE `poin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jawaban_siswa`
--
ALTER TABLE `jawaban_siswa`
  ADD CONSTRAINT `jawaban_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jawaban_siswa_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jawaban_siswa_ibfk_3` FOREIGN KEY (`kuis_id`) REFERENCES `kuis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kuis`
--
ALTER TABLE `kuis`
  ADD CONSTRAINT `kuis_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lencana_siswa`
--
ALTER TABLE `lencana_siswa`
  ADD CONSTRAINT `lencana_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lencana_siswa_ibfk_2` FOREIGN KEY (`lencana_id`) REFERENCES `lencana` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materi`
--
ALTER TABLE `materi`
  ADD CONSTRAINT `materi_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  ADD CONSTRAINT `pengumpulan_tugas_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumpulan_tugas_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `poin`
--
ALTER TABLE `poin`
  ADD CONSTRAINT `poin_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `soal`
--
ALTER TABLE `soal`
  ADD CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`kuis_id`) REFERENCES `kuis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
