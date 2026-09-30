<?php
require_once 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $u)) {
        $error = 'Имя: 3-20 символов (латиница, цифры, _).';
    } elseif (strlen($p) < 4) {
        $error = 'Пароль минимум 4 символа.';
    } elseif ($p !== $p2) {
        $error = 'Пароли не совпадают.';
    } else {
        $users = get_users();
        if (isset($users[$u])) {
            $error = 'Такое имя уже занято.';
        } else {
            $users[$u] = password_hash($p, PASSWORD_DEFAULT);
            save_users($users);
            $_SESSION['user'] = $u;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Регистрация — Retro Video Host</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <a href="index.php" class="logo">Retro Video<span class="tube">Host</span></a>
</div>
<div class="container">
    <div class="form-box">
        <h2>Регистрация</h2>
        <?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
        <form method="post">
            <label>Имя пользователя</label>
            <input type="text" name="username" required value="<?= h($_POST['username'] ?? '') ?>">
            <label>Пароль</label>
            <input type="password" name="password" required>
            <label>Повторите пароль</label>
            <input type="password" name="password2" required>
            <button type="submit">Создать аккаунт</button>
        </form>
        <p style="margin-top:12px;font-size:11px;">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div>
</body>
</html>
