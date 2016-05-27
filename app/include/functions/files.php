<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function dims_getFiles($dims,$id_module,$id_object,$id_record,$order_virtual = false) {
	require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
	$db = $dims->getDb();

	$user= new user();
	$user->open($_SESSION['dims']['userid']);
	$lstworks=$user->getworkspaces();

	$lstworks=array_keys($lstworks);

	if (empty($lstworks)) {
		$lstworks[0]=0;
	}


	$params = array();
	$sql= "select		f.*,
						u.firstname,u.lastname
			from		dims_mod_doc_file as f
			INNER JOIN	dims_user u
			ON			f.id_user = u.id
			AND			id_module = :idmodule
			and			id_object = :idobject
			and			id_record = :idrecord
			and			id_workspace in (".$db->getParamsFromArray($lstworks, 'idworkspace', $params).")";

	$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
	$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_object);
	$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_record);

	$res=$db->query($sql, $params);
	$result=array();
	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
			// test si fichier existe
			$doc= new docfile();
			$doc->fields['id']=$f['id'];
			$doc->fields['timestp_create']=$f['timestp_create'];
			$doc->fields['timestp_create']=$f['timestp_create'];
			$doc->fields['id_module']=$f['id_module'];
			$doc->fields['version']=$f['version'];
			$doc->fields['extension']=$f['extension'];

			if (file_exists($doc->getfilepath())) {
				// generation du lien de téléchargement des docs
				$f['downloadlink']=dims_urlencode("admin-light.php?dims_op=doc_file_download&docfile_id=".$f['id']);
				if ($order_virtual){
					$gbo = new dims_globalobject();
					$gbo->open($f['id_globalobject']);
					$lst_link= $gbo->searchLink();
					if (count($lst_link[dims_const::_SYSTEM_OBJECT_DOCFOLDER]) > 0){
						foreach ($lst_link[dims_const::_SYSTEM_OBJECT_DOCFOLDER] as $id_gb){
							$gb_fold = new dims_globalobject();
							$gb_fold->open($id_gb);
							$result[$gb_fold->fields['id_record']]['doc'][] = $f;
						}
					}else{
						$result[0]['doc'][] = $f;
					}
				}else
					$result[]=$f;
			}
			/*else {
				$doc->delete(); // update pat
			}*/
		}

		return $result;
	} else return false;
}

function dims_createAddFileLink($id_module,$id_object,$id_record,$style='',$linkonly=false) {
	global $_DIMS;
	$_SESSION['dims']['uploadfile']=array();
	$_SESSION['dims']['uploadfile']['id_module']=$id_module;
	$_SESSION['dims']['uploadfile']['id_object']=$id_object;
	$_SESSION['dims']['uploadfile']['id_record']=$id_record;

	switch ($id_object){
		case dims_const::_SYSTEM_OBJECT_ACTION :
			require_once DIMS_APP_PATH . '/modules/system/class_action.php';
			$obj = new action();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_CONTACT :
			require_once DIMS_APP_PATH . '/modules/system/class_contact.php';
			$obj = new contact();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_NEWSLETTER :
			require_once DIMS_APP_PATH . '/modules/system/class_newsletter.php';
			$obj = new newsletter();
			$obj->open($id_record);
						if (isset($obj->fields['id_globalobject']))
							$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_TIERS :
			require_once DIMS_APP_PATH . '/modules/system/class_tiers.php';
			$obj = new tiers();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_MAIL :
			require_once DIMS_APP_PATH . '/modules/system/class_webmail_email.php';
			$obj = new webmail_email();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_DOCFILE :
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
			$obj = new docfile();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_RSS :
			require_once DIMS_APP_PATH . '/modules/rss/class_rssfeed.php';
			$obj = new rssfeed();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_WCE_OBJECT :
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_object.php';
			$obj = new wce_object();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case  dims_const::_SYSTEM_OBJECT_RSS_ARTICLE :
			require_once DIMS_APP_PATH . '/modules/rss/class_rsscache.php';
			$obj = new rsscache();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_WCE_ARTICLE :
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
			$obj = new article();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_WCE_MAILLING_SEND :
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_mailinglist_send.php';
			$obj = new mailinglist_send();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_WCE_MAILLING_MAIL :
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_mailinglist_mail.php';
			$obj = new mailinglist_mail();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
		case dims_const::_SYSTEM_OBJECT_DOCFOLDER :
			require_once DIMS_APP_PATH . '/modules/doc/class_docfolder.php';
			$obj = new docfolder();
			$obj->open($id_record);
			$_SESSION['dims']['uploadfile']['id_globalobject']=$obj->fields['id_globalobject'];
			break;
	}

	//$_SESSION['dims']['uploadfile']['url']=$_SERVER['SCRIPT_URI']."?".$_SERVER['QUERY_STRING'];
	if (strpos($_SERVER['SCRIPT_NAME'],'admin-light.php')>0) {
		$_SESSION['dims']['uploadfile']['url']= "/admin.php";
	}
	else {
		$_SESSION['dims']['uploadfile']['url']=$_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
	}

	if ($linkonly)
		return ("javascript:displayAddFiles(event,600);");
	else {
		?>

		<?
		$currentfolder=-1;
		return dims_create_button($_DIMS['cste']['_DOC_NEWFILE'],"plus","javascript:displayAddFiles(event,600);","addfile",$style,'');
	}
	//echo "<a href=\"javascript:void(0);\" onclick=\"displayAddFiles(event,600);\">".$_DIMS['cste']['_DOC_NEWFILE']."&nbsp;<img src=\"./common/img/add.gif\" alt=\"\"></a>";
}

function dims_createAddFileLinkProgress($id_module,$id_object,$id_record,$style='',$linkonly=false) {
	global $_DIMS;
	$_SESSION['dims']['uploadfile']=array();
	$_SESSION['dims']['uploadfile']['id_module']=$id_module;
	$_SESSION['dims']['uploadfile']['id_object']=$id_object;
	$_SESSION['dims']['uploadfile']['id_record']=$id_record;

	//$_SESSION['dims']['uploadfile']['url']=$_SERVER['SCRIPT_URI']."?".$_SERVER['QUERY_STRING'];
	if (strpos($_SERVER['SCRIPT_NAME'],'admin-light.php')>0) {
		$_SESSION['dims']['uploadfile']['url']= "/admin.php";
	}
	else {
		$_SESSION['dims']['uploadfile']['url']=$_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
	}

	if ($linkonly)
		return ("javascript:displayAddFiles(event,600);");
	else {
		?>

		<?
		$currentfolder=-1;

		require_once(DIMS_APP_PATH . '/modules/doc/public_file_form_progressbar_object.php');
	}
	//echo "<a href=\"javascript:void(0);\" onclick=\"displayAddFiles(event,600);\">".$_DIMS['cste']['_DOC_NEWFILE']."&nbsp;<img src=\"./common/img/add.gif\" alt=\"\"></a>";
}
?>
