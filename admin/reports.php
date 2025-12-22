<?php
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_id']) && isset($_POST['status'])) {
    $report_id = mysqli_real_escape_string($conn, $_POST['report_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_sql = "UPDATE waste_reports SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $status, $report_id);
    if ($stmt->execute()) {
        $success = "Report status updated successfully!";
    } else {
        $error = "Error updating status: " . $conn->error;
    }
}

// Handle filters
$where_conditions = [];
$params = [];
$param_types = "";

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $where_conditions[] = "wr.status = ?";
    $params[] = $_GET['status'];
    $param_types .= "s";
}

if (isset($_GET['waste_type']) && !empty($_GET['waste_type'])) {
    $where_conditions[] = "wr.waste_type = ?";
    $params[] = $_GET['waste_type'];
    $param_types .= "s";
}

if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $where_conditions[] = "DATE(wr.created_at) >= ?";
    $params[] = $_GET['date_from'];
    $param_types .= "s";
}

if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $where_conditions[] = "DATE(wr.created_at) <= ?";
    $params[] = $_GET['date_to'];
    $param_types .= "s";
}

// Build the query
$reports_sql = "SELECT wr.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
                FROM waste_reports wr 
                JOIN users u ON wr.user_id = u.id";

if (!empty($where_conditions)) {
    $reports_sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$reports_sql .= " ORDER BY wr.created_at DESC";

// Prepare and execute the query
$stmt = $conn->prepare($reports_sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$reports_result = $stmt->get_result();

// Get unique waste types for filter
$waste_types_sql = "SELECT DISTINCT waste_type FROM waste_reports";
$waste_types_result = $conn->query($waste_types_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reports - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        .dashboard-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 30px;
        }
        .filter-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .reports-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .map-preview {
            height: 200px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }
        .report-image {
            max-width: 150px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .table th {
            border-top: none;
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
        }
        .table td {
            vertical-align: middle;
        }
        .status-select {
            border-radius: 20px;
            padding: 5px 15px;
            border: 2px solid #eee;
            background: white;
        }
        .filter-form select,
        .filter-form input {
            border-radius: 20px;
            border: 2px solid #eee;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-alt mr-2"></i>WDS Admin
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-clipboard-list mr-1"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users mr-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="dashboard-header text-center">
        <div class="container">
            <h1 class="display-4">All Reports</h1>
            <p class="lead">Comprehensive view of all waste reports in the system</p>
        </div>
    </header>

    <div class="container mb-5">
        <div class="card filter-card">
            <div class="card-body">
                <form class="filter-form" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_progress" <?php echo isset($_GET['status']) && $_GET['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="rejected" <?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Waste Type</label>
                                <select name="waste_type" class="form-control">
                                    <option value="">All Types</option>
                                    <?php while($type = $waste_types_result->fetch_assoc()) { ?>
                                        <option value="<?php echo htmlspecialchars($type['waste_type']); ?>" 
                                            <?php echo isset($_GET['waste_type']) && $_GET['waste_type'] == $type['waste_type'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['waste_type']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="reports.php" class="btn btn-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card reports-card">
            <div class="card-body">
                <?php if (isset($success)) { ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php } ?>
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php } ?>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reporter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Media</th>
                                <th>Status</th>
                                <th>Reported On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($report = $reports_result->fetch_assoc()) { ?>
                                <tr>
                                    <td>#<?php echo $report['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($report['user_name']); ?></strong>
                                        <div class="small text-muted">
                                            <i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($report['user_email']); ?><br>
                                            <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($report['user_phone']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($report['waste_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($report['description']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($report['address']); ?></small>
                                        <div class="map-preview" id="map-<?php echo $report['id']; ?>"></div>
                                    </td>
                                    <td>
                                        <?php if ($report['image_url']) { ?>
                                            <img src="<?php echo htmlspecialchars($report['image_url']); ?>" class="report-image" alt="Report Image">
                                        <?php } else { ?>
                                            <span class="text-muted">No image</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="badge status-badge <?php 
                                            echo $report['status'] == 'pending' ? 'badge-warning' : 
                                                ($report['status'] == 'in_progress' ? 'badge-info' : 
                                                ($report['status'] == 'completed' ? 'badge-success' : 'badge-danger')); 
                                        ?>">
                                            <?php echo ucfirst($report['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($report['created_at'])); ?></td>
                                    <td>
                                        <form action="" method="POST">
                                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                            <select name="status" class="form-control status-select" onchange="this.form.submit()">
                                                <option value="">Update Status</option>
                                                <option value="pending" <?php echo $report['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="in_progress" <?php echo $report['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo $report['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="rejected" <?php echo $report['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize maps for each report
        <?php 
        mysqli_data_seek($reports_result, 0);
        while($report = $reports_result->fetch_assoc()) { 
        ?>
            var map<?php echo $report['id']; ?> = L.map('map-<?php echo $report['id']; ?>').setView([<?php echo $report['location_lat']; ?>, <?php echo $report['location_lng']; ?>], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map<?php echo $report['id']; ?>);
            L.marker([<?php echo $report['location_lat']; ?>, <?php echo $report['location_lng']; ?>]).addTo(map<?php echo $report['id']; ?>);
        <?php } ?>
    </script>
</body>
</html> 