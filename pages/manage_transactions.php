<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

// Cek jika ID user valid
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
    $obat_ids = $_POST['obat_id'] ?? [];
    $kuantitas = $_POST['kuantitas'] ?? [];

    if (empty($obat_ids) || empty($kuantitas)) {
        $error = "Pilih setidaknya satu obat dengan kuantitas yang valid.";
    } else {
        $conn->begin_transaction();
        try {
            foreach ($obat_ids as $index => $obat_id) {
                $kuantitas_obat = $kuantitas[$index];

                if ($kuantitas_obat <= 0) {
                    throw new Exception("Kuantitas untuk salah satu obat tidak valid.");
                }

                $query = "SELECT harga, nama_obat, stok FROM obat WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $obat_id);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($harga, $nama_obat, $stok);

                if ($stmt->fetch()) {
                    if ($stok < $kuantitas_obat) {
                        throw new Exception("Stok obat '$nama_obat' tidak cukup.");
                    }

                    $total_harga += $harga * $kuantitas_obat;
                    $detail_transaksi .= "$nama_obat - $kuantitas_obat x Rp " . number_format($harga, 2) . "<br>";

                    $new_stok = $stok - $kuantitas_obat;
                    $update_query = "UPDATE obat SET stok = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("ii", $new_stok, $obat_id);
                    $update_stmt->execute();
                } else {
                    throw new Exception("Obat dengan ID $obat_id tidak ditemukan.");
                }
                $stmt->free_result();
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
}
?>

<?php include '../templates/header.php'; ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Apotek Media Medika</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<div class="max-w-4xl mx-auto p-6 bg-gray-50 rounded shadow">
    <h1 class="text-3xl font-bold mb-6">Kelola Transaksi</h1>

    <?php if (isset($success)): ?>
        <p class="bg-green-100 text-green-800 p-4 rounded"><?= $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p class="bg-red-100 text-red-800 p-4 rounded"><?= $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div id="obat-container">
            <div class="flex items-center space-x-4">
                <label for="obat_id" class="p-3 block text-gray-700">Obat:</label>
                <select name="obat_id[]" required class=" p-3 border rounded w-full">
                    <?php
                    $query = "SELECT id, nama_obat FROM obat";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                    }
                    ?>
                </select>
                <label for="kuantitas" class="p-3 block text-gray-700">Kuantitas:</label>
                <input type="number" name="kuantitas[]" required min="1" class="p-3 border rounded w-full">
            </div>
        </div>
        <button type="button" id="add-obat" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Obat</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
</div>

<script>
    document.getElementById('add-obat').addEventListener('click', function() {
        const container = document.getElementById('obat-container');
        const div = document.createElement('div');
        div.classList.add('flex', 'items-center', 'space-x-4', 'mt-4');
        div.innerHTML = `
            <label for="obat_id" class="p-3 block text-gray-700">Obat:</label>
            <select name="obat_id[]" required class="p-3 border rounded w-full">
                <?php
                $query = "SELECT id, nama_obat FROM obat";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                }
                ?>
            </select>
            <label for="kuantitas" class="p-3 block text-gray-700">Kuantitas:</label>
            <input type="number" name="kuantitas[]" required min="1" class="p-3 border rounded w-full">
        `;
        container.appendChild(div);
    });
</script>

<?php include '../templates/footer.php'; ?>
