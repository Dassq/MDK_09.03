<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Ошибка безопасности: Неверный CSRF-токен! Запрос отклонен.");
    }
    
    // Только теперь обновляем данные в БД...
}
?>