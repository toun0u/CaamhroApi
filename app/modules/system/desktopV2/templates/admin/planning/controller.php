<?php
require_once DIMS_APP_PATH."modules/system/activity/class_type.php";
switch ($action) {
	default:
	case 'show':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/planning/display_planning_type.tpl.php';
		break;
	case 'save_label':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$label = trim(dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$type = activity_type::find_by(array('id'=>$id),null,1);
		if(!empty($type) && $label != ''){
			$type->set('label',$label);
			$type->save();
		}
		die();
		break;
	case 'save_color':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$color = trim(dims_load_securvalue("color",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$type = activity_type::find_by(array('id'=>$id),null,1);
		if(!empty($type) && $color != ''){
			$type->set('color',$color);
			$type->save();
		}
		die();
		break;
	case 'save':
		$color = trim(dims_load_securvalue("color",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$label = trim(dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($label != ''){
			$type = new activity_type();
			$type->init_description();
			$type->set('label',$label);
			$type->set('color',$color);
			$type->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=planning");
		break;
	case 'delete':
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$type = activity_type::find_by(array('id'=>$id),null,1);
		if(!empty($type)){
			$type->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=planning");
		break;
}
