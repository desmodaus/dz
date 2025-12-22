<?php
session_start();
require_once 'api.php';
$message = '';
$error = '';


// Пример использования:
// --- Настоящий токен ---
$token = 'ba67df6a-a17c-476f-8e95-bcdb75ed3958';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (empty($firstName) || empty($lastName) || empty($phone) || empty($email)) {
            $error = 'Все поля обязательны';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Некорректный email';
        } else {
            $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
            $landingUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
            $leadData = [
                "firstName" => $firstName,
                "lastName" => $lastName,
                "phone" => $phone,
                "email" => $email,
                "countryCode" => "GB",
                "box_id" => 28,
                "offer_id" => 5,
                "landingUrl" => $landingUrl,
                "ip" => $ip,
                "password" => "qwerty12",
                "language" => "en",
                "clickId" => "",
                "quizAnswers" => "",
                "custom1" => "",
                "custom2" => "",
                "custom3" => ""
            ];
            // Логируем отправляемые данные и ответ API
            $debug_leadData = $leadData;
            $result = addLead($leadData);
            $debug_apiResult = $result;
            if (!empty($result['success']) && $result['success'] === true) {
                $message = 'Лид успешно отправлен! ID: ' . ($result['data']['id'] ?? 'N/A');
            } else {
                $error = 'Ошибка: ' . ($result['message'] ?? 'Не удалось отправить лид');
            }
        }
    }
            // Closing PHP tag removed to avoid unnecessary output

    ?>
    <!-- Отладочная информация по отправке лида -->
    <details style="margin:16px 0; background:#222; color:#fff; padding:10px; border-radius:8px;">
        <summary style="cursor:pointer; color:#ffb86c;">Показать debug-данные отправки лида</summary>
        <div style="margin-bottom:10px;">
            <strong>Отправленные данные:</strong>
            <pre style="white-space:pre-wrap; color:#fff; background:#222; padding:8px; border-radius:6px; font-size:13px; max-height:300px; overflow:auto;">
<?= htmlspecialchars(print_r($debug_leadData ?? [], true)) ?>
            </pre>
        </div>
        <div>
            <strong>Ответ API:</strong>
            <pre style="white-space:pre-wrap; color:#fff; background:#222; padding:8px; border-radius:6px; font-size:13px; max-height:300px; overflow:auto;">
<?= htmlspecialchars(print_r($debug_apiResult ?? [], true)) ?>
            </pre>
        </div>
    </details>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Добавить лид</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <nav>
            <a href="index.php" class="active">Добавить лид</a>
            <a href="statuses.php">Статусы лидов</a>
        </nav>
        <div class="container">
            <h1>Добавить новый лид</h1>
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="" class="lead-form">
                <div class="form-group">
                    <label for="firstName">Имя *</label>
                    <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Фамилия *</label>
                    <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <button type="submit" class="btn">Отправить</button>
            </form>
        </div>

    
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
