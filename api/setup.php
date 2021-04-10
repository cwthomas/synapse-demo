<?php
include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();
$setup = "CALL gameDataSetup";
$result = $conn->query($setup);
if ($conn->error) {
  echo $conn-> error;
} else {
  http_response_code(200);
  echo "Setup Complete";

}
