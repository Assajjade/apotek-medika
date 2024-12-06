<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

// Laporan transaksi
$query_transaksi = "SELECT id, tanggal, total_harga, detail_transaksi 
                    FROM transaksi 
                    ORDER BY tanggal DESC";
$result_transaksi = mysqli_query($conn, $query_transaksi);

// Laporan stok dengan tanggal kedaluwarsa
$query_stok = "SELECT nama_obat, stok, tanggal_kedaluwarsa, created_at 
               FROM obat 
               ORDER BY nama_obat ASC, tanggal_kedaluwarsa ASC";

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
                        <th class="py-2 px-4 text-left">Total Pendapatan</th>
                        <th class="py-2 px-4 text-left">Detail Barang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= htmlspecialchars($row['tanggal']); ?></td>
                            <td class="py-2 px-4">Rp <?= number_format($row['total_harga'], 2); ?></td>
                            <td class="py-2 px-4">
                                <details>
                                    <summary class="text-blue-500 cursor-pointer">Lihat Barang</summary>
                                    <div class="mt-2 ml-4">
                                        <?= htmlspecialchars_decode($row['detail_transaksi']); ?>
                                    </div>
                                </details>
                            </td>
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
                        <th class="py-2 px-4 text-left">Tanggal Kedaluwarsa</th>
                        <th class="py-2 px-4 text-left">Tanggal Input</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_stok)): ?>

                        <?php // Atur zona waktu
                        date_default_timezone_set('Asia/Jakarta');
                        $tanggal_kedaluwarsa = date('l, j F Y', strtotime($row['tanggal_kedaluwarsa']));
                        $tanggal_input = date('l, j F Y', strtotime($row['created_at'])); ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="py-2 px-4"><?= $row['stok']; ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($tanggal_kedaluwarsa); ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($tanggal_input); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../templates/footer.php'; ?>