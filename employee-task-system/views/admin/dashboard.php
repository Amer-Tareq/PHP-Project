<?php
require_once '../../config/db.php';
require_once '../../models/task.php';
require_once '../../models/user.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';

checkAdmin();

$taskModel = new Task($conn);
$userModel = new User($conn);

// Get stats
$reviewCount = $taskModel->getPendingReviewCount();
$totalTasks = $taskModel->getTotalTasksCount();
$employeeCount = $userModel->getEmployeeCount();

// Get recent tasks
$recentTasks = $taskModel->getRecentTasks(5);
// Get active employees
$activeEmployees = $userModel->getMostActiveEmployees(5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .stat-card {
            transition: transform 0.3s;
            border-left: 4px solid;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.tasks {
            border-left-color: #4e73df;
        }
        .stat-card.pending {
            border-left-color: #f6c23e;
        }
        .stat-card.employees {
            border-left-color: #1cc88a;
        }
        .recent-table {
            font-size: 0.9rem;
        }
        .sidebar {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar text-white vh-100 p-4">
            <div class="d-flex flex-column h-100">
                <div class="mb-4 text-center">
                    <h4><i class="bi bi-person-badge"></i> Admin Panel</h4>
                    <p class="mb-0"><strong>Welcome:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
                </div>

                <hr class="bg-light">

                <div class="mb-3">
                    <h6 class="sidebar-heading">CORE</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="add-tasks.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i>Add Task</a>
                        </li>
                        <li class="nav-item">
                            <a href="view-employees.php" class="nav-link"><i class="bi bi-people me-2"></i>Employees</a>
                        </li>
                        <li class="nav-item">
                            <a href="review-tasks.php" class="nav-link"><i class="bi bi-clipboard-check me-2"></i>Review Tasks</a>
                        </li>
                    </ul>
                </div>

                <div class="mt-auto">
                    <hr class="bg-light">
                    <a href="../../public/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 p-4">
            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Overview</h2>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card stat-card tasks h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary">Total Tasks</h5>
                                    <h2 class="mb-0"><?= $totalTasks ?></h2>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-list-task text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card stat-card pending h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-warning">Pending Review</h5>
                                    <h2 class="mb-0"><?= $reviewCount ?></h2>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-hourglass-split text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card stat-card employees h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-success">Employees</h5>
                                    <h2 class="mb-0"><?= $employeeCount ?></h2>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-people text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Tasks</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentTasks)): ?>
                                <div class="alert alert-info">No recent tasks found.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table recent-table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Assigned To</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentTasks as $task): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                                    <td><?= htmlspecialchars($task['employee_name']) ?></td>
                                                    <td>
                                                        <?php
                                                        $badgeClass = match($task['status']) {
                                                            'completed' => 'success',
                                                            'assigned' => 'warning',
                                                            'pending_review' => 'info',
                                                            'rejected' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($task['status']) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-star me-2"></i>Top Employees</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($activeEmployees)): ?>
                                <div class="alert alert-info">No employee data available.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table recent-table">
                                        <thead>
                                            <tr>
                                                <th>Employee</th>
                                                <th>Completed Tasks</th>
                                                <th>Performance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($activeEmployees as $employee): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($employee['name']) ?></td>
                                                    <td><?= $employee['completed_tasks'] ?></td>
                                                    <td>
                                                        <?php
                                                        $progress = min(100, ($employee['completed_tasks'] / max(1, $employee['total_tasks']))) * 100;
                                                        $progressClass = $progress > 75 ? 'success' : ($progress > 50 ? 'info' : 'warning');
                                                        ?>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-<?= $progressClass ?>" 
                                                                 role="progressbar" 
                                                                 style="width: <?= $progress ?>%" 
                                                                 aria-valuenow="<?= $progress ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                <?= round($progress) ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>