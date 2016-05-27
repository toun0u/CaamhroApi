<?php
include_once DIMS_APP_PATH.'/modules/wce/include/classes/class_dynobject.php';
class Flash_accueilController extends DynObject{
	public function buildIHM(){
		if( ! is_null($this->smarty)){

			switch($this->getParam('mode')){
				default:
				case 'home':
					$this->buildView();
					$path = $this->getParam('path');
					if(!is_null($path) && file_exists(_WCE_MODELS_PATH."/objects/".$path)){
						$this->setTPLPath(_WCE_MODELS_PATH."/objects/".$path);
					}else{
						$this->setTPLPath(DIMS_APP_PATH.'templates/objects/flash_accueil/index_home.tpl');
					}
					$dims = dims::getInstance();
					if(isset($_SESSION['dims']['front_template_name']) && $_SESSION['dims']['front_template_name'] != '' && file_exists(DIMS_WEB_PATH."/common/templates/frontoffice/".$_SESSION['dims']['front_template_name']."/styles_flash_home.css")){
						$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/common/templates/frontoffice/'.$_SESSION['dims']['front_template_name'].'/styles_flash_home.css');
					}else{
						$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/common/templates/objects/flash_accueil/styles_flash_home.css');
					}
					break;
				case 'rss':
					$this->buildRSS();
					$this->setTPLPath(DIMS_APP_PATH.'templates/objects/flash_accueil/rss.tpl');
					break;
				case 'full_index':
					$this->buildView(true);
					$path = $this->getParam('path');
					if(!is_null($path) && file_exists(_WCE_MODELS_PATH."/objects/".$path)){
						$this->setTPLPath(_WCE_MODELS_PATH."/objects/".$path);
					}else{
						$this->setTPLPath(DIMS_APP_PATH.'templates/objects/flash_accueil/full_index.tpl');
					}
					$dims = dims::getInstance();
					if(isset($_SESSION['dims']['front_template_name']) && $_SESSION['dims']['front_template_name'] != '' && file_exists(DIMS_WEB_PATH."/common/templates/frontoffice/".$_SESSION['dims']['front_template_name']."/full_flash_styles.css")){
						$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/common/templates/frontoffice/'.$_SESSION['dims']['front_template_name'].'/full_flash_styles.css');
					}else{
						$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/common/templates/objects/flash_accueil/full_flash_styles.css');
					}
					break;
			}
			return $this->getTPLPath();
		}
	}

	public function buildView($full = false){
		$dims = dims::getInstance();
		$max_elems = $this->getParam('max-elem');
		if( empty($max_elems) ) $max_elems = 3;

		if( ! $full ) $limit = ' LIMIT 0, '.$max_elems;
		else $limit = '';
		//récupération des N dernières alertes
		$db = $this->object->db;
		$now = date('YmdHis');
		$res = $db->query("	SELECT 		a.*
							FROM 		(
										SELECT 		a.*
										FROM 		".wce_article::TABLE_NAME." a
									   	INNER JOIN 	dims_mod_wce_object_corresp c
									   	ON 			c.id_article = a.id
									   	AND 		c.id_object = :idobject
									   	WHERE 		".$now." >= a.timestp_published
									   ".($this->object->fields['pubfin_dependant']?"AND 		(".$now." <= a.timestp_unpublished
										   				OR a.timestp_unpublished=0)":"")."
										AND 		a.id_lang IN ( :wcedefaultlg , :idlang )
										ORDER BY 	a.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."
										) as a
						   	GROUP BY 	a.id
						   	ORDER BY 	a.timestp_published DESC
						   	".$limit, array(
				':idobject'			=> $this->object->getId(),
				':wcedefaultlg'		=> $_SESSION['dims']['wce_default_lg'],
				':idlang'			=> $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']
		));

