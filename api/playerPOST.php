<?php
include_once '../config/database.php';
include_once './playerData.php';

$database = new Database();
$conn = $database->getConnection();
$playerData = new PlayerData();

$method = $_SERVER['REQUEST_METHOD'];

if ('POST' === $method) {
  $player = $playerData->getPlayerFromPOST($_POST);
  $result = $playerData->put($conn, $player);
  if ($result === TRUE) {
    http_response_code(200);
    echo json_encode($player);
  } else {
    echo "Error updating record: " . $conn->error;
  }
}


$conn->close();
