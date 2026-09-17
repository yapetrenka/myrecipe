<?php
// File: `spa.php`
// Подключаем конфиг чтобы работать с MyCMS::init('dir')
include("config.php");

// Fallback: простая оболочка, если сборки нет — укажите правильные пути к JS/CSS
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="./favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>my-vue-app</title>
    <script type="module" crossorigin src="/assets/index-BqRxIDeu.js"></script>
    <link rel="stylesheet" crossorigin href="/assets/index-C3b_1WT6.css">
</head>
<body style="opacity: 0;">
<div id="app"></div>
</body>
</html>