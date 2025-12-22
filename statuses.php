<?php
session_start();
require_once 'api.php';

$dateFrom = $_GET['dateFrom'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['dateTo'] ?? date('Y-m-d');

$leads = [];
$error = '';

// Отримуємо статуси лідів
$result = getStatuses($dateFrom, $dateTo);

if ($result['success']) {
    $leads = $result['data'] ?? [];
} else {
    $error = 'Помилка при отриманні статусів: ' . $result['message'];
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статуси Лідів</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php">Додати лід</a>
        <a href="statuses.php" class="active">Статуси лідів</a>
    </nav>
    
    <div class="container">
        <h1>Статуси лідів</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="GET" action="" class="filter-form">
            <div class="form-group">
                <label for="dateFrom">Дата від</label>
                <input type="date" id="dateFrom" name="dateFrom" 
                       value="<?= htmlspecialchars($dateFrom) ?>">
            </div>
            
            <div class="form-group">
                <label for="dateTo">Дата до</label>
                <input type="date" id="dateTo" name="dateTo" 
                       value="<?= htmlspecialchars($dateTo) ?>">
            </div>
            
            <button type="submit" class="btn">Фільтрувати</button>
        </form>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>FTD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Лідів не знайдено</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td><?= htmlspecialchars($lead['id'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($lead['email'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($lead['status'] ?? 'unknown') ?>">
                                        <?= htmlspecialchars($lead['status'] ?? 'Unknown') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($lead['ftd'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="info">
            <p>Всього лідів: <strong><?= count($leads) ?></strong></p>
            <p>Період: <strong><?= htmlspecialchars($dateFrom) ?></strong> - <strong><?= htmlspecialchars($dateTo) ?></strong></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.container').classList.add('page-loaded');
            }, 100);
            
            const links = document.querySelectorAll('nav a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.href.includes('index.php') || (!this.href.includes('statuses.php') && !this.href.includes('index.php'))) {
                        e.preventDefault();
                        
                        // Добавляєм morphing ефект до кнопки
                        this.classList.add('morphing');
                        
                        // Зникання контейнера
                        document.querySelector('.container').classList.add('page-exit');
                        
                        setTimeout(() => {
                            window.location.href = this.href.includes('index.php') ? this.href : 'index.php';
                        }, 600);
                    }
                });
            });
        });
    </script>
</body>
</html>
