<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
		require_once module_wiki::getTemplatePath('/languages/list_lang.tpl.php');
		break;
	case module_wiki::_ACTION_SWITCH_ACTIVE:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			wce_lang::switchActive($id);
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU));
		break;
	case module_wiki::_ACTION_EDIT_LANG :
		$lang = new wce_lang();
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			$lang->openFromIdLang($id);
		}else
			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU));
		$lang->display(module_wiki::getTemplatePath('/languages/edit_lang.tpl.php'));
		break;
	case module_wiki::_ACTION_SAVE_LANG :
		$lang = new wce_lang();
		$id = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			$lang->open($id);
			$lang->setvalues($_POST,'lg_');
			$lang->save();
		}else{ // on ne peux pas en créer de nouvelle : pour l'instant ?
			$lang->init_description();
			$lang->setugm();
			$lang->fields['is_active'] = 1;
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU));
		break;
	case module_wiki::_ACTION_EDIT_TAG:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$tag = new tag();
		if ($id != '' && $id > 0)
			$tag->open($id);
		else{
			$tag->init_description();
		}
		$tag->display(module_wiki::getTemplatePath('/languages/edit_tag.tpl.php'));
		break;
	case module_wiki::_ACTION_SAVE_TAG:
		$id = dims_load_securvalue('id_tag',dims_const::_DIMS_NUM_INPUT,true,true);
		$tag = new tag();
		if ($id != '' && $id > 0){
			$tag->open($id);
			$tag->fields['timestp_modify'] = dims_createtimestamp();
		}else{
			$tag->init_description();
			$tag->setugm();
			$tag->fields['timestp_create'] = dims_createtimestamp();
		}
		$tag->setvalues($_POST,'tag_');
		$tag->save();
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU));
		break;
}
?>