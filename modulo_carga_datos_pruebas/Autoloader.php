<?php

//Incluimos las constantes de estados
require_once ( dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'redom_constants.php');
// AppServ\web\includes\redom_constants.php
/**
 * Carga de forma automática en el path de la aplicación las clases que se encuentren en el mismo directorio/subdirectorio
 * @author dfdoce
 *
 */
class Autoloader
{

    public static $MODULE_APP_LIB = '';
	public static $config_script = false;
	
	/**
	 * Registra en el pool de clases a cartar para ser incluidas en el path
	 * @param string $script
	 * @param string $prepend
	 */
	public static function register($script = false, $prepend = false)
    {
		self::$MODULE_APP_LIB = defined('MODULE_APP_LIB') ? MODULE_APP_LIB : dirname(__FILE__) . DIRECTORY_SEPARATOR ;
		// var_dump(self::$MODULE_APP_LIB);
        if(!defined('MODULE_APP_LIB')) define('MODULE_APP_LIB', self::$MODULE_APP_LIB);
       	self::$config_script  = $script;
       	
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        }
        
    }

	/**
	 * Lee automaticamente las clases pertenecientes al modulo
	 * @param unknown $class
	 * @return boolean
	 */
    public static function autoload($class)
	{
		if ((class_exists($class))) {
    		return false;
    	}
    	$class_name = explode('_', strtolower($class));
    	array_splice($class_name, -1);
		$path_class_name = implode(DIRECTORY_SEPARATOR, $class_name);
		$path_class_name = self::$MODULE_APP_LIB . $path_class_name . DIRECTORY_SEPARATOR . $class . '.php';
		// var_dump($path_class_name);
    	if ((file_exists($path_class_name) === false) || (is_readable($path_class_name) === false)) {
    		return false;
    	}
    
    	require($path_class_name);
    }
    
 
}
