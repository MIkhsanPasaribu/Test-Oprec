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

// Get all applicants
$sql = "SELECT * FROM applicants ORDER BY submission_date DESC";
$result = $conn->query($sql);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="all_applicants_' . date('Y-m-d') . '.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Set column headers
fputcsv($output, [
    'ID', 'Email', 'Full Name', 'Nickname', 'Gender', 'Birth Date',
    'Faculty', 'Department', 'Study Program', 'Previous School',
    'Address in Padang', 'Phone Number', 'Motivation', 'Future Plans',
    'Reason to Join', 'Software Used', 'Other Software', 'Submission Date'
]);

// Output each row of the data
while ($row = $result->fetch_assoc()) {
    $csvData = [
        $row['id'],
        $row['email'],
        $row['full_name'],
        $row['nickname'],
        $row['gender'],
        $row['birth_date'],
        $row['faculty'],
        $row['department'],
        $row['study_program'],
        $row['previous_school'],
        $row['address_in_padang'],
        $row['phone_number'],
        $row['motivation'],
        $row['future_plans'],
        $row['reason_to_join'],
        $row['software_used'],
        $row['other_software'],
        $row['submission_date']
    ];
    fputcsv($output, $csvData);
}

// Close the file pointer
fclose($output);
exit;
?>