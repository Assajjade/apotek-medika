<?php
// Start the session and check if user is logged in
// session_start();
// if (!isset($_SESSION['user'])) {
//     header('Location: pages/login.php');
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Apotek Media Medika</title>
    <link href="./dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800 font-sans">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <h1 class="text-2xl font-semibold">POS Apotek Media Medika</h1>
            <nav class="flex space-x-4">
                <a href="pages/Login.php" class="hover:text-gray-200 px-4">Login</a>
                <a href="pages/register.php" class="hover:text-gray-200 px-4">Register</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-6 max-w-screen-lg">
        <h2 class="text-3xl font-semibold text-center text-blue-600 mb-6">
            Selamat Datang di POS Apotek Media Medika
        </h2>
        <p class="text-gray-600 text-center">
            Sistem Point of Sale untuk pengelolaan transaksi, stok, dan pelacakan kadaluarsa obat.
        </p>
    </main>

    <!-- Footer -->
    <?php include 'templates/footer.php'; ?>