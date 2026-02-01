<?php
session_start();
require '../db.php';

// 1. Получаем все товары из базы
// ORDER BY id DESC означает "сначала новые"
$stmt = $pdo->query("SELECT * FROM tickets ORDER BY id DESC");
$tickets = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Навигация -->
<nav class="navbar navbar-light bg-light px-4 mb-4 shadow-sm">
    <span class="navbar-brand mb-0 h1">Мой Магазин</span>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Если вошел -->
            <span class="me-3">Привет!</span>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_panel.php" class="btn btn-outline-danger btn-sm">Админка</a>
                <a href="admin_orders.php" class="btn btn-outline-primary btn-sm">Отклики</a>
                <a href="add_item.php" class="btn btn-success btn-sm">+ Добавить товар</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-dark btn-sm">Выйти</a>
        <?php else: ?>
            <!-- Если гость -->
            <a href="login.php" class="btn btn-primary btn-sm">Войти</a>
            <a href="register.php" class="btn btn-outline-primary btn-sm">Регистрация</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Каталог товаров</h2>
    
    <div class="row">
        <?php foreach ($tickets as $ticket): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <!-- Если картинки нет, ставим заглушку -->
                    <h5 class="card-title"><?= htmlspecialchars($ticket['subject']) ?></h5>
                    
                    <div class="card-body">
                        <p class="card-text text-truncate"><?= htmlspecialchars($ticket['message']) ?></p>
                        <p class="card-text fw-bold text-primary"><?= h($ticket['price']) ?> ₽</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="make_order.php?id=<?= $ticket['id'] ?>" class="btn btn-primary">Откликнуться</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (count($tickets) === 0): ?>
            <p class="text-muted">Товаров пока нет. Зайдите под админом и добавьте их.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>