<?php

// Require project routes

$files = glob(implode("/", ["routes", "*.php"]));
foreach ($files as $file) {
    require_once $file;
}
