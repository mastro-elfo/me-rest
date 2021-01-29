<?php

require_once "resources/user.ns.php";
require_once "resources/user.session.php";

// Create new user
Flight::route("POST /api/user", function () {
  // Check access
  if (!is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }
  //
  // Get request
  $request = Flight::request();
  // Query
  $id = User\create($request->data->getData());
  // Check last inserted id
  if (!$id) {
    // Probably username or email duplicate
    return Flight::stop(BAD_REQUEST);
  }
  // Response
  Flight::json(["id" => $id]);
});

// Get user by id
Flight::route("GET /api/user/@id", function ($id) {
  // Check access
  if (!is_me() && !is_admin()) {
    return Flight::stop(UNAUTHORIZED);
  }
  // Get user from db
  $data = $model->read($id);
  //
  if (!$data) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }
  // Remove keys
  $data = denied_keys($data, ["password"]);
  // Response
  Flight::json($data);
});

// Update user data
Flight::route("PUT /api/user/@id", function ($id) {
  // // Check access
  // if(!is_logged()) {
  //   return Flight::stop(UNAUTHORIZED);
  // }
  // // Check access
  // if($id != $_SESSION["user"]["id"] && !is_admin()) {
  //   return Flight::stop(UNAUTHORIZED);
  // }
  //
  // $model = new User();
  // $request = Flight::request();
  // // A super user can be edited only by super users
  // if(!is_super()) {
  //   $model_data = $model->read($id);
  //   if(!$model_data || $model_data["type"] == "super") {
  //     return Flight::stop(NOT_FOUND);
  //   }
  // }
  // // Get request data
  // $data = $request->data->getData();
  // //
  // if($id == $_SESSION["user"]["id"]) {
  //   // Can't change own type
  //   $data = denied_keys($data, ["type"]);
  // }
  // // If $data is empty $model->update gives error
  // if(count($data) > 0) {
  //   $ret = $model->update($id, $data);
  //   $data = $model->read($id);
  //   $data = denied_keys($data, ["password"]);
  //   Flight::json([
  //     "response" => $ret
  //   ]);
  // } else {
  //   // Nothing to update
  //   Flight::json(["response" => 0]);
  // }
});

Flight::route("DELETE /api/user/@id", function () {
  // // Check access
  // if(!is_logged()) {
  //   return Flight::stop(UNAUTHORIZED);
  // }
  // // Check access
  // if($id != $_SESSION["user"]["id"] && !is_admin()) {
  //   return Flight::stop(UNAUTHORIZED);
  // }
  // //
  // $model = new User();
  // $request = Flight::request();
  // // A super user can be edited only by super users
  // if(!is_super()) {
  //   $model_data = $model->read($id);
  //   if(!$model_data || $model_data["type"] == "super") {
  //     return Flight::stop(NOT_FOUND);
  //   }
  // }
  // // Query
  // $ret = $model->delete($id);
  // // Log user out
  // $_SESSION["user"] = null;
  // // Response
  // Flight::json([
  //   "response" => $ret
  // ]);
});

// List all users
Flight::route("GET /api/users", function () {
  // // Check access
  // if(!is_admin()) {
  //   return Flight::stop(UNAUTHORIZED);
  // }
  // //
  // $model = new User();
  // $request = Flight::request();
  // $query = $request->query->getData();
  // // file_put_contents('users', json_encode($query));
  // // Build where statement
  // $where = [];
  // if(array_key_exists("query", $query)) {
  //   $where["OR"] = [
  //     "username[~]" => $query["query"],
  //     "name[~]" => $query["query"],
  //     "surname[~]" => $query["query"],
  //     "email[~]" => $query["query"]
  //   ];
  // }
  // // Don't show super users if logged user is less then super
  // if(!is_super()) {
  //   $where["AND"] = [
  //     "type[!]" => "super"
  //   ];
  // }
  // // Limit part
  // if(array_key_exists("start", $query) && array_key_exists("count", $query)) {
  //   $where["LIMIT"] = [$query["start"], $query["count"]];
  // } else if (array_key_exists("count", $query)) {
  //   $where["LIMIT"] = $query["count"];
  // }
  // // Query db
  // $list = $model->select($where);
  // // Remove password
  // $list = array_map(function($item){
  //   return denied_keys($item, ["password"]);
  // }, $list);
  // // Response
  // Flight::json($list);
});
