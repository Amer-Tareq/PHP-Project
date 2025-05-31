<?php
require_once __DIR__ . '/../config/db.php'; // Include the database connection file
//Fetches the user's name
function getUserNameById($conn, $id) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['name'] : 'Unknown';
}
class user{
    private $conn;
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    public function getAllUsers() {
    $stmt = $this->conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    // Get user details by ID// Get total count of employees
    public function getEmployeeCount() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users WHERE role = 'employee'");
        return (int) $stmt->fetchColumn();
    }
    public function getEmployees() {
        $stmt = $this->conn->query("SELECT id, name,email FROM users WHERE role = 'employee'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addUser($name,$email,$hashed, $phone, $gender,$role){
        if ($role == 'User'){

            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, phone, gender,role) VALUES (:name, :email, :password, :phone, :gender,:role)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindparam(':role', $role);
            $stmt->execute();
            return $stmt;
            }
            else {
                $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, phone, gender,role) VALUES (:name, :email, :password, :phone, :gender,:role)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindparam(':role', $role);
            $stmt->execute();
            return $stmt;
            }
    }
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserProfile($userId, $name, $email, $phone, $gender) {
        $stmt = $this->conn->prepare("
            UPDATE users 
            SET name = :name, email = :email, phone = :phone, gender = :gender 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':gender' => $gender,
            ':id' => $userId
        ]);
    }

    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }
    public function getMostActiveEmployees($limit = 5) {
    $stmt = $this->conn->query("
        SELECT u.id, u.name, 
            COUNT(t.id) as total_tasks,
            SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks
        FROM users u
        LEFT JOIN tasks t ON u.id = t.assigned_to
        WHERE u.role = 'employee'
        GROUP BY u.id
        ORDER BY completed_tasks DESC
        LIMIT $limit
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

?>
