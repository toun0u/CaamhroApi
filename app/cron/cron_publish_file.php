<?php
define('AUTHORIZED_ENTRY_POINT', true);
ob_start();
session_start();

chdir(dirname($argv[0]));
chdir('../www');

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

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

	include_once(DIMS_APP_PATH."modules/system/class_workspace.php");
	include_once(DIMS_APP_PATH.'include/functions/system.php');
	include_once(DIMS_APP_PATH.'modules/system/include/functions.php');
	include_once(DIMS_APP_PATH.'include/class_campaign.php');
	include_once(DIMS_APP_PATH.'modules/wce/include/classes/class_article.php');
	include_once(DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block.php');

	$regexp = '/src="('.str_replace(array('.','/'),array('\.','\/'),_DIMS_WEBPATHDATA).'(doc-[0-9]*\/[0-9]{8}\/)([0-9]+_[0-9]*\.[a-zA-Z]{2,3}))"/';
	$lstDoc = array();
	// On récupère tous les doc présents dans les articles
	$sel = "SELECT 	*
			FROM 	".wce_article::TABLE_NAME;
	foreach($db->query($sel) as $r){
		for($i=1;$i<=19;$i++){
			if(preg_match_all($regexp, $r["content$i"], $matches) !== false){
				for($o=0;$o<count($matches[0]);$o++){
					$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
													'path'=>$matches[2][$o],
													'filename'=>$matches[3][$o]);
				}
			}
			if(preg_match_all($regexp, $r["draftcontent$i"], $matches) !== false){
				for($o=0;$o<count($matches[0]);$o++){
					$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
													'path'=>$matches[2][$o],
													'filename'=>$matches[3][$o]);
				}
			}
		}
	}
	// On récupère tous les doc présents dans les blocs d'article
	$sel = "SELECT 	*
			FROM 	".wce_block::TABLE_NAME;
	foreach($db->query($sel) as $r){
		for($i=1;$i<=19;$i++){
			if(preg_match_all($regexp, $r["content$i"], $matches) !== false){
				for($o=0;$o<count($matches[0]);$o++){
					$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
													'path'=>$matches[2][$o],
													'filename'=>$matches[3][$o]);
				}
			}
			if(preg_match_all($regexp, $r["draftcontent$i"], $matches) !== false){
				for($o=0;$o<count($matches[0]);$o++){
					$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
													'path'=>$matches[2][$o],
													'filename'=>$matches[3][$o]);
				}
			}
		}
	}
	// On parcour le www/data pour savoir ce qui est déjà publié
	$wwwData = DIMS_APP_PATH."www/data/";
	$data = opendir($wwwData) or die('Not open '.$wwwData);
	while($doc = @readdir($data)) {
		if(is_dir($wwwData.$doc) && $doc != '.' && $doc != '..' && substr($doc,0,4) == 'doc-') {
			$docDir = opendir($wwwData.$doc) or die('Not open '.$wwwData.$doc);
			while($date = @readdir($docDir)) {
				if(is_dir($wwwData.$doc."/".$date) && $doc != '.' && $doc != '..'){
					$dateDir = opendir($wwwData.$doc."/".$date) or die('Not open '.$wwwData.$doc."/".$date);
					while($file = @readdir($dateDir)) {
						$fe = explode('.',$file);
						if(is_file($wwwData.$doc."/".$date."/".$file) && $file != '.' && $file != '..' && count($fe)>=2 && substr($fe[count($fe)-2],-5) != '_mini'){
							if(isset($lstDoc[_DIMS_WEBPATHDATA.$doc."/".$date."/".$file])){
								// ça ne sert à rien de copier 2 fois le même fichier
								// TODO : à commenter si besoin de faire des updates de fichiers
								unset($lstDoc[_DIMS_WEBPATHDATA.$doc."/".$date."/".$file]);
							}else{
								// Supprime le doc, plus utilisé
								unlink($wwwData.$doc."/".$date."/".$file);
							}
						}
					}
					closedir($dateDir);
				}
			}
			closedir($docDir);
		}
	}
  	closedir($data);

	foreach($lstDoc as $doc){
		if(!file_exists(DIMS_APP_PATH."www/data/".$doc['path']))
			dims_makedir(DIMS_APP_PATH."www/data/".$doc['path']);
		if(file_exists(DIMS_APP_PATH."data/".$doc['path'].$doc['filename']) && !file_exists(DIMS_APP_PATH."www/data/".$doc['path'].$doc['filename']))
			copy(DIMS_APP_PATH."data/".$doc['path'].$doc['filename'],DIMS_APP_PATH."www/data/".$doc['path'].$doc['filename']);
	}

}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
?>