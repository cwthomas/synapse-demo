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
    
        $sql = "UPDATE player SET name='".$player->name."', atk=".$player->atk.", def=".$player->def.", hp=".$player->hp.", xp=".$player->xp." WHERE id = ".$player->id;
        $result = $conn->query($sql);
        return $result;
    }

  
}
