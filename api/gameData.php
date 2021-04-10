<?php
include_once '../config/database.php';
include_once './playerData.php';
include_once './mapData.php';
include_once './mapItemsData.php';
include_once './enemyData.php';
include_once './itemData.php';
include_once './playerItemsData.php';

$database = new Database();
$conn = $database->getConnection();
$playerData = new PlayerData();
$mapData = new MapData();
$mapItemsData = new MapItemsData();
$playerItemsData = new PlayerItemsData();
$enemyData = new EnemyData();
$itemData = new ItemData();

$method = $_SERVER['REQUEST_METHOD'];

// I am grouping all of these data elements together for efficiency since the game will need all of these items at once and it will 
// result in only one API call 
// This game is simple, so this is only called once at the beginning.  However, if multiple fetches of specific data were needed
// I would modify this to include a parameter specifying which entities are needed.
// For example, if the game was loading a new map, it would probably specify : map, mapItems, mapEnemies

// I like to keep the data "flat" as separate arrays of entities (maps and mapItems) instead of nested ( maps ( items) ) as it makes it much simpler to update, insert delete etc.
// It also keeps things more flexible overall.
if ('GET' === $method) {
  $player = $playerData->get($conn,1);
  $map = $mapData->get($conn);
  $mapItems = $mapItemsData->get($conn);
  $enemies = $enemyData->get($conn);
  $items = $itemData->get($conn);
  $playerItems = $playerItemsData->get($conn);
  $game_data = array(
    "player" => $player,
    "map" => $map,
    "mapItems" => $mapItems,
    "enemies" => $enemies,
    "playerItems" => $playerItems,
    "items" => $items
  );
  http_response_code(200);
  echo json_encode($game_data);
}

// if ('POST' === $method) {
//   $player_item = $playerData->getPlayerFromPOST($_POST);
//   $result = $playerData->put($conn, $player_item);
//   if ($result === TRUE) {
//     http_response_code(200);
//     echo json_encode($player_item);
//   } else {
//     echo "Error updating record: " . $conn->error;
//   }
// }


$conn->close();
