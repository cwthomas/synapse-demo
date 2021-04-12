<?php

// Manages Enemy entities in the database.
class EnemyData
{

  // Get All Enemies
  public function getAll($conn)
  {
    $sql = "CALL getAllEnemies()";
    $result = $conn->query($sql);
    
    if ($conn->error) {
      echo $conn->error;
      return;
    }

    $enemies = array();
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {

        $enemy = $this->mapRowToEnemy($row);
        array_push($enemies, $enemy);
      }

      // close out the result set from the sproc
      $result->close();
      $conn->next_result();

      return $enemies;
    }
  }

  // Get One Enemy by EnemyID
  public function getOne($conn, $enemyID)
  {
    $sql = "CALL getEnemy(" . $enemyID. ")";
    $result = $conn->query($sql);
    if ($conn->error) {
      echo $conn->error;
      return;
    }
    $enemies = array();
    if ($result->num_rows > 0) {
      // output data of each row
      $row = $result->fetch_assoc();

      $enemy = $this->mapRowToEnemy($row);

      // close out result set from sproc
      $result->close();
      $conn->next_result();

      return $enemy;
    }
  }

  private function mapRowToEnemy($row)
  {
    extract($row);
    $enemy = array(
      "id" => intval($id),
      "type" => $type,
      "atk" => intval($atk),
      "def" => intval($def),
      "hp" => intval($hp),
      "name" => $name,
      "itemDrops" => json_decode($itemdrops),
      "xpReward" => intval($xp_reward),
    );
    return $enemy;
  }
}
