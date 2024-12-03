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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kedaluwarsa</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-center">Kelola Kedaluwarsa</h1>
        <h2 class="text-xl font-semibold mb-4">Obat Mendekati Kedaluwarsa</h2>

        <?php if (mysqli_num_rows($result) == 0): ?>
            <p class="text-gray-600">Tidak ada obat yang mendekati kedaluwarsa dalam waktu 1 bulan.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300 bg-white shadow-lg">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left text-gray-700">Nama Obat</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-gray-700">Stok</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-gray-700">Tanggal Kedaluwarsa</th>
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
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
