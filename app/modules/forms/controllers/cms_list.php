<?php
$view = view::getInstance();
$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);
switch($a){
	default:
	case 'list':
		$accesscode = $form->get('accesscode');

		global $article;
		$urlrewrite = $article->get('urlrewrite');
		if(empty($urlrewrite)){
			$urlSend = form\get_path(array('articleid'=>$article->get('id')));
		}else{
			$urlSend = "/".$urlrewrite.".html";
		}

		if(empty($accesscode) || (isset($_SESSION['dims']['forms']['list'][$form->get('id')]) && $_SESSION['dims']['forms']['list'][$form->get('id')])){
			$view->assign('urlSend',$urlSend);

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
			$view->render('form/front_list.tpl.php');
		}else{
			$view->assign('urlSend',$urlSend."&a=valide");
			$view->render('form/front_list_accesscode.tpl.php');
		}
		break;
	case 'valide':
		global $article;
		$accesscode = $form->get('accesscode');
		$ac = dims_load_securvalue('ac', dims_const::_DIMS_CHAR_INPUT, true, true);
		if(empty($accesscode) || $accesscode == $ac){
			$_SESSION['dims']['forms']['list'][$form->get('id')] = true;
		}
		if(empty($urlrewrite)){
			$urlSend = form\get_path(array('articleid'=>$article->get('id')));
		}else{
			$urlSend = "/".$urlrewrite.".html";
		}
		dims_redirect($urlSend);
		break;
	case 'export':
		$accesscode = $form->get('accesscode');
		if(empty($accesscode) || (isset($_SESSION['dims']['forms']['list'][$form->get('id')]) && $_SESSION['dims']['forms']['list'][$form->get('id')])){
			$search = dims_load_securvalue('s',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$form->export('CSV',$search,true);
		}else{
			global $article;
			$urlrewrite = $article->get('urlrewrite');
			if(empty($urlrewrite)){
				$urlSend = form\get_path(array('articleid'=>$article->get('id')));
			}else{
				$urlSend = "/".$urlrewrite.".html";
			}
		}
		break;
}
