<?php
session_start();

require_once('./helpers.php');
require_once 'init.php';

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
} else {

    $sql = 'SELECT title, symbol_code FROM categories';
    $result = mysqli_query($link, $sql);

    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $lots = [];
    $total_lots = 0;
    $current_page = $_GET['page'] ?? 1;
    $items_per_page = 9;
    $offset = ($current_page - 1) * $items_per_page;

    $search = trim($_GET['search'] ?? '');

    if (!empty($search)) {

        $count_sql = 'SELECT COUNT(*) as total
                      FROM lots
                      JOIN categories ON lots.category_id = categories.id
                      WHERE lots.end_date > NOW()
                      AND MATCH(lots.title, lots.description) AGAINST(?)';

        $stmt_count = db_get_prepare_stmt($link, $count_sql, [$search]);
        mysqli_stmt_execute($stmt_count);
        $result_count = mysqli_stmt_get_result($stmt_count);
        $total_lots = mysqli_fetch_assoc($result_count)['total'];

        $lots_sql = 'SELECT lots.title AS lot_title, lots.id AS lot_id, lots.start_price, lots.image,
                            lots.category_id, lots.end_date, categories.title AS category_title '
            . 'FROM lots '
            . 'JOIN categories ON lots.category_id = categories.id '
            . 'WHERE lots.end_date > NOW() '
            . 'AND MATCH(lots.title, lots.description) AGAINST(?) '
            . 'ORDER BY lots.created_at DESC '
            . 'LIMIT ? OFFSET ?';

        $stmt = db_get_prepare_stmt($link, $lots_sql, [$search, $items_per_page, $offset]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $pages_count = ceil($total_lots / $items_per_page);
    }

    $pageContent = include_template('search-template.php', [
        'categories' => $categories,
        'lots' => $lots,
        'search' => $search,
        'pages_count' => $pages_count ?? 1,
        'current_page' => $current_page,
        'total_lots' => $total_lots
    ]);

    $pageLayout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'title' => 'Результаты поиска',
        'categories' => $categories,
    ]);

    print $pageLayout;
}
