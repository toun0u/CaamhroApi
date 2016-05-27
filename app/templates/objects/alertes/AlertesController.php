<?php
include_once DIMS_APP_PATH.'/modules/wce/include/classes/class_dynobject.php';
class AlertesController extends DynObject{
	public function buildIHM(){
		if( ! is_null($this->smarty)){
			switch($this->getParam('mode')){
				default:
				case 'home':
					$this->buildView();
					$this->setTPLPath(DIMS_APP_PATH.'templates/objects/'.$this->object->fields['template'].'/index.tpl');
					$dims = dims::getInstance();
					$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/templates/objects/'.$this->object->fields['template'].'/styles.css');
					break;
				case 'rss':
					$this->buildRSS();
					$this->setTPLPath(DIMS_APP_PATH.'templates/objects/alertes/rss.tpl');
					$dims = dims::getInstance();
					break;
				case 'full_index':
					$this->buildView(true);
					$this->setTPLPath(DIMS_APP_PATH.'templates/objects/'.$this->object->fields['template'].'/full_index.tpl');
					$dims = dims::getInstance();
					$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/templates/objects/'.$this->object->fields['template'].'/full_styles.css');
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

		$alerts = array();
		while($fields = $db->fetchrow($res)){
			//Gestion de l'assignation smarty des variables utilisées
			$alert = array();
			$alert['id'] = $fields['id'];
			$alert['timestp_published'] = $fields['timestp_published'];
			$alert['title'] = $fields['title'];
			$alert['color'] = $fields['color'];
			$alert['description'] = ( ! $full) ? dims_strcut($fields['description'], 80) : $fields['description'];
			switch($fields['alert_level'])
			{
				default:
				case 1:
					$alert['level_class'] = 'low_gravity';
					$alert['level_title'] = $_SESSION['cste']['LOW_LEVEL'];
					break;
				case 2:
					$alert['level_class'] = 'middle_gravity';
					$alert['level_title'] = $_SESSION['cste']['MIDDLE_LEVEL'];
					break;
				case 3:
					$alert['level_class'] = 'high_gravity';
					$alert['level_title'] = $_SESSION['cste']['HIGH_LEVEL'];
					break;
			}


			if(!empty($fields['id_article_link']) && $fields['id_article_link'] != $fields['id']){
				$alert['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id_article_link'];
			}elseif(isset($fields['id_heading']) && $fields['id_heading'] > 0 && $fields['id_article_link'] != $fields['id']){
				$alert['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id'];
			}elseif( ! $full )
				$alert['link'] = $dims->getProtocol().$dims->getHttpHost().'/alertes.html#alert_'.$fields['id'];
			else
				$alert['link'] = '';

			$path=DIMS_ROOT_PATH.'www/data/articles/'.$fields['picto'];

			$webpath= $dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];
			if ($fields['picto']!='' && file_exists($path)) {
				$alert['picto'] = $webpath;
				$alert['picto_length'] = filesize($path);
			}else{
				$alert['picto'] = '';
				$alert['picto_length'] = 0;
			}

			$alerts[] = $alert;
		}
		//gestion du lien
		$all_article = wce_article::getArticleByRewrite($_SESSION['dims']['workspaceid'], 'alertes');
		if(!is_null($all_article)) $this->smarty->assign('link_available', true);
		else $this->smarty->assign('link_available', false);

		$this->smarty->assign('alerts', $alerts);
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
		$res = $db->query("	SELECT 		a.*
							FROM 		".wce_article::TABLE_NAME." a
							INNER JOIN 	dims_mod_wce_object_corresp c
							ON 			c.id_article = a.id
							INNER JOIN 	dims_mod_wce_object o
							ON 			o.id = c.id_object
							WHERE 		".$now." >= a.timestp_published $limit
						   ".($this->object->fields['pubfin_dependant']?"AND 		(".$now." <= a.timestp_unpublished
											OR a.timestp_unpublished=0)":"")."
							GROUP BY 	a.id
							ORDER BY 	a.timestp_published DESC ",array(
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
			$ldate_pub = ($fields['lastupdate_timestp']!='') ? dims_timestamp2local($fields['lastupdate_timestp']) : array('date' => '');
			$tab_date = explode('/', $ldate_pub['date']);
			$elem['date']= $tab_date[2].'-'.$tab_date[1].'-'.$tab_date[0].'T'.$ldate_pub['time'].'Z';
			$elem['description'] = $fields['description'];

			$path=DIMS_ROOT_PATH.'www/data/articles/'.$fields['picto'];
			$webpath= $dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];
			if ($fields['picto']!='' && file_exists($path)) {
				$elem['picto'] = $webpath;
				$elem['picto_length'] = filesize($path);
			}else{
				$elem['picto'] = '';
				$elem['picto_length'] = 0;
			}

			$elem['target']= "";

			$elem['link'] = $dims->getProtocol().$dims->getHttpHost().'/alertes.html#alert_'.$fields['id'];
			if(!empty($fields['id_article_link']) && $fields['id_article_link'] != $fields['id']){
				$elem['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id_article_link'];
			}
			else if(isset($fields['id_heading']) && $fields['id_heading'] > 0 && $fields['id_article_link'] != $fields['id']){
				$elem['link'] = $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$fields['id'];
			}

			if ($fields['url_window']) $elem['target']= " target='_blank' ";
			$elem['cpte'] = $cpte;

			$arrayElem[]=$elem;
			$cpte++;
		}
		$this->smarty->assign('rss_elems', $arrayElem);
	}
}
