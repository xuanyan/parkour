<?php
defined('ROOT_PATH') || define('ROOT_PATH', getcwd());
include(ROOT_PATH . DIRECTORY_SEPARATOR . "Service.php");
include(ROOT_PATH . DIRECTORY_SEPARATOR . "Help.php");

if ( isset($argv[1]) && function_exists($argv[1])) {
	try{
		$argv[1]();
	} catch (Exception $e) {
		// error handle
	}
} else {
	echo "Error parmas , use like php index.php scan \n";
}






