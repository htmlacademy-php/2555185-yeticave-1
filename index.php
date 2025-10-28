<?php
session_start();

$isAuth = isset($_SESSION['user']);
$userName = $_SESSION['user']['name'] ?? '';

date_default_timezone_set('Asia/Vladivostok');

require_once('./helpers.php');
require_once('./functions.php');
require_once 'init.php';

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
} else {
    // ПОЛУЧЕНИЕ КАТЕГОРИЙ
    $sql = 'SELECT title, symbol_code FROM categories';
    $result = mysqli_query($link, $sql);

    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    // ПОЛУЧЕНИЕ ЛОТОВ
    $lots_sql = 'SELECT lots.title AS lot_title, lots.id AS lot_id, lots.start_price, lots.image, lots.category_id, lots.end_date, categories.title AS category_title '
        . 'FROM lots '
        . 'JOIN categories ON lots.category_id = categories.id '
        . 'WHERE lots.end_date > NOW() '
        . 'ORDER BY lots.created_at DESC LIMIT 6;';

    $advertisements = mysqli_query($link, $lots_sql);

    if ($advertisements) {
        $lots = mysqli_fetch_all($advertisements, MYSQLI_ASSOC);
    }
    $pageContent = include_template('main.php', [
        'categories' => $categories,
        'advertisements' => $lots
    ]);

    $pageLayout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'title' => 'Главная',
        'categories' => $categories,
        'showNavigation' => false,
    ]);

    print $pageLayout;

}
