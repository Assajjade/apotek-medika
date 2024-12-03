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

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-center mb-6">Dashboard</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <p class="text-lg font-semibold">Total Transaksi Hari Ini:</p>
        <p class="text-2xl text-green-600"><b><?= number_format($data['total_transaksi'] ?? 0); ?></b></p>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <p class="text-lg font-semibold">Total Pendapatan Hari Ini:</p>
        <p class="text-2xl text-blue-600"><b>Rp <?= number_format($data['total_pendapatan'] ?? 0, 2); ?></b></p>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
