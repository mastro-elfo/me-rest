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
require_once "resources/api.php";
require_once "resources/cors.php";
// require_once "resources/model.php";
require_once "resources/sleep.php";
require_once "resources/status.php";
require_once "resources/user.php";
require_once "resources/utils.php";
//
require "resources/log.php"; // Requires Model
// Start
Flight::start();
?>
