<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Apotek Media Medika</title>
    <link href="../dist/output.css" rel="stylesheet">
    <script src="../assets/js/script.js"></script>
</head>

<body class="bg-gray-100">
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto p-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">POS Apotek Media Medika</h1>
                <p class="text-sm">Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']); ?></p>
            </div>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="dashboard.php" class="hover:text-blue-300">Dashboard</a></li>
                    <li><a href="manage_stocks.php" class="hover:text-blue-300">Kelola Stok</a></li>
                    <li><a href="manage_transactions.php" class="hover:text-blue-300">Kelola Transaksi</a></li>
                    <li><a href="manage_expirations.php" class="hover:text-blue-300">Kelola Kedaluwarsa</a></li>
                    <li><a href="reports.php" class="hover:text-blue-300">Laporan</a></li>
                    <li><a href="logout.php" class="hover:text-red-300">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto mt-6 p-4">
