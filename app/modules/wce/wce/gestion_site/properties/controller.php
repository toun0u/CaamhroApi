<?
require_once DIMS_APP_PATH."include/class_input_validator.php";

$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
	case module_wce::_PROPERTIES_DEF:
		$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($articleid != '' && $articleid > 0){
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			// test existance sinon redirection sur le premier menu
			if (isset($article->fields['id_heading']) && $article->fields['id_heading']>0)
				$article->display(module_wce::getTemplatePath("gestion_site/properties/edit_article.tpl.php"));
			else
				dims_redirect ('/admin.php?sub2='.module_wce::_SUB_SITE);
		}else{
			$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($headingid != '' && $headingid > 0){
				$heading = new wce_heading();
				$heading->open($headingid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			}else{
				$lstHeadings = wce_heading::getAllHeadings();
				$heading = current($lstHeadings);
			}

			// test existance sinon redirection sur le premier menu
			if (isset($heading->fields['id_module']) && $heading->fields['id_module']>0)
				$heading->display(module_wce::getTemplatePath("gestion_site/properties/edit_heading.tpl.php"));
			else
				dims_redirect ('/admin.php?sub2='.module_wce::_SUB_SITE);
		}
		break;
	case module_wce::_PROPERTIES_SAVE_ART:
		$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($articleid != '' && $articleid > 0){
			$article = new wce_article();
			$id_lang = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$article->open($articleid,$id_lang);
			$db = dims::getInstance()->getDb();
			$newPosit = dims_load_securvalue('art_position',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$oldPosit = $article->fields['position'];

			$article->fields['visible'] = 0;
			$article->fields['edito'] = 0;
			$article->fields['url_window'] = 0;
			$article->fields['is_sitemap'] = 0;
			$article->fields['edito'] = 0;
			$article->fields['actu'] = 0;
			if ($article->fields['first_page'] != dims_load_securvalue('art_first_page',dims_const::_DIMS_NUM_INPUT,true,true,false))
				$article->fields['timestp_modify_first'] = dims_createtimestamp();
			$article->fields['first_page'] = 0;

			$article->setvalues($_POST,'art_');

			if ($newPosit != $oldPosit){
				if ($newPosit<1)
					$newPosit=1;
				else {
					$select = "	SELECT	MAX(position) as maxpos
								FROM	".wce_article::TABLE_NAME."
								WHERE	id_heading = :id_heading";
					$res=$db->query($select,array(':id_heading'=>array('value'=>$article->fields['id_heading'],'type'=>PDO::PARAM_INT)));
					$fields = $db->fetchrow($res);
					if ($newPosit > $fields['maxpos']) $newPosit = $fields['maxpos'];
				}
				if ($newposition > $oldPosit) {
					$res=$db->query("UPDATE		".wce_article::TABLE_NAME."
									SET			position=position-1
									WHERE		position BETWEEN :oldpos
									AND			:newpos
									AND			position>0
									AND			id_heading = :id_heading",
									array(':id_heading'=>array('value'=>$article->fields['id_heading'],'type'=>PDO::PARAM_INT),
										':oldpos'=>array('value'=>($oldPosit-1),'type'=>PDO::PARAM_INT),
										':newpos'=>array('value'=>$newPosit,'type'=>PDO::PARAM_INT)));
				}
				else {
					if (($oldPosit-1)>=$newposition) {
						$res=$db->query("UPDATE		".wce_article::TABLE_NAME."
										SET			position=position+1
										WHERE		position BETWEEN :newpos
										AND			:oldpos
										AND			id_heading = :id_heading",
									array(':id_heading'=>array('value'=>$article->fields['id_heading'],'type'=>PDO::PARAM_INT),
										':oldpos'=>array('value'=>($oldPosit-1),'type'=>PDO::PARAM_INT),
										':newpos'=>array('value'=>$newPosit,'type'=>PDO::PARAM_INT)));
					}
				}
				$res=$db->query("UPDATE		".wce_article::TABLE_NAME."
								SET			position=:newpos
								WHERE		position=:oldpos
								AND			id_heading = :id_heading",
									array(':id_heading'=>array('value'=>$article->fields['id_heading'],'type'=>PDO::PARAM_INT),
										':oldpos'=>array('value'=>$oldPosit,'type'=>PDO::PARAM_INT),
										':newpos'=>array('value'=>$newPosit,'type'=>PDO::PARAM_INT)));
				$article->fields['position'] = $newPosit;
			}
			$article->fields['timestp_published'] = dims_local2timestamp($article->fields['timestp_published']);
			$article->fields['timestp_unpublished'] = dims_local2timestamp($article->fields['timestp_unpublished']);
			$article->save();

			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
			$sel = "SELECT	*
					FROM	".article_object_corresp::TABLE_NAME."
					WHERE	id_article=:id_article";
			$res = $db->query($sel,array(':id_article'=>array('value'=>$article->fields['id'],'type'=>PDO::PARAM_INT)));
			while($r = $db->fetchrow($res)){
				$obj = new article_object_corresp();
				$obj->openFromResultSet($r);
				$obj->delete();
			}

			$objaffect = dims_load_securvalue('obj_affect', dims_const::_DIMS_NUM_INPUT, true, true, true);
			if(is_array($objaffect)) {
				foreach($objaffect as $value) {
					$objt_corresp = new article_object_corresp();
					$objt_corresp->fields['id_article']= $article->fields['id'];
					$objt_corresp->fields['id_object']= $value;
					$objt_corresp->fields['id_heading']= 0;
					$objt_corresp->save();
				}
			}

			if (isset($_FILES['photo']) && !empty($_FILES['photo']) && $_FILES['photo']['name']!='') {
				$time = time();
				$valid = new \InVal\FileValidator('photo');
				$valid->rule(new \InVal\Rule\Image(true));

				if ($valid->validate()) {
					if ($article->fields['picto']!='' && file_exists(realpath('.').'/data/articles/'.$article->fields['picto'])) {
						unlink(realpath('.').'/data/articles/'.$article->fields['picto']);
					}

					$logo_upload = $_FILES['photo'];

					//on recupere l'extension du fichier
					$ext = explode('.', $logo_upload['name']);
					$ext = strtolower($ext[count($ext)-1]);
					dims_makedir(realpath('.').'/data/articles');

					$path = realpath('.').'/data/articles/art_'.$article->fields['id']."_".$time.".".$ext;

					// on va reziser l'image
					$pathtemp=$logo_upload['tmp_name'];
					if (move_uploaded_file($pathtemp,$path)) {
						chmod($path, 0777);
					}

					$pathdest = realpath('.').'/data/articles/art_'.$article->fields['id']."_500.".$ext;
					dims_resizeimage($path, 0, 500, 0,'',0,$pathdest);

					dims_resizeimage($path, 0, 0, 0,'',0,$path,150,150);

					$article->fields['picto']='art_'.$article->fields['id']."_".$time.".".$ext;
				}

				$article->save();
			}

			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_PROPERTIES_SAVE_HEAD:
		$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($headingid != '' && $headingid > 0){
			$heading = new wce_heading();
			$heading->open($headingid,$id_lang);
			$db = dims::getInstance()->db;

			$heading->fields['visible'] = 0;
			$heading->fields['visible_if_connected'] = 0;
			$heading->fields['is_sitemap'] = 0;
			$heading->fields['private'] = 0;
			$heading->fields['url_window'] = 0;

			$newPosit = dims_load_securvalue('head_position',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$oldPosit = $heading->fields['position'];
			$heading->setvalues($_POST,'head_');

			if ($newPosit != $oldPosit) {
				if ($newPosit<1)
					$newPosit=1;
				else {
					$select = "	SELECT	MAX(position) as maxpos
								FROM	".wce_heading::TABLE_NAME."
								WHERE	id_heading = :id_heading
								AND		id_lang = :id_lang
								AND		id_module = :id_module
								AND 		type = 0";
					$res=$db->query($select,array(':id_heading'=>array('value'=>$heading->fields['id_heading'],'type'=>PDO::PARAM_INT),
													':id_lang'=>array('value'=>$heading->fields['id_lang'],'type'=>PDO::PARAM_INT),
													':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
					$fields = $db->fetchrow($res);
					if ($newPosit > $fields['maxpos'])
						$newPosit = $fields['maxpos'];
				}

				if ($newPosit > $oldPosit) {
					$res=$db->query("UPDATE		".wce_heading::TABLE_NAME."
									SET			position=position-1
									WHERE		position BETWEEN :oldpos AND :newpos
									AND			position > 0
									AND			id_lang = :id_lang
									AND			id_heading = :id_heading
									AND			id_module = :id_module
									AND 		type = 0",
									array(':id_heading'=>array('value'=>$heading->fields['id_heading'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$heading->fields['id_lang'],'type'=>PDO::PARAM_INT),
											':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
											':newpos'=>array('value'=>$newPosit,'type'=>PDO::PARAM_INT),
											':oldpos'=>array('value'=>$oldPosit,'type'=>PDO::PARAM_INT)));
				}else{
					$res=$db->query("UPDATE		".wce_heading::TABLE_NAME."
									SET			position=position+1
									WHERE		position BETWEEN :newpos AND :oldpos
									AND			id_lang = :id_lang
									AND			id_heading = :id_heading
									AND			id_module = :id_module
									AND 		type = 0",
									array(':id_heading'=>array('value'=>$heading->fields['id_heading'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$heading->fields['id_lang'],'type'=>PDO::PARAM_INT),
											':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
											':newpos'=>array('value'=>$newPosit,'type'=>PDO::PARAM_INT),
											':oldpos'=>array('value'=>($oldPosit-1),'type'=>PDO::PARAM_INT)));
				}
				$heading->fields['position'] = $newPosit;
			}

			$heading->fields['urlrewrite']=strtolower(dims_convertaccents(html_entity_decode(strip_tags($heading->fields['urlrewrite']))));
			$heading->fields['urlrewrite']=str_replace(array(" ","_"),"-",$heading->fields['urlrewrite']);

			$heading->save();
			$resu=$db->query("	DELETE FROM	dims_mod_wce_object_corresp
								WHERE		id_heading=:id_heading",
								array(':id_heading'=>array('value'=>$heading->fields['id'],'type'=>PDO::PARAM_INT)));

			foreach($_POST as $key => $value) {
				if (substr($key,0,10)=="obj_affect") {
					$idobject=(int)substr($key,10);

					$objt_corresp = new article_object_corresp();
					$objt_corresp->fields['id_heading']= $heading->fields['id'];
					$objt_corresp->fields['id_article'] = 0;
					$objt_corresp->fields['id_object'] = $idobject;
					$objt_corresp->save();
				}
			}

			if (isset($_FILES['photo']) && !empty($_FILES['photo']) && $_FILES['photo']['name']!='') {
				$valid = new \InVal\FileValidator('photo');
				$valid->rule(new \InVal\Rule\Image(true));

				if ($valid->validate()) {
					$time = time();
					if (!file_exists(realpath('./data/headings/'))) {
						dims_makedir(realpath('.').'/data/headings');
					}

					if ($heading->fields['picto']!='' && file_exists(realpath('.').'/data/headings/'.$heading->fields['picto'])) {
						unlink(realpath('.').'/data/headings/'.$heading->fields['picto']);
					}

					$logo_upload = $_FILES['photo'];

					//on recupere l'extension du fichier
					$ext = explode('.', $logo_upload['name']);
					$ext = strtolower($ext[count($ext)-1]);
					dims_makedir(realpath('.').'/data/headings');

					$path = realpath('.').'/data/headings/heading_'.$heading->fields['id']."_".$time.".".$ext;

					// on va reziser l'image
					$pathtemp=$logo_upload['tmp_name'];
					if (move_uploaded_file($pathtemp,$path)) {
						chmod($path, 0777);
					}
					dims_resizeimage($path, 0, 0, 0,'',0,$path,150,150);

					$heading->fields['picto']='heading_'.$heading->fields['id']."_".$time.".".$ext;
				}
				$heading->save();
			}

			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$heading->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_PROPERTIES_ADD_ART:
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$article = new wce_article();
		if ($id != '' && $id > 0){
			$article->open($id,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			$article->setvalues($_POST,"wce_article_");
			$article->save();
		}else {
			// on initialise l'article
			$article->init_description();
			$article->setugm();
			$article->fields['author'] = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
			$article->setvalues($_POST,"wce_article_");
			$db = dims::getInstance()->db;
			$sel = "SELECT	MAX(position) as maxi
				FROM	".wce_article::TABLE_NAME."
				WHERE	id_heading = :id_heading
				AND		id_module = :id_module
				AND		id_lang = :id_lang";
			$res = $db->query($sel,array(':id_heading'=>array('value'=>$article->fields['id_heading'],'type'=>PDO::PARAM_INT),
										':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],'type'=>PDO::PARAM_INT)));
			if($db->numrows($res) > 0){
				$r = $db->fetchrow($res);
				$maxi = $r['maxi'];
			}else
				$maxi = 0;

			$article->fields['position'] = $maxi+1;
			$article->fields['visible']=0;
			$article->fields['id_lang'] = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];
			$article->save();
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		break;
	case module_wce::_PROPERTIES_CLONE_ART:
		$id_article = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_lang = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if($id_article != '' && $id_article > 0){
			$article = new wce_article();
			$article->open($id_article,$id_lang);
			$tmspt = dims_createtimestamp();
			foreach($article->getBlocks(true,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']) as $block){
				$Newblock = new wce_block();
				$Newblock->fields = $block->fields;
				$Newblock->setugm();
				$Newblock->fields['id_lang'] = $id_lang;
				$Newblock->fields['uptodate'] = 0;
				$Newblock->fields['timestp_modify'] = $tmspt;
				$Newblock->save();
			}
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']."&lang=$id_lang");
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_PROPERTIES_ADD_HEAD:
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			//$headingid=dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true);
			$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($headingid>0) {
				$heading = new wce_heading();

				$heading->open($headingid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);

				$heading_new = new wce_heading();
				$heading_new->init_description();
				$heading_new->fields['label'] = "Sous rubrique de '{$heading->fields['label']}'";
				$heading_new->fields['id_heading'] = $headingid;
				$heading_new->fields['parents'] = "{$heading->fields['parents']};{$headingid}";
				$heading_new->fields['depth'] = $heading->fields['depth']+1;

				$select = "	SELECT		MAX(position) as maxpos
							FROM		dims_mod_wce_heading
							WHERE		id_heading = :id_heading
							AND			id_lang = :id_lang
							AND 		type = 0";
				$res=$db->query($select,array(':id_heading'=>array('value'=>$headingid,'type'=>PDO::PARAM_INT),
												':id_lang'=>array('value'=>$heading->fields['id_lang'],'type'=>PDO::PARAM_INT)));
				if($db->numrows($res) > 0){
					$fields = $db->fetchrow($res);
					$maxpos = $fields['maxpos'];
				}else
					$maxpos = 0;
				$heading_new->fields['position'] = $maxpos+1;
				$heading_new->setugm();
				$sql = "SELECT	template
						FROM	dims_workspace_template
						WHERE	is_default = 1
						AND		id_workspace = :id_workspace";
				$res = $db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
				if($r = $db->fetchrow($res))
					$heading_new->fields['template'] = $r['template'];
				$heaging_new->fields['id_lang'] = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];

				$heading_new->save();
				$headingid = $heading_new->fields['id'];


				dims_create_user_action_log(_WCE_ACTION_CATEGORY_EDIT,$_DIMS['CSTE']['_MODIFY'],-1,-1, $heading_new->fields['id'],_WCE_OBJECT_HEADING);
			}
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$heading_new->fields['id']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_PROPERTIES_ADD_ROOT:
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$heading_new = new wce_heading();
			$heading_new->init_description();

			$select = "	SELECT		MAX(position) as maxpos
						FROM		dims_mod_wce_heading
						WHERE		id_heading = 0
						AND			id_module = :id_module
						AND 		type = 0
						GROUP BY	id";
			$res=$db->query($select,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
			$fields = $db->fetchrow($res);
			$maxpos = $fields['maxpos'];
			if (!is_numeric($maxpos)) $maxpos = 0;
			$heading_new->fields['position'] = $maxpos+1;

			$heading_new->fields['label'] = "Racine {$heading_new->fields['position']}";
			$heading_new->fields['id_heading'] = 0;
			$heading_new->fields['parents'] = 0;
			$heading_new->fields['depth'] = 1;
			$heading_new->setugm();
			$sql = "SELECT	template
					FROM	dims_workspace_template
					WHERE	is_default = 1
					AND		id_workspace = :id_workspace";
			$res = $db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
			if($r = $db->fetchrow($res))
				$heading_new->fields['template'] = $r['template'];
			$heaging_new->fields['id_lang'] = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];

			$headingid = $heading_new->save();

			dims_create_user_action_log(_WCE_ACTION_CATEGORY_EDIT,$_DIMS['CSTE']['_MODIFY'],-1,-1, $heading_new->fields['id'],_WCE_OBJECT_HEADING);
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$heading_new->fields['id_heading']);
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_PROPERTIES_DEL_ROOT:
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($headingid > 0 && $headingid != '') {
				$heading = new wce_heading();
				$heading->open($headingid);
				$heading->delete();
			}
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_PROPERTIES_DEL_ART:
		$heading = "";
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($articleid > 0 && $articleid != '') {
				$site = new wce_site(dims::getInstance()->db,$_SESSION['dims']['moduleid']);
				$id_lang = $site->getDefaultLanguage();
				$article = new wce_article();
				$article->open($articleid,$id_lang);
				$heading = "&headingid=".$article->fields['id_heading'];
				$head = new wce_heading();
				$head->open($article->fields['id_heading']);
				$article->delete();
				$head->updateArticlePosition();
			}
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF.$heading);
		break;
}
?>
