<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Strona główna</title>
</head>
<body>
    <h1>Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="dashboard.php">Panel użytkownika</a>
    <a href="logout.php">Wyloguj</a><br>
    <?php if ($_SESSION['is_admin'] == 1): ?>
    <a href="admin.php">Panel Administratora</a>
    <?php endif; ?>

</body>
</html>
