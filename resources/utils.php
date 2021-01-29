<?php

// Get a value from `global config` if it is defined
// Otherwise get the default value
function get_config(string $key, $default)
{
  global $config;
  if (array_key_exists($key, $config)) {
    return $config[$key];
  } else {
    return $default;
  }
}

// Uses an hash function to hide `value`
function hide(string $value): string
{
  $algorithm = get_config("hide", "sha256");
  return hash($algorithm, $value);
}

// Keeps only key/value pairs if key is not in `keys`
function denied_keys(array $array, array $keys): array
{
  // return array_diff_key($array, array_keys($keys));
  return array_filter($array,
    function($k) use($keys) {
      return !in_array($k, $keys);
    },
    ARRAY_FILTER_USE_KEY);
}

// Keeps only key/value pairs if key is in `keys`
function allowed_keys(array $array, array $keys): array
{
  return array_filter($array,
    function($k) use($keys) {
      return in_array($k, $keys);
    },
    ARRAY_FILTER_USE_KEY);
}

// Apply map function if key in `keys`
// If `keys[key]` is callable, the new value is `keys[key](value)`
// Otherwise is `keys[key]`
function apply_maps(array $array, array $keys): array
{
  $mapped = [];
  foreach ($array as $key => $value) {
    if (array_key_exists($key, $keys)) {
      if (is_callable($keys[$key])) {
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

// @see: https://stackoverflow.com/a/39877269
function array_any(array $array, callable $fn): bool
{
  foreach ($array as $value) {
    if ($fn($value)) {
      return true;
    }
  }
  return false;
}

// @see: https://stackoverflow.com/a/39877269
function array_every(array $array, callable $fn): bool
{
  foreach ($array as $value) {
    if (!$fn($value)) {
      return false;
    }
  }
  return true;
}

// Merge `$data` into an array like object
function array_like_merge(object &$object, array $data)
{
  foreach ($data as $key => $value) {
    $object[$key] = $value;
  }
}
