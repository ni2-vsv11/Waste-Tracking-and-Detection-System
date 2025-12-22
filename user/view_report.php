<?php
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$report_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch report details with prepared statement
$sql = "SELECT * FROM waste_reports WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        .report-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1591193686104-fddba4cb0539?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        .report-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        #map {
            height: 300px;
            border-radius: 10px;
        }
        .status-badge {
            font-size: 1rem;
            padding: 8px 15px;
            border-radius: 20px;
        }
        .report-image {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .detail-row {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-recycle mr-2"></i>WDS
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
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="report-header text-center">
        <div class="container">
            <h1 class="display-4">Report Details</h1>
            <p class="lead">View detailed information about your waste report</p>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card report-card mb-4">
                    <div class="card-body">
                        <div class="detail-row">
                            <h5 class="text-muted mb-2">Status</h5>
                            <span class="badge status-badge <?php 
                                echo $report['status'] == 'pending' ? 'badge-warning' : 
                                    ($report['status'] == 'in_progress' ? 'badge-info' : 
                                    ($report['status'] == 'completed' ? 'badge-success' : 'badge-danger')); 
                            ?>">
                                <?php echo ucfirst($report['status']); ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <h5 class="text-muted mb-2">Waste Type</h5>
                            <p class="mb-0">
                                <i class="fas <?php 
                                    echo $report['waste_type'] == 'Household' ? 'fa-home' : 
                                        ($report['waste_type'] == 'Industrial' ? 'fa-industry' : 
                                        ($report['waste_type'] == 'Medical' ? 'fa-medkit' : 'fa-laptop')); 
                                ?> mr-2"></i>
                                <?php echo htmlspecialchars($report['waste_type']); ?>
                            </p>
                        </div>
                        <div class="detail-row">
                            <h5 class="text-muted mb-2">Description</h5>
                            <p class="mb-0"><?php echo htmlspecialchars($report['description']); ?></p>
                        </div>
                        <div class="detail-row">
                            <h5 class="text-muted mb-2">Location</h5>
                            <p class="mb-0"><?php echo htmlspecialchars($report['address']); ?></p>
                        </div>
                        <div class="detail-row">
                            <h5 class="text-muted mb-2">Reported On</h5>
                            <p class="mb-0">
                                <i class="far fa-calendar-alt mr-2"></i>
                                <?php echo date('F d, Y h:i A', strtotime($report['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($report['image_url']) { ?>
                    <div class="card report-card mb-4">
                        <div class="card-body">
                            <h5 class="text-muted mb-3">Report Image</h5>
                            <img src="<?php echo htmlspecialchars($report['image_url']); ?>" alt="Report Image" class="report-image">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="col-md-4">
                <div class="card report-card mb-4">
                    <div class="card-body">
                        <h5 class="text-muted mb-3">Location on Map</h5>
                        <div id="map"></div>
                    </div>
                </div>

                <a href="dashboard.php" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize map
        var map = L.map('map').setView([<?php echo $report['location_lat']; ?>, <?php echo $report['location_lng']; ?>], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add marker
        L.marker([<?php echo $report['location_lat']; ?>, <?php echo $report['location_lng']; ?>])
            .addTo(map)
            .bindPopup('<?php echo htmlspecialchars($report['waste_type']); ?> Waste Location');
    </script>
</body>
</html> 