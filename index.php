<?php

require 'Library/__init__.php';

include(ROOT_PATH . "/Service.php");
include(ROOT_PATH . "/Help.php");

if ( isset($argv[1]) && function_exists($argv[1])) {
	try{
		$argv[1]();
	} catch (Exception $e) {
		// error handle
	}
} else {
	echo "Error parmas , use like php index.php scan \n";
}