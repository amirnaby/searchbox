<?php
// Base URL
$baseUrl = '/xampp/search/';

// Start session
session_start();

// Prevent concurrent sessions
$sessionFile = 'session_id.txt';
$currentSessionId = session_id();
$storedSessionId = file_exists($sessionFile) ? trim(file_get_contents($sessionFile)) : '';

// If the stored session ID is not empty and doesn't match the current session, log out the old session
if (!empty($storedSessionId) && $storedSessionId !== $currentSessionId) {
    session_destroy();
    header('Location: ' . $baseUrl . 'login.php');
    exit;
}

// Store the current session ID
file_put_contents($sessionFile, $currentSessionId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS (Minty Theme) -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/minty/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>style.css">
</head>
<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= $baseUrl ?>index.php">Search Names</a>
            <div class="navbar-nav">
                <?php if (isset($_SESSION['logged_in'])): ?>
                    <a class="nav-link" href="<?= $baseUrl ?>admin.php">Admin</a>
                    <a class="nav-link" href="<?= $baseUrl ?>logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="<?= $baseUrl ?>login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>