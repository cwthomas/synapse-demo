<?php
include_once '../config/database.php';

// manages acces to the mapitem entities
class MapItemsData
{
  public function get($conn, $mapID)
  {
    $sql = "CALL getMapItems(" . $mapID . ")";
    $result = $conn->query($sql);
    if ($conn->error) {
      echo $conn->error;
      return;
    }
    $mapItems = array();
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        extract($row);
        $mapItem = array(
          "mapID" => intval($map_id),
          "itemID" => intval($item_id),
          "x" => intval($x),
          "y" => intval($y)
        );
        array_push($mapItems, $mapItem);
      }

      $result->close();
      $conn->next_result();
      
      return $mapItems;
    }
  }
}
