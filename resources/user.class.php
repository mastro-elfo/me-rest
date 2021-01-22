<?php

require_once "resources/model.php";

// Define user model
class User extends Model {
  public function __construct() {
    parent::__construct("Users", [
      // Primary key
      "id" => ["INTEGER", "NOT NULL", "PRIMARY KEY", "AUTOINCREMENT", "UNIQUE"],
      // Access control
      "username" => ["TEXT", "UNIQUE"],
      "password" => ["TEXT"],
      // User type ["user", "admin", "super"]
      "type" => ["TEXT"],
      // User info
      "name" => ["TEXT"],
      "surname" => ["TEXT"],
      "email" => ["TEXT", "UNIQUE"],
      "picture" => ["BLOB"]
    ]);
  }
}

?>
