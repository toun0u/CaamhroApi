<?
dims_init_module('doc');

require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docgallery.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfolder.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfiledraft.php';

if (!isset($op) || $op=="") $op = 'mydocs';

$folders=doc_getfolders();
$currentfolder=0;
$readonly=false;

if (!isset($_SESSION['dims']['doc'][$_SESSION['dims']['moduleid']]['current_id']) && !empty($folders)) {
	$f=current($folders['list']);
	$_SESSION['dims']['doc'][$_SESSION['dims']['moduleid']]['current_id']=$f['id'];
	$currentfolder=$f['id'];
}
$mod=$dims->getModule($_SESSION['dims']['moduleid']);
$label=ucfirst(strtolower($mod['instancename']));

if (isset($_GET['currentfolder'])) {
        $currentfolder=dims_load_securvalue('currentfolder',dims_const::_DIMS_NUM_INPUT,true,true);
}
else
    $currentfolder=dims_load_securvalue('currentfolder',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['doc'][$_SESSION['dims']['moduleid']]['current_id']); // ('field',type=num,get,post,sqlfilter=false)

if (isset($currentfolder) && is_numeric($currentfolder)) $_SESSION['dims']['doc'][$_SESSION['dims']['moduleid']]['current_id']=$currentfolder;
$currentfolder=$_SESSION['dims']['doc'][$_SESSION['dims']['moduleid']]['current_id'];
?>
<script type="text/javascript" src="/common/modules/doc/include/javascript.js"></script>
<?php
//echo $skin->open_backgroundbloc($_DIMS['cste']['_DOCS'], '', '', '');
echo "<div style=\"width:20%;float:left;\">";
	echo "<div id=\"dims_searchmenu\" style=\"width:100%;display:none;visibility:hidden;\">";
	//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_SEARCH_RESULT']."&nbsp;-&nbsp;<span id= \"dims_ressearch\"></span>",'width:100%;','','',false);
	echo "<div id=\"dims_searchcontent\" style=\"padding-left: 4px; width: 100%; visibility: visible; display: block;max-height:350px;\"></div>";
	//echo $skin->close_simplebloc();
	echo "</div>";


	echo "<div id=\"dims_menuleft\" class=\"dims_menuleft\" style=\"width:100%;\">";
	echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_TREE']." : ".$label,'width:100%;','padding-left:10px;color:#cccccc;','',false);
	// construction de l'arborescence
	echo doc_build_maintree($folders,-1,'',1,'',$currentfolder);
	echo "<div style=\"clear:both;\"></div>";
	echo $skin->close_simplebloc();
	echo "</div>";
echo "</div>";
echo "<div style=\"width:79%;float:right;\">";

