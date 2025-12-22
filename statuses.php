
<?php
require_once 'api.php';
$statuses = [];
$date_from_default = date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';
$date_to_default = date('Y-m-d') . ' 23:59:59';
$date_from = isset($_GET['date_from']) && $_GET['date_from'] ? str_replace('T', ' ', $_GET['date_from']) . ':00' : $date_from_default;
$date_to = (isset($_GET['date_to']) && $_GET['date_to'] && strtotime($_GET['date_to'])) ? str_replace('T', ' ', $_GET['date_to']) . ':00' : $date_to_default;
$page = 0;
$limit = 100;
$params = [
    "date_from" => $date_from,
    "date_to" => $date_to,
    "page" => $page,
    "limit" => $limit
];
$token = defined('API_TOKEN') ? API_TOKEN : 'ba67df6a-a17c-476f-8e95-bcdb75ed3958';
$result = getStatuses($params, $token);
if (!empty($result['status']) && $result['status'] === true && !empty($result['data'])) {
    // Если data — строка (как в документации), декодируем, иначе используем как массив
    if (is_string($result['data'])) {
        $statuses = json_decode($result['data'], true);
    } elseif (is_array($result['data'])) {
        $statuses = $result['data'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статусы лидов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php">Добавить лид</a>
        <a href="statuses.php" class="active">Статусы лидов</a>
    </nav>
    <div class="container">
        <h1>Статусы лидов</h1>
        <!-- raw-ответ API (отладка) удалён -->
        <form method="GET" class="filter-form" style="display: flex; gap: 24px; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap;">
            <div style="display: flex; flex-direction: column;">
                <label for="date_from" style="margin-bottom: 6px; color: #ffb86c; font-weight: bold;">Дата с:</label>
                <input type="datetime-local" name="date_from" id="date_from" placeholder="Выберите дату" value="<?= htmlspecialchars(str_replace(' ', 'T', substr($date_from, 0, 16))) ?>" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #444; background: #222; color: #fff; font-size: 1rem;">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="date_to" style="margin-bottom: 6px; color: #ffb86c; font-weight: bold;">Дата по:</label>
                <input type="datetime-local" name="date_to" id="date_to" placeholder="Выберите дату" value="<?= htmlspecialchars(str_replace(' ', 'T', substr($date_to, 0, 16))) ?>" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #444; background: #222; color: #fff; font-size: 1rem;">
            </div>
            <button type="submit" class="btn" style="height: 40px; min-width: 140px; font-size: 1rem;">Фильтровать</button>
        </form>
        <table class="statuses-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>FTD</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($statuses): ?>
                    <?php foreach ($statuses as $lead): ?>
                        <tr>
                            <td><?= htmlspecialchars($lead['id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($lead['email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($lead['status'] ?? '') ?></td>
                            <td><?= htmlspecialchars($lead['ftd'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">Нет данных</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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
                    if (this.href.includes('index.php')) {
                        e.preventDefault();
                        this.classList.add('morphing');
                        document.querySelector('.container').classList.add('page-exit');
                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 600);
                    }
                });
            });
        });
    </script>
</body>
</html>
