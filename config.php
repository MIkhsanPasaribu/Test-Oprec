<?php
// Database configuration for InfinityFree
$servername = "sql312.infinityfree.com"; // Use the MySQL hostname from InfinityFree
$username = "epiz_yourusername"; // Your InfinityFree MySQL username
$password = "your_database_password"; // Your InfinityFree MySQL password
$dbname = "epiz_yourdatabase"; // Your InfinityFree database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
$conn->select_db($dbname);

// Create applicants table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS applicants (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    nickname VARCHAR(100) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    birth_date DATE NOT NULL,
    faculty VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    study_program VARCHAR(100) NOT NULL,
    previous_school VARCHAR(255) NOT NULL,
    address_in_padang TEXT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    motivation TEXT NOT NULL,
    future_plans TEXT NOT NULL,
    reason_to_join TEXT NOT NULL,
    software_used VARCHAR(255) NOT NULL,
    other_software VARCHAR(255),
    payment_proof VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NOT NULL,
    student_card VARCHAR(255) NOT NULL,
    study_plan_card VARCHAR(255) NOT NULL,
    ig_follow_proof VARCHAR(255) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}
?>