<?
require_once("../../config.php");
require_once DIMS_APP_PATH . '/include/default_config.php'; // load config (mysql, path, etc.)
require_once(DIMS_APP_PATH . "/include/db/class_db_mysql.php");
require_once(DIMS_APP_PATH . "/include/jabber/functions.php");

/**
 * Cette classe permet de créer la structure de chaque message contenu dans messageStructure.xml
 *
 * Creator : JC
 * Juin 2010
 *
 */

/* Ce fichier contient la liste des requetes utilisees dans ce dossier intercom
 * Liste des fichiers à modifier en cas de changement de version de la base de DIMS :
 * 	-requetes.php
 *	-params.php
 *
 */

function quelleRequete($intitule, $elements, &$database) {
	$tableau = array();
	switch($intitule) {
		case "contact":
			return $database->query(requeteContact($elements["contactID"]));
		break;

		case "contenuTicket":
			return $database->query(requeteContenuTicket(getiduser($elements["from"])));
		break;

		case "document":
			return $database->query(requeteDocument(getiduser($elements["from"])));
		break;

		case "workspace":
			return $database->query(requeteNaveSpaceProjet("work", getiduser($elements["from"])));
		break;

		case "proj":
			return $database->query(requeteNaveSpaceProjet("proj", getiduser($elements["from"])));
		break;

		case "article":
			return $database->query(requeteNew("article", getiduser($elements["from"])));
		break;

		case "doc":
			return $database->query(requeteNew("doc", getiduser($elements["from"])));
		break;

		case "action":
			return $database->query(requeteNew("action", getiduser($elements["from"])));
		break;

		case "ticket":
			return $database->query(requeteTicket(getiduser($elements["from"])));
		break;

		case "line";
			return research($elements["from"], $elements["keyword"]);
		break;

		case "fichier" :
			return $database->query(requeteDownload($elements["ID_fichier"]));
		break;
	}
}
function requetePresence ($type, $id){
	switch ($type){
		case "select":
			return "select * from dims_jabber_connecteduser where jabberId like '$id'";
		break;

		case "update":
			return "update dims_jabber_connecteduser set state=1 where jabberId like '$id'";
		break;

		case "insert":
			return "insert into dims_jabber_connecteduser set state=1, jabberId ='$id', timestp='date(\"YmdHis\")'";
		break;

		case "delete":
			return "delete from dims_jabber_connecteduser where jabberId ='$id'";
		break;
	}
}

function requeteNew($type, $id){
	switch ($type){
		case "article":
			return "SELECT DISTINCT dmwa.id as ID, dmwa.title as name, dmwa.timestp_modify as date, du.firstname, du.lastname, dmwa.id_workspace as ID_workspace, dmwa.id_user
				FROM dims_mod_wce_article dmwa, dims_user du
				WHERE dmwa.id_user = du.id AND dmwa.id_workspace in (
					SELECT dims_workspace.id
					FROM 		dims_workspace
					INNER JOIN 	dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
					WHERE		dims_workspace_user.id_user = '$id')";
		break;

		case "doc" :
			return "SELECT DISTINCT dmdf.id as ID, dmdf.name, dmdf.timestp_create as date, du.firstname, du.lastname, dmdf.id_workspace as ID_workspace
				FROM dims_mod_doc_file dmdf, dims_user du
				WHERE dmdf.id_user = du.id AND dmdf.id_workspace in (
					SELECT 		dims_workspace.id
					FROM 		dims_workspace
					INNER JOIN 	dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
					WHERE		dims_workspace_user.id_user = '$id')";
		break;

		case "action" :
			return "SELECT DISTINCT dmba.id as ID, dmba.libelle as name, dmba.timestp_modify as date, du.firstname, du.lastname, dmba.id_workspace as ID_workspace
				FROM dims_mod_business_action dmba, dims_user du
				WHERE dmba.id_user = du.id AND dmba.id_workspace in (
					SELECT 		dims_workspace.id
					FROM 		dims_workspace
					INNER JOIN 	dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
					WHERE		dims_workspace_user.id_user = '$id')";
		break;
	}
}

function requeteNaveSpaceProjet($type, $id){
	switch ($type) {
		case "work":
			return "SELECT DISTINCT dw.id, dw.label
				FROM dims_workspace dw, dims_workspace_user dwu
				WHERE dwu.id_user = '$id' AND dw.id = dwu.id_workspace";
		break;

		case "proj":
			return "SELECT DISTINCT dp.id, dp.label, dp.id_workspace
				FROM dims_project dp, dims_workspace_user dwu
				WHERE dp.id_workspace IN (
					SELECT DISTINCT dw.id
					FROM dims_workspace dw, dims_workspace_user dwu
					WHERE dwu.id_user = '$id' AND dw.id = dwu.id_workspace)";
		break;
	}
}

function requeteDocument($id) {
	return "SELECT DISTINCT dmdf.id as ID, dmdf.name, dmdf.timestp_create as date, du.firstname, du.lastname, dmdf.extension, dmdf.id_workspace as ID_workspace
		FROM dims_mod_doc_file dmdf, dims_user du
		WHERE dmdf.id_user = du.id AND dmdf.id_workspace in (
		SELECT dims_workspace.id
			FROM 		dims_workspace
			INNER JOIN 	dims_workspace_user ON dims_workspace_user.id_workspace = dims_workspace.id
			WHERE		dims_workspace_user.id_user = '$id')";
}

function requeteTicket($id) {
	return "SELECT DISTINCT dt.id as ID, dt.title as name, dt.needed_validation as type, du.firstname , du.lastname, dt.timestp as date, dts.status as etat, dt.id_workspace as ID_workspace
		FROM dims_ticket dt, dims_ticket_dest dtd, dims_ticket_status dts, dims_user du
		WHERE du.id = '$id' AND du.id = dtd.id_user AND dtd.id_ticket = dt.id AND dtd.id_ticket = dts.id_ticket";
}


function requeteContenuTicket($id) {
	return "SELECT DISTINCT dt.title, dt.message
		FROM dims_ticket dt
		WHERE dt.id ='$id'";
}

function requeteContact($id){
	return "SELECT jabberId as jabberID, firstname, lastname, mobile, function, address, postalcode as cp, city
                FROM dims_user
                WHERE jabberId='$id'";
}

function requeteDownload($id) {
	return "SELECT id as id_fichier, name FROM dims_mod_doc_file WHERE id = '$id'";
}

function requeteURL($id) {
	$database = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	$res = $database->query("SELECT id, id_module, timestp_create, version, extension FROM dims_mod_doc_file WHERE id='$id'");
	$tab = mysql_fetch_array($res);
	$hostname = Params::HOSTNAME;

	return $hostname."/data/doc-".$tab['id_module']."/".substr($tab['timestp_create'], 0, 8)."/".$tab['id']."_".$tab['version'].".".$tab['extension'];
}
?>
