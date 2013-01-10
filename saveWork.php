<?php

require 'Library/__init__.php';

$config = require_once __DIR__ . '/Config.php';

$dbConfig = $config['database'];
$db = Database::connect($dbConfig['connection']);
$db->setConfig('initialization', $dbConfig['initialization']);

$location = convertip(getClienip());

echo $location;