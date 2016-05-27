<?php

require_once DIMS_APP_PATH.'/modules/wce/include/classes/class_dynobject.php';

/**
*	Here we are
*/

$view = View::getInstance();

if (isset($this->fields['url']) && trim($this->fields['url']) != '') dims_redirect(trim($this->fields['url'])); // redirection
$nbVersion = $this->getListVersion($this->fields['id_lang']);
if ($this->fields['id_article_link'] != '' && $this->fields['id_article_link'] > 0){
	$article2 = new wce_article();
	$article2->open($this->fields['id_article_link'],$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
	require_once DIMS_APP_PATH.'modules/wce/wiki/include/class_module_wiki.php';
	// duplicate content
	$url = "/index.php?articleid=".$this->fields['id'];
	if(trim($this->fields['urlrewrite']) != '')
		$url = "/".$this->fields['urlrewrite'].".html";
	$article2->setLightAttribute('canonical',$url);
	$head = module_wiki::getRootHeadingFront();
	if(!is_null($head)){
		if($article2->fields['id_heading'] == $head->fields['id']){
			global $article;
			$article = $article2;
			$headingid = $article2->fields['id_heading'];
			$articleid = $this->fields['id_article_link'];
			$user=new user();
			$user->open($article->fields['id_user']);
			$contactadd = new contact();
			$contactadd->init_description();
			$contactadd->open($user->fields['id_contact']);
			$dd = dims_timestamp2local($article->fields['timestp_modify']);
			$db = dims::getInstance()->db;
			include module_wiki::getTemplatePath('/article/display_wiki.php');
		}else{
			$article2->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
		}
	}else
		$article2->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
	// duplicate content
	$url = "/index.php?articleid=".$this->fields['id'];
	if(trim($this->fields['urlrewrite']) != '')
		$url = "/".$this->fields['urlrewrite'].".html";
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("head").append('<link rel="canonical" href="<? echo $url; ?>" />');
		});
	</script>
	<?php
}elseif(($this->fields['timestp_unpublished'] == 0 || $this->fields['timestp_unpublished'] >= date('Ymd000000')) &&
		($this->fields['timestp_published'] == 0 || $this->fields['timestp_published'] <= date('Ymd000000')) &&
		($this->fields['uptodate'] || (!empty($nbVersion) && max($nbVersion) > 0)) || dims::getInstance()->getScriptEnv() == 'admin.php'){
	if(!isset($_SESSION['dims']['enter_article']))
		$_SESSION['dims']['enter_article'] = $this->fields['id'];
	$db = dims::getInstance()->getDb();
	global $smarty;
	global $months;
	$smartypath = (isset($_SESSION['dims']['smarty_path']))?$_SESSION['dims']['smarty_path']:realpath('.')."/smarty";
	global $article;
	$article = $this;

	$articleid = $this->fields['id']; //$articleid = dims_load_securvalue("articleid",dims_const::_DIMS_NUM_INPUT,true,true,false);
	$adminedit=dims_load_securvalue("adminedit",dims_const::_DIMS_NUM_INPUT,true,true);
	$versionid=dims_load_securvalue("versionid",dims_const::_DIMS_NUM_INPUT,true,true);

	/*
	 *		Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
	 *
	 *		This program is free software; you can redistribute it and/or modify
	 *		it under the terms of the GNU General Public License as published by
	 *		the Free Software Foundation; either version 2 of the License, or
	 *		(at your option) any later version.
	 *
	 *		This program is distributed in the hope that it will be useful,
	 *		but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *		GNU General Public License for more details.
	 *
	 *		You should have received a copy of the GNU General Public License
	 *		along with this program; if not, write to the Free Software
	 *		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
	 */

	// mode : edit / render / display => mode edition / rendu backoffice / affichage frontoffice
	// readonly : true / false => article modifiable oui/non (charge fckeditor si false)
	// type : draft / online => type du document : brouillon / en ligne
	//
	// include template class

	require_once DIMS_APP_PATH.'include/class_template.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article_meter.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block_model.php';
	// mise en commentaire
	//require_once DIMS_APP_PATH.'modules/system/class_action.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_section_pagination.php';

	global $wce_mode;

	if(dims::getInstance()->getScriptEnv() != 'admin.php'){
		if (!isset($_SESSION['wce']['meter'][$articleid])) {
			$_SESSION['wce']['meter'][$articleid]=true;
			$this->updatecount();
		}

		// on publie la page, on peut compter l'affichage
		$art_meter=new article_meter();
		$day=date("Ymd")."000000";
		$email="";
		if (isset($_SESSION['webletter']['email'])) $email=$_SESSION['webletter']['email'];

		$res=$db->query("SELECT		*
						FROM		dims_mod_wce_article_meter
						WHERE		id_article=:id_article
						AND			timestp=:timestp
						AND			email=:email",
						array(':id_article'=>array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT),
								':timestp'=>array('value'=>$day,'type'=>PDO::PARAM_STR),
								':email'=>array('value'=>$email,'type'=>PDO::PARAM_STR)));
		if ($db->numrows($res)>0) $art_meter->open($this->fields['id'],$day,$email);
		else {
			$art_meter->fields['id_article']=$this->fields['id'];
			$art_meter->fields['timestp']=$day;
			$art_meter->fields['email']=$email;
			$art_meter->fields['meter']=0;
		}

		$art_meter->updatecount();
		$this->updateCountTags();
	}

	$today = dims_createtimestamp();

	// initialisation des sections utilises pour les objets dynamiques
	$article_sections=array();

	$wce_mode = (!empty($_GET['wce_mode'])) ? dims_load_securvalue('wce_mode', dims_const::_DIMS_CHAR_INPUT, true, true, true) : 'display';
	$readonly = (!empty($_GET['readonly']) && $_GET['readonly']==1) ? 1 : 0;

	$lastupdate['date']="";
	$lastupdate['time']="";

	if (!isset($_SESSION['dims']['currentheadingid'])) $_SESSION['dims']['currentheadingid']=0;
	if (!isset($_SESSION['dims']['currentarticleid'])) $_SESSION['dims']['currentarticleid']=0;

	// new update for managing redirect by domain to article
	// update Pat from 28/06/2010
	$id_workspace_domain=0;
	$id_domain=0;
	$select = " SELECT		DISTINCT wd.id_workspace,d.id
				FROM		dims_workspace_domain as wd
				INNER JOIN	dims_domain as d
				ON			d.id = wd.id_domain
				AND			d.domain = :domain
				AND			(wd.access=1 OR wd.access=2)";

	$res=$db->query($select,array(':domain'=>array('value'=>$_SERVER['HTTP_HOST'],'type'=>PDO::PARAM_STR)));

	if ($fields = $db->fetchrow($res)) {
		$id_workspace_domain=$fields['id_workspace'];
		$id_domain=$fields['id'];
	}

	if (!isset($_SESSION['dims']['currentdomain_id'])) {
		$_SESSION['dims']['currentdomain_id']='';
	}

	if ($wce_mode == 'render' || $wce_mode == 'display') {
		$type = '';
	}

	$wce_module_id=$_SESSION['dims']['moduleid'];
	$local_module_id=$wce_module_id;
	// verification du moduleid courant au regard de l'heading et/ou article passï¿œ
	if ($this->fields['id'] > 0) {
		$local_module_id=$this->fields['id_module'];
	}else
		dims_redirect(dims::getInstance()->getScriptEnv());

	// test
	if ($local_module_id!=$wce_module_id) {
		if (isset($lstwcemods[$local_module_id])) {
			$wce_module_id=$local_module_id;
		}
		else {
			// on check en avancï¿œe si on peut y accï¿œder
			$lstwcemods=dims::getInstance()->getWceModules(true);
			if (isset($lstwcemods[$local_module_id])) {
				$wce_module_id=$local_module_id;
			}
		}
	}

	if (!isset($_SESSION['dims']['wce_module_id']) || $_SESSION['dims']['wce_module_id']!=$wce_module_id) {
		$_SESSION['dims']['wce_module_id']=$wce_module_id;
		require_once DIMS_APP_PATH."modules/system/class_module.php";
		$mod = new module_wce();
		$mod->open($_SESSION['dims']['wce_module_id']);
		$_SESSION['dims']['webworkspaceid']=$mod->fields['id_workspace'];
	}

	$wce_site=wce_site::getInstance(dims::getInstance()->db);

	if (!isset($wce_site) || $wce_site==null) {
		$wce_site = new wce_site(dims::getInstance()->db,$_SESSION['dims']['wce_module_id']);
	}

	$wce_site->loadBlockModels();
	//dims_print_r($_SESSION);
	if (!isset($_SESSION['dims']['homepageurl']) || $_SESSION['dims']['homepageurl']=='')
		$_SESSION['dims']['homepageurl']=$wce_site->getHomePageUrl();

	if (!isset($_SESSION['dims']['wce']['homePageUrl']) || $_SESSION['dims']['wce']['homePageUrl']=='')
		$_SESSION['dims']['wce']['homePageUrl'] = $wce_site->getHomePageUrl();

	//}

	$headings = wce_getheadings($wce_module_id);

	// recuperation de la famille e partir de l'articleid
	//$this->open($articleid);
	$headingid = $this->fields['id_heading'];

	if ($headingid==0) {
		// on va recercher l'heading du CMS
		if (isset($headings['tree'][0][0])) $headingid=$headings['tree'][0][0];
	}

	// on test si on a un link
	if ($this->fields['id']>0) {
		$this->verifyLinkedContent();
		$_SESSION['dims']['currentarticletitle']=$this->fields['title'];
	}
	else {
		$_SESSION['dims']['currentarticletitle']="";
	}

	$_SESSION['dims']['currentarticleid']=$this->fields['id'];
	$_SESSION['dims']['currentheadingid']=$headingid;
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid']=$_SESSION['dims']['currentheadingid'];
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid']=$_SESSION['dims']['currentarticleid'];

	//on recupere la langue par defaut de l'article
	/*if (isset($this->fields['id_lang'])) {
		$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$this->fields['id_lang'];
	}
	else {
		$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$_SESSION['dims']['currentlang'];
	}*/

	if (isset($headings['list'][$headingid]) && isset($headings['list'][$headingid]['nav'])) {
		$nav = $headings['list'][$headingid]['nav'];
		$array_nav = explode('-',$nav);
	}
	else {
		$nav="";
		$array_nav=array();
	}

	// get template name
