<?php
define('AUTHORIZED_ENTRY_POINT', true);
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT']=='') $_SERVER['DOCUMENT_ROOT']=realpath(".")."/";
elseif ($_SERVER['DOCUMENT_ROOT']!='') {
	die('Web access error');
}

chdir(realpath('.')."/app");

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

include_once(DIMS_APP_PATH . "modules/system/class_dims.php");
include_once(DIMS_APP_PATH . "modules/system/class_workspace.php");

//Charge la class des gestions d'exceptions
require(DIMS_APP_PATH."include/class_exception.php");

try {
	// INITIALIZE DIMS OBJECT
	$dims = dims::getInstance();

	include_once DIMS_APP_PATH . 'include/errors.php';

	// load DIMS global classes
	include_once DIMS_APP_PATH . 'include/class_dims_data_object.php';

	// initialize DIMS
	include_once DIMS_APP_PATH . 'include/global.php';			// load dims global functions & constants
	include_once DIMS_APP_PATH . 'modules/system/class_module.php';
	/**
	* Database connection
	*
	* Don't forget to param db connection in DIMS_APP_PATH/config.php
	*/
	if (file_exists(DIMS_APP_PATH . 'include/db/class_db_'._DIMS_SQL_LAYER.'.php')) include_once DIMS_APP_PATH . 'include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	$db = dims::getInstance()->getDb();

	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

	$dims->setDb($db);

	//////////////////////////////////////////////////////////////////////////////////////
	// boucle sur l'ensemble des modules pour execution de la maj des sitemap par module
	//////////////////////////////////////////////////////////////////////////////////////
	$resu=$db->query("select m.*,mt.label as labeltype from dims_module as m inner join dims_module_type as mt on m.id_module_type=mt.id");
	if ($db->numrows($resu)>0) {
		while ($mod=$db->fetchrow($resu)) {
			// test si fichier sitemap.php existe

			if (file_exists(DIMS_APP_PATH . "modules/".$mod['labeltype']."/sitemap.php")) {
				ob_start();
				$moduleid=$mod['id'];

				include(DIMS_APP_PATH . "modules/".$mod['labeltype']."/sitemap.php");
				$contentsitemap = ob_get_contents();
				@ob_end_clean();

				// update current module
				$modu = new module();
				$modu->open($mod['id']);
				$modu->fields['sitemap']=$contentsitemap;
				$modu->save();
			}
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////
	// boucle sur les workspaces pour fusion des sitesmap module pour chaque workspace
	//////////////////////////////////////////////////////////////////////////////////////
	$resu=$db->query("select m.id,m.sitemap,mw.id_module,mw.id_workspace from dims_module_workspace as mw inner join dims_module as m on m.id=mw.id_module order by id_workspace asc");
	$curworkspace=0;
	$content="";
	$nbmodule=0;

	if ($db->numrows($resu)>0) {
		while ($mod=$db->fetchrow($resu)) {
			if ($curworkspace!=$mod['id_workspace']) {
				if ($curworkspace>0) {
					// on ajoute le contenu courant dans le workspace
					$work = new workspace();
					$work->open($curworkspace);
					echo $curworkspace." ".$nbmodule. " ".strlen($content)."<br>";
					$work->fields['sitemap']=$content;
					$work->save();
					$nbmodule=0;
				}
				$content="";
				$curworkspace=$mod['id_workspace'];
			}
			// ajout du sitemap courant
			if ($mod['sitemap']!="") {
				if ($content!="") $content.="\r\n";
				$content.=$mod['sitemap'];
				echo "Module ".$mod['id']." => ".strlen($mod['sitemap'])."<br>";
				$nbmodule++;
			}
		}

		// traitement du dernier cas
		if ($curworkspace>0) {
			// on ajoute le contenu courant dans le workspace
			$work = new workspace();
			$work->open($curworkspace);
			$work->fields['sitemap']=$content;
			$work->save();
		}
	}
}

catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
?>
