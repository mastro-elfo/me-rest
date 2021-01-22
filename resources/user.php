<?php

// require_once "resources/model.php";
//
// // Define user model
// class User extends Model {
//   public function __construct() {
//     parent::__construct("Users", [
//       // Primary key
//       "id" => ["INTEGER", "NOT NULL", "PRIMARY KEY", "AUTOINCREMENT", "UNIQUE"],
//       // Access control
//       "username" => ["TEXT", "UNIQUE"],
//       "password" => ["TEXT"],
//       // User type ["user", "admin", "super"]
//       "type" => ["TEXT"],
//       // User info
//       "name" => ["TEXT"],
//       "surname" => ["TEXT"],
//       "email" => ["TEXT", "UNIQUE"],
//       "picture" => ["BLOB"]
//     ]);
//   }
// }

require_once "resources/user.class.php";

// // Check if user is logged
// function is_logged() {
//   return array_key_exists("user", $_SESSION) && !!$_SESSION["user"];
// }
//
// // Check if user is admin (all super are also admin)
// function is_admin() {
//   return is_logged() && (
//     $_SESSION["user"]["type"] == "admin"
//     || $_SESSION["user"]["type"] == "super");
// }
//
// // Check if user is a superuser
// function is_super() {
//   return is_logged() && $_SESSION["user"]["type"] == "super";
// }

require_once "resources/user.session.php";

Flight::route("POST /api/user/check", function(){
  $model = new User();
  $request = Flight::request();
  // Get request data
  $username = $request->data->username;
  $email = $request->data->email;
  // Query
  $data = $model->select([
    "OR" => [
      "username" => $username,
      "email" => $email
    ]
  ]);
  // Response
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
  // Check access
  if(!is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }
  //
  $model = new User();
  $request = Flight::request();
  // Get request params
  // TODO: this can be simplified with `$request->data->getData()` and `apply_maps`
  $username = $request->data->username;
  $password = hide($request->data["password"]);
  $name = $request->data->name;
  $surname = $request->data->surname;
  $email = $request->data->email;
  $picture = $request->data->picture;
  // Query
  $id = $model->create([
    "username" => $username,
    "password" => $password,
    "type" => "user",
    "name" => $name,
    "surname" => $surname,
    "email" => $email,
    "picture" => $picture
  ]);
  // Check last inserted id
  if(!$id) {
    // Probably username or email duplicate
    return Flight::stop(BAD_REQUEST);
  }
  // Response
  Flight::json(["id" => $id]);
});

// Get logged user
Flight::route("GET /api/user", function(){
  // Check access
  if(!is_logged()) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }
  // Response
  Flight::json($_SESSION["user"]);
});

// List all users
Flight::route("GET /api/users", function(){
  // Check access
  if(!is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }
  //
  $model = new User();
  $request = Flight::request();
  $query = $request->query->getData();
  // file_put_contents('users', json_encode($query));
  // Build where statement
  $where = [];
  if(array_key_exists("query", $query)) {
    $where["OR"] = [
      "username[~]" => $query["query"],
      "name[~]" => $query["query"],
      "surname[~]" => $query["query"],
      "email[~]" => $query["query"]
    ];
  }
  // Don't show super users if logged user is less then super
  if(!is_super()) {
    $where["AND"] = [
      "type[!]" => "super"
    ];
  }
  // Limit part
  if(array_key_exists("start", $query) && array_key_exists("count", $query)) {
    $where["LIMIT"] = [$query["start"], $query["count"]];
  } else if (array_key_exists("count", $query)) {
    $where["LIMIT"] = $query["count"];
  }
  // Query db
  $list = $model->select($where);
  // Remove password
  $list = array_map(function($item){
    return denied_keys($item, ["password"]);
  }, $list);
  // Response
  Flight::json($list);
});

// Get user by id
Flight::route("GET /api/user/@id", function($id){
  // Check access
  if(!is_logged()) {
    return Flight::stop(UNAUTHORIZED);
  }
  // Check access
  if($id != $_SESSION["user"]["id"] && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }
  //
  $model = new User();
  // Get user from db
  $data = $model->read($id);
  //
  if(!$data) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }
  // Remove keys
  $data = denied_keys($data, ["password"]);
  // Response
  Flight::json($data);
});

// Update user data
Flight::route("PUT /api/user/@id", function($id){
  // Check access
  if(!is_logged()) {
    return Flight::stop(UNAUTHORIZED);
  }
  // Check access
  if($id != $_SESSION["user"]["id"] && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }

  $model = new User();
  $request = Flight::request();
  // A super user can be edited only by super users
  if(!is_super()) {
    $model_data = $model->read($id);
    if(!$model_data || $model_data["type"] == "super") {
      return Flight::stop(NOT_FOUND);
    }
  }
  // Get request data
  $data = $request->data->getData();
  //
  if($id == $_SESSION["user"]["id"]) {
    // Can't change own type
    $data = denied_keys($data, ["type"]);
  }
  // If $data is empty $model->update gives error
  if(count($data) > 0) {
    $ret = $model->update($id, $data);
    $data = $model->read($id);
    $data = denied_keys($data, ["password"]);
    Flight::json([
      "response" => $ret
    ]);
  } else {
    // Nothing to update
    Flight::json(["response" => 0]);
  }
});

Flight::route("DELETE /api/user/@id", function(){
  // Check access
  if(!is_logged()) {
    return Flight::stop(UNAUTHORIZED);
  }
  // Check access
  if($id != $_SESSION["user"]["id"] && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }
  //
  $model = new User();
  $request = Flight::request();
  // A super user can be edited only by super users
  if(!is_super()) {
    $model_data = $model->read($id);
    if(!$model_data || $model_data["type"] == "super") {
      return Flight::stop(NOT_FOUND);
    }
  }
  // Query
  $ret = $model->delete($id);
  // Log user out
  $_SESSION["user"] = null;
  // Response
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
