<?php
$view = view::getInstance();
$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);
switch($a){
	default:
	case 'show':
		$reply_id = dims_load_securvalue('reply_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$ajax = 0;
		$ajax = dims_load_securvalue('ajax', dims_const::_DIMS_NUM_INPUT, true, true, false,$ajax);
		if($ajax) {
			ob_clean();
		}
		
		$reply = reply::find_by(array('id'=>$reply_id,'id_forms'=>$forms_id),null,1);
		$get = array(
			'articleid' => $articleid,
			'a' => 'create',
		);
		if(!empty($reply)){
			$view->assign('values',$reply->getFields(false));
			$view->assign('reply_id',$reply_id);
			$get['a'] = 'update';
			$get['idr'] = $reply_id;
		}else{
			$view->assign('values',array());
			$view->assign('reply_id',0);
		}
		$view->assign('action_form_'.$form->get('id'),form\get_path($get));

		if(!empty($_SESSION['dims']['front_template_name']) && file_exists(DIMS_APP_PATH."templates/frontoffice/".$_SESSION['dims']['front_template_name']."/forms/_preview.tpl.php")){
			$view->render("../../../templates/frontoffice/".$_SESSION['dims']['front_template_name']."/forms/_preview.tpl.php");
		}else{
			$view->render('form/_preview.tpl.php');
		}
		if($ajax) {
			ob_flush();
			die();
		}
		break;
	case 'update':
		if(isset($_POST['comment']) && empty($_POST['comment'])){
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$reply = reply::find_by(array('id'=>$id,'id_module'=>$_SESSION['dims']['moduleid']),null,1);
			if(!empty($reply)){
				if($form->replyTo($reply,(isset($_POST)?$_POST:array()),(isset($_FILES)?$_FILES:array()))){
					$reply->sendEmail(false);
					$view->flash(nl2br($form->get('cms_response')), 'bg-success');
				}else{
					$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
				}
			}else{ // à ce moment là on fait un create
				$reply = new reply();
				if($form->replyTo($reply,(isset($_POST)?$_POST:array()),(isset($_FILES)?$_FILES:array()))){
					$reply->sendEmail(true);
					$view->flash($form->get('cms_response'), 'bg-success');
					dims_redirect(form\get_path(array('c'=>'form',"a"=>"show",'id'=>$form->get('id'))));
				}else{
					$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
				}
			}
		} // on a affaire à un bot
		global $article;
		$urlrewrite = $article->get('urlrewrite');
		if(empty($urlrewrite)){
			dims_redirect(form\get_path(array('articleid'=>$article->get('id'),"a"=>"validate")));
		}else{
			dims_redirect("/".$urlrewrite.".html&a=validate");
		}
		break;
	case 'create':
		if(isset($_POST['comment']) && empty($_POST['comment'])){
			$reply = new reply();
			if($form->replyTo($reply,(isset($_POST)?$_POST:array()),(isset($_FILES)?$_FILES:array()))){
				$reply->sendEmail(true);
				$view->flash(nl2br($form->get('cms_response')), 'bg-success');
			}else{
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			}
		} // on a affaire à un bot
		global $article;
		$urlrewrite = $article->get('urlrewrite');
		if(empty($urlrewrite)){
			dims_redirect(form\get_path(array('articleid'=>$article->get('id'),"a"=>"validate")));
		}else{
			dims_redirect("/".$urlrewrite.".html&a=validate");
		}
		break;
	case 'validate':
		$view->render('form/front_validated.tpl.php');
		break;
}
