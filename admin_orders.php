<?php
require 'check_admin.php'; // Только админ!
require '../db.php';

// САМОЕ СЛОЖНОЕ: Объединяем 3 таблицы в одном запросе
// orders (главная) + users (чтобы взять email) + products (чтобы взять название)
$sql = "
    SELECT 
        otklik.id as otklik_id,
        otklik.created_at,
        users.email,
        tickets.subject,
        tickets.price
    FROM otklik
    JOIN users ON otklik.user_id = users.id
    JOIN tickets ON otklik.tickets_id = tickets.id
    ORDER BY otklik.id DESC
";

$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Управление заказами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Все заказы</h1>
        
        <a href="index.php" class="btn btn-outline-danger btn-sm">Главная</a>
        <a href="admin_orders.php" class="btn btn-outline-primary btn-sm">Отклики</a>
        <a href="add_item.php" class="btn btn-success btn-sm">+ Добавить товар</a>
        <a href="logout.php" class="btn btn-danger">Выйти</a>
    </div>
    
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID Заказа</th>
                <th>Дата</th>
                <th>Клиент (Email)</th>
                <th>Товар</th>
                <th>Цена</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['otklik_id'] ?></td>
                <td><?= $order['created_at'] ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['subject']) ?></td>
                <td><?= $order['price'] ?> ₽</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>