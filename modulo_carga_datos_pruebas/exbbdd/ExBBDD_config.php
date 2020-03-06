<?php
/*Configuraciùn general para todo el aplicativo

-Datos de conexiùn a la base de datos
-Rutas generales de la aplicaciùn
-Activar/Desactivar errores de la base de datos
-Iniciar sesiùn (datos en variable USER)
-Conexiùn a la base de datos
-Datos de conexiùn al fileSystem de la aplicaciùn
*/
$foot="";
class object{};

$DB=new object;
$CFG=new object;

#Constantes para entorno
define('HOST_PRO', 'sitamap');
define('HOST_PRE', 'sitamr');

$CFG->wwwroot = "http://10.132.72.113:4000";
//Rutas generales de la aplicaciùn
if(!isset($_SERVER['WINDIR'])) {
	//Datos de conexiùn a la base de datos
	$CFG->dbhost			= false;
	$CFG->dbname			= "ACCONR.WORLD";
	$CFG->dbuser			= "cobros01_wri";
	$CFG->dbpass			= "temporal01";
	$CFG->dirroot			= "/cobros_app_vol/AppServ/web";
	
	$CFG->log				= "logs";
} else {
	$CFG->dbhost			= false;
	$CFG->dbname			= "ACCONR.WORLD";
	$CFG->dbuser			= "cobros02_wri";
	$CFG->dbpass			= "temporal01";

	$CFG->log				= "logs";
	
	if(is_dir("D:/AppServ/web")) {
		$CFG->dirroot		= "D:/AppServ/web";
	} else {
		$CFG->dirroot		= "C:/AppServ/web";
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
    die("ERROR: La BBDD de ACCON est· caida".chr(13)."No esposible acceder a la aplicaciÛn.");
} 
$DB->SetFetchMode(ADODB_FETCH_ASSOC);
//*******************ConexiÛn-PRO*******************//
$CFG->pro=true;
$dbhost_pro="10.132.7.128:1531";
$dbname_pro="SITAMP";
$dbuser_pro="COBROS03_REA";
$dbpass_pro="Temporal01";

@$DBpro=NewADOConnection('oci8');
if(!@$DBpro->Connect($dbhost_pro,$dbuser_pro,$dbpass_pro,$dbname_pro)){
	die("ERROR: La BBDD de ACCON est· caida".chr(13)."No esposible acceder a la aplicaciÛn.");
}
$DBpro->SetFetchMode(ADODB_FETCH_ASSOC);
//*******************ù+++++++++++++ù*******************//


//*******************ConexiÛn-siebelx*******************//
$CFG->siebelx=true;
$dbhost_siebelx="10.132.6.85:1536";
$dbname_siebelx="siebelx";
$dbuser_siebelx="accon_s7x";
$dbpass_siebelx="sApoHryJc8";

@$DBsiebelx=NewADOConnection('oci8');
$DBsiebelx->SetFetchMode(ADODB_FETCH_ASSOC);
if(!@$DBsiebelx->Connect($dbhost_siebelx,$dbuser_siebelx,$dbpass_siebelx,$dbname_siebelx)){
	die("ERROR: La BBDD de ACCON est· caida".chr(13)."No esposible acceder a la aplicaciÛn.");
}
//*******************ù+++++++++++++ù*******************//
/***HORA DEL SERVIDOR WEB***/

date_default_timezone_set('Europe/Madrid');


/***********************************/
function PintaArray($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


?>