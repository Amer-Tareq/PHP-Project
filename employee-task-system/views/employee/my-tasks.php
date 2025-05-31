<?php
require_once '../../config/db.php';
require_once '../../models/task.php';
require_once '../../includes/auth.php';

$auth = checkEmployee($conn);

$employeeId = $auth['employeeId'];
$employeeName = $auth['employeeName'];

$taskmodel = new task($conn);
$employeeId = $_SESSION['user_id'];
$tasks = $taskmodel->getTasksByEmployee($employeeId);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Tasks Dashboard</title>
    <link rel="stylesheet" href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.css">    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .task-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .task-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 20px;
        }
        
        .task-body {
            padding: 20px;
            background-color: white;
        }
        
        .task-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending { background-color: #e9ecef; color: #495057; }
        .status-in_progress { background-color: #d0ebff; color: #1971c2; }
        .status-completed { background-color: #d3f9d8; color: #2b8a3e; }
        .status-pending_review { background-color: #fff3bf; color: #e67700; }
        .status-rejected { background-color: #ffe3e3; color: #c92a2a; }
        
        .task-due {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 10px 0;
        }
        
        .action-btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 15px;
        }
        
        .feedback-item {
            border-left: 3px solid var(--primary-color);
            padding: 10px 15px;
            margin: 10px 0;
            background-color: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .empty-state i {
            font-size: 60px;
            color: #adb5bd;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="h3 mb-0" style="color: var(--primary-color);">
                <i class="fas fa-tasks me-2"></i> My Tasks Dashboard
            </h1>
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>

        <?php if (count($tasks) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3 class="h4">No Tasks Assigned</h3>
                <p class="text-muted">You don't have any tasks assigned yet. Check back later!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($tasks as $task): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="task-card">
                            <div class="task-header">
                                <h5 class="mb-0"><?= htmlspecialchars($task['title']) ?></h5>
                            </div>
                            <div class="task-body">
                                <p class="text-muted"><?= htmlspecialchars($task['description']) ?></p>
                                
                                <div class="task-due">
                                    <small class="text-muted">Due Date</small>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        <strong><?= htmlspecialchars($task['due_date']) ?></strong>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <small class="text-muted">Status</small>
                                        <div>
                                            <span class="task-status status-<?= $task['status'] ?>">
                                                <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <?php if (!in_array($task['status'], ['pending_review', 'completed', 'refused','pending'])): ?>
                                        <form method="POST" class="ajax-task-form">
                                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <div class="input-group">
                                                <select name="new_status" class="form-select form-select-sm">
                                                    <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary action-btn">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="feedback-section">
                                    <h6 class="h6 mb-3">
                                        <i class="fas fa-comments me-2"></i> Admin Feedback
                                    </h6>
                                    
                                    <?php
                                    $updates = $taskmodel->getTaskUpdates($task['id']);
                                    if (!empty($updates)) {
                                        foreach ($updates as $update) {
                                            echo "<div class='feedback-item'>
                                                <div class='d-flex justify-content-between'>
                                                    <strong class='text-primary'>{$update['admin_name']}</strong>
                                                    <small class='text-muted'>{$update['updated_at']}</small>
                                                </div>
                                                <p class='mb-0 mt-1'>{$update['update_text']}</p>
                                            </div>";
                                        }
                                    } else {
                                        echo "<div class='text-center py-2 text-muted'>
                                            <i class='far fa-comment-dots me-2'></i> No feedback yet
                                        </div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="../../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.ajax-task-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch('/employee-task-system/controllers/taskController.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.innerHTML = `
                        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header bg-success text-white">
                                    <strong class="me-auto">Success</strong>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Task status updated successfully!
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                        location.reload();
                    }, 2000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to update task'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the task');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    });
    </script>
</body>
</html>