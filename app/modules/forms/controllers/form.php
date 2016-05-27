<?php
$view = view::getInstance();
switch($a){
	default:
	case 'show':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form)){
			$search = dims_load_securvalue('s',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$p = dims_load_securvalue('p',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if(empty($p)){
				$p = 0;
			}

			$form->setPaginationParams(10);//$forms->fields['nbline'];
			$form->setPageLimited(true);
			$form->page_courant = $p;
			$form->nom_get = $form->name_get_page = 'p';

			$answers = $form->getAnswers($search,true, false);
			$fields = $form->getAllFields();

			$view->assign('search',$search);
			$view->assign('answers',$answers);
			$view->assign('fields',$fields);

			$view->assign('form',$form);
			$view->render('form/answers.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'preview':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form)){
			$view->assign('action_form_'.$form->get('id'),form\get_path(array('c'=>'form','a'=>'preview','id'=>$form->get('id'))));
			$view->assign('back_form_'.$form->get('id'),form\get_path(array('c'=>'index')));
			$view->assign('form',$form);
			$view->render('form/preview.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'new':
		if (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0)) {
			$form = new forms();
			$form->init_description();
			$view->assign('form',$form);
			$view->render('form/edit.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'edit':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form) && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0))){
			$view->assign('form',$form);
			$view->render('form/edit.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'delete':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form) && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0))){
			$form->delete();
			$view->flash("La suppression s'est effectuée avec succès", 'bg-success');
			dims_redirect(form\get_path(array('c'=>'index')));
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'update':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$forms = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($forms) && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0))){
			// On définit à zéro les éléments issue de checkbox
			// Leurs variables correspondante seront défini dans le post
			// et donc grace au setvalues() sur l'objet
			$forms->fields['option_onlyone']		= 0;
			$forms->fields['option_onlyoneday']		= 0;
			$forms->fields['option_displayuser']	= 0;
			$forms->fields['option_displaygroup']	= 0;
			$forms->fields['option_displaydate']	= 0;
			$forms->fields['option_displayip']		= 0;
			$forms->fields['cms_link']				= 0;

			$forms->setvalues($_POST,'form_');

			// Dans le cas des sondage & enquete on ne peut répondre qu'une fois
			if ($forms->fields['typeform'] == 'son' || $forms->fields['typeform'] == 'enq')
				$forms->fields['option_onlyone'] = 1;

			$forms->fields['pubdate_start'] = dims_local2timestamp($forms->fields['pubdate_start']);
			$forms->fields['pubdate_end'] = dims_local2timestamp($forms->fields['pubdate_end']);

			$forms->save();

			require_once DIMS_APP_PATH . '/include/functions/workflow.php';
			// on sauve les droits en contributeurs
			dims_workflow_save(_FORMS_OBJECT_FORM, $forms->fields['id'],$forms->fields['id_module'],_FORMS_ACTION_ADDREPLY);

			$view->flash("La modification s'est effectuée avec succès", 'bg-success');
			dims_redirect(form\get_path(array('c'=>'index')));
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'create':
		if (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0)) {
			$forms = new forms();
			$forms->init_description();
			$forms->setugm();

			$forms->setvalues($_POST,'form_');

			// Dans le cas des sondage & enquete on ne peut répondre qu'une fois
			if ($forms->fields['typeform'] == 'son' || $forms->fields['typeform'] == 'enq')
				$forms->fields['option_onlyone'] = 1;

			$forms->fields['pubdate_start'] = dims_local2timestamp($forms->fields['pubdate_start']);
			$forms->fields['pubdate_end'] = dims_local2timestamp($forms->fields['pubdate_end']);

			$forms->save();

			require_once DIMS_APP_PATH . '/include/functions/workflow.php';
			// on sauve les droits en contributeurs
			dims_workflow_save(_FORMS_OBJECT_FORM, $forms->fields['id'],$forms->fields['id_module'],_FORMS_ACTION_ADDREPLY);

			$view->flash("La création s'est effectuée avec succès", 'bg-success');
			dims_redirect(form\get_path(array('c'=>'index')));
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case "fields":
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form) && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0))){
			$view->assign('form',$form);

			$sa = dims_load_securvalue('sa', dims_const::_DIMS_CHAR_INPUT, true, true);
			$view->assign('sa', $sa);

			include_once DIMS_APP_PATH.'modules/forms/controllers/form_field.php';
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'export':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form) && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_EXPORT) || dims_isactionallowed(0))){
			$format = strtoupper(dims_load_securvalue('format',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$form->export($format);
		}else{
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'import':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if (!empty($form) && $form->get('nb_fields') <= 0 && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_ADDREPLY) || dims_isactionallowed(0))){
			$step = dims_load_securvalue('step',dims_const::_DIMS_NUM_INPUT,true,true,true);
			switch ($step) {
				default:
				case 0:
					unset($_SESSION['dims']['importform'][$form->get('id')]);
					$view->assign('form',$form);
					$view->render('form/import_step1.tpl.php');
					break;
				case 1:
					if(!empty($_FILES['add_file_field']['tmp_name']) && file_exists($_FILES['add_file_field']['tmp_name'])){
						$_SESSION['dims']['importform'][$form->get('id')] = $form->importFile($_FILES['add_file_field']['tmp_name'],$_FILES['add_file_field']['name']);
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>2)));
					}else{
						$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>0)));
					}
					break;
				case 2:
					if(!empty($_SESSION['dims']['importform'][$form->get('id')])){
						$view->assign('form',$form);
						$view->assign('import',$_SESSION['dims']['importform'][$form->get('id')]);
						$view->render('form/import_step2.tpl.php');
					}else{
						$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>0)));
					}
					break;
				case 3: // save
					if(!empty($_SESSION['dims']['importform'][$form->get('id')])){
						$firstdataline = dims_load_securvalue('firstdataline',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$form->importData($_SESSION['dims']['importform'][$form->get('id')],$firstdataline);
						unset($_SESSION['dims']['importform'][$form->get('id')]);
						$view->flash("L'import s'est effectué avec succès", 'bg-success');
						dims_redirect(form\get_path(array('c'=>'index')));
					}else{
						$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>0)));
					}
					break;
				case 4: // edit title
					$field = dims_load_securvalue('field',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					if(isset($_SESSION['dims']['importform'][$form->get('id')]['formatcol'][$field])){
						$flash = array();
						$v = new view($flash);
						$v->set_tpl_webpath('modules/forms/views/');
						$v->setLayout('layouts/empty_layout.tpl.php');
						$v->assign('form',$form);
						$v->assign('import',$_SESSION['dims']['importform'][$form->get('id')]);
						$v->assign('k',$field);
						$v->render('form/import_step2_edit.tpl.php');
						die($v->compile());
					}else{
						$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>2)));
					}
					break;
				case 5: // refresh preview
					if(isset($_SESSION['dims']['importform'][$form->get('id')])){
						$flash = array();
						$v = new view($flash);
						$v->set_tpl_webpath('modules/forms/views/');
						$v->setLayout('layouts/empty_layout.tpl.php');
						$v->assign('form',$form);
						$v->assign('import',$_SESSION['dims']['importform'][$form->get('id')]);
						$v->render('form/import_step2_preview.tpl.php');
						die($v->compile());
					}else{
						$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>2)));
					}
					break;
				case 6: // save title / format / type
					$field = dims_load_securvalue('field',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					if(isset($_SESSION['dims']['importform'][$form->get('id')]['formatcol'][$field])){
						$_SESSION['dims']['importform'][$form->get('id')]['formatcol'][$field] = dims_load_securvalue('format',dims_const::_DIMS_CHAR_INPUT,true,true,true);
						$_SESSION['dims']['importform'][$form->get('id')]['typecol'][$field] = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true,true);
						$_SESSION['dims']['importform'][$form->get('id')]['titlecol'][$field] = dims_load_securvalue('title',dims_const::_DIMS_CHAR_INPUT,true,true,true);
						$flash = array();
						$v = new view($flash);
						$v->set_tpl_webpath('modules/forms/views/');
						$v->setLayout('layouts/empty_layout.tpl.php');
						$v->assign('form',$form);
						$v->assign('import',$_SESSION['dims']['importform'][$form->get('id')]);
						$v->render('form/import_step2_preview.tpl.php');
						die($v->compile());
					}else{
						$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
						dims_redirect(form\get_path(array('c'=>'form','a'=>'import','id'=>$form->get('id'),'step'=>2)));
					}
					break;
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
}
