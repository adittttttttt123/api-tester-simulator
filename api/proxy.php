<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'] ?? '';
    $method = $_POST['method'] ?? 'GET';
    $headers = [];

    if (empty($url)) {
        echo json_encode(['error' => 'URL is required']);
        exit;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Add header to get response time accurately from curl if we wanted, but we'll measure in JS or PHP.
    // PHP measurement:
    $start_time = microtime(true);
    
    $response = curl_exec($ch);
    
    $end_time = microtime(true);
    $response_time = ($end_time - $start_time) * 1000; // in milliseconds

    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    curl_close($ch);

    if ($response === false) {
        echo json_encode([
            'success' => false,
            'status_code' => 0,
            'response_time' => round($response_time),
            'data' => 'cURL Error: ' . $curl_error
        ]);
    } else {
        echo json_encode([
            'success' => ($status_code >= 200 && $status_code < 300),
            'status_code' => $status_code,
            'response_time' => round($response_time),
            'data' => $response
        ]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
