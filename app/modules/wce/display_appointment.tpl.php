<?php
require_once DIMS_APP_PATH.'include/class_template.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article_meter.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block_model.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_site.php';

require_once DIMS_APP_PATH.'modules/system/class_action.php';
//global $template_body;
//global $template_path;
global $wce_mode;
global $recursive_mode;
$recursive_mode = array();

// view pdf content of HTML
$wceviewpdf=dims_load_securvalue("wceviewpdf",dims_const::_DIMS_NUM_INPUT,true,true,false);

$today = dims_createtimestamp();

//$type = (!empty($_GET['type'])) ? $_GET['type'] : '';
$wce_mode = (!empty($_GET['wce_mode'])) ? $_GET['wce_mode'] : 'display';
$readonly = (!empty($_GET['readonly']) && $_GET['readonly']==1) ? 1 : 0;

$lastupdate['date']="";
$lastupdate['time']="";

if ((isset($_GET['headingid']) && !is_numeric($_GET['headingid'])) || (isset($_GET['articleid']) && !is_numeric($_GET['articleid']))) {
	dims_redirect($scriptenv);
}

if (!isset($_SESSION['dims']['currentheadingid'])) $_SESSION['dims']['currentheadingid']=0;
if (!isset($_SESSION['dims']['currentarticleid'])) $_SESSION['dims']['currentarticleid']=0;

// new update for managing redirect by domain to article
// update Pat from 28/06/2010
$id_workspace_domain=0;
$id_domain=0;
$articleid=0;

if(isset($_SESSION['dims']['enter_article']))
	$articleid = $_SESSION['dims']['enter_article'];
elseif(isset($_SESSION['dims']['currentarticleid'])){
	$articleid = $_SESSION['dims']['currentarticleid'];
}

$select = "	SELECT		distinct wd.id_workspace,d.id, d.id_home_wce_article
			FROM		dims_workspace_domain as wd
			INNER JOIN	dims_domain as d
			ON			d.id=wd.id_domain
			AND			d.domain = :domain
			AND			(wd.access=1 or wd.access=2)";
$params = array();
$params[':domain'] = array('value'=>$_SERVER['HTTP_HOST'],'type'=>PDO::PARAM_STR);

$res=$db->query($select,$params);

if ($fields = $db->fetchrow($res)) {
	$id_workspace_domain=$fields['id_workspace'];
	$id_domain=$fields['id'];
	if ($articleid <= 0 && $fields['id_home_wce_article'] != '' && $fields['id_home_wce_article'] > 0){
		$articleid = $fields['id_home_wce_article'];
		$_SESSION['dims']['enter_article'] = $articleid;
	}
}

if (!isset($_SESSION['dims']['currentdomain_id'])) {
	$_SESSION['dims']['currentdomain_id']='';
}

$smartypath = (isset($_SESSION['dims']['smarty_path']))?$_SESSION['dims']['smarty_path']:realpath('.')."/smarty";

$headingid=dims_load_securvalue("headingid",dims_const::_DIMS_NUM_INPUT,true,true,false);
$adminedit=dims_load_securvalue("adminedit",dims_const::_DIMS_NUM_INPUT,true,true);
$versionid=dims_load_securvalue("versionid",dims_const::_DIMS_NUM_INPUT,true,true);
$id_event=dims_load_securvalue("id_event",dims_const::_DIMS_NUM_INPUT,true,true);

if ($wce_mode == 'render' || $wce_mode == 'display') {
	$type = '';
}

$lstwcemods=$dims->getWceModules();
if (!empty($lstwcemods) && $wce_mode == 'display') {
	$wce_module_id= current($lstwcemods);
}
else {
	$wce_module_id=$_SESSION['dims']['moduleid'];
}

$local_module_id=$wce_module_id;
// verification du moduleid courant au regard de l'heading et/ou article passï¿œ
$article = new wce_article();
if ($articleid>0) {
	$res=$article->open($articleid);
	$local_module_id=isset($article->fields['id_module'])?$article->fields['id_module']:current(dims::getInstance()->getWceModules());
	// si article supprime on redirige
	if ($article->fields['id']==0) dims_redirect("/index.php");
}else
	$article->init_description();
$heading = new wce_heading();
if ($headingid>0) {
	$heading->open($headingid);
	if(isset($heading->fields['id_module'])) $local_module_id=$heading->fields['id_module'];
	else $local_module_id = $_SESSION['dims']['moduleid'];
	//$local_module_id=$heading->fields['id_module'];
}

