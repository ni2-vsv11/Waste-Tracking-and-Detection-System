<?php
session_start();
require_once 'config/db_connect.php';

$role = isset($_GET['role']) ? $_GET['role'] : 'user';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    if ($role === 'admin') {
        // Admin login
        if ($email === 'admin@gmail.com' && $password === '12') {
            $_SESSION['user_id'] = 'admin';
            $_SESSION['user_role'] = 'admin';
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials!";
        }
    } else {
        // User login
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = 'user';
                header("Location: user/dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($role); ?> Login - Waste Detection System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 30px;
            margin-top: 100px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        .login-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="text-center">
                        <i class="fas <?php echo $role === 'admin' ? 'fa-user-shield' : 'fa-user'; ?> login-icon"></i>
                        <h2 class="mb-4"><?php echo ucfirst($role); ?> Login</h2>
                    </div>
                    
                    <?php if ($error) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>

                    <?php if ($role === 'user') { ?>
                        <div class="text-center mt-3">
                            <p class="mb-0">Don't have an account?</p>
                            <a href="register.php" class="btn btn-success mt-2">
                                <i class="fas fa-user-plus mr-2"></i>Register Now
                            </a>
                        </div>
                    <?php } ?>

                    <div class="text-center mt-4">
                        <a href="index.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 