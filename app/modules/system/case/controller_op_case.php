<?php

require_once DIMS_APP_PATH.'modules/system/case/global.php';

$case_op = dims_load_securvalue('case_op',dims_const::_DIMS_NUM_INPUT,true,true,true);
switch($case_op){
	case _OP_DELETE_CASE:
		$id_case = dims_load_securvalue('id_case',dims_const::_DIMS_NUM_INPUT,true,true,true);

		if ($id_case >0) {
		   $case = new dims_case();
			$case->open($id_case);
			$case->delete();
		}
		dims_redirect("/admin.php");
		break;

	case _OP_VIEW_CASE :
		$id_case = dims_load_securvalue('id_case',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_NUM_INPUT,true,true,true);
		controller_case::viewCaseBOPublic($id_case,$id_popup);
		break;
	case _OP_EDIT_CASE :

		break;
	case _OP_CREATE_CASE :

		break;
	case _OP_SAVE_CASE :

		break;
	case _OP_ADD_FILE_CASE :
		global $dims;
		$id_case = dims_load_securvalue('idCase',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id_case != '' && $id_case > 0){
			$case = new dims_case();
			$case->open($id_case);
			$gbCase = $case->getMyGlobalObject();

			require_once DIMS_APP_PATH.'/modules/doc/class_docfile.php';
			$liste_objets_joints = array () ;
			$sid = session_id();
			$upload_dir = realpath('./data/uploads/'.$sid).'/';
			if (is_dir( realpath('./data/uploads/'.$sid)) && is_dir($upload_dir)) {
				if ($dh = opendir($upload_dir)) {
					while (($filename = readdir($dh)) !== false) {
						if ($filename!="." && $filename!="..") {
							$docfile = new docfile();
							$docfile->init_description();
							$docfile->setugm();

							$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
							$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$docfile->fields['id_folder'] = -1;
							$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
							$docfile->tmpuploadedfile = $upload_dir.$filename;
							$docfile->fields['name'] = $filename;
							$docfile->fields['size'] = filesize($upload_dir.$filename);
							$docfile->fields['version'] = 0;
							$docfile->save();

							$liste_objets_joints[] = $docfile->fields['id_globalobject'];
						}
					}
				}
				closedir($dh);
			}
			rmdir($upload_dir);
			$gbCase->addLink($liste_objets_joints);
			$_SESSION['dims']['case']['reopen'] = $id_case;
		}
		dims_redirect($dims->getScriptEnv());
		break;
}

?>