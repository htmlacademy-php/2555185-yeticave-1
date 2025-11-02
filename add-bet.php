<?php
/**
 * @var mysqli $link
 */
session_start();
require_once 'init.php';
require_once('./helpers.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$lotId = filter_input(INPUT_POST, 'lot_id', FILTER_VALIDATE_INT);
if (!$lotId) {
    $_SESSION['error'] = 'Неверный идентификатор лота';
    header("Location: index.php");
    exit();
}

$sql = 'SELECT l.id, l.title AS lots_title, l.description, l.image, l.start_price, l.bidding_step, l.created_at, l.end_date, c.title AS category_title '
    . 'FROM lots l '
    . 'JOIN categories c ON l.category_id = c.id '
    . 'WHERE l.id = ?;';

$stmt = db_get_prepare_stmt($link, $sql, [$lotId]);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$lot = mysqli_fetch_assoc($res);

if (!$lot) {
    $_SESSION['error'] = 'Лот не найден';
    header("Location: index.php");
    exit();
}

$timeLeft = get_dt_range($lot['end_date']);
if ($timeLeft[0] <= 0 && $timeLeft[1] <= 0) {
    $_SESSION['error'] = 'Торги по этому лоту завершены';
    header("Location: lot.php?id=" . $lotId);
    exit();
}

$costRaw = trim($_POST['cost'] ?? '');
$errors = [];

// Проверка: заполнено ли поле
if ($costRaw === '') {
    $errors['cost'] = 'Введите вашу ставку';
} else {
    $cleanedCost = str_replace(' ', '', $costRaw);

    if (!ctype_digit($cleanedCost) || $cleanedCost === '0') {
        $errors['cost'] = 'Ставка должна быть целым положительным числом';
    } else {
        $cost = (int)$cleanedCost;
        $currentPrice = (int)getCurrentPrice($link, $lotId);
        $minBid = $currentPrice + $lot['bidding_step'];
        if ($cost < $minBid) {
            $errors['cost'] = 'Ставка должна быть не меньше ' . formatPrice($minBid);
        }
    }
}

if (!empty($errors)) {
    $_SESSION['bid_errors'] = $errors;
    header("Location: lot.php?id=" . $lotId);
    exit();
}

// Добавляем ставку в таблицу ставок
$userId = $_SESSION['user']['id'];
$bidAmount = (int)str_replace(' ', '', $costRaw);
$addedBid = saveBid($link, $bidAmount, $lotId, $userId);

if ($addedBid) {
    $_SESSION['success'] = 'Ваша ставка успешно размещена!';
    unset($_SESSION['bid_errors']);
} else {
    $_SESSION['error'] = 'Ошибка при сохранении ставки. Попробуйте еще раз.';
}

header("Location: lot.php?id=" . $lotId);
exit();
