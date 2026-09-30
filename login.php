<?php
require_once 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $users = get_users();
    if (isset($users[$u]) && password_verify($p, $users[$u])) {
        $_SESSION['user'] = $u;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверное имя пользователя или пароль.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Вход — Retro Video Host</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <a href="index.php" class="logo">Retro Video<span class="tube">Host</span></a>
</div>
<div class="container">
    <div class="form-box">
        <h2>Вход</h2>
        <?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
        <form method="post">
            <label>Имя пользователя</label>
            <input type="text" name="username" required>
            <label>Пароль</label>
            <input type="password" name="password" required>
            <button type="submit">Войти</button>
        </form>
        <p style="margin-top:12px;font-size:11px;">Нет аккаунта? <a href="https://seogleru.github.io/RetroVideoHost/register.php">Зарегистрируйтесь</a></p>
    </div>
</div>
</body>
</html>
