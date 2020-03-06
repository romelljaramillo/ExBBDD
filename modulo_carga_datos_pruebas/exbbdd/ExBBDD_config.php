<?php
/*Configuracin general para todo el aplicativo

-Datos de conexin a la base de datos
-Rutas generales de la aplicacin
-Activar/Desactivar errores de la base de datos
-Iniciar sesin (datos en variable USER)
-Conexin a la base de datos
-Datos de conexin al fileSystem de la aplicacin
*/
$foot="";
class object{};

$DB=new object;
$CFG=new object;

#Constantes para entorno
define('HOST_PRO', '');
define('HOST_PRE', '');

$CFG->wwwroot = "";
//Rutas generales de la aplicacin
if(!isset($_SERVER['WINDIR'])) {
	//Datos de conexin a la base de datos
	$CFG->dbhost			= ;
	$CFG->dbname			= "";
	$CFG->dbuser			= "";
	$CFG->dbpass			= "";
	$CFG->dirroot			= "";
	
	$CFG->log				= "logs";
} else {
	$CFG->dbhost			= false;
	$CFG->dbname			= "";
	$CFG->dbuser			= "";
	$CFG->dbpass			= "";

	$CFG->log				= "logs";
	
	if(is_dir("D:/AppServ/web")) {
		$CFG->dirroot		= "";
	} else {
		$CFG->dirroot		= "";
	}
}

$CFG->libdir				= "$CFG->dirroot/htdocs";
$CFG->privatedir			= "$CFG->dirroot/htdocs_privado";
$CFG->bbdddir				= "$CFG->privatedir/adodb";
$CFG->usercontrol			= "$CFG->privatedir/usuarios";

//Activar-Desactivar errores de la base de datos
$DB_DEBUG=true;
$DB_DIE_ON_FAIL=true;

require_once("$CFG->usercontrol/standarlib.php");
require_once("$CFG->usercontrol/controlUsuarios.php");
include_once("$CFG->bbdddir/adodb.inc.php"); //database abstraction library

 
$DB=NewADOConnection('oci8');
if(!@$DB->Connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname)){
    die("ERROR: La BBDD de ACCON está caida".chr(13)."No esposible acceder a la aplicación.");
} 
$DB->SetFetchMode(ADODB_FETCH_ASSOC);
//*******************Conexión-PRO*******************//
$CFG->pro=true;
$dbhost_pro="";
$dbname_pro="";
$dbuser_pro="";
$dbpass_pro="";

@$DBpro=NewADOConnection('oci8');
if(!@$DBpro->Connect($dbhost_pro,$dbuser_pro,$dbpass_pro,$dbname_pro)){
	die("ERROR: La BBDD de ACCON está caida".chr(13)."No esposible acceder a la aplicación.");
}
$DBpro->SetFetchMode(ADODB_FETCH_ASSOC);
//*******************+++++++++++++*******************//


//*******************Conexión-siebelx*******************//
$CFG->siebelx=true;
$dbhost_siebelx="";
$dbname_siebelx="";
$dbuser_siebelx="";
$dbpass_siebelx="";

@$DBsiebelx=NewADOConnection('oci8');
$DBsiebelx->SetFetchMode(ADODB_FETCH_ASSOC);
if(!@$DBsiebelx->Connect($dbhost_siebelx,$dbuser_siebelx,$dbpass_siebelx,$dbname_siebelx)){
	die("ERROR: La BBDD de ACCON está caida".chr(13)."No esposible acceder a la aplicación.");
}
//*******************+++++++++++++*******************//
/***HORA DEL SERVIDOR WEB***/

date_default_timezone_set('Europe/Madrid');


/***********************************/
function PintaArray($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


?>
