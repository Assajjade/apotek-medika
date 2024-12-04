<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

// Laporan transaksi
$query_transaksi = "SELECT tanggal, COUNT(*) AS total_transaksi, SUM(total_harga) AS total_pendapatan FROM transaksi GROUP BY tanggal";
$result_transaksi = mysqli_query($conn, $query_transaksi);

// Laporan stok dengan tanggal kedaluwarsa
$query_stok = "SELECT nama_obat, stok, tanggal_kedaluwarsa FROM obat ORDER BY nama_obat, tanggal_kedaluwarsa";
$result_stok = mysqli_query($conn, $query_stok);
?>

<?php include '../templates/header.php'; ?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Laporan</h1>

    <!-- Laporan Transaksi -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Transaksi Harian</h2>
        <?php if (mysqli_num_rows($result_transaksi) == 0): ?>
            <p class="text-gray-600">Tidak ada transaksi pada periode ini.</p>
        <?php else: ?>
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-sm">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="py-2 px-4 text-left">Tanggal</th>
                        <th class="py-2 px-4 text-left">Total Transaksi</th>
                        <th class="py-2 px-4 text-left">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= htmlspecialchars($row['tanggal']); ?></td>
                            <td class="py-2 px-4"><?= $row['total_transaksi']; ?></td>
                            <td class="py-2 px-4">Rp <?= number_format($row['total_pendapatan'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Laporan Stok Obat -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Stok Obat</h2>
        <?php if (mysqli_num_rows($result_stok) == 0): ?>
            <p class="text-gray-600">Stok obat tidak tersedia.</p>
        <?php else: ?>
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-sm">
                <thead>
                    <tr class="bg-green-600 text-white">
                        <th class="py-2 px-4 text-left">Nama Obat</th>
                        <th class="py-2 px-4 text-left">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_stok)): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="py-2 px-4"><?= $row['stok']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<?php include '../templates/footer.php'; ?>