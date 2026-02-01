<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// 1. Подключаем БД и проверку на админа
require '../db.php';
require 'check_admin.php'; // Эту страницу видит только админ!

$message = '';

// 2. Если нажата кнопка "Сохранить"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $price = $_POST['price'];
    $priority  = trim($_POST['priority']);
    $status   = trim($_POST['status']);

    if (empty($subject) || empty($price)) {
        $message = '<div class="alert alert-danger">Заполните название и цену!</div>';
    } else {
        // 3. Сохраняем в Базу Данных
        $sql = "INSERT INTO tickets (subject, message, price, priority, status) VALUES (:u, :m, :p, :r, :s)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([
                ':u' => $subject,
                ':m' => $message,
                ':p' => $price,
                ':r' => $priority,
                ':s' => $status
            ]);
            $message = '<div class="alert alert-success">Товар успешно добавлен!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Ошибка БД: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить пост</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Добавление нового товара</h1>
        <a href="index.php" class="btn btn-secondary mb-3">← На главную</a>
        
        <?= $message ?>

        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label>Название услуги:</label>
                <input type="text" name="subject" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label>Описание услуги:</label>
                <input type="text" name="message" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label>Цена (руб):</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
                <label>Приоритет услуги:</label>
                <select name="priority" id="priority">
                  <option value="low">low</option>
                  <option value="medium">medium</option>
                  <option value="high">high</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Статус услуги:</label>
                <select name="status" id="status">
                  <option value="new">new</option>
                  <option value="in_progess">in_progess</option>
                  <option value="closed">closed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Сохранить в БД</button>
        </form>
    </div>
</body>
</html>