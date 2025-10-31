<?php
/**
 * Форматирует число в строку с ценой в рублях
 *
 * @param mixed $number Исходное числовое значение для форматирования
 * @return string Отформатированная строка с ценой в формате "X XXX ₽"
 */
function formatPrice($number)
{
    if ($number === null || !is_numeric($number)) {
        return '0 ₽';
    }
    $rounded = ceil($number);
    if ($rounded >= 1000) {
        $formatted = number_format($rounded, 0, '', ' ');
    } else {
        $formatted = $rounded;
    }
    return $formatted . ' ₽';
}
/**
 * Вычисляет оставшееся время до указанной даты
 *
 * @param string $date Дата и время в формате, понятном для strtotime()
 * @return array Массив с двумя элементами [часы, минуты]
 */
function get_dt_range($date)
{
    $current_time = time();
    $end_time = strtotime($date);
    $time_diff = $end_time - $current_time;

    if ($time_diff <= 0) {
        return [0, 0];
    }

    $hours = floor($time_diff / 3600);
    $minutes = floor(($time_diff % 3600) / 60);

    $time = [$hours, $minutes];

    return $time;
}
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    if (!$dateTimeObj) {
        return false;
    }

    return $dateTimeObj->format($format_to_check) === $date;
}
/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else if (is_string($value)) {
                $type = 's';
            } else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}
/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}
/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}
/**
 * Валидирует идентификатор категории
 *
 * @param mixed $id Идентификатор категории для проверки
 * @param array $allowedList Массив разрешенных идентификаторов категорий
 * @return string|null Возвращает строку с ошибкой или null если валидация успешна
 */
function validateCategory($id, $allowedList)
{

    if ($id === '' || $id === null) {
        return 'Выберите категорию';
    }

    if (!in_array($id, $allowedList)) {
        return 'Указана несуществующая категория';
    }

    return null;
}
/**
 * Валидирует числовое значение цены
 *
 * @param mixed $value Значение цены для проверки
 * @return string|null Возвращает строку с ошибкой или null если валидация успешна
 */
function validatePrice($value)
{
    if (!is_numeric($value) || $value <= 0) {
        return 'Начальная цена должна быть выше нуля';
    }
    return null;
}
/**
 * Валидирует значение шага ставки
 *
 * @param mixed $value Значение шага ставки для проверки
 * @return string|null Возвращает строку с ошибкой или null если валидация успешна
 */
function validateStep($value)
{
    if (!ctype_digit($value)) {
        return 'Значение должно быть числовым';
    }
    $intValue = (int) $value;
    if ($intValue <= 0) {
        return 'Ставка должна быть выше нуля';
    }

    return null;
}
/**
 * Валидирует дату окончания
 *
 * @param string $value Строка с датой для проверки
 * @return string|null Возвращает строку с ошибкой или null если валидация успешна
 */
function validateEndDate($value)
{
    if (!is_date_valid($value)) {
        return 'Неверный формат даты';
    }

    $date = date_create($value);
    $cur_date = date_create('today');

    if ($date <= $cur_date) {
        return 'Дата должна быть больше текущей';
    }

    return null;
}
/**
 * Определяет текущую цену лота для аукциона
 *
 * @param mysqli $link Объект подключения к базе данных
 * @param int $lotId ID лота для проверки
 * @return float|int Текущая цена лота (максимальная ставка или стартовая цена)
 */
function getCurrentPrice($link, $lotId)
{
    // Получаем максимальную ставку
    $sql = "SELECT MAX(amount) as max_bid FROM bids WHERE lot_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $bidData = mysqli_fetch_assoc($result);

    // Если есть ставки, возвращаем максимальную, иначе стартовую цену
    if ($bidData && $bidData['max_bid']) {
        return $bidData['max_bid'];
    }
    // Получаем стартовую цену
    $sql = "SELECT start_price FROM lots WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $lotData = mysqli_fetch_assoc($result);

    return $lotData ? $lotData['start_price'] : 0;
}
/**
 * Сохраняет новую ставку в базе данных
 *
 * @param mysqli $link Объект подключения к базе данных
 * @param int $amount Сумма ставки
 * @param int $lotId ID лота на который делается ставка
 * @param int $userId ID пользователя делающего ставку
 * @return bool Возвращает true в случае успешного сохранения, false при ошибке
 */
function saveBid($link, $amount, $lotId, $userId)
{
    $sql = "INSERT INTO bids (amount, lot_id, user_id, created_at) VALUES (?, ?, ?, NOW())";

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'iii', $amount, $lotId, $userId);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}
/**
 * Получает список ставок для указанного лота
 *
 * @param mysqli $link Объект подключения к базе данных
 * @param int $lotId ID лота для которого получаем ставки
 * @return array Массив ставок с информацией о пользователях
 */
function getBids($link, $lotId)
{
    $sql = 'SELECT ' .
        'b.id, ' .
        'b.amount, ' .
        'b.created_at, ' .
        'b.user_id, ' .
        'b.lot_id, ' .
        'u.name as user_name, ' .
        'u.email as user_email ' .
        'FROM bids b ' .
        'JOIN users u ON b.user_id = u.id ' .
        'WHERE b.lot_id = ? ' .
        'ORDER BY b.created_at DESC, b.amount DESC';

    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $bids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bids[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $bids;
}
/**
 * Форматирует дату в относительное время или абсолютную дату
 *
 * @param string $datetime Строка с датой и временем в формате, понятном для strtotime()
 * @return string Отформатированная строка времени
 */
function formatTimeAgo($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return 'только что';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' ' . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . ' назад';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' ' . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' назад';
    } else {
        return date('d.m.Y в H:i', $time);
    }
}
