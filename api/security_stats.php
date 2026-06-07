<?php
/**
 * Real-time Security Stats API
 */
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance();

// Check WAF status
$wafStatus = 'offline';
try {
    $ch = curl_init('http://127.0.0.1:5005/health');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
    $response = curl_exec($ch);
    if ($response !== false) {
        $wafHealth = json_decode($response, true);
        $wafStatus = $wafHealth['status'] ?? 'offline';
    }
    curl_close($ch);
} catch (Exception $e) {
    $wafStatus = 'offline';
}

$stats = [
    'waf_status' => $wafStatus,
    'total_attacks' => $db->query("SELECT COUNT(*) as c FROM security_logs")->fetch()['c'],
    'blocked_today' => $db->query("SELECT COUNT(*) as c FROM security_logs WHERE DATE(created_at) = CURDATE()")->fetch()['c'],
    'avg_confidence' => round($db->query("SELECT AVG(confidence) as avg FROM security_logs")->fetch()['avg'] ?? 0, 2),
    'last_attack' => $db->query("SELECT created_at FROM security_logs ORDER BY created_at DESC LIMIT 1")->fetch()['created_at'] ?? null,
];

echo json_encode($stats);