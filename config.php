<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'bhps_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Accounts Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) UNIQUE NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_admin TINYINT(1) DEFAULT 0
    )");

    // Households Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS households (
        id INT AUTO_INCREMENT PRIMARY KEY,
        household_head_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Household Info Table 
    $pdo->exec("CREATE TABLE IF NOT EXISTS household_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        iodized_salt ENUM('Yes', 'No') DEFAULT 'No',
        iron_fortified_rice ENUM('Yes', 'No') DEFAULT 'No',
        toilet_type ENUM('Water sealed', 'Open pit', 'None') DEFAULT 'None',
        water_source ENUM('Community piped', 'Well', 'Spring', 'Other') DEFAULT 'Other',
        FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
    )");

    // Family Groups Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS family_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        household_id INT NOT NULL,
        family_head_id INT NULL,
        FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE
    )");

    // Residents Table 
    $pdo->exec("CREATE TABLE IF NOT EXISTS residents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_id INT NOT NULL,
        household_id INT NULL,
        family_group_id INT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        middle_name VARCHAR(100),
        suffix VARCHAR(10),
        dob DATE,
        sex ENUM('Male', 'Female', 'Other'),
        civil_status ENUM('Single', 'Married', 'Widowed', 'Separated'),
        nationality VARCHAR(50),
        occupation VARCHAR(100),
        employment_status ENUM('Employed', 'Unemployed', 'Student', 'Homemaker', 'Self-employed') DEFAULT 'Unemployed',
        educational_attainment ENUM('No Formal Education', 'Elementary', 'High School', 'Senior High School', 'Vocational / Technical', 'College / Undergraduate', 'College Graduate') DEFAULT 'No Formal Education',
        contact_no VARCHAR(20),
        id_picture LONGBLOB,
        is_household_head TINYINT(1) DEFAULT 0,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        pregnancy_status ENUM('None', 'Pregnant', 'Delivered') DEFAULT 'None',
        breastfeeding_type ENUM('None', 'Exclusive breastfeeding', 'Formula Only', 'Mixed Feeding') DEFAULT 'None',
        FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
        FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE SET NULL,
        FOREIGN KEY (family_group_id) REFERENCES family_groups(id) ON DELETE SET NULL
    )");

    // Address Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS address (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        address_type ENUM('current', 'permanent') NOT NULL,
        street VARCHAR(255),
        barangay VARCHAR(100),
        municipality VARCHAR(100),
        province VARCHAR(100),
        zip_code VARCHAR(10),
        FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
    )");

    // Archived Residents Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS archived_residents (
        archive_id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Active Requests Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS document_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        document_type VARCHAR(100) NOT NULL,
        purpose TEXT NOT NULL,
        status ENUM('Pending', 'Approved', 'Declined', 'Rejected') DEFAULT 'Pending',
        date_requested TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
    )");

    // Admin Archived Requests Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_archived_request (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        document_type VARCHAR(100) NOT NULL,
        purpose TEXT,
        status VARCHAR(50),
        date_requested TIMESTAMP,
        archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Resident Archived Requests Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS archived_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        document_type VARCHAR(100) NOT NULL,
        purpose TEXT NOT NULL,
        status VARCHAR(50),
        date_requested TIMESTAMP,
        archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
    )");

    // Notifications Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
    )");

    // Announcement Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (
        announcement_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        announcement_picture LONGBLOB,
        status ENUM('active', 'archived') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function createNotification($pdo, $resident_id, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (resident_id, message, is_read) VALUES (?, ?, 0)");
        return $stmt->execute([$resident_id, $message]);
    } catch (PDOException $e) {
        return false;
    }
}
?>