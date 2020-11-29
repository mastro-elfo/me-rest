<?php
// Start session
session_set_cookie_params([
  "SameSite" => "Strict",
  "Secure" => "true"
]);
session_start();
// Require libs
require "vendor/autoload.php";
// Load config file
$config = parse_ini_file("config.ini");
// Require resources
require "resources/api.php";
require "resources/cors.php";
require "resources/model.php";
require "resources/status.php";
require "resources/user.php";
require "resources/utils.php";
// Start
Flight::start();
?>
