<?php

// Get a value from `global config` if it is defined
// Otherwise get the default value
function get_config($key, $default) {
  global $config;
  if(array_key_exists($key, $config)) {
    return $config[$key];
  } else {
    return $default;
  }
}

// Uses an hash function to hide `value`
function hide($value) {
  $algorithm = get_config("hide", "sha256");
  return hash($algorithm, $value);
}

// Keeps only key/value pairs if key is not in `keys`
function denied_keys($array, $keys) {
  return array_filter($array,
    function($k) use($keys) {
      return !in_array($k, $keys);
    },
    ARRAY_FILTER_USE_KEY);
}

// Keeps only key/value pairs if key is in `keys`
function allowed_keys($array, $keys) {
  return array_filter($array,
    function($k) use($keys) {
      return in_array($k, $keys);
    },
    ARRAY_FILTER_USE_KEY);
}

// Apply map function if key in `keys`
// If `keys[key]` is callable, the new value is `keys[key](value)`
// Otherwise is `keys[key]`
function apply_maps($array, $keys) {
  $mapped = [];
  foreach ($array as $key => $value) {
    if(in_array($key, $keys) && is_callable()) {
      if(is_callable($keys[$key])) {
        $mapped[$key] = $keys[$key]($value);
      } else {
        $mapped[$key] = $keys[$key];
      }
    } else {
      $mapped[$key] = $value;
    }
  }
  return $mapped;
}

?>
