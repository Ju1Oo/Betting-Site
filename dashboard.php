<?php
require 'db.php';
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pobieranie aktywnych zakładów użytkownika
$stmt = $pdo->prepare("SELECT * FROM bets WHERE user_id = ? AND status = 'oczekujący'");
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
                    <td><?php echo htmlspecialchars($bet['match_id']); ?></td>
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
