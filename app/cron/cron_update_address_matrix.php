<?php
define('AUTHORIZED_ENTRY_POINT', true);
ob_start();
session_start();
chdir(dirname($argv[0]));
chdir('..');

require_once '../config.php'; // load config (mysql, path, etc.)
require_once './include/default_config.php'; // load config (mysql, path, etc.)


if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT']=='') $_SERVER['DOCUMENT_ROOT']=realpath(".")."/";
elseif ($_SERVER['DOCUMENT_ROOT']!='') {
	die('Web access error');
}

include_once(DIMS_APP_PATH."modules/system/class_dims.php");
include_once(DIMS_APP_PATH."include/class_debug.php");

/*	Pat - 24/12/2006 */
ini_set('max_execution_time',0);
ini_set('memory_limit',"1512M");

//Charge la class des gestions d'exceptions
require(DIMS_APP_PATH."include/class_exception.php");

try {
	//chdir (dirname($_SERVER['SCRIPT_FILENAME']));

	include_once DIMS_APP_PATH.'include/errors.php';
	include_once DIMS_APP_PATH.'include/class_timer.php' ;

	// execution timer
	$dims_timer = new timer();
	$dims_timer->start();

	// load DIMS global classes
	include_once DIMS_APP_PATH.'include/class_dims_data_object.php';
	include_once DIMS_APP_PATH.'include/class_user_action_log.php' ;

	// initialize DIMS
	include_once DIMS_APP_PATH.'include/global.php';		// load dims global functions & constants

	/**
	* Database connection
	*
	* Don't forget to param db connection in DIMS_APP_PATH/config.php
	*/

	if (file_exists(DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) {
		include DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	}
	// INITIALIZE DIMS OBJECT
	$dims = new dims();

	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

	$dims->setDb($db);

	$dims->loadHeader();

    $_DIMS['cste']=$dims->loadLanguage();
    dims::setInstance($dims);
    $dims->init_metabase();

	include_once(DIMS_APP_PATH."modules/system/class_workspace.php");
	include_once(DIMS_APP_PATH.'include/functions/system.php');
	include_once(DIMS_APP_PATH.'modules/system/include/functions.php');
	include_once(DIMS_APP_PATH.'include/class_campaign.php');
	include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
    include_once(DIMS_APP_PATH.'modules/system/class_address.php');

    $departements=array();
    $res=$db->query("select * from dims_departement");

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $departements[$data['code']]=$data;
        }
    }

    /* on pretraite les villes car elles n'ont pas d'info de rattachement */
    $sel = "SELECT 		*
			FROM 		".city::TABLE_NAME." where cp>0";

    $params=array();
    $res = $db->query($sel,$params);
    $cpte=0;

    while($r = $db->fetchrow($res)){
        $c = new city();
        $c->openFromResultSet($r);

        if ($c->fields['cp']!="" && strlen($c->fields['cp'])>=5 && $c->fields['code_dep']=="") {
            // on doit remettre les bons codes
            if (isset($departements[substr($c->fields['cp'],0,2)])) {
                $depcour=$departements[substr($c->fields['cp'],0,2)];
                if (isset($depcour['code']) && $depcour['code']!="" && isset($depcour['code_reg']) && $depcour['code_reg']!="") {
                    $c->fields['code_dep']=$depcour['code'];
                    $c->fields['code_reg']=$depcour['code_reg'];
                    $c->save();
                    $cpte++;
                }

            }

        }


    }

	/* On va tout simplement ouvrir les adresses afin de faire un save

    */
	$sel = "SELECT 		m.*
			FROM 		".matrix::TABLE_NAME." m where m.id_city>0 and m.id_departement=0
			";
	$params=array();
	$res = $db->query($sel,$params);
	while($r = $db->fetchrow($res)){
		$m = new matrix();
		$m->openFromResultSet($r);
		$m->save();

	}
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
?>
