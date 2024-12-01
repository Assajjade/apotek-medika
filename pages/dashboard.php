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

<h1>Dashboard</h1>
<div>
    <p>Total Transaksi Hari Ini: <b><?= number_format($data['total_transaksi'] ?? 0); ?></b></p>
    <p>Total Pendapatan Hari Ini: <b>Rp <?= number_format($data['total_pendapatan'] ?? 0, 2); ?></b></p>
</div>

<?php include '../templates/footer.php'; ?>