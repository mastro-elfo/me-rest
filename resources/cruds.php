<?php

// Creates endpoints routes for Create, Read, Update, Delete and Search

function cruds($endpoint, $create, $read, $update, $delete, $search)
{
    // Full endpoint
    $full = implode("/", ["", "api", $endpoint]);
    // Get route
    Flight::route("GET $full/@id", $read);
    // Search route
    Flight::route("GET $full", $search);
    // Post route
    Flight::route("POST $full", $create);
    // Put route
    Flight::route("PUT $full/@id", $update);
    // Delete route
    Flight::route("DELETE $full/@id", $delete);
}
