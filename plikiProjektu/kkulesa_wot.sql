-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 25 Sty 2021, 16:27
-- Wersja serwera: 10.2.18-MariaDB
-- Wersja PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `kkulesa_wot`
--

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`kkulesa_kkulesa`@`localhost` PROCEDURE `koniecBitwy` (IN `idBitwy` INT)  NO SQL
BEGIN
    DECLARE i int DEFAULT 0;
    DECLARE liczba int;
    
    SELECT FLOOR(RAND( )*2)+1 INTO i from DUAL;
    SELECT count(*) INTO liczba FROM gracz;

    UPDATE bitwa set wygrana = i WHERE id = idBitwy;
    
    UPDATE gracz set wygranych = wygranych + 1, id_bitwy=null, druzyna=null, srednie_uszkodzenia = FLOOR((srednie_uszkodzenia + FLOOR(RAND()*4000)+2000)/2) WHERE id_bitwy=idBitwy and druzyna = i;
  
  
   UPDATE gracz set przegranych = przegranych + 1, id_bitwy=null, druzyna=null, srednie_uszkodzenia = FLOOR((srednie_uszkodzenia + FLOOR(RAND()*4000))/2) WHERE id_bitwy=idBitwy and druzyna<>i;
   
   UPDATE gracz SET WN8 = null;
   
    WHILE (liczba > 0) DO
   		UPDATE gracz SET WN8 = liczba WHERE WN8 is null ORDER BY srednie_uszkodzenia DESC LIMIT 1;
        SET liczba = liczba - 1;
	END WHILE;
END$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` PROCEDURE `koniecBitwyKlanowej` (IN `idBitwy` INT, IN `nazwa` VARCHAR(100))  NO SQL
BEGIN
    DECLARE i int DEFAULT 0;
    DECLARE liczba int;
    DECLARE nazwa2 VARCHAR(100);
    DECLARE nazwa3 VARCHAR(100);
    SET nazwa3 = nazwa;
    
    SELECT FLOOR(RAND( )*2)+1 INTO i from DUAL;
    SELECT count(*) INTO liczba FROM gracz;
	SELECT nazwa_klanu INTO nazwa2 FROM gracz WHERE nazwa_klanu <> nazwa AND id_bitwy_klanowej = idBitwy GROUP BY nazwa_klanu LIMIT 1;
    
	IF (i < 2) THEN 
    SET nazwa3 = nazwa2;
    END IF;
    
    UPDATE bitwaKlanowa SET wygrana = nazwa3 WHERE id = idBitwy;
    
   UPDATE gracz set wygranych = wygranych + 1,
   id_bitwy_klanowej=null,
   srednie_uszkodzenia = FLOOR((srednie_uszkodzenia + RAND()*4000+2000)/2) 
   WHERE id_bitwy_klanowej=idBitwy AND
   nazwa_klanu = nazwa3;
  
   UPDATE gracz set
   przegranych = przegranych + 1,
   id_bitwy_klanowej=null,
   srednie_uszkodzenia = FLOOR((srednie_uszkodzenia + RAND()*4000)/2) 
   WHERE id_bitwy_klanowej=idBitwy AND
   nazwa_klanu<>nazwa3;
   
   UPDATE gracz SET WN8 = null;
   
    WHILE (liczba > 0) DO
   		UPDATE gracz SET WN8 = liczba WHERE WN8 is null ORDER BY srednie_uszkodzenia DESC LIMIT 1;
        SET liczba = liczba - 1;
	END WHILE;
END$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` PROCEDURE `losowanie` (IN `nowe_id` INT, IN `numer` INT, IN `nickGracza` VARCHAR(100))  NO SQL
BEGIN
    DECLARE flaga boolean default 0;
    DECLARE one_ boolean default 0;
    DECLARE i int DEFAULT 0;
    DECLARE j int;
	
    if(nickGracza is not null) THEN
        SELECT losujLosujacego(nickGracza, nowe_id) into j;
        if(j = 0) THEN
            CALL losujPlutonLosujacego(nickGracza, nowe_id);
            SET i = i + 2;
        ELSE
            SET i = i + 1;
        END IF;
    END IF;

    WHILE (i < 5) DO
        IF(rand() > 0.85 and i < 4) THEN
            SELECT losujPluton(nowe_id, numer) INTO flaga;
            SELECT losujGracza(nowe_id, flaga, numer) INTO one_;
            SELECT losujGracza(nowe_id, flaga, numer) INTO one_;
            SET i = i + 2;
        ELSE
            SELECT losujGracza(nowe_id, 0, numer) INTO one_;
            if(one_ = 0) THEN
            	SELECT losujPluton(nowe_id, numer) INTO flaga;
                 SET i = i + 2;
            ELSE
            SET i = i + 1;
            END IF;
        END IF;
       
    END WHILE;
    
END$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` PROCEDURE `losujKlan` (IN `id_bitwy_k` INT, IN `nazwa` VARCHAR(100))  NO SQL
BEGIN
UPDATE gracz SET id_bitwy_klanowej = id_bitwy_k
where ISNULL(id_bitwy) and ISNULL(id_bitwy_klanowej) and nazwa_klanu = nazwa ORDER BY RAND() LIMIT 5;
END$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` PROCEDURE `losujKlan2` (IN `id_bitwy_k` INT, IN `nazwa` VARCHAR(100), IN `nick_` VARCHAR(100))  NO SQL
BEGIN
UPDATE gracz SET id_bitwy_klanowej = id_bitwy_k WHERE nick = nick_;
UPDATE gracz SET id_bitwy_klanowej = id_bitwy_k
where ISNULL(id_bitwy) and ISNULL(id_bitwy_klanowej) and nazwa_klanu = nazwa ORDER BY RAND() LIMIT 4;
END$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` PROCEDURE `losujPlutonLosujacego` (IN `nickGracza` VARCHAR(100), IN `idBitwy` INT)  NO SQL
BEGIN
DECLARE i int;
SET i = (select id_plutonu from gracz where nick = nickGracza);
UPDATE gracz set id_bitwy = idBitwy, druzyna = 1 WHERE id_plutonu = i;
END$$

--
-- Funkcje
--
CREATE DEFINER=`kkulesa_kkulesa`@`localhost` FUNCTION `losujGracza` (`idBitwy` INT, `flaga` BOOLEAN, `numer` INT) RETURNS INT(11) NO SQL
if(flaga = 0) THEN
    if((select count(*) from gracz where  ISNULL(id_bitwy) and ISNULL(id_plutonu)) > 0) then
    	UPDATE gracz set id_bitwy = idBitwy, druzyna = numer WHERE ISNULL(id_bitwy) and ISNULL(id_bitwy_klanowej) and ISNULL(id_plutonu) ORDER BY RAND() LIMIT 1;
    	return 1;
else 
	RETURN 0;
    END IF;
ELSE
RETURN 0;
END IF$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` FUNCTION `losujLosującego` (`nickGracza` VARCHAR(100), `idBitwy` INT) RETURNS INT(11) NO SQL
BEGIN

if((SELECT count(*) from gracz where isnull(id_plutonu) and nick = nickGracza) > 0) then 

update gracz set id_bitwy = idBitwy, druzyna = 1 where nick = nickGracza;
return 1;

END IF;

RETURN 0;
END$$

