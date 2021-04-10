<?php
include_once '../config/database.php';

class MapData {
  public function get($conn) {
    $sql = "SELECT width, height, startX, startY, enemies FROM map";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      $row = $result->fetch_assoc();
      extract($row);
      $map_item = array(
        "width" => intval($width),
        "height" => intval($height),
        "startX" => intval($startX),
        "startY" => intval($startY),
        "enemies" => intval($enemies)
      );
      return $map_item;
  }}

}
