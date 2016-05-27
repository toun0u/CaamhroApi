<?php
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block.php';

$contactadd = new contact();
$contactadd->init_description();
$id = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true, true, $_SESSION['wiki']['articleid']);

$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
if (empty($id_lang) || $id_lang <= 0){
	$site = new wce_site(dims::getInstance()->getDb(),$_SESSION['dims']['moduleid']);
	$id_lang = $site->getDefaultLanguage();
}

if(!isset($_SESSION['dims']['wce_default_lg'])){
	if (!isset($site))
		$site = new wce_site(dims::getInstance()->getDb(),$_SESSION['dims']['moduleid']);
	$_SESSION['dims']['wce_default_lg'] = $site->getDefaultLanguage();
}

$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $id_lang;

$article = new wce_article();
$new_article = true;
if ($id != '' && $id > 0){
	$article->open($id,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
	$new_article = false;
	$user=new user();
	$user->open($article->fields['id_user']);
	$contactadd->open($user->fields['id_contact']);

	//------------- gestion de l'historique de navigation --------------
	module_wiki::handleHistoric($_SESSION['dims']['wiki']['historic'], $article, 5);
	//-------------------------------------------------------------------
}

if (!isset($_SESSION['dims']['wiki']['wce_mode'])) $_SESSION['dims']['wiki']['wce_mode']='edit';
$wce_mode=dims_load_securvalue('wce_mode',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['wiki']['wce_mode']);
$article->setLightAttribute('wce_mode', $wce_mode);
$dd = dims_timestamp2local($article->fields['timestp_modify']);

$versionid=dims_load_securvalue("versionid",dims_const::_DIMS_NUM_INPUT,true,true);

if( ! isset($_SESSION['dims']['wiki']['article']['action'])) $_SESSION['dims']['wiki']['article']['action'] = module_wiki::_ACTION_SHOW_ARTICLE;
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true, false, $_SESSION['dims']['wiki']['article']['action'], module_wiki::_ACTION_SHOW_ARTICLE);

