<?php
// Start the session and check if user is logged in
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: pages/login.php');
    exit;
}
?>

<?php include 'templates/header.php'; ?>

<!-- Main content of the page goes here -->
<h2>Welcome to POS Apotek</h2>
<p>This is the main page of the application.</p>

<?php include 'templates/footer.php'; ?>