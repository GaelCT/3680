<?php
//remove the blocks between each side. 
header("Access-Control-Allow-Origin: https://jescalante.cs3680.com");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('')
require_once('config.php');
require_once('loadENV.php');
//list them all out
$servername = $_ENV['server'];
$username   = $_ENV['user'];
$password   = $_ENV['pass']; //have this in the .env file
$dbname     = $_ENV['name'];

function closeConnection($db){
    mysqli_close($db);
}

function getConnection($servername, $username, $password, $dbname){
  $db = mysqli_connect($servername, $username, $password, $dbname);

    try{
        $db; 
        return $db;
    }catch(Exception $ex){
        die($ex->getMessage() . "\n");
    }
}

$conn = getConnection($servername, $username, $password, $dbname);
// echo "Connected successfully";

//every message has am id so we push it down like in a stack in 2020
// and so we re iterate through the messages and push them down to the client.
$newestID = 0;

while(true){

    $query = <<<QUERY
        SELECT * FROM messages WHERE chatroom_id = 1 AND message_id > $newestID
    QUERY;

    $resultThatWillBeSent = mysqli_query($conn, $query);
    //how can i increase the messages by 1?

    if($resultThatWillBeSent > 0){
        while($row = mysqli_fetch_assoc($resultThatWillBeSent)){
            
                //better because the query is getting much more data than what I need
                // so we just finalize what is going to be sent. 
                  $data = [  
                    'user_id' => $row['user_id'],
                    'message_id' => $row['message_id'],
                    'content' => $row['content'],
                    'sent_at' => $row['sent_at'],
                    'chatroom_id' => $row['chatroom_id']
                ];//push the message to the frontend in SSE format
                echo "data: " . json_encode($data) . "\n\n";
                $newestID = $row['message_id'];
        }
        
        ob_flush(); //data from PHP ---> web server
        flush(); // prevent the server to hold and send immediately
    }

    sleep(2); //will only have 30 quieries per min
}