<?php
require 'header.php';

// Redirect to admin page if already logged in
if (isset($_SESSION['logged_in'])) {
    header('Location: ' . $baseUrl . 'admin.php');
    exit;
}

// Prevent concurrent sessions
$sessionFile = 'session_id.txt';
$storedSessionId = file_exists($sessionFile) ? trim(file_get_contents($sessionFile)) : '';

// If the stored session ID is not empty and doesn't match the current session, log out the old session
if (!empty($storedSessionId) && $storedSessionId !== session_id()) {
    session_destroy();
    header('Location: ' . $baseUrl . 'login.php');
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if ($password === 'admin123') {  // Simple hardcoded password
        $_SESSION['logged_in'] = true;
        file_put_contents($sessionFile, session_id());
        header('Location: ' . $baseUrl . 'admin.php');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid password';
    }
}
?>
    <div class="card shadow" style="max-width: 400px; margin: auto;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="card-title mb-0">Admin Login</h1>
            <a href="<?= $baseUrl ?>index.php" class="btn btn-light btn-sm">View Site</a>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
<?php require 'footer.php'; ?>