<?php

// Manage DB with RedBeanPHP

require_once "lib/rb.php";

Flight::before("start", function () {
  // RedBeanPHP connection: https://www.redbeanphp.com/index.php?p=/connection
  $setup = get_config("RBSetup", "");
  R::setup($setup);
  // RedBeanPHP frozen: https://www.redbeanphp.com/index.php?p=/fluid_and_frozen
  $frozen = get_config("RBFrozen", false);
  R::freeze($frozen);
});
