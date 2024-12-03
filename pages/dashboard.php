<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$date = date('Y-m-d');
$query = "SELECT COUNT(*) AS total_transaksi, SUM(total_harga) AS total_pendapatan 
          FROM transaksi WHERE tanggal = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<?php include '../templates/header.php'; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <p class="text-lg font-semibold">Total Transaksi Hari Ini:</p>
        <p class="text-3xl text-green-600 font-bold"><?= number_format($data['total_transaksi'] ?? 0); ?></p>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-6">
        <p class="text-lg font-semibold">Total Pendapatan Hari Ini:</p>
        <p class="text-3xl text-blue-600 font-bold">Rp <?= number_format($data['total_pendapatan'] ?? 0, 2); ?></p>
    </div>
</div>

<?php include '../templates/footer.php'; ?>