//dims_print_r($headings['list'][$headingid]);
	$template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';

	if (!file_exists(DIMS_APP_PATH."templates/frontoffice/$template_name")) $template_name = 'default';

	$template_path = DIMS_APP_PATH."templates/frontoffice/$template_name";
	$_SESSION['dims']['front_template_name']=$template_name;
	$_SESSION['dims']['front_template_path']=$template_path;
	//$template_body = new Template($template_path);
	$smarty->template_dir = $template_path;

	//Baptiste
	// Inclusion des styles
	$vue = View::getInstance();
	$manager = $vue->getStylesManager();
	$manager->loadRessource('frontoffice', $template_name);
	$styles = $manager->includeStyles();
	if (isset($adminedit) && $adminedit==1 && $wce_mode=='edit') {
		$styles .= '<link type="text/css" rel="stylesheet" href="/assets/stylesheets/common/jquery_ui/smoothness/jquery-ui.css" />';
	}
	$smarty->assign('styles', $styles);
	$view->assign('styles', $styles);

	// Inclusion des scripts du backoffice
	$manager = $vue->getScriptsManager();

	$manager->loadRessource('frontoffice', $template_name);
	$scripts = $manager->includeScripts();
	$smarty->assign('scripts',$scripts);
	$view->assign('scripts',$scripts);


	if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/", 0777, true);

	$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/";

	if (file_exists("{$template_path}/config.php")) require_once "{$template_path}/config.php";

	// chargement eventuel du modele
	$wce_site->loadSchema(realpath(DIMS_APP_PATH)."/templates/frontoffice/$template_name/",'index');

	// annulation de la construction des rubrique
	//wce_template_assign($headings, $array_nav, 0, '', 0);
	$smarty_heading=array();

	// construction de la racine du site
	$root_path=$this->getRootPath();

	smarty_template_assign($smarty,$smarty_heading,$headings, $array_nav, 0 , '', '','',$root_path,$adminedit);

	$smarty->assign('headings',$smarty_heading);
	$view->assign('headings',$smarty_heading);
	$additional_css="";

	$template_path_fck=$template_path;

	$articlecolor="";

	if ($headingid>0) {
		$heading = new wce_heading();
		$heading->setWceSite($wce_site);
		$heading->open($headingid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
		if(!$heading->isNew()){
			if(dims::getInstance()->getScriptEnv() != 'admin.php' && $heading->fields['private']){
				if (!(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
					unset($_SESSION['dims']['currentarticleid']);
					dims_redirect(dims::getInstance()->getScriptEnv());
				}
			}

			// recherche de couleur
			$lisht=explode(";",$heading->fields['parents']);
			$lisht[]=$headingid;
			foreach($lisht as $i=>$hid) {
				if (isset($headings['list'][$hid]['colour']) && $headings['list'][$hid]['colour']!='') {
					$articlecolor=$headings['list'][$hid]['colour'];
				}
			}

			if ($heading->fields['fckeditor']=='' && isset($headings['list'][$heading->fields['id']]['fckeditor']) && $headings['list'][$heading->fields['id']]['fckeditor']!='') {
				$heading->fields['fckeditor']=$headings['list'][$heading->fields['id']]['fckeditor'];
			}
			if ($heading->fields['fckeditor']!='') {
				$template_path_fck.='/fckstyles/'.$heading->fields['fckeditor'];
				$customfck='/fckstyles/'.$heading->fields['fckeditor'];
			}
		}
	}

	/* ******************************************************************/
	/* nouvel algo de traitement des rewrite dans le contenu des pages  */
	/* 30/03/2009                                                       */
	/* ******************************************************************/
	$wce_site->loadRewritingUrl();

	$smarty->assign('articlerewrite',$article->getRewriteLang());

	// get articles
	if (isset($wce_module_id)) { // && isset($headingid) && is_numeric($headingid)) {
		$select = "	SELECT		*
					FROM		dims_mod_wce_article
					WHERE		id_module = :id_module
					AND			(timestp_published <= :timestp_published OR timestp_published = 0)
					AND			(timestp_unpublished >= :timestp_unpublished OR timestp_unpublished = 0)
					ORDER BY	position";


		$res=$db->query($select,array(	':id_module'=>array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT),
										':timestp_published'=>array('value'=>$today,'type'=>PDO::PARAM_INT),
										':timestp_unpublished'=>array('value'=>$today,'type'=>PDO::PARAM_INT)));
		$arraypage=array();
		$arrayarticles=array();
		if ($db->numrows($res)>0) {
			while ($row = $db->fetchrow($res)) {
				if (empty($articleid)) $articleid = $row['id'];

				if ($row['visible']) {
					switch($wce_mode) {
						case 'edit':
							$script = "javascript:window.parent.document.location.href='".module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid={$headingid}&articleid={$row['id']}';";
							//$script = "javascript:window.parent.document.location.href='admin.php?op=article_modify&headingid={$headingid}&articleid={$row['id']}';";
						break;

						case 'render':
							$script = "index.php?wce_mode=render&moduleid={$wce_module_id}&headingid={$headingid}&articleid={$row['id']}";
							//$script = "$scriptenv?nav={$nav}&articleid={$row['id']}";
						break;

						default:
						case 'display':
							if ($row['urlrewrite']!="" && isset($headings['list'][$row['id_heading']])
								&& $headings['list'][$row['id_heading']]['headingrewrite']!="") {
								$script = $headings['list'][$row['id_heading']]['headingrewrite'];
								if ($adminedit!='') $script.="?adminedit=1";
							}
							else {
								$script = $root_path."/index.php?articleid={$row['id']}";
								if ($adminedit!='') $script.="&adminedit=1";
							}
						break;
					}

					if (!empty($row['url'])) {
						if (substr($row['url'],0,4)!='http') {
							$row['url']='http://'.$row['url'];
						}
						$script = $row['url'];
					}

					$sel = '';
					if ($articleid == $row['id']) $sel = 'selected';

					$ldate_pub = (isset($row['timestp_published']) && $row['timestp_published']!='') ? $row['timestp_published'] : '';
					$ldate_unpub = ($row['timestp_unpublished']!='') ? $row['timestp_unpublished'] : '';

					$elemart=array(
							'ID'			=> $row['id'],
							'REFERENCE'				=> $row['reference'],
							'LABEL'			=> $row['title'],
							'CONTENT'		=> $row['content1'],
							'AUTHOR'		=> $row['author'],
							'VERSION'		=> $row['version'],
							'TIMESTP_PUB'	=> $ldate_pub,
							'TIMESTP_UNPUB' => $ldate_unpub,
							'LINK'			=> $script,
							'LINK_TARGET'	=> ($row['url_window']) ? 'target="_blank"' : '',
							'LINK'			=> $script,
							'POSITION'		=> $row['position'],
							'LENGTH'		=>strlen($row['title']),
							'SEL'			=> $sel);

					// structure pages de la rubrique selectionnee
					if ($row['id_heading']==$headingid) {
							$arraypage[]=$elemart;
					}
					// ajour de l'article dans la structure principale articles
					$arrayarticles[$row['id_heading']][]=$elemart;
				}
			}

			// pages de la rubrique courante
			$smarty->assign('pages',$arraypage);
			$view->assign('pages',$arraypage);
			// tous les articles
			$smarty->assign('articles',$arrayarticles);
			$view->assign('articles',$arrayarticles);
		}
		// fichier template par defaut
		$template_file = 'index.tpl';
	}
	else {
		if (file_exists(DIMS_APP_PATH."templates/frontoffice/{$template_name}/erreur.tpl")) $template_file = 'erreur.tpl';
		else $template_file = 'index.tpl';
	}

	if (!file_exists(DIMS_APP_PATH."templates/frontoffice/{$template_name}/$template_file")) {
		echo "Fichier $template_file manquant";
		die();
	}

	$filArianne = array();

	$elem['id']		= 0;
	$elem['type']	= 2; //Heading
	$elem['label']	= 'Accueil';
	$elem['link']	= $root_path.'/index.php';

	$filArianne[] = $elem;

	if ((!empty($articleid) || !empty($headingid)) &&  $headings['tree'][0][0] != $headingid) {
		$parentList = '';
		if(empty($headingid) && !empty($articleid) && !is_null($article)) {
			$parentList = $headings['list'][$article->fields['id_heading']]['parents'];
		}
		elseif(!empty($headingid) && isset($headings['list'][$headingid]['parents'])) {
			$parentList = ($headings['list'][$headingid]['parents'] > 0)?$headings['list'][$headingid]['parents']:$headingid;
		}

		$parents = explode(';',$parentList);
		foreach($parents as $heading_parent) {
			$elem = array();
			$detail = isset($headings['list'][$heading_parent])?$headings['list'][$heading_parent]:array();

			if(isset($detail['depth']) && $detail['depth'] > 1) {
				$script=$root_path;

				if ($detail['headingrewrite']!='' || $detail['article_urlrewrite']!='') {
					if ($detail['headingrewrite']!='')
						$script.='/'.$detail['headingrewrite'];

					if ($detail['article_urlrewrite']!='')
						$script.='/'.$detail['article_urlrewrite'];

					$script .= '.html';
				}
				else {
					$script .= '/index.php?headingid='.$heading_parent;
				}

				$elem['id']		= $detail['id'];
				$elem['type']	= 2; //Heading
				$elem['label']	= $detail['label'];
				$elem['link']	= $script;

				$filArianne[] = $elem;
			}
		}
	}

	if (isset($_SESSION['dims']['catalogue_mode']) && $_SESSION['dims']['catalogue_mode']
		&& isset($_SESSION['dims']['tpl_page']['ARIANE']) && !empty($_SESSION['dims']['tpl_page']['ARIANE'])) {

		$smarty->assign("arianne", $_SESSION['dims']['tpl_page']['ARIANE']);
		$view->assign("arianne", $_SESSION['dims']['tpl_page']['ARIANE']);
	}
	else {
		$smarty->assign("arianne", $filArianne);
		$view->assign("arianne", $filArianne);
	}

	//cyril : permet d'indiquer Ã  smarty si l'utilisateur est sur la page d'accueil de son site
	if($wce_site->getHomePageArtId()==$this->fields['id']){
		$smarty->assign("is_homepage", 1);
		$view->assign("is_homepage", 1);
		if(is_null($this->getLightAttribute('canonical')))
			$this->setLightAttribute('canonical',"/index.php");
	}else{
		$smarty->assign("is_homepage", 0);
		$view->assign("is_homepage", 0);
	}

	// objects dynamiques
	$dynobj=array();
	if (!empty($articleid) && is_numeric($articleid)) {

		$content = '';
		$ishomepage = (
				!empty($headingid)
			&&	!empty($articleid)
			&&	(isset($this->fields['position']) &&
				($this->fields['position'] == 1 &&
					$headings['list'][$headingid]['depth'] == 1 &&
					$headings['list'][$headingid]['position'] == 1 &&
					empty($headings['list'][$headingid]['linkedpage'])) ||
				$headings['list'][$headings['tree'][0][0]]['linkedpage'] == $articleid)
		);

		$nbcontent=0;

		$article_timestp = (isset($this->fields['timestp']) && $this->fields['timestp']!='') ? dims_timestamp2local($this->fields['timestp']) : array('date' => '');
		if(isset($this->fields['timestp_published']) && $this->fields['timestp_published']!='') {
			$article_lastupdate = dims_timestamp2local($this->fields['timestp_published']);
			$lastupdate_detail_raw = dims_gettimestampdetail($this->fields['timestp_published']);
			$lastupdate_detail = array();
			if (isset($lastupdate_detail_raw[1]) && isset($lastupdate_detail_raw[2])) {
				$lastupdate_detail['year']		= $lastupdate_detail_raw[1];
				$lastupdate_detail['month']		= $lastupdate_detail_raw[2];
				$lastupdate_detail['month_long']= $months[$lastupdate_detail_raw[2]]['label'];
				$lastupdate_detail['month_small']= $months[$lastupdate_detail_raw[2]]['small'];
				$lastupdate_detail['day']		= $lastupdate_detail_raw[3];
			}
			else {
				$lastupdate_detail['year']		= "";
				$lastupdate_detail['month']		= "";
				$lastupdate_detail['month_long']= "";
				$lastupdate_detail['month_small']= "";
				$lastupdate_detail['day']		= "";
			}

		}
		else {
			$article_lastupdate = array('date' => '', 'time' => '');
			$lastupdate_detail['year']		= '';
			$lastupdate_detail['month']		= '';
			$lastupdate_detail['month_str'] = '';
			$lastupdate_detail['day']		= '';
		}

		$user_lastname = "";
		$user_firstname = "";
		$user_login = "";
		$user = new user();
		if (isset($this->fields['lastupdate_id_user']) && $this->fields['lastupdate_id_user']>0 && $user->open($this->fields['lastupdate_id_user'])) {
			if (isset($user->fields['lastname'])) {
				$user_lastname = $user->fields['lastname'];
				$user_firstname = $user->fields['firstname'];
				$user_login = $user->fields['login'];
			}
		}

		$lastupdate = ($lastupdate = wce_getlastupdate()) ? dims_timestamp2local($lastupdate) : array('date' => '', 'time' => '');

		if (isset($print)) $template_file = 'print.tpl';
		elseif ($ishomepage && file_exists(DIMS_APP_PATH . "templates/frontoffice/{$template_name}/home.tpl")) $template_file = 'home.tpl';

		if (($wce_mode == 'edit')) {

			if (!$readonly)	{

				ob_start();
							/*
							 <script type="text/javascript" src="./FCKeditor/fckeditor.js"></script>
				<script type="text/javascript">
					function wce_getcontent(indice) {
						if (document.getElementById('fck_wce_article_draftcontent'+indice)!=null) {
							var oEditor = FCKeditorAPI.GetInstance('fck_wce_article_draftcontent'+indice) ;
							return(oEditor.GetXHTML(true));
						}
						else return false;
					}
				</script>
							 */
							// construction de l'ensemble des article avec le lien
							$listjsonarticle=$wce_site->getJsonArticles();
				?>
				<cfoutput>
						<input type='hidden' id='pageListJSON' value="<? echo $listjsonarticle;?>">
				</cfoutput>
				<div id="dialog-confirm" title="Confirm ?">
				</div>
				<script type="text/javascript" src="/common/js/ckeditor/ckeditor.js"></script>
				<script type="text/javascript">
					var ajax_load = "<img src='/common/img/loading16.gif' alt='loading...' />";


					function wce_getcontent(indice) {
						if (document.getElementById('fck_wce_article_draftcontent'+indice)!=null) {
							var oEditor = FCKeditorAPI.GetInstance('fck_wce_article_draftcontent'+indice) ;
							return(oEditor.GetXHTML(true));
						}
						else return false;
					}

					function wceEditLittleBlock(blockid,lang,focuselement) {
						if (focuselement==null) focuselement='';
						if (lang==null) lang=1;
						dimsAjaxLoading("zoneedit_dims_block"+blockid,"/admin.php?dims_op=getAjaxEditInfoBlock&block_id="+blockid+"&lang="+lang,focuselement);
					}

					window['wceSaveLittleBlock'] = function wceSaveLittleBlock(blockid) {
						var form = $('#form_wce_block_edit_small'+blockid);
						$(form).submit();

						setTimeout(function() {window.parent.refreshWceIframe(); },100);
					}

					function dimsAjaxLoading(contentid,loadUrl,focuselement) {
						$("#"+contentid)
							.html(ajax_load)
							.load(loadUrl, {language: "php", version: 5}, function(responseText){
								if (focuselement!='') {
									$("#"+focuselement).focus();
								}
								$('.ajaxForm').submit(function (event) {
									event.preventDefault();
									$.ajax({
										type: $(this).attr('method'),
										url: $(this).attr('action'),
										data: $(this).serialize()
									});
								});
							});
					}

					function wceModifBlockContentCkeditor(blockid,langid,contentid) {
						loadUrl="/admin.php?dims_op=getAjaxEditContentBlock&block_id="+blockid+"&content_id="+contentid+"&lang="+langid;
						$('#block'+blockid+'_'+contentid).load(loadUrl, {language: "php", version: 5}, function(responseText){
							var blockeedit=$('#wikiedit'+blockid+'_'+contentid);
							blockeedit.css('display','none');
							blockeedit.css('visibility','hidden');
							var instance = CKEDITOR.instances['block'+blockid+'_'+contentid];
							if(instance){
								CKEDITOR.remove(instance); //if existed then remove it
								$('#block'+blockid+'_'+contentid).remove();

							}
							instance=CKEDITOR.replace( 'block'+blockid+'_'+contentid,
							{
								customConfig : '/common/modules/wce/ckeditor/ckeditor_config_fr<? echo (defined('_DISPLAY_WIKI') && _DISPLAY_WIKI)?"_wiki":""; ?>.js',
								stylesSet:'default:/common/templates/frontoffice/<? echo $template_name;?>/ckstyles.js',
								contentsCss:'/common/templates/frontoffice/<? echo $template_name;?>/ckeditorarea.css'
							});

							instance.on('instanceReady',function(){
								$(window,window.document.parent).trigger('resize');

								// Scroll iframe to edition block.
								offset = $('#cke_block'+blockid+'_'+contentid).offset();
								window.scrollTo(0, offset.top);
							});

							//instance.config.stylesSet = 'dims_styles:/templates/frontoffice/<? echo $template_name;?>/ckstyles.js';

							instance.on('beforeCommandExec', function (event) {

								var blockelem=$('#block'+blockid+'_'+contentid);
								var blockeedit=$('#wikiedit'+blockid+'_'+contentid);

								if (event.data.name === 'ajaxsave') {
									instance.updateElement();
									var form = $('#form_wce_block'+blockid+'_'+contentid);
									instance.updateElement();
									var blockelem=$('#block'+blockid+'_'+contentid);

									$('#fck_contentBlockReturn'+blockid+'_'+contentid).val(blockelem.html());
									$('#icon_valid_'+blockid).src="/common/modules/wce/wiki/gfx/puce_orange.png";
									event.cancel();
									wceSaveBlocAjax(form);
								}

								if (event.data.name === 'ajaxclose') {
									var oldcontent=$('#block'+blockid+'_'+contentid).html();

									if (oldcontent!=instance.getData()) {

										valleft=($(window).width())/2-100;
										valtop=$(parent.window).scrollTop()+$(window).scrollTop();

										//var d = $("#block'+blockid+'_'+contentid").offset({scroll:false}).top;

										//$("#dialog-confirm").dialog("option", "position", [position.left, position.top]);
										$( "#dialog-confirm" ).dialog({
											resizable: false,
											height:140,
											modal: true,
											position: [valleft,valtop+20],
											buttons: {
														"<? echo $_SESSION['cste']['_DIMS_SAVE_BEFORE']; ?>": function() {
																$( this ).dialog( "close" );
																instance.updateElement();
																var form = $('#form_wce_block'+blockid+'_'+contentid);
																instance.updateElement();
																var blockelem=$('#block'+blockid+'_'+contentid);

																$('#fck_contentBlockReturn'+blockid+'_'+contentid).val(blockelem.html());
																event.cancel();
																//form.submit();
																wceSaveBlocAjax(form);

																wceUpdateBlockContentAfterEdit(instance,blockid,langid,contentid,true);
														},
														"<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>": function(){
																$( this ).dialog( "close" );
																var blockelem=$('#block'+blockid+'_'+contentid);
																blockelem.html(oldcontent);
																$('#fck_contentBlockReturn'+blockid+'_'+contentid).val(oldcontent);
																//instance.updateElement();
																//alert(oldcontent);

																event.cancel();
																wceUpdateBlockContentAfterEdit(instance,blockid,langid,contentid,true);
														},
														"<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>": function() {
																$( this ).dialog( "close" );
														}
												}
										});

									}
									else {
										wceUpdateBlockContentAfterEdit(instance,blockid,langid,contentid,false);
									}
								}
							});
						});
					}

					function wceSaveBlocAjax(form) {
						$.ajax({
							type: form.attr('method'),
							url: form.attr('action'),
							async : false,
							data: form.serialize()
						});
					}

					function wceUpdateBlockContentAfterEdit(instance,blockid,langid,contentid,updated) {
						setTimeout( function() { instance.destroy() }, 0);
						var blockelem=$('#block'+blockid+'_'+contentid);
						var blockeedit=$('#wikiedit'+blockid+'_'+contentid);
						blockelem.css('display','block');
						blockelem.css('visibility','visible');

						// on affiche le bloc d'edit
						blockeedit.css('display','block');
						blockeedit.css('visibility','visible');

						// on remet le texte avec les liens remplaces
						// on rafraichit le contenu ss les liens transformes
						$('#block'+blockid+'_'+contentid).html(ajax_load);
						dimsAjaxLoading('block'+blockid+'_'+contentid,"/admin.php?dims_op=getAjaxEditContentBlock&block_id="+blockid+"&content_id="+contentid+"&linksmodify=1"+"&lang="+langid);

						if (updated) {
							$('#icon_valid_'+blockid).src="/common/modules/wce/wiki/gfx/puce_orange.png";
						}

					}

					$(document).ready(function () {

						/*$('.ajaxForm').submit(function (event) {
							event.preventDefault();
							console.log('version ajaxForm');
							$.ajax({
								type: $(this).attr('method'),
								url: $(this).attr('action'),
								data: $(this).serialize()
							});
							return false;
						});*/
					});
				</script>
				<?php
							/*

							  $('.ckeditoriz').each(function () {
											var id = $(this).attr('id'),
												form = this.form;
												alert(id);
											CKEDITOR.instances[id].on('beforeCommandExec', function (event) {
												if (event.data.name === 'ajaxsave') {
													event.cancel();
													alert($('#form_wce_'+id));
													$(form).submit();
												}
											});

										});

							 * instance.on('beforeCommandExec', function (event) {

											if (event.data.name === 'ajaxsave') {
												var id = $(instance).attr('id');
												var form = instance.form;
												alert(id);
												event.cancel();
												$(form).submit();
												alert('save');
											}
										});
							 */
				//require_once(DIMS_APP_PATH . '/FCKeditor/fckeditor.php') ;
				//require_once(DIMS_ROOT_PATH . 'www/common/js/ckeditor/ckeditor.php') ;
				require_once(DIMS_APP_PATH . "modules/system/class_user.php");
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$jour=date("j/m/Y");

				if($this->fields["model"]!="") {
					$model = array();
					$model['nb_elem'] = 0;
					$model['path'] = $this->fields["model"];
					$tab_editor = array();
					$page="";
					//ob_start();
					$page=$this->getModel();

					// definition du composant wcesite
					$this->setWceSite($wce_site);

					if ($this->isBlock()) {
						// detection de section ou de block
						if ($this->isSection()) {
							$blockcontent='';

							// recuperation des blocks
							$blocksSection=$this->getBlocksSection($article_sections,true,$versionid,$this->fields['id_lang']);
							$lstBlockSection = array();
							$lstBlockSection[1] = array();
							if(isset($article_sections[1]))
								foreach($article_sections[1] as $key => $block)
									if($block['DISPLAY_TITLE'])
										$lstBlockSection[1][$key] = $block;

							// affectation de la variable pour smarty
							$smarty->assign('article_sections', $lstBlockSection);
							$view->assign('article_sections', $lstBlockSection);

							// recuperation des modeles d'affichage
							$models=$this->getWceSite()->getBlockModels();

							// sections modeles
							$sections=$this->getSections();

							// chargement des objets
							$dynobjects=$this->getWceSite()->getDynamicObjects();

							foreach ($sections as $idsection => $section) {
								// on initialise
								$contentsection='';
								switch ($section['type']) {
									case 'object':
										// init du contenu d'objet
										$contentobject=$section['value'];
										include module_wce::getTemplatePath('/common/article/article_edit_contentBlockObject.tpl.php');
										break;

									case 'text':
										// init des blocks si non defini
										$blocks=array();

										if (isset($blocksSection[$idsection])) {
											$blocks=$blocksSection[$idsection];
										}

										include module_wce::getTemplatePath('common/article/article_edit_contentBlock.tpl.php');
										break;

								}

								// on va remplacer les differentes balises
								$page=str_replace($section['pattern'],$contentsection,$page);
							}
						}
						else {
							$blockcontent='';
							// check page content
							if ($page=='') $page=$this->getModel ();

							include module_wce::getTemplatePath('common/article/article_edit_blocmodel.tpl.php');
							//require_once(DIMS_APP_PATH . '/modules/wce/display_edit_blockmodel.php');
							$posstart=strpos($page,"<BLOCK>");
							$posend=strpos($page,"</BLOCK>");

							if (($posstart+strlen("<BLOCK>"))==$posend || $posend==0) {
								$page = str_replace("<BLOCK>", $blockcontent, $page);
							}
							else {
								// on  a qq chose
								$chparams=substr($page,$posstart+strlen("<BLOCK>"),$posend-($posstart+strlen("<BLOCK>")));
								// on  nettoie les params en plus + le tag de fin
								$page = str_replace("<BLOCK>$chparams</BLOCK>",$blockcontent , $page);
							}

							$page = str_replace("</BLOCK>", "", $page);
						}
					}
					else {
						require_once module_wce::getTemplatePath('/common/article/article_render_simplemodel.tpl.php');
					}
					//$page = preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$page);
					//$page = preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$page);
					echo $page;
				}

				$pathmodel=_WCE_MODELS_PATH."/pages_publiques/".$this->fields["model"];
				$webpathmodel=_WCE_WEB_MODELS_PATH."/pages_publiques/".$this->fields["model"];
				// test si il existe un fichier style.css

				if (file_exists($pathmodel."/style.css"))
					$additional_css="<link type=\"text/css\" rel=\"stylesheet\" href=\"".$webpathmodel."/style.css\" media=\"screen\" title=\"styles\" />";
				$editor = ob_get_contents();
				ob_end_clean();
			}
		}

		$smarty->assign('switch_content_page','');
		$view->assign('switch_content_page','');
		$article_timestp = (isset($this->fields['timestp']) && $this->fields['timestp']!='') ? dims_timestamp2local($this->fields['timestp']) : array('date' => '');
		if(isset($this->fields['timestp_published']) && $this->fields['timestp_published']!='') {
			$article_lastupdate = dims_timestamp2local($this->fields['timestp_published']);
		}
		else {
			$article_lastupdate = (isset($this->fields['lastupdate_timestp']) && $this->fields['lastupdate_timestp']!='') ? dims_timestamp2local($this->fields['lastupdate_timestp']) : array('date' => '', 'time' => '');
		}

		if (!empty($editor)) {
			$editor = str_replace("<ARTICLE_COLOR>", $articlecolor, $editor);
			$editor = str_replace("<PAGE_TITLE>", $this->fields['title'], $editor);
			$editor = str_replace("<PAGE_TITLE_FAVORITES>", addslashes($this->fields['title']), $editor);
			$editor = str_replace("<PAGE_AUTHOR>", $this->fields['author'], $editor);
			$editor = str_replace("<PAGE_VERSION>", $this->fields['version'], $editor);
			$editor = str_replace("<PAGE_DATE>", $article_timestp['date'], $editor);
			$editor = str_replace("<PAGE_LASTUPDATE_DATE>", $article_lastupdate['date'], $editor);
			$editor = str_replace("<PAGE_LASTUPDATE_TIME>", $article_lastupdate['time'], $editor);
			$editor = str_replace("<PAGE_LASTUPDATE_USER_LASTNAME>", $user_lastname, $editor);
			$editor = str_replace("<PAGE_LASTUPDATE_USER_FIRSTNAME>", $user_firstname, $editor);
			$editor = str_replace("<PAGE_LASTUPDATE_USER_LOGIN>", $user_login, $editor);
			$editor = str_replace("<LASTUPDATE_DATE>", $lastupdate['date'], $editor);
			$editor = str_replace("<MODEL_PATH>", $pathmodel, $editor);
			$content = $editor;
		}
		else if(isset($this->fields["model"]) && $this->fields["model"]) {
			//$pathmodel=substr($this->fields["model"],0,strlen($this->fields["model"])-9);
			$pathmodel=_WCE_MODELS_PATH."/pages_publiques/".$this->fields["model"];
			$webpathmodel=_WCE_WEB_MODELS_PATH."/pages_publiques/".$this->fields["model"];

			// test si il existe un fichier style.css

			if (file_exists($pathmodel."/style.css")) {
				$additional_css="<link type=\"text/css\" rel=\"stylesheet\" href=\"".$webpathmodel."/style.css\" media=\"screen\" title=\"styles\" />";
			}

			//if (!isset($this->fields['timestp_cache']) || !isset($this->fields['cache'])
			//		|| $this->fields['timestp_cache']<$this->fields['timestp_modify'] || $this->fields['cache']=='') {

				// on force le rechargement du modele permettant le remplacement des variables liées à $site
				$page=$this->getModel(true);

				$page = str_replace("<ARTICLE_COLOR>", $articlecolor, $page);
				$page = str_replace("<PAGE_TITLE>", $this->fields['title'], $page);
				$page = str_replace("<PAGE_TITLE_FAVORITES>", addslashes($this->fields['title']), $page);
				$page = str_replace("<PAGE_AUTHOR>", $this->fields['author'], $page);
				$page = str_replace("<PAGE_VERSION>", $this->fields['version'], $page);
				$page = str_replace("<PAGE_DATE>", $article_timestp['date'], $page);
				$page = str_replace("<PAGE_LASTUPDATE_DATE>", $article_lastupdate['date'], $page);
				$page = str_replace("<PAGE_LASTUPDATE_TIME>", $article_lastupdate['time'], $page);
				$page = str_replace("<PAGE_LASTUPDATE_USER_LASTNAME>", $user_lastname, $page);
				$page = str_replace("<PAGE_LASTUPDATE_USER_FIRSTNAME>", $user_firstname, $page);
				$page = str_replace("<PAGE_LASTUPDATE_USER_LOGIN>", $user_login, $page);
				$page = str_replace("<LASTUPDATE_DATE>", $lastupdate['date'], $page);
				$page = str_replace("<MODEL_PATH>", $pathmodel, $page);

				if ($this->isBlock()) {
					//$page=$this->getModel();

					// definition du composant wcesite
					$this->setWceSite($wce_site);

					// detection de section ou de block
					if ($this->isSection()) {
						$blockcontent='';

						// recuperation des blocks
						$blocksSection=$this->getBlocksSection($article_sections,false,$versionid,$this->fields['id_lang']);
						$lstBlockSection = array();
						$lstBlockSection[1] = array();
						if(isset($article_sections[1]))
							foreach($article_sections[1] as $key => $block)
								if($block['DISPLAY_TITLE'])
									$lstBlockSection[1][$key] = $block;

						// affectation de la variable pour smarty
						$smarty->assign('page',array('COLOR' => $this->fields['color']));
						$view->assign('page',array('COLOR' => $this->fields['color']));
						$smarty->assign('article_sections', $lstBlockSection);
						$view->assign('article_sections', $lstBlockSection);
						//$smarty->assign('headings_article',$this->getHeadingBlocksSection());

						// recuperation des modeles d'affichage
						$models=$this->getWceSite()->getBlockModels();

						// sections modeles
						$sections=$this->getSections();

						// chargement des objets
						$dynobjects=$this->getWceSite()->getDynamicObjects();
						foreach ($sections as $idsection => $section) {
							// on initialise
							$contentsection='';

							switch ($section['type']) {
								case 'menu_libre':
									if(!empty($section['properties']['id_object'])){
										$root = wce_heading::find_by(array('id' => $section['properties']['id_object']), null, 1);
										if(!empty($root)){
											$view = view::getInstance();
											$headings = $view->get('headings');
											if(!empty($headings)){
												$data = null;
												foreach($headings as $level => $h){
													if($level == 'root'.$root->get('position')){
														$data = $h;
														break;
													}
												}

												if( ! is_null($data) && !empty($root->fields['freetemplate'])){
													$path = DIMS_APP_PATH.'templates/frontoffice/'.$root->get('template').'/freeroots/'.$root->get('freetemplate').'.tpl';
													if(file_exists($path)){
														$smarty2 = new Smarty();
														if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
															$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

														$smartypath=$_SESSION['dims']['smarty_path'];
														$smarty2->cache_dir = $smartypath.'/cache';
														$smarty2->config_dir = $smartypath.'/configs';

														if (!file_exists($smartypath.'/templates_c/'.$root->get('template').'/'.$root->fields['freetemplate'])) {
															dims_makedir ($smartypath."/templates_c/".$root->get('template')."/".$root->fields['freetemplate'].'/', 0777, true);
														}
														$smarty2->compile_dir = $smartypath."/templates_c/".$root->get('template')."/".$root->fields['freetemplate'].'/';
														$smarty2->assign('freeroot', $data);
														$view->assign('freeroot', $data);
														ob_start();
														$smarty2->display('file:'.$path);
														$contentsection .= ob_get_contents();
														ob_end_clean();
													}
												}
											}
										}
									}
									break;
								case 'slideshow':
									if(!empty($section['properties']['id_object'])){
										require_once DIMS_APP_PATH.'modules/wce/include/classes/class_slideshow.php';
										require_once DIMS_APP_PATH.'modules/wce/include/classes/class_slideshow_element.php';
										require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
										$slideshow = new wce_slideshow();
										$slideshow->open($section['properties']['id_object']);

										$slide_tab['id']			= $slideshow->fields['id'];
										$slide_tab['nom']			= $slideshow->fields['nom'];
										$slide_tab['description']	= $slideshow->fields['description'];
										$slide_tab['template']		= $slideshow->fields['template'];
										$slide_tab['color']			= substr($slideshow->fields['color'],1);

										foreach($slideshow->getElements() as $slide) {

											if($slide->fields['image'] > 0) {
												$docfile = new docfile();
												$docfile->open($slide->fields['image']);
												$readyToDisplay = true;
											}

											if($readyToDisplay && ( !$slide->fields['connected_only'] || $_SESSION['dims']['connected'] ) ) {
												$slide_tab['slide'][$slide->fields['id']]['id']				= $slide->fields['id'];
												$slide_tab['slide'][$slide->fields['id']]['connected_only'] = $slide->fields['connected_only'];
												$slide_tab['slide'][$slide->fields['id']]['titre']			= $slide->fields['titre'];
												$slide_tab['slide'][$slide->fields['id']]['descr_courte']	= $slide->fields['descr_courte'];
												$slide_tab['slide'][$slide->fields['id']]['descr_longue']	= $slide->fields['descr_longue'];
												$slide_tab['slide'][$slide->fields['id']]['position']		= $slide->fields['position'];
												$slide_tab['slide'][$slide->fields['id']]['descr_position'] = $slide->fields['descr_position'];
												$slide_tab['slide'][$slide->fields['id']]['lien']			= "";
												$slide_tab['slide'][$slide->fields['id']]['color'] = $slide->fields['color'];

												if ($slide_tab['template'] == "smallCarousel") {
													$slide_tab['slide'][$slide->fields['id']]['descr_position'] = 'bottom';
												} else if ($slide_tab['template'] == "topCarousel") {
													$slide_tab['slide'][$slide->fields['id']]['descr_position'] = 'left';
												}

												// on ajoute le lien que si il est valide
												$url = parse_url($slide->fields['lien']);

												// si on a un lien
												if (!empty($url['host']) || !empty($url['path'])) {

													// si c'est un lien relatif, on rajoute le protocole et le host
													if ( !isset($url['scheme']) || ($url['scheme'] != 'http' && $url['scheme'] != 'https' && $url['scheme'] != 'mailto' && $url['scheme'] != 'news' && $url['scheme'] != 'file') ) {
														$lien = $dims->getProtocol().$_SERVER['HTTP_HOST'];
														if (isset($slide->fields['lien'][0]) && $slide->fields['lien'][0] != '/') $lien .= '/';
														$lien .= $slide->fields['lien'];
													}
													else {
														$lien = $slide->fields['lien'];
													}
													if (filter_var($lien, FILTER_VALIDATE_URL)) {
														$slide_tab['slide'][$slide->fields['id']]['lien'] = $lien;
													}
												}

												if($slide->fields['image'] > 0) {
													if (isset($docfile->fields['extension']) && in_array(strtolower($docfile->fields['extension']),array('mp4','ogv','webm'))) {
														$slide_tab['slide'][$slide->fields['id']]['isVideo'] = true;
														// Construction de l'url pour la iframe
														if ($slide_tab['template'] == "smallCarousel") {
															$width = 236;
															$height = 133;
														} else if ($slide_tab['template'] == "topCarousel") {
															$width = 485;
															$height = 273;
														}
														$work = new workspace();
														$work->open($_SESSION['dims']['workspaceid']);
														$domain = current($work->getFrontDomains());
														if ($domain['ssl'])
															$lk = "https://".$domain['domain'];
														else
															$lk = "http://".$domain['domain'];
														$url = $lk.'/'.dims::getInstance()->getScriptEnv().'?dims_op=video_room&id_video='.$docfile->fields['id'].'&width='.$width.'&height='.$height.'&tpl='.$slide_tab['template'].'&color='.$slide_tab['color'];
														$slide_tab['slide'][$slide->fields['id']]['iframe_url'] = $url;
													} else {
														$slide_tab['slide'][$slide->fields['id']]['isVideo'] = false;
														$slide_tab['slide'][$slide->fields['id']]['filePath'] = $docfile->getwebpath();
													}
												}

												if($slide->fields['miniature'] > 0) {
													$miniature = new docfile();
													$miniature->open($slide->fields['miniature']);

													$slide_tab['slide'][$slide->fields['id']]['miniature'] = $miniature->getwebpath();
												}
											}
										}

										$template_name = $slideshow->fields['template'];

										if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
											$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
										}
										$smartypath=$_SESSION['dims']['smarty_path'];

										$smartyobject = new Smarty();
										$smartyobject->cache_dir = $smartypath.'/cache';
										$smartyobject->config_dir = $smartypath.'/configs';
										$smartyobject->template_dir = DIMS_APP_PATH."templates/objects/slideshows/";

										$smartyobject->assign('slideshow',$slide_tab);

										if (file_exists($smartyobject->template_dir.'/'.$template_name.'.tpl')) {
											if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
											$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";
											ob_start();
											$smartyobject->display($template_name.'.tpl');
											$contentsection .= ob_get_contents();
											ob_end_clean();
										}
										else echo 'ERREUR : '.$template_name.'.tpl manquant !';
									}
									break;
								case 'dyn_object':
									$dyn = new DynObject($section['properties']['id_object'], $smarty, $section['properties']);
									$path = $dyn->buildIHM();
									if(!is_null($path)){
										ob_start();
										$smarty->display('file:'.$path);
										$contentsection .= ob_get_contents();
										ob_end_clean();
									}
									break;
								case 'object':
									// init du contenu d'objet
									$contentobject=$section['value'];
									if (isset($blocks[$idsection][1]['content1'])) $contentobject=$blocks[$idsection][1]['content1'];

									if (isset($section['properties']['path'])) {
										$pathmodelobject=_WCE_MODELS_PATH."/objects/".str_replace("..", "", $section['properties']['path']);
										if (file_exists($pathmodelobject)) {
											ob_start();
											$smarty->display('file:'.$pathmodelobject);
											$contentsection .= ob_get_contents();
											ob_end_clean();
										}
									}

									break;
								case 'dyn_planning':
									$dyn = new DynObject(null, $smarty, $section['properties'], $section['type']);
									$path = $dyn->buildIHM();
									if(!is_null($path)){
										ob_start();
										$smarty->display('file:'.$path);
										$contentsection .= ob_get_contents();
										ob_end_clean();
									}
									break;
								case 'text':
									// init des blocks si non defini
									$blocks=array();

									if (isset($blocksSection[$idsection])) {
										$blocks=$blocksSection[$idsection];
									}

									include module_wce::getTemplatePath('/common/article/article_render_contentBlock.tpl.php');
									break;
								case 'php':
									$path = $pathmodel.'/'.basename($section['path']);
									if (file_exists($path)) {
										ob_start();
										$this->display($path);
										$contentsection .= ob_get_contents();
										ob_end_clean();
									}
									break;

							}
							// on va remplacer les differentes balises
							$page=str_replace($section['pattern'],$contentsection,$page);
						}
					}
					else {

						$blockcontent='';
						require_once module_wce::getTemplatePath('/common/article/article_render_blockmodel.tpl.php');

						if (isset($subpages)){
							$smarty->assign('subpages', $subpages);
							$smarty->view('subpages', $subpages);
						}

						$posstart=strpos($page,"<BLOCK>");
						$posend=strpos($page,"</BLOCK>");

						if (($posstart+strlen("<BLOCK>"))==$posend || $posend==0) {
								$page = str_replace("<BLOCK>", $blockcontent, $page);
						}
						else {
							// on  a qq chose
							$chparams=substr($page,$posstart+strlen("<BLOCK>"),$posend-($posstart+strlen("<BLOCK>")));
							// on  nettoie les params en plus + le tag de fin
							$page = str_replace("<BLOCK>$chparams</BLOCK>",$blockcontent , $page);
						}
						$page = str_replace("</BLOCK>", "", $page);

					}
				}
				else {
					require_once module_wce::getTemplatePath('/common/article/article_render_simplemodel.tpl.php');
				}

			/*	// on met en cache si possible
				if (isset($this->fields['timestp_cache'])) {
					$this->fields['cache']=$page;
					$this->fields['timestp_cache']=  date("YmdHis");
					$this->save();
				}
			}
			else {
				$page=$this->fields['cache'];
			}*/

			$content = preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$page);

			$pathmodel=_WCE_MODELS_PATH."/pages_publiques/".$this->fields["model"];
			$webpathmodel=_WCE_WEB_MODELS_PATH."/pages_publiques/".$this->fields["model"];
			// test si il existe un fichier style.css

			if (file_exists($pathmodel."/style.css")) {
				$additional_css="<link type=\"text/css\" rel=\"stylesheet\" href=\"".$webpathmodel."/style.css\" media=\"screen\" title=\"styles\" />";
			}
		}
		else {
			$tabversioncontent="";
			if (isset($this->fields["content1"])) {
				if ($wce_mode!="online" && isset($adminedit) && isset($versionid) && is_numeric($versionid) && $versionid>0) {

					$rver=$db->query("	SELECT	*
										FROM	dims_mod_wce_article_version
										WHERE	id = :id",
										array(':id'=>array('value'=>$versionid,'type'=>PDO::PARAM_INT)));

					if ($db->numrows($rver)>0) {
						if ($fver=$db->fetchrow($rver)) {
							$tabversioncontent=$fver['content1'];
						}
					}
				}
				else $tabversioncontent=$this->fields["content1"];
			}

			$content = preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$tabversioncontent);
		}

		// on remplace maintenant les liens internes pour valider l'urlrewrite
		$rep_fromdeb=array();
		$rep_fromdeb[]= "'index.php";
		$rep_fromdeb[]= "\"index.php";

		$rep_todeb=array();
		$rep_todeb[]="'/index.php";
		$rep_todeb[]="\"/index.php";

		//on traite les objets dynamiques
		$dynobj=$wce_site->getDynamicObjects(true);
		if (!empty($dynobj)) {
			foreach ($dynobj as $object) {
				$contenttmp = str_replace($rep_fromdeb,$rep_todeb,preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$object['content']));
				$dynobj['WCE_OBJECT_'.$object['id']]=$contenttmp;
			}
		}

		// affichage des styles en Ã©dition
		if (isset($adminedit) && $adminedit==1 && $wce_mode=='edit') {
		  $content.= "<link type=\"text/css\" rel=\"stylesheet\" href=\"./common/modules/wce/css/dimsedit.css\" media=\"screen\" title=\"styles\" />";
		  $content.= "<link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"./common/js/colorpicker/css/colorpicker.css\" />";
		  $content.= "<script type=\"text/javascript\" src=\"./common/js/colorpicker/js/colorpicker.js\"></script>";
		}
		$content= str_replace($rep_fromdeb,$rep_todeb,$content);

		$rep_from[]= "'./index.php";
		$rep_from[]= "'/index.php";
		$rep_from[]= "'./index-quick.php";
		$rep_from[]= "'./data/";
		$rep_from[]= "\"/templates";
		$rep_from[]= "'/templates";
		$rep_from[]= "&quot;/templates";
		$rep_from[]= '"./index.php';
		$rep_from[]= '"/index.php';
		$rep_from[]= '"./index-quick.php';
		$rep_from[]= '"./data/';

		$rep_to[]="'".$root_path."/index.php";
		$rep_to[]="'".$root_path."/index.php";
		$rep_to[]="'".$root_path."/index-quick.php";
		$rep_to[]="'".$root_path."/data/";
		$rep_to[]= "\"".$root_path."/templates";
		$rep_to[]= "'".$root_path."/templates";
		$rep_to[]= "&quot;".$root_path."/templates";
		$rep_to[]='"'.$root_path."/index.php";
		$rep_to[]='"'.$root_path."/index.php";
		$rep_to[]='"'.$root_path."/index-quick.php";
		$rep_to[]='"'.$root_path."/data/";

		$content= str_replace($rep_from,$rep_to,$content);

		if ($adminedit) {
			$content.="<div id='dims_popup' style='position: absolute;visibility: hidden;top: 0px;left: 0px;z-index: 200;width: 200px;float: left;padding: 0px;color: #000000;text-align: left;'></div>";
		}
		// Traitement des variables dans les metas.
		if (isset($_SESSION['dynfield'])) {
			dims_dynamic_replace($this->fields['meta_description'], $_SESSION['dynfield']);
			dims_dynamic_replace($this->fields['meta_keywords'], $_SESSION['dynfield']);
			dims_dynamic_replace($this->fields['title'], $_SESSION['dynfield']);
		}

			if (!isset($this->fields['id'])) $this->init_description();

			if (!isset($article_sections)) $article_sections=array();

		if (isset( $this->fields['id']) &&	$this->fields['id']>0) {
			$tpl_page=array(
					'ID'						=> $this->fields['id'],
					'REFERENCE'					=> $this->fields['reference'],
					'TITLE'						=> "",
					'TITLE_FAVORITES'			=> addslashes($this->fields['title']),
					'AUTHOR'					=> $this->fields['author'],
					'VERSION'					=> $this->fields['version'],
					'DATE'						=> $article_timestp['date'],
					'LASTUPDATE_DATE'			=> $article_lastupdate['date'],
					'LASTUPDATE_DATE_DAY'					=> $lastupdate_detail['day'],
					'LASTUPDATE_DATE_MONTH'					=> $lastupdate_detail['month'],
					'LASTUPDATE_DATE_MONTH_LONG'			=> $lastupdate_detail['month_long'],
					'LASTUPDATE_DATE_MONTH_SMALL'			=> $lastupdate_detail['month_small'],
					'LASTUPDATE_DATE_YEAR'					=> $lastupdate_detail['year'],
					'LASTUPDATE_TIME'			=> $article_lastupdate['time'],
					'LASTUPDATE_USER_LASTNAME'				=> $user_lastname,
					'LASTUPDATE_USER_FIRSTNAME'				=> $user_firstname,
					'LASTUPDATE_USER_LOGIN'					=> $user_login,
					'TOP_CONTENT'				=> $this->fields['topcontent'],
					'LEFT_CONTENT'				=> $this->fields['leftcontent'],
					'RIGHT_CONTENT'				=> $this->fields['rightcontent'],
					'BOTTOM_CONTENT'			=> $this->fields['bottomcontent'],
					'META_DESCRIPTION'			=> $this->fields['meta_description'],
					'META_KEYWORDS'				=> $this->fields['meta_keywords'],
					'SCRIPT_BOTTOM'				=> $this->fields['script_bottom'],
					'SECTIONS'				=> $article_sections,
					'CONTENT'				=> $wce_site->replaceUrlContent($content),
					'COLOR'					=> $this->fields['color']);

			if(_DIMS_DEBUGMODE == true) {
				$tpl_page["TITLE"]		.= "(mode debug) - ";
			}

			/* Modif Pat pour prise en compte du titre meta */
			if (isset($_SESSION['dims']['catalogue_mode']) && $_SESSION['dims']['catalogue_mode']
					&& isset($_SESSION['dims']['tpl_page']['TITLE']) && $_SESSION['dims']['tpl_page']['TITLE'] !="") {
				$tpl_page["TITLE"]	.= $_SESSION['dims']['tpl_page']['TITLE'];
			}
			else {
				if (isset($this->fields['title_meta']) && $this->fields['title_meta']!='')
					$tpl_page["TITLE"]	.= $this->fields['title_meta'];
				else
					$tpl_page["TITLE"]	.= $this->fields['title'];
			}

			if (isset($_SESSION['dims']['catalogue_mode']) && $_SESSION['dims']['catalogue_mode']
					&& isset($_SESSION['dims']['tpl_page']['META_DESCRIPTION']) && $_SESSION['dims']['tpl_page']['META_DESCRIPTION'] !="") {
				$tpl_page["META_DESCRIPTION"] = $_SESSION['dims']['tpl_page']['META_DESCRIPTION'];
			}

			$smarty->assign('page', $tpl_page);
			$view->assign('page', $tpl_page);
		}
		$smarty->assign('debug_mode', _DIMS_DEBUGMODE);
		$view->assign('debug_mode', _DIMS_DEBUGMODE);
	}

	$additional_javascript = "";
	ob_start();
	/*if(dims::getInstance()->getScriptEnv() == 'admin.php')
		include DIMS_APP_PATH.'include/javascript.php';*/
	//include DIMS_APP_PATH.'modules/wce/include/javascript.php';

	$AutoResize = dims_load_securvalue('resize',dims_const::_DIMS_NUM_INPUT,true,true,false);

	if ((isset($adminedit) && $adminedit==1) || $wce_mode=="render") {
		echo "$(document).ready(function() {";
		if ($wce_mode!='render') {
			if ($wce_mode=='edit') {
				//echo "activeDimsBloc();autofitIframe();";//echo "window.parent.refreshTreeView(); activeDimsBloc();autofitIframe();";
				if($AutoResize != 1)
					echo " window.parent.activeDimsBloc();window.parent.autofitIframe();";
				else
					echo " window.parent.activeDimsBloc();";
			}
			elseif($AutoResize != 1)
				echo "window.parent.autofitIframe();";//echo "window.parent.refreshTreeView();autofitIframe();";
		}
		elseif($AutoResize != 1){
			echo "window.parent.autofitIframe();";
		}

		echo "});";
	}
	else {
		if ($wce_mode=='render') {
			echo "window.onload = function() {window.parent.autofitIframe();};";
		}
	}

	$additional_javascript = ob_get_contents();

	ob_end_clean();
	// template assignments
	$metadesc="";
	$metakeywords="";
	$title="";

	if (isset($_SESSION['dims']['catalogue_mode']) && $_SESSION['dims']['catalogue_mode'] && isset($_SESSION['dims']['tpl_page'])
		&& 	isset($_SESSION['dims']['tpl_page']['TITLE']) && $_SESSION['dims']['tpl_page']['TITLE'] !="") {

		$metadesc=$_SESSION['dims']['tpl_page']['META_DESCRIPTION'];
		$metakeywords=$_SESSION['dims']['tpl_page']['META_KEYWORDS'];
		$title=$_SESSION['dims']['tpl_page']['TITLE'];
	}

	if (isset($this->fields["id_workspace"]) && is_numeric($this->fields["id_workspace"])){
		$workspace=dims::getInstance()->getWorkspaces($this->fields["id_workspace"]);
		$work = new workspace();
		$work->open($this->fields["id_workspace"]);
	}else{
		$workspace=dims::getInstance()->getWorkspaces($_SESSION['dims']['workspaceid']);
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
	}
	if ($metadesc=="" && isset($work->fields['meta_description'])) $metadesc=($work->fields['meta_description']);
	if ($metakeywords=="" && isset($work->fields['meta_keywords'])) $metakeywords=($work->fields['meta_keywords']);
	if ($title=="" && isset($workspace['title'])) $title=$work->fields['title'];
	$dims_stats=array();

	$edito='';

	$res=$db->query("	SELECT	edito,content1
						FROM	dims_mod_wce_article
						WHERE	id_module = :id_module
						AND		edito = 1",
						array(':id_module'=>array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT)));
	if ($db->numrows($res)>0 && !isset($_SESSION['dims']['edito'])) {
		while ($fedito=$db->fetchrow($res)) {
			$edito=($fedito['content1']);
			$_SESSION['dims']['edito']=1;
		}
	}
	if (!isset($customfck)) $customfck='';

	$pathprint=$_SERVER['REQUEST_URI'];

	if (strpos($pathprint,"?")>0)
	{
		$pathprint.="&wceviewpdf=1";
	}
	else
	{
		$pathprint.="?wceviewpdf=1";
	}

	if (empty($_SESSION['dims']['userid'])) $nom_skin = 'smoothness';
	if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0) {
		$select = 'SELECT `nom_skin` FROM `dims_user` NATURAL JOIN `dims_skin` WHERE `id` = :id ;';
		$answer = $db->query($select,array(':id'=>array('value'=>$_SESSION['dims']['userid'],'type'=>PDO::PARAM_INT)));
		if ($fields = $db->fetchrow($answer)) {
			$nom_skin = $fields['nom_skin'];
		}
		else {
			$nom_skin = '';
		}
	}

	if (file_exists(realpath('.').str_replace("./","/",$template_path).$customfck.'/ckeditorarea.css'))
		$ckedit=$root_path.'/common'.str_replace("./","/",$template_path).$customfck.'/ckeditorarea.css';
	else
		$ckedit='';

	if (!isset($_SESSION['dims']['currentworkspace']['facebook']) || $_SESSION['dims']['currentworkspace']['facebook']=='') {
		$_SESSION['dims']['currentworkspace']['facebook']='#';
	}
	if (!isset($_SESSION['dims']['currentworkspace']['twitter']) || $_SESSION['dims']['currentworkspace']['twitter']=='') {
		$_SESSION['dims']['currentworkspace']['twitter']='#';
	}

	if (!isset($dims_content)) $dims_content='';
	$dims_timer=dims::getinstance()->getTimer();

	$dims_stats=dims::getinstance()->getStats($db,$dims_timer,$dims_content='');

	$tpl_site=array(
		'DYN_OBJECTS'                   => $dynobj,
		'TEMPLATE_PATH'                 => $template_path,
		'TEMPLATE_ROOT_PATH'            => $root_path.str_replace("./","/",_WCE_TEMPLATES_PATH."/$template_name"),
		'TEMPLATE_NAME'                 => $template_name,
		'TEMPLATE_ROOT_PATH_CK'         => $ckedit,
		'TEMPLATE_ROOT_PATH_BACKOFFICE' => $root_path.'/common'.str_replace("./","/",$_SESSION['dims']['template_path']),
		'ROOT_PATH'                     => $root_path.'/common',
		'EDITO'                         => $edito,
		'DEBUG_MODE'                    => defined('_DIMS_DEBUGMODE')?_DIMS_DEBUGMODE:false,
		'URL_PRINT'                     => $pathprint,
		'ADDITIONAL_JAVASCRIPT'         => $additional_javascript,
		'ADDITIONAL_CSS'                => $additional_css,
		'CONNECTEDUSERS'                => (isset($_SESSION['dims']['connectedusers'])) ? $_SESSION['dims']['connectedusers'] : "",
		'TITLE'                         => ($title),
		'WORKSPACE_ID'                  => $_SESSION['dims']['workspaceid'],
		'META_DESCRIPTION'              => $metadesc,
		'META_KEYWORDS'                 => $metakeywords,
		'META_AUTHOR'                   => (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
		'META_COPYRIGHT'                => (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
		'META_ROBOTS'                   => (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
		'SITE_TITLE'                    => (isset($_SESSION['dims']['currentworkspace']['label']) ) ? $_SESSION['dims']['currentworkspace']['label'] : '' ,
		'WORKSPACE_META_DESCRIPTION'    => $metadesc,
		'WORKSPACE_META_KEYWORDS'       => $metakeywords,
		'WORKSPACE_META_AUTHOR'         => (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
		'WORKSPACE_META_COPYRIGHT'      => (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
		'WORKSPACE_META_ROBOTS'         => (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
		'HOME_PAGE_URL'                 => (isset($_SESSION['dims']['homepageurl']))?$_SESSION['dims']['homepageurl']:'',
		'SITE_CONNECTEDUSERS'           => (isset($_SESSION['dims']['connectedusers'])) ?  $_SESSION['dims']['connectedusers'] : 0,
		'DIMS_PAGE_SIZE'                => sprintf("%.02f",$dims_stats['pagesize']/1024),
		'DIMS_EXEC_TIME'                => $dims_stats['total_exectime'],
		'DIMS_PHP_P100'                 => $dims_stats['php_ratiotime'],
		'DIMS_SQL_P100'                 => $dims_stats['sql_ratiotime'],
		'DIMS_NUMQUERIES'               => $dims_stats['numqueries'],
		'NAV'                           => $nav,
		'HOST'                          => $_SERVER['HTTP_HOST'],
		'URL'                           => (isset($_SERVER['SCRIPT_URI'])) ? $_SERVER['SCRIPT_URI'] : '',
		'DATE_DAY'                      => date('d'),
		'DATE_MONTH'                    => date('m'),
		'DATE_YEAR'                     => date('Y'),
		'LASTUPDATE_DATE'               => $lastupdate['date'],
		'LASTUPDATE_TIME'               => $lastupdate['time'],
		'DIMS_PAGE_SIZE'                => isset($dims_stats['pagesize']) ? sprintf("%.02f",$dims_stats['pagesize']/1024) : '',
		'DIMS_EXEC_TIME'                => isset($dims_stats['total_exectime']) ? $dims_stats['total_exectime']: '',
		'DIMS_PHP_P100'                 => isset($dims_stats['php_ratiotime']) ? $dims_stats['php_ratiotime'] : '',
		'DIMS_SQL_P100'                 => isset($dims_stats['sql_ratiotime']) ? $dims_stats['sql_ratiotime'] : '',
		'DIMS_NUMQUERIES'               => isset($dims_stats['numqueries']) ? $dims_stats['numqueries'] : '',
		'CSS_FILE'                      => $nom_skin,
		'TWITTER'                       => (isset($_SESSION['dims']['currentworkspace']['twitter'])) ? (dims_const::_RS_TWITTER.$_SESSION['dims']['currentworkspace']['twitter']) : "",
		'FACEBOOK'                      => (isset($_SESSION['dims']['currentworkspace']['facebook'])) ? (dims_const::_RS_FACEBOOK.$_SESSION['dims']['currentworkspace']['facebook']) : "",
		'YOUTUBE'                       => (isset($_SESSION['dims']['currentworkspace']['youtube'])) ? (dims_const::_RS_YOUTUBE.$_SESSION['dims']['currentworkspace']['youtube']) : "",
		'GOOGLE_PLUS'                   => (isset($_SESSION['dims']['currentworkspace']['google_plus'])) ? (dims_const::_RS_GOOGLE_PLUS.$_SESSION['dims']['currentworkspace']['google_plus']) : "",
		'FAVICON'                       => $work->getFrontFavicon($template_path),
		'LANG'                          => $this->fields['id_lang'],
		'CANONICAL'                     => (!is_null($this->getLightAttribute('canonical')))?$this->getLightAttribute('canonical'):"",
		'ADMIN_EDIT'                    => $adminedit,
	);

	$smarty->assign('site',$tpl_site);
	$view->assign('site',$tpl_site);

	// Kevin : Test à Bapsiste (je ne sais pas à quoi il sert...) :
	// if($template_name != 'netlorresponsive'){
	// 	$view->set_tpl_webpath('templates/frontoffice/'.$template_name.'/');
	// 	$view->setLayout('index_smarty.tpl.php'); //déclaration du layout principal
	// 	$view->compute();

	// } else {
		echo $smarty->display('index.tpl');
	//}

	if ($wce_mode=="edit" && dims::getInstance()->getScriptEnv() == 'admin.php') {
		echo "
			<script language=\"javascript\">
			var courantcontent=0;
			var maxcontent=".$nbcontent.";

			function FCKeditor_OnComplete( editorInstance ) {
				courantcontent+=1;

				if (courantcontent>=maxcontent) {
					window.parent.activeWceButton(true);
					window.parent.DIV_InitScroll();
				}
			}

		</script>
		";
	}
}else {
	header("HTTP/1.0 404 Not Found");
	$db = dims::getInstance()->getDb();
	$select = "	SELECT		id,urlrewriteold,urlrewrite
				FROM		dims_mod_wce_article
				WHERE		id_workspace=:idworkspace
				AND			(urlrewrite='error'
				OR			urlrewrite='erreur')";

	$res=$db->query($select, array(
		':idworkspace' => array('value' => $_SESSION['dims']['workspaceid'], 'type' => PDO::PARAM_INT),
	));
	$id_article=0;
	if ($db->numrows($res)>0) {
		// on recherche celui qui correspond le mieux
		// on recupere la liste des ip, on genere le path et on teste
		$article = new wce_article();
		while ($fields = $db->fetchrow($res)) {
			$id_article=$fields['id'];
			$_GET['articleid']=$id_article;
			$article->open($id_article,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			$article->display(module_wce::getTemplatePath("gestion_site/preview/display_article.tpl.php"));
		}
	} else {
		dims_404();
	}
}
