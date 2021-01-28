<?php

// // Define log model
// class Log extends Model {
//   public function __construct() {
//     parent::__construct("Logs", [
//       // Primary key
//       "id" => ["INTEGER", "NOT NULL", "PRIMARY KEY", "AUTOINCREMENT", "UNIQUE"],
//       "datetime" => ["TEXT"],
//       "url" => ["TEXT"],
//       "base" => ["TEXT"],
//       "method" => ["TEXT"],
//       "referrer" => ["TEXT"],
//       "ip" => ["TEXT"],
//       "ajax" => ["INTEGER"],
//       "scheme" => ["TEXT"],
//       "user_agent" => ["TEXT"],
//       "type" => ["TEXT"],
//       "length" => ["INTEGER"],
//       "query" => ["BLOB"],
//       "data" => ["BLOB"],
//       "cookies" => ["BLOB"],
//       // "files" => ["BLOB"],
//       "secure" => ["INTEGER"],
//       "accept" => ["TEXT"],
//       "proxy_ip" => ["TEXT"]
//     ]);
//   }
// }
//
//
// Flight::before("start", function(){
//   $log = get_config("log", true);
//   // This stops the function but not the next "before"
//   if(!$log) return true;
//   //
//   $model = new Log();
//   $request = Flight::request();
//   // Define data object
//   $data = [
//     "datetime" => date("Y-m-d H:i:s"),
//     "url" => $request->url,
//     "base" => $request->base,
//     "method" => $request->method,
//     "referrer" => $request->method,
//     "ip" => $request->ip,
//     "ajax" => $request->ajax,
//     "scheme" => $request->scheme,
//     "user_agent" =>  $request->user_agent,
//     "type" =>  $request->type,
//     "length" =>  $request->length,
//     "query" =>  $request->query->getData(),
//     "data" => $request->data->getData(),
//     "cookies" =>  $request->cookies->getData(),
//     // "files" => ???
//     "secure" =>  $request->secure,
//     "accept" =>  $request->accept,
//     "proxy_ip" =>  $request->proxy_ip
//   ];
//   // Create row
//   $model->create($data);
// });
//
// Flight::route("GET /api/logs", function(){
//   // Check access
//   if(!is_admin()) {
//     return Flight::stop(UNAUTHORIZED);
//   }
//   //
//   $model = new Log();
//   $request = Flight::request();
//   $query = $request->query->getData();
//   $response = $model->select($query);
//   // Response
//   Flight::json($response);
// });

?>