CREATE DEFINER=`kkulesa_kkulesa`@`localhost` FUNCTION `losujPluton` (`idBitwy` INT, `numer` INT) RETURNS TINYINT(1) BEGIN
	DECLARE flaga int;
    DECLARE pluton int;
 	SELECT count(*) into flaga FROM gracz where ISNULL(id_bitwy) and ISNULL(id_bitwy_klanowej) and id_plutonu is not null;
    if (flaga > 1)
     	THEN
        SELECT id_plutonu into pluton from gracz where id_plutonu is not null and ISNULL(id_bitwy) order by rand() limit 1;
 	 	UPDATE gracz set id_bitwy = idBitwy, druzyna = numer WHERE id_plutonu = pluton;
 	 	return 1;
    END IF;
    RETURN 0;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bitwa`
--

CREATE TABLE `bitwa` (
  `id` int(11) NOT NULL,
  `nazwa_mapy` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `wygrana` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `bitwa`
--

INSERT INTO `bitwa` (`id`, `nazwa_mapy`, `wygrana`) VALUES
(1, 'Abbey', 2),
(2, 'Abbey', NULL),
(3, 'Abbey', NULL),
(4, 'Abbey', NULL),
(5, 'Cliff', NULL),
(6, 'Berlin ', NULL),
(7, 'Erlenberg', NULL),
(8, 'Berlin ', NULL),
(9, 'Berlin ', NULL),
(10, 'Cliff', NULL),
(11, 'Erlenberg', NULL),
(12, 'Nibylandia', NULL),
(13, 'El Halluf', NULL),
(14, 'El Halluf', NULL),
(15, 'Nibylandia', NULL),
(16, 'Cliff2', NULL),
(17, 'Nibylandia', NULL),
(18, 'Berlin3', 1),
(19, 'Berlin3', NULL),
(20, 'Berlin2', 1),
(21, 'Berlin2', 2),
(22, 'Erlenberg', 1),
(23, 'Lakeville', 1),
(24, 'nowa', 1),
(25, 'Berlin2', 1),
(26, 'Berlin3', 1),
(27, 'El Halluf', 1),
(28, 'Cliff2', 2),
(29, 'nowa', 1),
(30, 'Berlin3', 1),
(31, 'Cliff2', 2),
(32, 'Erlenberg', 2),
(33, 'Erlenberg', 1),
(34, 'Berlin2', 2),
(35, 'Berlin3', 1),
(36, 'Berlin2', 2),
(37, 'Berlin2', 1),
(38, 'Nibylandia', 2),
(39, 'Cliff2', 1),
(40, 'nowa', 2),
(41, 'El Halluf', 1),
(42, 'El Halluf', 2),
(43, 'nowa', 1),
(44, 'Erlenberg', 1),
(45, 'Lakeville', 2),
(46, 'nowa', 2),
(47, 'Lakeville', 2),
(48, 'El Halluf', 1),
(49, 'Cliff2', 1),
(50, 'Berlin3', 1),
(51, 'Lakeville', 2),
(52, 'Erlenberg', 1),
(53, 'Erlenberg', 2),
(54, 'Cliff2', 1),
(55, 'Berlin2', 2),
(56, 'Cliff2', 1),
(57, 'Å‚', 1),
(58, 'Nibylandia', 2),
(59, 'nowa', 1),
(60, 'Cliff2', 2),
(61, 'Lakeville', 2),
(62, 'Berlin2', 1),
(63, 'Lakeville', 1),
(64, 'Lakeville', 2),
(65, 'Berlin2', 1),
(66, 'El Halluf', 1),
(67, 'NowaMapa3', NULL),
(68, 'Erlenberg', 2),
(69, 'El Halluf', 2),
(70, 'El Halluf', 2),
(71, 'Erlenberg', 1),
(72, 'Mapa3', 1),
(73, 'El Halluf', 2),
(74, 'Lakeville', 1),
(75, 'Berlin2', 1),
(76, 'Berlin2', 2),
(77, 'Mapa3', 1),
(78, 'Lakeville', 1),
(79, 'Mapa3', 1),
(80, 'Berlin2', 2),
(81, 'El Halluf', 1),
(82, 'MAPA', 1),
(83, 'Mapa3', 2),
(84, 'NowaMapa3', 2),
(85, 'Berlin2', 2),
(86, 'Mapa3', 2),
(87, 'Berlin2', 2),
(88, 'Lakeville', 1),
(89, 'Berlin2', 2),
(90, 'MAPA', 2),
(91, 'Erlenberg', 2),
(92, 'Lakeville', 2),
(93, 'Berlin2', 2),
(94, 'NowaMapa3', 1),
(95, 'NowaMapa3', 2),
(96, 'Berlin2', 1),
(97, 'Lakeville', 2),
(98, 'Mapa3', 1),
(99, 'Erlenberg', 1),
(100, 'El Halluf', 2),
(101, 'Berlin2', 1),
(102, 'NowaMapa3', 2),
(103, 'Lakeville', 1),
(104, 'Mapa3', 2),
(105, 'MAPA', 1),
(106, 'MAPA', 1),
(107, 'Berlin2', 2),
(108, 'El Halluf', 1),
(109, 'El Halluf', 2),
(110, 'NowaMapa3', 2),
(111, 'Mapa3', 1),
(112, 'Erlenberg', 1),
(113, 'Erlenberg', 2),
(114, 'Erlenberg', 2),
(115, 'Mapa3', 2),
(116, 'Berlin2', 2),
(117, 'MAPA', 2),
(118, 'NowaMapa3', 2),
(119, 'Erlenberg', 2),
(120, 'El Halluf', 1),
(121, 'NowaMapa3', 1),
(122, 'Mapa3', 1),
(123, 'Mapa3', 1),
(124, 'Erlenberg', 2),
(125, 'NowaMapa3', 1),
(126, 'Berlin2', 2),
(127, 'El Halluf', 2),
(128, 'El Halluf', 1),
(129, 'Berlin2', 1),
(130, 'Berlin2', 2),
(131, 'El Halluf', 2),
(132, 'Erlenberg', 1),
(133, 'Erlenberg', 1),
(134, 'Erlenberg', 1),
(135, 'Erlenberg', 2),
(136, 'Lakeville', 2),
(137, 'Lakeville', 1),
(138, 'Erlenberg', 1),
(139, 'Lakeville', 1),
(140, 'Berlin2', 2),
(141, 'El Halluf', 2),
(142, 'Erlenberg', 1),
(143, 'Lakeville', 2),
(144, 'El Halluf', 1),
(145, 'El Halluf', 2),
(146, 'Erlenberg', 2),
(147, 'El Halluf', 2),
(148, 'Lakeville', 1),
(149, 'El Halluf', 2),
(150, 'Lakeville', 1),
(151, 'Berlin2', 2),
(152, 'Berlin2', NULL),
(153, 'Berlin2', 1),
(154, 'El Halluf', 1),
(155, 'El Halluf', 2),
(156, 'Erlenberg', 1),
(157, 'Lakeville', 1),
(158, 'Berlin2', 2),
(159, 'Erlenberg', 1),
(160, 'Berlin2', 2),
(161, 'Lakeville', 2),
(162, 'Berlin2', 2),
(163, 'Berlin2', 2),
(164, 'Erlenberg', 2),
(165, 'Erlenberg', 2),
(166, 'El Halluf', 1),
(167, 'El Halluf', 1),
(168, 'El Halluf', 2),
(169, 'Lakeville', 2),
(170, 'Lakeville', 1),
(171, 'Berlin2', 2),
(172, 'Erlenberg', 2),
(173, 'Erlenberg', 2),
(174, 'Berlin2', 2),
(175, 'Berlin2', 1),
(176, 'Lakeville', 1),
(177, 'Erlenberg', 2),
(178, 'El Halluf', 2),
(179, 'Berlin2', 2),
(180, 'Malinovka', 1),
(181, 'Ensk', 2),
(182, 'Malinovka', 1),
(183, 'Fjords ', 1),
(184, 'Paris', 2),
(185, 'Erlenberg', 2),
(186, 'Malinovka', 1),
(187, 'Ensk', 2),
(188, 'Erlenberg', 1),
(189, 'Ensk', 1),
(190, 'Erlenberg', 1),
(191, 'Lakeville', 1),
(192, 'Paris', 2),
(193, 'Highway', 1),
(194, 'Malinovka', 2),
(195, 'Mannerheim Line ', 1),
(196, 'Paris', 1),
(197, 'Lakeville', 1),
(198, 'Karelia', 2),
(199, 'Ensk', 2),
(200, 'Malinovka', 2),
(201, 'Lakeville', 2),
(202, 'Ensk', 2),
(203, 'Karelia', 2),
(204, 'Fjords ', NULL),
(205, 'Fisherman', 2),
(206, 'Fisherman', 2),
(207, 'Lakeville', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bitwaKlanowa`
--

