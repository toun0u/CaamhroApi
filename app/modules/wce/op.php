<?
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
dims_init_module('wce');
require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_heading.php';
require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_block.php');
require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_block_model.php');
require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_site.php');
require_once DIMS_APP_PATH . '/include/functions/workflow.php';

/*
$template_name=$_SESSION['dims']['front_template_name'];
$smartypath=$_SESSION['dims']['smarty_path'];
$smartyobject = new Smarty();
$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = DIMS_APP_PATH . "/modules/wce/";
*/
$dims_op=dims_load_securvalue('dims_op',dims_const::_DIMS_CHAR_INPUT,true,true);

if (!isset($headingid) && isset($_SESSION['wce']) && isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'])) {
	$headingid=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'];
}

if ((!isset($articleid) || $articleid==0) && isset($_SESSION['wce']) && isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid'])) {
	$articleid=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid'];
}

$wce_templates = wce_gettemplates();
$wce_models = wce_getmodels();

$wce_site = new wce_site($db);
$wce_site->loadBlockModels();

switch($dims_op) {
		case 'wiki':
			ob_clean();
			// dims_op=wiki&op_wiki=???
			//if(defined('_DISPLAY_WIKI') && _DISPLAY_WIKI) {
			include DIMS_APP_PATH."modules/wce/wiki/op.php";
			//}
			die();
			break;
		/*
		 * Fonction de déinscription d'un email
		 */
		case 'wce_unsubscribe':
			require_once(DIMS_APP_PATH . '/modules/system/class_mailinglist.php');
			$ml = new mailinglist();

			$email=dims_load_securvalue('email',dims_const::_DIMS_CHAR_INPUT,true,true); // ('field',type=num,get,post,sqlfilter=false)
			$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true); // ('field',type=num,get,post,sqlfilter=false)
			$workspaceid=dims_load_securvalue('workspaceid',dims_const::_DIMS_NUM_INPUT,true,true); // ('field',type=num,get,post,sqlfilter=false)

			if ($email !='' && $mailinglistid>0 && $workspaceid>0) {
				/*
				 * On remonte le workspace courant
				 */
				$workspace = new workspace();
				$workspace->open($workspaceid);



				/*
				 * On test si l'email est disponible, si c'est le cas on parcoure toutes les newsletters
				 */
				if ($email !='') {
					$ownmailinglist = $workspace->getMailingList();

					/*
					 * On parcoure l'ensemble des mailinglists attache pour détacher la personne
					 */
					foreach ($ownmailinglist AS $index => $mailingobj) {
						$ml = new mailinglist();
						$ml->open($mailingobj['id']);
						$ml->unsubscribeByMail($email);
					}

					/*
					 * Redirection vers page courante sur le site après mise à jour
					 */
					$path_return=dims_load_securvalue('path_return',dims_const::_DIMS_CHAR_INPUT,true,true);
					/*if (substr($path_return,-4,4)=='html') {
						$path_return.="?";
					}
					else {
						$path_return.="&";
					}*/

					// variable
					$_SESSION['unsubscribe_wce_newsletter']=1;
					// construction de la variable smarty pour affichage du texte
					dims_redirect($path_return);
				}

			}

			/*
			 * arret du script au cas ou non trouve
			 */
			die();
		break;
				case 'save_editwceblock':
					ob_end_clean();
					if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
						$id_bloc = dims_load_securvalue('id_bloc',dims_const::_DIMS_CHAR_INPUT,true,false);
						//$menu = '<ul id="icons"><li title=".ui-icon-carat-1-n" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-pencil" style="mouse:pointer;" onclick="javascript:alert(\''.$id_bloc.'\');"></span></li></ul>';
						$block='';
						if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['blocks'])) {

							$block=$wce_site->getblockById($id_bloc,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['blocks']);

							if (!empty($block)) {
								require_once(DIMS_APP_PATH . '/modules/wce/admin_detail_save_blockobject.php');
							}
						}
					}
					die();
					break;
				case 'wce_edit_bloc':
					ob_end_clean();
					if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
						$id_bloc = dims_load_securvalue('id_bloc',dims_const::_DIMS_CHAR_INPUT,true,false);

						//$menu = '<ul id="icons"><li title=".ui-icon-carat-1-n" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-pencil" style="mouse:pointer;" onclick="javascript:alert(\''.$id_bloc.'\');"></span></li></ul>';
						$block='';
						//dims_print_r($_SESSION['wce'][$_SESSION['dims']['moduleid']]['blocks']);
						if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['blocks'])) {

							$block=$wce_site->getblockById($id_bloc,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['blocks']);

							if (!empty($block)) {
								require_once(DIMS_APP_PATH . '/modules/wce/form_detail_edit_blockobject.php');
							}
						}

					}
					die();
					break;
				case 'switch_value_wce':
					ob_end_clean();
					$object = dims_load_securvalue('object',dims_const::_DIMS_NUM_INPUT,true,false);
					$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,false);
					$value = dims_load_securvalue('value',dims_const::_DIMS_NUM_INPUT,true,false);
					$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,false);

					if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
						if ($object==1) {
							$heading = new wce_heading();
							$heading->open($id);
							if ($type==0) {
								$heading->fields['visible']=!$value;
							}
							else {
								$heading->fields['is_sitemap']=!$value;
							}
							$heading->save();
						}
						else {
							$article = new wce_article();
							$article->open($id);
							if ($type==0) {
								$article->fields['visible']=!$value;
							}
							else {
								$article->fields['is_sitemap']=!$value;
							}
							$article->save();
						}

						if ($value==0) {
							echo '<a href="javascript:void(0);" onclick="updateStateWce('.$object.','.$type.','.$id.',1)">';
							echo '<img src="./common/img/bullet_sel.png">';
						}
						else {
							echo '<a href="javascript:void(0);" onclick="updateStateWce('.$object.','.$type.','.$id.',0)">';
							echo '<img src="./common/img/bullet.png">';
						}
						echo '</a>';
					}
					die();
					break;

	case 'desinscription_newsletter':
		$id_news = dims_load_securvalue('news',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$mail = dims_load_securvalue('mail',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if ($id_news > 0 && $mail != ''){
			require_once DIMS_APP_PATH . '/modules/system/class_mailingblacklist.php';
			$black = new mailingblacklist();
			$black->fields['id_newsletter'] = $id_news;
			$black->fields['email'] = $mail;
			$black->save();
		}
		break;
	case 'autoRssArticle':
		ob_clean();
		require_once DIMS_APP_PATH . '/include/class_dims_globalobject.php';
		require_once DIMS_APP_PATH . '/modules/rss/class_rssfeed.php';

		$id_global = dims_load_securvalue('id_global',dims_const::_DIMS_NUM_INPUT,true,true);
		$globalobject_art = new dims_globalobject();
		$globalobject_art->open($id_global);

		$id_rss = dims_load_securvalue('id_rss',dims_const::_DIMS_NUM_INPUT,true,true);
		$rss = new rssfeed();
		$rss->open($id_rss);

		$globalobject_rss = new dims_globalobject();
		$globalobject_rss->open($rss->fields['id_globalobject']);

		$rss = new rssfeed();
		$rss->open($globalobject_rss->fields['id_record']);

		$all_link = $globalobject_art->searchLink();
		if (in_array($globalobject_rss->fields['id'],$all_link[$globalobject_rss->fields['id_object']])){
			$globalobject_art->deleteLink($globalobject_rss);
			$rss->fields['isauto'] = 0;
		}
		else{
			$globalobject_art->addLink($globalobject_rss);
			$rss->fields['isauto'] = 1;
		}
		$rss->save();
		die();
		break;
	/*case 'refreshRssObject' :
		ob_clean();
		require_once DIMS_APP_PATH . '/modules/rss/class_rssfeed.php';
		require_once DIMS_APP_PATH . '/modules/rss/class_rsscache.php';
		require_once DIMS_APP_PATH . '/include/class_dims_globalobject.php';

		if (empty($id_global))
			$id_global = dims_load_securvalue('id_global',dims_const::_DIMS_NUM_INPUT,true,true);

		$globalobject_art = new dims_globalobject();
		$globalobject_art->open($id_global);

		if (empty($id_rss))
			$id_rss = dims_load_securvalue('id_rss',dims_const::_DIMS_NUM_INPUT,true,true);
		$rss = new rssfeed();
		$rss->open($id_rss);

		$liste_art = $rss->getList();
		$tr = 'trl1';
		$auto = '';
		$disa = '';
		if ($rss->fields['isauto']){
			$auto = 'checked="checked"';
			$disa = 'disabled="disabled"';
		}
		echo '<div style="margin-bottom:15px; margin-left:50px;font-weight:normal;">Passer ce flux (<b>'.$rss->fields['title'].'</b>) en automatique <input type="checkbox" onclick="javascript:dims_xmlhttprequest_todiv(\'admin.php\',\'dims_op=autoRssArticle&id_global='.$id_global.'&id_rss='.$id_rss.'\',\'\',\'rss_cache\');" '.$auto.'></div>';
		echo '<table cellspacing="0" cellpadding="0" width="1000px">';
		foreach ($liste_art as $id){
			$art = new rsscache();
			$art->open($id['id']);
			$date = dims_timestamp2local($art->fields['timestp']);

			if (!$rss->fields['isauto'])
				$js = "javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=addRssObject&id_global=$id_global&id_global_cache=".$art->fields['id_globalobject']."','','');";
			$sel = '';

			$all_link = $globalobject_art->searchLink();
			if (in_array($art->fields['id_globalobject'],$all_link[$art->id_globalobject]))
				$sel = 'checked="checked"';

			echo '	<tr>
						<td rowspan="3" valign="top">
							<input type="checkbox" onclick="'.$js.'" style="margin-right:5px;" '.$sel.' '.$disa.'>
						</td>
						<td style="width:100px;">
							Le : '.$date['date'].'
						</td>
						<td style="width:900px;">
							Titre : <b>'.$art->fields['title'].'</b>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							'.$art->fields['description'].'
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<a href="'.$art->fields['link'].'">'.$art->fields['link'].'</a>
						</td>
					</tr>';

			if ($tr == 'trl1')
				$tr = 'trl2';
			else
				$tr = 'trl1';
		}
		echo '</table>';

		die();
		break;*/

	case 'addRssObject' :
		ob_clean();
		require_once DIMS_APP_PATH . '/include/class_dims_globalobject.php';

		$id_global = dims_load_securvalue('id_global',dims_const::_DIMS_NUM_INPUT,true,true);
		$globalobject_art = new dims_globalobject();
		$globalobject_art->open($id_global);

		$id_global_cache = dims_load_securvalue('id_global_cache',dims_const::_DIMS_NUM_INPUT,true,true);
		$global_cache = new dims_globalobject();
		$global_cache->open($id_global_cache);

		$all_link = $globalobject_art->searchLink();
		if (in_array($global_cache->fields['id'],$all_link[$global_cache->fields['id_object']])){
			if (count($all_link[dims_const::_SYSTEM_OBJECT_WCE_ARTICLE]) > 0){
				require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
				foreach ($all_link[dims_const::_SYSTEM_OBJECT_WCE_ARTICLE] as $val){
					$art_global = new dims_globalobject();
					$art_global->open($val);
					$link_art = $art_global->searchLink();
					if ($art_global->fields['title'] == $global_cache->fields['title'] && in_array($global_cache->fields['id'],$link_art[dims_const::_SYSTEM_OBJECT_RSS_ARTICLE])){
						$params = array();
						$params[':id_article'] = array('value'=>$art_global->fields['id_record'],'type'=>PDO::PARAM_INT);
						$db->query("DELETE FROM		dims_mod_wce_object_corresp
									WHERE			id_article = :id_article",$params);

						$breve = new wce_article();
						$breve->open($art_global->fields['id_record']);
						$breve->delete();
					}
				}
			}
			$globalobject_art->deleteLink($global_cache);
		}
		else{
			$globalobject_art->addLink($global_cache);
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
			require_once DIMS_APP_PATH . '/modules/rss/class_rsscache.php';
			require_once DIMS_APP_PATH . '/modules/rss/class_rssfeed.php';
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article_object_corresp.php';

			$cache = new rsscache();
			$cache->open($global_cache->fields['id_record']);

			$feed = new rssfeed();
			$feed->open($cache->fields['id_rssfeed']);

			$breve = new wce_article();
			$breve->init_description();
			$breve->setugm();

			$breve->fields['title'] = $cache->fields['title'];
			if ($cache->fields['author'] == '')
				$breve->fields['author'] = $feed->fields['title'];
			else
				$breve->fields['author'] = $cache->fields['author'];
			//suppression du titre dans le body pour les flux Google
			if (strpos($breve->fields['author'],'Google') === false)
				$breve->fields['description'] = $cache->fields['description'];
			else{
				$title = explode('-',$cache->fields['title']);
				$breve->fields['description'] = preg_replace("/.*".trim($title[count($title)-1])."(.*(\.){3})\<br \/\>.*/i",'${1}',$cache->fields['description']);
				$breve->fields['description'] = preg_replace('`^(<br />)?(.+?)(<br />)?$`sim', '$2', $breve->fields['description']);
				//echo "/".$title[count($title)-1]."(.*\.\.\.)/i";
				echo $breve->fields['description'];
				ob_flush();
			}

			$breve->fields['lastupdate_timestp'] = $cache->fields['timestp'];
			$breve->fields['timestp_modify'] = $cache->fields['timestp'];
			$breve->fields['id_heading'] = 0;
			$breve->fields['picto']		= $cache->fields['image'];
			$breve->fields['url']		= $cache->fields['link'];
			$breve->fields['source']	= $feed->fields['title'];
			$breve->fields['type']		= dims_const::_SYSTEM_OBJECT_RSS_ARTICLE;
			$id = $breve->save();
			$breve->open($id);

			$global_breve = new dims_globalobject();
			$global_breve->open($breve->fields['id_globalobject']);

			$global_cache->addLink($global_breve);
			$globalobject_art->addLink($global_breve);

			$sel_max = "SELECT	MAX(position) as max
						FROM	dims_mod_wce_object_corresp
						WHERE	id_heading = 0
						AND		id_object = :id_object";
			$params = array();
			$params[':id_object'] = array('value'=>$globalobject_art->fields['id_record'],'type'=>PDO::PARAM_INT);
			$res_max = $db->query($sel_max,$params);
			if ($max = $db->fetchrow($res_max))
				if ($max['max'] == NULL)
					$m = 0;
				else
					$m = $max['max']+1;
			else
				$m = 0;
			$objt_corresp = new article_object_corresp();
			$objt_corresp->init_description();
			$objt_corresp->fields['id_article']= $global_breve->fields['id_record'];
			$objt_corresp->fields['id_heading']= 0;
			$objt_corresp->fields['id_object']= $globalobject_art->fields['id_record'];
			$objt_corresp->fields['position']=$m;
			$objt_corresp->save();
		}
		die();
		break;

	case 'rss_content':
		if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $rootpath="http://";
		else $rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];

		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>
		<rss version="2.0"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:content="http://purl.org/rss/1.0/modules/content/">';
		echo "<channel>";
		echo "<title>".$rootpath."</title>
			<link>".$rootpath."/index.php?dims_op=rss_content</link>
			<description></description>
			<copyright></copyright>
			<language>fr-FR</language>";

		// mode standard
		$select_object =	"
							SELECT			a.*
							FROM			dims_mod_wce_article as a
							WHERE			a.actu=1
							AND				a.id_module = :id_module
							ORDER BY		a.timestp_modify_first DESC";
		$params = array();
		$params[':id_module'] = array('value'=>$_SESSION['dims']['wcemoduleid'],'type'=>PDO::PARAM_INT);

		$result_object = $db->query($select_object,$params);
		$cpte=1;

		$arrayElem = array();

		if (!isset($wce_site)) {
			$wce_site = new wce_site($db,$_SESSION['dims']['wcemoduleid']);
		}

		$actu_sel=dims_load_securvalue('actu_sel',dims_const::_DIMS_NUM_INPUT,true,true,false);

		$total=$db->numrows($result_object);
		while ($fields = $db->fetchrow($result_object)) {
			$path=realpath('.').'/data/articles/'.$fields['picto'];
			$webpath=$dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];

			if ($fields['picto']!='' && file_exists($path)) {

				$ext = explode('.', $fields['picto']);
				$ext = strtolower($ext[count($ext)-1]);
				$webpathactu=$dims->getProtocol().$dims->getHttpHost().'/data/articles/art_'.$fields['id']."_500.".$ext;

				$elem=array();
				$elem['id'] = $fields['id'];
				$elem['title'] = $fields['title'];
				$elem['date_published']=$fields['lastupdate_timestp'];
				$elem['description'] = $fields['description'];
				$elem['path'] = $webpath;
				$elem['pathactu'] = $webpathactu;
				$elem['target']= "";
				if ($fields['url']!='') {
					if (substr($fields['url'],0,4)!='http') {
						$fields['url']="http://".$fields['url'];
					}
					$elem['link']=str_replace("./", $dims->getProtocol().$dims->getHttpHost()."/",$fields['url']);
					$elem['link2'] = $elem['link'];
				}
				else {
					$elem['link'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?articleid=".$fields['id'];
					$elem['link2'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?articleid=".$fields['id'];
				}

				if ($fields['url_window']) $elem['target']= " target='_blank' ";
				$elem['cpte'] = $cpte;

				$arrayElem[]=$elem;
				$cpte++;
			}
		}
		$c=0;

		$contentcms='';

		if (sizeof($arrayElem)>0) {
			foreach ($arrayElem as $elem) {
				$ldate_pub = ($elem['date_published']!='') ? dims_timestamp2local($elem['date_published']) : array('date' => '');

				$contentcms.= "
				<item>
				  <pubDate>".$ldate_pub['date']." GMT</pubDate>
					<title>".utf8_encode($elem['title'])."</title>
					<link>".utf8_encode($elem['link'])."</link>
					<guid>".utf8_encode($elem['id'])."</guid>
					<dc:date>".$ldate_pub['date']." GMT</dc:date>
					<dc:format>text/html</dc:format>
					<dc:language>fr</dc:language>


				</item>
				";
			}
		}

		echo  $wce_site->replaceUrlContent($contentcms);

		echo "</channel>
		</rss>";
		die();
		break;
	case 'getAjaxEditContentBlock':
		ob_clean();
		$article= new wce_article();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$linksmodify=dims_load_securvalue('linksmodify',dims_const::_DIMS_NUM_INPUT,true,true);

		$versionid=0;
		if (isset($_SESSION['dims']['connected'])
			&& $_SESSION['dims']['connected']
			&& (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id,$id_lang);
			$articleid=$block->fields['id_article'];
			if ($linksmodify==0) {
				// on test si bloc modifie ou non
				if ($block->fields['uptodate'] && $block->fields['content'.$content_id] == $block->fields['draftcontent'.$content_id])
					echo $block->fields['content'.$content_id];
				else
					echo $block->fields['draftcontent'.$content_id];
			}
			else {
				// on demande la version modifiée
				// on test si bloc modifie ou non
				if ($block->fields['uptodate'] && $block->fields['content'.$content_id] == $block->fields['draftcontent'.$content_id])
					$content= $block->fields['content'.$content_id];
				else
					$content= $block->fields['draftcontent'.$content_id];

				// on transforme
				$content=wce_article::convertLinksToLinksEdit($content);

				//$content .= "<script type=\"text/javascript\">if(SyntaxHighlighter != undefined){if(brushes != undefined){SyntaxHighlighter.autoloader(brushes);} SyntaxHighlighter.all(); }</script>";
				echo $content;
			}
		}
		die();
		break;
	/*case 'getAjaxEditContentBlock':
		ob_clean();
		$article= new wce_article();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$linksmodify=dims_load_securvalue('linksmodify',dims_const::_DIMS_NUM_INPUT,true,true);

		$versionid=0;

		if (isset($_SESSION['dims']['connected'])
			&& $_SESSION['dims']['connected']
			&& (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))
			&& $content_id <= $article->getNbElements() ) {
			$block->open($block_id,$id_lang);
			$articleid=$block->fields['id_article'];
			if ($linksmodify==0) {
				// on test si bloc modifie ou non
				if ($block->fields['uptodate'] && $block->fields['content'.$content_id] == $block->fields['draftcontent'.$content_id])
					echo $block->fields['content'.$content_id];
				else
					echo $block->fields['draftcontent'.$content_id];
			}
			else {
				// on demande la version modifiée
				// on test si bloc modifie ou non
				if ($block->fields['uptodate'] && $block->fields['content'.$content_id] == $block->fields['draftcontent'.$content_id])
					$content= $block->fields['content'.$content_id];
				else
					$content= $block->fields['draftcontent'.$content_id];

				// on transforme
				$content=wce_article::convertLinksToLinksEdit($content);

				echo $content;
			}

		}
		die();
		break;*/
	case 'modify_blockcontent':
		ob_end_clean();
		ob_start();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);

		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id);
			echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY'],'100%','','');
			include(DIMS_APP_PATH . '/modules/wce/admin_article_blockcontent_form.php');
			echo $skin->close_simplebloc();
		}
		die();
		break;
	case 'add_block':
		ob_clean();
		$section_id=dims_load_securvalue('section',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$block = new wce_block();
		$block->init_description();
		$block->fields['id_article']=$_SESSION['wce']['articleid'];
		$block->fields['section']=$section_id;
		$block->fields['id_lang']=$id_lang;
		$block->setugm();
		if ($section_id>0)
			echo $skin->open_simplebloc($_DIMS['cste']['_ADD']. " > section ".$section_id,'100%','','');
		else
		echo $skin->open_simplebloc($_DIMS['cste']['_ADD'],'100%','','');
		$block->display(module_wce::getTemplatePath('/common/block/admin_article_block_form.php'));
		echo $skin->close_simplebloc();
		die();
		break;
	case 'modify_block':
		ob_clean();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id,$lang);
			echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY'],'100%','','');
			$block->display(module_wce::getTemplatePath('/common/block/admin_article_block_form.php'));
			echo $skin->close_simplebloc();
		}
		die();
		break;
	case 'move_block':
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$sens=dims_load_securvalue('sens',dims_const::_DIMS_NUM_INPUT,true,true);
		$contentid=dims_load_securvalue('contentid',dims_const::_DIMS_NUM_INPUT,true,true);

		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id);
			$block->move($sens,$contentid);
		}
		die();
		break;
	case 'refreshTreeView':
		ob_end_clean();
		ob_start();

		require_once(DIMS_APP_PATH . '/modules/wce/admin_content_menutop.php');

		if (!isset($_SESSION['wce_expand_tree'])) {
			$_SESSION['wce_expand_tree']=false;
		}

		if (isset ($_GET['wce_expand_tree'])) {
			$_SESSION['wce_expand_tree']=dims_load_securvalue('wce_expand_tree',dims_const::_DIMS_NUM_INPUT,true,true);
		}
		$link="<a href=\"/admin.php?wce_expand_tree=".(!$_SESSION['wce_expand_tree'])."\"><img src=\"./common/img/zoomouput.png\" style=\"border:0px\"></a>";
		echo $skin->open_simplebloc(str_replace("LABEL",$_SESSION['dims']['modulelabel'],$_DIMS['cste']['_DIMS_LABEL_PAGE_TITLE'])." ".$link,'margin-top:12px;','','');
		//echo $skin->open_simplebloc(str_replace("LABEL",$_SESSION['dims']['modulelabel'],$_DIMS['cste']['_DIMS_LABEL_PAGE_TITLE']),'margin-top:12px;','','');

		$headings = wce_getheadings();
		$articles = wce_getarticles();
		$headingid = wce_setheadingid($headings);
		//echo wce_build_tree($headings, $articles,0, '', 1, '', $_SESSION['dims']['currentheadingid'],$_SESSION['dims']['currentarticleid']);
		echo wce_build_tree($headings, $articles,0, '', 1, '', $_SESSION['dims']['currentheadingid'],$_SESSION['dims']['currentarticleid'],array(),array(),$_SESSION['wce_expand_tree']);
		echo "<div style=\"clear:both;\"></div>";
		echo $skin->close_simplebloc();
		die();
		break;
	case 'refreshWceDesktop':

		ob_end_clean();
		ob_start();

		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['blockid'],$_SESSION['wce'][$_SESSION['dims']['moduleid']]['blockid']);
		//echo "old :".$_SESSION['dims']['wcemenu'][$block_id];
		$wcemenu=dims_load_securvalue('wcemenu',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['wcemenu'][$block_id],$_SESSION['dims']['wcemenu'][$block_id]);
		//echo "new :".$_SESSION['dims']['wcemenu'][$block_id];

				//$readonly = ( !dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) && !dims_isactionallowed(0) && (($article->fields['status'] == 'wait') && (!(dims_isactionallowed(_WCE_ACTION_ARTICLE_PUBLISH) || in_array($_SESSION['dims']['userid'],$wfusers)))));
		// get workflow validators
		$wfusers = array();
		$wgroups = array();
		// on parcourt chaque heading du niveau sup&eacute;rieur et on r&eacute;cup&eacute;re la liste
		$objheading = new wce_heading();
		$objheading->open($_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid']);
		if(isset($objheading->fields['parents'])){
			foreach(explode(';',$objheading->fields['parents'].";".$headingid) as $hid) {
				//dims_print_r(dims_workflow_get(_WCE_OBJECT_HEADING, $hid));
				foreach(dims_workflow_get(_WCE_OBJECT_HEADING, $hid,-1,-1,_WCE_ACTION_ARTICLE_EDIT) as $value) {
					if ($value['type_workflow']=='user') {
						$wfusers[] = $value['id_workflow'];
					}
					else {
						$wfgroups[] = $value['id_workflow'];
					}
				}
			}
		}

		$pubusers = array();
		if (!empty($wfusers)) {
			$params = array();
			$sql = "SELECT		DISTINCT id,login,lastname,firstname
					FROM		dims_user
					WHERE		id in (".$db->getParamsFromArray($wfusers,'id',$params).")
					ORDER BY	lastname, firstname";
			$res=$db->query($sql,$params);
			while ($row = $db->fetchrow($res)) $pubusers[$row['id']] = $row;
		}

		if (!empty($wfgroups)) {
			$params = array();
			$sql = "SELECT		DISTINCT id,login,lastname,firstname
					FROM		dims_user
					INNER JOIN	dims_group_user as gu
					ON			gu.id_user=dims_user.id
					AND			gu.id_group in (".$db->getParamsFromArray($wfgroups,'id',$params).")
					ORDER BY	lastname, firstname";
			$res=$db->query($sql,$params);
			while ($row = $db->fetchrow($res)) $pubusers[$row['id']] = $row;
		}


		$readonly=!isset($pubusers[$_SESSION['dims']['userid']]);
		if (dims_isadmin() || dims_isactionallowed(0)) $readonly=false;


		//echo "<div style=\"float: left;width:18px;margin-top:2px;\"><a title=\"Agrandir/R&eacute;tr&eacute;cir\" href=\"#\" onclick=\"javascript:dims_switchdisplay('wce_tree');dims_xmlhttprequest('admin-light.php', 'op=xml_switchdisplay&display='+dims_getelem('wce_tree').style.display, true);\"><img src=\"./common/modules/wce/img/fullscreen.png\"></a></div>";
		//echo "<div style=\"float:left;\">";
		// affichage des menus
		//require_once(DIMS_APP_PATH . '/modules/wce/admin_content_menu.php');
		//echo "</div>";

		// s�parateur de contenu
		//echo '|||';

		//echo $skin->open_simplebloc('','100%','','');
		//echo '<div id="content'.$_SESSION['dims']['moduleid'].'"	style="background:#ffffff;">';

		switch($block_id) {
			case '1':
				$headingid=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'];
				$heading = new wce_heading();
				$heading->open($headingid);
				include DIMS_APP_PATH . '/modules/wce/admin_heading.php';
				break;
			case '2':
				$articleid=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid'];
				if ($articleid>0) {
					 if (!isset($article->fields['id'])) {
						$article = new wce_article();
						$article->open($articleid);
					 }
					$article->verifVersion();
					$title=$article->fields['title'];
				}
				else {
					if ($op == 'article_addnew') {
						$title="Nouvel article";
					}
				}
				include DIMS_APP_PATH . '/modules/wce/admin_article.php';
			break;
		}
		//echo '</div>';
		//echo $skin->close_simplebloc();

		die();
		break;
	case 'coverflow':
		ob_end_clean();
		$id_object=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id_object>0) {
			header("Content-type: text/xml");
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo "<artworkinfo>";
			$select_object =	"
								SELECT			a.*
								FROM			dims_mod_wce_article as a
								INNER JOIN		dims_mod_wce_object_corresp as c
								ON				a.id=c.id_article
								AND				c.id_object = :id_object";
			$params = array();
			$params[':id_object'] = array('value'=>$id_object,'type'=>PDO::PARAM_INT);
			$result_object = $db->query($select_object,$params);
			$cpte=1;
			$arrayElem = array();

			$total=$db->numrows($result_object);
			while ($fields = $db->fetchrow($result_object)) {
				$path=realpath('.').'/data/articles/'.$fields['picto'];
				$webpath='../data/articles/'.$fields['picto'];

				if ($fields['picto']!='' && file_exists($path)) {
					$elem=array();

					$elem['title'] = utf8_encode($fields['title']);
					$elem['path'] = $webpath;
					if ($fields['url']!='') {
						if (substr($fields['url'],0,4)!='http') {
							$fields['url']="http://".$fields['url'];
						}
						$elem['link']=str_replace("./", $dims->getProtocol().$dims->getHttpHost()."/",$fields['url']);
						$elem['link2'] = $elem['link'];
					}
					else {
						$elem['link'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?headingid=".$fields['id'];
						$elem['link2'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?headingid=".$fields['id'];
					}
					$elem['cpte'] = $cpte;
					$arrayElem[]=$elem;
					$cpte++;
				}
			}

			$select_object =	"
								SELECT			h.*
								FROM			dims_mod_wce_heading as h
								INNER JOIN		dims_mod_wce_object_corresp as c
								ON				h.id=c.id_heading
								AND				c.id_object = :id_object";
			$params = array();
			$params[':id_object'] = array('value'=>$id_object,'type'=>PDO::PARAM_INT);
			$result_object = $db->query($select_object,$params);
			//$cpte=1;

			$total=$db->numrows($result_object);
			while ($fields = $db->fetchrow($result_object)) {
				$path=realpath('.').'/data/headings/'.$fields['picto'];
				$webpath='../data/headings/'.$fields['picto'];

				if ($fields['picto']!='' && file_exists($path)) {
					$elem=array();

					$elem['title'] = utf8_encode($fields['label']);
					$elem['path'] = $webpath;
					if ($fields['url']!='') {
						$elem['link']=str_replace("./", $dims->getProtocol().$dims->getHttpHost()."/",$fields['url']);
						$elem['link2'] = $elem['link'];
					}
					else {
						$elem['link'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?headingid=".$fields['id'];
						$elem['link2'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?headingid=".$fields['id'];
					}

					$elem['cpte'] = $cpte;
					$arrayElem[]=$elem;
					$cpte++;
				}
			}
			$c=0;
			$total=sizeof($arrayElem);
			if ($total<5) $total=5;

			foreach ($arrayElem as $elem) {
				echo  "<albuminfo>
						<artLocation>".$elem['path']."</artLocation>
						<artist>".$elem['title']."</artist>
						<albumName>".$elem['cpte']."/".$total."</albumName>
						<artistLink>".$elem['link']."</artistLink>
						<albumLink>".$elem['link2']."</albumLink>
					</albuminfo>";
				$c++;
			}

			// on complete pour avoir au moins 4 elements
			 if ($c<$total && sizeof($arrayElem)>0) {
				while ($c<$total) {
					foreach ($arrayElem as $elem) {
						if ($c<$total) {
							$c++;
							echo  "<albuminfo>
									<artLocation>".$elem['path']."</artLocation>
									<artist>".$elem['title']."</artist>
									<albumName>".$c."/".$total."</albumName>
									<artistLink>".$elem['link']."</artistLink>
									<albumLink>".$elem['link2']."</albumLink>
								</albuminfo>";
						}
					}
				}
			}
			echo "</artworkinfo>";
		}
		die();
		break;
	case 'choice_heading':
		ob_end_clean();
		ob_start();
		require_once(DIMS_APP_PATH.'/templates/backoffice/'.$_SESSION['dims']['template_name']."/class_skin.php");
		$skin=new skin();
		echo $skin->open_simplebloc("");
		echo "<div style=\"background-color:#FFFFFF;width:100%;height:550px;overflow:auto;\">";
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			$headings = wce_getheadings();

			$selectheadings = array();
			if (isset($_GET['selectheadings'])) {
				$selectheadings = explode(",", dims_load_securvalue('selectheadings', dims_const::_DIMS_CHAR_INPUT, true, true, true));
			}

			if (isset($_GET['currentheading'])) {
				$currentheading = dims_load_securvalue('currentheading', dims_const::_DIMS_NUM_INPUT, true, true, true);
				echo wce_build_tree($headings,	array(), 0, '', 1, 'selectheading', 0,0,array($currentheading),array(),true);
			} else {
				echo _DIMS_ERROR;
			}

			echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
					<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';document.getElementById('op').selectedIndex=0;\" value=\"".$_DIMS['cste']['_DIMS_LABEL_CANCEL']."\" class=\"flatbutton\"/>
					</div>";
			echo "</div>";
		echo $skin->close_simplebloc();
		die();
		break;
	case 'reset_currentobject':
		if (isset($_SESSION['dims']['current_object'])) {
			unset($_SESSION['dims']['current_object']);
		}
		break;
	case 'object_detail_properties':
	case 'preview':
		// generation du contenu par unoconv
		if ($idobject==_WCE_OBJECT_ARTICLE) {
			$obj=new wce_article();
			$obj->open($idrecord);
			$articleid=$idrecord;//$this->fields['id'];

			echo "<iframe src=\"".$obj->getRootPath()."/index.php?articleid=".$obj->fields['id']."\" style=\"border: 0pt none ; margin: 0pt; padding: 0pt; width: 100%; height: 600px;\"></iframe>";
			die();
		}
	break;
	case 'object_properties':
	case 'refreshDesktop':

		$moduleid=$_SESSION['dims']['current_object']['id_module'];
		$objectid=$_SESSION['dims']['current_object']['id_object'];
		$recordid=$_SESSION['dims']['current_object']['id_record'];

		if($objectid==_WCE_OBJECT_ARTICLE) {
			$obj=new wce_article();
			$obj->open($idrecord);
			$_SESSION['dims']['current_object']['label']=$obj->fields['title'];
			$_SESSION['dims']['current_object']['id_workspace']=$obj->fields['id_workspace'];
			$_SESSION['dims']['current_object']['id_user']=$obj->fields['id_user'];
			$_SESSION['dims']['current_object']['timestp_modify']=$obj->fields['timestp_modify'];

			$_SESSION['dims']['current_object']['cmd']=array();

			$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
			$elem['src']="./common/img/view.png";
			$elem['link']= dims_urlencode("admin.php?dims_moduleid={$obj->fields['id_module']}&dims_desktop=block&dims_action=public&wce_mode=render&articleid=$recordid");
			$_SESSION['dims']['current_object']['cmd'][]=$elem;
		}
	break;
	case 'wce_getdimsobjects':
		//ob_start();
		echo "<script type=\"text/javascript\" src=\"./include/prototype.js\"></script>
		<script type=\"text/javascript\" src=\"./include/scriptaculous.min.js\"></script>
		<script type=\"text/javascript\" src=\"./js/portal_v5.js\"></script>";
		?>
		<script language="javascript">

		var oEditor = window.parent.InnerDialogLoaded() ;
		var FCKLang = oEditor.FCKLang ;
		var FCKPlaceholders = oEditor.FCKPlaceholders ;
		var eSelected ;

		window.onload = function () {
			// First of all, translate the dialog box texts
			oEditor.FCKLanguageManager.TranslatePage( document ) ;

			//var eSelected = oEditor.FCKSelection.GetSelectedElement();
			if (oEditor.elemSelected!=null)
				eSelected=oEditor.elemSelected;

			LoadSelected() ;

			// Show the "Ok" button.
			window.parent.SetOkButton( true ) ;


		}

		function LoadSelected() {
			if (!eSelected) {
				return ;
			}
			var info = eSelected._fckplaceholder.split("/");
			var sValue = info[0];
			var detail = sValue.split(",");
			moduleid=detail[1];
			if ( eSelected.tagName == 'SPAN' && eSelected._fckplaceholder )
			{
				var obj = document.getElementById('dims_wce_modules');
				for (i=0;i<obj.length;i++) {
					if (obj[i].value == moduleid) {
						obj.selectedIndex = i;
					}
				}

				if (obj.selectedIndex>=0) {
					refresh_selectedModule(sValue);
				}
			}
			else
				eSelected == null ;

		}

		function Ok()
		{
			var obj = document.getElementById('dims_wce_objects');

			var sValue = obj[obj.selectedIndex].value+'/'+obj[obj.selectedIndex].text ;

			if ( eSelected && eSelected._fckplaceholder == sValue )
				return true ;

			if ( sValue.length == 0 )
			{
				alert( FCKLang.PlaceholderErrNoName ) ;
				return false ;
			}

			if ( FCKPlaceholders.Exist( sValue ) )
			{
				alert('<? echo $_DIMS['cste']['_WCE_OBJECT_ALREADY_EXISTS']; ?>');
				return false ;
			}

			FCKPlaceholders.Add( sValue ) ;
			return true ;
		}

		function refresh_selectedModule(detail) {
			var obj=document.getElementById("dims_wce_modules");
			var sValue = obj[obj.selectedIndex].value;

			if (sValue>=0) {
				dims_xmlhttprequest_todiv('admin.php','dims_op=refresh_select_moduletype&id_module='+sValue+'&selobj='+detail,'','content');
			}
		}
		</script>

		<div style="padding: 4px 0;"><? echo $_DIMS['cste']['_WCE_INSERT_MODULE_CHOICE']; ?>
			<?
			$select_object =	"
			SELECT		distinct dims_module.label as module_label, dims_module.id as module_id
			FROM		dims_mb_wce_object
			INNER JOIN	dims_module
			ON			dims_mb_wce_object.id_module_type = dims_module.id_module_type
			INNER JOIN	dims_module_workspace
			ON			dims_module_workspace.id_module = dims_module.id
			AND			dims_module_workspace.id_workspace = :id_workspace";
			$params = array();
			$params[':id_workspace'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);

			$result_object = $db->query($select_object,$params);
			while ($fields_object = $db->fetchrow($result_object)) {
				$array_modules[$fields_object['module_id']] = $fields_object;
			}

			// on ajoute les modules systemes
			$select_object =	"
			SELECT		distinct dims_module.label as module_label, dims_module.id as module_id
			FROM		dims_mb_wce_object
			INNER JOIN	dims_module
			ON			dims_mb_wce_object.id_module_type = dims_module.id_module_type
			AND			dims_mb_wce_object.id_module_type=1";

			$result_object = $db->query($select_object);
			while ($fields_object = $db->fetchrow($result_object)) {
				$array_modules[$fields_object['module_id']] = $fields_object;
			}
			?>
		<select id="dims_wce_modules"
			onchange="javascript:refresh_selectedModule();" style="width: 100%;">
			<option value="0">(aucun)</option>
			<?
			foreach($array_modules as $key => $value)
			{
				if ($fields_column['id_object'] == $key) $sel = 'selected';
				//else $sel = '';
				$sel = '';
			?>
			<option <? echo $sel; ?> value="<? echo $key; ?>"><? echo "{$value['module_label']}"; ?></option>
			<?
		}
		?>
		</select>
		</div>
		<div id="content" style="width: 100%"></div>
		<?
		/*
		$main_content = ob_get_contents();
		@ob_end_clean();

		$template_body->assign_vars(array(
					'TEMPLATE_PATH'			=> $_SESSION['dims']['template_path'],
					'ADDITIONAL_JAVASCRIPT' => $additional_javascript,
					'PAGE_CONTENT'			=> $main_content
		)
		);

		$template_body->pparse('body');
		*/

		die();
	break;
	case 'wce_getdimsobjects2':
		ob_clean();
		$array_modules = array();
		$select_object =	"
		SELECT		distinct dims_module.label as module_label, dims_module.id as module_id
		FROM		dims_mb_wce_object
		INNER JOIN	dims_module
		ON			dims_mb_wce_object.id_module_type = dims_module.id_module_type
		INNER JOIN	dims_module_workspace
		ON			dims_module_workspace.id_module = dims_module.id
		AND			dims_module_workspace.id_workspace = :id_workspace";
		$params = array();
		$params[':id_workspace'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);

		$result_object = $db->query($select_object,$params);
		while ($fields_object = $db->fetchrow($result_object)) {
			$array_modules[$fields_object['module_id']] = $fields_object['module_label'];
		}

		// on ajoute les modules systemes
		$select_object =	"
		SELECT		distinct dims_module.label as module_label, dims_module.id as module_id
		FROM		dims_mb_wce_object
		INNER JOIN	dims_module
		ON			dims_mb_wce_object.id_module_type = dims_module.id_module_type
		AND			dims_mb_wce_object.id_module_type=1";

		$result_object = $db->query($select_object);
		while ($fields_object = $db->fetchrow($result_object)) {
			$array_modules[$fields_object['module_id']] = $fields_object['module_label'];
		}
		$res = array();
		$elem = array();
		$elem[] = "(aucun)";
		$elem[] = "0";
		$res[] = $elem;
		foreach($array_modules as $k => $v){
			$elem = array();
			$elem[] = $v;
			$elem[] = "$k";
			$res[] = $elem;
		}
		echo json_encode($res);
		die();
		break;
	case 'refresh_select_moduletype2':
		ob_clean();
		$id_module = dims_load_securvalue('id_module',dims_const::_DIMS_NUM_INPUT,true,true);

		$res = array();
		$elem = array();
		$elem[] = "(aucun)";
		$elem[] = "0";
		$res[] = $elem;

		if(!empty($id_module)){
			$select_object =	"
								SELECT	dims_mb_wce_object.*, dims_module.label as module_label, dims_module.id as module_id
								FROM	dims_mb_wce_object, dims_module
								WHERE	dims_mb_wce_object.id_module_type = dims_module.id_module_type
								AND		dims_module.id = :id_module";
			$params = array();
			$params[':id_module'] = array('value'=>$id_module,'type'=>PDO::PARAM_INT);

			$result_object = $db->query($select_object,$params);
			$array_modules = array();
			while ($fields_object = $db->fetchrow($result_object)) {
				if ($fields_object['select_label'] != ''){
					$select = "	SELECT	{$fields_object['select_id']}, {$fields_object['select_label']}
								FROM	{$fields_object['select_table']}
								WHERE	id_module = :id_module";
					$params = array();
					$params[':id_module'] = array('value'=>$fields_object['module_id'],'type'=>PDO::PARAM_INT);

					// test si condition suppl�mentaire
					if (isset($fields_object['select_params']) && $fields_object['select_params']!="" ) {
						$select .=" AND ".$fields_object['select_params']; // TODO : c'est moche pr la gestion pdo
					}

					$resu=$db->query($select,$params);

					while ($fields = $db->fetchrow($resu)) {
						$fields_object['object_label'] = $fields[$fields_object['select_label']];
						$array_modules["{$fields_object['id']},{$fields_object['module_id']},{$fields[$fields_object['select_id']]}/{$fields_object['module_label']} > {$fields_object['label']} > {$fields_object['object_label']}"] = $fields_object['label']." > ".$fields_object['object_label'];
					}
				}
				else $array_modules["{$fields_object['id']},{$fields_object['module_id']}/{$fields_object['module_label']} > {$fields_object['label']}"] = $fields_object['label'];
			}

			foreach($array_modules as $k => $v){
				$elem = array();
				$elem[] = $v;
				$elem[] = "$k";
				$res[] = $elem;
			}
		}
		echo json_encode($res);
		die();
		break;
	case 'wce_selectlink':
	case 'wce_detail_heading';
		echo "<script type=\"text/javascript\" src=\"./include/prototype.js\"></script>
		<script type=\"text/javascript\" src=\"./include/scriptaculous.js\"></script>
		<script type=\"text/javascript\" src=\"./include/portal.js\"></script>";

		ob_start();
		echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".$_SESSION['dims']['template_path']."/css/main.css\" media=\"screen\" title=\"styles\"/>";
		echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"./common/modules/wce/include/styles.css\" media=\"screen\" title=\"styles\"/>";
		require_once DIMS_APP_PATH . '/modules/wce/fck_link.php';
		$main_content = ob_get_contents();
		@ob_end_clean();
		echo $main_content;
		die();
		break;
	case 'modify_desc_article':
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$articleid=dims_load_securvalue('article_id',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($articleid>0) {
				// save domainid into article
				$article = new wce_article();
				if ($articleid>0) {
					// test de s&eacute;curit&eacute; si article appartient &eacute; l'utilisateur
					$article->open($articleid);
					require_once DIMS_APP_PATH.'modules/wce/admin_article_description_form.php';
				}
			}
		}
		die();
		break;




// ----------------------- À partir d'ici nouveaux op du WCE -----------------------
	case 'templates_view_wce':
		ob_clean();
		require_once module_wce::getTemplatePath("parameters/conf_avancee/edit_template.tpl.php");
		die();
		break;
	case 'article_select_link_article':
	case 'selectredirectheading':
	case 'selectlinkarticle':
		ob_clean();
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0))) {
			echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"./common/modules/wce/include/styles.css\" media=\"screen\" title=\"styles\" />";
			?>
			<div style="padding:4px;background-color:#d0d0d0;border-bottom:1px solid #c0c0c0;font-weight:bold;">Choix d'une page</div>
			<div style="padding:4px;background-color:#FFFFFF;height:150px;overflow:auto;">
			<?
			$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$headings = wce_getheadings();
			$articles = wce_getarticles();
			$input_id = dims_load_securvalue('input',dims_const::_DIMS_CHAR_INPUT,true,true,false);
			$input_display = dims_load_securvalue('display',dims_const::_DIMS_CHAR_INPUT,true,true,false);
			if( empty($input_id) ){
				$input_id = 'wce_article_id_article_link';
			}
			if( empty($input_display) ){
				$input_display = 'linkedpage_displayed';
			}

			echo wce_build_tree($headings, $articles, 0, '', 1, $dims_op, $headingid,$articleid, array(),array(),false, $input_id, $input_display);
			?>
			</div>
			<div style="padding:4px;background-color:#d0d0d0;border-top:1px solid #c0c0c0;text-align:right;">
				<a href="javascript:dims_hidepopup();"><? echo $_SESSION['cste']['_DIMS_CLOSE']; ?></a>
			</div>
			<?
		}
		die();
		break;
	case 'switch_display_arborescence':
		ob_clean();
		$_SESSION['wce_display_tree'] = !$_SESSION['wce_display_tree'];
		die();
		break;
	case 'switch_display_menu':
		ob_clean();
		$_SESSION['wce_display_menu'] = !$_SESSION['wce_display_menu'];
		die();
		break;
	case 'properties_article':
		ob_clean();
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$article = new wce_article();
		if ($id != '' && $id > 0){
			$article->open($id);
			echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY'],'100%','','');
		}else {
			$article->init_description();
			$article->fields['id_heading'] = dims_load_securvalue('id_heading',dims_const::_DIMS_NUM_INPUT,true,true);

			// recherche du modele par defaut
			$work = new workspace();
			$work->open($_SESSION['dims']['workspaceid']);
			if (isset($work->fields['page_default_template']) && $work->fields['page_default_template']!="") {
				$article->fields['model'] =$work->fields['page_default_template'];
			}
			else
				$article->fields['model'] = module_wce::DEFAULT_MODELE;
			echo $skin->open_simplebloc($_DIMS['cste']['_ADD'],'100%','','');
		}
		$article->display(module_wce::getTemplatePath('/common/properties_article.php'));
		echo $skin->close_simplebloc();
		die();
		break;
	case 'get_stats_consultations':
		ob_clean();
		$db = dims::getInstance()->db;
		$values = array();
		$d = date('j');
		// initialisation
		for($i=1;$i<=date('t');$i++){
			$elem = array();
			$elem[0] = 0;
			$elem[1] = 0;
			$values['datas'][$i] = $elem;
			$values['legende'][] = $i;
			if($i <= $d)
				$values['datas2'][$i] = 0;
			else
				$values['datas2'][$i] = -1;
		}

		// pages consultés (pas WIKI)
		$sel = "SELECT		m.timestp, SUM(m.meter) as meter
				FROM		".wce_article::TABLE_NAME." a
				INNER JOIN	dims_mod_wce_article_meter m
				ON			m.id_article = a.id
				WHERE		m.timestp >= :timestp
				AND			a.type = 0
				AND			a.id_module = :id_module
				GROUP BY	m.timestp
				ORDER BY	m.timestp DESC";
		$params = array();
		$params[':id_module'] = array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT);
		$params[':timestp'] = array('value'=>date('Ym00000000'),'type'=>PDO::PARAM_INT);
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$tmp = intval(substr($r['timestp'],6,2));
			if (isset($values['datas'][$tmp])){
				$values['datas'][$tmp][0] = intval($r['meter']);
			}
			if (isset($values['datas2'][$tmp])){
				$values['datas2'][$tmp] = intval($r['meter']);
			}
		}

		// utilisateurs
		$sel = "SELECT		timestp, COUNT(sid) as meter
				FROM		dims_mod_wce_article_visite
				WHERE		timestp >= :timestp
				AND			id_module = :id_module
				GROUP BY	timestp
				ORDER BY	timestp DESC";
		$params = array();
		$params[':id_module'] = array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT);
		$params[':timestp'] = array('value'=>date('Ym00000000'),'type'=>PDO::PARAM_INT);
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$tmp = intval(substr($r['timestp'],6,2));
			if (isset($values['datas'][$tmp])){
				$values['datas'][$tmp][1] = intval($r['meter']);
			}
		}
		$values['datas'] = array_values($values['datas']);
		$values['datas2'] = array_values($values['datas2']);
		echo json_encode($values);
		die();
		break;
	case 'load_thumbnail_site':
		ob_clean();
		$thumb = 'data/thumbnail/wce/thumbnail_'.$_SESSION['dims']['moduleid'].'.png';
		$thumbServ = realpath(".")."/$thumb";
		if (!file_exists($thumbServ)){
			if(!file_exists(realpath(".").'/data/thumbnail/wce'))
				dims_makedir(realpath(".")."/data/thumbnail/wce");
			$work = new workspace();
			$work->open($_SESSION['dims']['workspaceid']);
			$domain = current($work->getFrontDomains());
			if ($domain['ssl'])
				$lk = "https://".$domain['domain']."/index.php";
			else
				$lk = "http://".$domain['domain']."/index.php";
			exec('xvfb-run --server-args="-screen 0, 1024x768x24" cutycapt --url='.$lk.' --out='.$thumbServ);
			ob_clean();
			if (file_exists(DIMS_APP_PATH . '/'.$thumb))
				echo $thumb;
		}elseif(date('YmdHis',mktime(date('H'),date('i')-30)) > date('YmdHis',filemtime($thumbServ))){
			$work = new workspace();
			$work->open($_SESSION['dims']['workspaceid']);
			$domain = current($work->getFrontDomains());
			if ($domain['ssl'])
				$lk = "https://".$domain['domain']."/index.php";
			else
				$lk = "http://".$domain['domain']."/index.php";
			exec('xvfb-run --server-args="-screen 0, 1024x768x24" cutycapt --url='.$lk.' --out='.$thumbServ);
			ob_clean();
			if (file_exists(DIMS_APP_PATH . '/'.$thumb))
				echo $thumb;
		}else{
			echo $thumb;
		}
		die();
		break;
	case 'getAjaxEditInfoBlock':
		ob_clean();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id);
			// construction du formulaire d'edition de changement de valeur
			$block->display(module_wce::getTemplatePath('/common/block/admin_article_block_form_smalledit.php'));
		}
		die();
		break;

	case 'alerts_rss':
		require_once DIMS_APP_PATH.'/templates/objects/alertes/AlertesController.php';
		$smarty = new Smarty();

		if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
			$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

		$smartypath=$_SESSION['dims']['smarty_path'];
		$smarty->cache_dir = $smartypath.'/cache';
		$smarty->config_dir = $smartypath.'/configs';
		$controller = new AlertesController(null, $smarty);
		$template='alertes_rss';

		if (!file_exists($smartypath.'/templates_c/alertes/'.$template)) {
			dims_makedir ($smartypath."/templates_c/alertes/".$template.'/', 0777, true);
		}
		$smarty->compile_dir = $smartypath."/templates_c/alertes/".$template.'/';

		$controller->addParam('mode', 'rss');
		$path = $controller->buildIHM();

		header("Content-type: text/xml");
		if( ! is_null($path) ){
			ob_start();
			$smarty->display('file:'.$path);
			$contentcms .= ob_get_contents();
			ob_end_clean();
		}
		if (!isset($wce_site)) {
			$wce_site = new wce_site($db,$_SESSION['dims']['wcemoduleid']);
		}
		echo $wce_site->replaceUrlContent($contentcms);
		die();
		break;

	case 'news_rss':
		require_once DIMS_APP_PATH.'/templates/objects/news/NewsController.php';
		$smarty = new Smarty();

		if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
			$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

		$smartypath=$_SESSION['dims']['smarty_path'];
		$smarty->cache_dir = $smartypath.'/cache';
		$smarty->config_dir = $smartypath.'/configs';
		$controller = new NewsController(null, $smarty);
		$template='news_rss';

		if (!file_exists($smartypath.'/templates_c/news/'.$template)) {
			dims_makedir ($smartypath."/templates_c/news/".$template.'/', 0777, true);
		}
		$smarty->compile_dir = $smartypath."/templates_c/news/".$template.'/';

		$controller->addParam('mode', 'rss');
		$path = $controller->buildIHM();

		header("Content-type: text/xml");
		if( ! is_null($path) ){
			ob_start();
			$smarty->display('file:'.$path);
			$contentcms .= ob_get_contents();
			ob_end_clean();
		}
		if (!isset($wce_site)) {
			$wce_site = new wce_site($db,$_SESSION['dims']['wcemoduleid']);
		}
		echo $wce_site->replaceUrlContent($contentcms);
		die();
		break;
	case 'planning_rss':
		require_once DIMS_APP_PATH.'/templates/objects/planning/PlanningController.php';
		$smarty = new Smarty();

		if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
			$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

		$smartypath=$_SESSION['dims']['smarty_path'];
		$smarty->cache_dir = $smartypath.'/cache';
		$smarty->config_dir = $smartypath.'/configs';
		$controller = new PlanningController(null, $smarty);
		$template='planning_rss';

		if (!file_exists($smartypath.'/templates_c/planning/'.$template)) {
			dims_makedir ($smartypath."/templates_c/planning/".$template.'/', 0777, true);
		}
		$smarty->compile_dir = $smartypath."/templates_c/planning/".$template.'/';

		$controller->addParam('mode', 'rss');
		$path = $controller->buildIHM();

		header("Content-type: text/xml");
		if( ! is_null($path) ){
			ob_start();
			$smarty->display('file:'.$path);
			$contentcms .= ob_get_contents();
			ob_end_clean();
		}
		if (!isset($wce_site)) {
			$wce_site = new wce_site($db,$_SESSION['dims']['wcemoduleid']);
		}
		echo $wce_site->replaceUrlContent($contentcms);
		die();
		break;
	case 'video_room':
		ob_clean();
		$id=dims_load_securvalue('id_video',dims_const::_DIMS_NUM_INPUT,true,true);
		$width=dims_load_securvalue('width',dims_const::_DIMS_NUM_INPUT,true,true);
		$height=dims_load_securvalue('height',dims_const::_DIMS_NUM_INPUT,true,true);
		$tpl=dims_load_securvalue('tpl',dims_const::_DIMS_CHAR_INPUT,true,true);
		$color=dims_load_securvalue('color',dims_const::_DIMS_NUM_INPUT,true,true);
		if(!empty($id)){
			$doc = new docfile();
			$doc->open($id);
			$doc->fields['width'] = $width;
			$doc->fields['height'] = $height;
			$doc->fields['tpl'] = $tpl;
			$doc->fields['color'] = "#".$color;
			if(!$doc->isNew() && in_array(strtolower($doc->fields['extension']),array('mp4','ogv','webm')) && $doc->fields['id_workspace'] == $_SESSION['dims']['workspaceid']) {
				$doc->display(DIMS_APP_PATH."modules/wce/wce/common/video.tpl.php"); //--> Mettre le bon chemin
			}
		}
		die();
		break;
	case 'sel_article_wce':
		ob_clean();
		$sel = "SELECT		distinct a.*
				FROM		".wce_article::TABLE_NAME." a
				INNER JOIN	".wce_heading::TABLE_NAME." h
				ON			a.id_heading = h.id
				WHERE		h.type != 1
				AND			a.id_module = :id_module
				AND			a.id_workspace = :id_workspace
				AND		a.id_lang = :id_lang
				GROUP BY	a.id
				ORDER BY	a.title";
		$params = array();
		$params[':id_module'] = array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT);
		$params[':id_workspace'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$_SESSION['dims']['currentlang'],'type'=>PDO::PARAM_INT);
		$db = dims::getInstance()->getDb();
		$lst = array();
		$elem = array();
		$elem[] = "(Aucun)";
		$elem[] = "0";
		$lst[] = $elem;
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$elem = array();
			$elem[] = $r['title'];
			$elem[] = $r['id'];
			$lst[] = $elem;
		}
		echo json_encode($lst);
		die();
		break;

	case 'sel_section_wce':
		ob_clean();
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$lst = array();
		$elem = array();
		$elem[] = "(Aucun)";
		$elem[] = "0";
		$elem[] = "1";
		$elem[] = "1";
		$lst[] = $elem;
		if ($id != '' && $id > 0){
			$sel = "SELECT		title, id, section, page_break
					FROM		dims_mod_wce_article_block
					WHERE		id_article = :id_article
					ORDER BY	level, position";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,array(':id_article'=>array('value'=>$id,'type'=>PDO::PARAM_INT)));
			$i = 1;
			while($r = $db->fetchrow($res)){
				if($r['page_break']) $i ++;
				$elem = array();
				$elem[] = $r['title'];
				$elem[] = $r['id'];
				$elem[] = $r['section'];
				$elem[] = $i;
				$lst[] = $elem;
			}
		}
		echo json_encode($lst);
		die();
		break;
	case 'get_tags':
		ob_clean();
		require_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$mode = dims_load_securvalue('mode',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if ( !empty($mode) && $mode == 'complexe'){
			//dans ce cas le script javascript doit jouer avec des ids et les chaînes
			$tags = tag::getAllTags(true, '', ' WHERE id_workspace = '.$_SESSION['dims']['workspaceid']);
			$data = array();
			foreach($tags as $t){
				$data['availableTags'][] = $t->fields['tag'];
				$data['ids'][$t->fields['tag']] = $t->getId();
			}
			echo json_encode($data);
		}
		else echo json_encode(tag::getAllTags(false, '', ' WHERE id_workspace = '.$_SESSION['dims']['workspaceid']));
		die();
		break;
}
