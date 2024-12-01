<?php
require_once '../config/db.php';

// Cek apakah ada pengguna di database
$result = $conn->query("SELECT COUNT(*) AS user_count FROM users");
$row = $result->fetch_assoc();
if ($row['user_count'] == 0) {
    header('Location: register.php');
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h1>Login POS Apotek</h1>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>

</html>