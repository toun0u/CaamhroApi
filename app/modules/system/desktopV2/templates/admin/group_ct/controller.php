<?php
require_once DIMS_APP_PATH."modules/system/class_ct_group.php";
switch ($action) {
	default:
	case 'show':
		require_once _DESKTOP_TPL_LOCAL_PATH.'admin/group_ct/display_group_ct.tpl.php';
		break;
	case 'save_label':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$label = trim(dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$type = ct_group::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($type) && $label != ''){
			$type->set('label',$label);
			$type->save();
		}
		die();
		break;
	case 'save':
		$label = trim(dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($label != ''){
			$type = new ct_group();
			$type->init_description();
			$type->set('id_workspace',$_SESSION['dims']['workspaceid']);
			$type->set('id_user_create',$_SESSION['dims']['userid']);
			$type->set('date_create',dims_createtimestamp());
			$type->set('label',$label);
			$type->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=grct");
		break;
	case 'delete':
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$type = ct_group::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($type)){
			$type->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=grct");
		break;
}
