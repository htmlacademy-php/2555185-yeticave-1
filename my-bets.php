<?php
session_start();
require_once 'init.php';
require_once('./helpers.php');

$sql_categories = 'SELECT title, symbol_code FROM categories';
$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
} else {
    $categories = [];
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

$sql = "SELECT
            b.id as bid_id,
            b.amount,
            b.created_at,
            b.lot_id,
            l.title as lot_title,
            l.image as lot_image,
            l.end_date as lot_end_date,
            l.author_id as lot_author_id,
            l.winner_id,
            c.title as category_title,
            u.contact_info,
            l.end_date < NOW() as expired
        FROM bids b
        JOIN lots l ON b.lot_id = l.id
        JOIN categories c ON l.category_id = c.id
        LEFT JOIN users u ON l.author_id = u.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC";

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bids = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['is_winner'] = ($row['winner_id'] == $userId);
    $row['time'] = get_dt_range($row['lot_end_date']);
    $bids[] = $row;
}

$pageContent = include_template('my-bets-template.php', [
    'bids' => $bids,
    'categories' => $categories
]);

$layout = include_template('layout.php', [
    'pageContent' => $pageContent,
    'title' => 'Мои ставки',
    'categories' => $categories,
    'showNavigation' => true,
]);

print $layout;
