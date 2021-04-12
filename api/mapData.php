<?php
include_once '../config/database.php';

class MapData {
  public function get($conn) {
    $sql = "CALL getMap();";
    $result = $conn->query($sql);
    
    if ($conn->error) {
      echo $conn->error;
      return;
    }

    if ($result->num_rows > 0) {
      // output data of each row
      $row = $result->fetch_all(MYSQLI_ASSOC)[0];
      extract($row);
      $map_item = array(
        "id" => intval($id),
        "width" => intval($width),
        "height" => intval($height),
        "startX" => intval($startX),
        "startY" => intval($startY),
        "enemies" => intval($enemies)
      );

      // close out result set from sproc
      $result->close();
      $conn->next_result();

      return $map_item;
  }}

}
