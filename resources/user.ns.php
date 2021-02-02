<?php

namespace User;

require_once "lib/rb.php";
require_once "resources/user.session.php";

use \R;

function create(array $data): int
{
    // Create record
    $user = R::dispense("user");
    // Filter allowed keys
    $keys = ["email", "name", "password","surname","username"];
    $data = allowed_keys($data, $keys);
    // Map data
    $maps = [
        "password" => "hide",
        // Only a super can create a super
        "type"     => function ($v) use ($user) {
            return ($v == "super" && !\UserSession\is_super())
            ? $user["type"]
            : $v;
        },
    ];
    $data = apply_maps($data, $maps);
    // Apply default values
    $data["type"] = "user";
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
    // Load for update
    $user = R::loadForUpdate("user", $id);
    // Only a super can update a super
    if ($user["type"] == "super" && !\UserSession\is_super()) {
        return false;
    }
    // Filter allowed keys
    $keys = ["email", "name", "password", "surname","type", "username"];
    $data = allowed_keys($data, $keys);
    // Map data
    $maps = [
        "password" => "hide",
        // Only a super can create a super
        "type"     => function ($v) use ($user) {
            return ($v == "super" && !\UserSession\is_super())
            ? $user["type"]
            : $v;
        },
    ];
    $data = apply_maps($data, $maps);
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
    if ($user["type"] == "super" && !\UserSession\is_super()) {
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
    $limit = 10) : array
{
    // Query db
    $users = R::findAndExport("user",
        implode(" ", [
            group_join("AND", [
                group_join("OR", [
                    "username LIKE :query",
                    "name LIKE :query",
                ]),
                // Only super can search a super
                !\UserSession\is_super() ? "type != 'super'" : "1",
            ]),
            "LIMIT :offset, :limit",
        ]), [
            "query"  => "%$query%",
            "offset" => $offset,
            "limit"  => $limit,
        ]);
    // Map results
    $users = array_map(function ($user) {
        // Remove denied keys
        return denied_keys($user, ["password"]);
    }, $users);
    // Return
    return $users;
}
