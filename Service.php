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
        $start = microtime(true);
        try {
            fsockopen($this->host, $this->port, $ero, $estr, $this->timeout);
        } catch (Exception $e) {
            return false;
        }

        return microtime(true) - $start;
    }
}