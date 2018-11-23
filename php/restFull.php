<?php 
//*********************************<RESTFULL API MOBILE & INTERNET 3>***************************
header('Access-Control-Allow-Origin: *');
// server
$servername = "localhost:3307";
// user
$username = "root";
// password
$password = "test";
// name of DB
$database = "project";
// Create connection
$conn = new mysqli($servername, $username, $password,$database);
// get the name of the function to execute 
$func = $_POST["function"];
// execute function
$func($conn);
// database close 
$conn->close();

function test($conn)
{
    $user = $_POST["user"]. "_mp";
    
    // to lower
    $user = strtolower($user);

    $sql = "UPDATE $user SET Sent = 'n' WHERE Username LIKE '%%'";

    $conn->query($sql);

}
//****************<creates an account>************
function create($conn)
{
    // get username
    $user = $_POST["user"];
    // get password
    $password = $_POST["password"];
    // to lower
    $user = strtolower($user);
    // check if username and password are not empty
    if($user != '' && $password != '')
    {
       // create sql statement
       $sql = "INSERT INTO useraccounts (UserName,Password) VALUES('$user','$password')";
       // insert into database
       if($conn->query($sql) == true)
       {
            // create table name
            $tableNametwo = $user . '_mp';
            // create sql statement
            $sql3 = "CREATE TABLE $tableNametwo (Username varchar(255), Message text(300), Id int NOT NULL AUTO_INCREMENT, PRIMARY KEY (ID), Sent varchar(2))";
            // connect
            $conn->query($sql3); 
            // say created successfully
            $obj->answer =  "created successfully";
       }
    }
    // if they are empty 
    else{$obj->answer =  "please fill in a username and password";}
    $json = json_encode($obj);
    echo $json;
}
//****************<login to an account, check if password is correct>************
function login($conn)
{
    // get username
    $user = $_POST["user"];
    // to lower
    $user = strtolower($user);
    // get password
    $password = $_POST["password"];
    // check if password and username have been written
    if($user != "" && $password != "")
    {
        // sql select
        $sql = "SELECT Password, UserId FROM useraccounts WHERE Username = '$user'";
        // launch query
        $result = $conn->query($sql);
        // get row
        $row = $result->fetch_assoc();
        // check if there is a row
        if($row != "")
        {
            // check if password is correct
            if($row["Password"] == $password) 
            {   
                // echo Id
                $obj->answer =  "connected succesfully;" . $row["UserId"];    
            }
              // error message
              else {$obj->answer =  "wrong password";}
         }
         // errormessage
         else{$obj->answer =  "username does not exist";}
     }
     // error message
     else{$obj->answer =  "please fill in a username and password";}
     // encode json
     $json = json_encode($obj);
     // echo json
     echo $json;
}
//****************************<load friend from user>**************************
function loadFriends($conn)
{
    // get username
    $user = $_POST["user"];
    // create sql statement
    $sql = "SELECT FriendWith FROM friends WHERE Friend = '$user'";
    // launch statement
    $result = $conn->query($sql);  
    // output data of each row
    while($row = $result->fetch_assoc()) 
    {
        $var = $row["FriendWith"];
        $obj->HTMLResponse .= "
             <tr>
                    <td id=$var>" . $row["FriendWith"] . "</td>
                    <td><button class=\"btn btn-2\" onclick=send('$var')>Send Message</button></td>
            </tr>
            <tr>
                    <td><br></td>
            </tr>
                ";
        $obj->Response .= $row["FriendWith"] . ";";
    }
    $json = json_encode($obj);

    echo $json;
}
//****************************<search user to make a new friend>**************************
function search($conn)
{
// get name
$name = $_POST["name"] . '%';
// get user
$user = $_POST["user"];
// write sql query 
$sql = "SELECT * FROM useraccounts WHERE UserName LIKE '$name' AND UserName <> '$user'";
//launch query
$result = $conn->query($sql);
$obj->HTMLResponse = "";
// get all data
while($row = $result->fetch_assoc()) 
{
    // create var
    $nametwo = $row["UserName"];
    // create var
    $id = $row["UserId"];
    $obj->HTMLResponse .= "
         <tr>
             <td>$nametwo</td>
             <td><button class = 'btn btn-4' onclick = Add('$nametwo','$id')>Add</button></td>
         </tr>

         <tr>
             <td><br></td>
         </tr>
     ";
}
$json = json_encode($obj);
echo $json;
}
//****************************<add friend to user's list>**************************
function add($conn)
{
    // user var 
    $user = $_POST["user"];
    // friend var
    $name = $_POST["friend"];
    // to lower
    $user = strtolower($user);
    // to lower
    $name = strtolower($name);
    // sql statement
    $sql = "INSERT INTO friends (Friend, FriendWith) VALUES ('$user','$name')";
    // use statement
    $conn->query($sql);
    // sql statement
    $sql2 = "INSERT INTO friends (Friend, FriendWith) VALUES ('$name','$user')";
    // use statement
    $conn->query($sql2);   
}
//****************************<refresh conversation from user>**************************
function refresh($conn)
{
    // friend
    $friend = $_POST["friend"];
    // user
    $user = $_POST["user"];
    // to lower
    $user = strtolower($user);
    // to lower
    $friend = strtolower($friend);
    // tablename
    $tableName = $user ."_mp";
    // sql statement
    $sql = "SELECT * FROM messages WHERE (Receiver = '$friend' AND Sender = '$user') OR (Receiver = '$user' AND Sender = '$friend') ORDER BY rowId";
    // run statement 
    $result = $conn->query($sql);
    // create Array
    $a = array();
    // loop trough values
    while($row = $result->fetch_assoc()) {array_push($a,"<p><span class=\"username\">" . $row["Sender"] .  ": </span>" .  $row["Message"] . '</p>');}
    // create var
    $data = "";
    // fill data
    foreach($a as $value){$data.= $value;}
    $obj->data = $data;
    $json = json_encode($obj);
    // echo 
    echo $json;
}
//****************************<send a message to someone>**************************
function insert($conn)
{
    // user 
    $user = $_POST["user"];
    // friend
    $friend = $_POST["friend"];
    // message
    $message = $_POST["message"];
    // tablename
    $tablename = $friend . "_mp";
    // statement
    $sql = "INSERT INTO messages (Message,Sender,Receiver) VALUES ('$message','$user','$friend')";
    // run statement
    $result = $conn->query($sql);
    // statement
    $sql2 = "INSERT INTO $tablename (Message,Username,Sent) VALUES ('$message','$user','n')";
    // run statement
    $conn->query($sql2); 
}
?>