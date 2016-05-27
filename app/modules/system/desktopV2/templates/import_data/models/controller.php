<div>
	<h1>
		<img src="/common/modules/system/import/img/icon_gclients.png" />
		<? echo $_SESSION['cste']['_DIMS_LABEL_MANAGE_MODEL']; ?>
	</h1>
</div>
<div style="float:left;width:32%;text-align:center;margin:2px auto;">
	<img src="<? echo $_SESSION['dims']['template_path']; ?>/media/goback32.png">
	<br><input type="button" onclick="document.location.href='<? echo dims_urlencode("/admin.php?import_op="._OP_DEFAULT_IMPORT); ?>';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="<? echo $_SESSION['cste']['_DIMS_BACK']; ?>"/>
</div>
<!-- add model -->
<div style="float:left;width:32%;text-align:center;margin:2px auto;">
	<img src="<? echo $_SESSION['dims']['template_path']; ?>/media/add_table32.png">
	<br><input type="button" onclick="document.location.href='<? echo dims_urlencode("/admin.php?op_model=addNewModel"); ?>';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="<? echo $_SESSION['cste']['_DIMS_LABEL_ADD_MODEL']; ?>"/>
</div>
<!-- add model -->
<div style="float:left;width:32%;text-align:center;margin:2px auto;">
	<img src="<? echo $_SESSION['dims']['template_path']; ?>/media/table32.png">
	<br><input type="button" onclick="document.location.href='<? echo dims_urlencode("/admin.php?op_model=listModelFields"); ?>';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="<? echo $_SESSION['cste']['_FORMS_FIELDLIST']; ?>"/>
</div>
<?php
$op_model = dims_load_securvalue('op_model',dims_const::_DIMS_CHAR_INPUT,true,true,false);
switch($op_model){
	case 'displayModelFieldsCorrespRh':

		break;
	case "addNewModelFieldsCorresp":

		break;

	case "addNewModelFile":
		$_SESSION['dims']['import']['import']['import_label']=dims_load_securvalue('import_title',dims_const::_DIMS_CHAR_INPUT,false,true);
		$_SESSION['dims']['import']['id_globalobject']=$tiers->fields['id_globalobject'];

		$file	= $_FILES['file_import']['tmp_name'];

		$extension	= explode(".", $_FILES['file_import']['name']);
		$extension	= $extension[count($extension)-1];
		$extension  = strtolower($extension);

		//save extension
		$_SESSION['dims']['import']['import']['extension']=$extension;

		$sid = session_id();
		$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
		$session_dir = $temp_dir.$sid;

		dims_makedir($session_dir);

		$filepath=$session_dir."/source.".$extension;
		// Warning in php.ini file
		// post_max_size = 20M
		// upload_max_filesize = 20M
		//echo $file. " ".$filepath."<br>";die();
		if (move_uploaded_file($file, $filepath)) {

			//$error=import_fichier_modele::importFile($filepath,$session_dir,true);
			$error=import_fichier_modele::importFile($filepath, $session_dir,true);
			if ($error>0) {
				dims_redirect("/admin.php?op_model=addNewModel&error=".$error);
			}
			else {
				// initialisation des champs de champs de corresp
				$_SESSION['dims']['import']['corresp'] = array();
				dims_redirect('/admin.php?op_model=addNewModelFieldsCorresp');
			}

		}
		else {
			//echo "Error with config PHP file";
		}
		break;

	case "addNewModel":
		$_SESSION['dims']['import']['import_label']='';
		$_SESSION['dims']['import']['id_globalobject']=0;
		$_SESSION['dims']['import']['import']=array();
		include _DESKTOP_TPL_LOCAL_PATH.'/import_data/models/form_model.tpl.php';
		break;
	case 'return':
	default:
		include _DESKTOP_TPL_LOCAL_PATH.'/import_data/models/list_models.tpl.php';
		break;
}
?>