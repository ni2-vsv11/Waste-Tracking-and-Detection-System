<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 600px;
            color: white;
        }
        .feature-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #28a745;
        }
        .login-buttons .btn {
            min-width: 200px;
            margin: 10px;
        }
        .feature-image {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-recycle mr-2"></i>
                Waste Detection System
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Welcome to Waste Detection System</h1>
            <p class="lead mb-5">Help us keep our environment clean by reporting waste in your area.</p>
            <div class="login-buttons">
                <a href="login.php?role=user" class="btn btn-success btn-lg">User Login</a>
                <a href="login.php?role=admin" class="btn btn-warning btn-lg">Admin Login</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card feature-card">
                        <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="card-img-top feature-image" alt="Report Waste">
                        <div class="card-body text-center">
                            <i class="fas fa-map-marker-alt feature-icon"></i>
                            <h5 class="card-title">Report Waste</h5>
                            <p class="card-text">Easily report waste in your area with precise location tracking.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card">
                        <img src="https://images.unsplash.com/photo-1503596476-1c12a8ba09a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="card-img-top feature-image" alt="Track Progress">
                        <div class="card-body text-center">
                            <i class="fas fa-tasks feature-icon"></i>
                            <h5 class="card-title">Track Progress</h5>
                            <p class="card-text">Monitor the status of your reported waste cleanup requests.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card">
                        <img src="https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="card-img-top feature-image" alt="Clean Environment">
                        <div class="card-body text-center">
                            <i class="fas fa-leaf feature-icon"></i>
                            <h5 class="card-title">Clean Environment</h5>
                            <p class="card-text">Contribute to maintaining a clean and healthy environment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1526951521990-620dc14c214b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="About Us" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <h2 class="mb-4">About Our System</h2>
                    <p class="lead">The Waste Detection System is a modern solution for waste management and reporting. Our platform enables citizens to actively participate in keeping their environment clean.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success mr-2"></i> Easy waste reporting</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i> Real-time tracking</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i> Location-based monitoring</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i> Quick response system</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Waste Detection System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 