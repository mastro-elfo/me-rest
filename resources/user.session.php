<?php

namespace UserSession;

// Check if user is logged
function is_logged(): bool
{
    return array_key_exists("user", $_SESSION) && !!$_SESSION["user"];
}

// Check if user is a superuser
function is_super(): bool
{
    return is_logged() && $_SESSION["user"]["type"] == "super";
}

// Check if user is admin (all super are also admin)
function is_admin(): bool
{
    return is_logged() && (
        $_SESSION["user"]["type"] == "admin"
        || $_SESSION["user"]["type"] == "super");
}

// Check if given id corresponds to logged user
function is_me(integer $id): bool
{
    return is_logged() && $_SESSION["user"]["id"] == $id;
}

// Set session variable
function login(array $user): bool
{
    $_SESSION["user"] = $user;
    return true;
}

// Delete session variable
function logout()
{
    $_SESSION["user"] = null;
}

// Get logged user
function logged_user():  ? array
{
    if (is_logged()) {
        return $_SESSION["user"];
    }

    return null;
}
