<?php
echo "<h1>🛡️ AI WAF Test</h1>";

function testWAF($name, $uri, $getData, $postData) {
    echo "<h2>$name</h2>";
    
    $payload = [
        'request_uri' => $uri,
        'request_data' => [
            'get' => (object)$getData,
            'post' => (object)$postData
        ]
    ];
    
    $ch = curl_init('http://127.0.0.1:5000/predict');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    // ✅ منطق الـ PHP يطابق الـ Python
    $isAttack = ($result['is_attack'] ?? false) === true;
    $confidence = $result['confidence'] ?? 0;
    $attackType = $result['attack_type'] ?? 'Unknown';
    
    // ✅ الـ threshold هنا 70% زي الـ Python
    $shouldBlock = $isAttack && $confidence >= 70;
    
    if ($shouldBlock) {
        echo "<p style='color: red; font-size: 20px;'>🚨 BLOCKED: " . htmlspecialchars($attackType) . " ($confidence%)</p>";
    } else {
        echo "<p style='color: green; font-size: 20px;'>✅ ALLOWED: Normal ($confidence%)</p>";
    }
    
    echo "<hr>";
}

// ✅ Normal
testWAF("Normal Login", "/login.php", [], ['username' => 'john_doe', 'password' => 'MySecurePass123']);
testWAF("Normal Search", "/search.php", ['q' => 'computer science'], []);
testWAF("Contact Form", "/contact.php", [], ['name' => 'Ahmed', 'email' => 'ahmed@test.com']);

// 🚨 Attacks
testWAF("SQL Injection", "/auth.php", [], ['username' => "admin' OR '1'='1", 'password' => 'x']);
testWAF("XSS", "/search.php", ['q' => '<script>alert(1)</script>'], []);
testWAF("Path Traversal", "/download.php", ['file' => '../../../etc/passwd'], []);
testWAF("Union SQLi", "/api.php", [], ['id' => '1 UNION SELECT * FROM users--']);

// ✅ Health Check
echo "<h2>Health Check</h2>";
$health = @file_get_contents('http://127.0.0.1:5000/health');
echo "<pre>";
print_r(json_decode($health, true));
echo "</pre>";