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
	elseif(isset($_POST["query"]) && isset($_POST["sql"]) )
	{ 
		echo Control::query($_POST["sql"]);
		exit();
	}
	elseif(isset($_POST["editreg"])  && isset($_POST["field"])  && isset($_POST["val"]) && isset($_POST["table"]) )
	{ 
		echo Control::editreg($_POST["field"], $_POST["val"],$_POST["table"]);
		exit();
	}
	elseif(isset($_POST["insertreg"]) && isset($_POST["table"])  )
	{ 
		echo Control::insertreg($_POST["table"]);
		exit();
	}
	else
	{
		header("HTTP/1.0 404 Not Found");
		exit();
	}
}
else //fallo en la autentificación; mostramos un error 404
{
	header("HTTP/1.0 404 Not Found");
	exit();
}


?>