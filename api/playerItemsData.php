<?php
include_once '../config/database.php';

class PlayerItemsData {
  public function get($conn, $playerID) {
    $sql = "SELECT player_id, item_id, qty FROM playeritems WHERE player_id =".$playerID;
    $result = $conn->query($sql);
    $playerItems = array();
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
      extract($row);
      $playerItem = array(
        "playerID" => intval($player_id),
        "itemID" => intval($item_id),
        "qty" => intval($qty)
      );
      array_push($playerItems, $playerItem);
    }
      return $playerItems;
  }}

  public function putItem($conn, $playerItem, $playerID) {
    $sql = "CALL UpsertPlayerItem (".$playerID.",".$playerItem->itemID.",".$playerItem->qty.");";
    $result = $conn->query($sql);
    if ($conn->error) {
      echo $conn->error;
      return;
    }
    return $result;
  }

  public function clearItems($conn, $playerID) {
    $sqlClear = "DELETE FROM playeritems WHERE player_id = ".$playerID;
    $conn->query($sqlClear);
    if ($conn->error) {
      echo $conn->error;
      return;
    }
  }

  public function putItems($conn, $playerItems, $playerID){
    // for a case with just a few items it's easiest to just clear the entires and re-write
    // as opposed to determining insert/update/delete
    // for situations with a large number of items, calculating the delta would be more appropriate
    $this->clearItems($conn, $playerID);

    foreach($playerItems as $playerItem) {
      $this->putItem($conn, $playerItem, $playerID);
    }
  }

  public function getItemsFromPost($postData){
    $newItems = array();
    foreach($postData as $oldPlayerItem){
      $playerItem = array(
        "playerID" => intval($oldPlayerItem["playerID"]),
        "itemID" => intval($oldPlayerItem["itemID"]),
        "qty" =>intval($oldPlayerItem["qty"]),
      );
      array_push($newItems, $playerItem);
    }

      return $newItems;
}


  

}
