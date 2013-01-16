<?php
require 'Library/__init__.php';

$config = require ROOT_PATH . "/Config.php";
include ROOT_PATH . "/Service.php";
include ROOT_PATH . "/Help.php";

$route = $config['route'];
scan($route);
//getfast();
?>