<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_obat = $_POST['nama_obat'];
    $stok = $_POST['stok'];
    $tanggal_kedaluwarsa = $_POST['tanggal_kedaluwarsa'];
    $harga = $_POST['harga'];

    $query = "INSERT INTO obat (nama_obat, stok, tanggal_kedaluwarsa, harga) 
              VALUES ('$nama_obat', $stok, '$tanggal_kedaluwarsa', $harga)";
    mysqli_query($conn, $query);
    $success = "Obat berhasil ditambahkan!";
}

$query = "SELECT * FROM obat";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Stok</title>
</head>

<body>
    <h1>Kelola Stok</h1>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        <label>Nama Obat:</label>
        <input type="text" name="nama_obat" required>
        <label>Stok:</label>
        <input type="number" name="stok" required>
        <label>Tanggal Kedaluwarsa:</label>
        <input type="date" name="tanggal_kedaluwarsa" required>
        <label>Harga:</label>
        <input type="number" step="0.01" name="harga" required>
        <button type="submit">Tambah</button>
    </form>
    <h2>Daftar Obat</h2>
    <table border="1">
        <tr>
            <th>Nama Obat</th>
            <th>Stok</th>
            <th>Tanggal Kedaluwarsa</th>
            <th>Harga</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['nama_obat']; ?></td>
                <td><?= $row['stok']; ?></td>
                <td><?= $row['tanggal_kedaluwarsa']; ?></td>
                <td>Rp <?= number_format($row['harga'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>