switch($op) {
	case 'add_file_jqueryupload':

		$lstFiles = dims_load_securvalue('file_name', dims_const::_DIMS_CHAR_INPUT, true, true,true);
		$descriptions = dims_load_securvalue('doc_description', dims_const::_DIMS_CHAR_INPUT, true, true,true);
		$directory = dims_load_securvalue('directory', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$tags = dims_load_securvalue('tags', dims_const::_DIMS_CHAR_INPUT, true, true,true);
		$id_folder = dims_load_securvalue('id_folder', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$tmp_path = DIMS_ROOT_PATH.'www/data/uploads/'.session_id();

		if(!empty($lstFiles) && file_exists($tmp_path)){
			$dir = scandir($tmp_path);
			require_once(DIMS_APP_PATH.'modules/system/class_tag.php');
			require_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');
			require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			foreach($lstFiles as $key => $name){
				if(in_array($name, $dir)){
					$doc = new docfile();
					$doc->init_description();
					$doc->setugm();
					$doc->set('name',$name);
					$doc->set('size',filesize($tmp_path."/".$name));
					$doc->set('description',$descriptions[$key]);
					$doc->set('id_folder',(($directory[$key] != '' && $directory[$key] > 0)?$directory[$key]:$id_folder));
					$doc->tmpuploadedfile = $tmp_path."/".$name;
					$doc->save();

					// Lien matrice
					$matrice = new matrix();
					$matrice->fields['id_doc'] = $doc->fields['id_globalobject'];
					$matrice->fields['year'] = substr($doc->fields['timestp_create'],0,4);
					$matrice->fields['month'] = substr($doc->fields['timestp_create'],4,2);
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();

					if(isset($tags[$key]) && !empty($tags[$key])){
						if(strrpos($tags[$key],',') !== false)
							$tags[$key] = explode(',', $tags[$key]);
						if(is_array($tags[$key])){
							foreach($tags[$key] as $t){
								$lk = new tag_globalobject();
								$lk->init_description();
								$lk->set('id_tag',$t);
								$lk->set('id_globalobject',$doc->get('id_globalobject'));
								$lk->set('timestp_modify',dims_createtimestamp());
								$lk->save();
							}
						}else{
							$lk = new tag_globalobject();
							$lk->init_description();
							$lk->set('id_tag',$tags[$key]);
							$lk->set('id_globalobject',$doc->get('id_globalobject'));
							$lk->set('timestp_modify',dims_createtimestamp());
							$lk->save();
						}
					}
				}
			}
			dims_deletedir($tmp_path);
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;

	case 'view':
		$id_object=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,false);
		$id_record=dims_load_securvalue('id_record',dims_const::_DIMS_NUM_INPUT,true,false);
		//$id_module=dims_load_securvalue('dims_moduleid',dims_const::_DIMS_NUM_INPUT,true,false);

		if ($id_object>0 && $id_record>0) {
			switch ($id_object) {
				case _DOC_OBJECT_FILE:
					dims_redirect(dims_urlencode("$scriptenv?dims_action=public&op=file_modify&docfile_id=".$id_record));
					break;
				case _DOC_OBJECT_FOLDER:
					dims_redirect(dims_urlencode("$scriptenv?dims_action=public&op=folder_modify&currentfolder=".$id_record));
					break;
			}
		}
		break;
	case 'xml_detail_folder':
		ob_end_clean();
		$hid = dims_load_securvalue('hid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$str = dims_load_securvalue('str', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$option = dims_load_securvalue('option', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		echo doc_build_maintree($folders, $hid, $str, 2, $option);
		die();
	break;
	case "viewpda":
		$dims_op="searchnews";
		require_once DIMS_APP_PATH . '/modules/doc/block_pda.php';

		break;
	case "file_extract":
		$docfile_id=dims_load_securvalue('docfile_id',dims_const::_DIMS_NUM_INPUT,true,false);

		if ($docfile_id>0) {
			$docfile = new docfile();
			$docfile->open($docfile_id);
			$currentfolder=$docfile->fields['id_folder'];
			$_SESSION['dims']['docs']['docfile_id']=$docfile_id;
			$_SESSION['dims']['docs']['currentfolder']=$currentfolder;
			$_SESSION['dims']['docs']['start']=0;
			$_SESSION['dims']['docs']['current']=0;
			$_SESSION['dims']['docs']['total']=0;

			dims_redirect("{$scriptenv}?op=file_extract_suite");
		}
		break;
	case "file_extract_suite":
		require_once DIMS_APP_PATH . '/modules/doc/public_file_extract.php';
		break;
	case "file_extract_init":
		ob_end_clean();
		ini_set('max_execution_time',-1);
		ini_set('memory_limit',"1024M");

		$_SESSION['dims']['docs']['start']=1;
		// copy extract file  to current folder, follow files and folder recursively and create line
		$docfile = new docfile();
		$docfile->open($_SESSION['dims']['docs']['docfile_id']);
		$currentfolder=$docfile->fields['id_folder'];

		if ($docfile->fields['extension']=="zip" || $docfile->fields['extension']=="tar.gz" || $docfile->fields['extension']=="tgz") {

			if (file_exists($docfile->getfilepath()) && is_writeable(doc_getpath()._DIMS_SEP)) {
				$pathdest=$docfile->getbasepath()._DIMS_SEP."tmp_".$_SESSION['dims']['docs']['docfile_id'];
				$_SESSION['dims']['docs']['pathdest']=$pathdest;
				session_write_close();
				if (is_dir($pathdest)) dims_deletedir($pathdest);
				mkdir ($pathdest);

				if (strtolower($docfile->fields['extension'])=="zip") {
					dims_unzip($docfile->getfilepath(),$docfile->getbasepath(),$pathdest);
				}
				else {
					exec(escapeshellcmd("tar -zxvf ".escapeshellarg($docfile->getfilepath())." -C ".escapeshellarg($pathdest)));
				}

				// extract finished
				session_start();
				$_SESSION['dims']['docs']['start']=2;
				session_write_close();

				$pathdest=$_SESSION['dims']['docs']['pathdest'];
				// on s'occupe maintenant de parcourir l'ensemble du r�pertoire et d�placer au fur et � mesure les fichiers et dossiers
				$docfolder = new docfolder();
				$docfolder->open($_SESSION['dims']['docs']['currentfolder']);

				if ($_SESSION['dims']['docs']['currentfolder']==0) {
					$docfolder->init_description();
					$docfolder->fields['foldertype']="public";
					$docfolder->fields['parents']="";
					$docfolder->fields['id']=0;
					$docfolder->fields['published']=1;
				}
				$result=explode("\t",exec(escapeshellcmd("du -s ".escapeshellarg($pathdest))),2);
				if (isset($result)) {
					session_start();
					$_SESSION['dims']['docs']['total']= $result[0];
					session_write_close();
				}

				createRecursiveFiles($pathdest._DIMS_SEP,$_SESSION['dims']['docs']['basepath']._DIMS_SEP,"777",$docfolder);
				dims_deletedir($pathdest);
				session_start();
				// files created finished
				$_SESSION['dims']['docs']['start']=3;
			}
		}
		die();
		break;
	case "file_extract_ajax":
		ob_end_clean();
		require_once(DIMS_APP_PATH . '/modules/doc/extract_progress.php');
		die();
		break;
	case "file_download_zip":
		ini_set('max_execution_time',0);
		ini_set('memory_limit',"2048M");
		require_once DIMS_APP_PATH . '/lib/pclzip-2-5/pclzip.lib.php';

		$zip_path = doc_getpath()._DIMS_SEP.'zip';
		if (!is_dir($zip_path)) mkdir($zip_path);

		if (!empty($_GET['docfile_id'])) {
			$docfile = new docfile();
			$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

			if (file_exists($docfile->getfilepath()) && is_writeable($zip_path)) {
				// create a temporary file with the real name
				$tmpfilename = $zip_path._DIMS_SEP.$docfile->fields['name'];

				copy($docfile->getfilepath(),$tmpfilename);

				// create zip file
				$zip_filename = "archive_".dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true).".zip";
				echo $zip_filepath = $zip_path._DIMS_SEP.$zip_filename;
				$zip = new PclZip($zip_filepath);
				$zip->create($tmpfilename,PCLZIP_OPT_REMOVE_ALL_PATH);

				// delete temporary file
				unlink($tmpfilename);

				// download zip file
				dims_downloadfile($zip_filepath, $zip_filename, true);
			}
		}

		if (!empty($_GET['docfiledraft_id'])) {
			$docfiledraft = new docfiledraft();
			$docfiledraft->open(dims_load_securvalue('docfiledraft_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

			if (file_exists($docfiledraft->getfilepath()) && is_writeable($zip_path))
			{
				// create a temporary file with the real name
				$tmpfilename = $zip_path._DIMS_SEP.$docfiledraft->fields['name'];
				copy($docfiledraft->getfilepath(),$tmpfilename);

				// create zip file
				$zip_filename = "archive_draft_".dims_load_securvalue('docfiledraft_id', dims_const::_DIMS_NUM_INPUT, true, true, true).".zip";
				echo $zip_filepath = $zip_path._DIMS_SEP.$zip_filename;
				$zip = new PclZip($zip_filepath);
				$zip->create($tmpfilename,PCLZIP_OPT_REMOVE_ALL_PATH);

				// delete temporary file
				unlink($tmpfilename);

				// download zip file
				dims_downloadfile($zip_filepath, $zip_filename, true);
			}
		}
	break;

	case "file_download":

		if (!empty($_GET['docfile_id']) || isset($_GET['docfile_md5id'])) {
			ini_set('max_execution_time',0);
			ini_set('memory_limit',"4048M");

			$docfile = new docfile();
			$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

			if (!empty($_GET['version'])) {
				require_once DIMS_APP_PATH . '/modules/doc/class_docfilehistory.php';
				$docfilehistory = new docfilehistory();
				$docfilehistory->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true), dims_load_securvalue('version', dims_const::_DIMS_NUM_INPUT, true, true, true));
				if (file_exists($docfilehistory->getfilepath())) dims_downloadfile($docfilehistory->getfilepath(),$docfilehistory->fields['name']);
			}
			else {
				if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
				else if (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);
			}
		}

		if (!empty($_GET['docfile_md5id'])) {
			$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5", array(':md5' => addslashes(dims_load_securvalue('docfile_md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true))) );
			if ($fields = $db->fetchrow($res)) {
				$docfile = new docfile();
				$docfile->open($fields['id']);

				if (!empty($_GET['version'])) {
					require_once DIMS_APP_PATH . '/modules/doc/class_docfilehistory.php';
					$docfilehistory = new docfilehistory();
					$docfilehistory->open($docfile->fields['id'], dims_load_securvalue('version', dims_const::_DIMS_NUM_INPUT, true, true, true));

					if (file_exists($docfilehistory->getfilepath())) dims_downloadfile($docfilehistory->getfilepath(),$docfilehistory->fields['name']);
				}
				else {
					if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
					else if (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);
				}
			}
		}

		if (!empty($_GET['docfiledraft_id'])) {
			$docfiledraft = new docfiledraft();
			$docfiledraft->open(dims_load_securvalue('docfiledraft_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
			if (file_exists($docfiledraft->getfilepath())) dims_downloadfile($docfiledraft->getfilepath(),$docfiledraft->fields['name']);
		}
	break;

	case "filedraft_delete":
		if (!empty($_GET['docfiledraft_id'])) {
			$docfiledraft = new docfiledraft();
			$docfiledraft->open(dims_load_securvalue('docfiledraft_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
			$error = $docfiledraft->delete();
			dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder&error=$error");
		}
	break;

	case "file_delete":
				$docfile_id=dims_load_securvalue('docfile_id',dims_const::_DIMS_NUM_INPUT,true,false);
		if ($docfile_id>0) {
			ini_set('memory_limit','512M');
			$docfile = new docfile();
			$docfile->open($docfile_id);

			// on verifie que l'utilisateur a bien le droit de supprimer ce fichier (en fonction du statut du dossier parent)
			$docfolder_readonly_content = false;

			if (!empty($docfile->fields['id_folder']) && $docfile->fields['id_folder']>0) {
				$docfolder_parent = new docfolder();
				$docfolder_parent->open($docfile->fields['id_folder']);
				$docfolder_readonly_content = ($docfolder_parent->fields['readonly_content'] && $docfolder_parent->fields['id_user'] != $_SESSION['dims']['userid']);
			}

			if ((dims_isactionallowed(_DOC_ACTION_DELETEFILE) && !$docfolder_readonly_content) || $docfile->fields['id_user'] == $_SESSION['dims']['userid']) {
				$error = $docfile->delete();
								//dims_print_r($docfile->fields);
				dims_create_user_action_log(_DOC_ACTION_DELETEFILE,$_DIMS['CSTE']['_DELETE'],-1,-1, $docfile->fields['id'],_DOC_OBJECT_FILE);
				dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder&error=$error");
			}
		}

		dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
	break;

	case "file_save_progress":
		if (isset($currentfolder)) {
			require_once DIMS_APP_PATH . '/include/functions/shares.php';
			require_once DIMS_APP_PATH . '/include/functions/workflow.php';
			$docfolder = new docfolder();
			$docfolder->open($currentfolder);
			$wfusers = array();
			$ismodify=false;
			$docfile_id=dims_load_securvalue('docfile_id',dims_const::_DIMS_NUM_INPUT,false,true);
			foreach(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder) as $value) $wfusers[] = $value['id_workflow'];

			$docfile = new docfile();
			if ($docfile_id>0) {// file already exists
					$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
					dims_shares_save(_DOC_OBJECT_FILE, $docfile->fields['id']);
					$docfile->setvalues($_POST, "docfile_");
					$docfile->save();
					$ismodify=true;
			}
			else {
					$docfile->setugm();
			}
			// on va regarder ce qu'il y a dans le r�pertoire temporaire du user courant
			if (isset($_SESSION['dims']['uploaded_sid'])) {
				$sid = $_SESSION['dims']['uploaded_sid'];//session_id();
				unset($_SESSION['dims']['uploaded_sid']); // on efface l'id
			}

			$upload_dir = realpath('.').'/data/uploads/'.$sid.'/';
			$tmp_dir=DIMS_TMP_PATH.$sid;


			if (is_dir($tmp_dir)) {
				$upload_size_file = $tmp_dir."/upload_size";
				$upload_finished_file = $tmp_dir."/upload_finished";

				if (file_exists($upload_size_file)) unlink($upload_size_file);
				if (file_exists($upload_finished_file)) unlink($upload_finished_file);
				rmdir($tmp_dir); // on nettoie le dossier temp aussi
			}

			if (is_dir( $upload_dir) ) {
				if ($dh = opendir($upload_dir)) {
					while (($filename = readdir($dh)) !== false) {
						if ($filename!="." && $filename!="..") {

							if (!empty($wfusers) && !in_array($_SESSION['dims']['userid'],$wfusers)) {
									$docfiledraft = new docfiledraft();
									$docfiledraft->setugm();
									$docfiledraft->fields['id_folder'] = $currentfolder;

									if (!empty($_POST['docfile_id'])) { // file already exists
											$docfiledraft->fields['id_docfile'] = dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
											unset($docfiledraft->fields['id']);
									}

									$docfiledraft->fields['id_user_modify'] = $_SESSION['dims']['userid'];
									$docfiledraft->tmpuploadedfile =$upload_dir.$filename;
									$docfiledraft->fields['name'] = $filename;
									$docfiledraft->fields['size'] =filesize($upload_dir.$filename);

									$error = $docfiledraft->save();

									$_SESSION['dims']['tickets']['users_selected'] = $wfusers;
									require_once DIMS_APP_PATH . '/include/functions/tickets.php';
									dims_tickets_send("Demande de validation du document <strong>\"{$docfiledraft->fields['name']}\"</strong> (module {$_SESSION['dims']['currentmodule']['label']})", "Ceci est un message automatique envoye suite a une demande de validation du document \"{$docfiledraft->fields['name']}\" du module {$_SESSION['dims']['currentmodule']['label']}<br /><br />Vous pouvez acceder a ce document pour le valider en cliquant sur le lien ci-dessous.", true, 0, _DOC_OBJECT_FILEDRAFT, $docfiledraft->fields['id'], $docfiledraft->fields['name']);
							}
							else {

									if (!$docfile->new) $docfile->createhistory();

									$docfile->setvalues($_POST,'docfile_');
									$docfile->fields['id_folder'] = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);

									$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
									$docfile->tmpuploadedfile = $upload_dir.$filename;
									$docfile->fields['name'] = $filename;
									$docfile->fields['size'] = filesize($upload_dir.$filename);

									$error = $docfile->save();
									// new file
									if ($docfile_id==0) dims_shares_save(_DOC_OBJECT_FILE, $docfile->fields['id']);

									if (!$error) {
											if (dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true) != '') {
												dims_create_user_action_log(_DOC_ACTION_MODIFYFILE,$_DIMS['CSTE']['_MODIFY'],-1,-1, $docfile->fields['id'],_DOC_OBJECT_FILE);
											}
											else dims_create_user_action_log(_DOC_ACTION_ADDFILE,$_DIMS['CSTE']['_DIMS_ADD'],-1,-1, $docfile->fields['id'],_DOC_OBJECT_FILE);
									}
									else
											echo $error;

									// for multiple upload
									if (!$ismodify) {
										$docfile = new docfile();
										$docfile->setugm();
									}
							}
						}
					}
					closedir($dh);
				}
				// on nettoie le dossier
				rmdir($upload_dir);
			}
			dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
		}
	break;

	case "file_save":

		if (isset($currentfolder)) {
			require_once DIMS_APP_PATH . '/include/functions/shares.php';
			require_once(DIMS_APP_PATH . '/include/functions/workflow.php');
			$docfolder = new docfolder();
			$docfolder->open($currentfolder);

			$wfusers = array();
			foreach(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder) as $value) $wfusers[] = $value['id_workflow'];

			if (!empty($wfusers) && !in_array($_SESSION['dims']['userid'],$wfusers)) {
				$docfiledraft = new docfiledraft();
				$docfiledraft->setugm();
				$docfiledraft->setvalues($_POST,'docfile_');
				$docfiledraft->fields['id_folder'] = $currentfolder;
				$docfiledraft->fields['parents'] = "";

				if (!empty($_POST['docfile_id'])) {// file already exists
					$docfiledraft->fields['id_docfile'] = dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
					unset($docfiledraft->fields['id']);
				}

				if (!empty($_FILES['docfile_file']['name'])) {
					$docfiledraft->fields['id_user_modify'] = $_SESSION['dims']['userid'];
					$docfiledraft->tmpfile = $_FILES['docfile_file']['tmp_name'];
					$docfiledraft->fields['name'] = $_FILES['docfile_file']['name'];
					$docfiledraft->fields['size'] = $_FILES['docfile_file']['size'];
				}

				$error = $docfiledraft->save();

				$_SESSION['dims']['tickets']['users_selected'] = $wfusers;
				require_once DIMS_APP_PATH . '/include/functions/tickets.php';
				dims_tickets_send("Demande de validation du document <strong>\"{$docfiledraft->fields['name']}\"</strong> (module {$_SESSION['dims']['currentmodule']['label']})", "Ceci est un message automatique envoy� suite � une demande de validation du document \"{$docfiledraft->fields['name']}\" du module {$_SESSION['dims']['currentmodule']['label']}<br /><br />Vous pouvez acc�der � ce document pour le valider en cliquant sur le lien ci-dessous.", true, 0, _DOC_OBJECT_FILEDRAFT, $docfiledraft->fields['id'], $docfiledraft->fields['name']);

				dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder&error=$error");
			}
			else {
				$docfile = new docfile();

				if (!empty($_POST['docfile_id'])) {// file already exists
					$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
				}
				else {
					$docfile->setugm();
				}

				if (!empty($_FILES['docfile_file']['name']) && !$docfile->new) $docfile->createhistory();

				$docfile->setvalues($_POST,'docfile_');
				$docfile->fields['id_folder'] = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$docfile->fields['parents'] = "";
				if (!empty($_FILES['docfile_file']['name'])) {
					$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
					$docfile->tmpfile = $_FILES['docfile_file']['tmp_name'];
					$docfile->fields['name'] = $_FILES['docfile_file']['name'];
					$docfile->fields['size'] = $_FILES['docfile_file']['size'];
				}
				$error = $docfile->save();

				if (!$error) {
					if (!empty($_POST['docfile_id'])) {
						dims_create_user_action_log(_DOC_ACTION_MODIFYFILE,$_DIMS['CSTE']['_MODIFY'],-1,-1, $docfile->fields['id'],_DOC_OBJECT_FILE);
					}
					else dims_create_user_action_log(_DOC_ACTION_ADDFILE,$_DIMS['CSTE']['_DIMS_ADD'],-1,-1, $docfile->fields['id'],_DOC_OBJECT_FILE);

					dims_shares_save(_DOC_OBJECT_FILE, $docfile->fields['id']);
				}
			}
		}
		dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder&error=$error");
	break;

	case "file_publish":
		if (!empty($_GET['docfile_id'])) {
			require_once(DIMS_APP_PATH . "/include/functions/workflow.php");
			$wfusers = array();
			foreach(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder) as $value) $wfusers[] = $value['id_workflow'];

			if (!empty($wfusers) && in_array($_SESSION['dims']['userid'],$wfusers)) {
				$docfiledraft = new docfiledraft();
				$docfiledraft->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
				$docfiledraft->publish();
			}
		}
		dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
	break;

	case "folder_publish":
		if (!empty($_GET['docfolder_id']))	{
			require_once(DIMS_APP_PATH . "/include/functions/workflow.php");
			$wfusers = array();
			//dims_print_r(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder));
			foreach(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder) as $value) $wfusers[] = $value['id_workflow'];

			if (!empty($wfusers) && in_array($_SESSION['dims']['userid'],$wfusers))
			{
				$docfolder = new docfolder();
				$docfolder->open(dims_load_securvalue('docfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
				$docfolder->fields['published'] = 1;
				$docfolder->save();

			}
		}

		dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
	break;

	case "folder_delete":
		if (!empty($_GET['docfolder_id'])) {
			require_once(DIMS_APP_PATH . "/include/functions/workflow.php");
			$docfolder = new docfolder();
			$docfolder->open(dims_load_securvalue('docfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

			// on v�rifie que l'utilisateur a bien le droit de supprimer ce dossier (en fonction du statut du dossier et du dossier parent)
			$docfolder_readonly_content = false;

			if (!empty($docfolder->fields['id_folder'])) {
				$docfolder_parent = new docfolder();
				$docfolder_parent->open($docfolder->fields['id_folder']);
				$docfolder_readonly_content = ($docfolder_parent->fields['readonly_content'] && $docfolder_parent->fields['id_user'] != $_SESSION['dims']['userid']);
			}

			$readonly = (($docfolder->fields['readonly'] && $docfolder->fields['id_user'] != $_SESSION['dims']['userid']) || $docfolder_readonly_content);

			if (dims_isadmin() || dims_isactionallowed(0) || dims_isactionallowed(_DOC_ACTION_DELETEFOLDER) && (!$readonly) && ($row['nbelements'] == 0)  || $row['id_user']==$_SESSION['dims']['userid']) {
				$docfolder->delete();
				dims_create_user_action_log(_DOC_ACTION_DELETEFOLDER,$_DIMS['CSTE']['_DELETE'],-1,-1, $docfolder->fields['id'],_DOC_OBJECT_FOLDER);
			}

			dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
		}
	break;

	case "folder_save":
		$docfolder = new docfolder();
		require_once DIMS_APP_PATH . '/include/functions/shares.php';
		require_once DIMS_APP_PATH . '/include/functions/workflow.php';

		$docfolder_id=dims_load_securvalue('docfolder_id',dims_const::_DIMS_NUM_INPUT,false,true,false);

		if ($docfolder_id>0) {
			$currentgallery=0;
			$docfolder->open($docfolder_id);
			// on supprime la gallerie
			$docgallery = new docgallery();
			$res=$db->query("select id from dims_mod_doc_gallery where id_folder= :idfolder", array(':idfolder' => $docfolder->fields['id']) );

			if ($db->numrows($res)>0) {
				while ($f=$db->fetchrow($res)) {
					$currentgallery=$f['id'];
				}
			}

			$isgallery=false;
			if ($currentgallery>0 && $_POST['docfolder_foldertype']!='gallery') {
				$docgallery->open($currentgallery);
				$docgallery->delete();
			}elseif($_POST['docfolder_foldertype']=='gallery' && $currentgallery <= 0){
				$docfolder->fields['foldertype']='public';
				$isgallery=true;
			}
			$docfolder->setvalues($_POST,'docfolder_');

			if (empty($_POST['docfolder_readonly'])) $docfolder->fields['readonly'] = 0;
			if (empty($_POST['docfolder_readonly_content'])) $docfolder->fields['readonly_content'] = 0;

			$docfolder->save();
			dims_shares_save(_DOC_OBJECT_FOLDER, $docfolder->fields['id']);
			dims_workflow_save(_DOC_OBJECT_FOLDER, $docfolder->fields['id']);
			dims_create_user_action_log(_DOC_ACTION_MODIFYFOLDER,$_DIMS['CSTE']['_MODIFY'],-1,-1, $docfolder->fields['id'],_DOC_OBJECT_FOLDER);
			if ($isgallery) {
				dims_redirect("{$scriptenv}?op=folder_gallery_create&currentfolder={$docfolder->fields['id']}");
			}
			else {
				dims_redirect("{$scriptenv}?op=browser&currentfolder={$docfolder->fields['id_folder']}");
			}
		}
		else {// new folder
			$docfolder->setvalues($_POST,'docfolder_');
			if (empty($_POST['docfolder_readonly'])) $docfolder->fields['readonly'] = 0;
			if (empty($_POST['docfolder_readonly_content'])) $docfolder->fields['readonly_content'] = 0;

			// test if we should publish or not the folder
			$wfusers = array();
			foreach(dims_workflow_get(_DOC_OBJECT_FOLDER, $currentfolder) as $value) $wfusers[] = $value['id_workflow'];
			if (!empty($wfusers) && !in_array($_SESSION['dims']['userid'],$wfusers)) $docfolder->fields['published'] = 0;

			$docfolder->fields['id_folder'] = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$isgallery=false;

			if ($docfolder->fields['foldertype']=='gallery') {
				$docfolder->fields['foldertype']='public';
				$isgallery=true;
			}

			$docfolder->setugm();
			$currentfolder = $docfolder->save();

			dims_shares_save(_DOC_OBJECT_FOLDER, $docfolder->fields['id']);

			dims_workflow_save(_DOC_OBJECT_FOLDER, $docfolder->fields['id']);

			dims_create_user_action_log(_DOC_ACTION_ADDFOLDER,$_DIMS['CSTE']['_DIMS_ADD'],-1,-1, $docfolder->fields['id'],_DOC_OBJECT_FOLDER);

			if ($isgallery) {
				dims_redirect("{$scriptenv}?op=folder_gallery_create&currentfolder=$currentfolder");
			}
			else {
				dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
			}
		}

	break;

	case "folder_gallery_add":

		$docgallery = new docgallery();

		if (!isset($_POST['docgallery_id'])) {

			$sql =	"SELECT	mt.id FROM dims_module_type mt WHERE mt.label like 'doc'";
			$result = $db->query($sql);
			$row = $db->fetchrow($result);

			$docgallery->setvalues($_POST,'docgallery_');
			$docgallery->setugm();
			$docgallery->fields['id_user_modify'] = $_SESSION['dims']['userid'];
			$docgallery->save();

		}
		else {

			$docgallery->open($_POST['docgallery_id']);
			$docgallery->setvalues($_POST,'docgallery_');
			$docgallery->save();

		}

		$sql = "
		SELECT		parents
		FROM		dims_mod_doc_folder
		WHERE		id = :idfolder
		";

		$result = $db->query($sql, array(':idfolder' => $docgallery->fields['id_folder']) );

		$row = $db->fetchrow($result);

		$parents = explode(",", $row['parents']);
		$parent = $parents[sizeof($parents)-1];

		dims_redirect("{$scriptenv}?op=browser&currentfolder={$parent}");

	break;

	case "folder_gallery_delete":
		$currentgallery=dims_load_securvalue('currentgallery',dims_const::_DIMS_NUM_INPUT,true,true,false);

		if (isset($currentgallery)) {
			$docgallery = new docgallery();
			$docgallery->open($currentgallery);

			$docgallery->delete();

			dims_redirect("{$scriptenv}?op=browser&currentfolder=$currentfolder");
		}
	break;
}

if ($op!="viewpda" && $op!='file_extract_suite') {
	//echo $skin->create_pagetitle($_SESSION['dims']['modulelabel'],'100%');

	$toolbar = array();

	if (isset($_POST['currentfolder']) && $_POST['currentfolder']>0) $currentfolder = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
	elseif (isset($_GET['currentfolder']) && $_GET['currentfolder']>0) $currentfolder = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);

	$toolbar['mydocs'] = array(
							'title'		=> 'Mes Documents',
							'url'		=> "$scriptenv?op=mydocs",
							'position'	=> 'right',
							'icon'		=> './common/modules/doc/img/mydocs.png'
						);

	$toolbar['search'] = array(
							'title'		=> 'Recherche',
							'url'		=> "$scriptenv?op=search",
							'position'	=> 'right',
							'icon'		=> './common/modules/doc/img/search.png'
						);

	$toolbar['folder_add'] = array(
							'title'		=> 'Nouveau Dossier',
							'url'		=> "$scriptenv?op=folder_add&currentfolder={$currentfolder}",
							'position'	=> 'right',
							'icon'		=> './common/modules/doc/img/newfolder.png'
						);

	$toolbar['doc_add'] = array(
							'title'		=> 'Nouveau Fichier',
							'url'		=> "$scriptenv?op=file_add&currentfolder={$currentfolder}",
							'position'	=> 'right',
							'icon'		=> './common/modules/doc/img/newfile.png'
						);

	if (isset($_SESSION['dims']['modulemenuicon'])) $title=$globaltitle;
	else $title="";
	echo "<div id=\"desktop_detail_content\" style=\"width:100%;display:none;visibility:hidden;\"></div>";

	echo "<div id=\"desktop_right_content\">";
	//echo $skin->open_simplebloc($title,'width:100%;','','');

	?>
	<div class="doc_path" >

				<?
				echo "<div style=\"float:right;display:inline;width:150px\">";

				if ($readonly) {
					echo dims_create_button($_DIMS['cste']['_DOC_NEWFILE'],'/modules/doc/img/ico_newfile_grey.png','javascript:void(0)','','');
				}
				else {
					echo dims_create_button($_DIMS['cste']['_DOC_NEWFILE'],'./common/modules/doc/img/ico_newfile.png','','','',dims_urlencode("$scriptenv?op=file_add&currentfolder={$currentfolder}"));
				}
				echo "</div><div style=\"float:right;display:inline;width:150px\">";
				// voir pour une optimisation de cette partie car on ouvre un docfolder sans doute pour rien
				$docfolder = new docfolder();
				$readonly = false;
				if (!empty($currentfolder)) {
					$docfolder->open($currentfolder);
					$readonly = ($docfolder->fields['readonly_content'] && $docfolder->fields['id_user'] != $_SESSION['dims']['userid']);
				}

				if ($readonly) {
					echo dims_create_button($_DIMS['cste']['_DOC_NEWFOLDER'],'/modules/doc/img/ico_newfolder_grey.png','javascript:void(0)','','');
				}
				else {
					echo dims_create_button($_DIMS['cste']['_DOC_NEWFOLDER'],'./common/modules/doc/img/ico_newfolder.png','','','',dims_urlencode("$scriptenv?op=folder_add&currentfolder={$currentfolder}"));
				}
				?>

				</div>
				<div class="doc_pathmenu">Emplacement :
				<a <? if ($currentfolder == 0) echo 'class="doc_pathselected"'; ?> href="<? echo dims_urlencode("$scriptenv?op=browser&currentfolder=0"); ?>">
					<p style="float:left;">
						<img src="./common/modules/doc/img/ico_folder_home.png" />
						<span>Racine</span>
					</p>
				</a>
				<?
				if ($currentfolder != 0) {
					$docfolder = new docfolder();
					$docfolder->open($currentfolder);

					if (!isset($docfolder->fields['parents']) || $docfolder->fields['parents']=='') {
						$docfolder->fields['parents']=0;
					}
					//echo "SELECT id, name, foldertype, readonly FROM dims_mod_doc_folder WHERE id in ({$docfolder->fields['parents']},{$currentfolder}) order by parents";
					$res=$db->query("SELECT id, name, foldertype, readonly FROM dims_mod_doc_folder WHERE id in ( :parents , :currentfolder ) order by parents", array(
						':parents'			=> $docfolder->fields['parents'],
						':currentfolder'	=> $currentfolder
					));

					while ($row = $db->fetchrow($res)) {
						?>
						<a <? if ($currentfolder == $row['id']) echo 'class="doc_pathselected"'; ?> href="<? echo dims_urlencode("{$scriptenv}?op=browser&currentfolder={$row['id']}"); ?>">
							<p style="float:left;">
								<img src="./common/modules/doc/img/ico_folder<? if ($row['foldertype'] == 'shared') echo '_shared'; ?><? if ($row['foldertype'] == 'public') echo '_public'; ?><? if ($row['readonly']) echo '_locked'; ?>.png" />
								<span><? echo $row['name']; ?></span>
							</p>
						</a>
						<?
					}
				}
				?>
				</div>
			</div>

			<?
			switch($op) {
				case 'move':
					if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
						if ((dims_isactionallowed(_DOC_ACTION_MODIFYFILE) && !$docfolder_readonly_content) || $row['id_user'] == $_SESSION['dims']['userid']) {
							$idfolder= dims_load_securvalue('iddestfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);

							if (is_numeric($idfolder) && $idfolder>0) {
								$docfolder = new docfolder();
								$docfolder->open($idfolder);

								// files
								$seldoc = dims_load_securvalue('seldoc', dims_const::_DIMS_NUM_INPUT, true, true, true);
								foreach ($seldoc as $docelem) {
									if (is_numeric($docelem)) {
										$doc = new docfile();
										$doc->open($docelem);
										$doc->moveto($docfolder);
									}
								}

								// folders
								$selfolder = dims_load_securvalue('selfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
								foreach ($selfolder as $docfolderelem) {
									if (is_numeric($docfolderelem)) {
										$docf = new docfolder();
										$docf->open($docfolderelem);
										$docf->moveto($docfolder);
									}
								}
								dims_redirect($scriptenv."?op=browse&currentfolder=".$idfolder);
							}
						}
					}
				break;

				case 'delete':
					if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
						if ((dims_isactionallowed(_DOC_ACTION_DELETEFILE) && !$docfolder_readonly_content) ) {
							$currentfolder=0;
							$seldoc = dims_load_securvalue('seldoc', dims_const::_DIMS_NUM_INPUT, true, true, true);
							foreach ($seldoc as $docelem) {
								if (is_numeric($docelem)) {
									$doc = new docfile();
									$doc->open($docelem);
									if ($currentfolder==0) $currentfolder=$doc->fields['id_folder'];
									if ($doc->fields['id_user'] == $_SESSION['dims']['userid'] || dims_isadmin() || dims_isactionallowed(0)) {
										$doc->delete();
									}
								}
							}

							// folders
							$selfolder = dims_load_securvalue('selfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
							foreach ($selfolder as $docfolderelem) {
									if (is_numeric($docfolderelem)) {
											$docf = new docfolder();
											$docf->open($docfolderelem);
											if ($docf->fields['id_user'] == $_SESSION['dims']['userid'] || dims_isadmin() || dims_isactionallowed(0)) {
												$docf->delete();
											}
									}
							}
							dims_redirect($scriptenv."?op=browse&currentfolder=".dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true));
						}
					}
				break;

				case 'folder_gallery_create':
				case 'folder_gallery_modify':
					require_once DIMS_APP_PATH . '/modules/doc/public_gallery.php';
				break;

				case 'search':
				case 'search_next':
					require_once DIMS_APP_PATH . '/modules/doc/public_search.php';
				break;

				case 'folder_add':
					require_once DIMS_APP_PATH . '/modules/doc/public_folder_form.php';
				break;

				case 'folder_modify':
					require_once DIMS_APP_PATH . '/modules/doc/public_folder_form.php';
				break;

				case 'file_modify':
					if (_DIMS_PROGRESSBAR_USED)
						require_once DIMS_APP_PATH . '/modules/doc/public_file_form_progressbar.php';
					else
						require_once DIMS_APP_PATH . '/modules/doc/public_file_form.php';
				break;

				case 'file_add':
					if (_DIMS_PROGRESSBAR_USED) {
						$folder = new docfolder();
						$folder->open($currentfolder);
						$folder->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?op=add_file_jqueryupload");
						$folder->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?");
						$folder->display(DIMS_APP_PATH . '/modules/doc/public_file_form_jquery.php');
						//require_once DIMS_APP_PATH . '/modules/doc/public_file_form_progressbar.php';
					}
					else
						require_once DIMS_APP_PATH . '/modules/doc/public_file_form.php';
					break;
				case 'viewdraft':
					if (isset($_GET['docfiledraft_id']))
					{
						$docfiledraft = new docfiledraft();
						$docfiledraft->open(dims_load_securvalue('docfiledraft_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
						$currentfolder = $docfiledraft->fields['id_folder'];
					}
				case 'file_extract_suite':
					break;
				default:

					require_once DIMS_APP_PATH . '/modules/doc/public_browser.php';
				break;
			}
	/*
	}
	else {

		require_once(DIMS_APP_PATH . '/modules/doc/block_portal_search.php');
	}
	*/
	//echo $skin->close_simplebloc();
	echo "</div>";
}
echo "</div>";
//echo $skin->close_backgroundbloc();
?>
