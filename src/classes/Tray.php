<?php

class Tray {
	private $ip;
	private $url;
	
	public function __construct($ip) {
		$this->ip = $ip;
		$this->url = "https://$ip/sttray.htm";
	}
	
	public function setURL($url) {
		$this->url = $url;
	}

	public function getURL() {
		return $this->url;
	}
	
	public function fetch() {
		try {
			$matchStr = "infoIn=";
			
			// Open stream (Bound to timeout)
			$handle = fopen($this->url, "r");
			if (!$handle) {
				throw new Exception ("URL cannot be opened.");
			}		
				
			// Is page not found?
			$file_headers = @get_headers($this->url);
			if(!$file_headers || strtolower($file_headers[0]) == 'http/1.1 404 not found') {
				throw new Exception ("URL does not exist.");
			}
			
			while (($line = fgets($handle)) !== FALSE) {
				// Match line
				$pos = strpos($line, $matchStr);
				if ($pos !== FALSE) {
					// Match found, trim the data and store it
					$data = substr($line, strlen($matchStr) + $pos, -3);
					
					// Convert single quote to double quote for json encoding purposes
					$data = str_replace("'", "\"", $data);
					
					// Break the loop!
					break;
				}
			}
			
			// Close stream
			fclose($handle);
			
			if (!isset($data)) {
				throw new Exception("Matching was unsuccessful.");
			}
			
			return $this->result(0, json_decode($data));	
		} catch (Exception $e) {
			return $this->result(1, $e->getMessage());
		}
	}
	
	public function result($err, $data) {
		$arr = array('error' => $err, 'url' => $this->getURL(), 'data' => $data);
		return $arr;
	}	
	
}