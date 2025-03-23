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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITitanix - View Applicant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .container {
            max-width: 1000px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .document-img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .applicant-photo {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h1>Applicant Details</h1>
            <div>
                <a href="index.php" class="btn btn-secondary me-2">Back to List</a>
                <a href="export.php?id=<?php echo $id; ?>" class="btn btn-success">Export Data</a>
            </div>
        </header>
        
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <img src="../<?php echo htmlspecialchars($applicant['photo']); ?>" alt="Applicant Photo" class="applicant-photo mb-3">
                <h4><?php echo htmlspecialchars($applicant['full_name']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($applicant['nickname']); ?></p>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8"><?php echo htmlspecialchars($applicant['email']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Gender:</div>
                            <div class="col-md-8"><?php echo htmlspecialchars($applicant['gender']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Birth Date:</div>
                            <div class="col-md-8"><?php echo date('d F Y', strtotime($applicant['birth_date'])); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Phone Number:</div>
                            <div class="col-md-8"><?php echo htmlspecialchars($applicant['phone_number']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Address in Padang:</div>
                            <div class="col-md-8"><?php echo nl2br(htmlspecialchars($applicant['address_in_padang'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Academic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-3 fw-bold">Faculty:</div>
                            <div class="col-md-9"><?php echo htmlspecialchars($applicant['faculty']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-bold">Department:</div>
                            <div class="col-md-9"><?php echo htmlspecialchars($applicant['department']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-bold">Study Program:</div>
                            <div class="col-md-9"><?php echo htmlspecialchars($applicant['study_program']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-bold">Previous School:</div>
                            <div class="col-md-9"><?php echo htmlspecialchars($applicant['previous_school']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Motivation & Skills</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Motivation:</h6>
                            <p><?php echo nl2br(htmlspecialchars($applicant['motivation'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <h6>Future Plans:</h6>
                            <p><?php echo nl2br(htmlspecialchars($applicant['future_plans'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <h6>Reason to Join:</h6>
                            <p><?php echo nl2br(htmlspecialchars($applicant['reason_to_join'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <h6>Software Skills:</h6>
                            <p><?php echo htmlspecialchars($applicant['software_used']); ?></p>
                            <?php if (!empty($applicant['other_software'])): ?>
                                <p><strong>Other Software:</strong> <?php echo htmlspecialchars($applicant['other_software']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6>Payment Proof:</h6>
                                <a href="../<?php echo htmlspecialchars($applicant['payment_proof']); ?>" target="_blank">
                                    <img src="../<?php echo htmlspecialchars($applicant['payment_proof']); ?>" alt="Payment Proof" class="document-img">
                                </a>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>Student Card:</h6>
                                <a href="../<?php echo htmlspecialchars($applicant['student_card']); ?>" target="_blank">
                                    <img src="../<?php echo htmlspecialchars($applicant['student_card']); ?>" alt="Student Card" class="document-img">
                                </a>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>Study Plan Card:</h6>
                                <a href="../<?php echo htmlspecialchars($applicant['study_plan_card']); ?>" target="_blank">
                                    <img src="../<?php echo htmlspecialchars($applicant['study_plan_card']); ?>" alt="Study Plan Card" class="document-img">
                                </a>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>Instagram Follow Proof:</h6>
                                <a href="../<?php echo htmlspecialchars($applicant['ig_follow_proof']); ?>" target="_blank">
                                    <img src="../<?php echo htmlspecialchars($applicant['ig_follow_proof']); ?>" alt="Instagram Follow Proof" class="document-img">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Submission Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-3 fw-bold">Submission Date:</div>
                            <div class="col-md-9"><?php echo date('d F Y H:i:s', strtotime($applicant['submission_date'])); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-bold">Application ID:</div>
                            <div class="col-md-9"><?php echo $applicant['id']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>