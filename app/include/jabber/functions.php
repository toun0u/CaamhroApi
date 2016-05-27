<?php
require_once("../../config.php");
require_once DIMS_APP_PATH . '/include/default_config.php'; // load config (mysql, path, etc.)
//require_once("../include/header.php");
require_once(DIMS_APP_PATH . "/include/class_dims_data_object.php");
require_once(DIMS_APP_PATH . "/include/functions/date.php");
require_once(DIMS_APP_PATH . "/include/functions/filesystem.php");
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");
require_once(DIMS_APP_PATH . "/modules/doc/include/global.php");
require_once(DIMS_APP_PATH . "/modules/doc/class_docfilehistory.php");


function getiduser($user) {
	$id = array();
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
		$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	$rs=$db->query("select distinct id from dims_user where jabberId = :idjabber", array(':idjabber' => array('type' => PDO::PARAM_INT, 'value' => $user)));

	if ($db->numrows($rs)>0) {
		if ($f=$db->fetchrow($rs)) {
			$id_user=$f['id'];
		}
	}
	return $id_user;
}

function getidworkspaces($id_user) {

	$workspaces = array();
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);

	$select =	"
					SELECT		dims_workspace.id,
					FROM		dims_workspace
					INNER JOIN	dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
					WHERE		dims_workspace_user.id_user = :iduser
					";

	$result = $db->query($select, array(':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user)));

	while ($fields = $db->fetchrow($result)) {
		$workspaces = $fields['id'];
	}

	return $workspaces;

}

function getgroupsadmin($id_user) {
	$groups = array();
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
        $db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);


	$select =	"
					SELECT		dims_group_user.id_group as idgroup,
								dims_group.*,
								dims_group.depth as globaldepth
					FROM		dims_group_user
					LEFT JOIN	dims_group
					ON			dims_group_user.id_group = dims_group.id
					WHERE		dims_group_user.id_user = :iduser
					ORDER BY	globaldepth ASC
					";

	$result = $db->query($select, array(':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user)));

	while ($fields = $db->fetchrow($result)) {
		if ($fields['idgroup'] == 0) $fields['label'] = "SYSTEME";
		if ($fields['id_group']==0) $fields['globaldepth']=0;
		$groups[$fields['idgroup']] = $fields;
	}

	return $groups;
}

function getworkspaces($id_user) {

	$workspaces = array();
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);


	// get organisation groups
	// on r�cup�re l'ensemble des groupes d'utilisateurs et leurs parents
	$groups = getgroupsadmin($id_user);

	if (sizeof($groups)) {
		$parents = array();

		foreach($groups as $org) {
			$parents = array_merge($parents,explode(';',$org['parents']));
			$parents[] = $org['id'];
		}

		$groups = array_keys(array_flip($parents));
		$groupparam = array();

		$select = '
			SELECT		dims_workspace.*,
						dims_workspace_group.adminlevel,
						dims_workspace_group.id_profile
			FROM		dims_workspace
			LEFT JOIN	dims_workspace_group ON dims_workspace_group.id_workspace = dims_workspace.id
			WHERE		dims_workspace_group.id_group IN ('.$db->getParamsFromArray($groups, 'idgroup', $groupparam).')
			';

		$result = $db->query($select, $groupparam);

		while ($fields = $db->fetchrow($result, $groupparam)) {
			$workspaces[$fields['id']] = $fields;
		}
	}

	// get workspaces
	// rattachement classique entre 1 utilisateur et 1 groupe de travail
	$select =	"
					SELECT		dims_workspace.*,
								dims_workspace_user.adminlevel,
								dims_workspace_user.id_profile
					FROM		dims_workspace
					INNER JOIN	dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
					WHERE		dims_workspace_user.id_user = :iduser
					ORDER BY	dims_workspace.depth, id
					";

	$result = $db->query($select);

	while ($fields = $db->fetchrow($result, array(':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user)))) {
		$workspaces[$fields['id']] = $fields;
	}

	return $workspaces;
}



/**
 * Recherche dans dims les infos accessible pour un user donné
 * @param $user
 * @param $chaine
 * @return tableau d'éléments (script, label et nombre trouvé)
 */
function research($user, $chaine)
{
	$tab_elem = array();
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	// TODO les fonctions dims_sql_filter et dims_convertaccents n existe pas dans les fichiers importés..
	$rs=$db->query("select distinct id,word from dims_keywords where ucase(word) like :keyword", array(':keyword' => array('type' => PDO::PARAM_STR, 'value' => $chaine)));

	if ($db->numrows($rs)>0) {
		if ($f=$db->fetchrow($rs)) {
			$key=$f['id'];
		}

		// on recherche maintenant les correspondances sur ce que je peux voir
		$rs=$db->query("select distinct id from dims_user where jabberId like :user", array(':user' => array('type' => PDO::PARAM_STR, 'value' => $user)));

		if ($db->numrows($rs)>0) {
			if ($f=$db->fetchrow($rs)) {
				$id_user=$f['id'];
			}
		}

                // nouvelle recherche (on cherche les documents uniquement)
		$sql="select distinct	mdf.id, mdf.name, mdf.description, mdf.extension, mdf.version, mdf.version, mdf.size
			from		dims_module_type mt, dims_mod_doc_file mdf, dims_keywords_index
			inner join	dims_mb_object as mbo
			on		mbo.id=id_object

			and		mbo.id_module_type=dims_keywords_index.id_module_type
			where		mbo.id_module_type = mt.id
			and		mt.label = 'doc'
			and		id_keyword = :idkeyword";

		$rs=$db->query($sql, array(':idkeyword' => array('type' => PDO::PARAM_INT, 'value' => $key)));
	}
	return $rs;
}

function sendFile($user, $id_doc)
{

	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);

	 // on recherche maintenant les correspondances sur ce que je peux voir
         $rs=$db->query("select distinct id from dims_user where jabberId like :user", array(':user' => array('type' => PDO::PARAM_STR, 'value' => $user)));
         if ($db->numrows($rs)>0) {
		if ($f=$db->fetchrow($rs)) {
			$id_user=$f['id'];
                }
         }


	// TODO : verifier que la personne a bien le droit d'acces au fichier !
	$doc = new docfile();
	if ($doc->open($id_doc))
	{
		$docpath = "../"._DIMS_WEBPATHDATA._DIMS_SEP.$doc->getfilepath();

		$newpath = intercom::getTransfertpath()._DIMS_SEP.$doc->fields["md5id"].".".$doc->fields["extension"];

		shell_exec(escapeshellcmd("cp ".escapeshellarg($docpath)." ".escapeshellarg($newpath)));

		$url = "http://".intercom::getServername()._DIMS_SEP.$doc->fields["md5id"].".".$doc->fields["extension"];
		$id  = $id_doc;
		$elem=array();
		$elem['name'] = $doc->fields["name"];
		$elem['id'] = $id;
		$elem['url'] = $url;
        }


	return $elem;
}

function findIntitule ($compteur, $type) {
	switch ($type) {
		case "Contact":
			return 0;
		break;

		case "ContenuTicket":
			return 0;
		break;

		case "Document":
			return 0;
		break;

		case "NavEspaceProject":
			switch($compteur) {
				case 0:
					return "";
				break;

				case 1:
					return "";
				break;
			}
		break;

		case "New":
			switch($compteur) {
				case 0:
					return "article";
				break;

				case 1:
					return "doc";
				break;

				case 2:
					return "action";
				break;
			}
		break;

		case "Presence":
			return 0;
		break;

		case "Ticket":
			return 0;
		break;
	}
}

?>
