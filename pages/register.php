<?php
session_start();
require_once '../config/db.php';

$result = $conn->query("SELECT COUNT(*) AS user_count FROM users");
$row = $result->fetch_assoc();
if ($row['user_count'] > 0) {
    die("Registrasi tidak diizinkan karena pengguna sudah ada.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header('Location: login.php');
            exit;
        } else {
            $error = "Username sudah digunakan.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registrasi Pengguna</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>

<body>
    <div class="register-container">
        <h2>Registrasi</h2>
        <?php if (isset($error)) : ?>
            <div class="alert error"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Konfirmasi Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Daftar</button>
        </form>
    </div>
</body>

</html>