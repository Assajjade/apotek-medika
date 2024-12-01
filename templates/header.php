<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>POS Apotek Media Medika</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>

<body>
    <div class="header">
        <h1>POS Apotek Media Medika</h1>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']); ?></p>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_stocks.php">Kelola Stok</a>
            <a href="manage_transactions.php">Kelola Transaksi</a>
            <a href="manage_expirations.php">Kelola Kedaluwarsa</a>
            <a href="reports.php">Laporan</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
    <div class="content">