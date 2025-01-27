<?php
require 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pobranie listy dostępnych meczów z bazy danych
$stmt = $pdo->query("SELECT id, team_a, team_b, start_time, odds_team_a, odds_draw, odds_team_b FROM matches WHERE start_time > NOW()");
$matches = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id = $_POST['match_id'];
    $bet_type = $_POST['bet_type'];
    $stake = $_POST['stake'];
    $user_id = $_SESSION['user_id'];

    // Sprawdzenie, czy stawka jest poprawna
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user['balance'] < $stake) {
        $error = "Nie masz wystarczających środków na koncie.";
    } else {
        // Obliczenie potencjalnej wygranej
        $stmt = $pdo->prepare("SELECT odds_team_a, odds_draw, odds_team_b FROM matches WHERE id = ?");
        $stmt->execute([$match_id]);
        $match = $stmt->fetch();

        $odds = 0;
        if ($bet_type === 'team_a') $odds = $match['odds_team_a'];
        if ($bet_type === 'draw') $odds = $match['odds_draw'];
        if ($bet_type === 'team_b') $odds = $match['odds_team_b'];

        $potential_win = $stake * $odds;

        // Dodanie zakładu do bazy
        $stmt = $pdo->prepare("INSERT INTO bets (user_id, match_id, bet_type, stake, potential_win, status) VALUES (?, ?, ?, ?, ?, 'oczekujący')");
        $stmt->execute([$user_id, $match_id, $bet_type, $stake, $potential_win]);

        // Aktualizacja salda użytkownika
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$stake, $user_id]);

        $success = "Zakład został pomyślnie złożony!";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Obstawianie meczu</title>
</head>
<body>
    <h1>Obstawianie meczu</h1>

    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>

    <?php if (count($matches) > 0): ?>
        <form method="POST">
            <label for="match_id">Wybierz mecz:</label>
            <select name="match_id" id="match_id" required>
                <?php foreach ($matches as $match): ?>
                    <option value="<?php echo $match['id']; ?>">
                        <?php echo htmlspecialchars($match['team_a']) . " vs " . htmlspecialchars($match['team_b']) . " (" . $match['start_time'] . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label for="bet_type">Rodzaj zakładu:</label>
            <select name="bet_type" id="bet_type" required>
                <option value="team_a">Wygrana drużyny A</option>
                <option value="draw">Remis</option>
                <option value="team_b">Wygrana drużyny B</option>
            </select>
            <br><br>

            <label for="stake">Stawka (żetony):</label>
            <input type="number" name="stake" id="stake" min="1" required>
            <br><br>

            <button type="submit">Obstaw zakład</button>
        </form>
    <?php else: ?>
        <p>Obecnie brak dostępnych meczów do obstawienia.</p>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">Powrót do panelu użytkownika</a>
</body>
</html>
