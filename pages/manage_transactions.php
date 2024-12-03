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
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Transaksi</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body>
    <h1>Kelola Transaksi</h1>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <div id="obat-container">
            <div class="obat-item">
                <label>Obat:</label>
                <select name="obat_id[]" required>
                    <?php
                    $query = "SELECT * FROM obat";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                    }
                    ?>
                </select>
                <label>Kuantitas:</label>
                <input type="number" name="kuantitas[]" required min="1">
            </div>
        </div>
        <button type="button" id="add-obat">Tambah Obat</button>
        <button type="submit">Simpan</button>
    </form>

    <h2>Riwayat Transaksi</h2>
    <table border="1">
        <tr>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Detail Transaksi</th>
        </tr>
        <?php
        // Menampilkan riwayat transaksi
        $query = "SELECT * FROM transaksi ORDER BY tanggal DESC";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)):
        ?>
        <tr>
            <td><?= $row['tanggal']; ?></td>
            <td>Rp <?= number_format($row['total_harga'], 2); ?></td>
            <td><?= htmlspecialchars($row['detail_transaksi']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <script>
        // Menambahkan input obat baru
        document.getElementById("add-obat").addEventListener("click", function() {
            const container = document.getElementById("obat-container");
            const newItem = document.createElement("div");
            newItem.classList.add("obat-item");
            newItem.innerHTML = `
                <label>Obat:</label>
                <select name="obat_id[]" required>
                    <?php
                    $query = "SELECT * FROM obat";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                    }
                    ?>
                </select>
                <label>Kuantitas:</label>
                <input type="number" name="kuantitas[]" required min="1">
            `;
            container.appendChild(newItem);
        });
    </script>
</body>

</html>
