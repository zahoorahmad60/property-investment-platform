-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2024 at 02:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `realestatedb1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_ID` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `approve` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_ID`, `username`, `password`, `email`, `approve`) VALUES
(1, 'admin', '$2y$10$lWbWh9ioJW0v4A8z.3ZaseRYxJeqdWjrIBjnj3ULW0DJ3lNJ7fSpu', 'admin@example.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `consultant`
--

CREATE TABLE `consultant` (
  `consultant_ID` int(11) NOT NULL,
  `Fname` varchar(50) DEFAULT NULL,
  `Lname` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `availability_start` time DEFAULT NULL,
  `availability_end` time DEFAULT NULL,
  `approve` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 0,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultant`
--

INSERT INTO `consultant` (`consultant_ID`, `Fname`, `Lname`, `email`, `username`, `password`, `phone`, `experience`, `fee`, `company`, `rating`, `availability_start`, `availability_end`, `approve`, `status`, `rejection_reason`) VALUES
(2, 'Amjad', 'Albrahim', 'amjadalbrahim77@gmail.com', 'Amjad2', '$2y$10$zF/tAYuDXijwo7UZWyPLeuv1zEfiH3agkQmeLd1Bd8.8slGsfQ9.G', '0597751479', 3, 50.00, 'King Faisal University', 4.25, NULL, NULL, 1, 0, NULL),
(3, 'Ibrahim', 'Alhamad', 'I@gmail.com', 'Ibrahim', '$2y$10$TvihOsfKNOBgm.k20Zut8epmdmALjo6lGwrErTtcvrb3OlzKawW9y', '0596848323', 10, 250.00, 'Alohali\'s For Consultants', NULL, NULL, NULL, 1, 0, NULL),
(4, 'Ahmed', 'Albrahim', 'a@gmail.com', 'Ahmed2', '$2y$10$KGCSkbGNKL2cQOIm7vhppuxAx6pa7QhU.x1nSlbD5SfOvLSqx8usq', '0597751479', 15, 245.00, 'Bright Company', 2.00, NULL, NULL, 1, 0, NULL),
(5, 'Faris', 'Aljabr', 'f280@gmail.com', 'Faris2', '$2y$10$FrBGuQFGhY/6VAx115ucDumiVKBFPfr9IYJoJD3BTZG5fb87neCWG', '0597751479', 23, 1040.00, 'Aljabr Realestate', NULL, NULL, NULL, 1, 0, NULL),
(6, 'Mohammed', 'Alhassan', 'a@gmail.com', 'Mohammed', '$2y$10$F3GeMcAyUTUaCpZbayRajukWEreawYZlQ8/S9Rp7KV33jSGbJtwby', '05977512222', 8, 165.00, 'alnoor consultations', NULL, NULL, NULL, -1, 0, 'email already exists'),
(7, 'Asmaa', 'Alessa', 'Asmaa@gmail.com', 'Asmaa', '$2y$10$pvkDB02zhyp5R81bnuSOyuYhaeON0QhC1keIjZe7D61noL7gk5Q/i', '0545558123', 12, 150.00, 'Alohali\'s For Consultants', NULL, NULL, NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

CREATE TABLE `consultation` (
  `session_number` int(11) NOT NULL,
  `investor_ID` int(11) DEFAULT NULL,
  `consultant_ID` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `status` enum('pending','approved','disapproved') DEFAULT 'pending',
  `zoom_link` varchar(255) DEFAULT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultation`
--

INSERT INTO `consultation` (`session_number`, `investor_ID`, `consultant_ID`, `date`, `time`, `description`, `rating`, `feedback`, `status`, `zoom_link`, `paid`, `rejection_reason`) VALUES
(3, 2, 2, '2024-11-20', '16:15:00', 'I want to learn how to best invest 10,000', 4, 'Very helpful', 'approved', 'link.com', 1, NULL),
(4, 2, 3, '2024-11-18', '18:15:00', 'I want to know what type of property i should invest in', NULL, NULL, 'pending', NULL, 0, NULL),
(5, 2, 3, '2024-11-13', '17:16:00', 'i want to invest in apartments', NULL, NULL, 'pending', NULL, 0, NULL),
(6, 2, 2, '2024-11-14', '17:17:00', 'I want to learn to invest', 5, 'Amazing!', 'approved', 'https://us04web.zoom.us/j/73509341607?pwd=QboGf0TOkl2AdRD0XPbfiOVwaDHQE6.1', 1, NULL),
(7, 2, 2, '2024-11-14', '17:22:00', 'I want to learn about types of investing', NULL, NULL, 'disapproved', NULL, 0, 'TIme not suitable'),
(8, 5, 2, '2024-11-28', '19:15:00', 'I want to learn how to invest 30,000', NULL, NULL, 'pending', NULL, 0, NULL),
(9, 3, 2, '2024-11-09', '19:15:00', 'I want to invest in beach villa but what to know best city for that', NULL, NULL, 'approved', 'link.com', 1, NULL),
(10, 3, 2, '2024-11-28', '23:30:00', 'I want to know how to invest half a million SAR', NULL, NULL, 'pending', NULL, 0, NULL),
(11, 2, 2, '2024-11-13', '20:40:00', 'I want to learn about investing in real estate', NULL, NULL, 'disapproved', NULL, 0, 'Time is not suitable'),
(12, 2, 2, '2024-11-04', '14:01:00', 'I need advice for property 5', 5, 'Great session', 'approved', 'link.com', 1, NULL),
(13, 2, 4, '2024-10-29', '23:56:00', 'I want to invest ', NULL, NULL, 'disapproved', NULL, 0, 'TIme not suitable'),
(14, 3, 4, '2024-11-13', '21:52:00', 'I want to invest 1000', NULL, NULL, 'pending', NULL, 0, NULL),
(15, 2, 2, '2024-11-12', '21:50:00', 'I want to learn about investing in beach villa', 3, 'ok', 'approved', 'link.com', 1, NULL),
(16, 2, 2, '2024-11-21', '21:40:00', 'I want to invest in this website', NULL, NULL, 'approved', 'link.com', 1, NULL),
(17, 2, 4, '2024-11-12', '20:40:00', 'I want to invest ', NULL, NULL, 'pending', NULL, 0, NULL),
(18, 2, 2, '2024-11-12', '21:40:00', 'I want to invest', NULL, NULL, 'disapproved', NULL, 0, 'TIme not suitable'),
(19, 7, 4, '2024-11-13', '21:30:00', '', NULL, NULL, 'disapproved', NULL, 0, 'TIme not suitable'),
(20, 2, 4, '2024-11-26', '15:40:00', 'now', 2, 'not that helpful', 'approved', 'link.com', 1, NULL),
(21, 2, 3, '2024-11-12', '14:30:00', 'Want some consultation ', NULL, NULL, 'pending', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `investment_portfolio`
--

CREATE TABLE `investment_portfolio` (
  `portfolio_ID` int(11) NOT NULL,
  `property_ID` int(11) DEFAULT NULL,
  `investor_ID` int(11) DEFAULT NULL,
  `investment_percentage` decimal(5,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `amount_paid` decimal(15,2) DEFAULT NULL,
  `monthly_return_amount` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investor`
--

CREATE TABLE `investor` (
  `investor_ID` int(11) NOT NULL,
  `Fname` varchar(50) DEFAULT NULL,
  `Lname` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `approve` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 0,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investor`
--

INSERT INTO `investor` (`investor_ID`, `Fname`, `Lname`, `email`, `username`, `password`, `phone`, `approve`, `status`, `rejection_reason`) VALUES
(2, 'Amjad', 'Albrahim', 'amjadalbrahim77@gmail.com', 'Amjad3', '$2y$10$ibkE9BroI.me9u8AqwRqTu9BdakgMT89ca6PEupLF/9yW2XnVFa1u', '0597751479', 1, 0, NULL),
(3, 'Ahmed', 'Albrahim', 'a@gmail.com', 'Ahmed3', '$2y$10$lxBDDHmyDGMJKY46g3qS3u1WucBGWTfDDyRTZpVEyCKgn0HQthY2S', '0597751479', 1, 0, NULL),
(4, 'Rahaf', 'Asuad', 'a@gmail.com', 'Rahaf', '$2y$10$4/5qMP0zRaw1/A1u1J7x6uR5vJZJZyDNH/7UL8m99hJsQZ.tch0EW', '2345678765', 1, 0, NULL),
(5, 'Nourah', 'Aljabr', 'a@gmail.com', 'Nourah', '$2y$10$iAs.a54fHreWADYur0G1a.zpNaPNj5DSJlXso.8wIG7onsrpiFp3W', '6543234567', 1, 0, NULL),
(6, 'Amall', 'Alessa', 'A@gmail.com', 'Amall', '$2y$10$SJ5zM/YPYIlInJ76qdrryu4eGFwq.4MWDP7nyxvrWy3RzKIeNYV6W', '1234567898', 0, 0, NULL),
(7, 'Abdulah', 'J', 'abdullah@gmail.com', 'Abdullah', '$2y$10$ubwg7KLh6gUBSmsIGXIF6e2ap4y/jlKewAxEWnXDLn48T/eltM1se', '0544844112', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `property_ID` int(11) NOT NULL,
  `seller_ID` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `size` decimal(10,2) DEFAULT NULL,
  `cost_of_property` decimal(15,2) DEFAULT NULL,
  `monthly_rental_returns` decimal(15,2) DEFAULT NULL,
  `monthly_return_percentage` decimal(5,2) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`property_ID`, `seller_ID`, `name`, `city`, `street`, `zip_code`, `type`, `size`, `cost_of_property`, `monthly_rental_returns`, `monthly_return_percentage`, `image_path`, `description`) VALUES
(20, 10, 'Al Noor Tower', 'Riyadh', 'Al Olaya Street', '11564', 'Skyscrapers', 4500000.00, 5000000000.00, NULL, 2.80, 'PropertyImages/SkyScrapers/1732564806_Picture1.jpg', 'Al Noor Tower stands tall with its sleek, modern glass façade, intricately adorned with traditional Saudi geometric patterns. A pinnacle of elegance, it blends contemporary design with cultural heritage, offering state-of-the-art facilities for commercial and residential use in the heart of Riyadh’s bustling business district.'),
(21, 10, 'The Oasis Residences', 'Jeddah', 'King Abdullah Road', '21433', 'Skyscrapers', 3200000.00, 3500000000.00, NULL, 2.50, 'PropertyImages/SkyScrapers/1732564940_Picture2.jpg', 'The Oasis Residences redefine urban living with green terraces that mimic the tranquility of desert oases. Its eco-friendly design offers residents a refreshing connection to nature while being located in Jeddah\'s prime urban hub. Perfect for both living and working, this skyscraper prioritizes sustainability and comfort.'),
(22, 10, 'Alalyaa Tower', 'Riyadh', 'Al Faisaliyah ', '12212', 'Skyscrapers', 3700000.00, 4200000000.00, NULL, 2.90, 'PropertyImages/SkyScrapers/1732581564_Picture3.jpg', 'A stunning architectural marvel, Alalyaa Tower features intricate latticework and a towering spire that reflects Saudi Arabia\'s rich heritage. Designed to be a symbol of innovation and tradition, it houses luxurious residential units and premium office spaces with panoramic views of Riyadh.'),
(23, 10, 'Mirage Heights', 'Jeddah', 'King Abdulaziz Street', '24231', 'Skyscrapers', 5000000.00, 6000000000.00, NULL, 3.20, 'PropertyImages/SkyScrapers/1732581733_Picture4.jpg', 'Inspired by the beauty of desert mirages, Mirage Heights captivates with its reflective glass exterior and curved architecture. The skyscraper offers a harmonious blend of modernity and serenity, providing world-class amenities and accommodations in Mecca, a global hub of spirituality and culture.'),
(24, 10, 'Salam Tower', 'Jeddah', 'Prince Sultan', '21434', 'Skyscrapers', 3000000.00, 3800000000.00, NULL, 2.60, 'PropertyImages/SkyScrapers/1732581948_Picture5.jpg', 'Salam Tower’s dynamic twisting structure and metallic accents symbolize Jeddah’s transformation into a modern metropolis. Combining luxury with cutting-edge design, it offers unparalleled views of the Red Sea and high-end facilities, making it a sought-after destination for residents and businesses.'),
(25, 10, 'Desert Bloom Tower', 'Riyadh', 'Alnarjes', '11322', 'Skyscrapers', 4200000.00, 4800000000.00, NULL, 3.50, 'PropertyImages/SkyScrapers/1732582063_Picture6.jpg', 'A visionary project that embodies sustainability and modern living, Desert Bloom Tower integrates lush vertical gardens on every floor. Its design represents resilience and life amidst the desert, offering a peaceful oasis with top-notch facilities in Riyadh\'s vibrant Alnarjes district.'),
(26, 11, 'Al Shati Villa', 'Jeddah', 'Al Shati Street', '23412', 'Beach_villas', 3500.00, 4500000.00, NULL, 5.00, 'PropertyImages/Beach-villas/1732582444_Picture7.jpg', 'Situated in Jeddah\'s prestigious Al Shati district, this villa offers direct access to the pristine Red Sea. Designed for luxury and comfort, it features four spacious bedrooms, a modern living area, a private garden, and a swimming pool, creating an idyllic coastal retreat.'),
(27, 11, 'Half Moon Bay Residence', 'Al Khobar', 'Half Moon Bay', '31952', 'Beach_villas', 4200.00, 5200000.00, NULL, 4.80, 'PropertyImages/Beach-villas/1732582533_Picture8.jpg', 'Overlooking the serene Half Moon Bay, this residence combines luxury with tranquility. It offers five bedrooms, a sea-facing terrace, and a private dock. Located in a gated community with 24/7 security, it provides an exclusive lifestyle with access to recreational amenities.'),
(28, 11, 'Obhur Al-Shamaliyah Retreat', 'Jeddah', 'Obhur Al-Shamaliyah', '23811', 'Beach_villas', 3800.00, 4800000.00, NULL, 5.20, 'PropertyImages/Beach-villas/1732582917_Picture9.jpg', 'This exquisite beachfront property combines contemporary design with natural beauty. It includes four bedrooms, a private beach area, and a landscaped garden. Conveniently located near international schools and shopping centers, it offers a balance of privacy and accessibility.'),
(29, 11, 'Durrat Al-Arus Beach House', 'Jeddah', 'Durrat Al-Arus', '23851', 'Beach_villas', 3600.00, 3900000.00, NULL, 5.50, 'PropertyImages/Beach-villas/1732583127_Picture10.jpg', ': Located in the vibrant Durrat Al-Arus resort, this beach house offers three bedrooms, a private pool, and access to exclusive communal amenities such as a golf course and marina. It’s an ideal retreat for those seeking a luxurious resort lifestyle.'),
(30, 11, 'Al Murjan Island Villa', 'King Abdullah Economic City', 'Al Murjan Island', '23965', 'Beach_villas', 4000.00, 5500000.00, NULL, 4.70, 'PropertyImages/Beach-villas/1732583220_Picture11.jpg', 'A premier villa on Al Murjan Island featuring five bedrooms, a rooftop terrace with panoramic sea views, and a private elevator. As part of a master-planned community, residents enjoy access to parks, beaches, and recreational facilities.'),
(31, 11, 'Yanbu Al Bahr Coastal Home', 'Yanbu', 'Yanbu Al Bahr', '46422', 'Beach_villas', 3700.00, 3800000.00, NULL, 5.30, 'PropertyImages/Beach-villas/1732583306_Picture12.jpg', 'Combining traditional Arabian architecture with modern comforts, this charming home features four bedrooms and a spacious courtyard. Located near Yanbu Old Town, it offers cultural heritage alongside stunning coastal views.'),
(32, 12, 'Bohemian-Style Apartment', 'Khobar', 'King Fahad ', '34714', 'Apartments', 1000.00, 40000.00, NULL, 5.00, 'PropertyImages/Apartments/1732583571_Picture13.jpg', 'This unique Bohemian-style apartment blends artistic charm with modern convenience. Covering 1000 sq ft, the apartment is thoughtfully designed with bohemian-inspired décor, featuring two cozy bedrooms, a bright and airy living room filled with natural light, and a stylish bathroom adorned with high-quality finishes. Perfect for those seeking a creative yet comfortable lifestyle.'),
(33, 12, 'Luxurious Apartment', 'Riyadh', 'King Abdulaziz', '12313', 'Apartments', 1200.00, 5000000.00, NULL, 6.00, 'PropertyImages/Apartments/1732583655_Picture14.jpg', 'Nestled in the heart of Riyadh on King Abdulaziz St, this luxurious apartment offers a perfect combination of sophistication and comfort. Spanning 1200 sq ft, it features a serene master bedroom designed for privacy and relaxation, a spacious living room with expansive windows providing breathtaking city views, and premium finishes throughout. Ideal for those who value elegance and urban living.'),
(34, 12, 'Modern Apartment', 'Dammam', 'King Fahad ', '34223', 'Apartments', 1000.00, 3000000.00, NULL, 2.00, 'PropertyImages/Apartments/1732583732_Picture15.jpg', 'Situated in Dammam\'s vibrant city center, this 1000 sq ft modern apartment is designed for sleek and convenient living. It includes two well-appointed bedrooms, a functional bathroom, and a fully equipped kitchen. The apartment comes fully furnished with contemporary décor and the latest amenities, making it an excellent choice for small families or professionals.'),
(35, 12, 'A Family Apartment', 'Dammam', 'King Saud ', '34223', 'Apartments', 1200.00, 4500000.00, NULL, 4.00, 'PropertyImages/Apartments/1732583809_Picture16.jpg', 'This spacious family-friendly apartment offers 1200 sq ft of well-designed living space. It features three comfortable bedrooms, two modern bathrooms, and a large living room ideal for family gatherings. The fully equipped kitchen includes ample storage and workspace. With large windows allowing natural light to fill the apartment, this home creates a warm and welcoming atmosphere perfect for families.'),
(36, 12, 'Studio Apartment', ' Al Ahsa', 'King Fahad ', '36361', 'Apartments', 800.00, 2500000.00, NULL, 7.00, 'PropertyImages/Apartments/1732583900_Picture17.jpg', 'This stylish and compact studio apartment is a perfect choice for professionals or students. Located on King Fahad St in Al Ahsa, the 800 sq ft space boasts a modern open layout with a cozy bedroom area, a functional living space, and a well-designed bathroom. Fully furnished and optimized for comfort, this apartment is a practical and elegant solution for urban living.'),
(37, 12, 'Harmony Apartment', 'Riyadh', 'King Saud ', '12313', 'Apartments', 1000.00, 2000000.00, NULL, 5.00, 'PropertyImages/Apartments/1732583993_Picture18.jpg', 'Located on King Saud St in Riyadh, this fully furnished apartment offers modern amenities and a convenient location. The 1000 sq ft layout includes a spacious bedroom, a stylish bathroom, and a comfortable living area. This apartment is ideal for individuals or small families seeking a blend of comfort, style, and accessibility.');

-- --------------------------------------------------------

--
-- Table structure for table `seller`
--

CREATE TABLE `seller` (
  `seller_ID` int(11) NOT NULL,
  `Fname` varchar(50) DEFAULT NULL,
  `Lname` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `approve` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 0,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller`
--

INSERT INTO `seller` (`seller_ID`, `Fname`, `Lname`, `email`, `username`, `password`, `phone`, `approve`, `status`, `rejection_reason`) VALUES
(2, 'Amjad', 'Albrahim', 'amjadalbrahim77@gmail.com', 'Amjad', '$2y$10$lkadDSfeO4nOxTh.3HG/ceF95IdYljGPlu12ErIejGckZz9cO2n92', '0597751479', 1, 0, NULL),
(3, 'Amjad', 'Albrahim', 'a@gmail.com', 'Amjad8', '$2y$10$ASls6KX2qriC0EZQY76uh..c5TSvTXUDyAX.bA9E64W2/2MHMuI8q', '0597751479', 1, 1, 'User exists already'),
(4, 'Asma', 'Alahmed', 'a@gmail.com', 'Asma', '$2y$10$KMeX8LVDogttJ9eMAEpeGeRqlZlT3pKtC/fhY534qiSz4yP6vScxW', '0597751479', 1, 0, NULL),
(5, 'Fahad', 'Albrahim', 'F@gmail.com', 'Fahad', '$2y$10$3owFgCWBXS/LGggSUGYAlOGLUIaJEGyxgZ72cwQ5IvGH1ecxaJsda', '0597751479', 1, 1, 'User already exists'),
(6, 'Salwa', 'Ahmed', 'S@gmail.com', 'Salwa', '$2y$10$kEYJYck/mRkwJ2hiNpAVweFXeqlq9jHLzK/Z04H23qMxE9iYKZADu', '0597751479', 0, 1, 'Incorrect phone number'),
(7, 'Ahmed', 'Albrahim', 'a@gmail.com', 'Ahmed', '$2y$10$V07ihM0z6hYSKvQoF8BEyO7uC0z0uFwcgHybK2xQ0bX9bnHTLWJXq', '0597751479', 1, 0, NULL),
(8, 'Faris', 'Alnoor', 'F@gmail.com', 'Faris', '$2y$10$HZ9VpJfD4WVrvRnBQzuI3uHdJGMUVSZaOdotS.MI1c43plcMI8MqK', '0597751479', 1, 0, NULL),
(9, 'Noura', 'Aljabr', 'N@gmail.com', 'Noura', '$2y$10$ykgEAkf/OEGd26CRNI.7kOQDpAgA8E6RxnoK9d9oAEtUglkOvyLkC', '0597752222', 1, 0, NULL),
(10, 'Fatimah', 'AL Dhawi', 'fatimah.sdhawi@gmail.com', 'FatimahDhawi', '$2y$10$DU4MMOdvEb9UQuKe0poSpew/XEjuTp29C4/JnRF3NfA7uflVyVLE6', '0509866005', 1, 0, NULL),
(11, 'nora', 'ahmed', 'nora1@gmail.com', 'Nora', '$2y$10$9e6tuFfAmOtoYD2JjtvV4O/o0wT54P9q3MPngCK.gJn8sLPdNofAi', '1122334455', 1, 0, NULL),
(12, 'Hessah', 'Alohali', 'Hessah1@gmail.com', 'Hessah', '$2y$10$HEJJEHZCw70ZKsND33FYWeE2VVKDS/AGefM2TIJJeGuXJA4R.Jr6m', '1112245674', 1, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_ID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `consultant`
--
ALTER TABLE `consultant`
  ADD PRIMARY KEY (`consultant_ID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`session_number`),
  ADD KEY `investor_ID` (`investor_ID`),
  ADD KEY `consultant_ID` (`consultant_ID`);

--
-- Indexes for table `investment_portfolio`
--
ALTER TABLE `investment_portfolio`
  ADD PRIMARY KEY (`portfolio_ID`),
  ADD KEY `property_ID` (`property_ID`),
  ADD KEY `investor_ID` (`investor_ID`);

--
-- Indexes for table `investor`
--
ALTER TABLE `investor`
  ADD PRIMARY KEY (`investor_ID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`property_ID`),
  ADD KEY `seller_ID` (`seller_ID`);

--
-- Indexes for table `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`seller_ID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `consultant`
--
ALTER TABLE `consultant`
  MODIFY `consultant_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `session_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `investment_portfolio`
--
ALTER TABLE `investment_portfolio`
  MODIFY `portfolio_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `investor`
--
ALTER TABLE `investor`
  MODIFY `investor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `property_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `seller`
--
ALTER TABLE `seller`
  MODIFY `seller_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `consultation_ibfk_1` FOREIGN KEY (`investor_ID`) REFERENCES `investor` (`investor_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_ibfk_2` FOREIGN KEY (`consultant_ID`) REFERENCES `consultant` (`consultant_ID`) ON DELETE CASCADE;

--
-- Constraints for table `investment_portfolio`
--
ALTER TABLE `investment_portfolio`
  ADD CONSTRAINT `investment_portfolio_ibfk_1` FOREIGN KEY (`property_ID`) REFERENCES `property` (`property_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `investment_portfolio_ibfk_2` FOREIGN KEY (`investor_ID`) REFERENCES `investor` (`investor_ID`) ON DELETE CASCADE;

--
-- Constraints for table `property`
--
ALTER TABLE `property`
  ADD CONSTRAINT `property_ibfk_1` FOREIGN KEY (`seller_ID`) REFERENCES `seller` (`seller_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
