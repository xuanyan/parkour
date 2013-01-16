<?php

$dbname = 'parkour';
$host = 'localhost';
$user = 'root';
$password = '177628';

return array(
	'route' =>'http://local.parkour.com',
	'servers' => array(
		'wa-teleport02.cloudapp.net:22' => array('parkour','4dcd19c43fc9aee62b8f39751143&*()')
		),
	'database' => array(
		'connection' => array('pdo', 
							  'mysql:dbname='.$dbname.';host='.$host,
							  $user,
							  $password),
           'initialization' => array(
               'SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary',
               'SET sql_mode=``'
           	)
		)
	);