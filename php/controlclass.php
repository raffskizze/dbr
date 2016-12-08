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
		
		$tables = array();
		
		//si se van a mostrar datos buscamos los campos unicos y principales para dejar editarlos posteriormente 
		$editables = array();
		if($con->Treg > 0)
		{
			$infofields = $con->infofields(); 
			foreach($infofields as $field) 
			if(stristr(static::flagstext($field->flags),"PRI_KEY") || stristr(static::flagstext($field->flags),"UNIQUE_KEY"))
			$editables[$field->name]=TRUE;
			else
			$editables[$field->name]=FALSE;
			
			//sacamos el nombre de la tabla
			$editables["tablename"]=$infofields[0]->table;
			
			$tables = $con->getallarrayprev();
		}
		
		$array= ["info"=>["time"=> $con->Querytime,"rows"=>$con->Treg,"error"=>$con->Error],"data"=>$tables,"editables"=>$editables];
		
		
		$json=json_encode($array);
		
		return $json;
	}
	
	/* Funcion que devuelve las constantes en modo texto del numero flags pasado*/
	static function flagstext($flags_num)
	{
		static $flags;

		if (!isset($flags))
		{
			$flags = array();
			$constants = get_defined_constants(true);
			foreach ($constants['mysqli'] as $c => $n) if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m)) if (!array_key_exists($n, $flags)) $flags[$n] = $m[1];
		}

		$result = array();
		foreach ($flags as $n => $t) if ($flags_num & $n) $result[] = $t;
		return implode(' ', $result);
	}
	
	/* editar un registro mostrando sus datos editables a partir de un campo unico id u otro, devuelve el form html*/
	static function editreg($field, $val, $table)
	{
		$con = new Dbrclass;
		$con->sqlquery("SELECT * FROM `".$table."` WHERE ".$field." = '".$val."';");
		$re = $con->getarray();
		
		//sacamos los campos de la tabla
		$con->sqlquery("SHOW COLUMNS FROM `".$table."`;");
		$campostabla =  $con->getallarray(); 
		
		$formhtml = "";
						
		foreach($campostabla as $campo)
		{ 
			if( $campo["Field"] != $field) //nos saltamos el campo id principal
			{
				//segun sea text , int o varchar ponemos un input o textarea
				if(stristr($campo["Type"],"varchar") || stristr($campo["Type"],"date") || stristr($campo["Type"],"time") || stristr($campo["Type"],"decimal") )
				$input =  "<input type='text' class='form-control' id='".$campo["Field"]."' name='".$campo["Field"]."' value='".$re[$campo["Field"]]."'>";
				elseif((stristr($campo["Type"],"int") || stristr($campo["Type"],"year")) && !stristr($campo["Type"],"point"))
				$input =  "<input type='number' class='form-control' id='".$campo["Field"]."' name='".$campo["Field"]."' value='".$re[$campo["Field"]]."'>";
				elseif(stristr($campo["Type"],"text"))
				$input =  "<textarea id='".$campo["Field"]."' class='form-control' name='".$campo["Field"]."'>".$re[$campo["Field"]]."</textarea>";
				else
				$input =  "<span class='small'> Formato desconocido para el campo <span class='label label-warning'>".$campo["Field"]." ".$campo["Type"]."</span></span>";
					
				$formhtml  .="<div class='form-group'>
				<label class='control-label' for='".$campo["Field"]."'>".$campo["Field"]." </label> ".$input."
				</div>";
			}
		}
		
		return $formhtml;
	}
	
	/* insertar un registro mostrando las columnas a editar posibles de la tabla, devuelve el form html*/
	static function insertreg($table)
	{
		$con = new Dbrclass; 
		//sacamos los campos de la tabla
		$con->sqlquery("SHOW COLUMNS FROM `".$table."`;");
		$campostabla =  $con->getallarray(); 
		//print_r($campostabla);
		$formhtml = "";
						
		foreach($campostabla as $campo)
		{ 
				//segun sea text , int o varchar ponemos un input o textarea
				if(stristr($campo["Type"],"int") && stristr($campo["Extra"],"auto_increment"))
				$input = "<br><span class='small'> Auto increment <span class='label label-warning'>null</span></span>";
				elseif(stristr($campo["Type"],"varchar") || stristr($campo["Type"],"date") || stristr($campo["Type"],"time") || stristr($campo["Type"],"decimal") )
				$input =  "<input type='text' class='form-control' id='".$campo["Field"]."' name='".$campo["Field"]."' >";
				elseif((stristr($campo["Type"],"int") || stristr($campo["Type"],"year")) && !stristr($campo["Type"],"point"))
				$input =  "<input type='number' class='form-control' id='".$campo["Field"]."' name='".$campo["Field"]."' >";
				elseif(stristr($campo["Type"],"text"))
				$input =  "<textarea id='".$campo["Field"]."' class='form-control' name='".$campo["Field"]."'></textarea>";
				else
				$input =  "<br><span class='small'> Formato desconocido para el campo <span class='label label-warning'>".$campo["Field"]." ".$campo["Type"]."</span></span>";
					
				$formhtml  .="<div class='form-group'>
				<label class='control-label' for='".$campo["Field"]."'>".$campo["Field"]." </label> ".$input."
				</div>";
		}
		
		return $formhtml;
	}
}

/* PRUEBAS */

//echo Control::gettables();
//echo Control::query("Select * from exampletable"); 

?>