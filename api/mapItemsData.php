<?php
include_once '../config/database.php';

class MapItemsData {
  public function get($conn) {
    $sql = "SELECT map_id, item_id, x, y FROM mapitems;";
    $result = $conn->query($sql);
    $mapItems = array();
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
      extract($row);
      $mapItem = array(
        "mapID" => intval($map_id),
        "itemID" => intval($item_id),
        "x" => intval($x),
        "y" => intval($y)
      );
      array_push($mapItems, $mapItem);
    }
      return $mapItems;
  }}

}
