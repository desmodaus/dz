<?php
session_start();
require_once 'api.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Валідація
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email)) {
        $error = 'Всі поля обов\'язкові для заповнення';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Невірний формат email';
    } else {
        // Отримуємо реальний IP користувача
        $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        
        // Отримуємо домен
        $landingUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        
        // Підготовка даних для API
        $leadData = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'box_id' => 28,
            'offer_id' => 5,
            'countryCode' => 'GB',
            'language' => 'en',
            'password' => 'qwerty12',
            'ip' => $ip,
            'landingUrl' => $landingUrl
        ];
        
        $result = addLead($leadData);
        
        if ($result['success']) {
            $message = 'Лід успішно створено! ID: ' . ($result['data']['id'] ?? 'N/A');
            // Очищаємо форму
            $_POST = [];
        } else {
            $error = 'Помилка при створенні ліда: ' . $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати Лід</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php" class="active">Додати лід</a>
        <a href="statuses.php">Статуси лідів</a>
    </nav>
    
    <div class="container">
        <h1>Додати новий лід</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="lead-form">
            <div class="form-group">
                <label for="firstName">Ім'я *</label>
                <input type="text" id="firstName" name="firstName" 
                       value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="lastName">Прізвище *</label>
                <input type="text" id="lastName" name="lastName" 
                       value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон *</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            
            <button type="submit" class="btn">Відправити</button>
        </form>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.container').classList.add('page-loaded');
            }, 100);
            
            const links = document.querySelectorAll('nav a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.href.includes('statuses.php')) {
                        e.preventDefault();
                        
                        // Добавляєм morphing ефект до кнопки
                        this.classList.add('morphing');
                        
                        // Зникання контейнера
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
