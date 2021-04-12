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
  $map = (object)$mapData->get($conn);
  $player = (object)$playerData->get($conn, 1);

  $mapItems = $mapItemsData->get($conn,$map->id);
  $enemies = $enemyData->getAll($conn);
  $items = $itemData->get($conn);
  $playerItems = $playerItemsData->get($conn, $player->id);
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


// right now only player and player items can be persisted, but would add more if needed
if ('POST' === $method) {
  $json = file_get_contents('php://input');
  $data = json_decode($json);

  $playerID = $data->playerID;

  $resultObject = array();
  if (isset($data->playerItems)) {
    if (count($data->playerItems) > 0) {
      $playerItemsData->putItems($conn, $data->playerItems, $playerID);
    } else {
      $playerItemsData->clearItems($conn, $playerID);
    }
    $resultObject["playerItems"] =  $data->playerItems;
  }

  if (isset($data->player)) {
    $playerData->put($conn, $data->player);
    $resultObject["player"] = $data->player;
  }

  if (!$conn->error) {
    http_response_code(200);

    echo json_encode($resultObject);
  } else {
    echo "Error updating record: " . $conn->error;
  }
}


$conn->close();
