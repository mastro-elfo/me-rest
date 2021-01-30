<?php

namespace User;

require_once "lib/rb.php";

use \R;

function create(array $data): int
{
    // Filter allowed keys
    $keys = ["password", "username"];
    $data = allowed_keys($data, $keys);
    // Map data
    $maps = ["password" => "hide"];
    $data = apply_maps($data, $maps);
    // Create record
    $user = R::dispense("user");
    // Merge data
    array_like_merge($user, $data);
    // Save and return
    return R::store($user);
}

function read(int $id):  ? array
{
    // Load from db
    $user = R::load("user", $id);
    // If not found
    if (!$user || $user->id == 0) {
        return null;
    }
    // Export
    $user = $user->export();
    // Filter denied keys
    $user = denied_keys($user, ["password"]);
    // Return
    return $user;
}

function update(int $id, array $data)
{
    // Filter allowed keys
    $keys = ["name", "password", "username"];
    $data = allowed_keys($data, $keys);
    // Map data
    $maps = ["password" => "hide"];
    $data = apply_maps($data, $maps);
    // Load for update
    $user = R::loadForUpdate("user", $id);
    // Can't update super user
    if ($user["type"] == "super") {
        return false;
    }
    // Merge data into user
    array_like_merge($user, $data);
    // Save
    return R::store($user);
}

// Delete from db
// TODO: https://redbeanphp.com/api/classes/RedBeanPHP.Facade.html#method_hunt
function delete($id)
{
    // Lock row for update
    $user = R::loadForUpdate("user", $id);
    // Can't delete super user
    if ($user["type"] == "super") {
        return false;
    }
    // Remove
    R::trash($user);
    return true;
}

function login(string $username, string $password) :  ? array
{
    // Query db
    $user = R::findOne("user",
        "username = :username AND password = :password", [
            "username" => $username,
            "password" => hide($password),
        ]);
    // Check valid user
    if ($user && $user["id"] != 0) {
        // Export
        $user = $user->export();
        // Filter denied keys
        $user = denied_keys($user, ["password"]);
        // User found, export result and return
        return $user;
    }
    // User not found: return null
    return null;
}

function findAll(
    string $query,
    $offset = 0,
    $limit = 10,
    $super = false) : array
{
    // Query db
    $users = R::findAndExport("user",
        implode(" ", [
            "username LIKE :query",
            "LIMIT :offset, :limit",
        ]), [
            "query"  => "%$query%",
            "offset" => $offset,
            "limit"  => $limit,
        ]);
    // Map results
    $users = array_map(function ($user) {
        // Remove denied keys
        $user = denied_keys($user, ["password"]);
        return $user;
    }, $users);
    // Return
    return $users;
}
