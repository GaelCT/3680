<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://davalos.cs3680.com');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/vendor/autoload.php'; //i added this
require 'config.php';
require 'db.php'; // this

use Firebase\JWT\JWT;

$tokenSecret = JWT_SECRET;

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required!']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit;
    }
//This too
$payload = [
    'iss' => JWT_ISSUER,
    'aud' => JWT_AUDIENCE,
    'iat' => time(),
    'exp' => time() + JWT_EXPIRATION_SECONDS,
    'user_id' => $user['user_id'],
    'username' => $user['username'],
    'email' => $user['email']
];

    // Added this which generates JWT
    $token = JWT::encode($payload, $tokenSecret, 'HS256');

    echo json_encode([
        'message' => 'Login successful',
        'username' => $user['username'],
        'userId' => $user['user_id'],
        'token' => $token
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?> 
~~~
singup.php

<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://davalos.cs3680.com'); // i changed http://localhost:5173
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$firstName = $data['firstName'] ?? '';
$lastName = $data['lastName'] ?? '';
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$firstName || !$lastName || !$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare(
        'INSERT INTO users (first_name, last_name, username, email, password_hash)
        VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$firstName, $lastName, $username, $email, $passwordHash]);

    http_response_code(201);
    echo json_encode(['message' => 'User created']);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        echo json_encode(['error' => 'Username or email already taken']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
    }
}

?>
getUsers.php


<?php 

header('Content-Type: application/json');
header('Acess-Control-Allow-Origin: http://davalos.cs3680.com');
header('Access-Control-Allow-headers: Content-Type, Authorization');

require 'db.php';

try {
    $stmt = $pdo->prepare('SELECT user_id, username FROM users');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server errror']);
}


?>