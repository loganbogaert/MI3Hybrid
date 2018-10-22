<?php 
// Start the session
session_start();
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');
// data
$servername = "localhost:3307";
$username = "root";
$password = "logan";
// name of DB
$database = "project";
// user
$user = $_GET["user"] . "_mp";
// Create connection
$conn = new mysqli($servername, $username, $password, $database);
   // create sql script
    $sql = "SELECT Username, Id, Sent FROM $user";
    // execute statement
    $result = $conn->query($sql);
    // create data
    $data = "";
    // check if rows 
    if($result->num_rows > 0) 
    {
    while($row = $result->fetch_assoc()) 
    {
        // check if we can send the message
        if($row["Sent"] == "n")
        {
            // give username
            $data.= $row["Username"] . ";"; 
            // get username
            $client = $row["Username"];
            // set row back to 'y'
            $sql2 = "UPDATE $user SET Sent = 'y' WHERE Username = '$client'";
            // run sql
            $conn->query($sql2);
        }
    }
    if($data != "")
    {
    // delete last ';'
    $data =  substr($data, 0, -1);
    // echo
    echo "data:{$data}\n\n";
    // send to client
    flush(); 
    }
    }

?>
