<?php
try {
    $conn = new PDO('mysql:host=localhost;dbname=employee_task_db', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    
    die("Failed connection: " . $e->getMessage());
}
