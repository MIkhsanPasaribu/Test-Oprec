<?php
// Start session for login
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Include database configuration
require_once '../config.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Get applicant details to delete files
$sql = "SELECT payment_proof, photo, student_card, study_plan_card, ig_follow_proof FROM applicants WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $applicant = $result->fetch_assoc();
    
    // Delete uploaded files
    $files = [
        $applicant['payment_proof'],
        $applicant['photo'],
        $applicant['student_card'],
        $applicant['study_plan_card'],
        $applicant['ig_follow_proof']
    ];
    
    foreach ($files as $file) {
        if (file_exists("../" . $file)) {
            unlink("../" . $file);
        }
    }
    
    // Delete from database
    $sql = "DELETE FROM applicants WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        // Set success message
        $_SESSION['message'] = "Applicant deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        // Set error message
        $_SESSION['message'] = "Error deleting applicant: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
}

// Redirect back to admin index
header("Location: index.php");
exit;
?>