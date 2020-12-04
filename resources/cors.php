<?php

// Adds Access-Control-Allow-Origin to response

Flight::before("start", function(){
  $AccessControlAllowOrigin = get_config("AccessControlAllowOrigin", "*");
  Flight::response()->header(
    "Access-Control-Allow-Origin",
    $AccessControlAllowOrigin
  );
});

?>
