<?php
session_start();
require '../db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Смена почты</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark h-100">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Смена почты</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Блок вывода сообщений -->
                    <?php if($errorMsg): ?>
                        <div class="alert alert-danger"><?= $errorMsg ?></div>
                    <?php endif; ?>
                    
                    <?php if($successMsg): ?>
                        <div class="alert alert-success"><?= $successMsg ?></div>
                    <?php else: ?>

                    <!-- Сама форма -->
                    <form action="update_profile.php" method="POST">
    <!-- Скрытое поле с секретным кодом -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <input type="mail" name="e-mail" placeholder="Новый е-маил">
                        <button type="submit" class="btn btn-primary w-100">Изменить</button>
                    </form>
                    </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>