<?php
require_once '../includes/auth.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/db.php';
    
    // Debugging: Check if we have the necessary data
    error_log('Logout initiated - User ID: ' . ($_SESSION['user_id'] ?? 'none') . 
            ', Token exists: ' . (isset($_COOKIE['remember_token']) ? 'yes' : 'no'));

    // Delete token from database if cookie exists
    if (isset($_COOKIE['remember_token']) && isset($_SESSION['user_id'])) {
        try {
            // Get and hash the token exactly like during login
            $token = $_COOKIE['remember_token'];
            $hashedToken = hash('sha256', $token);
            
            error_log("Attempting to delete token: $hashedToken for user: " . $_SESSION['user_id']);

            $stmt = $conn->prepare("UPDATE users SET remember_token = NULL 
                                    WHERE id = :id AND remember_token = :token");
            $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':token', $hashedToken);
            
            if ($stmt->execute()) {
                $rowsAffected = $stmt->rowCount();
                error_log("Token deletion query executed. Rows affected: $rowsAffected");
                
                if ($rowsAffected === 0) {
                    error_log("No rows affected - token didn't match or user not found");
                }
            } else {
                $error = $stmt->errorInfo();
                error_log("Database error: " . $error[2]);
            }
            
        } catch (PDOException $e) {
            error_log('Database error during logout: ' . $e->getMessage());
        }
    }
    
    // Clear the remember token cookie
    clearRememberTokenCookie();
    
    // Destroy the session
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    header("Location: login.php");
    exit;
} else {
    // Redirect if accessed directly via GET
    header("Location: login.php");
    exit;
}