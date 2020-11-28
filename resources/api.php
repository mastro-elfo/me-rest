<?php

// Returns all the routes
Flight::route("/api", function(){
  Flight::json(Flight::router()->getRoutes());
});

?>
