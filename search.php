<?php
require '../db.php';

try {
    // Создаем соединение с базой данных
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Забираем переменные из GET-запроса
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : NULL;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : NULL;

    // Формируем SQL-запрос с условиями
    $where_conditions = [];
    $params = [];

    if ($min_price !== NULL) {
        $where_conditions[] = "price >= :min_price";
        $params[':min_price'] = $min_price;
    }

    if ($max_price !== NULL) {
        $where_conditions[] = "price <= :max_price";
        $params[':max_price'] = $max_price;
    }

    // Основной запрос
    $sql = "SELECT * FROM tickets"; // products - ваша таблица товаров

    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }

    // Выполняем запрос
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Получаем результаты
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Выводим товары
    foreach ($results as $item) {
        echo "<div>";
        echo "<strong>" . htmlspecialchars($item['subject'], ENT_QUOTES) . "</strong><br />";
        echo "Цена: " . number_format($item['price'], 2) . " ₽<br />";
        echo "</div>";
    }

} catch (PDOException $e) {
    die("Ошибка соединения с базой данных: " . $e->getMessage());
}
?>
