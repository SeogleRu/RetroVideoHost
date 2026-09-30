<?php
require_once 'config.php';

$videos = get_videos();
// Сортируем: свежие сверху
usort($videos, fn($a, $b) => $b['created'] - $a['created']);

$user = current_user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Retro Video Host— Broadcast Yourself</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <a href="index.php" class="logo">Retro Video<span class="tube">Host</span></a>
    <div class="search-box">
        <form action="search.php" method="get">
            <input type="text" name="q" placeholder="Поиск видео..." value="<?= h($_GET['q'] ?? '') ?>">
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
        <a href="index.php">Главная</a> › Все видео (<?= count($videos) ?>)
    </div>

    <div class="layout">
        <div class="sidebar">
            <h3>Категории</h3>
            <ul>
                <li><a href="index.php">Все видео</a></li>
                <?php
                $cats = [];
                foreach ($videos as $v) {
                    $c = $v['category'] ?? 'Разное';
                    $cats[$c] = ($cats[$c] ?? 0) + 1;
                }
                foreach ($cats as $c => $n): ?>
                    <li><a href="index.php?cat=<?= urlencode($c) ?>"><?= h($c) ?> (<?= $n ?>)</a></li>
                <?php endforeach; ?>
            </ul>
            <h3>Статистика</h3>
            <ul>
                <li>Видео: <b><?= count($videos) ?></b></li>
                <li>Просмотов: <b><?= views_count(array_sum(array_column($videos, 'views'))) ?></b></li>
            </ul>
        </div>

        <div class="video-grid">
            <?php
            $cat = $_GET['cat'] ?? null;
            $shown = 0;
            foreach ($videos as $v):
                if ($cat && ($v['category'] ?? 'Разное') !== $cat) continue;
                $shown++;
            ?>
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
                        <?= views_count($v['views']) ?> просмотров<br>
                        <?= time_ago($v['created']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($shown === 0): ?>
        <div class="no-videos">Пока нет видео. <a href="upload.php">Загрузите первое!</a></div>
    <?php endif; ?>

    <div class="footer">
        Мы не имеем никакого отношения к YouTube. Все совпадения случайны
    </div>
</div>

</body>
</html>
