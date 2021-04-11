<?php

class ItemData {
    public function get($conn) {
      $sql = "SELECT id, description, player_effect, enemy_effect FROM item";
      $result = $conn->query($sql);
      if ($conn->error) {
        echo $conn->error;
        return;
      }
      $items = array();
      if ($result->num_rows > 0) {
        // output data of each row
       while( $row = $result->fetch_assoc() ){
        extract($row);
        $item = array(
          "id" => intval($id),
          "name" => $description,
          "playerEffect" => json_decode($player_effect),
          "enemyEffect" => json_decode($enemy_effect));
      
        array_push($items, $item);
      }
        return $items;
    }}

    public function getOne($conn) {
      $sql = "SELECT id, description, player_effect, enemy_effect FROM item";
      $result = $conn->query($sql);
      if ($conn->error) {
        echo $conn->error;
        return;
      }
      $items = array();
      if ($result->num_rows > 0) {
        // output data of each row
       while( $row = $result->fetch_assoc() ){
        extract($row);
        $item = array(
          "id" => intval($id),
          "name" => $description,
          "playerEffect" => json_decode($player_effect),
          "enemyEffect" => json_decode($enemy_effect));
      
        array_push($items, $item);
      }
        return $items;
    }}
  
}
