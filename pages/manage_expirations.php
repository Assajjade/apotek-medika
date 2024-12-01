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
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Kedaluwarsa</title>
</head>

<body>
    <h1>Kelola Kedaluwarsa</h1>
    <h2>Obat Mendekati Kedaluwarsa</h2>
    <?php if (mysqli_num_rows($result) == 0): ?>
        <p>Tidak ada obat yang mendekati kedaluwarsa dalam waktu 1 bulan.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Nama Obat</th>
                <th>Stok</th>
                <th>Tanggal Kedaluwarsa</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_obat']); ?></td>
                    <td><?= $row['stok']; ?></td>
                    <td><?= $row['tanggal_kedaluwarsa']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>

</html>