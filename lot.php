<?php
require_once('./helpers.php');
require_once('./functions.php');
require_once 'init.php';

// Получаем категории

$sql_categories = 'SELECT title, symbol_code FROM categories';
$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
} else{
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
        'categories' => $categories
    ]);

    $layout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'title' => 'Страница не найдена',
        'categories' => $categories
    ]);
    print $layout;
    exit;
}

$pageContent = include_template('lot-template.php', [
    'lot' => $lot,
    'categories'=> $categories
]);

$layout = include_template('layout.php', [
    'pageContent' => $pageContent,
    'title' => $lot['lots_title'],
    'categories' => $categories
]);

print $layout;
?>
