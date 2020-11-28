<?php

$config = parse_ini_file("config.ini");

// Uses an hash function to hide `value`
function hide($value) {
  global $config;
  return hash($config["hide"], $value);
}

// Keeps only key/value pairs if key is not in `keys`
function filter_keys($array, $keys) {
  return array_filter($array,
    function($k) use($keys) {
      return !in_array($k, $keys);
    },
    ARRAY_FILTER_USE_KEY);
}

// Apply map function if key in `keys`
function apply_maps($array, $keys) {
  $mapped = [];
  foreach ($array as $key => $value) {
    if(in_array($key, $keys)) {
      $mapped[$key] = $keys[$key]($value);
    } else {
      $mapped[$key] = $value;
    }
  }
  return $mapped;
}

?>
