<?php
function setRememberTokenCookie($token, $expiryDays = 30) {
    $options = [
        'expires' => time() + (86400 * $expiryDays),
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    setcookie('remember_token', $token, $options);
}

function clearRememberTokenCookie() {
    $options = [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true
    ];
    setcookie('remember_token', '', $options);
    unset($_COOKIE['remember_token']);
    
    // Also clear user_id cookie if exists
    if (isset($_COOKIE['user_id'])) {
        setcookie('user_id', '', $options);
        unset($_COOKIE['user_id']);
    }
}

// 3. Check employee authentication
function checkEmployee($conn)
{
    

    // Auto-login via remember_token cookie
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
        } 
    }

    // Redirect if not logged in or not an employee
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
        header('Location: ../../public/login.php');
        exit;
    }

    // Return employee info
    return [
        'employeeId' => $_SESSION['user_id'],
        'employeeName' => $_SESSION['user_name'] ?? 'Employee',
    ];
}
function checkAdmin()
{
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: ../../public/login.php');
        exit();
    }
}
function checkUser()
{
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
        header('Location: ../../public/login.php');
        exit();
    }
}
function checkUserAuth($conn) {
    session_start();
    
    // Auto-login via remember_token cookie
    if (!isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
        // } else {
        //     clearRememberTokenCookie(); // remove invalid cookie
        // 
        }
    }

    // Redirect if not logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../public/login.php');
        exit;
    }

    // Return user info
    return [
        'userId' => $_SESSION['user_id'],
        'userName' => $_SESSION['user_name'] ?? 'User',
        'userRole' => $_SESSION['user_role']
    ];
}
