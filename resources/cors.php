<?php

// Adds Access-Control-Allow-Origin to response

$AccessControlAllowOrigin =
  array_key_exists("AccessControlAllowOrigin", $config)
  ? $config["AccessControlAllowOrigin"] : "*";
Flight::response()->header(
  "Access-Control-Allow-Origin",
  $AccessControlAllowOrigin
);

?>
