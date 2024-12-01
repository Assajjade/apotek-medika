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
    <?php if (mysqli_num_rows($result_transaksi) == 0): ?>
        <p>Tidak ada transaksi pada periode ini.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Tanggal</th>
                <th>Total Transaksi</th>
                <th>Total Pendapatan</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                    <td><?= $row['total_transaksi']; ?></td>
                    <td>Rp <?= number_format($row['total_pendapatan'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <h2>Stok Obat</h2>
    <?php if (mysqli_num_rows($result_stok) == 0): ?>
        <p>Stok obat tidak tersedia.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Nama Obat</th>
                <th>Stok</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result_stok)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_obat']); ?></td>
                    <td><?= $row['stok']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>

</html>