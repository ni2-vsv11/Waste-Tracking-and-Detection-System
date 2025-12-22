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

// Fetch all reports with user details using prepared statement
$reports_sql = "SELECT wr.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
                FROM waste_reports wr 
                JOIN users u ON wr.user_id = u.id 
                ORDER BY wr.created_at DESC";
$reports_result = $conn->query($reports_sql);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_reports,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_reports,
    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_reports,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_reports,
    COUNT(DISTINCT user_id) as total_users
    FROM waste_reports";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        .dashboard-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 30px;
        }
        .quick-stats {
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)),
                        url('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            position: relative;
            color: white;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: white;
        }
        .map-container {
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background: white;
        }
        #map {
            height: 100%;
            width: 100%;
        }
        .reports-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background: white;
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
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
        .user-info {
            background: rgba(0,0,0,0.05);
            padding: 10px;
            border-radius: 10px;
            margin-top: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt mr-2"></i>
                WDS Admin
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-clipboard-list mr-1"></i>All Reports
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
            <h1 class="display-4">Admin Dashboard</h1>
            <p class="lead">Monitor and manage waste reports across the system</p>
        </div>
    </header>

    <div class="container">
        <div class="quick-stats mb-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-clipboard-list stat-icon"></i>
                            <h3><?php echo $stats['total_reports']; ?></h3>
                            <p class="mb-0">Total Reports</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-clock stat-icon"></i>
                            <h3><?php echo $stats['pending_reports']; ?></h3>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-spinner stat-icon"></i>
                            <h3><?php echo $stats['in_progress_reports']; ?></h3>
                            <p class="mb-0">In Progress</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-check-circle stat-icon"></i>
                            <h3><?php echo $stats['completed_reports']; ?></h3>
                            <p class="mb-0">Completed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="map-container mb-4">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <div class="card reports-card">
            <div class="card-header bg-white">
                <h4 class="mb-0">Recent Reports</h4>
            </div>
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
                                <th>Location</th>
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
                                        <strong><?php echo htmlspecialchars($report['user_name']); ?></strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($report['user_email']); ?><br>
                                            <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($report['user_phone']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($report['waste_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($report['address']); ?></small><br>
                                        <a href="#" class="show-on-map" data-lat="<?php echo $report['location_lat']; ?>" data-lng="<?php echo $report['location_lng']; ?>">
                                            <i class="fas fa-map-marker-alt mr-1"></i>View on map
                                        </a>
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
                                        <form action="" method="POST" class="d-inline">
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
        // Initialize map
        var map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var markers = [];

        // Add markers for all reports
        <?php 
        mysqli_data_seek($reports_result, 0);
        while($report = $reports_result->fetch_assoc()) { 
            $status_color = 
                $report['status'] == 'pending' ? 'orange' : 
                ($report['status'] == 'in_progress' ? 'blue' : 
                ($report['status'] == 'completed' ? 'green' : 'red'));
        ?>
            var marker = L.marker([<?php echo $report['location_lat']; ?>, <?php echo $report['location_lng']; ?>])
                .bindPopup(
                    '<strong><?php echo htmlspecialchars($report['waste_type']); ?></strong><br>' +
                    '<small><?php echo htmlspecialchars($report['description']); ?></small><br>' +
                    '<span class="text-<?php echo $status_color; ?>">Status: <?php echo ucfirst($report['status']); ?></span><br>' +
                    '<small>Reported by: <?php echo htmlspecialchars($report['user_name']); ?></small>'
                );
            markers.push(marker);
            marker.addTo(map);
        <?php } ?>

        // Fit map to show all markers
        if (markers.length > 0) {
            var group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds());
        }

        // Show location on map when clicked
        $('.show-on-map').click(function(e) {
            e.preventDefault();
            var lat = $(this).data('lat');
            var lng = $(this).data('lng');
            map.setView([lat, lng], 15);
        });
    </script>
</body>
</html> 