<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');
// user 
$user = $_GET["user"];
$friend = $_GET["friend"];
$tablename = $user . "_mp";
// data
$servername = "localhost:3307";
$username = "root";
$password = "logan";
// name of DB
$database = "project";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);
    // statement
    $sql = "SELECT Message FROM $tablename WHERE Username = '$friend'";
    // run statement
    $result = $conn->query($sql);
    // create var
    $data = "";
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