<?php 
session_start();
require_once '../config/db.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                // Remember me functionality
                if (!empty($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + (86400 * 30); // 30 days
                    
                    setcookie('remember_token', $token, $expiry, "/", "", true, true);
                    setcookie('user_id', $user['id'], $expiry, "/", "", true, true);
                    
                    $stmt = $conn->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                    $stmt->execute([':token' => hash('sha256', $token), ':id' => $user['id']]);
                }

                // Redirect based on role
                switch($user['role']) {
                    case 'admin':
                        header('Location: ../views/admin/dashboard.php');
                        break;
                    case 'employee':
                        header('Location: ../views/employee/dashboard.php');
                        break;
                    case 'user':
                        header('Location: ../views/User/user.php');
                        break;
                    default:
                        header('Location: ../index.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password!';
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body class="d-flex align-items-center">
    <div class="container">
        <div class="login-container mx-auto">
            <div class="login-header">
                <h2><i class="fas fa-sign-in-alt me-2"></i>Login</h2>
            </div>
            <div class="login-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter your email" required value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                    </div>

                    <div class="mb-4 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter your password" required>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="1">
                        <label class="form-check-label remember-me" for="remember_me">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-login btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>

                    <div class="text-center mt-3">
                        <p class="mb-0">Don't have an account?
                            <a href="register.php" class="create-account">Create one</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Focus on email field when page loads
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('email').focus();
    });
    </script>
    </