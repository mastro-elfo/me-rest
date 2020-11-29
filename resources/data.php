<?php

class Data extends Model {
  public function __construct() {
    parent::__construct("Data", [
      // Primary key
      "id" => ["INTEGER", "NOT NULL", "PRIMARY KEY", "AUTOINCREMENT", "UNIQUE"],
      // Owner
      "user_id" => ["INTEGER", "NOT NULL"],
      // Data type
      "mime" => ["TEXT"],
      // Content
      "content" => ["BLOB"]
    ]);
  }
}

// Gets data with a given `id`
Flight::route("GET /api/data/@id", function($id){
  // User must be logged
  if(!is_logged()) {
    // Not found
    return Flight::stop(NOT_FOUND);
  }

  $model = new Data();
  // $request = Flight::request();

  $data = $model->read($id);

  // User must be owner
  if($data["user_id"] != $_SESSION["user"]["id"]) {
    return Flight::stop(UNAUTHORIZED);
  }

  Flight::response()->header("Content-Type", $data["type"]);
  echo $data["content"];
});

Flight::route("POST /api/data", function(){
  Flight::stop(NOT_FOUND);
});

Flight::route("PUT /api/data/@id", function($id){
  // User must be logged
  // User must be owner
  Flight::stop(NOT_FOUND);
});

Flight::route("DELETE /api/data/@id", function($id){
  // User must be logged
  // User must be owner
  Flight::stop(NOT_FOUND);
});

?>