switch($action){
	default:
	case module_wiki::_ACTION_SHOW_ARTICLE:

		if($new_article){
			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_HOMEPAGE));
		}
		else{
			$_SESSION['wiki']['articleid'] = $article->fields['id'];

			// ajout d'une méthode de vérification des liens construits avec nettoyage
			$article->updateInternalLinks();

			/*
			* inclusion du menu d'edition des articles wiki
			*/
			$article->setLightAttribute('user',$user);
			$article->setLightAttribute('contact',$contactadd);
			$article->setLightAttribute('dd',$dd);

			$article->display(module_wiki::getTemplatePath('/article/edition_article_menu.tpl.php'));

			$article->display(module_wiki::getTemplatePath('/article/edition_article.tpl.php'));
		}
		break;
	case module_wiki::_ACTION_SHOW_ARTICLE_NEWSLETTER:
			$article->display(module_wiki::getTemplatePath('/article/edition_article.tpl.php'));
			break;
	case module_wiki::_ACTION_EDIT_ARTICLE:
		ob_clean();
		include module_wiki::getTemplatePath('/article/display_wiki.php');
		die();
		break;

	case module_wiki::_ACTION_ART_SAVE_BLOC_LITTLE:
		ob_end_clean();

		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('wce_block_id_lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$article_id=dims_load_securvalue('wce_block_id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
						$block = new wce_block();
			$block->open($block_id,$id_lang);
			if ($block->fields['id_article'] == $article_id) {
				$block->setvalues($_POST,'wce_block_');
				$art = new wce_article();
				$art->open($article_id,$id_lang);
				if($block->isModify()){
					$block->setUpToDate(0);
					$art->setUpToDate(0);
				}
				$block->save();
				$art->save(); //pour mettre à jour le timestp_modify
			}
		}
		echo '<script type="text/javascript">window.parent.refreshWceIframe();</script>';
		die();
		break;
	case module_wiki::_ACTION_ART_SAVE_BLOC: // TODO : gérer id_lang
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$section_id=dims_load_securvalue('section',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('wce_block_id_lang',dims_const::_DIMS_NUM_INPUT,true,true);

		$block = new wce_block();
		if ($block_id>0) {
			$block->open($block_id,$id_lang);
			$new = false;
		}
		else {
			$block->init_description(true);
			$block->fields['section']=$section_id;
			$block->fields['level']=1;
			$block->setugm();
			$block->fields['position']=1;
			$new = false;
		}
		$block->fields['page_break'] = 0;
		$block->setvalues($_POST,'wce_block_');
		if(empty($block->fields['id_article']) ) $block->fields['id_article'] = $article->getId();
		$tablename="dims_mod_wce_article_block";

		if (empty($_POST['wce_block_display_title'])) $block->fields['display_title'] = 0;

		$newposition=dims_load_securvalue('block_position',dims_const::_DIMS_NUM_INPUT,false,true);

		if ($newposition != $block->fields['position'] && $block->fields['id']>0) { // nouvelle position définie
			if ($newposition > $block->fields['position']) {
				$res=$db->query("UPDATE 	{$tablename}
								SET 		position=position-1
								WHERE 		position BETWEEN :oldpos AND :newpos
								AND 		position>0
								AND 		id_article = :id_article
								AND 		section = :section
								AND 		id_lang = :id_lang",
								array(':id_article'=>array('value'=>$block->fields['id_article'],'type'=>PDO::PARAM_INT),
										':section'=>array('value'=>$block->fields['section'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT),
										':oldpos'=>array('value'=>($block->fields['position']+1),'type'=>PDO::PARAM_INT),
										':newpos'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
			}else {
				$res=$db->query("UPDATE 	{$tablename}
								SET 		position=position+1
								WHERE 		position BETWEEN :newpos AND :oldpos
								AND 		id_article = :id_article
								AND 		section = :section
								AND 		id_lang = :id_lang",
								array(':id_article'=>array('value'=>$block->fields['id_article'],'type'=>PDO::PARAM_INT),
										':section'=>array('value'=>$block->fields['section'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT),
										':oldpos'=>array('value'=>($block->fields['position']-1),'type'=>PDO::PARAM_INT),
										':newpos'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
			}
			$res=$db->query("UPDATE 	{$tablename}
							SET 		position = :position
							WHERE 		id = :id
							AND 		id_lang = :id_lang",
							array(':id'=>array('value'=>$block->fields['id'],'type'=>PDO::PARAM_INT),
									':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT),
									':position'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
			$block->fields['position'] = $newposition;
		}
		else {
			$block->fields['position']=$newposition;
			// on update tous ceux qui sont apres la position courante choisie
			$res=$db->query("UPDATE 	{$tablename}
							SET 		position = position+1
							WHERE 		position >= :position
							AND 		id_article = :id_article
							AND 		section = :section
							AND 		id_lang = :id_lang",
							array(':id_article'=>array('value'=>$block->fields['id_article'],'type'=>PDO::PARAM_INT),
									':section'=>array('value'=>$block->fields['section'],'type'=>PDO::PARAM_INT),
									':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT),
									':position'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
		}
		if($new) $article->setUpToDate(0);
		else if($block->isModify()){
			$block->setUpToDate(0);
			$article->setUpToDate(0);
		}

		$block->save();
		$article->save();//mise à jour du timestp_modify
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=".$block->fields['id_lang']));
		break;

	case module_wiki::_ACTION_ART_UP_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
			if ($block->fields['level']<wce_article::NB_LEVEL) {
				$block->fields['level']++;
				if($block->isModify()){
					$block->setUpToDate(0);
					$article->setUpToDate(0);
				}
				$block->save();
				$article->save();//mettre à jour le timestp_modify
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		break;
	case module_wiki::_ACTION_ART_DOWN_BLOC:

		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
			if ($block->fields['level']>1) {
				$block->fields['level']--;
				if($block->isModify()){
					$block->setUpToDate(0);
					$article->setUpToDate(0);
				}
				$block->save();
				$article->save();//mettre à jour le timestp_modify
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		break;

	case module_wiki::_ACTION_ART_POSITION_UP_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($block_id>0) {
			$block = new wce_block();
						$blockvoisin = new wce_block();
			$block->open($block_id,$id_lang);

			if ($block->fields['position']>1) {
				// on recupere les elements du dessous
				// on verifie si on peut incrémenter la position
				$sel = "SELECT		id,id_lang
						FROM		dims_mod_wce_article_block
						WHERE		id_article = :id_article
						AND			id_lang = :id_lang
						AND			position = :position
						LIMIT 		0,1";
				$db = dims::getInstance()->getDb();

				$res = $db->query($sel,
								array(':id_article'=>array('value'=>$_SESSION['wiki']['articleid'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT),
										':position'=>array('value'=>($block->fields['position']-1),'type'=>PDO::PARAM_INT)));
				$nblevel=wce_article::NB_LEVEL;
				$elemlevel=array();
				if ($db->numrows($res)>0) {
					while ($f=$db->fetchrow($res)) {
						$blockvoisin->open($f['id'],$f['id_lang']);
						for ($j=1;$j<=$nblevel;$j++) {
							$elemlevel[$j]=$blockvoisin->fields['l'.$j];
							// on inverse
							$blockvoisin->fields['l'.$j]=$block->fields['l'.$j];
						}
						$elemlevel['level']=$blockvoisin->fields['level'];

						$blockvoisin->fields['level']=$block->fields['level'];
					}

					// on remonte l'element du dessus en dessous
					$blockvoisin->fields['position']=$blockvoisin->fields['position']+1;

					$block->fields['position']=$block->fields['position']-1;

					// on affecte sur le bloc courant
					for ($j=1;$j<=$nblevel;$j++) {
						$block->fields['l'.$j]=$elemlevel[$j];
					}
					$block->fields['level']=$elemlevel['level'];
					//dims_print_r($block->fields);
					//dims_print_r($blockvoisin->fields);
					//die();
					// on sauvegarde
					$block->save();
					$blockvoisin->save();
				}
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		break;
	case module_wiki::_ACTION_ART_POSITION_DOWN_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($block_id>0) {
			$block = new wce_block();
			$blockvoisin = new wce_block();
			$block->open($block_id,$id_lang);

			// on verifie si on peut incrémenter la position
			$sel = "SELECT		*
					FROM		dims_mod_wce_article_block
					WHERE		id_article = :id_article
					AND			id_lang = :id_lang";

			$db = dims::getInstance()->getDb();

			$res = $db->query($sel,
							array(':id_article'=>array('value'=>$_SESSION['wiki']['articleid'],'type'=>PDO::PARAM_INT),
									':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT)));
			$nbelem=$db->numrows($res);

			if ($block->fields['position']<$nbelem) {
				// on recupere les elements du dessous
				// on verifie si on peut incrémenter la position
				$sel = "SELECT		id,id_lang
						FROM		dims_mod_wce_article_block
						WHERE		id_article = :id_article
						AND			id_lang = :id_lang
						AND			position > :position
						ORDER BY 	position
						LIMIT 		0,1";

				$res = $db->query($sel,
								array(':id_article'=>array('value'=>$_SESSION['wiki']['articleid'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$block->fields['id_lang'],'type'=>PDO::PARAM_INT),
										':position'=>array('value'=>$block->fields['position'],'type'=>PDO::PARAM_INT)));
				$nblevel=wce_article::NB_LEVEL;
				$elemlevel=array();
				if ($db->numrows($res)>0) {
					while ($f=$db->fetchrow($res)) {
						$blockvoisin->open($f['id'],$f['id_lang']);
						for ($j=1;$j<=$nblevel;$j++) {
							$elemlevel[$j]=$blockvoisin->fields['l'.$j];
							// on inverse
							$blockvoisin->fields['l'.$j]=$block->fields['l'.$j];
						}
						$elemlevel['level']=$blockvoisin->fields['level'];

						$blockvoisin->fields['level']=$block->fields['level'];
					}

					// on remonte l'element du dessus en dessous
					$blockvoisin->fields['position']=$blockvoisin->fields['position']-1;

					$block->fields['position']=$block->fields['position']+1;

					// on affecte sur le bloc courant
					for ($j=1;$j<=$nblevel;$j++) {
						$block->fields['l'.$j]=$elemlevel[$j];
					}
					$block->fields['level']=$elemlevel['level'];
					//dims_print_r($block->fields);
					//dims_print_r($blockvoisin->fields);
					//die();
					// on sauvegarde
					$block->save();
					$blockvoisin->save();
				}
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		break;

	case module_wiki::_ACTION_ART_DEL_BLOC:
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
			$block->delete();
			$article->setUpToDate(0);
			$article->save();//mettre à jour le timestp_modify
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		break;
	case module_wiki::_ACTION_ART_SAVE_BLOC_C:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0 && $content_id>0) {
			$block = new wce_block();
			$block->open($block_id);
						// must adding HTML filter
			$content= dims_load_securvalue('wce_article_draftcontent', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$block->fields['draftcontent'.$content_id]=$content;
			$block->fields['timestp_modify']=dims_createtimestamp();
			if($block->isModify()){
				$block->setUpToDate(0);
				$article->setUpToDate(0);
			}
			$block->save();
			$article->save();//mettre à jour le timestp_modify
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE));
		break;
	case module_wiki::_ACTION_ART_SAVE_BLOC_C_AJAX:
		ob_end_clean();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$idLang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0 && $content_id>0 && $idLang > 0) {
			$block = new wce_block();
			$block->open($block_id,$idLang);
			// must adding HTML filter
			$content= dims_load_securvalue('contentBlockReturn'.$block_id.'_'.$content_id, dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$block->fields['draftcontent'.$content_id] = preg_replace('/<span.*>(\[\[ [0-9]+,[0-9]+(,[0-9]+)?\/.+ &gt; .+( &gt; .+)? \]\])<\/span>/','${1}',$content);
			$block->fields['timestp_modify']=dims_createtimestamp();
			$article = new wce_article();
			$article->open($block->fields['id_article'],$block->fields['id_lang']);
			if($block->isModify()){
				$block->setUpToDate(0);
				$article->setUpToDate(0);
			}
			$block->save();
			$article->save();//met à jour le timestp_modify
		}
		die();
		break;
	case module_wiki::_ACTION_ART_SAVE_PROPERTIES:
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$article = new wce_article();
		if ($id != '' && $id > 0){
			$article->open($id);
			$article->setvalues($_POST,"wce_article_");
			$article->save();
		}else {
			// on initialise l'article
			$heading = module_wiki::getRootHeading();

			$article->init_description();
			$article->setugm();
			$article->fields['author'] = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
			$article->setvalues($_POST,"wce_article_");
			$article->fields['id_heading']=$heading->fields['id'];
			$article->fields['visible']=0;
			$article->fields['type']=  module_wiki::_TYPE_WIKI;
			$article->fields['model']=module_wiki::_ARTICLE_DEFAULT_MODEL;
			//$lstL = current(wce_lang::getInstance()->getAll(true));
			//$article->fields['id_lang']=(isset($lstL->fields['id'])?$lstL->fields['id']:1);
			$article->fields['id_lang']=1;
			$article->save();
		}
		$id_categ = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($id_categ != '' && $id_categ > 0){
			$categ = new category();
			$categ->open($id_categ);

			$gb = new dims_globalobject();
			$gb->open($article->fields['id_globalobject']);
			$gb->deleteLink($article->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY));
			$gb->addLink($categ->fields['id_globalobject']);
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$article->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE));
		break;


	/* Cyril - Gestion des messages / todos de l'article */
	case module_wiki::_COLLABORATION_VIEW:

				$article->setLightAttribute('user',$user);
				$article->setLightAttribute('contact',$contactadd);
				$article->setLightAttribute('dd',$dd);

				$article->display(module_wiki::getTemplatePath('/article/edition_article_menu_todo.tpl.php'));
		$go = new dims_globalobject();
		$go->open($article->fields['id_globalobject']);
		$go->setLightAttribute('keep_context', '&dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$article->getId().'&action='.module_wiki::_COLLABORATION_VIEW.'&wce_mode=render');
		$go->setLightAttribute('title_object', $article->fields['title']);
		$go->setLightAttribute('on_the_record', $_SESSION['cste']['ON_THE_RECORD_OF_THE_ARTICLE']);
		$go->setLightAttribute('mail_link', dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/admin.php?dims_moduleid='.$_SESSION['dims']['moduleid'].'&dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$article->getId().'&action='.module_wiki::_COLLABORATION_VIEW.'&wce_mode=render&todo_op='.dims_const::_SHOW_COLLABORATION.'#todo_');
		$go->display(DIMS_APP_PATH.'/include/controllers/todos/controller.php');
		break;

	/* Cyril - Gestion des paramètres de l'article */
	case module_wiki::_PARAMETERS_VIEW:

		$article->display(module_wiki::getTemplatePath('/article/parameters/controller.php'));
		break;
	case module_wiki::_ACTION_VALID_ARTICLE:

		$id = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$article = new wce_article();
			$article->open($id,$id_lang);
			if (empty($id_lang) || $id_lang <= 0){
				$site = new wce_site($article->db,$article->fields['id_module']);
				$id_lang = $site->getDefaultLanguage();
			}
			$article->publish($id_lang);
			$article->valide($id_lang);
			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$article->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_HOMEPAGE));
		break;
	case module_wiki::_ACTION_IMPORT_LANG_ART:
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$delete_content = dims_load_securvalue('delete_content',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			if (isset($_FILES) && isset($_FILES['file']) && $_FILES['file']['error'] == 0){
				$article = new wce_article();
				$article->open($id);
				$id_lang = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true,true);
				$article->setContentXml($_FILES['file']['tmp_name'],$id_lang,$delete_content);
			}
			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$article->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$id_lang"));
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$_SESSION['wiki']['articleid']."&action=".module_wiki::_ACTION_SHOW_ARTICLE));
		break;
	case module_wiki::_ACTION_GENERATE_NEW_LANG:
		$id = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true);
		$currLang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$newLang = dims_load_securvalue('newlang',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			$article = new wce_article();
			$article->open($id);
			$article->generateNewLang($currLang,$newLang);
			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$article->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=$newLang"));
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$article->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE));
		break;
}
?>
