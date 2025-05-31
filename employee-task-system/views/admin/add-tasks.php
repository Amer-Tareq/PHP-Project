<?php
require_once '../../config/db.php';
require_once '../../models/task.php';
require_once '../../models/user.php';
require_once '../../includes/auth.php';
checkAdmin();


$taskModel = new Task($conn);
$userModel = new User($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $assigned_to = $_POST['assigned_to'];
    $created_by = $_SESSION['user_id'];

    if ($title && $assigned_to) {
        if ($taskModel->addTask($title, $description, $assigned_to, $created_by, $due_date)) {
            $success = "Task added successfully!";
        } else {
            $error = "Failed to add task.";
        }
    } else {
        $error = "Title and employee are required!";
    }
}

$employees = $userModel->getEmployees();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Task</title>
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">➕ Add New Task</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Task Description</label>
                <textarea class="form-control" name="description" id="description"></textarea>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" name="due_date" id="due_date">
            </div>

            <div class="mb-3">
                <label for="assigned_to" class="form-label">Assign To</label>
                <select class="form-select" name="assigned_to" id="assigned_to" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
            
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
