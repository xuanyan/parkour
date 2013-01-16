<?php

require 'Library/__init__.php';

$config = require_once __DIR__ . '/Config.php';

$dbConfig = $config['database'];

$db = Database::connect($dbConfig['connection']);
$db->setConfig('initialization', $dbConfig['initialization']);

$location = convertip(getClienip());

$location = explode(' ', $location);

$location = current($location);

$server = _GET('server');

$speed = _GET('speed');

$now = time();

$sql = "INSERT INTO `server_info` (location, server, speed, updated) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE speed = ?, updated = ?";

try {
    $db->exec($sql, $location, $server, $speed, $now, $speed, $now);
} catch (Exception $e) {
    print_r($e);
}
