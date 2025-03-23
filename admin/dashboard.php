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

// Get total applicants
$sql = "SELECT COUNT(*) as total FROM applicants";
$result = $conn->query($sql);
$totalApplicants = $result->fetch_assoc()['total'];

// Get applicants by faculty
$sql = "SELECT faculty, COUNT(*) as count FROM applicants GROUP BY faculty ORDER BY count DESC";
$facultyResult = $conn->query($sql);

// Get applicants by gender
$sql = "SELECT gender, COUNT(*) as count FROM applicants GROUP BY gender";
$genderResult = $conn->query($sql);

// Get recent applicants
$sql = "SELECT id, full_name, email, submission_date FROM applicants ORDER BY submission_date DESC LIMIT 5";
$recentResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITitanix - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .container {
            max-width: 1200px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h1>ITitanix Admin Dashboard</h1>
            <div>
                <a href="index.php" class="btn btn-primary me-2">View Applicants</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users stat-icon"></i>
                        <h2><?php echo $totalApplicants; ?></h2>
                        <h5>Total Applicants</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt stat-icon"></i>
                        <h2><?php echo date('d'); ?></h2>
                        <h5>Days Remaining</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line stat-icon"></i>
                        <h2><?php echo round($totalApplicants / max(1, (time() - strtotime('-7 days')) / 86400), 1); ?></h2>
                        <h5>Applications Per Day</h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Applicants by Faculty</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $facultyResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['faculty']); ?></td>
                                            <td><?php echo $row['count']; ?></td>
                                            <td><?php echo round(($row['count'] / $totalApplicants) * 100, 1); ?>%</td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Applicants</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $recentResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($row['submission_date'])); ?></td>
                                            <td>
                                                <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Gender Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Gender</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $genderResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                            <td><?php echo $row['count']; ?></td>
                                            <td><?php echo round(($row['count'] / $totalApplicants) * 100, 1); ?>%</td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="export_all.php" class="btn btn-success">
                                <i class="fas fa-file-export me-2"></i> Export All Data
                            </a>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i> View All Applicants
                            </a>
                            <a href="../index.html" target="_blank" class="btn btn-info">
                                <i class="fas fa-external-link-alt me-2"></i> View Registration Form
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>