<?php
include_once '../config/database.php';
include_once './playerData.php';

$database = new Database();
$conn = $database->getConnection();
$playerData = new PlayerData();

$method = $_SERVER['REQUEST_METHOD'];

if ('POST' === $method) {
  $player_item = $playerData->getPlayerFromPOST($_POST);
  $result = $playerData->put($conn, $player_item);
  if ($result === TRUE) {
    http_response_code(200);
    echo json_encode($player_item);
  } else {
    echo "Error updating record: " . $conn->error;
  }
}


$conn->close();
