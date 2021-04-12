<?php
include_once '../config/database.php';
include_once './playerItemsData.php';
include_once './enemyData.php';
include_once './playerData.php';
$database = new Database();
$conn = $database->getConnection();
$playerData = new PlayerData();
$playerItemsData = new PlayerItemsData();
$enemyData = new EnemyData();

$method = $_SERVER['REQUEST_METHOD'];

if ('POST' === $method) {
  $json = file_get_contents('php://input');
  $data = json_decode($json);
  $enemyIDs = $data->enemyIDs;
  $playerID = $data->playerID;
  $player = (object)$playerData->get($conn, $playerID);
  $enemy = (object)$enemyData->getOne($conn, $enemyIDs[0]);
  
  $playerItems = $playerItemsData->get($conn, $player->id);

  // add player xp
  $player->xp += $enemy->xpReward;
  $itemDrops = $enemy->itemDrops;
  $countItems = count($itemDrops);
  $itemIndex = rand(0, $countItems - 1);
  $itemToReward = array(
    "playerID" => intval($playerID),
    "itemID" => intval($itemDrops[$itemIndex]->id),
    "qty" => 1
  );

  if (!$playerItems) {
    $playerItems = array();
  }

  array_push($playerItems, $itemToReward);
  
  $playerItemsData->putItem($conn, (object)$itemToReward, $playerID);
  $playerData->put($conn, $player);


  $result = array(
    "player" => $player,
    "playerItems" => $playerItems,
    "rewardedItemID" => intval($itemToReward["itemID"]),
    "rewardedXP" => $enemy->xpReward
  );

  //$playerItemsData->putItems($conn, $playerItems, $playerID);
  if (!$conn->error) {
    http_response_code(200);
    echo json_encode($result);
  } else {
    echo "Error updating record: " . $conn->error;
  }
}




$conn->close();
