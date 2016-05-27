<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/familles_helpers.php";

$view = view::getInstance();
$view->setLayout('layouts/famille_layout.tpl.php');

// infos contexuelles
$view->assign('a', $a);
cata_famille::all();

switch ($a) {
	default:
	case 'index':
		$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
		if($sa == '') $sa = "articles";
		$view->assign('sa', $sa);

		#Gestion des filtres
		$init_filter        = dims_load_securvalue('filter_init',   dims_const::_DIMS_NUM_INPUT, true,true, true);

		if(isset($init_filter) && $init_filter){
			unset($_SESSION['cata']['familles']['index']);
		}
		$default_search = dims_constant::getVal('_SEARCH_FAMILY');
		$cur_keywords       = &get_sessparam($_SESSION['cata']['familles']['index']['keywords'], '');
		$keywords           = dims_load_securvalue('keywords',      dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_keywords, '', true);
		if( ! empty($keywords) && $keywords == $default_search) $keywords = '';
		$view->assign('path_lateral_search',get_path('familles','index'));
		$view->assign('default_search',$default_search);
		$view->assign('keywords',$keywords);

		if(empty($keywords)){
			$elem = cata_famille::contructFinder();
			if(!isset($_SESSION['dims']['cata']['display_finder'])) $_SESSION['dims']['cata']['display_finder'] = true;
			$view->assign('url_finder',dims::getInstance()->getScriptEnv()."?c=familles&a=show".(($sa != "")?"&sa=$sa":""));
			$view->assign('url_switch_finder',dims::getInstance()->getScriptEnv()."?c=familles&a=switch_finder_fam");
			$view->assign('display_finder',$_SESSION['dims']['cata']['display_finder']);
			$view->assign('finder_familles', $elem);
			$view->assign('sel_elem', $elem);
			$view->assign('lst_articles', array());

			$actions = array();
			if($elem->fields['id_parent'] > 0){
				$actions[0]['picto'] = 'gfx/edit20.png';
				$actions[0]['text'] = dims_constant::getVal('_EDIT_THIS_FAMILY');
				$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=show&sa=properties&id='.$elem->get('id');
			}
			$actions[1]['picto'] = 'gfx/ajouter20.png';
			$actions[1]['text'] = dims_constant::getVal('_ADD_SUBFAMILY');
			$actions[1]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=new&id='.$elem->get('id');
			$actions[2]['picto'] = 'gfx/dupliquer20.png';
			$actions[2]['text'] = dims_constant::getVal('_DUPLICATE_THIS_FAMILY');
			$actions[2]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=duplique&id='.$elem->get('id');
			if($elem->fields['id_parent'] > 0){
				$actions[3]['picto'] = 'gfx/poubelle20.png';
				$actions[3]['text'] = dims_constant::getVal('_DELETE_THIS_FAMILY');
				$actions[3]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=delete&id='.$elem->get('id');
				$actions[3]['confirm'] = true;
				$actions[3]['txt_confirm'] = dims_constant::getVal('_CONFIRM_DELETE_FAMILY');
			}

			$view->assign('actions', $actions);

		}else{
			include_once DIMS_APP_PATH."modules/system/class_search.php";
			$dimsearch = new search(dims::getInstance());
			$dimsearch->addSearchObject($_SESSION['dims']['moduleid'],cata_famille::MY_GLOBALOBJECT_CODE,dims_constant::getVal('FAMILIES'));
			$dimsearch->executeSearch2($keywords, "",$_SESSION['dims']['moduleid'], "", "", "");
			$result = array();
			if(isset($dimsearch->tabresultat[$_SESSION['dims']['moduleid']][cata_famille::MY_GLOBALOBJECT_CODE]) && count($dimsearch->tabresultat[$_SESSION['dims']['moduleid']][cata_famille::MY_GLOBALOBJECT_CODE])){
				$noAccentExp = explode(' ',dims_convertaccents($keywords));
				$sel = "SELECT  *
						FROM    ".cata_famille::TABLE_NAME."
						WHERE   id_globalobject IN (".implode(',',array_keys($dimsearch->tabresultat[$_SESSION['dims']['moduleid']][cata_famille::MY_GLOBALOBJECT_CODE])).")";
				$db = dims::getInstance()->getDb();
				$res = $db->query($sel);
				while($r = $db->fetchrow($res)){
					$fam = new cata_famille();
					$fam->openFromResultSet($r);

					$sel2 = "SELECT content
							FROM    dims_keywords_sentence
							WHERE   id = ".$dimsearch->tabresultat[$_SESSION['dims']['moduleid']][cata_famille::MY_GLOBALOBJECT_CODE][$r['id_globalobject']]['sentence'];
					$res2=$db->query($sel2);
					if($r2 = $db->fetchrow($res2)){
					   $fam->setLightAttribute('sentence',dims_getManifiedWords($r2['content'],$noAccentExp,'<b>','</b>'));
					}

					$result[$r['id_globalobject']] = $fam;
				}

				/*foreach($dimsearch->tabresultat[$_SESSION['dims']['moduleid']][cata_famille::MY_GLOBALOBJECT_CODE] as $res){
					sentence
				}*/
			}
			$view->assign('result',$result);
			$view->render('familles/result_search.tpl.php','sub_menu');
		}
		break;

	case 'show':
		$view->assign('path_lateral_search',get_path('familles','index'));
		$view->assign('default_search',dims_constant::getVal('_SEARCH_FAMILY'));

		$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
		if($sa == '') $sa = "articles";
		$view->assign('sa', $sa);

		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$lstFinder = cata_famille::contructFinder($id);

		$lstArticles = array();
		if($id != '' && $id > 0){
			$elem = new cata_famille();
			$elem->open($id);
			$lstArticles = $elem->getArticles();

			$actions = array();
			if($elem->fields['id_parent'] > 0){
				$actions[0]['picto'] = 'gfx/edit20.png';
				$actions[0]['text'] = dims_constant::getVal('_EDIT_THIS_FAMILY');
				$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=show&sa=properties&id='.$elem->get('id');
			}
			$actions[1]['picto'] = 'gfx/ajouter20.png';
			$actions[1]['text'] = dims_constant::getVal('_ADD_SUBFAMILY');
			$actions[1]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=new&id='.$elem->get('id');
			$actions[2]['picto'] = 'gfx/dupliquer20.png';
			$actions[2]['text'] = dims_constant::getVal('_DUPLICATE_THIS_FAMILY');
			$actions[2]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=duplique&id='.$elem->get('id');
			if($elem->fields['id_parent'] > 0){
				$actions[3]['picto'] = 'gfx/poubelle20.png';
				$actions[3]['text'] = dims_constant::getVal('_DELETE_THIS_FAMILY');
				$actions[3]['link'] = dims::getInstance()->getScriptEnv().'?c=familles&a=delete&id='.$elem->get('id');
				$actions[3]['confirm'] = true;
				$actions[3]['txt_confirm'] = dims_constant::getVal('_CONFIRM_DELETE_FAMILY');
			}

			$view->assign('actions', $actions);

		}else
			$elem = $lstFinder;

		if(!isset($_SESSION['dims']['cata']['display_finder'])) $_SESSION['dims']['cata']['display_finder'] = true;
		$view->assign('url_finder',dims::getInstance()->getScriptEnv()."?c=familles&a=show".(($sa != "")?"&sa=$sa":""));
		$view->assign('url_switch_finder',dims::getInstance()->getScriptEnv()."?c=familles&a=switch_finder_fam");
		$view->assign('display_finder',$_SESSION['dims']['cata']['display_finder']);
		$view->assign('finder_familles', $lstFinder);
		$view->assign('sel_elem', $elem);
		$view->assign('lst_articles', $lstArticles);
		if($elem->fields['id_parent'] != '' && $elem->fields['id_parent'] > 0){
			store_lastfamille($elem->fields['id'],3);
			switch ($sa) {
				default:
				case 'articles':
					$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=actionsArt&id=".$elem->fields['id']);
					$view->render('familles/lst_articles.tpl.php','sub_menu');
					break;
				case 'attachart':
					$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=saveAttachArt&id=".$elem->fields['id']);
					$view->assign('back_path',dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$elem->fields['id']);
					$view->render('familles/famille_search_articles.tpl.php','sub_menu');
					break;
				case 'properties':
					$view->assign('languages',cata_param::getActiveLang());
					$view->assign('param_open_arbo',"c=familles&a=choose_famille&id=".$elem->fields['id']);
					$view->assign('select_sub_fam',dims::getInstance()->getScriptEnv()."?c=familles&a=select_sub_fam&id=".$elem->fields['id']);
					$view->assign('nb_brothers',count($elem->getbrothers()));

					$champs_libres = cata_champ::sortByCategories(cata_champ::completeListOfValuesFor($elem->getChampsLibre()));
					$view->assign('sel_champs', $champs_libres);

					$view->assign('translations', $elem->getTranslations());
					$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=save&id=".$elem->fields['id']);
					$view->assign('back_path',dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=properties&id=".$elem->fields['id']);
					$view->render('familles/properties.tpl.php','sub_menu');
					break;
				case 'vignettes':
					$view->assign('thumbnails',$elem->getThumbnails());
					$view->render('familles/vignettes.tpl.php','sub_menu');
					break;
				case 'newVign':
					$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=addVign&id=".$elem->fields['id']);
					$view->assign('back_path',dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=vignettes&id=".$elem->fields['id']);
					$view->assign('nb_thumbnails',$elem->getNbThumbnails());
					$view->render('familles/new_vignette.tpl.php','sub_menu');
					break;
				case 'chplibres':
					// Recherche
					$view->assign('defaultFam_search',addslashes(dims_constant::getVal('_SEARCH')));
					$fam_keywords = dims_load_securvalue('fam_keywords',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					if($fam_keywords == dims_constant::getVal('_SEARCH') || dims_load_securvalue('filter_init',dims_const::_DIMS_NUM_INPUT,true,true)){
						$fam_keywords = "";
					}
					$view->assign('fam_keywords',$fam_keywords);
					$view->assign('action_path2',dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=chplibres&id=".$elem->fields['id']);

					include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
					$my_champ = dims_load_securvalue('my_champ',dims_const::_DIMS_NUM_INPUT,true,true);
					if($my_champ || (isset($_SESSION['dims']['cata']['chpLibres']) && $_SESSION['dims']['cata']['chpLibres'])){ // on affiche uniquement ceux présents pour cette famille
						unset($_SESSION['dims']['cata']['chpLibres']);
						$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=saveChampsPos&id=".$elem->fields['id']);
						$view->assign('sel_champs',$elem->getChampsLibre($fam_keywords));
						$view->render('familles/famille_lst_champs_pos.tpl.php','sub_menu');
					}else{
						$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=saveChamps&id=".$elem->fields['id']);
						$view->assign('back_path',dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=chplibres&id=".$elem->fields['id']);

						$rootCat = cata_champ::getRootCategory();
						$lstCateg = $rootCat->getDirectChilds(); // on prend uniquement le 1er niveau
						$view->assign('lst_categ',$lstCateg);
						$view->assign('sel_champs',$elem->getChampsLibre());
						$lstChamps = array();
						if(!empty($fam_keywords))
							$lstChamps = cata_champ::getAllGo($fam_keywords);
						$view->assign('lst_champs',$lstChamps);
						$view->render('familles/famille_lst_champs.tpl.php','sub_menu');
					}
					break;
				case 'editchamp':
					include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
					include_once DIMS_APP_PATH."modules/system/class_category.php";
					$champ = new cata_champ();
					$champ->init_description();
					$view->assign('languages',cata_param::getActiveLang());
					$rootCat = cata_champ::getRootCategory();
					$lstCateg = $rootCat->getDirectChilds(); // on prend uniquement le 1er niveau
					$view->assign('lst_cat',$lstCateg);
					$view->assign('my_categ',0);
					$view->assign('add_categ',$dims->getScriptEnv().'?c=params&a=addCategChp');

					$view->assign('action_path',dims::getInstance()->getScriptEnv()."?c=familles&a=savechp&id=".$elem->fields['id']);
					$view->assign('back_path',dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=chplibres&id=".$elem->fields['id']);

					$view->assign('champ',$champ);
					$view->render('params/edit_champs.tpl.php','sub_menu');
					break;

				// Sélections
				case 'selections':
					// Mode liste > OK
					$view->assign('family', $elem);
					$view->assign('a_selections', $elem->getSelections());
					$view->render('familles/selections_index.tpl.php', 'sub_menu');
					break;
				case 'selection_edit':
					require_once DIMS_APP_PATH.'modules/catalogue/include/class_selection.php';
					$view->assign('selection_id', dims_load_securvalue('selection_id', dims_const::_DIMS_NUM_INPUT, true, false));
					$view->assign('family', $elem);
					$view->assign('selections', cata_selection::getAll($_SESSION['dims']['moduleid']));
					$view->render('familles/selections_edit.tpl.php', 'sub_menu');
					break;
				case 'selection_save':
					dims_print_r($_POST);
					die();
					break;
				case 'selection_delete':
					break;
			}
		}
		break;
	case 'save':
		#Traitement des champs libres
		$champs_libres = dims_load_securvalue('champs_libres', dims_const::_DIMS_CHAR_INPUT, true,true, true);

		if( empty($scope)) $scope = 'family';
		$go_lang = array();
		#Pour pour connaître les langues pour lesquelles on a de l'info, sinon on risque de créer des articles pour rien
		if (isset($champs_libres[$scope])) {
			foreach( $champs_libres[$scope] as $id_lang => $tab){
				foreach($tab as $field => $value){
					if( ! empty($value) && $value != 'dims_nan'){
						$go_lang[$id_lang][$field] = $value;
					}
				}
			}
		}

		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$elem = new cata_famille();
		$tmpstp = dims_createtimestamp();
		if($id != '' && $id > 0)
			$elem->open($id);
		else{
			$elem->init_description();
			$elem->setugm();
			$elem->fields['date_create'] = $tmpstp;
			$elem->fields['user_create'] = $_SESSION['dims']['userid'];
		}
		$elem->fields['date_modify'] = $tmpstp;
		$elem->fields['user_modify'] = $_SESSION['dims']['userid'];
		$elem->setvalues($_POST,'fam_');
		$elem->fields['label'] = dims_load_securvalue('label_'.$elem->fields['id_lang'],dims_const::_DIMS_CHAR_INPUT,false,true,true);
		$elem->fields['description'] = dims_load_securvalue('description_'.$elem->fields['id_lang'],dims_const::_DIMS_CHAR_INPUT,false,true,true);
		$elem->fields['display_mode'] = dims_load_securvalue('display_mode',dims_const::_DIMS_NUM_INPUT,false,true,true);

		#traitement des champs libres
		if( isset($go_lang[$elem->fields['id_lang']])){
			foreach($go_lang[$elem->fields['id_lang']] as $chp => $value){
				$elem->fields[$chp] = $value;
			}
		}
		$elem->save();

		$lstActiveLang = cata_param::getActiveLang();
		foreach($_POST as $key => $val){
			if(substr($key, 0, 6) == 'label_'){
				$lg = intval(substr($key,6));
				if(isset($_POST['description_'.$lg]) && isset($lstActiveLang[$lg])){
					if($lg != $elem->fields['id_lang']){
						$famlg = new cata_famille();
						$famlg->open($elem->fields['id'],$lg);

						if($famlg->isNew()){
							$famlg = new cata_famille();
							$famlg->fields = $elem->fields;
							$famlg->fields['id_lang'] = $lg;
							$famlg->setNew(true);
						}
						$famlg->fields['label'] = dims_load_securvalue('label_'.$famlg->fields['id_lang'],dims_const::_DIMS_CHAR_INPUT,false,true,false);
						$famlg->fields['description'] = dims_load_securvalue('description_'.$famlg->fields['id_lang'],dims_const::_DIMS_CHAR_INPUT,false,true,false);
						$famlg->setvalues($_POST,'fam_');

						#traitement des champs libres
						if( isset($go_lang[$lg])){
							foreach($go_lang[$lg] as $chp => $value){
								$famlg->fields[$chp] = $value;
							}
						}

						$famlg->save(false);
					}
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=properties&id=".$elem->fields['id']);
		break;
	case 'delete':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
		   $elem = new cata_famille();
		   $elem->open($id);
		   $ret = $elem->fields['id_parent'];
		   unstore_famille($id);
		   $elem->delete();
		   dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$ret);
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=index");
		break;
	case 'duplique':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id > 0){
		   $elem = new cata_famille();
		   $elem->open($id);
		   $clone = $elem->createclone();
		   dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$clone->fields['id']);
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=index");
		break;
	case 'new':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id > 0){
		   $elem = new cata_famille();
		   $elem->open($id);
		   $child = $elem->createchild();
		   dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$child->fields['id']);
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=index");
		break;
	case 'deleteart':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$idArt = dims_load_securvalue('idArt',dims_const::_DIMS_NUM_INPUT,true,true);
		if($idArt > 0){
			article::delete_all_lng($idArt);
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$id);
		break;
	case 'unlinkart':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$idArt = dims_load_securvalue('idArt',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id > 0 && $idArt > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
			$art = new cata_article_famille();
			$art->open($idArt,$id);
			$art->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$id);
		break;
	case 'switch_finder_fam':
		ob_clean();
		if(!isset($_SESSION['dims']['cata']['display_finder'])) $_SESSION['dims']['cata']['display_finder'] = true;
		$_SESSION['dims']['cata']['display_finder'] = !$_SESSION['dims']['cata']['display_finder'];
		die();
		break;
	case 'choose_famille':
		ob_clean();
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$input = dims_load_securvalue('input',dims_const::_DIMS_CHAR_INPUT,true,true);
		include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/familles_helpers.php";
		include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/popup_helpers.php";
		ob_start();
		_constructFamilleArborescence($id,$input);
		$content = ob_get_contents();
		ob_end_clean();
		$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
		_constructStandardPopup($id_popup,dims_constant::getVal('_LABEL_TREE')." : ".dims_constant::getVal('FAMILIES'),$content);
		die();
		break;
	case 'select_sub_fam';
		ob_clean();
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$fam = dims_load_securvalue('fam',dims_const::_DIMS_NUM_INPUT,true,true);
		$return = array(0,0);
		if($id != '' && $id > 0 && $fam != '' && $fam > 0){
			$elem = new cata_famille();
			$elem->open($id);
			$par = new cata_famille();
			$par->open($fam);
			$nbElem = $par->getNbChildren();
			if($elem->fields['id_parent'] == $par->fields['id'])
				$return = array($nbElem,$elem->fields['position']);
			else{
				$nbElem++;
				$return = array($nbElem,$nbElem);
			}

		}
		echo json_encode($return);
		die();
		break;
	case 'saveChamps':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_famille.php";
			$elem = new cata_famille();
			$elem->open($id);
			$lstAlready = $elem->getChampsLibre();
			$selection = dims_load_securvalue('selection',dims_const::_DIMS_NUM_INPUT,true,true);
			if(is_array($selection)){
				foreach($selection as $idChp){
					$lk = new cata_champ_famille();
					if(isset($lstAlready[$idChp]) && !$lstAlready[$idChp]->isNew()){
						$lk->open($elem->fields['id'],$idChp);
						unset($lstAlready[$idChp]);
					}else{
						// Réactivation du champ sur la famille
						if ($lk->open($elem->fields['id'],$idChp)) {
							$lk->reactivate();
						}
						// Nouveau champ sur la famille
						else {
							$lk->init_description();
							$lk->fields['id_champ'] = $idChp;
							$lk->fields['id_famille'] = $elem->fields['id'];
							$lk->fields['fiche'] = isset($_POST["fiche_$idChp"]);
							$lk->fields['filtre'] = isset($_POST["filtre_$idChp"]);
							$lk->fields['inherited'] = cata_champ_famille::_LOCAL;
							$lk->save();
						}
					}
				}
			}
			foreach($lstAlready as $chp){
				$lk = new cata_champ_famille();
				$lk->open($elem->fields['id'],$chp->fields['id']);
				$lk->delete();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=chplibres&id=".$id);
		break;
	case 'saveChampsPos':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			$_SESSION['dims']['cata']['chpLibres'] = true;
			include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_famille.php";
			$elem = new cata_famille();
			$elem->open($id);
			$lstAlready = $elem->getChampsLibre();
			foreach($lstAlready as $champ){
				if(!is_null($prop = $champ->getPropertiesFamille($elem->get('id'))) && isset($_POST['pos_'.$champ->get('id')])){
					$pos = dims_load_securvalue('pos_'.$champ->get('id'),dims_const::_DIMS_NUM_INPUT,true,true);
					if($pos != $prop->fields['position']){
						$prop->fields['position'] = $pos;
						$prop->save();
						break;
					}
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=chplibres&id=".$id);
		break;
	case 'savechp':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
			include_once DIMS_APP_PATH."modules/system/class_category.php";
			include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_famille.php";
			$champ = new cata_champ();
			$champ->init_description();
			$champ->setugm();
			$champ->fields['fiche'] = 0;
			$champ->fields['filtre'] = 0;
			$champ->fields['libelle'] = dims_load_securvalue("libelle_".$champ->getDefaultLang(),dims_const::_DIMS_CHAR_INPUT,true,true);
			$champ->setvalues($_POST, 'chp_');
			$champ->save();

			// Catégorie
			$goElem = $champ->getMyGlobalObject();
			$goElem->deleteLink($goElem->searchLink(category::MY_GLOBALOBJECT_CODE));
			$goCat = dims_load_securvalue('categorie',dims_const::_DIMS_NUM_INPUT,true,true);
			if($goCat != "" && $goCat > 0){
				$goElem->addLink($goCat);
			}

			// TODO : changer cela suivant l'activation ou non du multi-langue
			foreach(cata_param::getActiveLang() as $idlg => $lg){
				if(isset($_POST["libelle_$idlg"])){
					$champ->addLibelle($id,dims_load_securvalue("libelle_$idlg",dims_const::_DIMS_CHAR_INPUT,true,true));
				}

				if(isset($_POST["libelle_$id"]) && isset($_POST["values_$idlg"])){
					$values = dims_load_securvalue("values_$idlg",dims_const::_DIMS_CHAR_INPUT,true,true);
					$champ->addValues($id,explode('\r\n',$values));
				}
			}
			$lk = new cata_champ_famille();
			$lk->init_description();
			$lk->fields['id_champ'] = $champ->fields['id'];
			$lk->fields['id_famille'] = $id;
			$lk->fields['fiche'] = $champ->fields['fiche'];
			$lk->fields['filtre'] = $champ->fields['filtre'];
			$lk->fields['inherited'] = cata_champ_famille::_LOCAL;
			$lk->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=chplibres&id=".$id);
		break;
	case 'upVign':
		$id_doc = dims_load_securvalue('doc',dims_const::_DIMS_NUM_INPUT,true,true);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0 && $id_doc != '' && $id_doc > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_famille_thumb.php";
			$thumb = new cata_fam_thumb();
			$thumb->open($id,$id_doc);
			$thumb->fields['position'] ++;
			$thumb->save();

			// on met à jour le chemin de la vignette dans la famille
			// avec la nouvelle image en 1e position
			$fam = new cata_famille();
			$fam->open($id);

			$first = cata_fam_thumb::find_by(array('id_famille' => $id, 'position' => 1), null, 1);

			if (!is_null($first)) {
				$doc = new docfile();
				$doc->open($first->get('id_doc'));
				$fam->fields['image'] = substr($doc->getThumbnail(100), 1);
			}
			else {
				$fam->fields['image'] = '';
			}
			$fam->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=vignettes&id=".$id);
		break;
	case 'downVign':
		$id_doc = dims_load_securvalue('doc',dims_const::_DIMS_NUM_INPUT,true,true);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0 && $id_doc != '' && $id_doc > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_famille_thumb.php";
			$thumb = new cata_fam_thumb();
			$thumb->open($id,$id_doc);
			if($thumb->fields['position'] > 1)
				$thumb->fields['position'] --;
			$thumb->save();

			// on met à jour le chemin de la vignette dans la famille
			// avec la nouvelle image en 1e position
			$fam = new cata_famille();
			$fam->open($id);

			$first = cata_fam_thumb::find_by(array('id_famille' => $id, 'position' => 1), null, 1);

			if (!is_null($first)) {
				$doc = new docfile();
				$doc->open($first->get('id_doc'));
				$fam->fields['image'] = substr($doc->getThumbnail(100), 1);
			}
			else {
				$fam->fields['image'] = '';
			}
			$fam->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=vignettes&id=".$id);
		break;
	case 'delVign':
		$id_doc = dims_load_securvalue('doc',dims_const::_DIMS_NUM_INPUT,true,true);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0 && $id_doc != '' && $id_doc > 0){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_famille_thumb.php";
			$thumb = new cata_fam_thumb();
			$thumb->open($id,$id_doc);
			$thumb->delete();

			// Si on supprime la 1e vignette, on met à jour le chemin de la miniature dans la famille
			// avec la nouvelle image en 1e position
			$fam = new cata_famille();
			$fam->open($id);

			$first = cata_fam_thumb::find_by(array('id_famille' => $id, 'position' => 1), null, 1);

			if (!empty($first)) {
				$doc = new docfile();
				$doc->open($first->get('id_doc'));
				$fam->fields['image'] = substr($doc->getThumbnail(100), 1);
			}
			else {
				$fam->fields['image'] = '';
			}
			$fam->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=vignettes&id=".$id);
		break;
	case 'addVign':
		$type = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			switch ($type) {
				case 'import':
					if(isset($_FILES['file']['error']) && $_FILES['file']['error'] === 0){
						$doc = new docfile();
						$doc->init_description();
						$doc->setugm();
						$doc->fields['id_folder'] = -1;
						$doc->tmpuploadedfile = $_FILES['file']['tmp_name'];
						$doc->fields['name'] = $_FILES['file']['name'];
						$doc->fields['size'] = filesize($_FILES['file']['tmp_name']);
						$doc->save();

						$lk = new cata_fam_thumb();
						$lk->init_description();
						$lk->setugm();
						$lk->setvalues($_POST,'vign_');
						$lk->fields['id_famille'] = $id;
						$lk->setDocFile($doc);
						$lk->save();

						// si c'est la numéro 1, on enregistre le nom de la miniature (100x100) dans la famille
						if ($lk->fields['position'] == 1) {
							$fam = new cata_famille();
							$fam->open($id);
							$fam->fields['image'] = substr($doc->getThumbnail(100), 1);
							$fam->save();
						}
					}
					break;
				case 'art':
					$thumbs = dims_load_securvalue('thumbnails',dims_const::_DIMS_CHAR_INPUT,true,true);
					$vals = explode('-',$thumbs);
					if(count($vals) == 2){
						$lk = new cata_fam_thumb();
						$lk->init_description();
						$lk->setugm();
						$lk->setvalues($_POST,'vign_');
						$lk->fields['id_famille'] = $id;
						$lk->fields['id_article'] = $vals[0];
						$lk->fields['id_doc'] = $vals[1];
						$lk->save();
					}
					break;
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=vignettes&id=".$id);
		break;
	case 'searchArticle':
		ob_clean();
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($id != '' && $id > 0 && $val != ''){
			$elem = new cata_famille();
			$elem->open($id);
			$lstIds = $elem->getAllDescendance(false);
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
			$art = new article();
			$art->activePagination(false);
			$lstArticles = $art->build_index($elem->fields['id_lang'],article::STATUS_OK,'all','all',$lstIds,0,$val);
			$view2 = new view();
			$view2->set_tpl_webpath('modules/catalogue/admin/views/');
			$view2->setLayout('layouts/_empty_layout.tpl.php');
			$view2->assign('already_doc',$elem->getIdDoc());
			$view2->assign('lstArticles',$lstArticles);
			$view2->render('familles/new_vignette_art.tpl.php','default');
			$view2->compute();
		}
		die();
		break;
	case 'searchArticleAttach':
		ob_clean();
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($id != '' && $id > 0 && $val != '') {
			$elem = new cata_famille();
			$elem->open($id);
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
			$art = new article();
			$art->activePagination(false);
			$lstArticles = $art->build_index(1,article::STATUS_OK,'all','all','dims_nan',0,$val);
			$view2 = new view();
			$view2->set_tpl_webpath('modules/catalogue/admin/views/');
			$view2->setLayout('layouts/_empty_layout.tpl.php');
			$view2->assign('lstArticles',array_diff_key($lstArticles,$elem->getArticles(false)));
			$view2->render('familles/famille_result_articles.tpl.php','default');
			$view2->compute();
		}
		die();
		break;
	case 'saveAttachArt':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$selection = dims_load_securvalue('selection',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0 && count($selection)){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
			foreach($selection as $idArt){
				$lk = new cata_article_famille();
				$lk->init_description();
				$lk->fields['id_article'] = $idArt;
				$lk->fields['id_famille'] = $id;
				$lk->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$lk->save();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$id);
		break;
	case 'actionsArt':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$act = dims_load_securvalue('act',dims_const::_DIMS_CHAR_INPUT,true,true);
		$selection = dims_load_securvalue('selection',dims_const::_DIMS_NUM_INPUT,true,true,true);

		switch ($act) {
			case 'copy':
				if(count($selection)){
					copy_articles($selection);
					$view->flash(dims_constant::getVal('ARTICLES_COPIED_IN_CLIPBOARD'), 'success');
				}
				break;
			case 'cut':
				if(count($selection)){
					copy_articles($selection,$id);
					$view->flash(dims_constant::getVal('ARTICLES_COPIED_IN_CLIPBOARD'), 'success');
				}
				break;
			case 'invert':
				if(count($selection)){
					include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
					article::reverse_publication($selection);
					$view->flash(dims_constant::getVal('REVERSE_PUBLICATION_DONE'), 'success');
				}
				break;
			case 'paste':
				if(count($clipboard = get_clipboard())){
					include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";

					foreach($clipboard as $idArt){
						if(($orig = getCutClipboard($idArt)) !== false){ // Suppression de l'ancien dans le cas d'un cut
							$lk = new cata_article_famille();
							$lk->open($idArt,$orig);
							if(isset($lk->fields['id_module'])){
								$lk->delete();
							}
						}
					}
					delCutClipboard();

					$elem = new cata_famille();
					$elem->open($id);
					$elem->linkArticle($clipboard);// on colle

					$view->flash(dims_constant::getVal('_ARTICLES_WERE_PASTED'), 'success');
				}
				break;
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$id);
		break;
	case 'empty_clipboard':
		empty_clipboard();
		$view->flash(dims_constant::getVal('CLIPBOARD_EMPTY'), 'success');
		dims_redirect($_SERVER['HTTP_REFERER']);
		break;
	case 'shift_clipboard':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true,true, true);
		if(del_clipboard_article($id))
			$view->flash(dims_constant::getVal('ARTICLE_DELETED_FROM_CLIPBOARD'), 'success');
		else
			$view->flash(dims_constant::getVal('ARTICLE_NOT_IN_CLIPBOARD'), 'error');
		dims_redirect($_SERVER['HTTP_REFERER']);
		break;
}
//Dernières familles consultées
$view->assign('last_familles', get_lastfamilles());

$view->render('familles/lateral.tpl.php', 'lateral');
?>
