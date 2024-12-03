<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kelola Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Kelola Transaksi</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($success)): ?>
            <p class="bg-green-100 text-green-700 p-4 rounded-lg mb-6"><?= $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?= $error; ?></p>
        <?php endif; ?>

        <!-- Form Input Transaksi -->
        <div class="bg-white p-6 shadow-md rounded-lg">
            <form method="POST">
                <div id="obat-container" class="space-y-4">
                    <!-- Dropdown Obat -->
                    <div class="obat-item flex items-center space-x-4">
                        <label class="w-1/4 font-medium">Obat:</label>
                        <select name="obat_id[]" required class="w-2/4 border border-gray-300 rounded-lg p-2">
                            <?php
                            $query = "SELECT id, nama_obat FROM obat";
                            $result = mysqli_query($conn, $query);
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                                }
                            } else {
                                echo "<option value=''>Tidak ada obat tersedia</option>";
                            }
                            ?>
                        </select>
                        <label class="w-1/4 font-medium">Kuantitas:</label>
                        <input type="number" name="kuantitas[]" required min="1" class="w-1/4 border border-gray-300 rounded-lg p-2">
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <!-- Tombol Tambah dan Simpan -->
                    <button type="button" id="add-obat" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Tambah Pesanan Obat
                    </button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>

        <!-- Riwayat Transaksi -->
        <h2 class="text-2xl font-semibold text-blue-600 mt-10">Riwayat Transaksi</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table-auto w-full border-collapse border border-gray-300 shadow-md rounded-lg">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2">Total Harga</th>
                        <th class="border border-gray-300 px-4 py-2">Detail Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM transaksi ORDER BY tanggal DESC";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr class="bg-white hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2"><?= $row['tanggal']; ?></td>
                            <td class="border border-gray-300 px-4 py-2">Rp <?= number_format($row['total_harga'], 2); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['detail_transaksi']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script for Dynamic Obat Form -->
    <script>
        document.getElementById("add-obat").addEventListener("click", function() {
            const container = document.getElementById("obat-container");
            const newItem = document.createElement("div");
            newItem.classList.add("obat-item", "flex", "items-center", "space-x-4");
            newItem.innerHTML = `
                <label class="w-1/4 font-medium">Obat:</label>
                <select name="obat_id[]" required class="w-2/4 border border-gray-300 rounded-lg p-2">
                    <?php
                    $query = "SELECT id, nama_obat FROM obat";
                    $result = mysqli_query($conn, $query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}'>{$row['nama_obat']}</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada obat tersedia</option>";
                    }
                    ?>
                </select>
                <label class="w-1/4 font-medium">Kuantitas:</label>
                <input type="number" name="kuantitas[]" required min="1" class="w-1/4 border border-gray-300 rounded-lg p-2">
            `;
            container.appendChild(newItem);
        });
    </script>
</body>

</html>
