<?php 
namespace dbr; 

/* Globales */
require_once("confdbr.php");

/* Clase dbr mysqli */
class Dbrclass 
{ 
			/* identificador de conexión y consulta */
			public $IDcon = 0;
			
			public $IDquery = 0;
			
			public $Querytime = 0;
			
			public $Querytimemysql = 0;
			
			public $Treg = 0;
			
			/* número de error - texto error - consulta SQL */
			
			public $Errnum = 0;
			
			public $Error = "";
			
			public $SQL = ""; 
			
	/* Método Constructor: Cada vez que creemos una variable			
	de esta clase, se ejecutará esta función */			
	function __construct()
	{			 
				$this->BaseDatos = NOMBREBD;
				
				$this->Servidor = HOST;
				
				$this->Usuario = USERBD;
				
				$this->Clave = USERPWD; 
			
			$this->conectar();
	}
	
	/* Método destructor: */
	function __destruct()
	{
			$this->Con->close();
	}
	/*Conexión a la base de datos*/			
	function conectar()
	{			 
			/* conexion objeto de mysqli */
			$this->Con = new \mysqli($this->Servidor, $this->Usuario, $this->Clave, $this->BaseDatos); 
			$this->Con->set_charset("utf8");
			
			if (mysqli_connect_error()) {
			
				$this->Error = mysqli_connect_error();	
				$this->Errnum = mysqli_connect_errno();			
				return 0; 
			}
			  
			/* Si hemos tenido éxito conectando devuelve 
			el identificador de la conexión, sino devuelve 0 */
			return $this->IDcon = $this->Con->thread_id;
	}
	
	/* Ejecuta un consulta y devuelve TRUE si ha tenido exito o el numero de files devueltas y 0 FALSE si ocurrio un error */			
	function ejecutasql($sql)
	{
		$time_start = microtime(true);
		
		if(LOGS)
		$this->iniciaprofiles();
		
		if ($sql == "") 
			{			
				$this->Error = "No ha especificado una consulta SQL";
			
				return 0;
			}
			else
			$this->SQL = $sql;
			
			//ejecutamos la consulta
			if (!$this->Result = $this->Con->query($this->SQL)) { 
				$this->Error = $this->Con->error;	
				$this->Errnum = $this->Con->errno;
				return FALSE;
			}
		
		if(LOGS)
		$this->sacayresetprofiles(); 
		
			$time_end = microtime(true);
			$this->Querytime = $time_end - $time_start; 
			
			$this->IDquery = $this->Con->insert_id;
			
			if(  isset($this->Result->num_rows) && $this->Result->num_rows > 0) 
			{
				$this->Treg = $this->Result->num_rows;
				return  $this->Result->num_rows;
			}
			else
			{
				$this->Treg = 0; //la consulta se ejecuto pero no tiene registros
				return TRUE;
			}
	}
	
	/* Devuelve el número de campos de una consulta */
	function numcampos() 
	{
			return $this->Result->field_count;
	} 
	
	/* Devuelve el número de registros de una consulta */
	function numregistros()
	{
			return $this->Treg;
	}
	
	/* Devuelve el nombre de un campo de una consulta */
	function nombrecampo($numcampo) 
	{			 
			$numcampo--; //le restamos 1 por que empieza desde el indice 0
			$info_campo = $this->Result->fetch_field_direct($numcampo); 

			return $info_campo->name;
	}
	
	/* Devuelve la consulta en un array */
	function devuelvearray() 
	{			
			if($r=@$this->Result->fetch_assoc())
			{
				return $r; 
			}
			else
			return 0;				
	}	
	
	/* Devuelve el array completo que genera una consulta*/
	function devuelvearraycompleto() 
	{			
		$i = 0;
		
			while(@$valor = $this->Result->fetch_array() )
			{
				$array[$i] = $valor;	
				$i++;
			}
			
			if(isset($array))
				return $array;
			else
				return 0;		
	}
		
	/* Devuelve el array completo con los valores del campo especificado que ha devuelto la consulta*/
	function devuelvecampocompleto($campo) 
	{			
		$i = 0;
		
			while(@$valor = $this->Result->fetch_array() )
			{
				$array[$i] = $valor["$campo"];	
				$i++;
			}
			
			if(isset($array))
				return $array;
			else
				return 0;		
	}
	
	/* Devuelve el valor de un campo especificado*/
	function devuelvecampo($campo) 
	{			
			if(@$res = $this->Result->fetch_array())
			{
				return $res["$campo"]; 
			}
			else
			return 0;
	}
	
	/* Devuelve la ultima id autoincrement insertada */
	function devuelveid() 
	{			
			return 	$this->IDquery;			
	}
	
	/* Cierra el resultado existente*/
	function cerrarresultado() 
	{		
			$this->Result->free(); 
	}
	
	/* funciones que establecen y cierran los profiles mysql para recoger el tiempo de la consulta */
	function iniciaprofiles()
	{
		//creamos los perfiles
		$this->Con->query('SET profiling_history_size=1;');
		$this->Con->query('SET profiling=1'); 
	}
	
	function sacayresetprofiles()
	{
		//sacamos el tiempo del perfil mysql
		if ($res = $this->Con->query("SHOW profiles", MYSQLI_USE_RESULT)) {
			if($row = $res->fetch_row()) { 
				//print_r($row);
				$this->Querytimemysql =$row[1]; //viene en el segundo campo
			}
			$res->close();
		}
		 
		//remueve los perfiles
		$this->Con->query('SET profiling=0'); 
		$this->Con->query('SET profiling_history_size=0;');
	}
	
}



 
/* PRUEBAS */

?>