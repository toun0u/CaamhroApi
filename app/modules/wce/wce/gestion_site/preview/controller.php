<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,false);

/**
*	A ce moment précis on se situe dans le backoffice
*	Mais on souhaite afficher les styles du front
*	Donc on reset les styles déjà inclus et on inclut les styles du front
*/

// Reset
$vue = View::getInstance();
$manager = $vue->getStylesManager();
unset($manager->styles);

switch($action){
	default:
	case module_wce::_PREVIEW_DEF:
		if (empty($articleid)){

			$heading->open($headingid);
			$articleid = $heading->getRedirectArticle();
			if (is_numeric($articleid)){
				if ($articleid != '' && $articleid > 0){
					$article = new wce_article();
					$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
					dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid=".$article->fields['id_heading']."&articleid=$articleid");
				}else{
					$article = $heading->getFirstPage();
					if($article != 0 && count($article) > 0){
						dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid=".$article['id_heading']."&articleid=".$article['id']);
					}else{
						foreach($heading->getAllRubriques() as $head){
							$articleid = $head->getRedirectArticle();
							if ($articleid != '' && $articleid > 0){
								$article = new wce_article();
								$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
								dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid=".$article->fields['id_heading']."&articleid=$articleid");
							}else{
								$article = $head->getFirstPage();
								if($article != 0 && count($article) > 0){
									dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid=".$article['id_heading']."&articleid=".$article['id']);
								}
							}
						}
					}
					dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=$headingid");
				}
			}else{
				$article = new wce_article();
				$article->init_description();
				$article->setLightAttribute('wce_mode','display');
				$article->setLightAttribute('url',$articleid);
				$article->display(module_wce::getTemplatePath("gestion_site/preview/preview_article.tpl.php"));
			}
		}else{

			$_SESSION['wce']['articleid'] = $articleid;
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			if ($article->fields['id_article_link'] != '' && $article->fields['id_article_link'] > 0){
				$article2 = new wce_article();
				$article2->open($article->fields['id_article_link'],$article->fields['id_lang']);
				dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid=".$article2->fields['id_heading']."&articleid=".$article2->fields['id']);
			}else{
				$article->updateInternalLinks();
				$article->setLightAttribute('wce_mode','display');
				$article->setLightAttribute('url',"");
				$article->display(module_wce::getTemplatePath("gestion_site/preview/preview_article.tpl.php"));
			}
		}
		break;
	case module_wce::_PREVIEW_EDIT:
		if (empty($articleid)){
			$heading = new wce_heading();
			$heading->open($headingid);

			$articleid = $heading->getRedirectArticle();
			if (is_numeric($articleid)){
				if ($articleid != '' && $articleid > 0){
					$article = new wce_article();
					$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
					dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=$articleid");
				}else
					dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PREVIEW_EDIT."&headingid=$headingid");
			}else{
				$article = new wce_article();
				$article->init_description();
				$article->setLightAttribute('wce_mode','display');
				$article->setLightAttribute('url',$articleid);
				$article->display(module_wce::getTemplatePath("gestion_site/preview/preview_article.tpl.php"));
			}
		}else{
			$_SESSION['wce']['articleid'] = $articleid;
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			if ($article->fields['id_article_link'] != '' && $article->fields['id_article_link'] > 0){
				$article2 = new wce_article();
				$article2->open($article->fields['id_article_link'],$article->fields['id_lang']);
				dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article2->fields['id_heading']."&articleid=".$article2->fields['id']);
			}else{
				$article->updateInternalLinks();
				$article->setLightAttribute('wce_mode','edit');
				$article->setLightAttribute('url',"");

			//	var_dump($article); die();

				$article->display(module_wce::getTemplatePath("gestion_site/preview/preview_article.tpl.php"));
			}
		}

		break;
	case module_wce::_PREVIEW_ART:
		$article = new wce_article();
		$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
		ob_clean();
		$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
		die();
		break;
	case module_wce::_PREVIEW_SAVE:

		break;
	case module_wce::_DELETE_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
			$articleid = $block->fields['id_article'];
			$block->delete();
			$article = new wce_article();
			$article->open($articleid,$id_lang);
			$article->setUpToDate(0);
			$article->save();
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT);
		break;
	case module_wce::_ACTION_ART_RIGHT_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
			$articleid = $block->fields['id_article'];
			$article = new wce_article();
			$article->open($articleid,$id_lang);
			if ($block->fields['level']<wce_article::NB_LEVEL) {
                $block->fields['level']++;
                if($block->isModify()){
					$block->setUpToDate(0);
					$article->setUpToDate(0);
				}
                $block->save();
                $article->save();//mettre à jour le timestp_modify
            }
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT);
		break;
	case module_wce::_ACTION_ART_LEFT_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
			$articleid = $block->fields['id_article'];
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			if ($block->fields['level']>1) {
                $block->fields['level']--;
                if($block->isModify()){
					$block->setUpToDate(0);
					$article->setUpToDate(0);
				}
                $block->save();
                $article->save();//mettre à jour le timestp_modify
            }
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT);
		break;
	case module_wce::_ACTION_ART_UP_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
            $blockvoisin = new wce_block();
			$block->open($block_id,$id_lang);
			$articleid = $block->fields['id_article'];
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);

			if ($block->fields['position']>1) {
				// on recupere les elements du dessous
				// on verifie si on peut incrémenter la position
				$sel = "SELECT		id
						FROM		dims_mod_wce_article_block
						WHERE		id_article = :id_article
						AND         position=:position
						AND 		id_lang = :id_lang
						LIMIT 	 	0,1";
				$db = dims::getInstance()->getDb();

				$res = $db->query($sel,array(':id_article'=>array('value'=>$_SESSION['wce']['articleid'],'type'=>PDO::PARAM_INT),
												':position'=>array('value'=>($block->fields['position']-1),'type'=>PDO::PARAM_INT),
												':id_lang'=>array('value'=>$article->fields['id_lang'],'type'=>PDO::PARAM_INT)));
				$nblevel=wce_article::NB_LEVEL;
				$elemlevel=array();
				if ($db->numrows($res)>0) {
					while ($f=$db->fetchrow($res)) {
						$blockvoisin->open($f['id']);
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
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT);
		break;
	case module_wce::_ACTION_ART_DOWN_BLOC:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
            $blockvoisin = new wce_block();
			$block->open($block_id,$id_lang);
			$articleid = $block->fields['id_article'];
			$article = new wce_article();
			$article->open($articleid,$id_lang);

			// on verifie si on peut incrémenter la position
			$sel = "SELECT		*
					FROM		dims_mod_wce_article_block
					WHERE		id_article = :id_article
					AND 		id_lang = :id_lang";
			$db = dims::getInstance()->getDb();

			$res = $db->query($sel,array(':id_article'=>array('value'=>$_SESSION['wce']['articleid'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$article->fields['id_lang'],'type'=>PDO::PARAM_INT)));
			$nbelem=$db->numrows($res);

			if ($block->fields['position']<$nbelem) {
				// on recupere les elements du dessous
				// on verifie si on peut incrémenter la position
				$sel = "SELECT		id
						FROM		dims_mod_wce_article_block
						WHERE		id_article = :id_article
						AND         position>:position
						AND 		id_lang = :id_lang
						LIMIT  		0,1";

				$res = $db->query($sel,array(':id_article'=>array('value'=>$_SESSION['wce']['articleid'],'type'=>PDO::PARAM_INT),
												':position'=>array('value'=>$block->fields['position'],'type'=>PDO::PARAM_INT),
												':id_lang'=>array('value'=>$article->fields['id_lang'],'type'=>PDO::PARAM_INT)));
				$nblevel=wce_article::NB_LEVEL;
				$elemlevel=array();
				if ($db->numrows($res)>0) {
					while ($f=$db->fetchrow($res)) {
						$blockvoisin->open($f['id']);
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
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT);
		break;
	case module_wce::_ACTION_ART_SAVE_BLOC_AJAX:
		ob_end_clean();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);

		if ($block_id>0 && $content_id>0) {
			$block = new wce_block();
			$block->open($block_id,$id_lang);
            // must adding HTML filter
			$content= dims_load_securvalue('contentBlockReturn'.$block_id.'_'.$content_id, dims_const::_DIMS_CHAR_INPUT, true, true, true);
            $block->fields['draftcontent'.$content_id] = preg_replace('/<span.*>(\[\[ [0-9]+,[0-9]+(,[0-9]+)?\/.+ &gt; .+( &gt; .+)? \]\])<\/span>/','${1}',$content);
			$block->fields['timestp_modify']=dims_createtimestamp();
			$article = new wce_article();
			$article->open($block->fields['id_article'],$id_lang);
			if($block->isModify()){
				$block->setUpToDate(0);
				$article->setUpToDate(0);
			}
			$block->save();
			$article->save();//met à jour le timestp_modify
		}
		die();
		break;
	case module_wce::_ACTION_ART_SAVE_BLOC_LITTLE:
		ob_end_clean();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$article_id=dims_load_securvalue('wce_block_id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($block_id>0) {
			$block = new wce_block();
			$block->open($block_id);
			if ($block->fields['id_article'] == $article_id) {
				$block->setvalues($_POST,'wce_block_');
				$art = new wce_article();
				$art->open($article_id);
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
	case module_wce::_ACTION_VALID_ARTICLE:
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
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']."&lang=$id_lang");
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT);
		break;
	case module_wce::_PREVIEW_BLOC_SAVE:
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$section_id=dims_load_securvalue('section',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true);

		$article = new wce_article();
		$article->open(dims_load_securvalue('wce_block_id_article',dims_const::_DIMS_NUM_INPUT,true,true));
		$block = new wce_block();
		if ($block_id>0) {
			$block->open($block_id,$id_lang);
			$new = false;
		}
		else {
			$block->init_description(true);
			$block->fields['section']=$section_id;
            $block->fields['level']=1;
            $block->fields['id_lang'] = $id_lang;
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
							SET 		position=:position
							WHERE 		id = :id",
							array(':id'=>array('value'=>$block->fields['id'],'type'=>PDO::PARAM_INT),
									':position'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
			$block->fields['position'] = $newposition;
		}
		else {
			$block->fields['position']=$newposition;
			// on update tous ceux qui sont apres la position courante choisie
			$res=$db->query("UPDATE 	{$tablename}
							SET 		position=position+1
							WHERE 		position>=:position
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
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']."&lang=".$block->fields['id_lang']);
		break;
}
?>
