<?php
require 'db.php';
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pobieranie salda użytkownika
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Jeśli użytkownik nie istnieje, przekieruj go do strony logowania
if (!$user) {
    header("Location: login.php");
    exit();
}

// Pobieranie aktywnych zakładów użytkownika z informacjami o drużynach
$stmt = $pdo->prepare("
    SELECT bets.*, matches.team_a, matches.team_b
    FROM bets
    JOIN matches ON bets.match_id = matches.id
    WHERE bets.user_id = ? AND bets.status = 'oczekujący'
");
$stmt->execute([$_SESSION['user_id']]);
$bets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Twoje Zakłady</h1>
    
    <!-- Wyświetlanie liczby żetonów -->
    <p>Aktualna liczba żetonów: <?php echo htmlspecialchars($user['balance']); ?></p>

    <?php if (empty($bets)): ?>
        <p>Nie masz żadnych aktywnych zakładów.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Mecz</th>
                <th>Twój typ</th>
                <th>Stawka</th>
                <th>Potencjalna wygrana</th>
            </tr>
            <?php foreach ($bets as $bet): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bet['team_a']) . " vs " . htmlspecialchars($bet['team_b']); ?></td>
                    <td><?php echo htmlspecialchars($bet['bet_type']); ?></td>
                    <td><?php echo htmlspecialchars($bet['stake']); ?> zł</td>
                    <td><?php echo htmlspecialchars($bet['potential_win']); ?> zł</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <a href="index.php">Powrót do strony głównej</a>
    <a href="bet.php">Stwórz nowy zakład</a>
</body>
</html>
