-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 13 Paź 2020, 13:54
-- Wersja serwera: 10.1.26-MariaDB
-- Wersja PHP: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `mvclogin`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `remembered_logins`
--

CREATE TABLE `remembered_logins` (
  `token_hash` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `remembered_logins`
--

INSERT INTO `remembered_logins` (`token_hash`, `user_id`, `expires_at`) VALUES
('695c9d4f6b6f89ecd8abe2a9e61af84c0dfab276426f1d406531a8df10984e37', 13, '2020-08-21 18:10:42'),
('e1e5b71c3efce3bae786c8796400fda13d3fb87f108b4d2ea398c46d833f35ae', 13, '2020-09-03 22:14:26'),
('f7966b4b4fd8553f90830a60f3f3a958de2628e0e5b3dab80f824114aaff8f8d', 13, '2020-08-01 18:37:22');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `password_reset_hash` varchar(64) COLLATE utf8_polish_ci DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `activation_hash` varchar(64) COLLATE utf8_polish_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `password_reset_hash`, `password_reset_expires_at`, `activation_hash`, `is_active`) VALUES
(6, 'Wojti', 'wojti@on.pl', '$2y$10$YQ4cvpgHpm9YmjDeDZqZxOH6JVFeVt5UMcczmn93AbZ7lnbgortbG', NULL, NULL, NULL, 0),
(7, 'Matka', 'm@m.pl', '$2y$10$iia1Ndnb1fhA5Th4jrC62uEUGh9hHwBMI/sbvK8WYzgzeyCWQ7bj6', NULL, NULL, NULL, 0),
(8, 'Mat', 'ma@m.pl', '$2y$10$Zcyv6FTCTa79nrnvSKCzyOnx2ACT/HY8d2x6zWMgMKw2Gk1jEEEju', NULL, NULL, NULL, 0),
(9, 'Mattt', 'matt@m.pl', '$2y$10$YqGVR48Z8yqK46QV38VVEO4Wm1Q3FBkZCgqAGk6r2MfsODpoQ.o2S', NULL, NULL, NULL, 0),
(10, 'jaa', 'qw@rty.pl', '$2y$10$hrNkhZi811By.h/oDWG6uugvMUUbq/GGWftFqouYOwpSD3ria.y42', NULL, NULL, NULL, 0),
(11, 'www', 'wojtiiiii@on.pl', '$2y$10$m6T1sq/sPkWCmWXYrDa1X.suFJ9S0cUpeIG.pF6CHqaKY8OCVo6lK', NULL, NULL, NULL, 0),
(12, 'Alphabet Servicez', 'adam@gmail.com', '$2y$10$LZ68faSxxBgJI.Kn2zNNZ.IKu3UUFktsibm6LUGKPHQCqOwQ1Fl3a', NULL, NULL, NULL, 0),
(13, 'Wojciech', 'uchyki@interia.pl', '$2y$10$05bR0rjGgdAMgDFUbta2HePks/uz3Ii1joN87Z6oPoBowGGrrVn2e', 'dd152699e374b4fe45b6d4c51a242e846aa1a426d2cd7bfa3b2ff9b0aa6674e0', '2020-07-22 19:02:45', NULL, 0),
(14, 'jestezwyciezca', 'jeste@zwyciezca.pl', '$2y$10$T7RG0bNyln/1ngQ7WsG53u6hO.Cgz/ZPcYMId.7qxeyOYc55Y/nMi', NULL, NULL, NULL, 0),
(15, 'wojtinka', 'wojtink@op.pl', '$2y$10$Dh5vM6B7o7yCvgkUiC.uB.yMLbf/Khj6CkMespwd6n/ic8EolL.ne', NULL, NULL, 'f9f061771c8893257059db60131c1339ca55fd66cbe821e738311602ef763906', 0),
(16, 'wojtaszczyk', 'wojt@szczyk.pl', '$2y$10$19PTon0TBafsBCEaY0qL9uo.dZamqozDQZ4fUosAp2iG2z9Japee.', NULL, NULL, '1a71bf3441637f2510d0e8ff02a62b016812ac66e561ad621ec0dd53328068b0', 0),
(17, 'ropucha', 'r@p.pl', '$2y$10$3VJ6bJIB/Hn6ko4pPmginue/SvD2ZdwZhMBxhsOVg4GQxAl9xgkBW', NULL, NULL, NULL, 0),
(18, 'Wojciech', 'wojciech.luczak.88@interia.pl', '$2y$10$utDKm39Ewrgsav8iPv6Pe.fakSMxI1ZpqkvidrcfQLw1FSrOzNMrS', NULL, NULL, NULL, 0);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD PRIMARY KEY (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_hash` (`password_reset_hash`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
