<?php
session_start();
require_once '../config/db.php';
require_once '../models/user.php';

$usermodel = new user($conn);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rpassword = $_POST['rpassword'];
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $role = trim($_POST['role']);

    if (empty($name) || empty($email) || empty($password) || empty($rpassword) || empty($phone) || empty($gender)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $rpassword) {
        $error = "Passwords do not match!";
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $usermodel->addUser($name, $email, $hashed, $phone, $gender, $role);
            header("Location: login.php?email=" . urlencode($email));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register</title>

    <!-- Bootstrap CSS (محلي) -->
    <link rel="stylesheet" href="../assets/bootstrap-5.3.3-dist/css/bootstrap.css" />
    <!-- Font Awesome CSS (من CDN خارجي لتشغيل الأيقونات) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3a0ca3;
        --accent-color: #4cc9f0;
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .register-container {
        max-width: 500px;
        margin: auto;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: fadeIn 0.6s ease-in-out;
    }

    .register-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 25px;
        text-align: center;
    }

    .register-body {
        padding: 30px;
        background-color: white;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    .btn-register {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        padding: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
    }

    .create-account {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .create-account:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body class="d-flex align-items-center">
    <div class="container">
        <div class="register-container mt-5">
            <div class="register-header">
                <h2><i class="fas fa-user-plus me-2"></i>Register</h2>
            </div>
            <div class="register-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-user" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="text" class="form-control" name="name" id="name" required
                                placeholder="Your full name" style="border-left: none;" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-envelope" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="email" class="form-control" name="email" id="email" required
                                placeholder="Enter your email" style="border-left: none;" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-lock" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="password" class="form-control" name="password" id="password" required
                                placeholder="Create a password" style="border-left: none;" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rpassword" class="form-label">Repeat Password</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-lock" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="password" class="form-control" name="rpassword" id="rpassword" required
                                placeholder="Repeat your password" style="border-left: none;" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-phone" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="text" class="form-control" name="phone" id="phone" required
                                placeholder="Your phone number" style="border-left: none;" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-venus-mars" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="text" class="form-control" name="gender" list="gender" id="gender" required
                                placeholder="Select gender" style="border-left: none;" />
                        </div>
                        <datalist id="gender">
                            <option value="male"></option>
                            <option value="female"></option>
                        </datalist>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Role</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-user-tag" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="text" class="form-control" name="role" list="role" id="role" required
                                placeholder="Choose role" style="border-left: none;" />
                        </div>
                        <datalist id="role">
                            <option value="User"></option>
                            <option value="employee"></option>
                        </datalist>
                    </div>

                    <button type="submit" class="btn btn-register w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>

                    <div class="text-center">
                        <p class="mb-0">Already have an account?
                            <a href="login.php" class="create-account">Login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>