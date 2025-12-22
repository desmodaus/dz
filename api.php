<?php
/**
 * API конфігурація
 */
// УВАГА: Замініть на реальний URL з документації!
define('API_BASE_URL', 'DEMO_MODE'); // Поки що DEMO режим
define('API_TOKEN', 'ba67df6a-a17c-476f-8e95-bcdb75ed3958');
define('USE_DEMO_MODE', true); // Встановіть false для використання реального API

/**
 * Виконує HTTP запит до API
 * 
 * @param string $method HTTP метод (GET, POST)
 * @param string $endpoint API endpoint
 * @param array $data Дані для відправки
 * @return array Результат запиту
 */
function makeApiRequest($method, $endpoint, $data = []) {
    // DEMO режим для тестування без реального API
    if (USE_DEMO_MODE) {
        return makeDemoApiRequest($method, $endpoint, $data);
    }
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    
    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
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
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return [
            'success' => true,
            'message' => 'Success',
            'data' => $decodedResponse
        ];
    } else {
        return [
            'success' => false,
            'message' => $decodedResponse['message'] ?? 'API Error: HTTP ' . $httpCode,
            'data' => $decodedResponse
        ];
    }
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
 * Отримує статуси лідів через API метод "getstatuses"
 * 
 * @param string $dateFrom Дата від (формат: Y-m-d)
 * @param string $dateTo Дата до (формат: Y-m-d)
 * @return array Результат запиту
 */
function getStatuses($dateFrom, $dateTo) {
    $params = [
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo
    ];
    
    return makeApiRequest('GET', '/getstatuses', $params);
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
    
    if ($endpoint === '/getstatuses' && $method === 'GET') {
        // Симуляція отримання статусів лідів
        $demoLeads = [];
        $statuses = ['new', 'pending', 'approved', 'rejected', 'contacted'];
        $ftdValues = ['Yes', 'No', 'Pending', 'N/A'];
        
        // Генеруємо тестові дані
        for ($i = 1; $i <= 10; $i++) {
            $demoLeads[] = [
                'id' => 1000 + $i,
                'email' => 'test' . $i . '@example.com',
                'status' => $statuses[array_rand($statuses)],
                'ftd' => $ftdValues[array_rand($ftdValues)],
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
            ];
        }
        
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
