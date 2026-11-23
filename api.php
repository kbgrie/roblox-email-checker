<?php
header('Content-Type: application/json; charset=utf-8');

// Lấy API Key từ biến môi trường của Render, nếu chưa cài thì dùng key mặc định của bạn
$apiKey = getenv('WSRB_API_KEY') ?: 'ws_live_CATc6t4A288pVHQFTp1W1lYjjWkMRYFl';
$baseUrl = 'https://wsrb.org/api/v1';

$action = $_GET['action'] ?? '';
$endpoint = '';

switch ($action) {
    // 1. Tìm kiếm account theo tên: GET /accounts/search?q=
    case 'search':
        $q = urlencode($_GET['q'] ?? '');
        if (empty($q)) {
            echo json_encode(['error' => 'Vui lòng nhập tên tài khoản cần tìm']);
            exit;
        }
        $endpoint = "/accounts/search?q={$q}";
        break;

    // 2. Lấy chi tiết stats & inventory của 1 account: GET /accounts/{id}/inventory
    case 'inventory':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID tài khoản không hợp lệ']);
            exit;
        }
        $endpoint = "/accounts/{$id}/inventory";
        break;

    // 3. Lọc nhiều account theo game: GET /inventory/{game}/accounts
    case 'game_accounts':
        $game = urlencode($_GET['game'] ?? 'blox-fruits');
        $queryParams = [];
        if (!empty($_GET['status'])) $queryParams['status'] = $_GET['status'];
        if (!empty($_GET['stat']))   $queryParams['stat']   = $_GET['stat'];
        if (!empty($_GET['has']))    $queryParams['has']    = $_GET['has'];
        if (!empty($_GET['sort']))   $queryParams['sort']   = $_GET['sort'];
        
        $queryString = http_build_query($queryParams);
        $endpoint = "/inventory/{$game}/accounts" . ($queryString ? "?{$queryString}" : "");
        break;

    default:
        echo json_encode(['error' => 'Hành động không hợp lệ']);
        exit;
}

// Khởi tạo cURL theo đúng chuẩn docs WSRB
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $baseUrl . $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $apiKey,
        "Accept: application/json",
        "User-Agent: WSRB-Neon-Dashboard/1.0"
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi kết nối cURL: ' . $curlError]);
    exit;
}

http_response_code($httpCode);
echo $response;
