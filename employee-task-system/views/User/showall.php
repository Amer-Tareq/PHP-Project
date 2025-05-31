<?php
require_once '../../config/db.php';
require_once '../../models/user.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';

checkUser();

$userModel = new User($conn);
$users = $userModel->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-table {
            margin-top: 20px;
        }
        .role-badge {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>👥 All Users</h2>
        <a href="user.php" class="btn btn-primary">Back to Profile</a>
    </div>

    <?php if (empty($users)): ?>
        <div class="alert alert-info">No users found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover user-table">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php
                                $badgeClass = match($user['role']) {
                                    'admin' => 'danger',
                                    'employee' => 'warning',
                                    'user' => 'success',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $badgeClass ?> role-badge">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="../../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>