<?php
namespace Bexi;

class DataBot
{
	$server="74.208.86.206";
	$database= "vikingosol_citybot";
	$user= "usrcitybot";
	$pass = "gW_dk893";

	public TestConection()
	{
		file_put_contents("php://stderr", "TestConection\n");
		$myConn = new mysqli($server, $user, $pass, $database);
		if ($myConn->connect_errno) {
			$res="Error: FAllo al conctarse a la Mysql Debido a: \n Error No: " . $myConn->connect_errno . " \n Error: " . $myConn->connect_error;	
			file_put_contents("php://stderr", $res);		
			return $res;
		}else{
			$res = "Conexion Exitosa";
		}
		return $res;
	}
}
