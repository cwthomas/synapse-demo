<?php
require('../vendor/autoload.php');
$servername = "us-cdbr-east-03.cleardb.com";
$username = "b187614a16d726";
$password = "950b4df7";
//mysql://b187614a16d726:950b4df7@us-cdbr-east-03.cleardb.com/heroku_01356cb77fdb7dd?reconnect=true
// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id, name, str, dex FROM player";
$result = $conn->query($sql);


?>

<html>
  <title>Hello Worldx</title>
  <body><?php 
    if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "Name: " . $row["name"]. " - Str: " . $row["str"]. " - Dex: " . $row["dex"]. "<br>";
  }
} else {
  echo "0 results";
}
$conn->close();
    
    ?></body>
</html>
