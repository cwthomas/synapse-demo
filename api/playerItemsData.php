<?php
include_once '../config/database.php';

class PlayerItemsData {
  public function get($conn) {
    $sql = "SELECT player_id, item_id, qty FROM playeritems;";
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

  public function putItem($conn, $playerItem) {
    extract($playerItem);

    $sql = "INSERT INTO player SET name='".$name."', atk=".$atk.", def=".$def.", hp=".$hp.", xp=".$xp." WHERE id = ".$id;
    $result = $conn->query($sql);
    if ($conn->error) {
      echo $conn->error;
      return;
    }
    return $result;
  }

  public function putItems($conn, $playerItems, $playerID){
    // for a case with just a few items it's easiest to just clear the entires and re-write
    // as opposed to determining insert/update/delete
    // for situations with a large number of items, calculating the delta would be more appropriate
    $sqlClear = "DELETE FROM playeritems WHERE player_id = ".$playerID;
    $conn->query($sqlClear);
    if ($conn->error) {
      echo $conn->error;
      return;
    }
    foreach($playerItems as $playerItem) {
      $this->putItem($conn, $playerItem);
    }
  }


  
  public function getPlayerItemsPOST($postData){
    echo $postData;
    extract($postData);
    // $player_item = array(
    //     "id" => intval($id),
    //     "name" => $name,
    //     "atk" => intval($atk),
    //     "def" => intval($def),
    //     "hp" => intval($hp),
    //     "xp" => intval($xp)
    //   );
    //   return $player_item;
}

}
