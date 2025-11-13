<?php
/**
 * Health Check Endpoint - Verify system status
 */
header('Content-Type: application/json');

require_once '../../app/db.php';

$response = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'database' => 'error'
];

try {
    $db = Database::getInstance();
    $result = $db->query("SELECT 1");
    if ($result) {
        $response['database'] = 'ok';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['error'] = $e->getMessage();
}

http_response_code($response['status'] === 'ok' ? 200 : 500);
echo json_encode($response);
exit;
?>