		$news_list = array();
		while($fields = $db->fetchrow($res)){
			//Gestion de l'assignation smarty des variables utilisées
			$news = array();
			$news['id'] = $fields['id'];
			//$news['timestp_published'] = $fields['timestp_published'];
			$news['timestp_published'] = ($fields['timestp_published']!='') ? $fields['timestp_published'] : $fields['lastupdate_timestp'];
			$news['title'] = $fields['title'];
			$news['color'] = $fields['color'];
			$news['description'] = ( ! $full) ? dims_strcut(strip_tags($fields['description']), 80) : $fields['description'];


			if(!empty($fields['id_article_link']) && $fields['id_article_link'] != $fields['id']){
				$news['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id_article_link'];
			}elseif(isset($fields['id_heading']) && $fields['id_heading'] > 0 && $fields['id_article_link'] != $fields['id']){
				$news['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id'];
			}elseif( ! $full && wce_article::getArticleByRewrite($_SESSION['dims']['workspaceid'], 'news') )
				$news['link'] = $dims->getProtocol().$dims->getHttpHost().'/news.html#actu_'.$fields['id'];
			elseif($fields['url'] != ''){
				$news['link'] = $fields['url'].'" target="_blank';
			}else
				$news['link'] = '';
			$path=DIMS_ROOT_PATH.'www/data/articles/'.$fields['picto'];

			$webpath= $dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];
			if ($fields['picto']!='' && file_exists($path)) {
				$news['picto'] = $webpath;
				$news['picto_length'] = filesize($path);
			}else{
				$news['picto'] = '';
				$news['picto_length'] = 0;
			}

			$news_list[] = $news;
		}
		//gestion du lien
		$all_news = wce_article::getArticleByRewrite($_SESSION['dims']['workspaceid'], 'news');
		if(!is_null($all_news)) $this->smarty->assign('link_available', true);
		else $this->smarty->assign('link_available', false);

		$this->smarty->assign('news', $news_list);
		$this->smarty->assign('title', $this->object->fields['label']);
	}

	public function buildRSS(){
		if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $rootpath="http://";
		else $rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];

		$this->smarty->assign('rootpath', $rootpath);
		global $db;
		$now = date('YmdHis');
		$limit = "";
		if($this->object->fields['limit_rss'] > 0){
			$limit = "AND a.timestp_published >= ".date('YmdHis',mktime(date("H")-$this->object->fields['limit_rss']));
		}
		$res = $db->query("SELECT a.* FROM ".wce_article::TABLE_NAME." a
						   INNER JOIN dims_mod_wce_object_corresp c
						   ON c.id_article = a.id
						   INNER JOIN dims_mod_wce_object o
						   ON o.id = c.id_object
						   WHERE ".$now." >= a.timestp_published $limit 
						   ".($this->object->fields['pubfin_dependant']?"AND 		(".$now." <= a.timestp_unpublished
							   				OR a.timestp_unpublished=0)":"")."
						   AND o.id_module = :idm
						   GROUP BY a.id
						   ORDER BY a.timestp_published DESC
						   LIMIT 20", array(
				':idm' => $_SESSION['dims']['wcemoduleid'],
		));

		$cpte=1;

		$arrayElem = array();

		$total=$db->numrows($res);
		$dims = dims::getInstance();
		while ($fields = $db->fetchrow($res)) {
			$elem=array();
			$elem['id'] = $fields['id'];
			$elem['title'] = $fields['title'];
			$elem['color'] = $fields['color'];
			$ldate_pub = ($fields['timestp_published']!='') ? dims_timestamp2local($fields['timestp_published']) : dims_timestamp2local($fields['lastupdate_timestp']);
			$tab_date = explode('/', $ldate_pub['date']);
			$elem['date']= $tab_date[2].'-'.$tab_date[1].'-'.$tab_date[0].'T'.$ldate_pub['time'].'Z';
			$elem['description'] = $fields['description'];

			$path=DIMS_ROOT_PATH.'www/data/articles/'.$fields['picto'];
			$webpath= $dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];
			$elem['picto'] = "";
			$elem['picto_length'] = "";
			if ($fields['picto']!='' && file_exists($path)) {
				$elem['picto'] = $webpath;
				$elem['picto_length'] = filesize($path);
			}else{
				$elem['picto'] = '';
				$elem['picto_length'] = 0;
			}

			$elem['target']= "";

			$elem['link'] = "";
			if($fields['url'] != ''){
				$elem['link'] = $fields['url'];
			}else{
				$elem['link'] = $dims->getProtocol().$dims->getHttpHost().'/news.html#actu_'.$fields['id'];
				if(!empty($fields['id_article_link']) && $fields['id_article_link'] != $fields['id']){
					$elem['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id_article_link'];
				}elseif(isset($fields['id_heading']) && $fields['id_heading'] > 0 && $fields['id_article_link'] != $fields['id']){
					$elem['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id'];
				}
			}

			$elem['target'] = "";
			if ($fields['url_window']) $elem['target']= " target='_blank' ";
			$elem['cpte'] = $cpte;

			$arrayElem[]=$elem;
			$cpte++;
		}
		$this->smarty->assign('rss_elems', $arrayElem);
	}

}
