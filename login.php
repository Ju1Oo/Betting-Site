<?php
require 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Pobieranie użytkownika na podstawie podanego e-maila
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Sprawdzanie poprawności danych logowania
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['isAdmin']; // Poprawne ustawienie zmiennej w sesji
        header("Location: index.php");
        exit();
    } else {
        $error = "Nieprawidłowy email lub hasło.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
    <form method="POST">
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Hasło: <input type="password" name="password" required></label><br>
        <button type="submit">Zaloguj</button>
    </form><br>
    <a href="register.php">REJESTRACJA</a>
    <?php if (!empty($error)) echo "<p>$error</p>"; ?>
</body>
</html>
