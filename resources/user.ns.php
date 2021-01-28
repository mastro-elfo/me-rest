<?php

namespace User {
  function login(string $username, string $password): ?array {
    return NULL;
    // return [
    //   "id" => 1,
    //   "username" => "mastro-elfo",
    //   "password" => "xxx",
    //   "name"=> "Francesco"
    // ];
  }

  function create(array $data): integer {
    // Filter allowed keys
    $keys = ["password", "username"];
    $data = allowed_keys($data, $keys);
    // Map data
    $maps = ["password" => "hide"];
    $data = apply_maps($data, $maps);
    // Create record
    $user = R::dispense("user");
    // Merge data into user
    array_like_merge($user, $data);
    // Save
    return R::store($user);
  }

  function read(integer $id): ?object {
    // Load user from db
    $user = R::load("user", $id);
    // If not found
    if(!$user || $user->id == 0) {
      return NULL;
    }
    // Filter denied keys
    $user = denied_keys($user, ["password"]);
    // Return
    return $user;
  }

  function update($id, $data) {
    // Filter allowed keys
    $keys = ["password", "username"];
    $data = allowed_keys($data, $keys);
    // Map data
    $maps = ["password" => "hide"];
    $data = apply_maps($data, $maps);
    // Load for update
    $user = R::loadForUpdate("user", $id);
    // Merge data into user
    array_like_merge($user, $data);
    // Save
    return R::store($user);
  }

  function delete($id) {

  }
}

?>
