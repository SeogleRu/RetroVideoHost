<?php
require_once 'config.php';

$user = current_user();
if (!$user) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'Разное');

    if ($title === '') $error = 'Введите название видео.';
    elseif (empty($_FILES['video']['name'])) $error = 'Выберите видеофайл.';
    elseif ($_FILES['video']['error'] !== UPLOAD_ERR_OK) $error = 'Ошибка загрузки: код ' . $_FILES['video']['error'];
    else {
        $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'mkv'];
        if (!in_array($ext, $allowed)) {
            $error = 'Недопустимый формат. Разрешены: ' . implode(', ', $allowed);
        } else {
            $id = bin2hex(random_bytes(6));
            $filename = $id . '.' . $ext;
            $dest = VIDEO_DIR . $filename;

            if (move_uploaded_file($_FILES['video']['tmp_name'], $dest)) {
                $videos = get_videos();
                $videos[] = [
                    'id' => $id,
                    'title' => $title,
                    'description' => $description,
                    'category' => $category ?: 'Разное',
                    'author' => $user,
                    'file' => $filename,
                    'views' => 0,
                    'created' => time(),
                ];
                save_videos($videos);
                header('Location: watch.php?id=' . $id);
                exit;
            } else {
                $error = 'Не удалось сохранить файл.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Загрузка видео — Retro Video Host</title>
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
        <a href="upload.php">Загрузить</a>
        <a href="logout.php">Выход (<?= h($user) ?>)</a>
    </div>
</div>

<div class="container">
    <div class="form-box">
        <h2>Загрузка видео</h2>
        <?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="uploadForm">
            <label>Название *</label>
            <input type="text" name="title" required maxlength="100" value="<?= h($_POST['title'] ?? '') ?>">

            <label>Описание</label>
            <textarea name="description" maxlength="2000"><?= h($_POST['description'] ?? '') ?></textarea>

            <label>Категория</label>
            <select name="category" style="width:100%;padding:5px;border:1px solid #999;border-radius:2px;font-size:12px;">
                <option>Разное</option>
                <option>Музыка</option>
                <option>Юмор</option>
                <option>Игры</option>
                <option>Спорт</option>
                <option>Кино</option>
                <option>Технологии</option>
                <option>Обучение</option>
            </select>

            <label>Видеофайл * (mp4, webm, ogg, avi, mov, mkv)</label>
            <input type="file" name="video" accept="video/*" required>

            <div class="upload-progress" id="progress"><div class="bar" id="progressBar">0%</div></div>

            <button type="submit">Загрузить видео</button>
        </form>
    </div>
    <div class="footer"></div>
</div>

<script>
// Прогресс-бар загрузки через XHR
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const fd = new FormData(form);
    const xhr = new XMLHttpRequest();
    const progress = document.getElementById('progress');
    const bar = document.getElementById('progressBar');

    progress.style.display = 'block';

    xhr.upload.addEventListener('progress', function(ev) {
        if (ev.lengthComputable) {
            const p = Math.round(ev.loaded / ev.total * 100);
            bar.style.width = p + '%';
            bar.textContent = p + '%';
        }
    });

    xhr.addEventListener('load', function() {
        // Ответ — HTML страницы. Если редирект — берём URL из редиректа
        // Проще: перезагрузим страницу с теми же данными
        window.location.reload();
    });

    xhr.open('POST', 'upload.php');
    xhr.send(fd);
});
</script>

</body>
</html>
