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

    // Cek jika obat dengan tanggal kedaluwarsa sama sudah ada
    $query = "SELECT id, stok FROM obat WHERE nama_obat = ? AND tanggal_kedaluwarsa = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nama_obat, $tanggal_kedaluwarsa);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Jika sudah ada, update stok
        $stmt->bind_result($id, $existing_stok);
        $stmt->fetch();
        $new_stok = $existing_stok + $stok;
        $update_query = "UPDATE obat SET stok = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $new_stok, $id);
        $update_stmt->execute();
    } else {
        // Jika belum ada, tambahkan sebagai entry baru
        $query = "INSERT INTO obat (nama_obat, stok, tanggal_kedaluwarsa, harga) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisd", $nama_obat, $stok, $tanggal_kedaluwarsa, $harga);
        $stmt->execute();
    }

    $success = "Obat berhasil ditambahkan atau diperbarui!";
}

// Ambil semua obat untuk ditampilkan
$query = "SELECT * FROM obat ORDER BY nama_obat, tanggal_kedaluwarsa";
$result = mysqli_query($conn, $query);
?>
<?php include '../templates/header.php'; ?>

<h1 class="text-2xl font-bold mb-4">Kelola Stok</h1>

<?php if (isset($success)): ?>
    <p class="bg-green-100 text-green-800 p-2 rounded"><?= $success; ?></p>
<?php endif; ?>

<form method="POST" class="space-y-4">
    <div class="space-y-2">
        <label class="block text-gray-700">Nama Obat:</label>
        <input type="text" name="nama_obat" required class="p-2 border rounded w-full">
    </div>
    <div class="space-y-2">
        <label class="block text-gray-700">Stok:</label>
        <input type="number" name="stok" required class="p-2 border rounded w-full">
    </div>
    <div class="space-y-2">
        <label class="block text-gray-700">Tanggal Kedaluwarsa:</label>
        <input type="date" name="tanggal_kedaluwarsa" required class="p-2 border rounded w-full">
    </div>
    <div class="space-y-2">
        <label class="block text-gray-700">Harga:</label>
        <input type="number" name="harga" required class="p-2 border rounded w-full">
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
</form>

<?php include '../templates/footer.php'; ?>