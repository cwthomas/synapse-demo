<?php

// manages the player entity
class PlayerData
{
  public function get($conn, $player_id)
  {

    $sql = "CALL getPlayer(" . $player_id . ");";
    $result = $conn->query($sql);

    if ($conn->error) {
      echo $conn->error;
      return;
    }

    if ($result->num_rows > 0) {
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

      if (!is_bool($result)) {
        $result->close();
        $conn->next_result();
      }

      return $player_item;
    }
  }

  public function put($conn, $player)
  {
    // could probably bind the parameters to a call object but for this exercise the string method was quicker
    $sql = "CALL updatePlayer(" . $player->id . ",'" . $player->name . "', " . $player->atk . ", " . $player->def . ", " . $player->hp . ", " . $player->xp . " ); ";
    $result = $conn->query($sql);

    if (!is_bool($result)) {
      $result->close();
      $conn->next_result();
    }

    return $result;
  }
}
