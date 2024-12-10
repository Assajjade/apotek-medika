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

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok</title>
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js"></script>
</head>

<div class="container mx-auto py-10 px-6">

    <div class="flex items-center gap-8">
        <!-- Card 1 -->
        <div class="w-full p-4">
            <div class="bg-white shadow-lg rounded-lg p-6 flex items-center space-x-4 border-l-4 border-green-500 ">
                <div class="text-green-600 text-4xl">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="px-4">
                    <p class="text-lg font-medium text-gray-700">Total Transaksi Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800 px-4"><?= number_format($data['total_transaksi'] ?? 0); ?></p>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white shadow-lg rounded-lg p-6 flex items-center space-x-4 border-l-4 border-blue-500 w-full">
            <div class="text-blue-600 text-4xl">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="px-4">
                <p class="text-lg font-medium text-gray-700">Total Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800">Rp <?= number_format($data['total_pendapatan'] ?? 0, 2); ?></p>
            </div>
        </div>
    </div>
</div>



<?php include '../templates/footer.php'; ?>
