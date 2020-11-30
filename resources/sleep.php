<?php

// Adds Access-Control-Allow-Origin to response

$delay =
  array_key_exists("delay", $config)
  ? $config["delay"] : 0;

if($delay) {
  sleep($delay);
}

?>
