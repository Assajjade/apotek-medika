<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$date_today = date('Y-m-d');

// Proses pengurangan stok untuk obat yang sudah kadaluwarsa
$reduce_stok_query = "UPDATE obat SET stok = 0 WHERE tanggal_kedaluwarsa <= ?";
$reduce_stmt = $conn->prepare($reduce_stok_query);
$reduce_stmt->bind_param("s", $date_today);
$reduce_stmt->execute();

// Tampilkan obat yang mendekati kedaluwarsa (dalam waktu 1 bulan)
$query = "SELECT * FROM obat WHERE tanggal_kedaluwarsa <= DATE_ADD(?, INTERVAL 1 MONTH) AND stok > 0 ORDER BY tanggal_kedaluwarsa";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date_today);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include '../templates/header.php'; ?>

<h1 class="text-2xl font-bold mb-4">Kelola Kedaluwarsa</h1>

<?php if (mysqli_num_rows($result) == 0): ?>
    <p class="text-gray-600">Tidak ada obat yang mendekati kedaluwarsa.</p>
<?php else: ?>
    <table class="w-full border-collapse border border-gray-300 bg-white shadow-lg">
        <thead class="bg-gray-200">
            <tr>
                <th class="border border-gray-300 px-4 py-2">Nama Obat</th>
                <th class="border border-gray-300 px-4 py-2">Stok</th>
                <th class="border border-gray-300 px-4 py-2">Tanggal Kedaluwarsa</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-100">
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['nama_obat']); ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= $row['stok']; ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= $row['tanggal_kedaluwarsa']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../templates/footer.php'; ?>