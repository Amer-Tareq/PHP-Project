<?php
require_once '../../config/db.php';
require_once '../../models/user.php';
require_once '../../models/task.php';
require_once '../../includes/auth.php';

checkAdmin();

$userModel = new user($conn);
$taskModel = new task($conn);

$employees = $userModel->getEmployees();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>👥 قائمة الموظفين</title>
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center fs-4">
            👥 قائمة الموظفين
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>إجمالي المهام</th>
                        <th>قيد التنفيذ</th>
                        <th>مكتملة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $index => $employee): 
                        $employeeId = $employee['id'];
                        $totalTasks = $taskModel->getTotalTasksCount($employeeId);
                        $pendingTasks = $taskModel->getPendingReviewCount($employeeId);
                        $completedTasks = $taskModel->getCompletedTasks($employeeId);
                    ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($employee['name']) ?></td>
                            <td><?= htmlspecialchars($employee['email']) ?></td>
                            <td><?= $totalTasks ?></td>
                            <td><?= $pendingTasks ?></td>
                            <td><?= $completedTasks ?></td>
                            <td>
<a href="view-employee-tasks.php?id=<?= $employeeId ?>" class="btn btn-sm btn-info">عرض المهام</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="dashboard.php" class="btn btn-secondary mb-3">🔙Go back</a>
        </div>
    </div>
</div>

<script src="../../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
