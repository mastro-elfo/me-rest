<?php

require_once "resources/user.ns.php";
require_once "resources/user.session.php";

// Get user by id
Flight::route("GET /api/user/@id", function ($id) {
    // Check access
    if (!\UserSession\is_admin()) {
        return Flight::stop(UNAUTHORIZED);
    }
    // Get user from db
    $user = User\read((integer) $id);
    // User not found
    if (!$user) {
        // Not found
        return Flight::stop(NOT_FOUND);
    }
    // Response
    Flight::json($user);
});

// Create new user
Flight::route("POST /api/user", function () {
    // Check access
    if (!\UserSession\is_admin()) {
        return Flight::stop(UNAUTHORIZED);
    }
    // Get request
    $request = Flight::request();
    // Query
    $id = User\create($request->data->getData());
    // Check last inserted id
    if (!$id) {
        // Probably username or email duplicate
        return Flight::stop(BAD_REQUEST);
    }
    // Response
    Flight::json(["id" => $id]);
});

// Update user data
Flight::route("PUT /api/user/@id", function ($id) {
    // Check access
    if (!\UserSession\is_admin()) {
        return Flight::stop(UNAUTHORIZED);
    }
    // Get request data
    $request = Flight::request();
    $data    = $request->data->getData();
    // Update
    $response = User\update($id, $data);
    // Can't update user
    if (!$response) {
        return Flight::stop(BAD_REQUEST);
    }
    // Response
    Flight::json([]);
});

Flight::route("DELETE /api/user/@id", function () {
    // Check access
    if (!\UserSession\is_admin()) {
        return Flight::stop(UNAUTHORIZED);
    }
    // Delete user
    $response = User\delete($id);
    // Can't delete user
    if (!$response) {
        Flight::stop(BAD_REQUEST);
    }
    // Response
    Flight::json([]);
});

// List all users
Flight::route("GET /api/users", function () {
    // // Check access
    if (!UserSession\is_admin()) {
        return Flight::stop(UNAUTHORIZED);
    }
    // Get query params
    $request = Flight::request();
    $params  = $request->query->getData();
    // Empty query give nothing
    if (!array_key_exists("query", $params)) {
        return Flight::json([]);
    }
    // Find all
    $users = User\findAll(
        $params["query"],
        array_key_exists("offset", $params) ? $params["offset"] : 0,
        array_key_exists("limit", $params) ? $params["limit"] : 100,
        UserSession\is_super());
    // Return
    Flight::json($users);
});
