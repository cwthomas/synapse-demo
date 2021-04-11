<?php

class EnemyData {
    public function getAll($conn) {
      $sql = "SELECT id, type, atk, def, hp, itemdrops, name, xp_reward FROM enemy";
      $result = $conn->query($sql);
      if ($conn->error) {
        echo $conn->error;
        return;
      }
      $enemies = array();
      if ($result->num_rows > 0) {
        // output data of each row
       while( $row = $result->fetch_assoc() ){
        
        $enemy = $this->mapRowToEnemy($row);
        array_push($enemies, $enemy);
      }
        return $enemies;
    }}

    public function getOne($conn, $enemyID) {
      $sql = "SELECT id, type, atk, def, hp, itemdrops, name, xp_reward FROM enemy WHERE id=".$enemyID;
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
 
        return $enemy;
    }}
    
    private function mapRowToEnemy($row) {
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
