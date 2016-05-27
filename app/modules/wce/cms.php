<?
dims_init_module('wce');
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_object.php';

if(!isset($op)) $op = '';

global $template_name;
global $field_formats; // from forms/include/global.php
global $field_operators; // from forms/include/global.php
global $nav;
global $idpage;
global $op2;
global $forms_id;

global $articleid;
global $headingid;

if (isset($op2) && !empty($op2)) $op = $op2;

switch($op) {
	case 'sitemap':
		require_once(DIMS_APP_PATH . "/modules/wce/cms_sitestructure.php");
		break;
	case 'site_news':
		$today = dims_createtimestamp();

		// mode standard
		$select_object =	"
							SELECT		a.*
							FROM		dims_mod_wce_article as a
							WHERE		a.actu=1
							AND			a.id_module = :id_module
							AND			(a.timestp_published <= :timestp_published OR a.timestp_published = 0)
							AND			(a.timestp_unpublished >= :timestp_unpublished OR a.timestp_unpublished = 0)
							ORDER BY	a.timestp_published  DESC";

		$result_object = $db->query($select_object,
									array(':id_module'=>array('value'=>$_SESSION['dims']['wce_module_id'],'type'=>PDO::PARAM_INT),
									':timestp_published'=>array('value'=>$today,'type'=>PDO::PARAM_INT),
									':timestp_unpublished'=>array('value'=>$today,'type'=>PDO::PARAM_INT)));
		$cpte=1;
		$elemFirst=array();
		$arrayElem = array();
		$earlydate='';

		// variable pour le multi pages
		$maxelem=5;
		$maxblock=10;
		$indicesel=0;

		if (!isset($wce_site)) {
			$wce_site = new wce_site($db,$_SESSION['dims']['wcemoduleid']);
		}

		$actu_sel=dims_load_securvalue('actu_sel',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if (!isset($_SESSION['wce']['actu_page_sel'])) {
			$_SESSION['wce']['actu_page_sel']=1;
		}

		$actu_page_sel=dims_load_securvalue('actu_page_sel',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($actu_page_sel>0) {
			$_SESSION['wce']['actu_page_sel']=$actu_page_sel;

		}

		// on a une page courante selectionnee, on reinit la selection
		if ($actu_page_sel>0) {
			$actu_sel=0; // on reinit car on change de page donc on reprend le premier element de la nouvelle liste que l'on vient de creer
			$indicesel=$maxelem*($_SESSION['wce']['actu_page_sel']-1)+1;
		}

		$total=$db->numrows($result_object);
		$ind=1;

		while ($fields = $db->fetchrow($result_object)) {
			if ($fields['timestp_published']>$earlydate) {
				$earlydate=$fields['timestp_published'];
			}
			$path=realpath('.').'/data/articles/'.$fields['picto'];
			$webpath=$dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];

			if ($fields['picto']!='' && file_exists($path)) {

				$ext = explode('.', $fields['picto']);
				$ext = strtolower($ext[count($ext)-1]);
				$webpathactu=$dims->getProtocol().$dims->getHttpHost().'/data/articles/art_'.$fields['id']."_500.".$ext;

				$elem=array();
				$elem['id'] = $fields['id'];
				$elem['title'] = $fields['title'];
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

				// calcul si on est sur le bon element
				if ($_SESSION['wce']['actu_page_sel']>0) {

					if ($indicesel==$ind) {
						$actu_sel=$fields['id']; // on a trouve notre premier element
					}
				}

				if ($fields['first_page'] && $actu_sel==0 ) {
					$elemFirst=$elem;
				}
				else {
					if ($actu_sel==$fields['id']) {
						$elemFirst=$elem;
					}
					else {

					}
				}
				$arrayElem[]=$elem;
				$cpte++;
				$ind++;
			}
		}

		// on met eventuellement a jour l'article portant le module si la date est <
		if (isset($_SESSION['dims']['currentarticleid']) && $_SESSION['dims']['currentarticleid']>0) {
			if (!isset($article)) {
				$article = new wce_article();
				$article->open($articleid);
			}

			if ($article->fields['timestp_published']<$earlydate) {
				$article->fields['timestp_published']=$earlydate;
				$article->save();
			}
		}

		$c=0;
		$width="100%";
		$contentcms='';

		if (!empty($elemFirst)) {
			$width="35%";

			$contentcms.='<div style="float:left;width:63%;margin-right:5px;">
			<span style="wifth:100%;text-align:center;">
			<img alt="" src="'.$elemFirst['pathactu'].'"/>
				</span>
				<span style="width:100%;">
				<p><span class="texte" style="font-weight:bold;text-align:justify;font-size:16px;color:#0000EE;">'.$elemFirst['title'].'<br></span><span style="font-weight:normal;font-style:italic;color:#454545;">'.$elemFirst['description'].'</span></p>
				<p class="savoirplus" style="text-align: right;"><a href="'.$elemFirst['link'].'" '.$elemFirst['target'].'>En savoir plus</a></p>';
			$contentcms.='</span></div>';
		}

		/*if ($_SERVER['QUERY_STRING']!="") {
			$ch="?".$_SERVER['QUERY_STRING'];
		}
		else {
			$ch="";
		}*/
		$ch='active=1';
		$cpte=1;

		$nbelem=sizeof($arrayElem);

		$nbblock=0;
		$block ="";
		$deb=0+($_SESSION['wce']['actu_page_sel']-1)*$maxelem;
		$fin=$deb+$maxelem;

		if ($nbelem>$maxelem) {
				$block .= "<div style=\"float: left; width:100%;text-align:center;margin-top:10px;\">";
				$nbblock=$nbelem/$maxelem;
				if ($nbelem%$maxelem>0) $nbblock++;
		}

		// on boucle sur les blocs pour afficher le multi page
		$selblock="";

		for($b=1;$b<=$nbblock;$b++) {
			if ($b<=$maxblock) { // limite de bloc
				if ($b==($_SESSION['wce']['actu_page_sel'])) {
					$selblock="background-color:#EFEFEF;";
				}
				else {
					$selblock="";
				}
				$block .= "<a style=\"border:dotted 1px #6E6E6E;padding:2px;".$selblock."\" href=\"index.php?actu_page_sel=".($b)."\">".$b."</a>&nbsp;";
			}
		}

		if ($nbelem>$maxelem) {
				$block .= "</div>";
		}

		if ($nbelem>0) {
			$contentcms.='<div style="float:right;width:'.$width.'">';
						$contentcms.=$block;
			foreach ($arrayElem as $k=>$elem) {
							if ($k>=$deb && $k<$fin) {
				if ($elem['first_page']==0) {
					$contentcms.=' <div style="float:left;width:100%;border-bottom:1px solid #DDDDDD;clear:both;">
						<p class="texte" style="float:left;margin-right:5px;"><img alt="" src="'.$elem['path'].'" width="80"/></p>
						<p class="texte" style="font-size:11px;font-weight:bold;text-align:justify;"><a href="'.$dims->getProtocol().$dims->getHttpHost().'/index.php?'.$ch.'&actu_sel='.$elem['id'].'">'.$elem['title'].'</a><br><font style="font-weight:normal;font-style:italic;color:#454545;">'.dims_strcut(strtolower(strip_tags($elem['description'])),100).'</font></p>
						<p class="savoirplus" style="text-align: right;"><a href="'.$elem['link'].'" '.$elem['target'].'>En savoir plus</a></p>
						</div>';
					$c++;
				}
							}
			}
						$contentcms.=$block;
			$contentcms.='</div>';
		}

		echo $wce_site->replaceUrlContent($contentcms);
		break;
	 case 'slideshow':
		require_once DIMS_APP_PATH.'modules/wce/include/classes/class_slideshow.php';
		require_once DIMS_APP_PATH.'modules/wce/include/classes/class_slideshow_element.php';
		require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
		$slideshow = new wce_slideshow();
		$slideshow->open($obj['object_id']);

		$slide_tab['id']			= $slideshow->fields['id'];
		$slide_tab['nom']			= $slideshow->fields['nom'];
		$slide_tab['description']	= $slideshow->fields['description'];
		$slide_tab['template']		= $slideshow->fields['template'];
		$slide_tab['color']			= substr($slideshow->fields['color'],1);

		$readyToDisplay = false;

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
		$template_dir = DIMS_APP_PATH."templates/objects/slideshows/";
		$smartyobject->template_dir = $template_dir;

		$smartyobject->assign('slideshow',$slide_tab);

		if (file_exists($template_dir.$template_name.'.tpl')) {
			if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
			$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

			$smartyobject->display($template_name.'.tpl');
		}
		else echo 'ERREUR : '.$template_name.'.tpl manquant !';
		break;
	case 'newsletter_manage':
		$id_object		= $obj['object_id'];
		$wce_module_id	= $obj['module_id'];
		include DIMS_APP_PATH.'modules/wce/cms_newsletter_manage.php';
		break;
	case 'display_tags':
		include DIMS_APP_PATH.'modules/wce/cms_display_tags.php';
		break;

	case 'control_dynobject':
		$object = new wce_object();
		$object->open($obj['object_id']);
		require_once DIMS_APP_PATH.'/templates/objects/'.$object->fields['template'].'/'.ucfirst($object->fields['template']).'Controller.php';
		$smarty = new Smarty();

		if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
			$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

		$smartypath=$_SESSION['dims']['smarty_path'];
		$smarty->cache_dir = $smartypath.'/cache';
		$smarty->config_dir = $smartypath.'/configs';
		$class_name = ucfirst($object->fields['template']).'Controller';
		$controller = new $class_name;
		$controller->setObject($object);
		$controller->setSmartyReference($smarty);

		$template=$controller->object->fields['template'].'_full';

		if (!file_exists($smartypath.'/templates_c/'.$template_name.'/'.$template)) {
			dims_makedir ($smartypath."/templates_c/".$template_name."/".$template.'/', 0777, true);
		}
		$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/".$template.'/';

		if(!empty($obj['params'])){
			foreach($obj['params'] as $k => $p){
				$controller->addParam($k, $p);
			}
		}else
			$controller->addParam('mode', 'full_index');
		$path = $controller->buildIHM();

		if( ! is_null($path) ){
			$smarty->display('file:'.$path);
		}
		break;

	case 'menu_libre':
		if(!empty($obj['object_id'])){
			$root = wce_heading::find_by(array('id' => $obj['object_id']), null, 1);
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
							$smarty = new Smarty();
							if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='')
								$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";

							$smartypath=$_SESSION['dims']['smarty_path'];
							$smarty->cache_dir = $smartypath.'/cache';
							$smarty->config_dir = $smartypath.'/configs';

							if (!file_exists($smartypath.'/templates_c/'.$root->get('template').'/'.$root->fields['freetemplate'])) {
								dims_makedir ($smartypath."/templates_c/".$root->get('template')."/".$root->fields['freetemplate'].'/', 0777, true);
							}
							$smarty->compile_dir = $smartypath."/templates_c/".$root->get('template')."/".$root->fields['freetemplate'].'/';
							$smarty->assign('freeroot', $data);
							$view->assign('freeroot', $data);
							$smarty->display('file:'.$path);
						}
					}
				}
			}
		}
		break;
}

?>
