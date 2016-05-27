<?php
require_once DIMS_APP_PATH.'modules/system/class_tag.php';
require_once DIMS_APP_PATH.'modules/system/class_tag_category.php';
switch ($action) {
	default:
	case 'show':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/tmp/display_tmp_tag.tpl.php';
		break;
	case 'save_label':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$label = dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_DURATION),null,1);
		if(!empty($tag) && trim($label) != ''){
			$tag->set('tag',$label);
			$tag->save();
		}
		die();
		break;
	case 'save':
		$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'type_tag'=>tag_category::_TYPE_DURATION),' ORDER BY label ');
		$idCateg = array(0=>0);
		foreach($categs as $c){
			$idCateg[$c->get('id')] = $c->get('id');
		}
		$tags = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION), ' ORDER BY tag ');
		foreach($tags as $t){
			$idCat = dims_load_securvalue("obj_val_".$t->get('id'),dims_const::_DIMS_NUM_INPUT,true,true,true);
			if(in_array($idCat, $idCateg)){
				$t->set('id_category',$idCat);
				$t->save();
			}
		}
		$new_label = trim(dims_load_securvalue("new_label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($new_label != ''){
			$tag = new tag();
			$tag->init_description();
			$tag->setugm();
			$tag->set('tag',$new_label);
			$tag->set('type',tag::TYPE_DURATION);
			$tmp = dims_createtimestamp();
			$tag->set('timestp_modify',$tmp);
			$tag->set('timestp_create',$tmp);
			$new_obj = dims_load_securvalue("new_obj",dims_const::_DIMS_NUM_INPUT,true,true,true);
			if(in_array($new_obj, $idCateg)){
				$tag->set('id_category',$new_obj);
			}
			$tag->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=tmp");
		break;
	case 'delete':
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_DURATION),null,1);
		if(!empty($tag)){
			$tag->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=tmp");
		break;
}