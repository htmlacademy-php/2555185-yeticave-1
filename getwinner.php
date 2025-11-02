<?php
require 'vendor/autoload.php';
require_once 'init.php';
require_once 'helpers.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$sql_lots_without_winners = 'SELECT l.id, l.title, l.description '
    . 'FROM lots l '
    . 'WHERE l.winner_id IS NULL '
    . 'AND l.end_date <= NOW()';

$result = mysqli_query($link, $sql_lots_without_winners);

if (!$result) {
    exit();
}

$expired_lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($expired_lots as $lot) {
    $lot_id = $lot['id'];
    $lot_title = $lot['title'];
    $lot_description = $lot['description'];

    $sql_last_bid = 'SELECT b.id, b.user_id, b.amount, u.name as user_name, u.email '
        . 'FROM bids b '
        . 'JOIN users u ON b.user_id = u.id '
        . 'WHERE b.lot_id = ? '
        . 'ORDER BY b.amount DESC, b.created_at DESC '
        . 'LIMIT 1';

    $stmt = mysqli_prepare($link, $sql_last_bid);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $bid_result = mysqli_stmt_get_result($stmt);
    $last_bid = mysqli_fetch_assoc($bid_result);

    if ($last_bid) {
        $winner_id = $last_bid['user_id'];
        $winner_name = $last_bid['user_name'];
        $winner_email = $last_bid['email'];
        $winner_bid_amount = $last_bid['amount'];

        $sql_update_winner = 'UPDATE lots SET winner_id = ? WHERE id = ?';
        $stmt = mysqli_prepare($link, $sql_update_winner);
        mysqli_stmt_bind_param($stmt, 'ii', $winner_id, $lot_id);

        if (mysqli_stmt_execute($stmt)) {

           $dsn = 'smtp://4234:32434@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);

            $emailMessage = include_template('email.php', [
                'winner_name' => $winner_name,
                'lot_title' => $lot_title,
                'lot_id' => $lot_id
            ]);

            // Формирование сообщения
            $message = new Email();
            $message->to($winner_email);
            $message->from("keks@phpdemo.ru");
            $message->subject("Ваша ставка победила");
            $message->html($emailMessage);

            // Отправка сообщения
           $mailer->send($message);

            error_log("Победитель определен: $winner_name ($winner_email) - лот '$lot_title'");
        }
    }
}

mysqli_free_result($result);
