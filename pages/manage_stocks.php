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
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisd", $nama_obat, $stok, $tanggal_kedaluwarsa, $harga);
    $stmt->execute();
    $success = "Obat berhasil ditambahkan!";
}

$query = "SELECT * FROM obat";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok</title>
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js"></script>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-8">

        <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Kelola Stok</h1>

        <?php if (isset($success)): ?>
            <p class="text-green-500 text-center mb-4"><?= $success; ?></p>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat:</label>
            <input type="text" id="nama_obat" name="nama_obat" required 
                class="mt-2 px-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <label for="stok" class="block text-sm font-medium text-gray-700 mt-4">Stok:</label>
            <input type="number" id="stok" name="stok" required 
                class="mt-2 px-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <label for="tanggal_kedaluwarsa" class="block text-sm font-medium text-gray-700 mt-4">Tanggal Kedaluwarsa:</label>
            <input type="date" id="tanggal_kedaluwarsa" name="tanggal_kedaluwarsa" required 
                class="mt-2 px-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <label for="harga" class="block text-sm font-medium text-gray-700 mt-4">Harga:</label>
            <input type="number" step="0.01" id="harga" name="harga" required 
                class="mt-2 px-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tambah</button>
        </form>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Obat</h2>

        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama Obat</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Stok</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Tanggal Kedaluwarsa</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="border-t border-gray-200">
                            <td class="px-4 py-2 text-sm text-gray-700"><?= htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="px-4 py-2 text-sm text-gray-700"><?= $row['stok']; ?></td>
                            <td class="px-4 py-2 text-sm text-gray-700"><?= $row['tanggal_kedaluwarsa']; ?></td>
                            <td class="px-4 py-2 text-sm text-gray-700">Rp <?= number_format($row['harga'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
