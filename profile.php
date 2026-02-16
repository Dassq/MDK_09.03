<?php
// 1. Начинаем сессию и подключаемся к базе
session_start();
require '../db.php';

// 2. Проверка доступа: Если не вошел — отправляем на вход
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT avatar_url FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_avatar = $stmt->fetchColumn();

// 3. БЕЗОПАСНЫЙ ЗАПРОС (Anti-IDOR)
// Мы выбираем только те заказы, где user_id совпадает с текущим пользователем.
// Используем JOIN, чтобы получить название товара и цену из таблицы products.
$sql = "
    SELECT 
        otklik.id as otklik_id, 
        otklik.created_at, 
        otklik.status, 
        tickets.subject, 
        tickets.price,
        tickets.message
    FROM otklik 
    JOIN tickets ON otklik.tickets_id = tickets.id 
    WHERE otklik.user_id = ? 
    ORDER BY otklik.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$my_orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <!-- Подключаем Bootstrap для красоты -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Биржа фриланс-услуг</a>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Вы вошли как: <b><?= htmlspecialchars($_SESSION['user_role'] ?? 'User') ?></b>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Выйти</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="d-flex flex-column align-items-center">
                        <img src="<? htmlspecialchars($user_avatar) ?>" class="rounded-circle border-2 border-dark mt-4" style="object-fit: cover; height: 100px; width: 100px;">
                        <h3 class="mb-0">Мой профиль</h3>
                    </div>
                </div>
            </div>
           
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="mb-0">Мои заказы</h2>
                    </div>
                    <div class="card-body">
                        
                        <!-- Проверка: Есть ли заказы вообще? -->
                        <?php if (count($my_orders) > 0): ?>
                            
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>№ Отклика</th>
                                            <th>Дата</th>
                                            <th>Услуга</th>
                                            <th>Цена</th>
                                            <th>Статус</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($my_orders as $order): ?>
                                            <tr>
                                                <!-- ID заказа -->
                                                <td>#<?= $order['otklik_id'] ?></td>
                                                
                                                <!-- Дата (форматируем красиво) -->
                                                <td>
                                                    <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                                </td>
                                                
                                                <!-- Название товара (защита от XSS) -->
                                                <td>
                                                    <strong><?= htmlspecialchars($order['subject']) ?></strong>
                                                </td>
                                                
                                                <!-- Цена -->
                                                <td><?= number_format($order['price'], 0, '', ' ') ?> ₽</td>
                                                
                                                <!-- Статус с цветным бейджиком -->
                                                <td>
                                                    <?php 
                                                    // Логика цвета для статуса
                                                    $status_color = 'secondary';
                                                    if ($order['status'] == 'new') $status_color = 'primary';
                                                    if ($order['status'] == 'processing') $status_color = 'warning';
                                                    if ($order['status'] == 'done') $status_color = 'success';
                                                    ?>
                                                    <span class="badge bg-<?= $status_color ?>">
                                                        <?= htmlspecialchars($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>
                            <!-- Если заказов нет -->
                            <div class="text-center py-5">
                                <h4 class="text-muted">Вы еще ничего не заказывали.</h4>
                                <a href="index.php" class="btn btn-primary mt-3">Перейти в каталог</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
             <!-- profile.php или add_item.php -->
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="col-md-3">
                    <label class="form-label">Выберите изображение:</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Загрузить</button>
            </form>
        </div>
    </div>

</body>
</html>