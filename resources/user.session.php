<?php

// Check if user is logged
function is_logged() {
  return array_key_exists("user", $_SESSION) && !!$_SESSION["user"];
}

// Check if user is admin (all super are also admin)
function is_admin() {
  return is_logged() && (
    $_SESSION["user"]["type"] == "admin"
    || $_SESSION["user"]["type"] == "super");
}

// Check if user is a superuser
function is_super() {
  return is_logged() && $_SESSION["user"]["type"] == "super";
}

?>
