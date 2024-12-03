<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

// Cek jika ID user ada di tabel users
$query = "SELECT id FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("ID user tidak ditemukan di database.");
}

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user']['id'];
    $tanggal = date('Y-m-d');
    $total_harga = 0;
    $detail_transaksi = "";
    $obat_ids = $_POST['obat_id'];  // Mendapatkan array obat_id
    $kuantitas = $_POST['kuantitas'];  // Mendapatkan array kuantitas

    // Mulai transaksi untuk menghindari kegagalan saat update stok
    $conn->begin_transaction();

    try {
        // Ambil data obat yang dipilih dan kuantitasnya
        foreach ($obat_ids as $index => $obat_id) {
            $kuantitas_obat = $kuantitas[$index];

            // Ambil harga obat dari database
            $query = "SELECT harga, nama_obat, stok FROM obat WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $obat_id);
            $stmt->execute();

            // Ambil hasil query secara benar
            $stmt->store_result(); // Menyimpan hasil query
            $stmt->bind_result($harga, $nama_obat, $stok);
            if ($stmt->fetch()) {  // Ambil hasil pertama dari query
                // Periksa jika stok cukup
                if ($stok < $kuantitas_obat) {
                    throw new Exception("Stok obat '$nama_obat' tidak cukup.");
                }

                // Hitung total harga berdasarkan kuantitas
                $total_harga += $harga * $kuantitas_obat;

                // Tambahkan detail transaksi
                $detail_transaksi .= $nama_obat . " - " . $kuantitas_obat . " x Rp " . number_format($harga, 2) . "<br>";

                // Kurangi stok obat setelah transaksi
                $new_stok = $stok - $kuantitas_obat;
                $update_query = "UPDATE obat SET stok = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ii", $new_stok, $obat_id);
                $update_stmt->execute();
            }

            $stmt->free_result(); // Bebaskan hasil query setelah digunakan
        }

        // Masukkan transaksi ke dalam database
        $query = "INSERT INTO transaksi (id_user, tanggal, total_harga, detail_transaksi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isds", $id_user, $tanggal, $total_harga, $detail_transaksi);
        $stmt->execute();

        // Commit transaksi jika semua berjalan lancar
        $conn->commit();
        $success = "Transaksi berhasil dicatat dan stok telah diperbarui!";
    } catch (Exception $e) {
        // Rollback jika ada error
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