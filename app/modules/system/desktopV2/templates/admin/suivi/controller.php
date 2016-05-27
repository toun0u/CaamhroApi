<?php
require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";
require_once DIMS_APP_PATH.'modules/system/desktopV2/include/class_gescom_param.php';
require_once DIMS_APP_PATH.'modules/system/class_country.php';
switch ($action) {
	default:
	case 'show':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/suivi/index.tpl.php';
		break;
	case 'save_params':
		$params = class_gescom_param::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']));
		$paramP = dims_load_securvalue('param',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		foreach($paramP as $k => $v){
			if(isset($params[$k."-".$_SESSION['dims']['workspaceid']])){
				$params[$k."-".$_SESSION['dims']['workspaceid']]->set('value',$v);
				$params[$k."-".$_SESSION['dims']['workspaceid']]->save();
			}else{
				$p = new class_gescom_param();
				$p->init_description();
				$p->set('param',$k);
				$p->set('value',$v);
				$p->set('id_workspace',$_SESSION['dims']['workspaceid']);
				$p->save();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=suivis&action=index");
		break;
	case 'add_type':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$type = suivi_type::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(empty($type)){
			$type = new suivi_type();
			$type->init_description();
		}

		if (file_exists(DIMS_APP_PATH.'/modules/catalogue/display.php') && dims::getInstance()->isModuleTypeEnabled('catalogue')) {
			require_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture.php';
			$type->setLightAttribute('hascatalogue', true);
		}

		$type->display(_DESKTOP_TPL_LOCAL_PATH.'/admin/suivi/edit_type.tpl.php');
		break;
	case 'save_type':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$type = suivi_type::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(empty($type)){
			$type = new suivi_type();
			$type->init_description();
			$type->setugm();
			$type->fields['timestp_create'] = $type->fields['timestp_modify'] = dims_createtimestamp();
		}else{
			$type->set('timestp_modify',dims_createtimestamp());
		}
		$type->set('status',0);
		$type->setvalues($_POST,'type_');
		$type->save();
		if(empty($_POST['continue'])){
			dims_redirect(dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=index');
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=suivis&action=add_type");
		}
		break;
	case 'disabled_type':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$type = suivi_type::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($type)){
			$type->fields['status'] = !$type->fields['status'];
			$type->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=index');
		break;
	case 'add_model':
		$id_model = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$model = print_model::find_by(array('id'=>$id_model,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(empty($model)){
			$model = new print_model();
			$model->init_description();
		}
		$model->display(_DESKTOP_TPL_LOCAL_PATH.'/admin/suivi/edit_models.tpl.php');
		break;
	case 'save_model':
		$id_model = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$model = print_model::find_by(array('id'=>$id_model,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(empty($model) ){
			$model = new print_model();
			$model->init_description();
			$model->setugm();
		}
		$model->setvalues($_POST, 'model_');
		$file = $_FILES['document'];
		if( (! $model->isNew() || ( ! empty($file) && !empty($file['name']) ) ) && $id_print_model = $model->save() ){ //en gros on ne fait le save que si on est pas new ou qu'on a bien le doc
			//gestion du document
			if( ! empty($file) && !empty($file['name']) ){
				$doc = new docfile();
				$doc->init_description();
				$doc->setugm();
				$doc->fields['id_record'] = $id_print_model;

				$doc->fields['name'] = $file['name'];
				move_uploaded_file($file['tmp_name'], DIMS_TMP_PATH . $file['name']);
				$doc->tmpuploadedfile = DIMS_TMP_PATH . $file['name'] ;
				$doc->fields['size'] = $file['size'];
				$doc->fields['id_folder'] = 0;
				$doc->save();

				$model->fields['id_doc'] = $doc->getId();
				$model->save();
			}

			//tout s'est bien passé on peut rediriger
			if(empty($_POST['continue'])){
				dims_redirect(dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=index');
			}else{
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=suivis&action=add_model");
			}
		}else{
			$error = true;
		}

		if($error){//pas cool --> erreur
			$model->setLightAttribute("global_error", $_SESSION['cste']['ERROR_THROWN']);
			$model->display(_DESKTOP_TPL_LOCAL_PATH.'/admin/suivi/edit_models.tpl.php');
		}
		break;
	case 'delete_model':
		$id_model = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$model = print_model::find_by(array('id'=>$id_model,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($model)){
			$model->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=index');
		break;
}
