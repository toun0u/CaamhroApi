<?php
date_default_timezone_set('Europe/Paris');
setlocale (LC_ALL, "fr_FR");

if( ! defined('ROOT_PATH') )				define('ROOT_PATH', dirname(__FILE__).'/..');
if( ! defined('APP_PATH') )					define('APP_PATH', ROOT_PATH . '/app');
//if( ! defined('_TOKEN_LIFE_MINUTE') )		define('_TOKEN_LIFE_MINUTE', 1);
if( ! defined('_TOKEN_LIFE_MINUTE') )		define('_TOKEN_LIFE_MINUTE', 259200);

function include_dir($path){
	if(!empty($path) && file_exists($path) && is_dir($path)){
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if(pathinfo($entry, PATHINFO_EXTENSION) == 'php'){//en chopan le file info on a juste text/plain
					include_once $path.'/'.$entry;
				}
			}
		}
	}
}

define('AUTHORIZED_ENTRY_POINT', true);
define('DIMS_APP_PATH', ROOT_PATH . '/../');
define('_DIMS_PATHDATA', realpath(ROOT_PATH . '/../../data').'/');
define('DIMS_WEB_PATH', realpath(ROOT_PATH . '/../..').'/');
define('DIMS_ROOT_PATH', realpath(ROOT_PATH . '/../..').'/');

require_once ROOT_PATH . '/../../config.php'; // load config (mysql, path, etc.)
require_once ROOT_PATH . '/../include/default_config.php'; // load config (mysql, path, etc.)

if(!defined('CRYPTO_COST')){
	if(defined('_DIMS_SECURITY_CRYPTO_COST')){
		define ('CRYPTO_COST',_DIMS_SECURITY_CRYPTO_COST);
	}else{
		define ('CRYPTO_COST',10);
	}
}
if(!defined('CRYPTO_SALT')){
	if(defined('_DIMS_SECURITY_CRYPTO_SALT')){
		define ('CRYPTO_SALT',_DIMS_SECURITY_CRYPTO_SALT);
	}else{
		define ('CRYPTO_SALT','Pl.FGi/55Cr');
	}
}

ini_set('max_execution_time',0);
ini_set('memory_limit',"1512M");

require_once(DIMS_APP_PATH."modules/system/class_dims.php");
require_once(DIMS_APP_PATH."include/class_debug.php");
require_once(DIMS_APP_PATH."include/class_exception.php");
require_once DIMS_APP_PATH.'include/errors.php';
require_once DIMS_APP_PATH.'include/class_timer.php' ;

require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'include/class_user_action_log.php' ;
require_once DIMS_APP_PATH.'include/global.php';		// load dims global functions & constants

if (file_exists(DIMS_APP_PATH.'include/db/class_db_'._DIMS_SQL_LAYER.'.php')) {
	require DIMS_APP_PATH.'include/db/class_db_'._DIMS_SQL_LAYER.'.php';
}
// INITIALIZE DIMS OBJECT
$dims = new dims();

$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

$dims->setDb($db);

$dims->loadHeader();
$_DIMS['cste']=$dims->loadLanguage();
dims::setInstance($dims);

dims::setInstance($dims);
$dims->init_metabase();
require_once(DIMS_APP_PATH."modules/system/class_workspace.php");
require_once(DIMS_APP_PATH.'include/functions/system.php');
require_once(DIMS_APP_PATH.'modules/system/include/functions.php');
require_once(DIMS_APP_PATH.'include/class_campaign.php');
require_once(DIMS_APP_PATH.'modules/doc/class_docfile.php');
require_once(DIMS_APP_PATH.'modules/system/class_indexation.php');
