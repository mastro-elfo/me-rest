<?php

require_once "resources/user.ns.php";
require_once "resources/user.session.php";

// Get user data from db
Flight::route("GET /api/profile", function () {
    // Check user logged
    if (!UserSession\is_logged()) {
        Flight::stop(NOT_FOUND);
    }
    // Get id from session
    $session = UserSession\logged_user();
    $id      = $session["id"];
    // Get user from db
    $user = User\read($id);
    // User not found
    if (!$user) {
        // Not found
        return Flight::stop(NOT_FOUND);
    }
    // Response
    Flight::json($user);
});

// User registration
Flight::route("POST /api/profile", function () {
    return Flight::stop(SERVICE_UNAVAILABLE);
    // Check if a user can register
    $register_profile = get_config("RegisterProfile", true);
    if (!$register_profile) {
        Flight::stop(SERVICE_UNAVAILABLE);
    }
    // TODO:
});

// User update
Flight::route("PUT /api/profile", function () {
    // Check user logged
    if (!UserSession\is_logged()) {
        Flight::stop(NOT_FOUND);
    }
    // Get request data
    $request = Flight::request();
    $data    = $request->data->getData();
    // Get id from session
    $session = UserSession\logged_user();
    $id      = $session["id"];
    // Update
    $response = User\update($id, $data);
    // Can't update user
    if (!$response) {
        return Flight::stop(BAD_REQUEST);
    }
    // Response
    Flight::json([]);
});

// User delete
Flight::route("DELETE /api/profile", function () {
    // Check user logged
    if (!UserSession\is_logged()) {
        Flight::stop(NOT_FOUND);
    }
    // Get id from session
    $session = UserSession\logged_user();
    $id      = $session["id"];
    // Delete user
    $response = User\delete($id);
    // Can't delete user
    if (!$response) {
        Flight::stop(BAD_REQUEST);
    }
    // Response
    Flight::json([]);
});
