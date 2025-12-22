<?php
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch user's reports
$reports_sql = "SELECT * FROM waste_reports WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($reports_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reports_result = $stmt->get_result();

// Get user's statistics
$stats_sql = "SELECT 
    COUNT(*) as total_reports,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_reports,
    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_reports,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_reports
    FROM waste_reports WHERE user_id = ?";
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats_result = $stmt->get_result();
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        .dashboard-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1523544261025-3159892cfba4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 30px;
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .map-container {
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        #map {
            height: 100%;
            width: 100%;
        }
        .report-card {
            border: none;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-section {
            background: url('https://images.unsplash.com/photo-1557683311-eac922347aa1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            position: relative;
        }
        .profile-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
        }
        .profile-content {
            position: relative;
            z-index: 1;
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-recycle mr-2"></i>WDS
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="report.php">
                            <i class="fas fa-plus-circle mr-1"></i>New Report
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
            <h1 class="display-4">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p class="lead">Track and manage your waste reports</p>
        </div>
    </header>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <h5>Total Reports</h5>
                        <h2><?php echo $stats['total_reports']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <h5>Pending</h5>
                        <h2><?php echo $stats['pending_reports']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-spinner fa-3x mb-3"></i>
                        <h5>In Progress</h5>
                        <h2><?php echo $stats['in_progress_reports']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>Completed</h5>
                        <h2><?php echo $stats['completed_reports']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="profile-section">
                    <div class="profile-content">
                        <h4 class="mb-4">Profile Information</h4>
                        <p><i class="fas fa-user mr-2"></i> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><i class="fas fa-envelope mr-2"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><i class="fas fa-phone mr-2"></i> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <a href="edit_profile.php" class="btn btn-primary">
                            <i class="fas fa-edit mr-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="map-container">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <div class="card report-card">
            <div class="card-header bg-white">
                <h4 class="mb-0">Recent Reports</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($report = $reports_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($report['waste_type']); ?></td>
                                    <td>
                                        <small><?php echo htmlspecialchars($report['address']); ?></small>
                                        <br>
                                        <a href="#" class="show-on-map" data-lat="<?php echo $report['location_lat']; ?>" data-lng="<?php echo $report['location_lng']; ?>">Show on map</a>
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
                                    <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                                    <td>
                                        <a href="view_report.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
        
        <?php 
        mysqli_data_seek($reports_result, 0);
        while($report = $reports_result->fetch_assoc()) { 
        ?>
            var marker = L.marker([<?php echo $report['location_lat']; ?>, <?php echo $report['location_lng']; ?>])
                .bindPopup(
                    '<strong><?php echo htmlspecialchars($report['waste_type']); ?></strong><br>' +
                    '<?php echo htmlspecialchars($report['description']); ?><br>' +
                    '<small>Status: <?php echo ucfirst($report['status']); ?></small>'
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