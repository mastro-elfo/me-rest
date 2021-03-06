<?php
// Start session
session_set_cookie_params([
	"SameSite" => "Strict",
	"Secure" => "true",
]);
session_start();
// Require libs
require_once "vendor/autoload.php";
// Load config file
$config = parse_ini_file("config.ini");
// Require before the rest
require_once "resources/rb.php";
// Require resources
require_once "resources/access.php";
require_once "resources/api.php";
require_once "resources/cors.php";
require_once "resources/log.php";
require_once "resources/sleep.php";
require_once "resources/status.php";
require_once "resources/user.php";
require_once "resources/user.profile.php";
require_once "resources/utils.php";
// Load project routes
require_once "resources/routes.php";
// Start
Flight::start();
