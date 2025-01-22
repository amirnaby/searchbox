<?php
session_start();

// Create session_id.txt if it doesn't exist
if (!file_exists('session_id.txt')) {
    file_put_contents('session_id.txt', '');
}

// Redirect to admin page if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password === 'admin123') {
        // Change this to your desired password
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['session_id'] = uniqid();
        file_put_contents('session_id.txt', $_SESSION['session_id']);
        header('Location: admin.php');
        exit();
    } else {
        $error = "Invalid password!";
    }
}
?>

<?php include 'header.php'; ?>
<div class="container mt-5">
	<div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
			<h1 class="card-title mb-0">Login</h1>
		</div>
		<div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php include 'footer.php'; ?>