<?php

function getCategories($link): array
{
    $sql = 'SELECT title, symbol_code FROM categories';
    $result = mysqli_query($link, $sql);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function getAdsList($link): array
{
    $sql = 'SELECT lots.title AS lot_title, lots.id AS lot_id, lots.start_price, lots.image, lots.category_id, lots.end_date, categories.title AS category_title '
. 'FROM lots '
. 'JOIN categories ON lots.category_id = categories.id '
. 'WHERE lots.end_date > NOW() '
. 'ORDER BY lots.created_at DESC LIMIT 6;';

    $result = mysqli_query($link, $sql);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function getAd($link, $id): ?array
{
    $sql = 'SELECT l.id, l.title, start_price, bidding_step, image, l.end_date, c.title AS category_title, description FROM lots l '
                . 'JOIN categories c ON category_id = c.id '
                . 'WHERE l.id = ?';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}


function getMaxBet($link, $id): ?array
{
    $sql = 'SELECT l.id, l.title, start_price, bet_step, COALESCE(MAX(b.amount), start_price) AS current_price '
                . 'FROM lots l '
                . 'LEFT JOIN bets b ON b.lot_id = l.id '
                . 'WHERE l.id = ? GROUP BY l.id';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}
