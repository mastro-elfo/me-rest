<?php

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

function is_logged() {
  return array_key_exists("user", $_SESSION) && !!$_SESSION["user"];
}

function is_admin() {
  return is_logged() && (
    $_SESSION["user"]["type"] == "admin"
    || $_SESSION["user"]["type"] == "super");
}

function is_super() {
  return is_logged() && $_SESSION["user"]["type"] == "super";
}

Flight::route("POST /api/user/check", function(){
  $model = new User();
  $request = Flight::request();

  $username = $request->data->username;
  $email = $request->data->email;

  $data = $model->select([
    "or" => [
      "username" => $username,
      "email" => $email
    ]
  ]);

  Flight::json([
    // `true` if ok, `false` otherwise
    "username" => !array_any($data, function($item) use($username) {
      return $item["username"] == $username;
    }),
    // `true` if ok, `false` otherwise
    "email" => !array_any($data, function($item) use($email) {
      return $item["email"] == $email;
    }),
  ]);
});

// Create new user
Flight::route("POST /api/user", function(){
  // To create a new user I need a user is connected and is admin
  if(!is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }

  $model = new User();
  $request = Flight::request();

  $username = $request->data->username;
  $password = hide($request->data["password"]);
  $name = $request->data->name;
  $surname = $request->data->surname;
  $email = $request->data->email;
  $picture = $request->data->picture;

  $id = $model->create([
    "username" => $username,
    "password" => $password,
    "type" => "user",
    "name" => $name,
    "surname" => $surname,
    "email" => $email,
    "picture" => $picture
  ]);

  if(!$id) {
    // Probably username or email duplicate
    return Flight::stop(BAD_REQUEST);
  }

  Flight::json(["id" => $id]);
});

// Get logged user
Flight::route("GET /api/user", function(){
  if(!is_logged()) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }
  Flight::json($_SESSION["user"]);
});

// List all users
Flight::route("GET /api/users", function(){
  if(!is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }

  $model = new User();
  $request = Flight::request();
  $query = $request->query->getData();
  // file_put_contents('users', json_encode($query));

  $where = [];
  if(array_key_exists("query", $query)) {
    $where["OR"] = [
      "username[~]" => $query["query"],
      "name[~]" => $query["query"],
      "surname[~]" => $query["query"],
      "email[~]" => $query["query"]
    ];
  }
  if(!is_super()) {
    $where["AND"] = [
      "type[!]" => "super"
    ];
  }
  if(array_key_exists("start", $query) && array_key_exists("count", $query)) {
    $where["LIMIT"] = [$query["start"], $query["count"]];
  } else if (array_key_exists("count", $query)) {
    $where["LIMIT"] = $query["count"];
  }
  // file_put_contents('users', json_encode($where));
  $list = $model->select($where);
  // file_put_contents('users', count($list));
  $list = array_map(function($item){
    return denied_keys($item, ["password"]);
  }, $list);
  Flight::json($list);
});

// Get user by id
Flight::route("GET /api/user/@id", function($id){
  if(!is_logged()) {
    return Flight::stop(UNAUTHORIZED);
  }

  if($id != $_SESSION["user"]["id"] && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }

  $model = new User();

  $data = $model->read($id);

  if(!$data) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }

  $data = denied_keys($data, ["password"]);
  Flight::json($data);
});

// Update user data
Flight::route("PUT /api/user/@id", function($id){
  if(!is_logged()) {
    return Flight::stop(UNAUTHORIZED);
  }

  if($id != $_SESSION["user"]["id"] && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }

  $model = new User();
  $request = Flight::request();

  $data = $request->data->getData();

  if($id == $_SESSION["user"]["id"]) {
    // Can't change own type
    $data = denied_keys($data, ["type"]);
  }

  if(count($data) > 0) {
    $ret = $model->update($id, $data);
    $data = $model->read($id);
    $data = denied_keys($data, ["password"]);
    $_SESSION["user"] = $data;
    Flight::json([
      "response" => $ret
    ]);
  } else {
    // `update` Gives error if `count($data) == 0`
    Flight::json(["response" => 0]);
  }
});

Flight::route("DELETE /api/user/@id", function(){
  if(!is_logged()) {
    return Flight::stop(UNAUTHORIZED);
  }

  if($id != $_SESSION["user"]["id"] && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }

  $model = new User();
  $request = Flight::request();

  $ret = $model->delete($id);
  // TODO: Should log user out
  Flight::json([
    "response" => $ret
  ]);
});

// Log user in
Flight::route("POST /api/user/login", function(){
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
  $_SESSION["user"] = $data;
  // file_put_contents('login', json_encode($data));
  Flight::json($data);
});

// Log user out
Flight::route("POST /api/user/logout", function(){
  // Just delete session
  $_SESSION["user"] = null;
  Flight::json([]);
});

?>
