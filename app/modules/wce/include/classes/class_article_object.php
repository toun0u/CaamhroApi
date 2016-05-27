<?
require_once DIMS_APP_PATH.'modules/system/class_pagination.php';

class article_object extends pagination {
	public $months;//permet le switch sur le mois pour afficher les billets avec le mois sur 4 lettres max
    const TABLE_NAME = 'dims_mod_wce_object';

	function __construct($limit_pagination=false) {
		parent::dims_data_object(self::TABLE_NAME,'id');
		$this->isPageLimited = $limit_pagination;
		//pour l'affichage des news ou à la une permet de convertir le mois (faudrait faire des constantes nan ?)
		$this->months =array();
		$this->months['01'] = 'janv';
		$this->months['02'] = 'fév';
		$this->months['03'] = 'mars';
		$this->months['04'] = 'avr';
		$this->months['05'] = 'mai';
		$this->months['06'] = 'juin';
		$this->months['07'] = 'juil';
		$this->months['08'] = 'août';
		$this->months['09'] = 'sept';
		$this->months['10'] = 'oct';
		$this->months['11'] = 'nov';
		$this->months['12'] = 'déc';
	}

	/*
	 * charge la liste des années disponibles
	*/
	public function getYearFilters($date_filters=true){
		$select = "	SELECT			DISTINCT(SUBSTRING(a.lastupdate_timestp,1, 4)) as year
					FROM			dims_mod_wce_article as a
					INNER JOIN		dims_mod_wce_object_corresp as c
					ON				a.id=c.id_article
					AND				c.id_object = :id_object";
		$params = array();
		$params[':id_object'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		//filtrage sur les dates de publication
		if($date_filters){
			$curDate = date('YmdHis');
			$select .= " WHERE :timestp_published >= a.timestp_published";//on filtre quoi qu'il arrive sur la borne inférieure
			$params[':timestp_published'] = array('value'=>$curDate,'type'=>PDO::PARAM_INT);
			if($this->fields['pubfin_dependant']==1){
				$select .= " AND (:timestp_unpublished <= a.timestp_unpublished  OR a.timestp_unpublished=0)";
				$params[':timestp_unpublished'] = array('value'=>$curDate,'type'=>PDO::PARAM_INT);
			}
		}

		$select .= " ORDER BY year DESC";

		$res = $this->db->query($select,$params);
		$arr = array();
		$arr['-1']['year'] = 'Toutes les années';
		$arr['-1']['sel'] = 0;

		$cpt = 0;
		while($tab = $this->db->fetchrow($res))
		{
			$arr[$tab['year']]['year'] = $tab['year'];
			$arr[$tab['year']]['sel'] = 0;
		}
		return $arr;
	}

	/*
	 * Fonction de collecte des contenus des objets liés
	*/
	public function getContent($type='',$templatecontent=false, $pagination=false, $wheresql = '', $date_filters=true, $view_rss_mode=_WCE_OBJECT_VIEW_FRONT) {
		$dims = dims::getInstance();
		$lstarticles = array();
		$params = array();

		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getContent($type, $templatecontent,true, $wheresql, $date_filters, $view_rss_mode));
			$limit = "LIMIT :limitdeb, :limitfin";
			$params[':limitdeb'] = array('value'=>$this->sql_debut,'type'=>PDO::PARAM_INT);
			$params[':limitfin'] = array('value'=>$this->limite_key,'type'=>PDO::PARAM_INT);
		}
		else $limit="";

		$today = dims_createtimestamp();

