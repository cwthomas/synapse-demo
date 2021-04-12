<?php
include_once '../config/database.php';


// manages player items
class PlayerItemsData
{
  public function get($conn, $playerID)
  {
    $sql = "CALL getPlayerItems(" . $playerID . ")";
    $result = $conn->query($sql);

    if ($conn->error) {
      echo $conn->error;
      return;
    }

    $playerItems = array();
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        extract($row);
        $playerItem = array(
          "playerID" => intval($player_id),
          "itemID" => intval($item_id),
          "qty" => intval($qty)
        );
        array_push($playerItems, $playerItem);
      }
      $result->close();
      $conn->next_result();

      return $playerItems;
    }
  }

  public function putItem($conn, $playerItem, $playerID)
  {
    $sql = "CALL upsertPlayerItem (" . $playerID . "," . $playerItem->itemID . "," . $playerItem->qty . ");";
    $result = $conn->query($sql);

    if ($conn->error) {
      echo $conn->error;
      return;
    }

    // close out result set from sproc
    if (!is_bool($result)) {
      $result->close();
      $conn->next_result();
    }
  }

  public function clearItems($conn, $playerID)
  {
    $sqlClear = "CALL clearPlayerItems(" . $playerID . ")";
    $conn->query($sqlClear);

    if ($conn->error) {
      echo $conn->error;
      return;
    }
    $conn->next_result();
  }

  // this is intended when writing out all existing playeritems for a given player
  public function putItems($conn, $playerItems, $playerID)
  {
    // for a case with just a few items it's easiest to just clear the entires and re-write
    // as opposed to determining insert/update/delete
    // for situations with a large number of items, calculating the delta would be more appropriate
    $this->clearItems($conn, $playerID);

    foreach ($playerItems as $playerItem) {
      $this->putItem($conn, $playerItem, $playerID);
    }
  }
}
