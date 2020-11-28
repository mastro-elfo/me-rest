<?php
// Start session
session_start();
// Require libs
require "vendor/autoload.php";
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
