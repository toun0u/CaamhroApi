<?
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
	case module_wce::_LIST_ART_DEF:
		$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($headingid != '' && $headingid > 0){
			$heading = new wce_heading();
			$heading->open($headingid);
			$heading->display(module_wce::getTemplatePath("gestion_site/list_articles/list_articles.tpl.php"));
		}else
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_LIST."&action=".module_wce::_LIST_ART_DEF);
		break;
	case module_wce::_LIST_ART_MOVE:
		$headingid2 = dims_load_securvalue('headingid2',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($headingid2 != '' && $headingid2 > 0){
			$site = new wce_site(dims::getInstance()->db,$_SESSION['dims']['moduleid']);
			$id_lang = $site->getDefaultLanguage();

			$lstHead = array();
			$db = dims::getInstance()->db;
			if (!empty($_POST['head'])) {
				$lstHead = dims_load_securvalue('head', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$sel = "SELECT	COUNT(id) as max
					FROM	".wce_heading::TABLE_NAME."
					WHERE	id_heading = :idheading
					AND 	id_module = :idmodule";
				$res = $db->query($sel, array(
					':idheading' => array('type' => PDO::PARAM_INT, 'value' => $headingid2),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid'])
				));
				$Max = 1;
				if($r = $db->fetchrow($res))
					$Max = $r['max']+1;
				$heading = new wce_heading();
				$heading->open($headingid2);
				$idshead = dims_load_securvalue('head', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($idshead as $idHead) {
					$head = new wce_heading();
					$head->open($idHead);
					if($head->fields['id_heading'] != $heading->fields['id']) {
						$head->fields['id_heading'] = $heading->fields['id'];
						$head->fields['parents'] = $heading->fields['parents'].";".$heading->fields['id'];
						$head->fields['position'] = $Max;
						$head->save();
						$Max ++;
					}
				}
			}

			if (!empty($_POST['art'])){
				$sel = "SELECT	COUNT(id) as max
					FROM	".wce_article::TABLE_NAME."
					WHERE	id_heading = :idheading
					AND 	id_module = :idmodule";
				$res = $db->query($sel, array(
					':idheading' => array('type' => PDO::PARAM_INT, 'value' => $headingid2),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid'])
				));
				$Max = 1;
				if($r = $db->fetchrow($res)) {
					$Max = $r['max']+1;
				}
				$idsart = dims_load_securvalue('art', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($idsart as $idArt){
					$article = new wce_article();
					$article->open($idArt,$id_lang);
					if (!in_array($article->fields['id_heading'],$lstHead) && $article->fields['id_heading'] != $headingid2){ // pour éviter de déplacer les articles directement dans le heading de destination
						$article->fields['id_heading'] = $headingid2;
						$article->fields['position'] = $Max;
						$article->save();
						$Max++;
					}
				}
			}
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_LIST."&action=".module_wce::_LIST_ART_DEF."&headingid=$headingid");
		break;
	case module_wce::_LIST_ART_DEL:
		$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($headingid != '' && $headingid > 0){
			if (!empty($_POST['art'])){
				$site = new wce_site(dims::getInstance()->db,$_SESSION['dims']['moduleid']);
				$id_lang = $site->getDefaultLanguage();
				$lstHead = array();
				$artsid = dims_load_securvalue('art', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($artsid as $idArt){
					$article = new wce_article();
					$article->open($idArt,$id_lang);
					if (!isset($lstHead[$article->fields['id_heading']])){
						$lstHead[$article->fields['id_heading']] = new wce_heading();
						$lstHead[$article->fields['id_heading']]->open($article->fields['id_heading']);
					}
					$article->delete();
				}
				foreach($lstHead as $head){
					$head->updateArticlePosition();
				}
			}
			if (!empty($_POST['head'])){
				$lstHead2 = array();
				$idshead = dims_load_securvalue('head', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($idshead as $idHead){
					$head = new wce_heading();
					$head->open($idHead);
					if (!isset($lstHead2[$head->fields['id_heading']])){
						if (isset($lstHead[$head->fields['id_heading']])){
							$lstHead2[$head->fields['id_heading']] = $lstHead[$head->fields['id_heading']];
						}else{
							$lstHead2[$head->fields['id_heading']] = new wce_heading();
							$lstHead2[$head->fields['id_heading']]->open($head->fields['id_heading']);
						}
					}
					$head->delete();
				}
				foreach($lstHead as $head){
					$head->updatePosition();
				}
			}
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_LIST."&action=".module_wce::_LIST_ART_DEF."&headingid=$headingid");
		}
		dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_LIST."&action=".module_wce::_LIST_ART_DEF);
		break;
}
