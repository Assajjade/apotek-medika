<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user']['id'];
    $tanggal = date('Y-m-d');
    $total_harga = $_POST['total_harga'];
    $detail_transaksi = $_POST['detail_transaksi'];

    $query = "INSERT INTO transaksi (id_user, tanggal, total_harga, detail_transaksi) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isds", $id_user, $tanggal, $total_harga, $detail_transaksi);
    $stmt->execute();
    $success = "Transaksi berhasil dicatat!";
}

$query = "SELECT * FROM transaksi ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Transaksi</title>
</head>

<body>
    <h1>Kelola Transaksi</h1>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        <label>Total Harga:</label>
        <input type="number" step="0.01" name="total_harga" required>
        <label>Detail Transaksi:</label>
        <textarea name="detail_transaksi" required></textarea>
        <button type="submit">Simpan</button>
    </form>
    <h2>Riwayat Transaksi</h2>
    <table border="1">
        <tr>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Detail Transaksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['tanggal']; ?></td>
                <td>Rp <?= number_format($row['total_harga'], 2); ?></td>
                <td><?= htmlspecialchars($row['detail_transaksi']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>