<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

// Dapatkan tanggal hari ini
$date_today = date('Y-m-d');

// Query untuk obat yang mendekati kedaluwarsa dalam 1 bulan
$query = "
    SELECT nama_obat, stok, tanggal_kedaluwarsa
    FROM obat
    WHERE tanggal_kedaluwarsa BETWEEN ? AND DATE_ADD(?, INTERVAL 1 MONTH)
    ORDER BY tanggal_kedaluwarsa ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $date_today, $date_today);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php include '../templates/header.php'; ?>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4 text-blue-600">Kelola Kedaluwarsa</h1>

    <?php if ($result->num_rows === 0): ?>
        <p class="text-gray-600">Tidak ada obat yang mendekati kedaluwarsa.</p>
    <?php else: ?>
        <table class="w-full border-collapse border border-gray-300 bg-white shadow-lg rounded-lg">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Nama Obat</th>
                    <th class="border border-gray-300 px-4 py-2">Stok</th>
                    <th class="border border-gray-300 px-4 py-2">Tanggal Kedaluwarsa</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    date_default_timezone_set('Asia/Jakarta');
                    $tanggal_kedaluwarsa = date('l, j F Y', strtotime($row['tanggal_kedaluwarsa'])); ?>
                    <tr class="hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['nama_obat']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= $row['stok']; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($tanggal_kedaluwarsa); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>