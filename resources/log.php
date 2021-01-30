<?php
namespace Log;

require_once "lib/rb.php";
require_once "resources/user.session.php";

use \Flight;
use \R;

function create(array $data): int
{
    // Create record
    $log = R::dispense("querylog");
    // Merge data
    array_like_merge($log, $data);
    // Save and return
    return R::store($log);
}

function read(integer $id)
{
    // Load from db
    $log = R::load("log", $id);
    // If not found
    if (!$log || $log->id == 0) {
        return null;
    }

    // Export
    $log = $log->export();
    // Return
    return $log;
}

function findAll($offset = 0): array
{
    // Query db
    $logs = R::findAndExport("log",
        implode(" ", [
            "LIMIT :offset, 10",
        ]), [
            "offset" => $offset,
        ]);
    // Return
    return $logs;
}

Flight::before("start", function () {
    $log = get_config("log", true);
    // This stops the function but not the next "before"
    if (!$log) {
        return true;
    }
    // Get request params
    $request = Flight::request();
    // Query
    $id = create([
        // General info
        "datetime"   => date("Y-m-d H:i:s"),
        "user"       => \UserSession\logged_user(),
        // The following are the request params
        "url"        => $request->url,
        "base"       => $request->base,
        "method"     => $request->method,
        "referrer"   => $request->method,
        "ip"         => $request->ip,
        "ajax"       => $request->ajax,
        "scheme"     => $request->scheme,
        "user_agent" => $request->user_agent,
        "type"       => $request->type,
        "length"     => $request->length,
        "query"      => $request->query->getData(),
        // This is not secure: may contain plain password
        // "data" => $request->data->getData(),
        "cookies"    => $request->cookies->getData(),
        // Can be large
        // "files" => ???
        "secure"     => $request->secure,
        "accept"     => $request->accept,
        "proxy_ip"   => $request->proxy_ip,
        "host"       => $request->host,
    ]);
});
//
Flight::route("GET /api/logs", function () {
    // Check access
    if (!is_admin()) {
        return Flight::stop(UNAUTHORIZED);
    }
    // Get query params
    $request = Flight::request();
    $params  = $request->query->getData();
    // Find all
    $logs = findAll(
        array_key_exists("offset", $params) ? $params["offset"] : 0
    );
    // Response
    Flight::json($logs);
});
