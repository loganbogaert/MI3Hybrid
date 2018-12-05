<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');
// user 
$user = $_GET["user"];
$friend = $_GET["friend"];
$user = strtolower($user);
$friend = strtolower($friend);
$user = trim($user);
$friend = trim($friend); 
$tablename = $user . "_mp";
// data
$servername = "localhost:3307";
$username = "root";
$password = "test";
// name of DB
$database = "project";
// Create connection
$conn = new mysqli($servername, $username, $password, $database);
// statement
$sql = "SELECT Message FROM $tablename WHERE Username = '$friend'";
// excecute sql 
$result = $conn->query($sql);
// create var
$data = "";
// als er data is 
if($result->num_rows > 0) 
{    
    $data = "y";
    // statement 
    $sql2 = "DELETE FROM $tablename WHERE Username = '$friend'";
    // run statement
    $conn->query($sql2);   
    // echo
    echo "data:{$data}\n\n";
    // send to client
    flush(); 
}
?>