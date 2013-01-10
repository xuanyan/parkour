<?php
class Service {
	protected $host;
	protected $port;
	protected $timeout;
	
	public function __construct($host, $port = 22, $timeout = 5) {
    if (!isset($host)) {
      throw new Exception("Error: Host name not supplied.");
    	}
    $this->host = $host;
	$this->port = $port;
	$this->timeout = $timeout;
	
	}
	
	function check(){
		error_reporting(E_ALL ^ E_WARNING);
		$start = microtime(true);
		if (fsockopen($this->host, $this->port, $ero, $estr, $this->timeout) === false){
			return 'false';
		} else {
			$spend = microtime(true) - $start;
			return $spend;
		}
	}
}