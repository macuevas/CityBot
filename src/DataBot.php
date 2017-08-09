<?php

namespace Bexi;

use Mysqli;

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
		$Place = "%".str_replace(" ","%",$Place)."%";
		$Place = str_replace("%%","%",$Place);
		$myConn2 = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($myConn2->connect_errno) {
		}
		file_put_contents("php://stderr", "GetLocation\n" . $Place );
		$sql = "SELECT dir,hours FROM Places WHERE name  like '". $Place . "' ";
		file_put_contents("php://stderr", "QUERY ". $sql . "\n" );
		if ($res1 = $myConn2->query($sql)) {
			if ($res1->num_rows === 0) {
			    // ¡Oh, no ha filas! Unas veces es lo previsto, pero otras
			    // no. Nosotros decidimos. En este caso, ¿podría haber sido
			    // actor_id demasiado grande? 
			    return "I´m sorry, I don't know that place. :(";
			}else{
				$place = $res1->fetch_assoc();
				if ($place["hours"]!="")
				{
					return $place["dir"] . "\n" . $place["hours"];
				}else{
					return $place["dir"];
				}
			}
		}else{
			return "Hmmm, I'm not sure I understand. Can you ask again? Try \"What's the weather like?\" or \"What time is it?\""; 
		}
			
	}	

	public function GetPlaces($busqueda = "%")
	{		
		$myConn2 = new mysqli($this->server, $this->user, $this->pass, $this->database);
		$Place = "%".str_replace(" ","%",$busqueda)."%";
		$Place = str_replace("%%","%",$Place);
		if ($myConn2->connect_errno) {
		}
		file_put_contents("php://stderr", "places_events\n" . $Place );
		$sql = "SELECT * FROM places_events WHERE nombre LIKE  '".$Place."' ORDER BY rand()";
		file_put_contents("php://stderr", "QUERY ". $sql . "\n" );
		if ($res1 = $myConn2->query($sql)) {
			if ($res1->num_rows === 0) {
					return ;
			}else{
				while ($fila = $res1->fetch_assoc()) {
					$pages[]=$fila;
				}
				$res1->free();
				return $pages;
			}
		}else{
			return ; 
		}
			
	}	

	public function GetActivities()
	{		
		$myConn2 = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($myConn2->connect_errno) {
		}
		file_put_contents("php://stderr", "activities\n" . $Place );
		$sql = "SELECT * FROM activities";
		file_put_contents("php://stderr", "QUERY ". $sql . "\n" );
		if ($res1 = $myConn2->query($sql)) {
			if ($res1->num_rows === 0) {
					return ;
			}else{
				while ($fila = $res1->fetch_assoc()) {
					$pages[]=$fila;
				}
				$res1->free();
				return $pages;
			}
		}else{
			return ; 
		}
			
	}	

	public function GetPagesId($busqueda = "%")
	{		
		$myConn2 = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($myConn2->connect_errno) {
		}
		$busqueda = "%".str_replace(" ","%",$busqueda)."%";
		$busqueda = str_replace("%%","%",$busqueda);
		file_put_contents("php://stderr", "GetPagesId\n" . $busqueda );
		$sql = "SELECT fb_id FROM places_events WHERE nombre LIKE '".$busqueda."'";
		file_put_contents("php://stderr", "QUERY ". $sql . "\n" );
		if ($res1 = $myConn2->query($sql)) {
			if ($res1->num_rows === 0) {
			    // ¡Oh, no ha filas! Unas veces es lo previsto, pero otras
			    // no. Nosotros decidimos. En este caso, ¿podría haber sido
			    // actor_id demasiado grande? 
			    return;
			}else{
				while ($fila = $res1->fetch_assoc()) {
					$pages[]=$fila["fb_id"];
				}
				$res1->free();
				return $pages;
			}
		}else{
			return ; 
		}
			
	}		
}

