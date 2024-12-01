<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$query_transaksi = "SELECT tanggal, COUNT(*) AS total_transaksi, SUM(total_harga) AS total_pendapatan 
                    FROM transaksi GROUP BY tanggal";
$result_transaksi = mysqli_query($conn, $query_transaksi);

$query_stok = "SELECT nama_obat, stok FROM obat";
$result_stok = mysqli_query($conn, $query_stok);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Laporan</title>
</head>

<body>
    <h1>Laporan</h1>
    <h2>Transaksi Harian</h2>
    <table border="1">
        <tr>
            <th>Tanggal</th>
            <th>Total Transaksi</th>
            <th>Total Pendapatan</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
            <tr>
                <td><?= $row['tanggal']; ?></td>
                <td><?= $row['total_transaksi']; ?></td>
                <td>Rp <?= number_format($row['total_pendapatan'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <h2>Stok Obat</h2>
    <table border="1">
        <tr>
            <th>Nama Obat</th>
            <th>Stok</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result_stok)): ?>
            <tr>
                <td><?= $row['nama_obat']; ?></td>
                <td><?= $row['stok']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>