		// recuperation
		$select_object =	"
							SELECT			a.*
							FROM			dims_mod_wce_article as a
							INNER JOIN		dims_mod_wce_object_corresp as c
							ON				a.id=c.id_article
							AND 			c.id_rss = 0
							AND				c.id_object = :id_object";
		$params[':id_object'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		//echo $type;
		if ($type!='') {
			switch ($type) {
				case WCE_ALL_BREVE:
					$select_object.= " AND a.id_heading=0";
					break;
				case WCE_BREVE:
					$select_object.= /*" AND	c.id_object = ".$this->fields['id'].*/" AND a.id_heading=0";
					break;
				case WCE_ARTICLE:
					$select_object.= /*" AND	c.id_object = ".$this->fields['id'].*/" AND a.id_heading>0";
					break;
			}
		}

		switch($view_rss_mode)
		{
			default:
			case _WCE_OBJECT_VIEW_FRONT:
				$select_object .= " WHERE 1=1";
			break;
			case _WCE_OBJECT_VIEW_NO_RSS:
				$select_object .= 	" 	WHERE 			a.type != ".dims_const::_SYSTEM_OBJECT_RSS_ARTICLE;
			break;
			case _WCE_OBJECT_VIEW_ONLY_RSS:
				$select_object .= 	" 	WHERE 			a.type = ".dims_const::_SYSTEM_OBJECT_RSS_ARTICLE;
			break;
		}
		//filtrage sur les dates de publication
		if($date_filters){
			$curDate = date('YmdHis');
			$select_object .= " AND :timestp_published >= a.timestp_published";//on filtre quoi qu'il arrive sur la borne inférieure
			$params[':timestp_published'] = array('value'=>$curDate,'type'=>PDO::PARAM_INT);
			if($this->fields['pubfin_dependant']==1){
				$select_object .= " AND (:timestp_unpublished <= a.timestp_unpublished  OR a.timestp_unpublished=0)";
				$params[':timestp_unpublished'] = array('value'=>$curDate,'type'=>PDO::PARAM_INT);
			}
		}
		//else $select_object .= " WHERE 1=1";
		if($wheresql!='') $select_object.=" ".$wheresql;
		$select_object.=" GROUP BY a.id ORDER BY a.timestp_published DESC";//par ordre chronologique descendant
		$select_object.=" ".$limit;

		$result_object = $this->db->query($select_object,$params);

		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($result_object);
		}
		else {
			$arrayElem = array();
			while ($fields = $this->db->fetchrow($result_object)) {
				$path=realpath('.').'/data/articles/'.$fields['picto'];
				$webpath=$dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];
				$elem=array();
				$elem['path'] ='';
				$elem['pathactu'] = '';
				$elem['type'] = 'article';

				if ($fields['picto']!='') {
					if (strpos($fields['picto'],'http://') !== false){
						$elem['path'] = $fields['picto'];
						$elem['pathactu'] = $fields['picto'];
					}
					elseif (file_exists($path)){
						$ext = explode('.', $fields['picto']);
						$ext = strtolower($ext[count($ext)-1]);
						$webpathactu=$dims->getProtocol().$dims->getHttpHost().'/data/articles/art_'.$fields['id']."_500.".$ext;
						$elem['path'] = $webpath;
						$elem['pathactu'] = $webpathactu;
					}
				}

				$elem['id'] = $fields['id'];
				$elem['id_lang'] = $fields['id_lang'];
				$elem['title'] = $fields['title'];
				$elem['timestp_modify'] = $fields['timestp_modify'];

				$elem['mail_subject'] = dims_convertaccents(str_replace('"', '', $fields['title']));
				$elem['description'] = $fields['description'];
				$elem['year'] = substr($fields['lastupdate_timestp'],0, 4);
				if(substr($fields['lastupdate_timestp'],4, 2) > 0)
					$elem['month'] = $this->months[substr($fields['lastupdate_timestp'],4, 2)];//cf. include/global.php
				$elem['day'] = substr($fields['lastupdate_timestp'],6, 2);
				$elem['hour'] = substr($fields['lastupdate_timestp'],8, 2);
				$elem['minute'] = substr($fields['lastupdate_timestp'],10, 2);
                $elem['timestp_published'] = $fields['timestp_published'];
                $elem['timestp_unpublished'] = $fields['timestp_unpublished'];
                $elem['link'] = "";

				$elem['target']= "";
				$elem['source'] = $fields['source'];

				if($fields['id_article_link'] > 0) {
					$elem['link'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?articleid=".$fields['id_article_link'];
					$elem['permalink'] = $elem['link'];
					$elem['link_mode'] = "interne";
				}
				elseif ($fields['url']!='') {
					if (substr($fields['url'],0,4)!='http') {
						$fields['url']="http://".$fields['url'];
					}

					if ($fields['type'] == dims_const::_SYSTEM_OBJECT_RSS_ARTICLE) {
						$elem['link']=$fields['url'];
						$posurl=strpos($elem['link'], "url=");
						if (!$posurl===false) { // modif Pat traitement des urls en source
							$source=substr($elem['link'], $posurl+4);
							// traitement des //
							$indds=strpos($source,"//");
							$inds=strpos($source, '/', $indds+2);

							if ($inds>0) {
								if ($indds>0) $indds+=2; // pour enlever les //
								$source=substr($source,$indds,$inds-$indds);
							}
							$elem['source']=$source;
						}
					}
					else
						$elem['link']=str_replace("./", $dims->getProtocol().$dims->getHttpHost()."/",$fields['url']);

					$elem['permalink'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?articleid=".$fields['id'];

					$elem['link_mode'] = "externe";
                    $elem['target']= " target='_blank' ";

				}elseif(trim($fields['urlrewrite']) != ''){
                    $elem['link']="/".$fields['urlrewrite'].".html";
                }
				if ($fields['url_window']) $elem['target']= " target='_blank' ";
				else $lstarticles[]=$elem;
			}

			// ajout des rubriques
			if ($type==WCE_ARTICLE) {
				$select_object =	"
								SELECT			h.*
								FROM			dims_mod_wce_heading as h
								INNER JOIN		dims_mod_wce_object_corresp as c
								ON				h.id=c.id_heading
								AND				c.id_object = :id_object";
				$param = array();
				$param[':id_object'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);

				$result_object = $this->db->query($select_object,$param);

				while ($fields = $this->db->fetchrow($result_object)) {
					$elem['id'] = $fields['id'];
					$elem['title'] = $fields['label'];
					$elem['type'] = 'heading';
					$elem['link'] = "/index.php?headingid=".$fields['id'];
					$elem['permalink'] = $elem['link'];
					$elem['link_mode'] = "interne";
					$lstarticles[]=$elem;
				}
			}
			return $lstarticles;
		}
	}

	public function getLinkedFeeds(){
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_feed.php";
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		*
				FROM 		".rss_feed::TABLE_NAME."
				WHERE 		id_workspace = :idw
				AND 		id_object = :ido
				ORDER BY 	title";
		$params = array(
			':idw'=>array('value'=>$this->get('id_workspace'),'type'=>PDO::PARAM_INT),
			':ido'=>array('value'=>$this->get('id'),'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$rss = new rss_feed();
			$rss->openFromResultSet($r);
			$lst[] = $rss;
		}
		return $lst;
	}
}
