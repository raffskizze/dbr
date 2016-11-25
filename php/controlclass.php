<?php
namespace dbr; 

require_once("dbrclass.php"); 


/* Clase control para el dbr */
class Control 
{ 
	/* Funcion para extraer las tablas de la bd conectada*/
	static function gettables()
	{
		$con = new Dbrclass;
		$con->sqlquery("SHOW tables FROM ".NOMBREBD.";");
		
		$tables = $con->getallarray();
		
		$json=json_encode($tables);
		
		return $json;
	}
		
	/* Funcion para ejecutar la consulta sql pasada que devuelve la info y datos en json*/
	static function query($sql)
	{
		$con = new Dbrclass;
		$con->sqlquery($sql);
		
		$tables = $con->getallarray();
		
		$json=json_encode($tables);
		
		return $json;
	}
}

/* PRUEBAS */

//echo Control::gettables();


?>