<?php
session_start();

$isAuth = isset($_SESSION['user']);
$userName = $_SESSION['user']['name'] ?? '';

date_default_timezone_set('Asia/Vladivostok');

require_once('./helpers.php');
require_once 'init.php';
require_once 'getwinner.php';

    // ПОЛУЧЕНИЕ КАТЕГОРИЙ
    $sql = 'SELECT id, title, symbol_code FROM categories';
    $result = mysqli_query($link, $sql);

    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Определяем выбранную категорию
    $selected_category_id = $_GET['category_id'] ?? null;
    $selected_category_title = '';
    $page_title = 'Главная';

    // Заголовок по умолчанию - "Открытые лоты"
    $section_title = 'Открытые лоты';

    // Если выбрана категория, меняем заголовок
    if ($selected_category_id) {
        $category_sql = "SELECT title FROM categories WHERE id = ?";
        $stmt = mysqli_prepare($link, $category_sql);
        mysqli_stmt_bind_param($stmt, 'i', $selected_category_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($category = mysqli_fetch_assoc($result)) {
            $selected_category_title = $category['title'];
            $page_title = "Лоты в категории «{$selected_category_title}»";
            $section_title = "Все лоты в категории «{$selected_category_title}»";
        }
    }
    // ПОЛУЧЕНИЕ ЛОТОВ с учетом выбранной категории
    if ($selected_category_id) {
        $lots_sql = 'SELECT lots.title AS lot_title, lots.id AS lot_id, lots.start_price, lots.image, lots.category_id, lots.end_date, categories.title AS category_title '
            . 'FROM lots '
            . 'JOIN categories ON lots.category_id = categories.id '
            . 'WHERE lots.end_date > NOW() AND lots.category_id = ? '
            . 'ORDER BY lots.created_at DESC LIMIT 6;';

        $stmt = mysqli_prepare($link, $lots_sql);
        mysqli_stmt_bind_param($stmt, 'i', $selected_category_id);
        mysqli_stmt_execute($stmt);
        $advertisements = mysqli_stmt_get_result($stmt);
    } else {

        $lots_sql = 'SELECT lots.title AS lot_title, lots.id AS lot_id, lots.start_price, lots.image, lots.category_id, lots.end_date, categories.title AS category_title '
            . 'FROM lots '
            . 'JOIN categories ON lots.category_id = categories.id '
            . 'WHERE lots.end_date > NOW() '
            . 'ORDER BY lots.created_at DESC LIMIT 6;';

        $advertisements = mysqli_query($link, $lots_sql);
    }

    if ($advertisements) {
        $lots = mysqli_fetch_all($advertisements, MYSQLI_ASSOC);
    }

    $pageContent = include_template('main.php', [
        'categories' => $categories,
        'advertisements' => $lots,
        'section_title' => $section_title,
        'selected_category_id' => $selected_category_id
    ]);

    $pageLayout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'title' => $page_title,
        'categories' => $categories,
        'showNavigation' => false,
    ]);

    print $pageLayout;

