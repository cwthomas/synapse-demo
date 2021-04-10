<?php
class Database {
private $servername = "us-cdbr-east-03.cleardb.com";
private $username = "b187614a16d726";
private $password = "950b4df7";
private $schema = "heroku_01356cb77fdb7dd";

public $conn;
//mysql://b187614a16d726:950b4df7@us-cdbr-east-03.cleardb.com/heroku_01356cb77fdb7dd?reconnect=true
// Create connection

public function getConnection() {
    $this->conn = null;
    $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->schema);

    // Check connection
    // if ($this$conn->connect_error) {
    //  die("Connection failed: " . $conn->connect_error);
    //}
    return $this->conn;
}
}
?>
