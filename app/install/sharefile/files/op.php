<?php
dims_init_module('sharefile');
dims_init_module('doc');

if (!isset($op)) $op = '';

global $dims;

$moduleid=$obj['module_id'];

include_once("./common/modules/sharefile/include/classes/class_sharefile_file.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_history.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_share.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_user.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_contact.php");
include_once('./common/modules/sharefile/include/classes/class_sharefile_param.php');
include_once("./common/modules/doc/class_docfile.php");

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$dims_op=dims_load_securvalue('dims_op',dims_const::_DIMS_CHAR_INPUT,true,true);

if ($dims_op!="") {
	switch($dims_op) {
		case 'share_file_download':
			include_once './include/class_dims_data_object.php';
			include_once './include/functions/date.php';
			include_once './include/functions/filesystem.php';
			include_once './common/modules/doc/include/global.php';
			include_once './common/modules/doc/class_docfile.php';

			if (!isset($_SESSION['currentshare']['id_share'])) $_SESSION['currentshare']['id_share']=0;
			if (!isset($_SESSION['currentshare']['id_user'])) $_SESSION['currentshare']['id_user']=0;
			if (!isset($_SESSION['currentshare']['id_contact'])) $_SESSION['currentshare']['id_contact']=0;

			$id_share=dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_share']);
			$id_user=dims_load_securvalue("id_user",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_user']);
			$id_contact=dims_load_securvalue("id_contact",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_contact']);

			if (!isset($usercode)) $usercode="";

			if ($_SESSION['currentshare']['id_share']==0) {
				die();
			}
			else {
				$share = new sharefile_share();
				$share->open($id_share);

				// verification de l'existance du partage
				if ($share->fields['label']=="" || $share->fields['id_module']=='') {
					dims_redirect($dims->getScriptEnv()."?op=sharefile_deleted",true);
				}

				// active par d�faut le code vierge
				if (!isset($_SESSION['sharecodes'][$id_share])) $_SESSION['sharecodes'][$id_share]="";

// verification du code
				if (($share->fields['code']!="" && $share->fields['code']!=$_SESSION['sharecodes'][$id_share]) || ($usercode!="" && $usercode!=$_SESSION['sharecodes'][$id_share]) ) {
					die('Security exception');
				}
				// code active
				// controle si date deja d�pass�e ou non
				$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
				$dateday=date('d/m/Y',$maxtoday);
				$maxtoday=dims_local2timestamp($dateday);

				if ($share->fields['timestp_finished']>$maxtoday && !isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid'])) {
					dims_redirect($dims->getScriptEnv()."?op=sharefile_maxdate",true);
				}

				// on peut etre ici
				if (!empty($_GET['docfile_md5id'])) {
					$resultat=$share->verifAccessFile($_GET['docfile_md5id']);
					if ($resultat) {
						// on peut downloader le fichier
						$res=$db->query("SELECT id
										FROM dims_mod_doc_file
										WHERE md5id = :md5",
										array(':md5' => addslashes($_GET['docfile_md5id']))
										);
						if ($fields = $db->fetchrow($res)) {

							$docfile = new docfile();
							$docfile->open($fields['id']);

							// vérification du partage
							if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
							elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);

						}
					}
				}

			}
			/*if (!empty($_GET['docfile_id'])) {

				$docfile = new docfile();
				$docfile->open($_GET['docfile_id']);

				if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
				elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);
			}*/

			die();
			break;
	}
}
?>

