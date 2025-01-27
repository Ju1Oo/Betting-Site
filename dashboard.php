<?php
require 'db.php';

session_start();

// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pobieramy dane użytkownika, w tym saldo
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Przechowujemy saldo użytkownika
$balance = $user['balance'];

// Pobieranie zakładów użytkownika
$stmt = $pdo->prepare("SELECT * FROM bets WHERE user_id = ?");
$stmt->execute([$user_id]);
$bets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel użytkownika</title>
</head>
<body>
    <h1>Twoje zakłady</h1>

    <p>Aktualna liczba żetonów: <?php echo $balance; ?></p>

    <ul>
        <?php foreach ($bets as $bet): ?>
            <li>Zakład ID: <?php echo $bet['id']; ?>, Mecz: <?php echo $bet['match_id']; ?>, Typ: <?php echo $bet['bet_type']; ?>, Stawka: <?php echo $bet['stake']; ?>, Potencjalna wygrana: <?php echo $bet['potential_win']; ?>, Status: <?php echo $bet['status']; ?></li>
        <?php endforeach; ?>
    </ul>

    <a href="bet.php">Obstaw nowy zakład</a>
    <a href="logout.php">Wyloguj</a>
</body>
</html>
