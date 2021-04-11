<?php

class PlayerData {

    public function get($conn, $player_id) {
      $sql = "SELECT id, name, atk, def, hp, xp FROM player WHERE id =".$player_id;
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        // output data of each row
        $row = $result->fetch_assoc();
        extract($row);
        $player_item = array(
          "id" => intval($id),
          "name" => $name,
          "atk" => intval($atk),
          "def" => intval($def),
          "hp" => intval($hp),
          "xp" => intval($xp)
        );
        return $player_item;
    }}

    public function put($conn, $player) {
        extract($player);
        $sql = "UPDATE player SET name='".$name."', atk=".$atk.", def=".$def.", hp=".$hp.", xp=".$xp." WHERE id = ".$id;
        $result = $conn->query($sql);
        return $result;
    }

    public function getPlayerFromPOST($postData){
        extract($postData);
        $player_item = array(
            "id" => intval($id),
            "name" => $name,
            "atk" => intval($atk),
            "def" => intval($def),
            "hp" => intval($hp),
            "xp" => intval($xp)
          );
          return $player_item;
    }
  
}
