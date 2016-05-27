<?php
$view = view::getInstance();
switch($a){
	default:

		break;
	case 'edit':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$reply = reply::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($reply)){
			$form = forms::find_by(array('id'=>$reply->get('id_forms'),'id_module'=>$_SESSION['dims']['moduleid']),null,1);
			if(!empty($form)){
				$view->assign('values',$reply->getFields(false));
				$view->assign('reply_id',$reply->get('id'));
				$view->assign('action_form_'.$form->get('id'),form\get_path(array('c'=>'answer','a'=>'update','id'=>$reply->get('id'))));
				$view->assign('back_form_'.$form->get('id'),form\get_path(array('c'=>'form','a'=>'show','id'=>$form->get('id'))));
				$view->assign('form',$form);
				$view->render('form/preview.tpl.php');
			}else{
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
				dims_redirect(form\get_path(array('c'=>'index')));
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'new':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form)){
			$view->assign('values',array());
			$view->assign('action_form_'.$form->get('id'),form\get_path(array('c'=>'answer','a'=>'create','id'=>$form->get('id'))));
			$view->assign('back_form_'.$form->get('id'),form\get_path(array('c'=>'form','a'=>'show','id'=>$form->get('id'))));
			$view->assign('form',$form);
			$view->render('form/preview.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'update':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$reply = reply::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($reply)){
			$form = forms::find_by(array('id'=>$reply->get('id_forms'),'id_module'=>$_SESSION['dims']['moduleid']),null,1);
			if(!empty($form) && $form->valideRight($reply->get('id'))){
				if($form->replyTo($reply,(isset($_POST)?$_POST:array()),(isset($_FILES)?$_FILES:array()))){
					$reply->sendEmail(false);
					$view->flash("La modification s'est effectuée avec succès", 'bg-success');
					dims_redirect(form\get_path(array('c'=>'form',"a"=>"show",'id'=>$form->get('id'))));
				}else{
					$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
					dims_redirect(form\get_path(array('c'=>'answer',"a"=>"edit",'id'=>$reply->get('id'))));
				}
			}else{
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
				dims_redirect(form\get_path(array('c'=>'answer',"a"=>"new",'id'=>$form->get('id'))));
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
	case 'create':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$form = forms::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
		if(!empty($form) && $form->valideRight()){
			$reply = new reply();
			if($form->replyTo($reply,(isset($_POST)?$_POST:array()),(isset($_FILES)?$_FILES:array()))){
				$reply->sendEmail(true);
				$view->flash("L'ajout s'est effectué avec succès", 'bg-success');
				dims_redirect(form\get_path(array('c'=>'form',"a"=>"show",'id'=>$form->get('id'))));
			}else{
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
				dims_redirect(form\get_path(array('c'=>'answer',"a"=>"new",'id'=>$form->get('id'))));
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(form\get_path(array('c'=>'index')));
		}
		break;
}
