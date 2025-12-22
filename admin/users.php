<?php
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all users
$users_sql = "SELECT u.*, 
    COUNT(wr.id) as total_reports,
    COUNT(CASE WHEN wr.status = 'pending' THEN 1 END) as pending_reports,
    COUNT(CASE WHEN wr.status = 'completed' THEN 1 END) as completed_reports
    FROM users u 
    LEFT JOIN waste_reports wr ON u.id = wr.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY u.created_at DESC";
$users_result = $conn->query($users_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1603228254119-e6a4d095dc59?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 30px;
        }
        .user-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 30px;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .user-stats {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            border-right: 1px solid #dee2e6;
        }
        .stat-item:last-child {
            border-right: none;
        }
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .user-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 15px;
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
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users mr-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-clipboard-list mr-1"></i>Reports
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
            <h1 class="display-4">Users Management</h1>
            <p class="lead">Monitor and manage system users</p>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row">
            <?php while ($user = $users_result->fetch_assoc()) { ?>
                <div class="col-md-6">
                    <div class="card user-card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="user-avatar mx-auto">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($user['email']); ?><br>
                                    <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($user['phone']); ?>
                                </p>
                            </div>

                            <div class="user-stats">
                                <div class="row">
                                    <div class="col-4 stat-item">
                                        <div class="stat-number text-primary"><?php echo $user['total_reports']; ?></div>
                                        <div class="stat-label">Total Reports</div>
                                    </div>
                                    <div class="col-4 stat-item">
                                        <div class="stat-number text-warning"><?php echo $user['pending_reports']; ?></div>
                                        <div class="stat-label">Pending</div>
                                    </div>
                                    <div class="col-4 stat-item">
                                        <div class="stat-number text-success"><?php echo $user['completed_reports']; ?></div>
                                        <div class="stat-label">Completed</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    Joined: <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 