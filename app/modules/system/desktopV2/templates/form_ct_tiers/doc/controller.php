<?php
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
switch($action){
	default :
	case 'new':
	case 'edit':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$doc = docfile::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($doc)){
			$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/doc/edit_doc.tpl.php');
		}else{
			// TODO : est ce qu'un doc peux être détaché d'un objet
			// Si oui, alors sur quel folder on se base ?
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&force_desktop=1&mode=default");
		}
		break;
	case 'show':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$doc = docfile::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($doc)){
			$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/doc/display_doc.tpl.php');
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=new");
		}
		break;
	case 'save':
		$id_globalobject = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$doc = docfile::find_by(array('id_globalobject'=>$id_globalobject,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($doc)){
			$doc->setvalues($_POST, 'doc_');
			if(isset($_FILES['file']) && !empty($_FILES['file']['name'])){
				$doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$doc->tmpfile = $_FILES['file']['tmp_name'];
				$doc->fields['name'] = $_FILES['file']['name'];
				$doc->fields['size'] = $_FILES['file']['size'];
			}
			$doc->save();
		}elseif(isset($_FILES['file']) && !empty($_FILES['file']['name'])){
			$doc = new docfile();
			$doc->init_description();
			$doc->setugm();
			$doc->setvalues($_POST, 'doc_');
			$doc->tmpfile = $_FILES['file']['tmp_name'];
			$doc->fields['name'] = $_FILES['file']['name'];
			$doc->fields['size'] = $_FILES['file']['size'];
			$doc->tmpfile = $_FILES['file']['tmp_name'];
			$doc->save();
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=new");
		}
		if(!empty($doc) && !$doc->isNew()){
			// Gestion des tags
			require_once(DIMS_APP_PATH.'modules/system/class_tag.php');
			require_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');
			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true,true);
			if(empty($tags)) $tags = array();
			$myTags = $doc->getMyTags();
			foreach($myTags as $t){
				if(in_array($t->get('id'), $tags)){
					unset($tags[array_search($t->get('id'), $tags)]);
				}else{
					$lk = new tag_globalobject();
					$lk->openWithCouple($t->get('id'),$doc->get('id_globalobject'));
					if(!$lk->isNew())
						$lk->delete();
				}
			}
			if(!empty($tags)){
				foreach($tags as $t){
					$lk = new tag_globalobject();
					$lk->init_description();
					$lk->set('id_tag',$t);
					$lk->set('id_globalobject',$doc->get('id_globalobject'));
					$lk->set('timestp_modify',dims_createtimestamp());
					$lk->save();
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=show&id=".$doc->get('id'));
		break;
	case 'delete':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$doc = docfile::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($doc)){
			$doc->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=new");
		break;
}