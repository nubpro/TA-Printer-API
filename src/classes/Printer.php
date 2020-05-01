<?php

class Printer 
{
	private $name;
	private $ip;
	
	private $Tray;
	private $Consumable;
	
	public function __construct($name, $ip) {
		$this->name = $name;
		$this->ip = $ip;
		
		$this->Tray = new Tray($ip);
		$this->Consumable = new Consumable($ip);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getIP() {
		return $this->ip;
	}
	
	public function getStatus() {
		try {
			$status = [];
			$status["tray"] = $this->Tray->fetch();
			$status["consumable"] = $this->Consumable->fetch();
			
			return $this->result(0, $status);
		} catch (Exception $e) {
			return $this->result(1, "Failed to retrieve status.");
		}
	}
	
	public function result($err, $data) {
		$arr = array(
					"name" => $this->getName(), 
					"ip" => $this->getIP(),
					"error" => $err,
					"data" => $data
				);
		return $arr;
	}
}