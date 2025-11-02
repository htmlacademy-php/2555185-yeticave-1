<?php
session_start();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_data = $_POST;
    $requiredFields = ['email', 'password'];

    // Проверка обязательных полей
    foreach ($requiredFields as $field) {
        if (empty(trim($user_data[$field] ?? ''))) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    // Если нет ошибок валидации - проверяем пользователя
    if (empty($errors)) {
        $email = $user_data['email'];

        // Безопасный запрос
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = db_get_prepare_stmt($link, $sql, [$email]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $user = $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : null;

        if ($user) {
            // Проверяем пароль
            if (password_verify($user_data['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                header("Location: /index.php");
                exit();
            } else {
                $errors['password'] = 'Вы ввели неверный пароль';
            }
        } else {
            $errors['email'] = 'Пользователь с таким email не найден';
        }
    }
}

// Подготавливаем контент для шаблона
$pageContent = include_template('login-template.php', [
    'categories' => $categories,
    'errors' => $errors,
    'form_data' => $form_data
]);

// Выводим layout
$layout_content = include_template('layout.php', [
    'pageContent' => $pageContent,
    'categories' => $categories,
    'title' => 'Вход на сайт',
    'showNavigation' => true,
]);

echo $layout_content;
