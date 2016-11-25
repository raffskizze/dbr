<?php
namespace dbr; 

require_once("controlclass.php");

/* Comprobamos si el usuario esta autentificado para mostrar el sistema DBR //use \younamespace\authmethod() */
if(TRUE)
{
	if(isset($_POST["gettables"]))
	{ 
		echo Control::gettables();
		exit();
	}

}
else //fallo en la autentificación; mostramos un error 404
{
	header("HTTP/1.0 404 Not Found");
	exit();
}


?>