<?php

class ItemData
{
  public function get($conn)
  {
    $sql = "CALL getAllItems()";
    $result = $conn->query($sql);
    
    if ($conn->error) {
      echo $conn->error;
      return;
    }

    $items = array();
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        $item = $this->mapRowToItem($row);
        array_push($items, $item);
      }


      // close out result set from sproc
      $result->close();
      $conn->next_result();

      return $items;
    }
  }



  private function mapRowToItem($row)
  {
    extract($row);
    $enemy = array(
      "id" => intval($id),
      "name" => $description,
      "playerEffect" => json_decode($player_effect),
      "enemyEffect" =>  json_decode($enemy_effect)
    );
    return $enemy;
  }
}
