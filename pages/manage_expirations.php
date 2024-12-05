<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$date_today = date('Y-m-d');
$query = "SELECT * FROM obat WHERE tanggal_kedaluwarsa <= DATE_ADD(?, INTERVAL 1 MONTH)";
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