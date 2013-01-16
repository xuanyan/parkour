<?php

require 'Library/__init__.php';

set_exception_handler(function($e){
    print_r($e);
});

$config = require_once __DIR__ . '/Config.php';

$dbConfig = $config['database'];
$db = Database::connect($dbConfig['connection']);
$db->setConfig('initialization', $dbConfig['initialization']);

$location = convertip(getClienip());

$location = explode(' ', $location);

$location = current($location);

$sql = "SELECT * FROM `server_info` WHERE location = ? ORDER BY speed";

$info = $db->getAll($sql, $location);

$servers = array_keys($config['servers']);

$serverInfo = array(
    'host' => $servers[0],
    'user' => $config['servers'][$servers[0]][0],
    'pass' => $config['servers'][$servers[0]][1]
);

foreach ($info as $key => $val) {
    if (isset($config['servers'][$val['server']])) {
        
        $serverInfo = array(
            'host' => $val['server'],
            'user' => $config['servers'][$val['server']][0],
            'pass' => $config['servers'][$val['server']][1]
        );

        //$serverInfo = $config['servers'][$val['server']];
        break;
    }
    $db->exec("DELETE FROM `server_info` WHERE id = ?", $val['id']);
}

//print_r($serverInfo);
echo json_encode($serverInfo);
exit;