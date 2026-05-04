<?php
header("Access-Control-Allow-Origin: https://gaelc.cs3680.com");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


require_once('loadENV.php');
//all in here
$servername = $_ENV['server'];
$username   = $_ENV['user'];
$password   = $_ENV['pass']; 
$dbname     = $_ENV['name'];

$json = file_get_contents('php://input');
$data = json_decode($json, true); //yes its associative array 
$user_id = $data['user_id'];
$content = $data['content']; //should rename to become consistent
//should add some regex to prevent dropping tables

//getConnection($servername, $username, $password, $dbname);
$dataBase = mysqli_connect($servername, $username, $password, $dbname);
//figure out the correct query
    $query = <<<QUERY
        INSERT INTO messages (chatroom_id, user_id, content)
        VALUES (1, $user_id, '$content')
    QUERY

//need to add the query to be ran
mysqli_query($dataBase, $query);

    echo json_encode(   //sending back a notice
        [
            "status" => "success",
            "message" => "Message posted successfully."
        ]
    );
    
    //pls get your mail daily because our mail box is being targeted and stolen from.

?>