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

	include_once(DIMS_APP_PATH."modules/system/class_workspace.php");
	include_once(DIMS_APP_PATH.'include/functions/system.php');
	include_once(DIMS_APP_PATH.'modules/system/include/functions.php');
	include_once(DIMS_APP_PATH.'include/class_campaign.php');
	include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
	include_once(DIMS_APP_PATH.'modules/system/class_tag.php');
	include_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');

	/* Gestion des tags temporels
	Pour tous ceux dont la date de création excéde 1an, on enlève le tag
	*/
	$sel = "SELECT 		m.*
			FROM 		".matrix::TABLE_NAME." m 
			INNER JOIN 	".tag::TABLE_NAME." t 
			ON 			t.id = m.id_tag 
			WHERE 		t.type = :type
			AND 		m.timestp_end = 0
			AND 		(m.year = :year
			AND 		m.month <= :month)
			OR 			m.year < :year2";
	$params = array(
		':year'=>array('value'=>(date('Y')-1),'type'=>PDO::PARAM_INT),
		':year2'=>array('value'=>(date('Y')-1),'type'=>PDO::PARAM_INT),
		':month'=>array('value'=>date('m'),'type'=>PDO::PARAM_INT),
		':type'=>array('value'=>tag::TYPE_DURATION,'type'=>PDO::PARAM_INT),
	);
	$res = $db->query($sel,$params);
	while($r = $db->fetchrow($res)){
		$m = new matrix();
		$m->openFromResultSet($r);
		$m->set('timestp_end',dims_createtimestamp());
		$m->save();

		$lk = tag_globalobject::find_by(array(
			'id_tag'=>$m->get('id_tag'),
			'id_globalobject'=>(($m->get('id_contact') > 0)?$m->get('id_contact'):$m->get('id_tiers')),
		),null,1);
		if(!empty($lk)){
			$lk->delete();
		}
	}
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
?>
