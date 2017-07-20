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
        $this->server="74.208.86.206";
		$this->database= "vikingosol_citybot";
		$this->user= "usrcitybot";
		$this->pass = "gW_dk893";
		$this->CityBotVersion = "v0.2";
    }

	public function TestConection()
	{
		file_put_contents("php://stderr", "TestConection\n");
		$myConn = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($myConn->connect_errno) {
			$res="Error: Fallo al conctarse a la Mysql Debido a: SERVER=".$this->server." USER= ". $this->user . " DATABASE=". $this->database ." \n Error No: " . $myConn->connect_errno . " \n Error: " . $myConn->connect_error;	
			file_put_contents("php://stderr", $res);		
			return $res;
		}else{
			$res = "Conexion Exitosa";
		}
		return $res;
	}

	public function GetPlaceDir($place)
	{

	}

	public function GetVersion()
	{
		file_put_contents("php://stderr", "GetVersion\n" . $this->CityBotVersion );
		return $this->CityBotVersion;
	}

	public function GetLocation($Place)
	{
		$myConn2 = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($myConn2->connect_errno) {
		}

		$sql = "SELECT dir,hours FROM Places WHERE name  like '". $Place . "'";
		if ($res1 = $mysqli->query($sql)) {
			if ($res->num_rows === 0) {
			    // ¡Oh, no ha filas! Unas veces es lo previsto, pero otras
			    // no. Nosotros decidimos. En este caso, ¿podría haber sido
			    // actor_id demasiado grande? 
			    return "I´m sorry, I don't know than place. :(";
			}
		}else{
			return "I'm sorry, I have some intertal problem";
		}
			
	}		
}

