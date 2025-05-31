<?php
require_once '../../config/db.php';
require_once '../../models/task.php';
require_once '../../models/user.php';
require_once '../../includes/auth.php';

checkAdmin();

$taskmodel = new task($conn);
$pendingTasks = $taskmodel->getPendingTasks($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pending Task Review</title>
    <link rel="stylesheet" href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .card-task {
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .task-actions form {
        display: inline-block;
        margin-right: 5px;
    }

    .reject-form {
        margin-top: 10px;
    }
    </style>
    <meta>
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4 text-primary">📝 Pending Task Review</h2>

        <!-- Go Back -->
        <a href="dashboard.php" class="btn btn-outline-secondary mb-4">🔙 Go Back</a>

        <!-- Alerts -->
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            ✅ Task <?= htmlspecialchars($_GET['success']) ?> successfully.
        </div>
        <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            ❌ Failed to process the request (<?= htmlspecialchars($_GET['error']) ?>).
        </div>
        <?php endif; ?>

        <!-- Tasks -->
        <?php if (count($pendingTasks) === 0): ?>
        <div class="alert alert-info">📭 No pending tasks found.</div>
        <?php else: ?>
        <?php foreach ($pendingTasks as $task): ?>
        <div class="card card-task">
            <div class="card-body">
                <h5 class="card-title text-dark"><?= htmlspecialchars($task['title']) ?></h5>
                <h6 class="card-subtitle mb-2 text-muted">Due: <?= htmlspecialchars($task['due_date']) ?></h6>
                <p class="card-text"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                <p class="mb-2"><strong>Assigned To:</strong>
                    <?= htmlspecialchars(getUserNameById($conn, $task['assigned_to'])) ?></p>

                <div class="task-actions">
                    <!-- Accept -->
                    <form method="POST" action="../../controllers/taskController.php">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn btn-success btn-sm">✅ Accept</button>
                    </form>

                    <!-- Reject -->
                    <form method="POST" action="../../controllers/taskController.php" class="reject-form">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <div class="input-group input-group-sm">
                            <input type="text" name="comment" class="form-control" placeholder="Rejection reason..."
                                required>
                            <button type="submit" class="btn btn-danger">❌ Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>

</html>