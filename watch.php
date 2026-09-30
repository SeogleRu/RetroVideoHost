<?php
require_once 'config.php';

$id = $_GET['id'] ?? '';
$videos = get_videos();
$video = null;
$idx = -1;
foreach ($videos as $i => $v) {
    if ($v['id'] === $id) { $video = $v; $idx = $i; break; }
}

if (!$video) {
    header('Location: index.php');
    exit;
}

// Увеличиваем счётчик просмотров (один раз за сессию)
$viewed = $_SESSION['viewed'] ?? [];
if (!in_array($id, $viewed)) {
    $videos[$idx]['views']++;
    save_videos($videos);
    $video['views']++;
    $viewed[] = $id;
    $_SESSION['viewed'] = $viewed;
}

$user = current_user();

// Похожие видео (та же категория, кроме текущего)
$related = array_filter($videos, fn($v) =>
    $v['id'] !== $id && ($v['category'] ?? '') === ($video['category'] ?? '')
);
$related = array_slice($related, 0, 8);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title><?= h($video['title']) ?> — Retro Video Host</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <a href="index.php" class="logo">Retro Video<span class="tube">Host</span></a>
    <div class="search-box">
        <form action="search.php" method="get">
            <input type="text" name="q" placeholder="Поиск видео...">
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
    <div class="breadcrumb">
        <a href="index.php">Главная</a> ›
        <a href="index.php?cat=<?= urlencode($video['category'] ?? 'Разное') ?>"><?= h($video['category'] ?? 'Разное') ?></a> ›
        <?= h($video['title']) ?>
    </div>

    <div class="watch-layout">
        <div class="watch-main">
            <div class="player-box">
                <video controls autoplay>
                    <source src="videos/<?= h($video['file']) ?>" type="video/mp4">
                    Ваш браузер не поддерживает видео.
                </video>
            </div>

            <h1 class="watch-title"><?= h($video['title']) ?></h1>

            <div class="watch-info">
                <span class="author"><?= h($video['author']) ?></span> ·
                <?= time_ago($video['created']) ?> ·
                <?= views_count($video['views']) ?> просмотров
                <div class="watch-actions">
                    <a href="#">★ В избранное</a>
                    <a href="#">⚑ Пожаловаться</a>
                    <a href="#">🔗 Ссылка</a>
                </div>
            </div>

            <div class="watch-description">
                <b>Описание:</b><br>
                <?= nl2br(h($video['description'] ?: 'Описание отсутствует.')) ?>
            </div>
        </div>

        <div class="watch-side">
            <h3 style="font-size:12px;background:linear-gradient(to bottom,#F5F5F5,#D5D5D5);border:1px solid #BBB;padding:4px 6px;border-radius:3px 3px 0 0;margin-bottom:8px;">
                Похожие видео
            </h3>
            <?php if (empty($related)): ?>
                <div style="font-size:11px;color:#999;">Нет похожих видео</div>
            <?php endif; ?>
            <?php foreach ($related as $r): ?>
                <div class="related-item">
                    <a href="watch.php?id=<?= h($r['id']) ?>" class="thumb">
                        <video preload="metadata" muted>
                            <source src="videos/<?= h($r['file']) ?>#t=0.5" type="video/mp4">
                        </video>
                    </a>
                    <div class="info">
                        <a href="watch.php?id=<?= h($r['id']) ?>" class="title"><?= h($r['title']) ?></a>
                        <div class="meta">
                            <?= h($r['author']) ?><br>
                            <?= views_count($r['views']) ?> просм.
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="footer"></div>
</div>

</body>
</html>
