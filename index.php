<?php
$servername = "us-cdbr-east-03.cleardb.co";
$username = "b187614a16d726";
$password = "950b4df7";
//mysql://b187614a16d726:950b4df7@us-cdbr-east-03.cleardb.com/heroku_01356cb77fdb7dd?reconnect=true
// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>

<html>
  <title>Hello World</title>
  <body>Hello World</body>
</html>
