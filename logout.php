<?php
require 'header.php';

// Clear session ID file
$sessionFile = 'session_id.txt';
if (file_exists($sessionFile)) {
    unlink($sessionFile);
}

session_destroy();
header('Location: ' . $baseUrl . 'login.php');
exit;
?>