<?php

class PlayerData {
    public function get($conn, $player_id) {
    
      $sql = "CALL getPlayer(".$player_id.");";
      $result = $conn->query($sql);
      
      if ($conn->error) {
        echo $conn->error;
        return;
      }

      if ($result->num_rows > 0) {
        // output data of each row
        $row = $result->fetch_all(MYSQLI_ASSOC)[0];
        extract($row);
        $player_item = array(
          "id" => intval($id),
          "name" => $name,
          "atk" => intval($atk),
          "def" => intval($def),
          "hp" => intval($hp),
          "xp" => intval($xp)
        );

        $result->close();
        $conn->next_result();
        
        return $player_item;
    }}

    public function put($conn, $player) {
    
        $sql = "UPDATE player SET name='".$player->name."', atk=".$player->atk.", def=".$player->def.", hp=".$player->hp.", xp=".$player->xp." WHERE id = ".$player->id;
        $result = $conn->query($sql);
        return $result;
    }

  
}
