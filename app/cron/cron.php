<?php
define('AUTHORIZED_ENTRY_POINT', true);
// add the cron.php script into your cron table
// ie:	* * * * * /usr/local/bin/php -f /home/faf/public_html/dims_redist/cron.php >/dev/null 2>&1
session_start();

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT']=='') $_SERVER['DOCUMENT_ROOT']=realpath(".")."/";
elseif ($_SERVER['DOCUMENT_ROOT']!='') {
	die('Web access error');
}
chdir(dirname($argv[0]));
chdir('..');

include_once(DIMS_APP_PATH."modules/system/class_dims.php");
include_once(DIMS_APP_PATH."include/class_debug.php");
include_once(DIMS_APP_PATH."modules/system/class_workspace.php");

/*	Pat - 24/12/2006 */
ini_set('max_execution_time',0);
ini_set('memory_limit',"1024M");

//Charge la class des gestions d'exceptions
require(DIMS_APP_PATH."include/class_exception.php");

try {
	// INITIALIZE DIMS OBJECT
	$dims = new dims();
	ob_start();
	session_start();

	//chdir (dirname($_SERVER['SCRIPT_FILENAME']));

	include_once DIMS_APP_PATH . '/include/errors.php';

	include_once DIMS_APP_PATH . '/include/class_timer.php' ;

	// execution timer
	$dims_timer = new timer();
	$dims_timer->start();

	// set default header
	if (DIMS_APP_PATH!="") include_once DIMS_APP_PATH . '/include/header.php';

	// load DIMS global classes
	include_once DIMS_APP_PATH . '/include/class_dims_data_object.php';
	include_once DIMS_APP_PATH . '/include/class_user_action_log.php' ;
	include_once DIMS_APP_PATH . '/include/class_param.php';
	include_once DIMS_APP_PATH . '/include/class_connecteduser.php';
	include_once DIMS_APP_PATH . '/include/functions/system.php';

	// initialize DIMS
	include_once DIMS_APP_PATH . '/include/global.php';			// load dims global functions & constants


	$dims->loadHeader();
	$execron=true;

	/**
	* Database connection
	*
	* Don't forget to param db connection in DIMS_APP_PATH/config.php
	*/
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) {
		include DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	}

	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

	$cron_rs = $db->query(	"
				SELECT		modu.id, modtype.label,modtype.id as idmodtype
				FROM		dims_module as modu,
							dims_module_type as modtype
				WHERE		modu.id_module_type = modtype.id
				");

	$_SESSION['dims']=array();
	$_SESSION['dims']['modules']=array();

	//dims_prepare_index();
	$tabmodules=array();
	while ($cron_fields = $db->fetchrow($cron_rs)) {
		$_SESSION['dims']['modules'][$cron_fields['id']]=array();
		$_SESSION['dims']['modules'][$cron_fields['id']]['id_module_type']=$cron_fields['idmodtype'];

		if (!isset($tabmodules[$cron_fields['idmodtype']])) {
			$tabmodules[$cron_fields['idmodtype']]=$cron_fields['idmodtype'];
			$cronfile = DIMS_APP_PATH . "/modules/{$cron_fields['label']}/cron.php";

			$cron_moduleid = $cron_fields['id'];
			if (file_exists($cronfile)) {
				echo $cronfile."\n";ob_flush();
				include $cronfile;
			}
		}
	}
	unset($tabmodules);
	$time = round($dims_timer->getexectime(),3);
	$time = sprintf("%d",$time*1000);

	$sql_time = round($db->exectime_queries,3);
	$sql_time = sprintf("%d",$sql_time*1000);

	$sql_p100 = round(($sql_time*100)/$time,0);
	$php_p100 = 100 - $sql_p100;

	if ($dims_errors_level && _DIMS_MAIL_ERRORS && _DIMS_ADMINMAIL != '') echo mail(_DIMS_ADMINMAIL,"[{$dims_errorlevel[$dims_errors_level]}] sur [{$_SERVER['HTTP_HOST']}]", "$dims_errors_nb erreur(s) sur $dims_errors_msg\n\nDUMP:\n$dims_errors_vars");
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
?>
