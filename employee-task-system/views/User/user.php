<?php
require_once '../../config/db.php';
require_once '../../models/user.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';

checkUser();

$userModel = new User($conn);
$userId = $_SESSION['user_id'];
$a = $_COOKIE['remember_token'];
$user = $userModel->getUserById($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>
<body>
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-center mb-0">👤 User Profile</h2>
    <a href="showall.php" class="btn btn-outline-primary">View All Users</a>
</div>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                switch($_GET['success']) {
                    case 'profile_updated': echo 'Profile updated successfully!'; break;
                    case 'password_changed': echo 'Password changed successfully!'; break;
                }
                ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                switch($_GET['error']) {
                    case 'update_failed': echo 'Failed to update profile.'; break;
                    case 'wrong_password': echo 'Current password is incorrect.'; break;
                    case 'password_mismatch': echo 'New passwords do not match.'; break;
                    case 'password_change_failed': echo 'Failed to change password.'; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">Password</button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <form method="POST" action="../../controllers/userController.php">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male" <?= $user['gender'] === 'male' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female" <?= $user['gender'] === 'female' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>

            <!-- Password Tab -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <form method="POST" action="../../controllers/userController.php">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>