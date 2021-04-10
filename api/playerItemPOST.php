<?php
include_once '../config/database.php';
include_once './playerItemsData.php';
$database = new Database();
$conn = $database->getConnection();

$playerItemsData = new PlayerItemsData();


$method = $_SERVER['REQUEST_METHOD'];

if ('POST' === $method) {
  $playerItems =$_POST["playerItems"];
  $playerID = $_POST["playerID"];
  $playerItemsData->putItems($conn, $playerItems, $playerID);
  if (!$conn->error) {
    http_response_code(200);
    echo json_encode($playerItems);
  } else {
    echo "Error updating record: " . $conn->error;
  }
}




$conn->close();
