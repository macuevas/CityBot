<?php

class DataBot
{
	public $server;
	public $database;
	public $user;
	public $pass;
	public $CityBotVersion;

	public function __construct()
    {
        $this->$server="74.208.86.206";
		$this->$database= "vikingosol_citybot";
		$this->$user= "usrcitybot";
		$this->$pass = "gW_dk893";
		$this->$CityBotVersion = "v0.2";
    }

	public function TestConection()
	{
		file_put_contents("php://stderr", "TestConection\n");
		$myConn = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($myConn->connect_errno) {
			$res="Error: FAllo al conctarse a la Mysql Debido a: \n Error No: " . $myConn->connect_errno . " \n Error: " . $myConn->connect_error;	
			file_put_contents("php://stderr", $res);		
			return $res;
		}else{
			$res = "Conexion Exitosa";
		}
		return $res;
	}

	public function GetVersion()
	{
		file_put_contents("php://stderr", "GetVersion\n" . $this->CityBotVersion );
		return $this-CityBotVersion;
	}
}
