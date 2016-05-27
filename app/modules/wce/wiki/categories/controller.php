<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
		require_once module_wiki::getTemplatePath('/categories/list_categories.tpl.php');
		break;
	case module_wiki::_ACTION_EDIT_CATEG:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$categ = new category();
		if ($id != '' && $id > 0)
			$categ->open($id);
		else
			$categ->init_description();
		$categ->display(module_wiki::getTemplatePath('/categories/edit_category.tpl.php'));
		break;
	case module_wiki::_ACTION_SAVE_CATEG:
		$id = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$cat = new category();
			$cat->open($id);
			$cat->setvalues($_POST,'categ_');//['label'] = dims_load_securvalue('categ_label',dims_const::_DIMS_CHAR_INPUT,true,true,true);

			$id_parent = dims_load_securvalue('categ_id_parent',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (!($id_parent != '' && $id_parent > 0)){
				$root = module_wiki::getCategRoot();
				$cat->fields['id_parent'] = $root->fields['id'];
			}
			$cat->save();
		}else{
			$new = true;
			$id_parent = dims_load_securvalue('categ_id_parent',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_parent != '' && $id_parent > 0){
				$root = new category();
				$root->open($id_parent);
			}else{
				$root = module_wiki::getCategRoot();
			}

			$root->addSubCategory(dims_load_securvalue('categ_label',dims_const::_DIMS_CHAR_INPUT,true,true,true),category::DIMS_CATEGORY_PUPLIC);
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES));
		break;
	case module_wiki::_ACTION_DEL_CATEG:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$cat = new category();
			$cat->open($id);
			$cat->delete();
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES));
		break;
}
?>