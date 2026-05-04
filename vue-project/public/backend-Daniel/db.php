<?php
$host = 'db-mysql-dro-do-user-34384569-0.d.db.ondigitalocean.com';
$port = '25060';
$db = 'droDB';
$user = 'doadmin';
$pass = 'AVNS_iuke3Eh4WDc2okTPO7F';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>