<?php
require_once '../../config/db.php';
require_once '../../models/task.php';
require_once '../../models/user.php';
require_once '../../includes/auth.php';

checkAdmin(); // Only admin can access this page

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die(" Invalid or missing employee ID.");
}

$employeeId = $_GET['id'];
$taskModel = new Task($conn);
$tasks = $taskModel->getTasksByEmployee($employeeId);
$employeeName = getUserNameById($conn, $employeeId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📋 Tasks for <?= htmlspecialchars($employeeName) ?></title>
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .status-badge {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center fs-4">
            📋 Task List for Employee: <?= htmlspecialchars($employeeName) ?>
        </div>
        <div class="card-body">
            <?php if (empty($tasks)): ?>
                <div class="alert alert-info text-center">No tasks assigned to this employee.</div>
            <?php else: ?>
                <table class="table table-striped table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $index => $task): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td><?= htmlspecialchars($task['due_date']) ?></td>
                                <td>
                                    <?php
                                    $status = $task['status'];
                                    $badgeClass = match($status) {
                                        'completed' => 'success',
                                        'assigned' => 'warning',
                                        'pending_review' => 'info',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> status-badge"><?= ucfirst($status) ?></span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <a href="view-employees.php" class="btn btn-secondary mt-3">🔙 Back to Employee List</a>
        </div>
    </div>
</div>
</body>
</html>
