<?php

// Routes to manage user access

require_once "resources/user.ns.php";
require_once "resources/user.session.php";

// Log user in
Flight::route("POST /api/access/login", function () {
    $request = Flight::request();
    // Get username and password from request
    $username = $request->data->username;
    $password = $request->data->password;
    // Get user from username/password
    $user = User\login($username, $password);
    // Check login successful
    if (!$user) {
        // Not found
        return Flight::stop(NOT_FOUND);
    }
    // Set session data
    UserSession\login($user);
    // Response
    Flight::json($user);
});

// Log user out
Flight::route("POST /api/access/logout", function () {
    // Just delete session
    UserSession\logout();
    Flight::json([]);
});

// Logged user
Flight::route("GET /api/access/user", function () {
    // Get logged user from session
    $user = UserSession\logged_user();
    // Check access
    if (!$user) {
        // Not found
        return Flight::stop(NOT_FOUND);
    }
    // Response
    Flight::json($user);
});
