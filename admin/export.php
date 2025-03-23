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

// Get applicant details
$sql = "SELECT * FROM applicants WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$applicant = $result->fetch_assoc();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="applicant_' . $id . '.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Set column headers
fputcsv($output, [
    'ID', 'Email', 'Full Name', 'Nickname', 'Gender', 'Birth Date',
    'Faculty', 'Department', 'Study Program', 'Previous School',
    'Address in Padang', 'Phone Number', 'Motivation', 'Future Plans',
    'Reason to Join', 'Software Used', 'Other Software', 'Submission Date'
]);

// Format data for CSV
$csvData = [
    $applicant['id'],
    $applicant['email'],
    $applicant['full_name'],
    $applicant['nickname'],
    $applicant['gender'],
    $applicant['birth_date'],
    $applicant['faculty'],
    $applicant['department'],
    $applicant['study_program'],
    $applicant['previous_school'],
    $applicant['address_in_padang'],
    $applicant['phone_number'],
    $applicant['motivation'],
    $applicant['future_plans'],
    $applicant['reason_to_join'],
    $applicant['software_used'],
    $applicant['other_software'],
    $applicant['submission_date']
];

// Output the data
fputcsv($output, $csvData);

// Close the file pointer
fclose($output);
exit;
?>