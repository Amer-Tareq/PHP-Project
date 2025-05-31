<?php
session_start();
require_once '../config/db.php';
require_once '../models/user.php';
require_once '../includes/auth.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

checkUser(); // Only authenticated users can access these actions

$userModel = new User($conn);
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Update profile
    if ($action === 'update_profile') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $gender = trim($_POST['gender']);

        if ($userModel->updateUserProfile($userId, $name, $email, $phone, $gender)) {
            $_SESSION['user_name'] = $name; // Update session name
            if ($isAjax) {
                jsonResponse(['success' => true, 'message' => 'Profile updated successfully']);
            }
            header('Location: ../views/User/user.php?success=profile_updated');
        } else {
            if ($isAjax) {
                jsonResponse(['success' => false, 'message' => 'Failed to update profile']);
            }
            header('Location: ../views/User/user.php?error=update_failed');
        }
        exit;
    }

    // Change password
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Verify current password
        $user = $userModel->getUserById($userId);
        if (!password_verify($currentPassword, $user['password'])) {
            header('Location: ../views/User/user.php?error=wrong_password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            header('Location: ../views/User/user.php?error=password_mismatch');
            exit;
        }

        if ($userModel->changePassword($userId, $newPassword)) {
            header('Location: ../views/User/user.php?success=password_changed');
        } else {
            header('Location: ../views/User/user.php?error=password_change_failed');
        }
        exit;
    }
}

// Invalid request
header('Location: ../views/User/user.php');
exit;
?>