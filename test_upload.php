<?php
// Display all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir) && !is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $target_file = $target_dir . basename($_FILES["test_file"]["name"]);
    $upload_ok = 1;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check file size (limit to 5MB for InfinityFree)
    if ($_FILES["test_file"]["size"] > 5000000) {
        echo "Sorry, your file is too large. Max size is 5MB.<br>";
        $upload_ok = 0;
    }
    
    // Allow certain file formats
    $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");
    if (!in_array($file_type, $allowed_types)) {
        echo "Sorry, only JPG, JPEG, PNG, GIF & PDF files are allowed.<br>";
        $upload_ok = 0;
    }
    
    // Check if $upload_ok is set to 0 by an error
    if ($upload_ok == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload file
        if (move_uploaded_file($_FILES["test_file"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars(basename($_FILES["test_file"]["name"])). " has been uploaded successfully!";
            echo "<br>File path: " . $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.<br>";
            echo "Error details: " . error_get_last()['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>InfinityFree Upload Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>
<body>
    <h1>InfinityFree File Upload Test</h1>
    
    <div class="info">
        <p>This page tests file uploads to your InfinityFree hosting at: <strong>ititanixrecruitment.infinityfreeapp.com</strong></p>
        <p>If uploads work here, they should work in your main application.</p>
    </div>
    
    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <p>Select a file to upload (max 5MB):</p>
            <input type="file" name="test_file" required>
            <br><br>
            <input type="submit" value="Upload File" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">
        </form>
    </div>
    
    <div style="margin-top: 20px;">
        <h3>Server Information:</h3>
        <ul>
            <li>PHP Version: <?php echo phpversion(); ?></li>
            <li>Upload Max Filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
            <li>Post Max Size: <?php echo ini_get('post_max_size'); ?></li>
            <li>Current Directory: <?php echo getcwd(); ?></li>
        </ul>
    </div>
</body>
</html>