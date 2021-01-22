<?php

// Routes to manage user access

require_once "resources/user.class.php";
require_once "resources/user.session.php";

// Log user in
Flight::route("POST /api/access/login", function(){
  // file_put_contents("user", "login");
  $model = new User();
  $request = Flight::request();

  $username = $request->data->username;
  $password = hide($request->data->password);

  $data = $model->select([
    "username" => $username,
    "password" => $password
  ]);

  if(!$data || count($data) == 0) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }

  $data = denied_keys($data[0], ["password"]);
  // Set session data
  login($data);
  // file_put_contents('login', json_encode($data));
  Flight::json($data);
});

// Log user out
Flight::route("POST /api/access/logout", function(){
  // Just delete session
  logout();
  Flight::json([]);
});

?>
