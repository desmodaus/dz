<?php
/**
 * API конфігурація
 */
// УВАГА: Замініть на реальний URL з документації!
define('API_BASE_URL', 'https://crm.belmar.pro/api/v1'); // Реальный URL из документации
define('API_TOKEN', 'ba67df6a-a17c-476f-8e95-bcdb75ed3958');
define('USE_DEMO_MODE', false); // Использовать реальный API

/**
 * Виконує HTTP запит до API
 * 
 * @param string $method HTTP метод (GET, POST)
 * @param string $endpoint API endpoint
 * @param array $data Дані для відправки
 * @return array Результат запиту
 */
function makeApiRequest($method, $endpoint, $data = [], $token = null) {
    // DEMO режим для тестирования без реального API
    if (USE_DEMO_MODE) {
        return makeDemoApiRequest($method, $endpoint, $data);
    }
    $url = API_BASE_URL . $endpoint;

    $ch = curl_init();

    $headers = [
        'token: ' . ($token ?? API_TOKEN),
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_CAINFO, 'C:/php/extras/ssl/cacert.pem');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'GET' && !empty($data)) {
        $url .= '?' . http_build_query($data);
        curl_setopt($ch, CURLOPT_URL, $url);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    if ($error) {
        return [
            'success' => false,
            'message' => 'CURL Error: ' . $error,
            'data' => null
        ];
    }

    $decodedResponse = json_decode($response, true);

    return $decodedResponse;
}

/**
 * Додає новий лід через API метод "addlead"
 * 
 * @param array $leadData Дані ліда
 * @return array Результат запиту
 */
function addLead($leadData) {
    return makeApiRequest('POST', '/addlead', $leadData);
}

/**
 * Получает статусы лидов через API метод "getstatuses"
 * @param array $params Массив параметров фильтра
 * @param string|null $token Токен (опционально)
 * @return array Результат запроса
 */
function getStatuses($params, $token = null) {
    return makeApiRequest('POST', '/getstatuses', $params, $token);
}

/**
 * DEMO функція для симуляції API відповідей (для тестування без реального API)
 */
function makeDemoApiRequest($method, $endpoint, $data = []) {
    // Симуляція затримки мережі
    usleep(200000); // 0.2 секунди
    
    if ($endpoint === '/addlead' && $method === 'POST') {
        // Симуляція успішного створення ліда
        return [
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => [
                'id' => rand(1000, 9999),
                'email' => $data['email'] ?? '',
                'status' => 'new',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    if ($endpoint === '/getstatuses' && ($method === 'GET' || $method === 'POST')) {
        // Симуляция получения статусов лидов с заполненными статусами и ftd
        $demoLeads = [
            [
                'id' => 1001,
                'email' => 'lead1@example.com',
                'status' => 'new',
                'ftd' => 'Yes',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 days'))
            ],
            [
                'id' => 1002,
                'email' => 'lead2@example.com',
                'status' => 'approved',
                'ftd' => 'No',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'id' => 1003,
                'email' => 'lead3@example.com',
                'status' => 'pending',
                'ftd' => 'Pending',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'id' => 1004,
                'email' => 'lead4@example.com',
                'status' => 'rejected',
                'ftd' => 'N/A',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'id' => 1005,
                'email' => 'lead5@example.com',
                'status' => 'contacted',
                'ftd' => 'Yes',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ]
        ];
        return [
            'success' => true,
            'message' => 'Statuses retrieved successfully',
            'data' => $demoLeads
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Unknown endpoint or method',
        'data' => null
    ];
}
