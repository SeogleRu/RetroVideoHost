<?php
session_start();

define('DATA_FILE', __DIR__ . '/data/videos.json');
define('VIDEO_DIR', __DIR__ . '/videos/');
define('USERS_FILE', __DIR__ . '/data/users.json');

// Инициализация файлов данных
if (!file_exists(__DIR__ . '/data')) mkdir(__DIR__ . '/data', 0777, true);
if (!file_exists(VIDEO_DIR)) mkdir(VIDEO_DIR, 0777, true);
if (!file_exists(DATA_FILE)) file_put_contents(DATA_FILE, json_encode([]));
if (!file_exists(USERS_FILE)) file_put_contents(USERS_FILE, json_encode([]));

function get_videos() {
    return json_decode(file_get_contents(DATA_FILE), true) ?: [];
}

function save_videos($videos) {
    file_put_contents(DATA_FILE, json_encode($videos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function get_users() {
    return json_decode(file_get_contents(USERS_FILE), true) ?: [];
}

function save_users($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function current_user() {
    if (!isset($_SESSION['user'])) return null;
    return $_SESSION['user'];
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function time_ago($timestamp) {
    $diff = time() - $timestamp;
    if ($diff < 60) return $diff . ' секунд назад';
    if ($diff < 3600) return floor($diff / 60) . ' минут назад';
    if ($diff < 86400) return floor($diff / 3600) . ' часов назад';
    if ($diff < 2592000) return floor($diff / 86400) . ' дней назад';
    if ($diff < 31536000) return floor($diff / 2592000) . ' месяцев назад';
    return floor($diff / 31536000) . ' лет назад';
}

function views_count($n) {
    return number_format($n, 0, '.', ' ');
}
