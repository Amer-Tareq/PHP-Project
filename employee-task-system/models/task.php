<?php
require_once __DIR__ . '/../config/db.php';

// Change task status (used by employee to send for review)
function updateTaskStatus($conn, $taskId, $newStatus) {
    $sql = "UPDATE tasks SET status = :status WHERE id = :id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':status' => $newStatus,
        ':id' => $taskId
    ]);
}
class task {
    private $conn;
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Get all tasks pending admin review
    public function getPendingTasks() {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE status = 'pending_review'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Accept a task by marking it completed
    public function acceptTask($taskId) {
        $stmt = $this->conn->prepare("
            UPDATE tasks 
            SET status = 'completed' 
            WHERE id = :taskId
        ");
        $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Reject a task and log comment
    public function rejectTask($taskId, $comment, $adminId) {
        // Update task table
        $stmt = $this->conn->prepare("
            UPDATE tasks 
            SET status = 'rejected',
                refusal_comment = :comment 
            WHERE id = :taskId
        ");
        $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->execute();

        // Insert into task_updates
        $stmt2 = $this->conn->prepare("
            INSERT INTO task_updates (task_id, update_text, updated_by) 
            VALUES (:taskId, :comment, :adminId)
        ");
        $stmt2->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $stmt2->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt2->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        return $stmt2->execute();
    }

    // Get updates/comments on a task
    public function getTaskUpdates($taskId) {
        $stmt = $this->conn->prepare("
            SELECT task_updates.*, users.name AS admin_name 
            FROM task_updates 
            JOIN users ON task_updates.updated_by = users.id 
            WHERE task_updates.task_id = :taskId
            ORDER BY task_updates.updated_at DESC
        ");
        $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Get task by ID
    public function getTaskById($taskId)
{
    $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $taskId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    // Get all tasks for an employee
    public function getTasksByEmployee($employeeId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM tasks 
            WHERE assigned_to = :id 
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':id', $employeeId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update task status (e.g., to pending_review)
    public function updateTaskStatus($taskId, $newStatus) {
        $sql = "UPDATE tasks SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $newStatus,
            ':id' => $taskId
        ]);
    }
    //add a new task
    public function addTask($title, $description, $assigned_to, $created_by, $due_date) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (title, description, assigned_to, created_by, due_date) 
                                    VALUES (:title, :description, :assigned_to, :created_by, :due_date)");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':assigned_to' => $assigned_to,
            ':created_by' => $created_by,
            ':due_date' => $due_date
        ]);
    }

// Get total number of tasks assigned to an employee
    public function getTotalTasks($employeeId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ?");
        $stmt->execute([$employeeId]);
        return (int) $stmt->fetchColumn();
    }

    // Get number of completed tasks assigned to an employee
    public function getCompletedTasks($employeeId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status = 'completed'");
        $stmt->execute([$employeeId]);
        return (int) $stmt->fetchColumn();
    }
     // Get total count of all tasks
    public function getTotalTasksCount() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM tasks");
        return (int) $stmt->fetchColumn();
    }
     // Get count of tasks pending review
    public function getPendingReviewCount() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM tasks WHERE status = 'pending_review'");
        return (int) $stmt->fetchColumn();
    }
    
public function getRecentTasks($limit = 5) {
    $stmt = $this->conn->prepare("
        SELECT t.*, u.name as employee_name 
        FROM tasks t
        JOIN users u ON t.assigned_to = u.id
        ORDER BY t.created_at DESC 
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getEmployeeRecentTasks($employeeId, $limit = 5) {
    $stmt = $this->conn->prepare("
        SELECT * FROM tasks 
        WHERE assigned_to = :employeeId 
        ORDER BY created_at DESC 
        LIMIT :limit
    ");
    $stmt->bindValue(':employeeId', $employeeId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}