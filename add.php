<?php
/**
 * @var mysqli $link
 */
session_start();

require_once 'init.php';
require_once 'helpers.php';

// Получаем категории из БД
$sql = "SELECT id, title FROM categories";
$result = mysqli_query($link, $sql);

if ($result) {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $categoriesIds = array_column($categories, 'id');
} else {

    $error = mysqli_error($link);
    exit();
}

if (!isset($_SESSION['user'])) {
    http_response_code(403);

    $pageContent = include_template('404.php', [
        'categories' => $categories,
        'error_code' => 403,
        'error_text' => 'Ледник недоверия. Для прохода требуется экипировка авторизованного пользователя'
    ]);

    $layout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'title' => 'Доступ запрещен',
        'categories' => $categories,
    ]);
    print $layout;
    exit;
}

$errors = [];
$form_data = [];

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;
    $requiredFields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-date', 'lot-step'];

    // Правила валидации
    $rules = [
        'lot-rate' => function ($value) {
            return validatePrice($value);
        },
        'lot-step' => function ($value) {
            return validateStep($value);
        },
        'lot-date' => function ($value) {
            return validateEndDate($value);
        },
        'category' => function ($value) use ($categoriesIds) {
            return validateCategory($value, $categoriesIds);
        },
    ];

    // Валидация обязательных полей
    foreach ($requiredFields as $field) {
        if (empty($lot[$field])) {
            $errors[$field] = "Поле обязательно для заполнения";
        }
    }

    // Валидация по правилам
    foreach ($rules as $field => $rule) {
        if (!empty($lot[$field])) {
            $error = $rule($lot[$field]);
            if ($error) {
                $errors[$field] = $error;
            }
        }
    }

    // Валидация файла изображения
    if (empty($_FILES['image']['name'])) {
        $errors['image'] = "Добавьте изображение лота";
    } else {
        $tmpName = $_FILES['image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $permittedFileTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $fileExtensions = [
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        if (!in_array($fileType, $permittedFileTypes)) {
            $errors['image'] = "Допустимы только JPG, PNG изображения";
        }
    }

    // Фильтруем ошибки (убираем пустые)
    $errors = array_filter($errors);

    // Если ошибок нет, сохраняем данные
    if (empty($errors)) {
        // Обработка загрузки изображения
        $fileExtension = $fileExtensions[$fileType];
        $filename = uniqid() . '.' . $fileExtension;
        $filepath = 'uploads/' . $filename;

        // Создаем папку uploads если её нет
        if (!file_exists('uploads')) {
            mkdir('uploads', 0755, true);
        }

        if (move_uploaded_file($tmpName, $filepath)) {
            // Подготавливаем данные для вставки
            $lot_name = mysqli_real_escape_string($link, $lot['lot-name']);
            $category_id = (int) $lot['category'];
            $message = mysqli_real_escape_string($link, $lot['message']);
            $lot_rate = (float) $lot['lot-rate'];
            $lot_step = (float) $lot['lot-step'];
            $lot_date = mysqli_real_escape_string($link, $lot['lot-date']);

            // SQL запрос для добавления лота
            $sql = 'INSERT INTO lots (
    title,
    description,
    created_at,
    image,
    start_price,
    end_date,
    bidding_step,
    author_id,
    category_id
) VALUES (?, ?, NOW(), ?, ?, ?, ?, 1, ?)';

            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                'sssdsdi',
                $lot_name,    // title
                $message,     // description
                $filepath,    // image
                $lot_rate,    // start_price
                $lot_date,    // end_date
                $lot_step,    // bidding_step
                $category_id  // category_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $lot_id = mysqli_insert_id($link);
                header("Location: lot.php?id=" . $lot_id);
                exit();
            } else {
                $errors['database'] = "Ошибка при сохранении лота: " . mysqli_error($link);
            }
        } else {
            $errors['image'] = "Ошибка при загрузке изображения";
        }
    }

    // Сохраняем введенные данные для повторного показа
    $form_data = $lot;
}

// Подготавливаем контент для шаблона
$pageContent = include_template('add-template.php', [
    'categories' => $categories,
    'errors' => $errors,
    'form_data' => $form_data
]);

// Выводим layout
$layout_content = include_template('layout.php', [
    'pageContent' => $pageContent,
    'categories' => $categories,
    'title' => 'Добавление лота',
    'showNavigation' => true,
]);

echo $layout_content;
