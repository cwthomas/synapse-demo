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


// This endpoint is the one bit of game logic that is in PHP
// It takes as it's input the type of the enemy defeated and then
// returns player rewards (while first storing those rewards)
if ('POST' === $method) {
  $json = file_get_contents('php://input');
  $data = json_decode($json);

  // a future expansion of this was to send multiple enemies (such as when a bomb goes off)
  // and handle them all at one time instead of making multiple calls.
  $enemyIDs = $data->enemyIDs;

  $playerID = $data->playerID;
  $player = (object)$playerData->get($conn, $playerID);
  // get the enemy that the player just defeated
  $enemy = (object)$enemyData->getOne($conn, $enemyIDs[0]);
  // we are going to return the full set of player items 
  // for the client to replace
  // I could also send just the added items.
  // it was a tossup as to which was simpler.
  $playerItems = $playerItemsData->get($conn, $player->id);
  // give the player the XP for the enemy
  $player->xp += $enemy->xpReward;
  
  // now determine the item drop from the enemy
  $itemDrops = $enemy->itemDrops;
  $countItems = count($itemDrops);

  // for this we're going to keep it very simple and just give a random item, all being equally possible.
  // the data exists to weight each one and adjust the probabilities.
  //
  // A future expansion might be to include a table of drop probabilities with the enemy and item id as keys
  // This would allow game designers to have a tool that easily allows them to adjust drop probabilities
  $itemIndex = rand(0, $countItems - 1);

  $itemToReward = array(
    "playerID" => intval($playerID),
    "itemID" => intval($itemDrops[$itemIndex]->id),
    "qty" => 1
  );

  // the player might not have had any items to begin with
  if (!$playerItems) {
    $playerItems = array();
  }

  // add this item to the items returned to the player 
  // representing the complete inventory
  array_push($playerItems, $itemToReward);
  
  // store this new item in the DB
  $playerItemsData->putItem($conn, (object)$itemToReward, $playerID);

  // save off the player with increased XP
  $playerData->put($conn, $player);

  $result = array(
    "player" => $player,
    "playerItems" => $playerItems,
    "rewardedItemID" => intval($itemToReward["itemID"]),
    "rewardedXP" => $enemy->xpReward
  );

  if (!$conn->error) {
    http_response_code(200);
    echo json_encode($result);
  } else {
    echo "Error updating record: " . $conn->error;
  }
}




$conn->close();
