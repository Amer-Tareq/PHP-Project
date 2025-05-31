<?php
session_start();
require_once '../config/db.php';
require_once '../models/task.php';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit;
}

$role = $_SESSION['user_role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Employee updates task status (e.g. submit for review)
    if ($action === 'update_status' && $role === 'employee') {
        $taskId = $_POST['task_id'] ?? null;
        $newStatus = $_POST['new_status'] ?? null;

        if ($taskId && $newStatus) {
            $statusToSet = $newStatus === 'completed' ? 'pending_review' : $newStatus;
            $result = updateTaskStatus($conn, $taskId, $statusToSet);

            if ($isAjax) {
                jsonResponse([
                    'success' => $result,
                    'debug' => [
                        'taskId' => $taskId,
                        'newStatus' => $statusToSet
                    ]
                ]);
            }

            header('Location: ../views/employee/my_tasks.php');
        } else {
            if ($isAjax) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Invalid task ID or status',
                    'debug' => [
                        'taskId' => $taskId,
                        'newStatus' => $newStatus
                    ]
                ]);
            }

            header('Location: ../views/employee/my_tasks.php?error=invalid_data');
        }
        exit;
    }
    $taskmodel = new task($conn);
    // Admin accepts task
    if ($action === 'accept' && $role === 'admin') {
        $taskId = $_POST['task_id'] ?? null;
        if ($taskId && $taskmodel->acceptTask($taskId)) {
            header("Location: ../views/admin/review-tasks.php?success=accepted");
        } else {
            header("Location: ../views/admin/review-tasks.php?error=accept_failed");
        }
        exit;
    }
    
    // Admin rejects task with comment
    if ($action === 'reject' && $role === 'admin') {
        $taskId = $_POST['task_id'] ?? null;
        $comment = $_POST['comment'] ?? '';
        $adminId = $_SESSION['user_id'];
        if ($taskId && $taskmodel->rejectTask($taskId, $comment, $adminId)) {
            header("Location: ../views/admin/review-tasks.php?success=rejected");
        } else {
            header("Location: ../views/admin/review-tasks.php?error=reject_failed");
        }
        exit;
    }

    // Unknown or invalid action
    header("Location: ../public/login.php");
    exit;
}
    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $name = trim($_POST['name']);
    // $email = trim($_POST['email']);
    // $password = $_POST['password'];
    // $rpassword = $_POST['rpassword'];
    // $phone = trim($_POST['phone']);
    // $gender = $_POST['gender'];
        