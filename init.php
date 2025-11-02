<?php
// require_once 'functions.php';
$db = require_once 'db.php';

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if (!$link) {
    $error = mysqli_connect_error() ?? 'Неизвестная ошибка подключения к базе данных';
    $content = include_template('error.php', ['error' => $error]);

    $layout = include_template('layout.php', [
        'pageContent' => $content,
        'title' => 'Ошибка подключения',
        'categories' => [],
        'isAuth' => false,
        'userName' => 'false',
        'error' => $error,
    ]);
    print $layout;
    exit;
}

mysqli_set_charset($link, charset:"utf8");
