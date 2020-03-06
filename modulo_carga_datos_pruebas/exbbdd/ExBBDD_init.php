<?php
set_time_limit(0);

//para mostrar errores php
error_reporting(E_ALL);
ini_set('display_errors', '1');
// end para mostrar errores php
include_once ("ExBBDD_config.php");
require_once ( "../Autoloader.php");
Autoloader::register(false, false);


// formato de fecha de carga Ymd
$fecha_carga = '20200304';
// $tabla = 'CLIENTES';
// $tabla = 'CUENTAS';
// $tabla = 'FACT_FACTURA';
// $tabla = 'P_ESTADOS_PARI';
// $tabla = 'P_PARI';
// $tabla = 'P_DEVOLUCIONES';
// $tabla = 'P_PAGOS';
// $tabla = 'P_PAGOS_NOPARI';
// $tabla = 'P_CARTERA';
// $tabla = 'INTERACCIONES_TIER';

// In parametros
$ambito = 'PERSONAL';
// $ambito = 'EMPRESA';

$ExBBDD_Model = new ExBBDD_Model_Carga();

echo 'se procesa la tabla ' . $tabla . '<br><br>';

$res = $ExBBDD_Model->get_datos_tabla($tabla, $fecha_carga, $ambito);

if($res){
    echo 'finalizo con éxito';
} else {
    echo 'finalizo con ERROR';
}

// Para la carga de los datos mediante sqlplus
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/CLIENTES.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/CUENTAS.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/FACT_FACTURA.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/P_ESTADOS_PARI.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/P_PARI.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/P_DEVOLUCIONES.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/P_PAGOS.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/P_PAGOS_NOPARI.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/P_CARTERA.sql
// sqlplus cobros02_wri/Temporal01@ACCONR.WORLD @/cobros_app_vol/AppServ/web/modulo_carga_datos_pruebas/exbbdd/files/INTERACCIONES_TIER.sql