if ($id_event>0) {
	$action= new action();
	$action->open($id_event);
	$id_workspace=$action->fields['id_workspace'];

	// recuperation du wce correspondant
	$params = array();
	$params[':id_workspace'] = array('value'=>$id_workspace,'type'=>PDO::PARAM_INT);
	$res=$db->query(	"
				SELECT		dims_module_workspace.id_module, dims_module_workspace.id_workspace
				FROM		dims_module
				INNER JOIN	dims_module_type
				ON			dims_module.id_module_type = dims_module_type.id
				AND			dims_module_type.label = 'WCE'
				INNER JOIN	dims_module_workspace
				ON			dims_module.id = dims_module_workspace.id_module
				AND			dims_module_workspace.id_workspace = :id_workspace",$params);

	while ($wcemod = $db->fetchrow($res)) {
		if ($wcemod['id_module']!=$local_module_id) {
			$local_module_id=$wcemod['id_module']; // on switch sur le module_id du workspace local ï¿œ l'event
		}
	}
}

// test
if ($local_module_id!=$wce_module_id) {
	if (isset($lstwcemods[$local_module_id])) {
		$wce_module_id=$local_module_id;
	}
	else {
		// on check en avancï¿œe si on peut y accï¿œder
		$lstwcemods=$dims->getWceModules(true);

		if (isset($lstwcemods[$local_module_id])) {
			$wce_module_id=$local_module_id;
		}
	}
}

if (!isset($_SESSION['dims']['wce_module_id']) || $_SESSION['dims']['wce_module_id']!=$wce_module_id) {
	$_SESSION['dims']['wce_module_id']=$wce_module_id;
	require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
	$mod = new module();
	$mod->open($_SESSION['dims']['wce_module_id']);
	$_SESSION['dims']['webworkspaceid']=$mod->fields['id_workspace'];
}

if (!isset($wce_site)) {
	$wce_site = new wce_site($db,$wce_module_id);
	$wce_site->loadBlockModels();
	$_SESSION['dims']['homepageurl']=$wce_site->getHomePageUrl();
	$_SESSION['dims']['wce']['homePageUrl'] = $wce_site->getHomePageUrl();
	$article->setWceSite($wce_site);
}

$headings = wce_getheadings($wce_module_id);
$dynobj = array();
if (empty($articleid) || !is_numeric($articleid) || $articleid==0 || isset($_GET['headingid'])) {
	if ((empty($headingid) || $headingid==0) && isset($headings['tree'][0][0]))  {// homepage

		$headingid = $headings['tree'][0][0];
		if ($headings['list'][$headingid]['linkedpage']) {

			if ($article->open($headings['list'][$headingid]['linkedpage'])
				&&	($article->fields['timestp_published'] <= $today || $article->fields['timestp_published'] == 0)
				&&	($article->fields['timestp_unpublished'] >= $today || $article->fields['timestp_unpublished'] == 0)
				) {

				$articleid = $headings['list'][$headingid]['linkedpage'];
				$headingid = $article->fields['id_heading'];
			}
		}

		// redirect heading
		if ($headings['list'][$headingid]['linkedheading']) {
			$headingid=$headings['list'][$headingid]['linkedheading'];
			if ($headings['list'][$headingid]['linkedpage']) {

				if ($article->open($headings['list'][$headingid]['linkedpage'])
					&&	($article->fields['timestp_published'] <= $today || $article->fields['timestp_published'] == 0)
					&&	($article->fields['timestp_unpublished'] >= $today || $article->fields['timestp_unpublished'] == 0)
					) {
					$articleid = $headings['list'][$headingid]['linkedpage'];
					$headingid = $article->fields['id_heading'];
				}
			}
		}
	}
	else {// acces par une rubrique
		//echo $headingid." ".$articleid." ".$headings['list'][$headingid]['linkedpage'];die();
		if (isset($headings['list'][$headingid]) && $headings['list'][$headingid]['linkedpage']) {
			if ($article->open($headings['list'][$headingid]['linkedpage'])
				&& ($article->fields['timestp_published'] <= $today || $article->fields['timestp_published'] == 0)
				&&	($article->fields['timestp_unpublished'] >= $today || $article->fields['timestp_unpublished'] == 0)) {
				$articleid = $headings['list'][$headingid]['linkedpage'];
				$headingid = $article->fields['id_heading'];
			}
			else {
				$article->fields['id']=0;
				$heading = new wce_heading();
				$heading->open($headingid);
				$heading->fields['linkedpage']='';

				$heading->save();
			}
		}
		else {
			// on a certainement une rubrique private
			$heading = new wce_heading();
			$heading->open($headingid);
			if ($heading->fields['private'] && !$_SESSION['dims']['connected']) {
				$dims->sessionReset();
				dims_redirect("/index.php");
			}
		}

		// redirect heading
		if ($headings['list'][$headingid]['linkedheading']) {
			$headingid=$headings['list'][$headingid]['linkedheading'];
			if ($headings['list'][$headingid]['linkedpage']) {

				if ($article->open($headings['list'][$headingid]['linkedpage'])
					&&	($article->fields['timestp_published'] <= $today || $article->fields['timestp_published'] == 0)
					&&	($article->fields['timestp_unpublished'] >= $today || $article->fields['timestp_unpublished'] == 0)
					) {
					$articleid = $headings['list'][$headingid]['linkedpage'];
					$headingid = $article->fields['id_heading'];
				}
			}
		}
	}

	// recherche du premier article
	if ($headingid>0) {
		$heading = new wce_heading();
		$heading->open($headingid);

		if ($articleid==0) $linkpage=$heading->getFirstPage();
		else $linkpage=$articleid;

		if ($linkpage>0) {
			// update heading
			/*if ($heading->fields['linkedpage']==0) {
				$heading->fields['linkedpage']=$linkpage;
				$heading->save();
			}*/
			$articleid=$linkpage;
			$article->open($articleid);
		}

		// redirect heading
		if ($heading->fields['linkedheading']) {
			$headingid=$heading->fields['linkedheading'];
			if ($headings['list'][$headingid]['linkedpage']) {

				if ($article->open($headings['list'][$headingid]['linkedpage'])
					&&	($article->fields['timestp_published'] <= $today || $article->fields['timestp_published'] == 0)
					&&	($article->fields['timestp_unpublished'] >= $today || $article->fields['timestp_unpublished'] == 0)
					) {
					$articleid = $headings['list'][$headingid]['linkedpage'];
					$headingid = $article->fields['id_heading'];
				}
			}
		}
	}
	// test si mode edition et clic sur rubrique qui redirige sur un article : modif Pat 01/04/2011
	if (isset($_GET['headingid']) && $articleid>0 && isset($_GET['adminedit']) && $_GET['adminedit']==1) {
		ob_flush();
		echo '<script language="javascript">';
		echo 'parent.document.location.href="/admin.php?op=article_modify&articleid='.$articleid.'";';
		echo '</script>';

		// test si mode edition et clic sur rubrique qui redirige sur un article : modif Pat 01/04/2011
		if (isset($_GET['headingid']) && $articleid>0 && isset($_GET['adminedit']) && $_GET['adminedit']==1) {
			ob_flush();
			echo '<script language="javascript">';
			echo 'parent.document.location.href="/admin.php?op=article_modify&articleid='.$articleid.'";';
			echo '</script>';

			// on modifie aussi le rendu PREVIEW du bloc article
			$_SESSION['dims']['wcemenu'][2] = _WCE_MENU_PREVIEW;
			die();
		}
	}
}
else {
	// recuperation de la famille e partir de l'articleid
	$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
	$headingid = $article->fields['id_heading'];
}

// on test si on a un link
if ($article->fields['id']>0) $article->verifyLinkedContent();

$_SESSION['dims']['currentarticleid']=$articleid;
$_SESSION['dims']['currentheadingid']=$headingid;
$_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid']=$_SESSION['dims']['currentheadingid'];
$_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid']=$_SESSION['dims']['currentarticleid'];

//on recupere la langue par defaut de l'article
if (isset($article->fields['id_lang'])) {
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$article->fields['id_lang'];
}
else {
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$_SESSION['dims']['currentlang'];
}

if (isset($headings['list'][$headingid]) && isset($headings['list'][$headingid]['nav'])) {
	$nav = $headings['list'][$headingid]['nav'];
	$array_nav = explode('-',$nav);
}
else {
	$nav="";
	$array_nav=array();
}

// get template name
if (isset($article->fields["id_workspace"]) && is_numeric($article->fields["id_workspace"])){
	$workspace=$dims->getWorkspaces($article->fields["id_workspace"]);
	$work = new workspace();
	$work->open($article->fields["id_workspace"]);
}else{
	$workspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
	$work = new workspace();
	$work->open($_SESSION['dims']['workspaceid']);
}

$template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : ((($t=$work->getDefaultTemplate())!='')?$t:'default');

// check for pdf view
if ($wceviewpdf && file_exists(_WCE_TEMPLATES_PATH."/print.tpl")) $template_name = 'print.tpl';

if (!file_exists(DIMS_APP_PATH."templates/frontoffice/$template_name")) $template_name = 'default';
$template_path = DIMS_APP_PATH."templates/frontoffice/$template_name";

//Baptiste
// Inclusion des styles
$vue = View::getInstance();
$manager = $vue->getStylesManager();
$manager->loadRessource('frontoffice', $template_name);
$styles = $manager->includeStyles();
$smarty->assign('styles', $styles);
$view->assign('styles', $styles);

// Inclusion des scripts du backoffice
$manager = $vue->getScriptsManager();

$manager->loadRessource('frontoffice', $template_name);
$scripts = $manager->includeScripts();
$smarty->assign('scripts',$scripts);
$view->assign('scripts',$scripts);

$_SESSION['dims']['front_template_name']=$template_name;
$_SESSION['dims']['front_template_path']=$template_path;
//$template_body = new Template($template_path);
$smarty->template_dir = $template_path;

if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/", 0777, true);

$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/";

if (file_exists("{$template_path}/config.php")) require_once "{$template_path}/config.php";

// chargement eventuel du modele
$wce_site->loadSchema(realpath(_WCE_TEMPLATES_PATH)."/$template_name/",'index');

// annulation de la construction des rubrique
//wce_template_assign($headings, $array_nav, 0, '', 0);
$smarty_heading=array();

// construction de la racine du site
$root_path=$article->getRootPath();

smarty_template_assign($smarty,$smarty_heading,$headings, $array_nav, 0 , '', '','',$root_path,$adminedit);

$smarty->assign('headings',$smarty_heading);
$additional_css="";

$template_path_fck=$template_path;

$articlecolor="";

if ($headingid>0) {
	$heading = new wce_heading();
	$heading->open($headingid);

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

/* ***********************************************************************/
/* nouvel algo de traitement des rewrite dans le contenu des pages		 */
/* 30/03/2009s									*/
/* ***********************************************************************/
$wce_site->loadRewritingUrl();

// get articles
if (isset($wce_module_id)) { // && isset($headingid) && is_numeric($headingid)) {
	$select =	"SELECT		*
			FROM		dims_mod_wce_article
			WHERE		id_module = :id_module
			AND			(timestp_published <= :timestp_published OR timestp_published = 0)
			AND			(timestp_unpublished >= :timestp_unpublished OR timestp_unpublished = 0)
			ORDER BY	position";
	$params = array();
	$params[':id_module'] = array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT);
	$params[':timestp_published'] = array('value'=>$today,'type'=>PDO::PARAM_INT);
	$params[':timestp_unpublished'] = array('value'=>$today,'type'=>PDO::PARAM_INT);

	$res=$db->query($select,$params);
	$arraypage=array();
	$arrayarticles=array();
	if ($db->numrows($res)>0) {
		while ($row = $db->fetchrow($res)) {
			if (empty($articleid)) $articleid = $row['id'];

			if ($row['visible']) {
				switch($wce_mode) {
					case 'edit':
						$script = "javascript:window.parent.document.location.href='admin.php?op=article_modify&headingid={$headingid}&articleid={$row['id']}';";
					break;

					case 'render':
							$script = "index.php?wce_mode=render&moduleid={$wce_module_id}&headingid={$headingid}&articleid={$row['id']}";
							//$script = "$scriptenv?nav={$nav}&articleid={$row['id']}";
					break;

					default:
					case 'display':
						if ($row['urlrewrite']!="" && isset($headings['list'][$row['id_heading']])
							&& $headings['list'][$row['id_heading']]['headingrewrite']!="" || isset($arraypagerewrite[$row['id']])) {
							$script = $root_path.$arraypagerewrite[$row['id']];
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

				//$ldate_pub = ($row['timestp_published']!='') ? dims_timestamp2local($row['timestp_published']) : array('date' => '');
				//$ldate_unpub = ($row['timestp_unpublished']!='') ? dims_timestamp2local($row['timestp_unpublished']) : array('date' => '');
				$ldate_pub = ($row['timestp_published']!='') ? $row['timestp_published'] : array('date' => '');
				$ldate_unpub = ($row['timestp_unpublished']!='') ? $row['timestp_unpublished'] : array('date' => '');

				$elemart=array(
						'ID'			=> $row['id'],
						'REFERENCE'				=> $row['reference'],
						'LABEL'			=> $row['title'],
						'CONTENT'		=> $row['content1'],
						'AUTHOR'		=> $row['author'],
						'VERSION'		=> $row['version'],
						'TIMESTP_PUB'	=> $ldate_pub['date'],
						'TIMESTP_UNPUB' => $ldate_unpub['date'],
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
		// tous les articles
		$smarty->assign('articles',$arrayarticles);
	}
	// fichier template par defaut
	$template_file = 'index.tpl';
}
else {
	if (file_exists(DIMS_APP_PATH . "templates/frontoffice/{$template_name}/erreur.tpl")) $template_file = 'erreur.tpl';
	else $template_file = 'index.tpl';
}
if (!file_exists(DIMS_APP_PATH . "templates/frontoffice/{$template_name}/$template_file")) {
	echo "Fichier $template_file manquant";
	die();
}

// cas particulier ou aucun article en page d'accueil
if (empty($articleid) && !empty($headingid) && $headings['tree'][0][0] == $headingid && file_exists(DIMS_APP_PATH . "templates/frontoffice/{$template_name}/home.tpl")) $template_file = 'home.tpl';

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
	elseif(!empty($headingid)) {
		$parentList = ($headings['list'][$headingid]['parents'] > 0)?$headings['list'][$headingid]['parents']:$headingid;
	}

	$parents = explode(';',$parentList);
	foreach($parents as $heading_parent) {
		$elem = array();
		$detail = $headings['list'][$heading_parent];

		if($detail['depth'] > 1) {
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

/*
	if(!empty($articleid) && !is_null($article)) {
		if(!empty($article->fields['urlrewrite']))
			$script = $article->fields['urlrewrite'];
		else
			$script = '/index.php?articleid='.$articleid;

		$elem['id']		= $article->fields['id'];
		$elem['type']	= 1; //Article
		$elem['label']	= $article->fields['title'];
		$elem['link']	= $script;

		$filArianne[] = $elem;
	}
*/
}

$smarty->assign('arianne', $filArianne);
// calcul des articles les plus consultes
$viewest_articles=array();
$sql ="	SELECT 		*
		FROM 		dims_mod_wce_article
		WHERE 		id_module = :id_module
		AND			(timestp_published <= :timestp_published OR timestp_published = 0)
		AND			(timestp_unpublished >= :timestp_unpublished OR timestp_unpublished = 0)
		ORDER BY 	meter DESC
		LIMIT 		0,5";
$params = array();
$params[':id_module'] = array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT);
$params[':timestp_published'] = array('value'=>$today,'type'=>PDO::PARAM_INT);
$params[':timestp_unpublished'] = array('value'=>$today,'type'=>PDO::PARAM_INT);

$res=$db->query($sql,$params);

if ($db->numrows($res)>0) {
	while ($row = $db->fetchrow($res)) {
		$articletmp = new wce_article();
		$articletmp->open($row['id']);
		$elemnew = array();
		$elemnew['id']=$row['id'];
		$elemnew['type']=1;
		$elemnew['link']=$articletmp->getUrlRewriting();
		$elemnew['label']=$row['title'];
		$elemnew['length']=strlen($row['title']);
		$elemnew['meter']=$row['meter'];
		$viewest_articles[]=$elemnew;
	}
}

$smarty->assign('viewest_articles',$viewest_articles);


//cyril : permet d'indiquer Ã  smarty si l'utilisateur est sur la page d'accueil de son site
if($wce_site->getHomePageArtId()==$_SESSION['dims']['currentarticleid']) $smarty->assign("is_homepage", 1);
else $smarty->assign("is_homepage", 0);

//$template_search = new Template(DIMS_APP_PATH . "./common/templates/frontoffice$template_name");
$smarty_search = new smarty();
$smarty_search->cache_dir = $_SESSION['dims']['smarty_path'].'/cache';
$smarty_search->config_dir = $_SESSION['dims']['smarty_path'].'/configs';
$smarty_search->compile_dir = $_SESSION['dims']['smarty_path']."/templates_c/".$template_name."/";
//ob_start();
$params = dims_load_securvalue($_GET, dims_const::_DIMS_CHAR_INPUT, true, true, true);
unset($params['md5']);
unset($params['object']);
$md5Contact = "";
if(isset($params['ct']) && $params['ct'] != '')
	$md5Contact = $params['ct'];
$name = $email = "";
$photo = "/common/img/contacts40.png";
if($md5Contact != '' && ($contact = $obj->getVerifContact($md5Contact)) !== false){
	$name = $contact->fields['firstname']." ".$contact->fields['lastname'];
	if(file_exists($contact->getPhotoPath(25)))
		$photo = "/".$contact->getPhotoWebPath(25);
	$obj->getListRep($contact->fields['id']);
}
if($obj->fields['private'] && ($name == '') && $obj->fields['status'] != dims_appointment_offer::STATUS_VALIDATED && (!isset($_SESSION['dims']['connected']) || !$_SESSION['dims']['connected']))
	dims_redirect("/".dims::getInstance()->getScriptEnv());
$obj->setLightAttribute('infos',array('name' => $name,
									  'photo' => $photo,
									  'email' => $email));
$obj->setLightAttribute('propositions',$obj->getListProp());
$obj->setLightAttribute('reponses',$obj->getListRep());
$obj->setLightAttribute('linkedDoc',$obj->getLinkedDoc());
$obj->setLightAttribute('submitApp',(isset($_SESSION['dims']['appointment']['reponse']) && $_SESSION['dims']['appointment']['reponse']));
unset($_SESSION['dims']['appointment']['reponse']);
ob_clean();
?>
<form method="POST" action="<?= $obj->getFrontUrl(); ?>" name="appointment_submit">
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="user" value="<?= $md5Contact; ?>" />
<?
if (file_exists(DIMS_APP_PATH."templates/frontoffice/$template_name/wce_appointment.php"))
	$obj->display(DIMS_APP_PATH."templates/frontoffice/$template_name/wce_appointment.php");
else
	$obj->display(DIMS_APP_PATH."/modules/system/appointment_offer/wce_appointment.php");
?>
</form>
<?
$content = ob_get_contents();

ob_end_clean();
$page=array(	'TITLE' => $_SESSION['cste']['_INVITATION']." : ".stripslashes($obj->fields['libelle']),
				'CONTENT' => $content,
				'META_DESCRIPTION'						=> $article->fields['meta_description'],
				'META_KEYWORDS'							=> $article->fields['meta_keywords']
			   );


$smarty->assign('page',$page);
$smarty->assign('switch_content_page','');

if ($_SESSION['dims']['connected']) {
	//$template_body->assign_block_vars('switch_user_logged_in', array());
	$smarty->assign('switch_user_logged_in','');
	$tpl_user=array('LOGIN'				=> $_SESSION['dims']['login'],
		'FIRSTNAME'			=> $_SESSION['dims']['user']['firstname'],
		'LASTNAME'			=> $_SESSION['dims']['user']['lastname'],
		'EMAIL'				=> $_SESSION['dims']['user']['email'],
		'SHOWPROFILE'		=> dims_urlencode($root_path.'/index.php?modcontent='.dims_const::_DIMS_MODULE_SYSTEM.'&op=showprofile'),
		'SHOWTICKETS'		=> dims_urlencode($root_path.'/index.php?modcontent='.dims_const::_DIMS_MODULE_SYSTEM.'&op=showtickets'),
		'SHOWFAVORITES'		=> dims_urlencode($root_path.'/index.php?modcontent='.dims_const::_DIMS_MODULE_SYSTEM.'&op=showfavorites'),
		'ADMINISTRATION'	=> dims_urlencode($root_path.'/admin.php?dims_mainmenu=0&dims_desktop=portal'.''),
		'DECONNECT'			=> dims_urlencode($root_path.'/index.php?dims_logout=1'));

	$smarty->assign('user',$tpl_user);
}
else
{
	$smarty->assign('switch_user_logged_out','');
}



$additional_javascript = "";
ob_start();
include(DIMS_APP_PATH . '/include/javascript.php');
include(DIMS_APP_PATH . '/modules/wce/include/javascript.php');
/*if (file_exists(DIMS_APP_PATH . "/modules/{$_SESSION['dims']['moduletype']}/include/javascript.php")) {
	include(DIMS_APP_PATH . "/modules/{$_SESSION['dims']['moduletype']}/include/javascript.php");
}*/

if ((isset($adminedit) && $adminedit==1) || $wce_mode=="render") {
	echo "$(document).ready(function() {";
	if ($wce_mode!='render') {
		if ($wce_mode=='edit') {
			echo "window.parent.refreshTreeView(); activeDimsBloc();autofitIframe();";
		}
		else
			echo "window.parent.refreshTreeView();autofitIframe();";
	}
	else {
		echo "autofitIframe();";
	}

	echo "});";
}
else {
	if ($wce_mode=='render') {
		echo "window.onload = function() {autofitIframe();};";
	}
}

$additional_javascript = ob_get_contents();

ob_end_clean();
// template assignments
$metadesc="";
$metakeywords="";
$title="";
//if (isset($article->fields['meta_description']) && $article->fields['meta_description']!="") $metadesc=$article->fields['meta_description'];
//if (isset($article->fields['meta_keywords']) && $article->fields['meta_keywords']!="") $metakeywords=$article->fields['meta_keywords'];

//if (empty($currentworkspace)) {

if(!isset($work) || !isset($workspace)){
	if (isset($article->fields["id_workspace"]) && is_numeric($article->fields["id_workspace"])){
		$workspace=$dims->getWorkspaces($article->fields["id_workspace"]);
		$work = new workspace();
		$work->open($article->fields["id_workspace"]);
	}else{
		$workspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
	}
}
if ($metadesc=="" && isset($workspace['meta_description'])) $metadesc=($workspace['meta_description']);
if ($metakeywords=="" && isset($workspace['meta_keywords'])) $metakeywords=($workspace['meta_keywords']);
if (isset($work->fields['title'])) $title=$work->fields['title'];

$dims_stats=array();

//require_once DIMS_APP_PATH . '/include/stats.php';

$edito='';

$params = array();
$params[':id_module'] = array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT);
$res=$db->query("	SELECT 	edito,content1
					FROM 	dims_mod_wce_article
					WHERE 	id_module = :id_module
					AND 	edito=1",$params);
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
	$params = array();
	$params[':id'] = array('value'=>$_SESSION['dims']['userid'],'type'=>PDO::PARAM_INT);
	$answer = $db->query($select,$params);
	if ($fields = $db->fetchrow($answer)) {
		$nom_skin = $fields['nom_skin'];
	}
	else {
		$nom_skin = '';
	}
}

$tpl_site=array(
	'DYN_OBJECTS'					=> $dynobj,
	'TEMPLATE_PATH'			=> $template_path,
	'TEMPLATE_ROOT_PATH'		=> $root_path.str_replace("./","/",_WCE_TEMPLATES_PATH."/$template_name"),
	'TEMPLATE_NAME' => $template_name,
	'TEMPLATE_ROOT_PATH_BACKOFFICE' => $root_path.'/common'.str_replace("./","/",$_SESSION['dims']['template_path']),
	'ROOT_PATH'			=> $root_path.'/common',
	'EDITO'				=> $edito,
	'DEBUG_MODE'					=> defined('_DIMS_DEBUGMODE')?_DIMS_DEBUGMODE:false,
	'URL_PRINT'					=> $pathprint,
	'ADDITIONAL_JAVASCRIPT'			=> $additional_javascript,
	'ADDITIONAL_CSS'		=> $additional_css,
	'CONNECTEDUSERS'		=> (isset($_SESSION['dims']['connectedusers'])) ? $_SESSION['dims']['connectedusers'] : "",
	'TITLE'				=> ($title),
	'WORKSPACE_ID'			=> $_SESSION['dims']['workspaceid'],
	'META_DESCRIPTION'		=> $metadesc,
	'META_KEYWORDS'			=> $metakeywords,
	'META_AUTHOR'			=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
	'META_COPYRIGHT'		=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
	'META_ROBOTS'			=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
	'SITE_TITLE'			=> (isset($_SESSION['dims']['currentworkspace']['label']) ) ? $_SESSION['dims']['currentworkspace']['label'] : '' ,
	'WORKSPACE_META_DESCRIPTION'	=> $metadesc,
	'WORKSPACE_META_KEYWORDS'		=> $metakeywords,
	'WORKSPACE_META_AUTHOR'		=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
	'WORKSPACE_META_COPYRIGHT'	=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
	'WORKSPACE_META_ROBOTS'		=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
	'HOME_PAGE_URL'					=> (isset($_SESSION['dims']['homepageurl']))?$_SESSION['dims']['homepageurl']:'',
	'SITE_CONNECTEDUSERS'		=> (isset($_SESSION['dims']['connectedusers'])) ?  $_SESSION['dims']['connectedusers'] : 0,
	/*'DIMS_PAGE_SIZE'		=> sprintf("%.02f",$dims_stats['pagesize']/1024),
	'DIMS_EXEC_TIME'		=> $dims_stats['total_exectime'],
	'DIMS_PHP_P100'			=> $dims_stats['php_ratiotime'],
	'DIMS_SQL_P100'			=> $dims_stats['sql_ratiotime'],
	'DIMS_NUMQUERIES'		=> $dims_stats['numqueries'],*/
	'NAV'					=> $nav,
	'HOST'				=> $_SERVER['HTTP_HOST'],
		'URL'							=> (isset($_SERVER['SCRIPT_URI'])) ? $_SERVER['SCRIPT_URI'] : '',
	'DATE_DAY'			=> date('d'),
	'DATE_MONTH'			=> date('m'),
	'DATE_YEAR'				=> date('Y'),
	'LASTUPDATE_DATE'		=> $lastupdate['date'],
	'LASTUPDATE_TIME'		=> $lastupdate['time'],
	'DIMS_PAGE_SIZE'		=> isset($dims_stats['pagesize']) ? sprintf("%.02f",$dims_stats['pagesize']/1024) : '',
	'DIMS_EXEC_TIME'		=> isset($dims_stats['total_exectime']) ? $dims_stats['total_exectime']: '',
	'DIMS_PHP_P100'			=> isset($dims_stats['php_ratiotime']) ? $dims_stats['php_ratiotime'] : '',
	'DIMS_SQL_P100'			=> isset($dims_stats['sql_ratiotime']) ? $dims_stats['sql_ratiotime'] : '',
	'DIMS_NUMQUERIES'		=> isset($dims_stats['numqueries']) ? $dims_stats['numqueries'] : '',
	'CSS_FILE'			=> $nom_skin,
	'TWITTER'			=> (isset($_SESSION['dims']['currentworkspace']['twitter'])) ? (dims_const::_RS_TWITTER.$_SESSION['dims']['currentworkspace']['twitter']) : "",
	'FACEBOOK'			=> (isset($_SESSION['dims']['currentworkspace']['facebook'])) ? (dims_const::_RS_FACEBOOK.$_SESSION['dims']['currentworkspace']['facebook']) : "",
	'YOUTUBE'			=> (isset($_SESSION['dims']['currentworkspace']['youtube'])) ? (dims_const::_RS_YOUTUBE.$_SESSION['dims']['currentworkspace']['youtube']) : "",
	'GOOGLE_PLUS'		=> (isset($_SESSION['dims']['currentworkspace']['google_plus'])) ? (dims_const::_RS_GOOGLE_PLUS.$_SESSION['dims']['currentworkspace']['google_plus']) : "",
	'FAVICON'			=> $work->getFrontFavicon($template_path),
	'LANG'				=> $article->fields['id_lang'],
	'CANONICAL'			=> (!is_null($article->getLightAttribute('canonical')))?$article->getLightAttribute('canonical'):""
);

	$smarty->assign('site',$tpl_site);


$sections=array();

$pos=-1;

preg_match_all("/<DIMSSECTION([^>]+)\>(.*)\<\/DIMSSECTION\>/",$content,$result,PREG_SET_ORDER);

if (!empty($result)) {
	foreach ($result as $section) {
		$struct=array();
		// construction du pattern de remplacement
		$struct['pattern']=$section[0];
		// on va construire les elements
		if (isset($section[1])) {
			$elem=$section[1];

			// on sépare sur les espaces
			$tabproperties=explode(" ",$elem);
			$name='';
			foreach ($tabproperties as $prop) {
				$provalue=explode("=",$prop);

				// test name + value
				if (isset($provalue[0]) && isset($provalue[1])) {
					$name=dims_sql_filter(strtolower(trim($provalue[0])));
					$valeur=  dims_sql_filter(trim(str_replace(array("'",'"'),"",$provalue[1])));

					if ($name=="id") $id=$valeur;
					// affectation
					$struct[$name]=$valeur;

					if ($name=='value') {
						// on traite les arguments en plus
						$propervalue=explode(";",$valeur);

						foreach ($propervalue as $el => $elvalue) {
							$propertiesvalue=explode(":",$elvalue);
							if (isset($propertiesvalue[0]) && isset($propertiesvalue[1])) {
								$namep=dims_sql_filter(strtolower(trim($propertiesvalue[0])));
								$valeurp=  dims_sql_filter(trim(str_replace(array("'",'"'),"",$propertiesvalue[1])));
								// affectation
								$struct['properties'][$namep]=$valeurp;
							}
						}
					}
				}
				else {
					if ($name!='' && isset($struct[$name]))
						$struct[$name].=" ".dims_sql_filter(trim(str_replace(array("'",'"'),"",$prop)));;
				}
			}
			if ($id>0)
				$sections[$id]=$struct;
		}
	}
}
require_once DIMS_APP_PATH.'/modules/wce/include/classes/class_dynobject.php';
foreach ($sections as $idsection => $section) {
	// on initialise
	$contentsection='';

	switch ($section['type']) {
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

		case 'text':
			// init des blocks si non defini
			$blocks=array();

			if (isset($blocksSection[$idsection])) {
				$blocks=$blocksSection[$idsection];
			}

			include module_wce::getTemplatePath('/common/article/article_render_contentBlock.tpl.php');
			break;

	}
	// on va remplacer les differentes balises
	$content = str_replace($section['pattern'],$contentsection,$content);
}
$page['CONTENT'] = $wce_site->replaceUrlContent($content);

$smarty->assign('page',$page);
$smarty->assign('is_homepage',false);

// buffer flushing
if (!$wceviewpdf) {
	$smarty->display('index.tpl');

	if ($wce_mode=="edit") {
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
}
else {
	// edition du contenu
	$name='edition.pdf';

	$pathrender=realpath(".")."/data/preview/wce-".$wce_module_id."/";
	if (!file_exists($pathrender)) {
		dims_makedir($pathrender);
	}

	if ($articleid>0 && isset($article->fields['timestp_published'])) {
		$pathrender.=md5("art-".$wce_module_id."-".$articleid)."-".$article->fields['timestp_published'].".pdf";
		$name=$article->fields['title'].".pdf";
	}
	elseif ($headingid>0) {
		$pathrender.=md5("head-".$wce_module_id."-".$headingid)."-".$heading->fields['timestp_published'].".pdf";
		$name=$heading->fields['label'].".pdf";
	}
	unlink($pathrender);
	if (!file_exists($pathrender)) {

//		require_once(realpath(".")."/scripts/dompdf/dompdf_config.inc.php");

		ob_start();
		$smarty->display('print.tpl');
		$contenthtml = ob_get_contents(); //utf8_decode(ob_get_contents());
		ob_end_clean();

		if ($contenthtml!='') {

		  if ( get_magic_quotes_gpc() )
			$contenthtml= stripslashes($contenthtml);

			//die($contenthtml);

			$pathrenderhtml=realpath(".")."/data/preview/wce-".$wce_module_id."/";

			if ($articleid>0 && isset($article->fields['timestp_published'])) {
				$pathrenderhtml.=md5("art-".$wce_module_id."-".$articleid)."-".$article->fields['timestp_published'].".html";
			}
			elseif ($headingid>0) {
				$pathrenderhtml.=md5("head-".$wce_module_id."-".$headingid)."-".$heading->fields['timestp_published'].".html";
			}

			$rep_from[]= $root_path."/data/";
			$rep_to[]=realpath(".")."/data/";
			$contenthtml= str_replace($rep_from,$rep_to,$contenthtml);

		  file_put_contents($pathrenderhtml, $contenthtml);
//		  exec('unoconv -f pdf "'.$pathrenderhtml.'"');
//		  exec('htmldoc --continuous --size 297x210mm --outfile '.$pathrender.' '.$pathrenderhtml);
			//die(realpath(".").'/scripts/wkhtmltopdf-i386 --orientation Landscape --page-size A4 '.$pathrenderhtml.' '.$pathrender);
		  exec(escapeshellcmd(realpath(".").'/scripts/wkhtmltopdf-i386 --orientation Landscape --page-size A4 '.$pathrenderhtml.' '.$pathrender));

		}
	}

	// download du fichier
	dims_downloadfile($pathrender, $name);
	exit(0);
}
