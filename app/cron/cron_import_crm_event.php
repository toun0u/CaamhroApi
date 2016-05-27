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

    include_once(DIMS_APP_PATH."modules/system/class_pagination.php");
    include_once(DIMS_APP_PATH."modules/system/class_tiers.php");
	include_once(DIMS_APP_PATH."modules/system/class_workspace.php");
	include_once(DIMS_APP_PATH.'include/functions/system.php');
	include_once(DIMS_APP_PATH.'modules/system/include/functions.php');
	include_once(DIMS_APP_PATH.'include/class_campaign.php');
	include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
    include_once(DIMS_APP_PATH.'modules/system/class_tiers.php');
    include_once(DIMS_APP_PATH."modules/system/activity/class_activity.php");
    include_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';

    $id_workspace=0;
    $params = array();

    $res=$db->query("select * from dims_workspace WHERE id_workspace = 1 limit 0,1",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $id_workspace=$data['id'];

        }
    }

    $events_ref=array();
    $params = array();
    $params[':typeaction']	= dims_activity::TYPE_ACTION;

    $res=$db->query("select * from ".dims_activity::TABLE_NAME." WHERE typeaction= :typeaction ",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $events_ref[$data['ref']]=$data;

        }
    }

    $clients=array();
    $params = array();
    $res=$db->query("select c.erp_id,c.tiers_id,c.adr1 as adr,c.cp,c.ville,c.id_pays,t.id_globalobject as id_tiersgo from ".client::TABLE_NAME." as c inner join ".tiers::TABLE_NAME." as t on t.id=c.tiers_id",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $clients[$data['erp_id']]=$data;
        }
    }

    // selection des comptes utilisateurs
    $params = array();
    $cts_exists=array();
    $res=$db->query("SELECT distinct A_IDCRE FROM IND_DIMS_CRM_EVENT",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $cts_exists[$data['A_IDCRE']]=$data['A_IDCRE'];
        }
    }

    $users=array();
    $params = array();
    $res=$db->query("SELECT * from dims_user where erp_id in (".$db->getParamsFromArray($cts_exists, 'c', $params).")",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $users[$data['erp_id']]=$data;
        }
    }

    // on traite les types d'event
    $alltypes=array();
    $params = array();
    $res=$db->query("select * from ".activity_type::TABLE_NAME."",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $alltypes[$data['id']]=$data;
        }
    }

    //on charge eventuellement les nouveaux types
    $res=$db->query("select * from IND_DIMS_DICO_CRM_EVE",$params);

    if ($db->numrows($res)>0) {
        while ($data=$db->fetchrow($res)) {
            $type_act=new activity_type();
            if (!isset($alltypes[$data['PAC_EVENT_TYPE_ID']])) {
                $type_act->init_description();
                $type_act->fields['id']=$data['PAC_EVENT_TYPE_ID'];
                $type_act->fields['label']=$data['TYP_SHORT_DESCRIPTION'];
                $type_act->save();
                // on l'ajoute car on ne le connaissait pas
                $alltypes[$data['PAC_EVENT_TYPE_ID']]=$data;
            }
            else {
                $type_act->open($data['PAC_EVENT_TYPE_ID']);
                $type_act->fields['label']=$data['TYP_SHORT_DESCRIPTION'];
                $type_act->save();
                // on l'ajoute car on ne le connaissait pas
                $alltypes[$data['PAC_EVENT_TYPE_ID']]=$data;
            }
        }
    }

    // on parcourt maintenant tous les events
    $params = array();
    $res=$db->query("select * from IND_DIMS_CRM_EVENT",$params);

    if ($db->numrows($res)>0) {
        while ($data = $db->fetchrow($res)) {
            if (!isset($events_ref[$data['EVE_NUMBER']]) && $data['EVE_NUMBER'] != null) {
                // on ne connait pas cet event, on le cree
                if (isset($clients[$data['PAC_PERSON_ID']]) && isset($users[$data['A_IDCRE']])
                    && isset($alltypes[$data['PAC_EVENT_TYPE_ID']])) {
                    $act = new dims_activity();
                    $act->init_description();

                    $id_tiersgo=$clients[$data['PAC_PERSON_ID']]['id_tiersgo'];
                    $act->fields['ref'] = $data['EVE_NUMBER'];
                    $act->fields['tiers_id'] = $clients[$data['PAC_PERSON_ID']]['tiers_id'];
                    $act->fields['libelle'] = $data['EVE_SUBJECT'];
                    $act->fields['description'] = $data['EVE_TEXT'];
                    $act->fields['datejour'] = $data['EVE_DATE'];
                    $year=substr($act->fields['datejour'],0,4);
                    $month=substr($act->fields['datejour'],5,2);
                    $day=substr($act->fields['datejour'],8,2);

                    $act->fields['activity_type_id'] = $data['PAC_EVENT_TYPE_ID'];
                    $act->fields['id_module']=1;
                    $act->fields['id_user']=$users[$data['A_IDCRE']]['id'];
                    $act->fields['id_responsible']=$users[$data['A_IDCRE']]['id'];
                    $act->fields['id_workspace']=$id_workspace;
                    $act->fields['typeaction']=dims_activity::TYPE_ACTION;
                    $act->fields['type'] = dims_const::_PLANNING_ACTION_ACTIVITY;
                    $act->fields['timestp_modify']=$year.$month.$day."000000";
                    $act->fields['timestp_create']=$year.$month.$day."000000";

                    // partie adresse
                    $act->fields['address']=$clients[$data['PAC_PERSON_ID']]['adr'];
                    $act->fields['cp']=$clients[$data['PAC_PERSON_ID']]['cp'];
                    $act->fields['lieu']=$clients[$data['PAC_PERSON_ID']]['ville'];
                    $act->fields['id_country']=$clients[$data['PAC_PERSON_ID']]['id_pays'];

                    $act->save(dims_const::_SYSTEM_OBJECT_ACTIVITY);

                    // on cree la ligne dans la matrice
                    $m = new matrix();
                    $m->fields['id_activity']=$act->fields['id_globalobject'];
                    $m->fields['id_tiers']=$id_tiersgo;
                    $m->fields['id_country']=$clients[$data['PAC_PERSON_ID']]['id_pays'];;
                    $m->fields['year']=$year;
                    $m->fields['month']=intval($month);
                    $m->fields['timestp_modify']=$year.$month.$day."000000";
                    $m->fields['timestp_create']=$year.$month.$day."000000";
                    $m->save();

                }
            }

        }
    }


}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
?>
