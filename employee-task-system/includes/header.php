<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Dashboard' ?></title>
    <link href="/assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand mb-0 h1">🧩 Task System</span>

    <!-- زر تسجيل الخروج عبر POST -->
    <form action="/employee-task-system/public/logout.php" method="POST" class="ms-auto mb-0">
    <button type="submit" class="btn btn-link nav-link text-danger p-0"
            onclick="return confirm('Are you sure you want to logout?');">
        🔓 Logout
    </button>
</form>
</nav>
