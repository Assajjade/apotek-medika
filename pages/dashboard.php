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
    <link href="../dist/output.css" rel="stylesheet">
</head>

<div class="container mx-auto p-4">
    <!-- Heading -->
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Dashboard</h1>
    
    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Transaksi -->
        <div class="bg-white shadow-lg rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-xl font-medium text-gray-700">Total Transaksi Hari Ini</p>
                <p class="text-3xl font-bold text-green-500 mt-2"><?= number_format($data['total_transaksi'] ?? 0); ?></p>
            </div>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 00-2-2H5m6 8v-2a2 2 0 012-2h6m-6 4v-2a2 2 0 00-2-2H9m-6-4h4m0 0v2m0-2h2m4 4h2m0 0v2m0-2h2" />
                </svg>
            </div>
        </div>
        
        <!-- Total Pendapatan -->
        <div class="bg-white shadow-lg rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-xl font-medium text-gray-700">Total Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-blue-500 mt-2">Rp <?= number_format($data['total_pendapatan'] ?? 0, 2); ?></p>
            </div>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.656 0 3-1.343 3-3s-1.344-3-3-3-3 1.343-3 3 1.344 3 3 3zm0 2c-2.205 0-6 1.115-6 3v2h12v-2c0-1.885-3.795-3-6-3zm9-3a1 1 0 011 1v7a3 3 0 01-3 3h-4a1 1 0 010-2h4a1 1 0 001-1V8a1 1 0 011-1z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>