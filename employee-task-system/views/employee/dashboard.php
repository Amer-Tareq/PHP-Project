<?php
require_once '../../config/db.php';
require_once '../../models/task.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';

$auth = checkEmployee($conn);
$employeeId = $auth['employeeId'];
$employeeName = $auth['employeeName'];

$taskModel = new Task($conn);
$totalTasks = $taskModel->getTotalTasks($employeeId);
$completedTasks = $taskModel->getCompletedTasks($employeeId);
$pendingTasks = $totalTasks - $completedTasks;
$recentTasks = $taskModel->getEmployeeRecentTasks($employeeId, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
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
        .stat-card.total {
            border-left-color: #4e73df;
        }
        .stat-card.completed {
            border-left-color: #1cc88a;
        }
        .stat-card.pending {
            border-left-color: #f6c23e;
        }
        .progress {
            height: 10px;
        }
        .sidebar {
            background: linear-gradient(180deg, #36b9cc 10%, #258391 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .completion-chart {
            height: 200px;
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
                    <h4><i class="bi bi-person-workspace"></i> Employee Panel</h4>
                    <p class="mb-0"><strong>Welcome:</strong> <?= htmlspecialchars($employeeName) ?></p>
                </div>

                <hr class="bg-light">

                <div class="mb-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="my-tasks.php" class="nav-link"><i class="bi bi-list-task me-2"></i>My Tasks</a>
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
            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Task Overview</h2>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card stat-card total h-100">
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
                    <div class="card stat-card completed h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-success">Completed</h5>
                                    <h2 class="mb-0"><?= $completedTasks ?></h2>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
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
                                    <h5 class="card-title text-warning">Pending</h5>
                                    <h2 class="mb-0"><?= $pendingTasks ?></h2>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-hourglass text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completion Progress -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Task Completion</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($totalTasks > 0): ?>
                                <?php
                                $completionRate = round(($completedTasks / $totalTasks) * 100);
                                $progressClass = $completionRate > 75 ? 'success' : ($completionRate > 50 ? 'info' : 'warning');
                                ?>
                                <h4><?= $completionRate ?>% Complete</h4>
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-<?= $progressClass ?> progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: <?= $completionRate ?>%" 
                                         aria-valuenow="<?= $completionRate ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <span class="badge bg-primary">Total: <?= $totalTasks ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="badge bg-success">Completed: <?= $completedTasks ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No tasks assigned yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-award me-2"></i>Performance</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($totalTasks > 0): ?>
                                <?php
                                $performance = '';
                                $performanceClass = '';
                                if ($completionRate >= 80) {
                                    $performance = 'Excellent';
                                    $performanceClass = 'success';
                                } elseif ($completionRate >= 50) {
                                    $performance = 'Good';
                                    $performanceClass = 'info';
                                } else {
                                    $performance = 'Needs Improvement';
                                    $performanceClass = 'warning';
                                }
                                ?>
                                <div class="display-4 mb-3 text-<?= $performanceClass ?>">
                                    <i class="bi bi-<?= $completionRate >= 80 ? 'star-fill' : ($completionRate >= 50 ? 'star-half' : 'exclamation-triangle-fill') ?>"></i>
                                </div>
                                <h3 class="text-<?= $performanceClass ?>"><?= $performance ?></h3>
                                <p class="mb-0">Keep up the good work!</p>
                            <?php else: ?>
                                <div class="alert alert-info">No tasks to measure performance.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Tasks</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentTasks)): ?>
                                <div class="alert alert-info">No recent tasks found.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentTasks as $task): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                                    <td><?= date('M j, Y', strtotime($task['due_date'])) ?></td>
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
                                                    <td>
                                                        <?php if ($task['status'] === 'completed'): ?>
                                                            <span class="text-success">100%</span>
                                                        <?php else: ?>
                                                            <div class="progress">
                                                                <div class="progress-bar" 
                                                                    role="progressbar" 
                                                                    style="width: <?= $task['progress'] ?? 25 ?>%" 
                                                                    aria-valuenow="<?= $task['progress'] ?? 25 ?>" 
                                                                    aria-valuemin="0" 
                                                                    aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <a href="my-tasks.php" class="btn btn-primary">View All Tasks</a>
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