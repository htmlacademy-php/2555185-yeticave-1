<?php

session_start();
require_once('./helpers.php');
require_once 'init.php';

$isAuth = isset($_SESSION['user']);
$userName = $_SESSION['user']['name'] ?? '';


// Получаем категории

$sql_categories = 'SELECT title, symbol_code FROM categories';
$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
} else {
    $categories = [];
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$sql = 'SELECT l.id, l.title AS lots_title, l.description, l.image, l.start_price, l.bidding_step, l.created_at, l.end_date, c.title AS category_title '
    . 'FROM lots l '
    . 'JOIN categories c ON l.category_id = c.id '
    . 'WHERE l.id = ?;';

$stmt = db_get_prepare_stmt($link, $sql, [$id]);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$lot = mysqli_fetch_assoc($res);

if (!$lot) {

    http_response_code(404);
    $pageContent = include_template('404.php', [
        'categories' => $categories,
        'error_code' => 404,
        'error_text' => 'Данной страницы не существует на сайте.'
    ]);

    $layout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'title' => 'Доступ запрещен',
        'categories' => $categories,
    ]);
    print $layout;
    exit;
}
// Вычисляем текущую цену и минимальную ставку
$currentPrice = getCurrentPrice($link, $lot['id']);
$minBid = $currentPrice + $lot['bidding_step'];

//Вычисление ставок по лоту
$bids = getBids($link, $lot['id']);

$pageContent = include_template('lot-template.php', [
    'lot' => $lot,
    'categories' => $categories,
    'currentPrice' => $currentPrice,
    'minBid' => $minBid,
    'bids' => $bids

]);

$layout = include_template('layout.php', [
    'pageContent' => $pageContent,
    'title' => $lot['lots_title'],
    'categories' => $categories,
    'showNavigation' => true,
    'isAuth' => $isAuth,
    'userName' => $userName
]);

print $layout;
