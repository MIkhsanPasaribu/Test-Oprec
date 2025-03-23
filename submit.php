<?php
// Enable CORS
header("Access-Control-Allow-Origin: https://your-vercel-domain.vercel.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";  // Change to your MySQL username
$password = "";      // Change to your MySQL password
$dbname = "ititanix_recruitment";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die(json_encode(['success' => false, 'message' => "Error creating database: " . $conn->error]));
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
    software_used TEXT,
    other_software TEXT,
    payment_proof VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NOT NULL,
    student_card VARCHAR(255) NOT NULL,
    study_plan_card VARCHAR(255) NOT NULL,
    ig_follow_proof VARCHAR(255) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die(json_encode(['success' => false, 'message' => "Error creating table: " . $conn->error]));
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $conn->real_escape_string($_POST['email']);
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $nickname = $conn->real_escape_string($_POST['nickname']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);
    $faculty = $conn->real_escape_string($_POST['faculty']);
    $department = $conn->real_escape_string($_POST['department']);
    $studyProgram = $conn->real_escape_string($_POST['studyProgram']);
    $previousSchool = $conn->real_escape_string($_POST['previousSchool']);
    $addressInPadang = $conn->real_escape_string($_POST['addressInPadang']);
    $phoneNumber = $conn->real_escape_string($_POST['phoneNumber']);
    $motivation = $conn->real_escape_string($_POST['motivation']);
    $futurePlans = $conn->real_escape_string($_POST['futurePlans']);
    $reasonToJoin = $conn->real_escape_string($_POST['reasonToJoin']);
    
    // Process software checkboxes
    $softwareUsed = isset($_POST['software']) ? implode(", ", $_POST['software']) : "";
    $otherSoftware = isset($_POST['otherSoftwareText']) ? $conn->real_escape_string($_POST['otherSoftwareText']) : "";
    
    // Create uploads directory if it doesn't exist
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Function to handle file uploads
    function uploadFile($fileInput, $uploadDir) {
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] != 0) {
            return ['success' => false, 'message' => "Error uploading $fileInput"];
        }
        
        $targetFile = $uploadDir . basename($_FILES[$fileInput]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if file is an actual image
        $check = getimagesize($_FILES[$fileInput]["tmp_name"]);
        if($check === false) {
            return ['success' => false, 'message' => "File $fileInput is not an image."];
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES[$fileInput]["size"] > 5000000) {
            return ['success' => false, 'message' => "File $fileInput is too large."];
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            return ['success' => false, 'message' => "Only JPG, JPEG, PNG files are allowed for $fileInput."];
        }
        
        // Generate unique filename
        $newFilename = uniqid() . '.' . $imageFileType;
        $targetFile = $uploadDir . $newFilename;
        
        // Upload file
        if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $targetFile)) {
            return ['success' => true, 'path' => $targetFile];
        } else {
            return ['success' => false, 'message' => "Error uploading file $fileInput."];
        }
    }
    
    // Upload files
    $paymentProofUpload = uploadFile("paymentProof", $uploadDir);
    $photoUpload = uploadFile("photo", $uploadDir);
    $studentCardUpload = uploadFile("studentCard", $uploadDir);
    $studyPlanCardUpload = uploadFile("studyPlanCard", $uploadDir);
    $igFollowProofUpload = uploadFile("igFollowProof", $uploadDir);
    
    // Check if any file upload failed
    if (
        !$paymentProofUpload['success'] || 
        !$photoUpload['success'] || 
        !$studentCardUpload['success'] || 
        !$studyPlanCardUpload['success'] || 
        !$igFollowProofUpload['success']
    ) {
        $errorMessage = "Error uploading files: ";
        if (!$paymentProofUpload['success']) $errorMessage .= $paymentProofUpload['message'] . " ";
        if (!$photoUpload['success']) $errorMessage .= $photoUpload['message'] . " ";
        if (!$studentCardUpload['success']) $errorMessage .= $studentCardUpload['message'] . " ";
        if (!$studyPlanCardUpload['success']) $errorMessage .= $studyPlanCardUpload['message'] . " ";
        if (!$igFollowProofUpload['success']) $errorMessage .= $igFollowProofUpload['message'] . " ";
        
        die(json_encode(['success' => false, 'message' => $errorMessage]));
    }
    
    // Insert data into database
    $sql = "INSERT INTO applicants (
        email, full_name, nickname, gender, birth_date, faculty, department, 
        study_program, previous_school, address_in_padang, phone_number, 
        motivation, future_plans, reason_to_join, software_used, other_software,
        payment_proof, photo, student_card, study_plan_card, ig_follow_proof
    ) VALUES (
        '$email', '$fullName', '$nickname', '$gender', '$birthDate', '$faculty', '$department',
        '$studyProgram', '$previousSchool', '$addressInPadang', '$phoneNumber',
        '$motivation', '$futurePlans', '$reasonToJoin', '$softwareUsed', '$otherSoftware',
        '{$paymentProofUpload['path']}', '{$photoUpload['path']}', '{$studentCardUpload['path']}', 
        '{$studyPlanCardUpload['path']}', '{$igFollowProofUpload['path']}'
    )";
    
    if ($conn->query($sql) === TRUE) {
        // Send confirmation email if requested
        if (isset($_POST['sendCopy']) && $_POST['sendCopy'] == 'on') {
            $to = $email;
            $subject = "ITitanix Open Recruitment - Application Confirmation";
            $message = "
            <html>
            <head>
                <title>Application Confirmation</title>
            </head>
            <body>
                <h2>Thank you for applying to ITitanix!</h2>
                <p>Dear $fullName,</p>
                <p>We have received your application for the ITitanix Open Recruitment. Here's a summary of your submission:</p>
                <ul>
                    <li><strong>Name:</strong> $fullName</li>
                    <li><strong>Email:</strong> $email</li>
                    <li><strong>Faculty:</strong> $faculty</li>
                    <li><strong>Department:</strong> $department</li>
                </ul>
                <p>We will review your application and contact you soon.</p>
                <p>Best regards,<br>ITitanix Recruitment Team</p>
            </body>
            </html>
            ";
            
            // Set content-type header for sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: recruitment@ititanix.org' . "\r\n";
            
            mail($to, $subject, $message, $headers);
        }
        
        // Return success response for AJAX or redirect for form submit
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true]);
        } else {
            header("Location: success.php");
        }
        exit();
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => "Error: " . $sql . "<br>" . $conn->error]);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>