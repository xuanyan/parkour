<?php

	function scan($route){
		if (!$route){
			echo 'Error config route';
			exit;
			//错误处理
		}
		$service = json_decode(getConfig($route.'/getServers.php'));
		$serviceCheck = array();
		foreach ($service as $value) {
			$tmp = explode(':', $value);
			//if (count(explode('.', current($tmp))) == 4 ) {
			$serviceCheck[] = array('ip'=>current($tmp),'port'=>next($tmp));
			//}
		}
		foreach ($serviceCheck as $key=> $value) {
			echo check($route , $value['ip'] , $value['port']);
		}
	}

	function getfast(){
		$server = array();
		$speed = array();
		foreach (new DirectoryIterator(ROOT_PATH . DIRECTORY_SEPARATOR . 'Logs') as $fileInfo) {
		    if($fileInfo->isDot()){
				continue;
			} else {
				$filename = $fileInfo->getPathname();
				$handle = fopen($filename, "r");
                $contents = '';
                if ($size = filesize($filename)) {
    				$contents = fread($handle, filesize($filename));
                }
				fclose($handle);
				$key = $fileInfo->getFilename();
				$server[$contents] = $key;
				$speed[$key] = $contents;
			}
		}
		if ($speed) {
			sort($speed);
			foreach($speed as $value){
				echo $server[$value] .' ( '.$value." )\n";
			}
		} else {
			echo "Can't find server! \n";
		}
		
	}

	function delete(){
		foreach (new DirectoryIterator(ROOT_PATH . DIRECTORY_SEPARATOR . 'service') as $fileInfo) {
		    if($fileInfo->isDot()) continue;
			@unlink($fileInfo->getPathname());
		}
	}

	function getConfig($route){
		//$ips = array('219.234.82.90:6666','5.8.242.10:8080','41.33.159.3:80');
        return file_get_contents($route);
		//$ips = array('219.234.82.90:6666','5.8.242.10:8080','41.33.159.3:80','118.97.150.179:8080');
		//return json_encode($ips);
	}

	function check($route, $host , $port) {
		$file_name = $host.'_'.$port;
		$file_path = ROOT_PATH . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . $file_name;
		if ( file_exists($file_path) && time() - filemtime($file_path) < 1) {
			return $file_name . " file is existe! \n";
		} else {
			$service = new Service($host,$port);
			$ping = $service->check();
            $ping || $ping = 9999;
            $server = $host.':'.$port;
            file_get_contents($route."/saveWork.php?server={$server}&speed={$ping}");
			
			$fp = fopen($file_path, 'w');
			fwrite($fp, $ping);
			fclose($fp);
			return $file_name ." file is created! \n";
		}
	}