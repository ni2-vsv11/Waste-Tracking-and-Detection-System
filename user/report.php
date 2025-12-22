<?php
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $waste_type = mysqli_real_escape_string($conn, $_POST['waste_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $lat = mysqli_real_escape_string($conn, $_POST['lat']);
    $lng = mysqli_real_escape_string($conn, $_POST['lng']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_extension, $allowed_types)) {
            $image_url = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_url)) {
                // Image uploaded successfully
            } else {
                $error = "Error uploading image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO waste_reports (user_id, waste_type, description, location_lat, location_lng, address, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $user_id, $waste_type, $description, $lat, $lng, $address, $image_url);
        
        if ($stmt->execute()) {
            $success = "Waste report submitted successfully!";
            // Redirect to dashboard after successful submission
            header("refresh:2;url=dashboard.php");
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Report - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        .report-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .waste-type-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            border-radius: 15px;
            margin-bottom: 15px;
        }
        .waste-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .waste-type-card.selected {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }
        .waste-type-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #28a745;
        }
        .map-container {
            position: relative;
        }
        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .image-preview {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 10px;
        }
        .form-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
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
            <h1 class="display-4">Report Waste</h1>
            <p class="lead">Help keep our environment clean by reporting waste in your area</p>
        </div>
    </header>

    <div class="container mb-5">
        <?php if ($success) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>

        <div class="card form-card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data" id="reportForm">
                    <div class="form-group">
                        <label class="font-weight-bold">Waste Type</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card waste-type-card" data-type="Household">
                                    <div class="card-body text-center">
                                        <i class="fas fa-home waste-type-icon"></i>
                                        <h6 class="mb-0">Household</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card waste-type-card" data-type="Industrial">
                                    <div class="card-body text-center">
                                        <i class="fas fa-industry waste-type-icon"></i>
                                        <h6 class="mb-0">Industrial</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card waste-type-card" data-type="Medical">
                                    <div class="card-body text-center">
                                        <i class="fas fa-medkit waste-type-icon"></i>
                                        <h6 class="mb-0">Medical</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card waste-type-card" data-type="Electronic">
                                    <div class="card-body text-center">
                                        <i class="fas fa-laptop waste-type-icon"></i>
                                        <h6 class="mb-0">Electronic</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="waste_type" id="selectedWasteType" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" required 
                                placeholder="Please provide details about the waste..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Location</label>
                        <div class="map-container">
                            <div id="map"></div>
                            <div class="map-controls">
                                <button type="button" class="btn btn-light" onclick="locateMe()">
                                    <i class="fas fa-location-arrow mr-1"></i>My Location
                                </button>
                            </div>
                        </div>
                        <input type="text" id="address" name="address" class="form-control" required readonly>
                        <input type="hidden" id="lat" name="lat" required>
                        <input type="hidden" id="lng" name="lng" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Image</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="imageInput" accept="image/*" required>
                            <label class="custom-file-label" for="imageInput">Choose file</label>
                        </div>
                        <div id="imagePreview"></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Report
                    </button>
                </form>
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

        var marker;
        var selectedWasteType = '';

        // Get user's location
        function locateMe() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    map.setView([lat, lng], 15);
                    
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                        marker.on('dragend', function(e) {
                            updateLocationFields(e.target.getLatLng().lat, e.target.getLatLng().lng);
                        });
                    }
                    
                    updateLocationFields(lat, lng);
                });
            } else {
                alert('Geolocation is not supported by your browser');
            }
        }

        // Handle map clicks
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    updateLocationFields(e.target.getLatLng().lat, e.target.getLatLng().lng);
                });
            }
            
            updateLocationFields(lat, lng);
        });

        function updateLocationFields(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            
            // Reverse geocoding using OpenStreetMap Nominatim
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('address').value = data.display_name;
                });
        }

        // Handle waste type selection
        $('.waste-type-card').click(function() {
            $('.waste-type-card').removeClass('selected');
            $(this).addClass('selected');
            selectedWasteType = $(this).data('type');
            document.getElementById('selectedWasteType').value = selectedWasteType;
        });

        // Handle image preview
        $('#imageInput').change(function() {
            var file = this.files[0];
            $(this).next('.custom-file-label').html(file.name);
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`
                        <img src="${e.target.result}" class="image-preview" alt="Preview">
                    `);
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        $('#reportForm').submit(function(e) {
            if (!selectedWasteType) {
                e.preventDefault();
                alert('Please select a waste type');
                return false;
            }
            if (!document.getElementById('lat').value || !document.getElementById('lng').value) {
                e.preventDefault();
                alert('Please select a location on the map');
                return false;
            }
            return true;
        });

        // Try to get user's location when page loads
        locateMe();
    </script>
</body>
</html> 