<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realestatedb1";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($dbname);

// SQL to create tables
$createTables = [
    "CREATE TABLE IF NOT EXISTS `admin` (
        `admin_ID` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) DEFAULT NULL,
        `password` varchar(100) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `approve` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`admin_ID`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    "CREATE TABLE IF NOT EXISTS `consultant` (
        `consultant_ID` int(11) NOT NULL AUTO_INCREMENT,
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
        `rejection_reason` TEXT DEFAULT NULL,
        PRIMARY KEY (`consultant_ID`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
 "CREATE TABLE IF NOT EXISTS `investor` (
    `investor_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Fname` varchar(50) DEFAULT NULL,
    `Lname` varchar(50) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `username` varchar(50) DEFAULT NULL,
    `password` varchar(100) DEFAULT NULL,
    `phone` varchar(15) DEFAULT NULL,
    `approve` tinyint(1) DEFAULT 0,
    `status` tinyint(1) DEFAULT 0,
    `rejection_reason` TEXT DEFAULT NULL,
    PRIMARY KEY (`investor_ID`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
"CREATE TABLE IF NOT EXISTS `seller` (
    `seller_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Fname` varchar(50) DEFAULT NULL,
    `Lname` varchar(50) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `username` varchar(50) DEFAULT NULL,
    `password` varchar(100) DEFAULT NULL,
    `phone` varchar(15) DEFAULT NULL,
    `approve` tinyint(1) DEFAULT 0,
    `status` tinyint(1) DEFAULT 0,
    `rejection_reason` TEXT DEFAULT NULL,
    PRIMARY KEY (`seller_ID`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `consultation` (
    `session_number` int(11) NOT NULL AUTO_INCREMENT,
    `investor_ID` int(11) DEFAULT NULL,
    `consultant_ID` int(11) DEFAULT NULL,
    `date` date DEFAULT NULL,
    `time` time DEFAULT NULL,
    `description` text DEFAULT NULL,
    `rating` int(11) DEFAULT NULL,
    `feedback` text DEFAULT NULL,
    `status` enum('pending', 'approved', 'disapproved') DEFAULT 'pending',
    `zoom_link` varchar(255) DEFAULT NULL,  
    `paid` boolean DEFAULT false,            
    `rejection_reason` text DEFAULT NULL,   
    PRIMARY KEY (`session_number`),
    KEY `investor_ID` (`investor_ID`),
    KEY `consultant_ID` (`consultant_ID`),
    CONSTRAINT `consultation_ibfk_1` FOREIGN KEY (`investor_ID`) REFERENCES `investor` (`investor_ID`) ON DELETE CASCADE,
    CONSTRAINT `consultation_ibfk_2` FOREIGN KEY (`consultant_ID`) REFERENCES `consultant` (`consultant_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

"CREATE TABLE IF NOT EXISTS `property` (
    `property_ID` int(11) NOT NULL AUTO_INCREMENT,
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
    `description` text DEFAULT NULL,
    PRIMARY KEY (`property_ID`),
    KEY `seller_ID` (`seller_ID`),
    CONSTRAINT `property_ibfk_1` FOREIGN KEY (`seller_ID`) REFERENCES `seller` (`seller_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    "CREATE TABLE IF NOT EXISTS `investment_portfolio` (
        `portfolio_ID` int(11) NOT NULL AUTO_INCREMENT,
        `property_ID` int(11) DEFAULT NULL,
        `investor_ID` int(11) DEFAULT NULL,
        `investment_percentage` decimal(5,2) DEFAULT NULL,
        `date` date DEFAULT NULL,
        `time` time DEFAULT NULL,
        `amount_paid` decimal(15,2) DEFAULT NULL,
        `monthly_return_amount` decimal(15,2) DEFAULT NULL,
        PRIMARY KEY (`portfolio_ID`),
        KEY `property_ID` (`property_ID`),
        KEY `investor_ID` (`investor_ID`),
        CONSTRAINT `investment_portfolio_ibfk_1` FOREIGN KEY (`property_ID`) REFERENCES `property` (`property_ID`) ON DELETE CASCADE,
        CONSTRAINT `investment_portfolio_ibfk_2` FOREIGN KEY (`investor_ID`) REFERENCES `investor` (`investor_ID`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

   

    "CREATE TABLE IF NOT EXISTS `seller_property` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `seller_ID` int(11) NOT NULL,
        `property_ID` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `seller_ID` (`seller_ID`),
        KEY `property_ID` (`property_ID`),
        CONSTRAINT `seller_property_ibfk_1` FOREIGN KEY (`seller_ID`) REFERENCES `seller` (`seller_ID`) ON DELETE CASCADE,
        CONSTRAINT `seller_property_ibfk_2` FOREIGN KEY (`property_ID`) REFERENCES `property` (`property_ID`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
];

// Execute each table creation query
foreach ($createTables as $query) {
    if ($conn->query($query) === FALSE) {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}
$password = '12345678';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
// Dummy data insertion
$dummyData = [
    "admin" => "INSERT INTO `admin` (`admin_ID`, `username`, `password`, `email`, `approve`) SELECT 1, 'admin','$hashedPassword' , 'admin@example.com', 1 WHERE NOT EXISTS (SELECT 1 FROM `admin`);",

    "consultant" => "INSERT INTO `consultant` (`consultant_ID`, `Fname`, `Lname`, `email`, `username`, `password`, `phone`, `experience`, `fee`, `company`, `rating`, `availability_start`, `availability_end`, `approve`, `status`) SELECT 1, 'saira', 'rao', 'Saira12@gmail.com', 'Sr123','$hashedPassword', '03034747081', 5, 100.00, 'Future Consultant', NULL, '08:00:00', '16:00:00', 1, 0 WHERE NOT EXISTS (SELECT 1 FROM `consultant`);",

    "investor" => "INSERT INTO `investor` (`investor_ID`, `Fname`, `Lname`, `email`, `username`, `password`, `phone`, `approve`, `status`) SELECT 1, 'John', 'Doe', 'john.doe@example.com', 'johndoe', '$hashedPassword' , '1234567890', 1, 0 WHERE NOT EXISTS (SELECT 1 FROM `investor`);",

    "seller" => "INSERT INTO `seller` (`seller_ID`, `Fname`, `Lname`, `email`, `username`, `password`, `phone`, `approve`, `status`) SELECT 1, 'Jane', 'Smith', 'jane.smith@example.com', 'janesmith','$hashedPassword' , '0987654321', 1, 0 WHERE NOT EXISTS (SELECT 1 FROM `seller`);",

    "property" => "INSERT INTO `property` (`property_ID`, `seller_ID`, `name`, `city`, `street`, `zip_code`, `type`, `size`, `cost_of_property`, `monthly_rental_returns`, `monthly_return_percentage`, `image_path`, `description`) SELECT 1, 1, 'Luxury Apartment', 'New York', '5th Avenue', '10001', 'Apartments', 100.00, 1000000.00, 5000.00, 5.00, '/path/to/image.jpg', 'A luxurious apartment in the heart of the city.' WHERE NOT EXISTS (SELECT 1 FROM `property`);",

    "investment_portfolio" => "INSERT INTO `investment_portfolio` (`portfolio_ID`, `property_ID`, `investor_ID`, `investment_percentage`, `date`, `time`, `amount_paid`, `monthly_return_amount`) SELECT 1, 1, 1, 50.00, '2024-11-01', '12:00:00', 500000.00, 2500.00 WHERE NOT EXISTS (SELECT 1 FROM `investment_portfolio`);",

    "consultation" => "INSERT INTO `consultation` (`session_number`, `investor_ID`, `consultant_ID`, `date`, `time`, `description`, `rating`, `feedback`, `status`) SELECT 1, 1, 1, '2024-11-01', '14:00:00', 'Discussion on investment strategies.', 4, 'Very helpful session.', 'approved' WHERE NOT EXISTS (SELECT 1 FROM `consultation`);",

    "seller_property" => "INSERT INTO `seller_property` (`id`, `seller_ID`, `property_ID`) SELECT 1, 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `seller_property`);"
];
foreach ($dummyData as $table => $query) {
    $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        if ($conn->query($query) === FALSE) {
            echo "Error inserting dummy data for table '$table': " . $conn->error . "<br>";
        }
    }
}

$conn->close();
?>
