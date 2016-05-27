<?php
if (trim($article->fields['url']) != '') dims_redirect(trim($article->fields['url'])); // redirection
$nbVersion = $article->getListVersion($article->fields['id_lang']);
if ($article->fields['id_article_link'] != '' && $article->fields['id_article_link'] > 0){
	$article2 = new wce_article();
	$article2->open($this->fields['id_article_link'],$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
	require_once DIMS_APP_PATH.'modules/wce/wiki/include/class_module_wiki.php';
	$head = module_wiki::getRootHeadingFront();
	if(!is_null($head)){
		if($article2->fields['id_heading'] == $head->fields['id']){
			$articleid = $article->fields['id_article_link'];
			global $article;
			$article = $article2;
			$headingid = $article2->fields['id_heading'];
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
}elseif(($article->fields['timestp_unpublished'] == 0 || $article->fields['timestp_unpublished'] >= date('Ymd000000')) &&
		($article->fields['timestp_published'] == 0 || $article->fields['timestp_published'] <= date('Ymd000000')) &&
		($article->fields['uptodate'] || (!empty($nbVersion) && max($nbVersion) > 0)) || dims::getInstance()->getScriptEnv() == 'admin.php'){
	if(!isset($_SESSION['dims']['enter_article']))
		$_SESSION['dims']['enter_article'] = $article->fields['id'];
	if (!isset($smarty))
		global $smarty;
	if (!isset($smartypath))
		global $smartypath;
	if (!isset($months))
		global $months;
	if (!isset($dims))
		global $dims;

	global $recursive_mode;
	$recursive_mode = array();

	$smarty->assign('articlerewrite',$article->getRewriteLang());
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
	require_once DIMS_APP_PATH.'modules/system/class_action.php';
	require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_section_pagination.php';

	global $wce_mode;
	if(dims::getInstance()->getScriptEnv() != 'admin.php'){
		if (!isset($_SESSION['wce']['meter'][$articleid])) {
			$_SESSION['wce']['meter'][$articleid]=true;
			$article->updatecount();
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
						array(':id_article'=>array('value'=>$article->fields['id'],'type'=>PDO::PARAM_INT),
								':timestp'=>array('value'=>$day,'type'=>PDO::PARAM_STR),
								':email'=>array('value'=>$email,'type'=>PDO::PARAM_STR)));

		if ($db->numrows($res)>0) $art_meter->open($article->fields['id'],$day,$email);
		else {
			$art_meter->fields['id_article']=$article->fields['id'];
			$art_meter->fields['timestp']=$day;
			$art_meter->fields['email']=$email;
			$art_meter->fields['meter']=0;
		}

		$art_meter->updatecount();
		$article->updateCountTags();
	}

	$today = dims_createtimestamp();

	// initialisation des sections utilises pour les objets dynamiques
	$article_sections=array();

	$wce_mode = (!empty($_GET['wce_mode'])) ? $_GET['wce_mode'] : 'display';
	$readonly = (!empty($_GET['readonly']) && $_GET['readonly']==1) ? 1 : 0;

	$lastupdate['date']="";
	$lastupdate['time']="";

	if (!isset($_SESSION['dims']['currentheadingid'])) $_SESSION['dims']['currentheadingid']=0;
	if (!isset($_SESSION['dims']['currentarticleid'])) $_SESSION['dims']['currentarticleid']=0;

	// new update for managing redirect by domain to article
	// update Pat from 28/06/2010
	$id_workspace_domain=0;
	$id_domain=0;
	$select = " SELECT		distinct wd.id_workspace,d.id
				FROM		dims_workspace_domain as wd
				inner join	dims_domain as d
				on			d.id=wd.id_domain
				and			d.domain = :domain
				and			(wd.access=1 or wd.access=2)";

	$res=$db->query($select,array(':domain'=>array('value'=>$_SERVER['HTTP_HOST'],'type'=>PDO::PARAM_STR)));

	if ($fields = $db->fetchrow($res)) {
		$id_workspace_domain=$fields['id_workspace'];
		$id_domain=$fields['id'];
	}

	if (!isset($_SESSION['dims']['currentdomain_id'])) {
		$_SESSION['dims']['currentdomain_id']='';
	}

	$articleid=dims_load_securvalue("articleid",dims_const::_DIMS_NUM_INPUT,true,true,false,$articleid);
	$adminedit=dims_load_securvalue("adminedit",dims_const::_DIMS_NUM_INPUT,true,true);
	$versionid=dims_load_securvalue("versionid",dims_const::_DIMS_NUM_INPUT,true,true);
	$lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);

	/*if (empty($id_lang) || $id_lang <= 0){
		if (!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']))
			$lang=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];
	}*/

	if ($versionid>0) $_SESSION['wce'][$_SESSION['dims']['moduleid']][$articleid]['versionid']=$versionid;
	else {
		$_SESSION['wce'][$_SESSION['dims']['moduleid']][$articleid]['versionid']=0;
	}

	if ($wce_mode == 'render' || $wce_mode == 'display') {
		$type = '';
	}

	$wce_module_id=$_SESSION['dims']['moduleid'];

	$local_module_id=$wce_module_id;

	$default_template='default';

	// verification du moduleid courant au regard de l'heading et/ou article passï¿œ
	$article = new wce_article();
	if ($articleid>0) {
		$res=$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
		$local_module_id=$article->fields['id_module'];
			if ($article->fields['template']!='')
				$default_template=$article->fields['template'];

		// si article supprime on redirige
		if ($article->fields['id']==0) dims_redirect("/index.php");
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
		require_once DIMS_APP_PATH."modules/system/class_module.php";
		$mod = new module_wiki();
		$mod->open($_SESSION['dims']['wce_module_id']);
		$_SESSION['dims']['webworkspaceid']=$mod->fields['id_workspace'];
	}

	if (!isset($wce_site)) {
		$wce_site = new wce_site(dims::getInstance()->db,$wce_module_id);
		$wce_site->loadBlockModels();
		$_SESSION['dims']['homepageurl']=$wce_site->getHomePageUrl();
		$_SESSION['dims']['wce']['homePageUrl'] = $wce_site->getHomePageUrl();
	}


	$headings = wce_getheadings($wce_module_id);
	// recuperation de la famille e partir de l'articleid
	//$article->open($articleid);
	$headingid = $article->fields['id_heading'];

	if ($headingid==0 && $wce_module_id>1) {
		// on va recercher l'heading du CMS
		if (isset($headings['tree'][0][0])) $headingid=$headings['tree'][0][0];
	}

	// on test si on a un link
	if ($article->fields['id']>0) $article->verifyLinkedContent();

	$_SESSION['dims']['currentarticleid']=$articleid;
	$_SESSION['dims']['currentheadingid']=$headingid;
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid']=$_SESSION['dims']['currentheadingid'];
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid']=$_SESSION['dims']['currentarticleid'];

	if (isset($headings['list'][$headingid]) && isset($headings['list'][$headingid]['nav'])) {
		$nav = $headings['list'][$headingid]['nav'];
		$array_nav = explode('-',$nav);
	}
	else {
		$nav="";
		$array_nav=array();
	}

	// get template name
	$template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : $default_template;

	if (!file_exists(_WCE_TEMPLATES_PATH."/$template_name")) {
		$template_name = 'default';
	}
	$template_path = DIMS_APP_PATH."templates/frontoffice/$template_name";

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
		$heading->open($headingid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);

		/*if(dims::getInstance()->getScriptEnv() != 'admin.php' && $heading->fields['visible_if_connected']){
			if (!(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])){
				unset($_SESSION['dims']['currentarticleid']);
				dims_redirect(dims::getInstance()->getScriptEnv());
			}
		}*/

		// recherche de couleur
		if( isset($heading->fields['parents']) )$lisht=explode(";",$heading->fields['parents']);
		else $lisht = array();
		$lisht[]=$headingid;
		foreach($lisht as $i=>$hid) {
			if (isset($headings['list'][$hid]['colour']) && $headings['list'][$hid]['colour']!='') {
				$articlecolor=$headings['list'][$hid]['colour'];
			}
		}

		if (empty($heading->fields['fckeditor']) && isset($headings['list'][$heading->fields['id']]['fckeditor']) && $headings['list'][$heading->fields['id']]['fckeditor']!='') {
			$heading->fields['fckeditor']=$headings['list'][$heading->fields['id']]['fckeditor'];
		}
		if (!empty($heading->fields['fckeditor'])) {
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


		$res=$db->query($select,array(':id_module'=>array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT),
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
							$script = "javascript:window.parent.document.location.href='".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_ACTION_SHOW_ARTICLE)."&headingid".$id."&wce_mode=edit';";
							//$script = "javascript:window.parent.document.location.href='admin.php?op=article_modify&headingid={$headingid}&articleid={$row['id']}';";
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
		if (file_exists(DIMS_APP_PATH."templates/frontoffice/{$template_name}/erreur.tpl")) $template_file = 'erreur.tpl';
		else $template_file = 'index.tpl';
	}

	if (!file_exists(DIMS_APP_PATH."templates/frontoffice/{$template_name}/$template_file")) {
		echo "Fichier $template_file manquant";
		die();
	}

	//cyril : permet d'indiquer Ã  smarty si l'utilisateur est sur la page d'accueil de son site
	if($wce_site->getHomePageArtId()==$_SESSION['dims']['currentarticleid']) $smarty->assign("is_homepage", 1);
	else $smarty->assign("is_homepage", 0);


	// objects dynamiques
	$dynobj=array();
	if (!empty($articleid) && is_numeric($articleid)) {

		$content = '';
		$ishomepage = (
				!empty($headingid)
			&&	!empty($articleid)
			&&	(isset($article->fields['position']) && ($article->fields['position'] == 1 && $headings['list'][$headingid]['depth'] == 1 && $headings['list'][$headingid]['position'] == 1 && empty($headings['list'][$headingid]['linkedpage'])) || $headings['list'][$headings['tree'][0][0]]['linkedpage'] == $articleid)
		);

		$nbcontent=0;

		$article_timestp = (isset($article->fields['timestp']) && $article->fields['timestp']!='') ? dims_timestamp2local($article->fields['timestp']) : array('date' => '');
		if(isset($article->fields['timestp_published']) && $article->fields['timestp_published']!='') {
			$article_lastupdate = dims_timestamp2local($article->fields['timestp_published']);
			$lastupdate_detail_raw = dims_gettimestampdetail($article->fields['timestp_published']);
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
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']){
			$user_lastname = $_SESSION['dims']['user']['lastname'];
			$user_firstname = $_SESSION['dims']['user']['firstname'];
			$user_login = $_SESSION['dims']['user']['login'];
		}else{
			$user_lastname = $user_firstname = $user_login = "";
		}
		$user = new user();
		if (isset($article->fields['lastupdate_id_user']) && $user->open($article->fields['lastupdate_id_user'])) {
			if (isset($user->fields['lastname']) && isset($_SESSION['dims']['user'])) {
				$user_lastname = $_SESSION['dims']['user']['lastname'];
				$user_firstname = $_SESSION['dims']['user']['firstname'];
				$user_login = $_SESSION['dims']['user']['login'];
			}
		}

		$lastupdate = ($lastupdate = wce_getlastupdate()) ? dims_timestamp2local($lastupdate) : array('date' => '', 'time' => '');

		if (isset($print)) $template_file = 'print.tpl';
		elseif ($ishomepage && file_exists(DIMS_APP_PATH . "./common/templates/frontoffice{$template_name}/home.tpl")) $template_file = 'home.tpl';

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
						dimsAjaxLoading("zoneedit_dims_block"+blockid,"/admin.php?dims_op=wiki&op_wiki=getAjaxEditInfoBlock&block_id="+blockid+"&lang="+lang,focuselement);
					}

					function wceSaveLittleBlock(blockid) {
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

						/////////////////////////////////////////////////////
						// on rafraichit le contenu ss les liens transformes
						//$('#block'+blockid+'_'+contentid).html(ajax_load);
						//dimsAjaxLoading('block'+blockid+'_'+contentid,"/admin.php?dims_op=wiki&op_wiki=getAjaxEditContentBlock&block_id="+blockid+"&content_id="+contentid);
						/////////////////////////////////////////////////////
						loadUrl="/admin.php?dims_op=wiki&op_wiki=getAjaxEditContentBlock&block_id="+blockid+"&content_id="+contentid+"&lang="+langid;
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
								customConfig : '/common/modules/wce/ckeditor/ckeditor_config_fr_wiki.js',
								stylesSet:'default:/common/templates/frontoffice/<? echo $template_name;?>/ckstyles.js',
								contentsCss:'/common/templates/frontoffice/<? echo $template_name;?>/ckeditorarea.css'
							});


							//instance.config.stylesSet = 'dims_styles:/templates/frontoffice/<? echo $template_name;?>/ckstyles.js';

							instance.on('beforeCommandExec', function (event) {

								var blockelem=$('#block'+blockid+'_'+contentid);
								//var blockeedit=$('#wikiedit'+blockid+'_'+contentid);

								if (event.data.name === 'ajaxsave') {
									instance.updateElement();
									var form = $('#form_wce_block'+blockid+'_'+contentid);
									instance.updateElement();
									var blockelem=$('#block'+blockid+'_'+contentid);

									$('#fck_contentBlockReturn'+blockid+'_'+contentid).val(blockelem.html());
									$('#icon_valid_'+blockid).src="/common/modules/wce/wiki/gfx/puce_orange.png";
									event.cancel();
									$(form).submit();
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
																$(form).submit();

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
						dimsAjaxLoading('block'+blockid+'_'+contentid,"/admin.php?dims_op=wiki&op_wiki=getAjaxEditContentBlock&block_id="+blockid+"&content_id="+contentid+"&linksmodify=1"+"&lang="+langid);

						if (updated) {
							$('#icon_valid_'+blockid).src="/common/modules/wce/wiki/gfx/puce_orange.png";
						}

					}

					$(document).ready(function () {

						$('.ajaxForm').submit(function (event) {
							event.preventDefault();
							$.ajax({
								type: $(this).attr('method'),
								url: $(this).attr('action'),
								data: $(this).serialize()
							});
						});
					});
				</script>
				<?
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

				if($article->fields["model"]!="") {
					$model = array();
					$model['nb_elem'] = 0;
					$model['path'] = $article->fields["model"];
					$tab_editor = array();
					$page="";
					//ob_start();
					$page=$article->getModel();

					// definition du composant wcesite
					$article->setWceSite($wce_site);

					// ajout des references
					$smarty->assign('article_references',$article->getReferences());

					if ($article->isBlock()) {
						// detection de section ou de block
						if ($article->isSection()) {
							$blockcontent='';

							// recuperation des blocks
							$blocksSection=$article->getBlocksSection($article_sections,true,$versionid,$article->fields['id_lang']);

							// affectation de la variable pour smarty
							$smarty->assign('article_sections', $article_sections);

							// recuperation des modeles d'affichage
							$models=$article->getWceSite()->getBlockModels();

							// sections modeles
							$sections=$article->getSections();

							// chargement des objets
							$dynobjects=$article->getWceSite()->getDynamicObjects();

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
								case 'dyn_planning':
									$dyn = new DynObject($section['properties']['id_object'], $smarty, $section['properties']);
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

									include module_wiki::getTemplatePath('/article/article_edit_contentBlock.tpl.php');
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
							// check page content
							if ($page=='') $page=$article->getModel ();

							require_once(DIMS_APP_PATH . '/modules/wce/display_edit_blockmodel.php');
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

				$pathmodel=_WCE_MODELS_PATH."/pages_publiques/".$article->fields["model"];
				$webpathmodel=_WCE_WEB_MODELS_PATH."/pages_publiques/".$article->fields["model"];
				// test si il existe un fichier style.css

				if (file_exists($pathmodel."/style.css"))
					$additional_css="<link type=\"text/css\" rel=\"stylesheet\" href=\"".$webpathmodel."/style.css\" media=\"screen\" title=\"styles\" />";
				$editor = ob_get_contents();
				ob_end_clean();
			}
		}

		$smarty->assign('switch_content_page','');
		$article_timestp = (isset($article->fields['timestp']) && $article->fields['timestp']!='') ? dims_timestamp2local($article->fields['timestp']) : array('date' => '');
		if(isset($article->fields['timestp_published']) && $article->fields['timestp_published']!='') {
			$article_lastupdate = dims_timestamp2local($article->fields['timestp_published']);
		}
		else {
			$article_lastupdate = (isset($article->fields['lastupdate_timestp']) && $article->fields['lastupdate_timestp']!='') ? dims_timestamp2local($article->fields['lastupdate_timestp']) : array('date' => '', 'time' => '');
		}

		if (!empty($editor)) {
			$editor = str_replace("<ARTICLE_COLOR>", $articlecolor, $editor);
			$editor = str_replace("<PAGE_TITLE>", $article->fields['title'], $editor);
			$editor = str_replace("<PAGE_TITLE_FAVORITES>", addslashes($article->fields['title']), $editor);
			$editor = str_replace("<PAGE_AUTHOR>", $article->fields['author'], $editor);
			$editor = str_replace("<PAGE_VERSION>", $article->fields['version'], $editor);
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
		else if(isset($article->fields["model"]) && $article->fields["model"]) {
			//$pathmodel=substr($article->fields["model"],0,strlen($article->fields["model"])-9);
			$pathmodel=_WCE_MODELS_PATH."/pages_publiques/".$article->fields["model"];
			$webpathmodel=_WCE_WEB_MODELS_PATH."/pages_publiques/".$article->fields["model"];

			// test si il existe un fichier style.css

			if (file_exists($pathmodel."/style.css")) {
				$additional_css="<link type=\"text/css\" rel=\"stylesheet\" href=\"".$webpathmodel."/style.css\" media=\"screen\" title=\"styles\" />";
			}
			$page=$article->getModel();

			$page = str_replace("<ARTICLE_COLOR>", $articlecolor, $page);
			$page = str_replace("<PAGE_TITLE>", $article->fields['title'], $page);
			$page = str_replace("<PAGE_TITLE_FAVORITES>", addslashes($article->fields['title']), $page);
			$page = str_replace("<PAGE_AUTHOR>", $article->fields['author'], $page);
			$page = str_replace("<PAGE_VERSION>", $article->fields['version'], $page);
			$page = str_replace("<PAGE_DATE>", $article_timestp['date'], $page);
			$page = str_replace("<PAGE_LASTUPDATE_DATE>", $article_lastupdate['date'], $page);
			$page = str_replace("<PAGE_LASTUPDATE_TIME>", $article_lastupdate['time'], $page);
			$page = str_replace("<PAGE_LASTUPDATE_USER_LASTNAME>", $user_lastname, $page);
			$page = str_replace("<PAGE_LASTUPDATE_USER_FIRSTNAME>", $user_firstname, $page);
			$page = str_replace("<PAGE_LASTUPDATE_USER_LOGIN>", $user_login, $page);
			$page = str_replace("<LASTUPDATE_DATE>", $lastupdate['date'], $page);
			$page = str_replace("<MODEL_PATH>", $pathmodel, $page);

			if ($article->isBlock()) {
				$page=$article->getModel();

				// definition du composant wcesite
				$article->setWceSite($wce_site);

				// ajout des references
				$smarty->assign('article_references',$article->getReferences());
				// detection de section ou de block
				if ($article->isSection()) {
					$blockcontent='';

					// recuperation des blocks
					$blocksSection=$article->getBlocksSection($article_sections,false,0,$article->fields['id_lang']);

					// affectation de la variable pour smarty
					$smarty->assign('article_sections', $article_sections);

					// recuperation des modeles d'affichage
					$models=$article->getWceSite()->getBlockModels();

					// sections modeles
					$sections=$article->getSections();

					// chargement des objets
					$dynobjects=$article->getWceSite()->getDynamicObjects();
	//dims_print_r($sections);die();
					foreach ($sections as $idsection => $section) {
						// on initialise
						$contentsection='';

						switch ($section['type']) {
							case 'object':
								// init du contenu d'objet
								$contentobject=$section['value'];
								if (isset($blocks[$idsection][1]['content1'])) $contentobject=$blocks[$idsection][1]['content1'];

								/*if (file_exists(_WCE_MODELS_PATH."/objects/".$contentobject)) {
									ob_start();
									$smarty->display('file:'._WCE_MODELS_PATH."/objects/".$contentobject);
									$contentsection = ob_get_contents();
									ob_end_clean();
								}*/

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

								include module_wiki::getTemplatePath('/article/article_render_contentBlock.tpl.php');
								break;

						}

						// on va remplacer les differentes balises
						$page=str_replace($section['pattern'],$contentsection,$page);
					}
				}
				else {

					$blockcontent='';
					require_once module_wiki::getTemplatePath('/article/article_render_blockmodel.tpl.php');

					if (isset($subpages)) $smarty->assign('subpages', $subpages);


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
				require_once module_wiki::getTemplatePath('/article/article_render_simplemodel.tpl.php');
			}

			$content = preg_replace_callback('/\[\[(.*)\]\]/i','wce_getobjectcontent',$page);

			$pathmodel=_WCE_MODELS_PATH."/pages_publiques/".$article->fields["model"];
			$webpathmodel=_WCE_WEB_MODELS_PATH."/pages_publiques/".$article->fields["model"];
			// test si il existe un fichier style.css

			if (file_exists($pathmodel."/style.css")) {
				$additional_css="<link type=\"text/css\" rel=\"stylesheet\" href=\"".$webpathmodel."/style.css\" media=\"screen\" title=\"styles\" />";
			}
		}
		else {
			$tabversioncontent="";
			if (isset($article->fields["content1"])) {
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
				else $tabversioncontent=$article->fields["content1"];
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


			// /href=[.*]articleid=([0-9]+)[.*][ |>]/i
			// /href="[a-zA-Z.&?_-]+articleid=([0-9]+)[&"]/i
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
			dims_dynamic_replace($article->fields['meta_description'], $_SESSION['dynfield']);
			dims_dynamic_replace($article->fields['meta_keywords'], $_SESSION['dynfield']);
			dims_dynamic_replace($article->fields['title'], $_SESSION['dynfield']);
		}

			if (!isset($article->fields['id'])) $article->init_description();

			if (!isset($article_sections)) $article_sections=array();

		if (isset( $article->fields['id']) &&  $article->fields['id']>0) {
			$tpl_page=array(
					'ID'						=> $article->fields['id'],
					'REFERENCE'					=> $article->fields['reference'],
					'TITLE'						=> "",
					'TITLE_FAVORITES'			=> addslashes($article->fields['title']),
					'AUTHOR'					=> $article->fields['author'],
					'VERSION'					=> $article->fields['version'],
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
					'TOP_CONTENT'				=> $article->fields['topcontent'],
					'LEFT_CONTENT'				=> $article->fields['leftcontent'],
					'RIGHT_CONTENT'				=> $article->fields['rightcontent'],
					'BOTTOM_CONTENT'			=> $article->fields['bottomcontent'],
					'META_DESCRIPTION'			=> $article->fields['meta_description'],
					'META_KEYWORDS'				=> $article->fields['meta_keywords'],
					'SCRIPT_BOTTOM'				=> $article->fields['script_bottom'],
					'SECTIONS'				=> $article_sections,
					'CONTENT'				=> $wce_site->replaceUrlContent($content));

			if(_DIMS_DEBUGMODE == true) {
				$tpl_page["TITLE"]		.= "(mode debug) - ";

			}
					/* Modif Pat pour prise en compte du titre meta */
					if (isset($article->fields['title_meta']) && $article->fields['title_meta']!='')
						$tpl_page["TITLE"]	.= $article->fields['title_meta'];
					else
			$tpl_page["TITLE"]	.= $article->fields['title'];


			$smarty->assign('page', $tpl_page);
		}
		$smarty->assign('debug_mode', _DIMS_DEBUGMODE);
	}




	$additional_javascript = "";
	ob_start();
	/*if(dims::getInstance()->getScriptEnv() == 'admin.php')
		include DIMS_APP_PATH.'include/javascript.php';*/

	//include module_wiki::getTemplatePath('/include/javascript.php');

	$AutoResize = dims_load_securvalue('resize',dims_const::_DIMS_NUM_INPUT,true,true,false);

	if ((isset($adminedit) && $adminedit==1) || $wce_mode=="render") {
		echo "$(document).ready(function() {";
		if ($wce_mode!='render') {
			if ($wce_mode=='edit') {
				if($AutoResize != 1)
					echo " window.parent.activeDimsBloc();window.parent.autofitIframe();";//echo "window.parent.refreshTreeView(); activeDimsBloc();autofitIframe();";
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
		if ($wce_mode=='render' && $AutoResize != 1) {
			echo "window.onload = function() {window.parent.autofitIframe();};";
		}
	}

	$additional_javascript = ob_get_contents();

	ob_end_clean();
	// template assignments
	$metadesc="";
	$metakeywords="";
	$title="";

	if (isset($article->fields["id_workspace"]) && is_numeric($article->fields["id_workspace"])){
		$workspace=dims::getInstance()->getWorkspaces($article->fields["id_workspace"]);
		$work = new workspace();
		$work->open($article->fields["id_workspace"]);
	}else{
		$workspace=dims::getInstance()->getWorkspaces($_SESSION['dims']['workspaceid']);
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
	}
	if ($metadesc=="" && isset($workspace['meta_description'])) $metadesc=($workspace['meta_description']);
	if ($metakeywords=="" && isset($workspace['meta_keywords'])) $metakeywords=($workspace['meta_keywords']);
	if (isset($workspace['title'])) $title=$workspace['title'];
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

	if (!isset($dims_content)) $dims_content='';
	$dims_timer=dims::getinstance()->getTimer();

	$dims_stats=dims::getinstance()->getStats($db,$dims_timer,$dims_content='');

	//on recherche le nom de domaine pour la desinscription
	$sql_r =   "SELECT d.domain
				FROM dims_domain d
				INNER JOIN dims_workspace w
				ON w.newsletter_id_domain = d.id
				AND w.id = ".$_SESSION['dims']['workspaceid'];

	$res_r = $db->query($sql_r);
	$dom = $db->fetchrow($res_r);
	$dims=dims::getinstance();

	$tpl_site=array(
		'DYN_OBJECTS'			=> $dynobj,
		'TEMPLATE_PATH'			=> $template_path,
		'TEMPLATE_ROOT_PATH'		=> $root_path.str_replace("./","/",_WCE_TEMPLATES_PATH."/$template_name"),
		'DATA_PATH'			=> $dims->getProtocol().$dom['domain']."/data",
		'TEMPLATE_ROOT_PATH_CK'		=> $ckedit,
		'TEMPLATE_ROOT_PATH_BACKOFFICE' => $root_path.'/common'.str_replace("./","/",$_SESSION['dims']['template_path']),
		'ROOT_PATH'			=> $root_path.'/common',
		'EDITO'				=> $edito,
		'DEBUG_MODE'			=> defined('_DIMS_DEBUGMODE')?_DIMS_DEBUGMODE:false,
		'URL_PRINT'			=> $pathprint,
		'ADDITIONAL_JAVASCRIPT'		=> $additional_javascript,
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
		'WORKSPACE_META_KEYWORDS'	=> $metakeywords,
		'WORKSPACE_META_AUTHOR'		=> (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
		'WORKSPACE_META_COPYRIGHT'	=> (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
		'WORKSPACE_META_ROBOTS'		=> (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
		'HOME_PAGE_URL'			=> (isset($_SESSION['dims']['homepageurl']))?$_SESSION['dims']['homepageurl']:'',
		'SITE_CONNECTEDUSERS'		=> (isset($_SESSION['dims']['connectedusers'])) ?  $_SESSION['dims']['connectedusers'] : 0,
		'DIMS_PAGE_SIZE'		=> sprintf("%.02f",$dims_stats['pagesize']/1024),
		'DIMS_EXEC_TIME'		=> $dims_stats['total_exectime'],
		'DIMS_PHP_P100'			=> $dims_stats['php_ratiotime'],
		'DIMS_SQL_P100'			=> $dims_stats['sql_ratiotime'],
		'DIMS_NUMQUERIES'		=> $dims_stats['numqueries'],
		'NAV'				=> $nav,
		'HOST'				=> $_SERVER['HTTP_HOST'],
		'URL'				=> (isset($_SERVER['SCRIPT_URI'])) ? $_SERVER['SCRIPT_URI'] : '',
		'DATE_DAY'			=> date('d'),
		'DATE_MONTH'			=> date('m'),
		'DATE_YEAR'			=> date('Y'),
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
		'GOOGLE_PLUS'			=> (isset($_SESSION['dims']['currentworkspace']['google_plus'])) ? (dims_const::_RS_GOOGLE_PLUS.$_SESSION['dims']['currentworkspace']['google_plus']) : "",
		'FAVICON'			=> $work->getFrontFavicon($template_path),
		'LANG'				=> $article->fields['id_lang']);

	$smarty->assign('site',$tpl_site);

	$smarty->display('index.tpl');

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
