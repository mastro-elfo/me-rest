<?php

require_once "resources/model.php";

// Define user model
class User extends Model {
  public ?string $type = "user";

  /**
   * Search user by username and password
   * @param  string $username
   * @param  string $password
   * @return ?object          `NULL` if user is not found
   */
  public function login(string $username, string $password): ?object {
    return NULL;
  }
}

?>