CREATE TABLE `bitwaKlanowa` (
  `id` int(11) NOT NULL,
  `nazwaMapy` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `wygrana` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `bitwaKlanowa`
--

INSERT INTO `bitwaKlanowa` (`id`, `nazwaMapy`, `wygrana`) VALUES
(1, 'Mapa', NULL),
(2, 'Erlenberg', NULL),
(3, 'Berlin ', NULL),
(4, 'Lakeville', NULL),
(5, 'Erlenberg', NULL),
(6, 'Lakeville', NULL),
(7, 'Nibylandia', NULL),
(8, 'Berlin3', 'IDAEL2'),
(9, 'Lakeville', 'nowy78'),
(10, 'Nibylandia', 'nowy78'),
(11, 'Cliff2', 'nowy78'),
(12, 'Berlin2', 'nowy78'),
(13, 'Berlin3', NULL),
(14, 'Berlin3', 'nowy78'),
(15, 'Berlin2', 'nowy78'),
(16, 'El Halluf', 'IDAEL2'),
(17, 'Cliff2', 'nowy78'),
(18, 'nowa', 'nowy78'),
(19, 'Ensk', 'NMSZ'),
(20, 'Fisherman', 'NMSZ'),
(21, 'Highway', 'NMSZ'),
(22, 'Lakeville', 'trzeci'),
(23, 'Mannerheim Line ', 'NMSZ'),
(24, 'Fjords ', 'NMSZ'),
(25, 'Ensk', 'trzeci'),
(26, 'Malinovka', 'NMSZ'),
(27, 'Lakeville', 'NMSZ'),
(28, 'Highway', 'trzeci'),
(29, 'Mannerheim Line ', 'NMSZ'),
(30, 'Mannerheim Line ', 'NMSZ');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `czolg`
--

CREATE TABLE `czolg` (
  `nazwa` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `model` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `pancerz` int(11) NOT NULL,
  `zycie` int(11) NOT NULL,
  `sila_ognia` int(11) NOT NULL,
  `tier` varchar(2) COLLATE utf8_polish_ci NOT NULL,
  `nacja` varchar(15) COLLATE utf8_polish_ci NOT NULL,
  `typ` varchar(20) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `czolg`
--

INSERT INTO `czolg` (`nazwa`, `model`, `pancerz`, `zycie`, `sila_ognia`, `tier`, `nacja`, `typ`) VALUES
('Centurion Action X', 'obrazy/czolgi/IX/medium/Centurion Mk. 7_1.png', 120, 1950, 390, 'IX', 'UK', 'medium'),
('Coonqueror 2', 'obrazy/czolgi/X/artillery/Conqueror Gun Carriage.png', 130, 530, 1200, 'X', 'Britain', 'artillery'),
('E 50 Ausf. M', 'obrazy/czolgi/IX/medium/E 50.png', 150, 2050, 390, 'IX', 'Germany', 'medium'),
('FV215b (183)', 'obrazy/czolgi/X/tank destroyer/FV215b (183).png', 152, 2000, 1150, 'X', 'Britain', 'tank destroyer'),
('FV217 Badger', 'obrazy/czolgi/X/tank destroyer/FV217 Badger.png', 355, 2100, 480, 'X', 'Britain', 'tank destroyer'),
('G.W. E 100', 'obrazy/czolgi/X/artillery/G.W. E 100.png', 80, 550, 1100, 'X', 'Germany', 'artillery'),
('Grille 15', 'obrazy/czolgi/X/tank destroyer/Grille 15.png', 30, 1800, 750, 'X', 'Germany', 'tank destroyer'),
('Jagdpanzer E 100', 'obrazy/czolgi/X/tank destroyer/Jagdpanzer E 100.png', 200, 2200, 1050, 'X', 'Germany', 'tank destroyer'),
('Object 263B', 'obrazy/czolgi/X/tank destroyer/Object 263B.png', 250, 1900, 390, 'X', 'Russia', 'tank destroyer'),
('Object 268', 'obrazy/czolgi/X/tank destroyer/Object 268.png', 187, 1950, 750, 'X', 'Russia', 'tank destroyer'),
('Object 279 early', 'obrazy/czolgi/X/heavy/Object 279 early.png', 185, 2400, 440, 'X', 'Russia', 'heavy'),
('Panhard EBR 105', 'obrazy/czolgi/X/light/Panhard EBR 105.png', 40, 1300, 390, 'X', 'France', 'light'),
('Rheinmetall Panzerwagen', 'obrazy/czolgi/X/light/Rheinmetall Panzerwagen.png', 30, 1600, 320, 'X', 'Germany', 'light'),
('Strv 103B', 'obrazy/czolgi/X/tank destroyer/Strv 103B.png', 40, 1800, 390, 'X', 'Sweden', 'tank destroyer'),
('T-100 LT', 'obrazy/czolgi/X/light/T-100 LT.png', 90, 1500, 300, 'X', 'Russia', 'light'),
('T110E3', 'obrazy/czolgi/X/tank destroyer/T110E3.png', 305, 2050, 750, 'X', 'America', 'tank destroyer'),
('T110E4', 'obrazy/czolgi/X/tank destroyer/T110E4.png', 260, 2000, 750, 'X', 'America', 'tank destroyer'),
('T92 HMC', 'obrazy/czolgi/X/artillery/T92 HMC.png', 25, 500, 1300, 'X', 'America', 'artillery'),
('Waffentrager auf E 100', 'obrazy/czolgi/X/tank destroyer/Waffentrager auf E 100.png', 80, 2000, 490, 'X', 'Germany', 'tank destroyer'),
('WZ-113G FT', 'obrazy/czolgi/X/tank destroyer/WZ-113G FT.png', 230, 2100, 750, 'X', 'China', 'tank destroyer'),
('WZ-132-1', 'obrazy/czolgi/X/light/WZ-132-1.png', 50, 1500, 390, 'X', 'China', 'light'),
('XM551 Sheridan', 'obrazy/czolgi/X/light/XM551 Sheridan.png', 14, 1600, 910, 'X', 'America', 'light');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `garaz`
--

CREATE TABLE `garaz` (
  `czolg_id` int(11) NOT NULL,
  `nick` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `nazwa` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `id_stylizacji` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `garaz`
--

INSERT INTO `garaz` (`czolg_id`, `nick`, `nazwa`, `id_stylizacji`) VALUES
(15, 'admin', 'Strv 103B', 10),
(37, 'admin', 'FV215b (183)', NULL),
(49, 'admin', 'Jagdpanzer E 100', 10),
(69, 'Filip', 'FV215b (183)', NULL),
(72, 'PaweÅ‚', 'Grille 15', 6),
(73, 'PaweÅ‚', 'Object 279 early', NULL),
(74, 'admin', 'Object 279 early', NULL),
(76, 'admin', 'Grille 15', NULL),
(77, 'admin', 'WZ-113G FT', NULL),
(78, 'uzytkownik', 'E 50 Ausf. M', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gracz`
--

CREATE TABLE `gracz` (
  `nick` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `haslo` varchar(500) COLLATE utf8_polish_ci NOT NULL,
  `nazwa_klanu` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
  `id_plutonu` int(11) DEFAULT NULL,
  `id_bitwy` int(11) DEFAULT NULL,
  `druzyna` int(11) DEFAULT NULL,
  `id_bitwy_klanowej` int(11) DEFAULT NULL,
  `wygranych` int(11) DEFAULT 0,
  `przegranych` int(11) DEFAULT 0,
  `srednie_uszkodzenia` int(11) DEFAULT 0,
  `WN8` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `gracz`
--

INSERT INTO `gracz` (`nick`, `email`, `haslo`, `nazwa_klanu`, `id_plutonu`, `id_bitwy`, `druzyna`, `id_bitwy_klanowej`, `wygranych`, `przegranych`, `srednie_uszkodzenia`, `WN8`) VALUES
('11A', '1@wp.pl', '$2y$10$EhoYLw9H20QClQ6kCqA5g.zQwa7BFSd.NAUzOx7P2tBAN29IOBhnK', 'NMSZ', NULL, 204, 2, NULL, 10, 10, 3591, 47),
('abc', 's@d', '$2y$10$w/NPDlj/ePkh3Zm3HSVhQeS.i18KBamQ02MJhBD3nFwM./Ec1C6KC', 'NMSZ', 105, NULL, NULL, NULL, 19, 21, 4310, 55),
('Abendlaendler', 'abendlaendler@wp.pl', '$2y$10$o0qa630eJ.8UB3nfNJUDuufKB/cELlEnwz38B0rvv42YIU0H3ogTq', NULL, 113, NULL, NULL, NULL, 11, 8, 4159, 54),
('admin', 'admin3@wp.pl', '$2y$10$B.YscUb5vYZ2WaQPrDXEp.KKPhtY8rWiqghKbEl8p1.KsL/Li0OiK', 'trzeci', 116, NULL, NULL, NULL, 90, 79, 3256, 37),
('Asseesino', 'asseesino@wp.pl', '$2y$10$oFE.L3O8.dIBruz02U3R0eifBVkJd.DrpYz7KHefNtAE5Q4a9/sY6', NULL, 102, NULL, NULL, NULL, 9, 8, 2217, 11),
('BlackLake', 'blacklake@wp.pl', '$2y$10$fMP4trheZBJGs59.zGuokeuIEY82QcwWuutQKUbYsjx1HdG.mrnTW', NULL, 101, NULL, NULL, NULL, 2, 8, 2935, 29),
('bUgI', 'bugl@wp.pl', '$2y$10$G/PGkfgEbX6phIYeRWl6muGL2BNBcJLUuozWwfEFMW9kT0nKuxVKi', 'KLAN93', NULL, 204, 1, NULL, 10, 15, 3383, 40),
('D0nat0r', 'd0nat0r@wp.pl', '$2y$10$iiQb192zKiiuCRmM1zclpea/uyG3ZP3Q2C35ORCLJSUDWFDmy/y0G', 'ZERO', 101, NULL, NULL, NULL, 4, 8, 2545, 18),
('dajan006', 'dajan006@wp.pl', '$2y$10$jFsVJJwp7TU2GppGDGIxLOH7BBtpK8A0T8ZWSCwuPlFWJJJhPK5A.', 'NMSZ', 111, NULL, NULL, NULL, 7, 10, 4046, 53),
('dajmon123', 'dajmon123@wp.pl', '$2y$10$WvVMnbBRb0FYnn1YbTQhKeYHmC7HIxSnQN1Iez7StqXnX4pYEdWGm', NULL, 103, NULL, NULL, NULL, 10, 3, 3293, 38),
('DeavQ', 'deavq@wp.pl', '$2y$10$Zi.NzflJIDCdvSTGIE8KLeoz0m7fUHbdyK17f3Fv9.JJhXOgm6rLW', 'KLAN93', NULL, 204, 2, NULL, 13, 18, 2921, 28),
('DerNachbar', 'dernachbar@wp.pl', '$2y$10$F/RxyMdL8cUL4joCeAwXne8TxRudhyjZY.5H4hrUi.M5gel3Msmc6', NULL, NULL, NULL, NULL, NULL, 6, 4, 2423, 16),
('DomKopft', 'domkopft@wp.pl', '$2y$10$5U1d52dFvrP/xY6Skpc48OcknuCjgm/UahyyHHRsSMxMH67iG8Hb.', 'nowyKlan', 105, NULL, NULL, NULL, 8, 9, 2541, 17),
('Duro79', 'duro79@wp.pl', '$2y$10$CBx8x14vN.C78PQmCk0Ehu1cueUrEAuckT8HwU9BTeLXQVTQy/z4W', 'trzeci', 102, NULL, NULL, NULL, 8, 8, 3988, 52),
('Filip', 'filip@wp.pl', '$2y$10$iD83N424CsUGty9Kr6ApIOBqwC81wzyhmQK4KZq56OQOkC1vAYrvW', 'nowyKlan', 111, NULL, NULL, NULL, 1, 2, 2275, 13),
('FreeWizard ', 'freewizard@wp.pl', '$2y$10$Y3H4ergUPtnyTQ2K3nHtd.D2ZCtCmCNCOMi2ttK1LdyLFjkEK8VKe', NULL, 116, NULL, NULL, NULL, 8, 12, 3690, 49),
('GabiJEU', 'gabijeu@wp.pl', '$2y$10$JIlkBncoU48mzIjQXCqoeeexJtwzUS/Clb87Ykq2FXH2Hnr/Odv6K', 'ZERO', 103, NULL, NULL, NULL, 4, 9, 3540, 45),
('Gason', 'gason@wp.pl', '$2y$10$mEE9q/61gfBeVpj2jhIHhugWJgeQ/gVAKawImvyVh3lySw.FASp9q', 'KLAN93', 104, NULL, NULL, NULL, 4, 5, 2909, 27),
('Ghostly Network', 'ghostlynetwork@wp.pl', '$2y$10$YjHjpeUS0xv8sUEWpMsc8eA.9LrtiUY1brpkA02ttBBZjUlUZGNOK', NULL, 113, NULL, NULL, NULL, 10, 4, 4758, 59),
('Gienia', 'gienia@wp.pl', '$2y$10$d7FgahMnhUMS1WyVIcRY/.dzg57DvIHAg/G1oXBfWAbT6cDSnKyC2', 'trzeci', 114, 204, 1, NULL, 10, 7, 2175, 9),
('gogata_', 'gogata_@wp.pl', '$2y$10$wW1ii6Nix1htBIRj8U0MWOiZS9l6xonJhLnCGqNdeq3CStzBagE0u', 'KLAN93', NULL, NULL, NULL, NULL, 10, 7, 2240, 12),
('harlus', 'harlus@wp.pl', '$2y$10$Bov6cEIT2K1RJ7k7QeKl..jqQLN2knlLgZ4vA8amgTeAYy13saU/C', 'ZERO', NULL, NULL, NULL, NULL, 5, 4, 3473, 43),
('Hipo', 'hipo@wp.pl', '$2y$10$5Q/gk6p4YFetqXATsAiqyeLvULKoRgx0ZLRM7kbgMh08nvz7SRsT.', 'NMSZ', NULL, NULL, NULL, NULL, 12, 3, 2944, 31),
('Hydro', 'hydro@wp.pl', '$2y$10$VUcInLQ8gqpKem6oBjgZquQnEw0nEQaRFXqYYgmiwfY3QLwagwt/2', 'nowyKlan', NULL, NULL, NULL, NULL, 1, 5, 2212, 10),
('ja ja', 'ja@wp.pl', '$2y$10$SjkXrx7co4/nC2F7vL7pfO6mSaWSocju4TRSZ6dql//mhWaXBlRSG', 'trzeci', NULL, 204, 2, NULL, 8, 13, 2803, 25),
('ja ja2', 'ja@wp.pl', '$2y$10$mW0HX309U6pAzBCdr3/PrOypr.pgGKp5NNDRA7KKqdDM.MKr.FvXu', 'ZERO', NULL, NULL, NULL, NULL, 4, 8, 1984, 6),
('JÄ™drek', 'jedrek@wp.pl', '$2y$10$NQgoG68VMcqRr.C4m9IvD.IrRYTwdIFQVEd4PXjMnmLnbc.6Xrc5q', NULL, NULL, 204, 2, NULL, 1, 6, 2728, 23),
('Jejsey', 'jejsey@wp.pl', '$2y$10$d9hpUon2464QPq1xn/9Gau3h6llY9H/dexyQJQUqso6olXaw1x2Yu', 'trzeci', NULL, NULL, NULL, NULL, 7, 3, 2565, 19),
('Joanna', 'joanna@wp.pl', '$2y$10$tubvMr7bh5ChK0HtcNmg8efZkymJ10IaTfI0WfOZxWLHtjUn/wP.i', 'trzeci', NULL, NULL, NULL, NULL, 7, 10, 2895, 26),
('Kasai', 'kasai@wp.pl', '$2y$10$5cWPe7sf63ZhxFTQzGjYA.hvOQo7oOx3/ydF/6rYK945bm.X.U8mK', 'nowyKlan', NULL, 204, 1, NULL, 2, 3, 2094, 7),
('Kasia', 'kasia@wp.pl', '$2y$10$oeJv/rhxGBMH1gKI3B0p0.X0hLZKW5DSOADbJwiFnxAiMDgQlldMi', 'nowyKlan', NULL, 204, 1, NULL, 3, 10, 3246, 36),
('Kinimaro', 'kinimaro@wp.pl', '$2y$10$I1D9Et6gsl.fHweT0uWEHO12vWhlyig5Fu5s5KLIYpqx/ngY7O8gm', 'NMSZ', NULL, NULL, NULL, NULL, 10, 13, 2761, 24),
('kluska_05', 'kluska@wp.pl', '$2y$10$JibZqohI0U6/khcx4jgEjeX04qv7aZQfNaFt4X3KSZOqjg61iggSO', NULL, NULL, NULL, NULL, NULL, 3, 4, 2307, 14),
('Krakus', 'krakus@wp.pl', '$2y$10$2JHmcLJ.F3eEEED.lxUcpexnVigpQDmfWU/vVQV8ppzV2km1THDkK', 'trzeci', NULL, NULL, NULL, NULL, 14, 6, 3460, 42),
('kubus', 'kubus@wp.pl', '$2y$10$lcJSHv3ETM0Z08BIq5rmj.kx2rcnItbwFeJ.2ZfD2cKi7BjVuJczS', 'trzeci', NULL, NULL, NULL, NULL, 8, 14, 2626, 20),
('kujon', 'kujon@wp.pl', '$2y$10$lU86BqVlVhCUx5dtZ.nEP.x.kP.Gmma6h0nC4jnHuQSsrRFDGWPgC', 'trzeci', NULL, NULL, NULL, NULL, 5, 7, 3390, 41),
('kuLa', 'kula@wp.pl', '$2y$10$Q3nMvahGMpSBJuWpt9O6uORlvcbBU5u09GWOsVaynvmfwxjzGKVu6', 'KLAN93', NULL, NULL, NULL, NULL, 7, 8, 2941, 30),
('leszek', 'leszek@wp.pl', '$2y$10$FscrnKWrFb4DxrhKJvDvieN2NEIG2rxm4FpgYV/.pLk5vxX7/i1GG', 'ZERO', NULL, NULL, NULL, NULL, 6, 2, 4509, 58),
('Lunar Network', 'lunarnetwork@wp.pl', '$2y$10$vD7xD7zd2lBSpsws.b2voeQUmBqVLzgjTDLW82LScPzq5tHknKuki', NULL, NULL, NULL, NULL, NULL, 8, 4, 3650, 48),
('Luxo', 'luxo@wp.pl', '$2y$10$OchIgF0dAnRzsZAu2ReX1OUtcSlmt0etC.eEgJeQah7up.Upix/0O', 'KLAN93', NULL, NULL, NULL, NULL, 1, 5, 1177, 2),
('Mac', 'mac@wp.pl', '$2y$10$oBzuH0WbMHsVyqB3xBmA4ejAiIreXtWD5FUvK36tYsRcXV/E/eqv6', 'NMSZ', NULL, NULL, NULL, NULL, 16, 7, 4435, 57),
('Mervin', 'mervin@wp.pl', '$2y$10$6XYCRlPT.KVqAm4MRmf/ouvPCMhwbB/ySix6kofh7pH6CPrIBsod6', 'NMSZ', NULL, NULL, NULL, NULL, 15, 6, 3194, 35),
('MleOT', 'mleot@wp.pl', '$2y$10$DPftki5jsGuQBvAo9yQjzuyxm/.P2J7.0K3YYvAnc8BFGLlWD2xGG', 'nowyKlan', NULL, NULL, NULL, NULL, 3, 6, 3316, 39),
('nowy uÅ¼ytkownik', 'nowy@wp.pl', '$2y$10$c3jpC.BmUE8WCBkLFYVzrepHJIhs237M2a1bneiAXWOm.iZcFYerC', NULL, NULL, NULL, NULL, NULL, 4, 7, 3861, 51),
('nowy uzytkownik', 'nowy@wp.pl', '$2y$10$kWb01l93aDjBJt/Ex2MydOx3Afh2qHai7IbEJrC61jpJ8g53nIEzu', NULL, NULL, NULL, NULL, NULL, 12, 7, 3561, 46),
('OnlyPVP', 'onlypvp@wp.pl', '$2y$10$xWrpmuFnr2UU1aWHV7IfxOOljKB.pSLsaGD/80q98U8G6kmkexdKa', 'ZERO', NULL, NULL, NULL, NULL, 6, 7, 2127, 8),
('PaweÅ‚', 'pawel@wp.pl', '$2y$10$oJYjtjvYFIcP.rPxbjxr9e0et8.f/bHib9cdOLq4mqpJpG5dIgnDW', 'NMSZ', 114, 204, 1, NULL, 0, 2, 1844, 4),
('Pietia', 'pietia@wp.pl', '$2y$10$Em1.k0pIZXx.0IS1ck39kekzVsqiZF4bqmN8.eC.5B8opsz8MP68S', 'nowyKlan', NULL, NULL, NULL, NULL, 14, 9, 3739, 50),
('Pinio', 'pinio@wp.pl', '$2y$10$CKg7Xp1EYmmoF9sclZ0f0undBw1ixnpoEfzs9fz5Rp.r1aPAuHRyS', 'nowyKlan', NULL, NULL, NULL, NULL, 9, 8, 2708, 22),
('PoWtarz', 'powtarz@wp.pl', '$2y$10$0ca4Iw127c5K/SmMAylxiOnrMo3NANvgFkCHmouAT5QeV7bj6zpBa', 'KLAN93', NULL, NULL, NULL, NULL, 8, 5, 3516, 44),
('poz4life', 'poz4life@wp.pl', '$2y$10$1UOnflATbbGs./7khK0YuOLqwKi2nDEXJjfpMT6H2KdwhzKnbZ7XK', NULL, NULL, NULL, NULL, NULL, 7, 7, 2980, 33),
('Robbie', 'robbie@wp.pl', '$2y$10$DUQAkBb9i/KlQZ7YD/bqbOSzh.nHkioirxmpohGXhZvKcbBcuDjzS', 'ZERO', NULL, NULL, NULL, NULL, 5, 4, 2992, 34),
('Sanders', 'sanders@wp.pl', '$2y$10$PRqMv7aL7IcO4qAK/U4Xf.uQB/B4FLne6byhobEIraW3W.nHUsiX6', 'nowyKlan', NULL, NULL, NULL, NULL, 0, 1, 912, 1),
('ShirkMaster', 'shirkmaster@wp.pl', '$2y$10$F12Ha7xqyc7MHLBHjTVjruN.hL6HKgc4PSREJIO2ZwnugNxxqhTa.', NULL, NULL, NULL, NULL, NULL, 7, 9, 1193, 3),
('sKoTi', 'skoti@wp.pl', '$2y$10$UKmtuHbinFkX7E5SzKFE9On9GgGAUG1Ux20UZWnUXvjPNsD2sOuHa', 'trzeci', NULL, NULL, NULL, NULL, 9, 13, 2676, 21),
('tom', 'tom@wp.pl', '$2y$10$9tr1nFxWOMksa4jRLfATcecPPZyQlUUG/CRn8vEdLbQaoMfGS.f0O', 'NMSZ', 104, NULL, NULL, NULL, 12, 8, 2395, 15),
('TraaBLinKa', 'traablinka@wp.pl', '$2y$10$DmtrHuPe.RUF4bBZeCowiexJ9hx7A7iPUdhHrFqhjbwUzXNTFyVVu', NULL, NULL, NULL, NULL, NULL, 4, 5, 1903, 5),
('Traax', 'traax@wp.pl', '$2y$10$SIeLwbJtyP5eM2v2AzcvEuZqDXj5BApQUq2.zIQP3QW1CQCJnR/8a', 'nowyKlan', NULL, NULL, NULL, NULL, 5, 5, 2957, 32),
('uzytkownik', 'u@wp.pl', '$2y$10$.5JdyvpaZWURMTPSAA4Pees2GBasuYM7tAeCCRS7kYY//3I/Ffn7.', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0),
('WaterMC', 'watermc@wp.pl', '$2y$10$BDNZ47X5Zp8LgkWj4K1tl.2/Qi1zj.IyqO80BXbX0ln4mfuNWzszG', 'nowyKlan', NULL, NULL, NULL, NULL, 4, 6, 4338, 56);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klan`
--

CREATE TABLE `klan` (
  `nazwa_klanu` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `maks_liczba_graczy` int(11) NOT NULL,
  `ilosc_prowincji` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `klan`
--

INSERT INTO `klan` (`nazwa_klanu`, `maks_liczba_graczy`, `ilosc_prowincji`) VALUES
('klan nowy', 23, 12),
('KLAN93', 23, 50),
('mÃ³jKlan', 22, 22),
('moj', 22, 22),
('mojWlasny', 23, 3),
('NMSZ', 30, 10),
('nowyKlan', 80, 80),
('trzeci', 89, 11),
('ZERO', 40, 6);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logowanie`
--

CREATE TABLE `logowanie` (
  `log_id` int(11) NOT NULL,
  `log_login` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `log_haslo` varchar(500) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `logowanie`
--

INSERT INTO `logowanie` (`log_id`, `log_login`, `log_haslo`) VALUES
(4, 'wojtek', 'abc'),
(7, 'Kasia', 'haslo');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `mapa`
--

CREATE TABLE `mapa` (
  `nazwa_mapy` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `opis` varchar(500) COLLATE utf8_polish_ci NOT NULL,
  `rozmiar` varchar(10) COLLATE utf8_polish_ci NOT NULL,
  `zdjecie` varchar(200) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `typ_bitwy` varchar(100) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `mapa`
--

INSERT INTO `mapa` (`nazwa_mapy`, `opis`, `rozmiar`, `zdjecie`, `typ_bitwy`) VALUES
('Ensk', 'mapa letnia', '1000x1000', 'obrazy/mapa/Ensk.jpg', 'szturm'),
('Erlenberg', 'mapa letnia', '1000x1000', 'obrazy/mapa/Erlenberg.jpg', 'bitwa standardowa'),
('Fisherman', 'mapa zimowa', '1000x1000', 'obrazy/mapa/Fishermans.jpg', 'bitwa spotkaniowa'),
('Fjords ', 'mapa zimowa', '850x850', 'obrazy/mapa/Fjords.jpg', 'bitwa spotkaniowa'),
('Glacier ', 'mapa zimowa', '1000x1000', 'obrazy/mapa/Glacier.jpg', 'bitwa standardowa'),
('Highway', 'mapa zimowa', '1000x1000', 'obrazy/mapa/Highway.jpg', 'bitwa standardowa'),
('Karelia', 'mapa zimowa', '800x800', 'obrazy/mapa/Karelia.jpg', 'bitwa spotkaniowa'),
('Lakeville', 'mapa letnia', '1000x1000', 'obrazy/mapa/Lakeville.jpg', 'szturm'),
('Malinovka', 'mapa letnia', '1000x1000', 'obrazy/mapa/Malinovka.jpg', 'bitwa spotkaniowa'),
('Mannerheim Line ', 'mapa zimowa', '1000x1000', 'obrazy/mapa/Mannerheim Line.jpg', 'szturm'),
('Paris', 'mapa letnia', '1000x1000', 'obrazy/mapa/Paris.jpg', 'bitwa standardowa');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pluton`
--

CREATE TABLE `pluton` (
  `id_plutonu` int(11) NOT NULL,
  `data_wygasniecia` date DEFAULT NULL,
  `aktywnosc` tinyint(1) NOT NULL DEFAULT 1,
  `nick1` varchar(100) DEFAULT NULL,
  `nick2` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `pluton`
--

INSERT INTO `pluton` (`id_plutonu`, `data_wygasniecia`, `aktywnosc`, `nick1`, `nick2`) VALUES
(1, '2020-12-09', 0, NULL, NULL),
(2, '2020-12-09', 0, NULL, NULL),
(3, '2020-12-22', 0, NULL, NULL),
(4, NULL, 1, NULL, NULL),
(5, NULL, 1, NULL, NULL),
(6, NULL, 1, NULL, NULL),
(7, NULL, 1, NULL, NULL),
(8, NULL, 1, NULL, NULL),
(9, NULL, 1, NULL, NULL),
(10, NULL, 1, NULL, NULL),
(11, NULL, 1, NULL, NULL),
(12, NULL, 1, NULL, NULL),
(13, '2020-12-09', 0, NULL, NULL),
(14, NULL, 1, NULL, NULL),
(15, NULL, 1, NULL, NULL),
(16, NULL, 1, NULL, NULL),
(17, NULL, 1, NULL, NULL),
(18, NULL, 1, NULL, NULL),
(19, NULL, 1, NULL, NULL),
(20, NULL, 1, NULL, NULL),
(21, NULL, 1, NULL, NULL),
(22, NULL, 1, NULL, NULL),
(23, NULL, 1, NULL, NULL),
(24, NULL, 1, NULL, NULL),
(25, NULL, 1, NULL, NULL),
(26, NULL, 1, NULL, NULL),
(27, NULL, 1, NULL, NULL),
(28, NULL, 1, NULL, NULL),
(29, NULL, 1, NULL, NULL),
(30, NULL, 1, NULL, NULL),
(31, NULL, 1, NULL, NULL),
(32, NULL, 1, NULL, NULL),
(33, NULL, 1, NULL, NULL),
(34, NULL, 1, NULL, NULL),
(35, NULL, 1, NULL, NULL),
(36, NULL, 1, NULL, NULL),
(37, NULL, 1, NULL, NULL),
(38, NULL, 1, NULL, NULL),
(39, NULL, 1, NULL, NULL),
(40, NULL, 1, NULL, NULL),
(41, NULL, 1, NULL, NULL),
(42, NULL, 1, NULL, NULL),
(43, NULL, 1, NULL, NULL),
(44, NULL, 0, NULL, NULL),
(45, '2020-12-10', 0, NULL, NULL),
(46, '2020-12-10', 0, NULL, NULL),
(47, '2020-12-10', 0, NULL, NULL),
(48, '2020-12-22', 0, NULL, NULL),
(49, '2020-12-22', 0, NULL, NULL),
(50, NULL, 1, NULL, NULL),
(51, NULL, 1, NULL, NULL),
(52, '2020-12-25', 0, NULL, NULL),
(53, '2020-12-25', 0, NULL, NULL),
(54, '2020-12-25', 0, NULL, NULL),
(55, '2020-12-25', 0, NULL, NULL),
(56, '2020-12-25', 0, NULL, NULL),
(57, '2020-12-26', 0, NULL, NULL),
(58, '2020-12-26', 0, NULL, NULL),
(59, '2021-01-05', 0, NULL, NULL),
(60, '2021-01-05', 0, NULL, NULL),
(61, NULL, 1, NULL, NULL),
(62, '2021-01-05', 0, NULL, NULL),
(63, '2021-01-05', 0, NULL, NULL),
(64, '2021-01-05', 0, NULL, NULL),
(65, '2021-01-05', 0, NULL, NULL),
(66, '2021-01-05', 0, NULL, NULL),
(67, '2021-01-05', 0, NULL, NULL),
(68, '2021-01-05', 0, NULL, NULL),
(69, '2021-01-05', 0, NULL, NULL),
(70, '2021-01-05', 0, NULL, NULL),
(71, '2021-01-05', 0, NULL, NULL),
(72, '2021-01-05', 0, NULL, NULL),
(73, '2021-01-05', 0, NULL, NULL),
(74, '2021-01-05', 0, NULL, NULL),
(75, '2021-01-05', 0, NULL, NULL),
(76, NULL, 1, NULL, NULL),
(77, NULL, 1, NULL, NULL),
(78, NULL, 1, NULL, NULL),
(79, '2021-01-05', 0, NULL, NULL),
(80, NULL, 1, NULL, NULL),
(81, '2021-01-05', 0, NULL, NULL),
(82, '2021-01-05', 0, NULL, NULL),
(83, '2021-01-05', 0, NULL, NULL),
(84, '2021-01-05', 0, NULL, NULL),
(85, '2021-01-05', 0, NULL, NULL),
(86, '2021-01-05', 0, NULL, NULL),
(87, '2021-01-05', 0, NULL, NULL),
(88, '2021-01-05', 0, NULL, NULL),
(89, '2021-01-05', 0, NULL, NULL),
(90, '2021-01-10', 0, NULL, NULL),
(91, '2021-01-10', 0, NULL, NULL),
(92, '2021-01-10', 0, NULL, NULL),
(93, '2021-01-10', 0, NULL, NULL),
(94, '2021-01-13', 0, NULL, NULL),
(95, '2021-01-13', 0, 'admin', 'Asseesino'),
(96, '2021-01-20', 0, 'admin', 'dajan006'),
(97, '2021-01-13', 0, 'abc', 'kluska_05'),
(98, '2021-01-13', 0, 'abc', 'Abendlaendler'),
(99, '2021-01-13', 0, 'abc', 'dajmon123'),
(100, '2021-01-14', 0, 'abc', 'DeavQ'),
(101, NULL, 1, 'BlackLake', 'D0nat0r'),
(102, NULL, 1, 'Asseesino', 'Duro79'),
(103, NULL, 1, 'GabiJEU', 'dajmon123'),
(104, NULL, 1, 'tom', 'Gason'),
(105, NULL, 1, 'abc', 'DomKopft'),
(106, '2021-01-20', 0, 'admin', 'ShirkMaster'),
(107, '2021-01-20', 0, 'admin', 'kubus'),
(108, '2021-01-20', 0, 'admin', 'Krakus'),
(109, '2021-01-20', 0, 'admin', 'bUgI'),
(110, '2021-01-20', 0, 'admin', 'DeavQ'),
(111, NULL, 1, 'Filip', 'dajan006'),
(112, '2021-01-25', 0, 'admin', 'poz4life'),
(113, NULL, 1, 'Abendlaendler\r\n', 'Ghostly Network'),
(114, NULL, 1, 'PaweÅ‚', 'Gienia'),
(115, '2021-01-25', 0, 'admin', 'gogata_'),
(116, NULL, 1, 'admin', 'FreeWizard ');

--
-- Wyzwalacze `pluton`
--
DELIMITER $$
CREATE TRIGGER `usunPluton` BEFORE UPDATE ON `pluton` FOR EACH ROW IF NEW.aktywnosc = false and OLD.aktywnosc = true THEN
SET NEW.data_wygasniecia = now();
UPDATE gracz set gracz.id_plutonu = null where gracz.id_plutonu = new.id_plutonu;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stylizacja`
--

CREATE TABLE `stylizacja` (
  `id_stylizacji` int(11) NOT NULL,
  `zdjecie` varchar(200) NOT NULL,
  `opis` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `stylizacja`
--

INSERT INTO `stylizacja` (`id_stylizacji`, `zdjecie`, `opis`) VALUES
(3, 'obrazy/stylizacja/karkonosz.png', 'karkonosz'),
(4, 'obrazy/stylizacja/czarna wdowa.png', 'czarna wdowa'),
(6, 'obrazy/stylizacja/flora.png', 'flora'),
(10, 'obrazy/stylizacja/dzialania desantowe.png', 'dziaÅ‚ania desantowe'),
(11, 'obrazy/stylizacja/Abbey.jpg', 'mapa letnia');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typczolgu`
--

CREATE TABLE `typczolgu` (
  `typ` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `ikona` varchar(500) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `typczolgu`
--

INSERT INTO `typczolgu` (`typ`, `ikona`) VALUES
('artillery', 'obrazy/typ/artillery.png'),
('heavy', 'obrazy/typ/heavie.png'),
('light', 'obrazy/typ/light.png'),
('medium', 'obrazy/typ/medium.png'),
('tank destroyer', 'obrazy/typ/td.png');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typ_mapy`
--

CREATE TABLE `typ_mapy` (
  `rodzaj_bitwy` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `czas_bitwy` int(11) NOT NULL,
  `wskazowka` varchar(10000) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `typ_mapy`
--

INSERT INTO `typ_mapy` (`rodzaj_bitwy`, `czas_bitwy`, `wskazowka`) VALUES
('Bitwa spotkaniowa', 15, 'Bitwa spotkaniowa to dwie druÅ¼yny przeciwne i jedna baza neutralna, znajdujÄ…ca siÄ™ miÄ™dzy pozycjami poczÄ…tkowymi druÅ¼yn. Ten neutralny obiekt nie naleÅ¼y do Å¼adnej z druÅ¼yn. Bitwa trwa tak dÅ‚ugo, aÅ¼ jedna z druÅ¼yn straci\r\nwszystkie czoÅ‚gi lub zajmie neutralnÄ… bazÄ™.<br><br>\r\nZajÄ™cie bazy wymaga zgromadzenia 100 punktÃ³w przejÄ™cia. Punkty przejÄ™cia neutralnej bazy naliczane sÄ… wedÅ‚ug\r\ntych samych zasad, ktÃ³re dotyczÄ… punktÃ³w przejmowania zwykÅ‚ych baz'),
('Bitwa standardowa', 15, 'Standardowy rodzaj bitwy to dwie druÅ¼yny przeciwne, broniÄ…ce swoich baz.\r\n<br><br>\r\nBitwa trwa do momentu, w ktÃ³rym wszystkie wrogie czoÅ‚gi sÄ… zniszczone lub jedna z baz zostanie zajÄ™ta'),
('Szturm', 10, 'Szturm to dwie druÅ¼yny przeciwne i jedna baza, naleÅ¼Ä…ca do druÅ¼yny, ktÃ³ra siÄ™ broni.\r\nAby zwyciÄ™Å¼yÄ‡:<br><br> DruÅ¼yna broniÄ…ca: musi utrzymaÄ‡ bazÄ™ lub zniszczyÄ‡ wszystkie atakujÄ…ce jÄ… pojazdy.<br><br> DruÅ¼yna atakujÄ…ca: musi zdobyÄ‡ bazÄ™ lub zniszczyÄ‡ wszystkie broniÄ…ce jej pojazdy');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `bitwa`
--
ALTER TABLE `bitwa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nazwaMapy` (`nazwa_mapy`);

--
-- Indeksy dla tabeli `bitwaKlanowa`
--
ALTER TABLE `bitwaKlanowa`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `czolg`
--
ALTER TABLE `czolg`
  ADD PRIMARY KEY (`nazwa`),
  ADD KEY `typ` (`typ`);

--
-- Indeksy dla tabeli `garaz`
--
ALTER TABLE `garaz`
  ADD PRIMARY KEY (`czolg_id`) USING BTREE,
  ADD KEY `id_stylizacji` (`id_stylizacji`),
  ADD KEY `garaz_ibfk_2` (`nazwa`),
  ADD KEY `nick` (`nick`);

--
-- Indeksy dla tabeli `gracz`
--
ALTER TABLE `gracz`
  ADD PRIMARY KEY (`nick`),
  ADD KEY `nazwa_klanu` (`nazwa_klanu`),
  ADD KEY `id_bitwy` (`id_bitwy`),
  ADD KEY `id_plutonu` (`id_plutonu`),
  ADD KEY `id_bitwy_klanowej` (`id_bitwy_klanowej`);

--
-- Indeksy dla tabeli `klan`
--
ALTER TABLE `klan`
  ADD PRIMARY KEY (`nazwa_klanu`);

--
-- Indeksy dla tabeli `logowanie`
--
ALTER TABLE `logowanie`
  ADD PRIMARY KEY (`log_id`);

--
-- Indeksy dla tabeli `mapa`
--
ALTER TABLE `mapa`
  ADD PRIMARY KEY (`nazwa_mapy`),
  ADD KEY `mapa_ibfk_1` (`typ_bitwy`);

--
-- Indeksy dla tabeli `pluton`
--
ALTER TABLE `pluton`
  ADD PRIMARY KEY (`id_plutonu`);

--
-- Indeksy dla tabeli `stylizacja`
--
ALTER TABLE `stylizacja`
  ADD PRIMARY KEY (`id_stylizacji`);

--
-- Indeksy dla tabeli `typczolgu`
--
ALTER TABLE `typczolgu`
  ADD PRIMARY KEY (`typ`);

--
-- Indeksy dla tabeli `typ_mapy`
--
ALTER TABLE `typ_mapy`
  ADD PRIMARY KEY (`rodzaj_bitwy`) USING BTREE;

--
-- AUTO_INCREMENT dla tabel zrzutów
--

--
-- AUTO_INCREMENT dla tabeli `bitwa`
--
ALTER TABLE `bitwa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT dla tabeli `bitwaKlanowa`
--
ALTER TABLE `bitwaKlanowa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT dla tabeli `garaz`
--
ALTER TABLE `garaz`
  MODIFY `czolg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT dla tabeli `logowanie`
--
ALTER TABLE `logowanie`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `pluton`
--
ALTER TABLE `pluton`
  MODIFY `id_plutonu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT dla tabeli `stylizacja`
--
ALTER TABLE `stylizacja`
  MODIFY `id_stylizacji` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `czolg`
--
ALTER TABLE `czolg`
  ADD CONSTRAINT `czolg_ibfk_1` FOREIGN KEY (`typ`) REFERENCES `typczolgu` (`typ`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `garaz`
--
ALTER TABLE `garaz`
  ADD CONSTRAINT `garaz_ibfk_1` FOREIGN KEY (`id_stylizacji`) REFERENCES `stylizacja` (`id_stylizacji`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `garaz_ibfk_2` FOREIGN KEY (`nazwa`) REFERENCES `czolg` (`nazwa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `garaz_ibfk_3` FOREIGN KEY (`nick`) REFERENCES `gracz` (`nick`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `gracz`
--
ALTER TABLE `gracz`
  ADD CONSTRAINT `gracz_ibfk_1` FOREIGN KEY (`nazwa_klanu`) REFERENCES `klan` (`nazwa_klanu`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `gracz_ibfk_2` FOREIGN KEY (`id_bitwy`) REFERENCES `bitwa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `gracz_ibfk_3` FOREIGN KEY (`id_plutonu`) REFERENCES `pluton` (`id_plutonu`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `gracz_ibfk_4` FOREIGN KEY (`id_bitwy_klanowej`) REFERENCES `bitwaKlanowa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `mapa`
--
ALTER TABLE `mapa`
  ADD CONSTRAINT `mapa_ibfk_1` FOREIGN KEY (`typ_bitwy`) REFERENCES `typ_mapy` (`rodzaj_bitwy`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
