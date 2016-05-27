<?php

$view = view::getInstance();

$sub_action = $view->get('sa');

switch($sub_action){
	default:
	case 'edit':
		$view->assign('lst_thumbnails',$article->getThumbnails());
		$view->render('articles/show/vignettes_edit.tpl.php');
		break;
	case 'add':
		$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$article->fields['id']."&sa=save");
		$view->assign('back_path',dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$article->fields['id']);
		$view->assign('nb_thumbnails',$article->getNbThumbnails());
		$view->render('articles/show/vignettes_add.tpl.php');
		break;
	case 'save':
		if(isset($_FILES['file']['error']) && $_FILES['file']['error'] === 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_thumb.php";
			$doc = new docfile();
			$doc->init_description();
			$doc->setugm();
			$doc->fields['id_folder'] = -1;
			$doc->tmpuploadedfile = $_FILES['file']['tmp_name'];
			$doc->fields['name'] = $_FILES['file']['name'];
			$doc->fields['size'] = filesize($_FILES['file']['tmp_name']);
			$doc->save();

			$lk = new cata_art_thumb();
			$lk->init_description();
			$lk->setugm();
			$lk->fields['id_article'] = $article->fields['id'];
			$lk->setDocFile($doc);
			$lk->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$article->fields['id']);
		break;
	case 'delete':
		$id_doc = dims_load_securvalue('doc',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id_doc != '' && $id_doc > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_thumb.php";
			$thumb = new cata_art_thumb();
			$thumb->open($article->fields['id'],$id_doc);
			$thumb->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$article->fields['id']);
		break;
	case 'down':
		$id_doc = dims_load_securvalue('doc',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id_doc != '' && $id_doc > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_thumb.php";
			$thumb = new cata_art_thumb();
			$thumb->open($article->fields['id'],$id_doc);
			if($thumb->fields['position'] > 1)
				$thumb->fields['position'] --;
			$thumb->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$article->fields['id']);
		break;
	case 'up':
		$id_doc = dims_load_securvalue('doc',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id_doc != '' && $id_doc > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_thumb.php";
			$thumb = new cata_art_thumb();
			$thumb->open($article->fields['id'],$id_doc);
			$thumb->fields['position'] ++;
			$thumb->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=articles&a=show&sc=vignettes&id=".$article->fields['id']);
		break;
}
?>
