<?php

// Returns all the routes
Flight::route("/api", function () {
    $expose = get_config("api", true);
    if ($expose) {
        // Expose api
        $routes = Flight::router()->getRoutes();
    } else {
        $routes = [];
    }
    Flight::json($routes);
});
