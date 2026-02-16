<?php
session_start();
require '../db.php';

$sql = "SELECT * FROM tickets"; // Начало запроса
$params = []; // Массив для подстановки данных (защита от SQL-инъекций)
$where_clauses = []; // Сюда будем складывать условия

// 1. Проверяем, есть ли поисковый запрос
if (!empty($_GET['q'])) {
    $where_clauses[] = "subject LIKE ?"; 
    $params[] = "%" . $_GET['q'] . "%"; // % означает "любой текст"
}

// 2. (Опционально) Фильтр по категории, если передан category_id
if (!empty($_GET['category_id'])) {
    $where_clauses[] = "category_id = ?";
    $params[] = $_GET['category_id'];
}

// 4. (Опционально) Фильтрация по минимальной цене
if (!empty($_GET['min_price'])) {
    $where_clauses[] = "price >= ?";
    $params[] = floatval($_GET['min_price']); // Преобразуем цену в число
}

// 5. (Опционально) Фильтрация по максимальной цене
if (!empty($_GET['max_price'])) {
    $where_clauses[] = "price <= ?";
    $params[] = floatval($_GET['max_price']);
}

// Далее продолжаем построение запроса, как было ранее...

// 6. Склеиваем условия, если они есть
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// 4. Добавляем сортировку
$sql .= " ORDER BY id DESC";

// 5. Выполняем
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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
    <span class="navbar-brand mb-0 h1">Биржа фриланс-услуг</span>
    <!-- index.php -->

    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Если вошел -->
            <span class="me-3">Привет!</span>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_panel.php" class="btn btn-outline-danger btn-sm">Админка</a>
                <a href="admin_orders.php" class="btn btn-outline-primary btn-sm">Отклики</a>
                <a href="add_item.php" class="btn btn-success btn-sm">+ Добавить товар</a>
            <?php endif; ?>
            <?php if ($_SESSION['user_role'] === 'client'): ?>
                <a href="profile.php" class="btn btn-outline-danger btn-sm">Профиль</a>
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
    <h2 class="mb-4">Каталог услуг</h2>
<div class="card mb-4 p-3 bg-light">
    
    <form action="index.php" method="GET" class="row g-3">
        <!-- Поле текстового поиска -->
        <div class="col-md-8">
            <input type="text" name="q" class="form-control" 
                   placeholder="Поиск по названию..." 
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>
        
        <!-- Кнопка отправки -->
        
        
        <!-- Форма для ввода минимального и максимального ценового диапазона -->
        <form action="index.php" method="GET">
            <label for="min_price">От:</label>
            <input type="number" name="min_price" placeholder="Минимальная цена" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price'], ENT_QUOTES) : ''; ?>">
        
            <label for="max_price">До:</label>
            <input type="number" name="max_price" placeholder="Максимальная цена" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price'], ENT_QUOTES) : ''; ?>">
        
            <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">Найти</button>
            </div>
        </form>

        
        <!-- Ссылка для сброса фильтров -->
        <div class="col-12 text-end">
            <a href="index.php" class="text-muted text-decoration-none small">Сбросить фильтры</a>
        </div>
    </form>
</div>

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