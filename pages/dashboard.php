<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$date = date('Y-m-d');
$query = "SELECT COUNT(*) AS total_transaksi, SUM(total_harga) AS total_pendapatan 
          FROM transaksi WHERE tanggal = '$date'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>
    <h1>Dashboard</h1>
    <p>Total Transaksi Hari Ini: <?= $data['total_transaksi'] ?? 0; ?></p>
    <p>Total Pendapatan Hari Ini: Rp <?= number_format($data['total_pendapatan'] ?? 0, 2); ?></p>
    <a href="manage_stocks.php">Kelola Stok</a>
    <a href="manage_transactions.php">Kelola Transaksi</a>
    <a href="manage_expirations.php">Kelola Kedaluwarsa</a>
    <a href="reports.php">Laporan</a>
    <a href="logout.php">Logout</a>
</body>

</html>