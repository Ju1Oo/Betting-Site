<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, balance, isAdmin) VALUES (?, ?, ?, 1000, 0)");
    $stmt->execute([$username, $email, $password]);

    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
</head>
<body>
    <form method="POST">
        <label>Nazwa użytkownika: <input type="text" name="username" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Hasło: <input type="password" name="password" required></label><br>
        <button type="submit">Zarejestruj się</button>
    </form><br>
    <a href="login.php">LOGOWANIE</a>
</body>
</html>
