<?php
require_once 'config.php';
$q = trim($_GET['q'] ?? '');
$videos = get_videos();
if ($q !== '') {
    $q_lower = mb_strtolower($q);
    $videos = array_filter($videos, function($v) use ($q_lower) {
        return mb_strpos(mb_strtolower($v['title']), $q_lower) !== false
            || mb_strpos(mb_strtolower($v['description'] ?? ''), $q_lower) !== false
            || mb_strpos(mb_strtolower($v['author']), $q_lower) !== false;
    });
}
usort($videos, fn($a, $b) => $b['created'] - $a['created']);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Поиск: <?= h($q) ?> — Retro Video Host</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <a href="index.php" class="logo">Retro Video<span class="tube">Host</span></a>
    <div class="search-box">
        <form action="search.php" method="get">
            <input type="text" name="q" value="<?= h($q) ?>">
            <button type="submit">Поиск</button>
        </form>
    </div>
    <div class="nav-links">
        <?php if ($user): ?>
            <a href="upload.php">Загрузить</a>
            <a href="logout.php">Выход (<?= h($user) ?>)</a>
        <?php else: ?>
            <a href="login.php">Вход</a>
            <a href="register.php">Регистрация</a>
        <?php endif; ?>
    </div>
</div>
<div class="container">
    <div class="breadcrumb"><a href="index.php">Главная</a> › Поиск</div>
    <h2 style="font-size:14px;margin-bottom:10px;">Результаты поиска: «<?= h($q) ?>» (<?= count($videos) ?>)</h2>
    <div class="video-grid">
        <?php foreach ($videos as $v): ?>
            <div class="video-item">
                <a href="watch.php?id=<?= h($v['id']) ?>" class="video-thumb">
                    <video preload="metadata" muted>
                        <source src="videos/<?= h($v['file']) ?>#t=0.5" type="video/mp4">
                    </video>
                    <div class="play-overlay"><div class="play-btn">▶</div></div>
                </a>
                <a href="watch.php?id=<?= h($v['id']) ?>" class="video-title"><?= h($v['title']) ?></a>
                <div class="video-meta">
                    <span class="video-author"><?= h($v['author']) ?></span><br>
                    <?= views_count($v['views']) ?> просмотров
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($videos)): ?>
        <div class="no-videos">Ничего не найдено.</div>
    <?php endif; ?>
    <div class="footer"></div>
</div>
</body>
</html>
