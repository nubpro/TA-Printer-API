<?php
class Consumable {
	private $url;
	private $ip;
	private $socket;
	private $socketTimeout = 1; // Timeout for connecting to the socket (seconds)
	private $streamTimeout = 2; // Timeout for reading from the socket (seconds)
	
	public function __construct($ip) {
		$this->ip = $ip;
		$this->url = "https://$ip/stsply.htm";
	}
	
	public function setURL($url) {
		$this->url = $url;
	}

	public function getURL() {
		return $this->url;
	}
	
	public function fetch() {
		try {
			$matchStr = "info=info.concat([";
			$data = [];
		
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
			
			$count = 0;
			while (($line = fgets($handle)) !== FALSE) {
				// Match line
				$pos = strpos($line, $matchStr);
				
				if ($pos !== FALSE) {
					// Match found, trim the data and store it
					$temp = substr($line, strlen($matchStr) + $pos, -5);

					// Convert single quote to double quote for json encoding purposes
					$temp = str_replace("'", "\"", $temp);
					
					$data[$count++] = json_decode($temp);
				}
			}
			
			// Close stream
			fclose($handle);
			if (!isset($data[0])) {
				throw new Exception("Matching was unsuccessful.");
			}
			

			return $this->result(0, $data);
			
			
		} catch (Exception $e) {
			return $this->result(1, $e->getMessage());
		}
	}
	
	public function result($err, $data) {
		$arr = array('error' => $err, 'url' => $this->getURL(), 'data' => $data);
		return $arr;
	}	
	
}