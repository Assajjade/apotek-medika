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
    $total_harga = 0;
    $detail_transaksi = "";
    $obat_ids = $_POST['obat_id'];
    $kuantitas = $_POST['kuantitas'];

    $conn->begin_transaction();

    try {
        foreach ($obat_ids as $index => $obat_id) {
            $kuantitas_obat = $kuantitas[$index];

            // Ambil stok dengan tanggal kedaluwarsa terdekat
            $query = "SELECT id, stok, harga, nama_obat FROM obat WHERE id = ? ORDER BY tanggal_kedaluwarsa ASC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $obat_id);
            $stmt->execute();
            $stmt->bind_result($id, $stok, $harga, $nama_obat);

            while ($stmt->fetch() && $kuantitas_obat > 0) {
                if ($stok > 0) {
                    $used_stok = min($stok, $kuantitas_obat);
                    $kuantitas_obat -= $used_stok;
                    $new_stok = $stok - $used_stok;

                    $update_query = "UPDATE obat SET stok = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("ii", $new_stok, $id);
                    $update_stmt->execute();

                    $total_harga += $used_stok * $harga;
                    $detail_transaksi .= "$nama_obat ($used_stok) - Rp " . number_format($harga, 2) . "<br>";
                }
            }
        }

        $query = "INSERT INTO transaksi (id_user, tanggal, total_harga, detail_transaksi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isds", $id_user, $tanggal, $total_harga, $detail_transaksi);
        $stmt->execute();

        $conn->commit();
        $success = "Transaksi berhasil dicatat!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<?php include '../templates/header.php'; ?>

<h1 class="text-2xl font-bold mb-4">Kelola Transaksi</h1>

<?php if (isset($success)): ?>
    <p class="bg-green-100 text-green-800 p-2 rounded"><?= $success; ?></p>
<?php elseif (isset($error)): ?>
    <p class="bg-red-100 text-red-800 p-2 rounded"><?= $error; ?></p>
<?php endif; ?>

<form method="POST" class="space-y-4">
    <div id="obat-container">
        <div class="flex items-center space-x-4">
            <label for="obat_id" class="block text-gray-700">Obat:</label>
            <select name="obat_id[]" required class="p-2 border rounded w-full">
                <?php
                $query = "SELECT * FROM obat";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                }
                ?>
            </select>
            <label for="kuantitas" class="block text-gray-700">Kuantitas:</label>
            <input type="number" name="kuantitas[]" required min="1" class="p-2 border rounded w-full">
        </div>
    </div>
    <button type="button" id="add-obat" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Obat</button>
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
</form>

<?php include '../templates/footer.php'; ?>