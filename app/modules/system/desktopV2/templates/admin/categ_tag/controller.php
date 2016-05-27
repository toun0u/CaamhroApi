<?php
require_once DIMS_APP_PATH.'modules/system/class_tag_category_object.php';
switch ($action) {
	default:
	case 'show':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/categ_tag/display_categ_tag.tpl.php';
		break;
	case 'save':
		$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']),' ORDER BY label ');
		$typeObj = tag_category_object::getListObject();
		foreach($categs as $c){
			// On enlève tous les liens
			$lks = tag_category_object::find_by(array('id_tag'=>$c->get('id')));
			$c->set('type_tag',dims_load_securvalue('obj_type_tag_'.$c->get('id'),dims_const::_DIMS_NUM_INPUT, true, true,true));
			foreach($lks as $lk){
				$lk->delete();
			}
			if($c->get('type_tag') == tag_category::_TYPE_DEFAULT){
				$newLks = dims_load_securvalue('obj_val_'.$c->get('id'), dims_const::_DIMS_NUM_INPUT, true, true,true);
				if(!empty($newLks) && is_array($newLks)){
					foreach($newLks as $newLk){
						if(in_array($newLk, $typeObj))
							$c->linkToObject($newLk);
					}
				}
			}
			$c->save();
		}
		$new_label = trim(dims_load_securvalue("new_label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($new_label != ''){
			$cat = new tag_category();
			$cat->init_description();
			$cat->setugm();
			$cat->set('label',$new_label);
			$cat->set('type_tag',dims_load_securvalue('new_type_tag',dims_const::_DIMS_NUM_INPUT, true, true,true));
			$cat->save();
			if($cat->get('type_tag') == tag_category::_TYPE_DEFAULT){
				$lstObj = dims_load_securvalue("new_obj",dims_const::_DIMS_NUM_INPUT,true,true,true);
				if(!empty($lstObj)){
					$typeObj = tag_category_object::getListObject();
					foreach($lstObj as $newLk){
						if(in_array($newLk, $typeObj)){
							$cat->linkToObject($newLk);
						}
					}
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=ctag");
		break;
	case 'save_label':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$label = dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$cat = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id),null,1);
		if(!empty($cat) && trim($label) != ''){
			$cat->set('label',$label);
			$cat->save();
		}
		die();
		break;
	case 'delete':
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$cat = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id),null,1);
		if(!empty($cat)){
			$cat->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=ctag");
		break;
}
