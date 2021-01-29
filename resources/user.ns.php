<?php

namespace User {
  function create(array $data): integer
  {
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

  function read(integer $id):  ? object
  {
    // Load user from db
    $user = R::load("user", $id);
    // If not found
    if (!$user || $user->id == 0) {
      return null;
    }
    // Filter denied keys
    $user = denied_keys($user, ["password"]);
    // Return
    return $user;
  }

  function update(integer $id, array $data)
  {
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

  function delete($id)
  {
    $user = R::loadForUpdate("user", $id);
    return R::trash($user);
  }

  function login(string $username, string $password) :  ? array
  {
    return R::findOne("user", "username = ? AND password = ?", [
      "username" => $username,
      "password" => hide($password),
    ]);
  }

  function findAll(string $query, $limit = 10, $offset = 0) : array
  {
    return R::find("user", "username LIKE %?% LIMIT $limit OFFSET $offset", [
      $query, $limit, $offset,
    ]);
  }
}
