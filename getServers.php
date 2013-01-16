<?php
$config = require_once __DIR__ . '/Config.php';
$servers = array_keys($config['servers']);

echo json_encode($servers);