<?php
require_once 'init.php';
require_once 'helpers.php';

// Получаем категории из БД
$sql = "SELECT id, title FROM categories";
$result = mysqli_query($link, $sql);

if ($result) {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = mysqli_error($link);
    exit();
}

$errors = [];
$form_data = $_POST ?? [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;
    $requiredFields = ['email', 'password', 'name', 'message'];
    $errors = [];

    // Проверка обязательных полей
    foreach ($requiredFields as $field) {
        if (empty(trim($user[$field] ?? ''))) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    // Проверка email только если нет ошибок в обязательных полях
    if (empty($errors)) {
        $email = $user['email'];

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный e-mail';
        } else {
            // Проверка уникальности email
            $sql = 'SELECT id FROM users WHERE email = ?';
            $stmt = db_get_prepare_stmt($link, $sql, [$email]);
            mysqli_stmt_execute($stmt);
            $resultCheckEmail = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($resultCheckEmail) > 0) {
                $errors['email'] = 'Пользователь с таким email уже существует';
            }
        }

        // Если ошибок нет - регистрируем пользователя
        if (empty($errors)) {
            $password = password_hash($user['password'], PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (registration_date, email, name, password, contact_info) VALUES (NOW(), ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [
                $user['email'],
                $user['name'],
                $password,
                $user['message']
            ]);

            $insertRes = mysqli_stmt_execute($stmt);

            if ($insertRes) {
                header('Location: login.php');
                exit();
            } else {
                $errors['general'] = 'Ошибка регистрации: ' . mysqli_error($link);
            }
        }
    }
}

// Подготавливаем контент для шаблона
$pageContent = include_template('sign-up-template.php', [
    'categories' => $categories,
    'errors' => $errors,
    'form_data' => $form_data
]);

// Выводим layout
$layout_content = include_template('layout.php', [
    'pageContent' => $pageContent,
    'categories' => $categories,
    'title' => 'Регистрация'
]);

echo $layout_content;
?>
