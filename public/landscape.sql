-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 01 Agu 2023 pada 02.19
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srsssmsc_mobilepro`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `landscape`
--

CREATE TABLE `landscape` (
  `id` int NOT NULL,
  `datetime` datetime NOT NULL,
  `est` varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `afd` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `petugas` varchar(30) NOT NULL,
  `pendamping` varchar(30) NOT NULL,
  `foto_temuan` varchar(500) NOT NULL,
  `komentar_temuan` varchar(500) NOT NULL,
  `nilai` varchar(30) NOT NULL,
  `komentar` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `landscape`
--

INSERT INTO `landscape` (`id`, `datetime`, `est`, `afd`, `petugas`, `pendamping`, `foto_temuan`, `komentar_temuan`, `nilai`, `komentar`) VALUES
(1, '2023-02-02 01:54:07', 'KNE', 'EST', 'Eriberto Jast', 'Katelynn Runte', 'molestiae.jpg$necessitatibus.jpg$omnis.jpg$modi.jpg$porro.jpg', 'explicabo$non$quam$et$dolor', '4$2$3$3$4', 'sunt$nihil$ad$quae$perspiciatis'),
(2, '2023-01-12 22:00:20', 'SLE', 'EST', 'Nick Glover', 'Prof. Kiera Strosin I', 'et.jpg$fuga.jpg$possimus.jpg$ea.jpg$qui.jpg', 'pariatur$qui$nam$exercitationem$est', '2$1$1$4$1', 'sit$id$amet$voluptatibus$odio'),
(3, '2023-02-12 18:37:21', 'TC', 'EST', 'Brian McCullough', 'Theresia Leuschke MD', 'dolor.jpg$quis.jpg$voluptates.jpg$voluptas.jpg$laborum.jpg', 'dolor$aliquam$odio$id$ipsam', '2$4$1$1$4', 'perferendis$velit$animi$est$et'),
(4, '2023-02-12 16:13:05', 'SRS', 'EST', 'Myra Stamm', 'Virgil Harvey', 'sunt.jpg$aspernatur.jpg$possimus.jpg$nemo.jpg$voluptatem.jpg', 'id$ab$cum$quasi$placeat', '1$1$3$2$4', 'voluptatem$et$voluptatum$nobis$eligendi'),
(5, '2023-01-28 13:47:05', 'SGM', 'EST', 'Luz Mann', 'Gennaro Greenholt', 'libero.jpg$expedita.jpg$et.jpg$tenetur.jpg$ducimus.jpg', 'dolorum$modi$et$adipisci$dolorem', '1$2$4$4$1', 'consectetur$dolorem$quia$sed$repellendus'),
(6, '2023-01-30 10:48:27', 'SYM', 'EST', 'Dario Larkin', 'Dr. Bridie Breitenberg', 'odit.jpg$et.jpg$voluptate.jpg$quis.jpg$pariatur.jpg', 'minima$omnis$error$aliquam$rerum', '3$4$3$2$1', 'aliquam$delectus$consequatur$est$enim'),
(7, '2023-02-03 09:46:06', 'SGM', 'EST', 'Sierra Cormier', 'Mr. Eduardo Upton', 'facere.jpg$qui.jpg$consequatur.jpg$repudiandae.jpg$sit.jpg', 'necessitatibus$dolores$commodi$optio$natus', '3$1$4$3$3', 'sed$sed$sapiente$rerum$culpa'),
(8, '2023-02-25 09:38:48', 'SRS', 'EST', 'Marcelina Rohan IV', 'Kaya Runte', 'modi.jpg$possimus.jpg$ab.jpg$delectus.jpg$repellendus.jpg', 'autem$qui$quas$rem$dolorum', '2$4$2$3$4', 'ea$consectetur$dolore$itaque$aut'),
(9, '2023-01-24 02:58:40', 'NBM', 'EST', 'Fredrick Franecki I', 'Prof. Brandon Dicki', 'qui.jpg$dolores.jpg$adipisci.jpg$sed.jpg$temporibus.jpg', 'ea$voluptatem$omnis$aut$est', '1$1$3$4$4', 'alias$aperiam$error$dignissimos$ut'),
(10, '2023-01-21 04:42:04', 'CWS1', 'EST', 'Edyth Gleichner', 'Felicia Kessler', 'voluptatem.jpg$mollitia.jpg$error.jpg$vero.jpg$est.jpg', 'voluptatibus$a$non$ea$ipsa', '1$3$4$3$2', 'vel$ullam$totam$quia$aut'),
(11, '2023-02-17 00:30:11', 'SR', 'EST', 'Griffin Runte DDS', 'Miller Donnelly', 'minima.jpg$error.jpg$quo.jpg$nulla.jpg$veniam.jpg', 'dolorem$perferendis$qui$voluptatibus$sunt', '1$1$3$4$2', 'quam$pariatur$nihil$est$quos'),
(12, '2023-02-25 06:26:04', 'NBM', 'EST', 'Mabel Walsh', 'Mr. Zion Lowe', 'repellat.jpg$et.jpg$fuga.jpg$nisi.jpg$ex.jpg', 'tempora$nobis$libero$repellendus$quas', '4$3$3$4$2', 'dolor$sequi$doloremque$illum$voluptate'),
(13, '2023-02-18 01:00:46', 'TC', 'EST', 'Ezequiel Dicki', 'Sammy Kuhic', 'non.jpg$cum.jpg$at.jpg$autem.jpg$et.jpg', 'eligendi$dignissimos$eius$illum$voluptatem', '1$2$2$2$3', 'ab$dignissimos$officia$ea$modi'),
(14, '2023-01-26 07:59:54', 'RDE', 'EST', 'Berry Roberts', 'Dr. Werner Heaney', 'aut.jpg$quis.jpg$omnis.jpg$et.jpg$nihil.jpg', 'deserunt$est$et$ullam$ut', '4$1$1$1$2', 'qui$voluptates$rem$quia$similique'),
(15, '2023-01-15 20:10:23', 'SGM', 'EST', 'Albina Green', 'Prof. Kaitlyn Kassulke IV', 'possimus.jpg$saepe.jpg$consectetur.jpg$nihil.jpg$assumenda.jpg', 'est$deleniti$a$nostrum$officiis', '3$1$4$3$4', 'quasi$aut$nulla$neque$dolore'),
(16, '2023-02-26 02:55:38', 'SRS', 'EST', 'Gabe Stracke', 'Louie Corwin', 'et.jpg$exercitationem.jpg$temporibus.jpg$error.jpg$pariatur.jpg', 'necessitatibus$illum$eum$soluta$possimus', '1$4$2$4$4', 'distinctio$magnam$ut$aut$repellat'),
(17, '2023-01-12 22:17:59', 'CWS1', 'EST', 'Tillman Homenick', 'Herminio Considine', 'eos.jpg$debitis.jpg$magnam.jpg$sit.jpg$aperiam.jpg', 'ut$hic$possimus$velit$sed', '1$3$3$1$1', 'mollitia$iure$aut$mollitia$dolorem'),
(18, '2023-01-26 15:02:58', 'SLE', 'EST', 'Jany Turner DDS', 'Destin Dickinson', 'fuga.jpg$dolores.jpg$quod.jpg$aperiam.jpg$ut.jpg', 'libero$et$qui$repellat$sunt', '2$1$2$3$2', 'optio$voluptas$odio$rerum$atque'),
(19, '2023-02-03 14:09:29', 'REG-I', 'EST', 'Ms. Alexandria Towne III', 'Mavis Wiza DVM', 'veritatis.jpg$dolorem.jpg$et.jpg$dolores.jpg$autem.jpg', 'tenetur$non$molestiae$sed$rerum', '3$2$4$4$3', 'voluptates$officiis$quas$id$ea'),
(20, '2023-01-03 22:38:25', 'REG-I', 'EST', 'Ms. Twila McGlynn Sr.', 'Malachi Gutkowski IV', 'nesciunt.jpg$molestiae.jpg$itaque.jpg$nemo.jpg$error.jpg', 'sequi$minus$fugit$veniam$ea', '2$2$3$3$1', 'dolores$ipsa$numquam$optio$laudantium'),
(21, '2023-01-21 06:42:04', 'CWS1', 'EST', 'Edyth Gleichner', 'Felicia Kessler', 'voluptatem.jpg$mollitia.jpg$error.jpg$vero.jpg$est.jpg', 'voluptatibus$a$non$ea$ipsa', '1$3$2$1$4', 'vel$ullam$totam$quia$aut'),
(22, '2023-01-21 07:42:04', 'CWS1', 'EST', 'Edyth Gleichner', 'Felicia Kessler', 'voluptatem.jpg$mollitia.jpg$error.jpg$vero.jpg$est.jpg', 'voluptatibus$a$non$ea$ipsa', '4$4$1$3$2', 'vel$ullam$totam$quia$aut');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `landscape`
--
ALTER TABLE `landscape`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `landscape`
--
ALTER TABLE `landscape`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
