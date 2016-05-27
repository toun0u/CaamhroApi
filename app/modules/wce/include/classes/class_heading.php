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
require_once DIMS_APP_PATH."modules/wce/include/classes/class_article.php";

class wce_heading extends dims_data_object {
	const TABLE_NAME = 'dims_mod_wce_heading';
	private $wce_site;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id', 'id_lang');
	}

	public function open() {
		$id=0;
		$id_lang="";
		$numargs = func_num_args();
		for ($i = 0; $i < $numargs; $i++) {
			if ($i==0) $id=func_get_arg($i);
			elseif ($i==1) $id_lang= func_get_arg($i);
		}
		if ($id_lang=='' || $id_lang<=0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
			if (!is_object($this->wce_site)){
				if (!isset($_SESSION['dims']['moduleid']) || (!(isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] > 0))){
					$lstwcemods=dims::getInstance()->getWceModules(true);
					$this->wce_site= new wce_site ($this->db,current($lstwcemods),true);
				}
				else
					$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid'],true);
			}
			$id_lang = $this->wce_site->getDefaultLanguage();
		}

		// ouverture de la fonction pere
		parent::open($id,$id_lang);

		if (!isset($this->fields['id_globalobject'])){
			if (!is_object($this->wce_site)){
				if (!isset($_SESSION['dims']['moduleid']) || (!(isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] > 0))){
					$lstwcemods=dims::getInstance()->getWceModules(true);
					$this->wce_site= new wce_site ($this->db,current($lstwcemods));
				}
				else
					$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
			}
			$id_lang2 = $this->wce_site->getDefaultLanguage();
			if(dims::getInstance()->getScriptEnv() == 'admin.php'){
				$id_lang2 = $this->wce_site->getDefaultLanguage();
				if ($this->fields['id_lang'] != $id_lang2){
					$def = new wce_heading();
					$def->setWceSite($this->wce_site);
					$def->open($id);
					$this->fields = $def->fields;
					$this->fields['id_lang'] = $id_lang;
					$this->save();
				}
			}else
				parent::open($id,$id_lang2);
		}
	}

	public function save() {
		if ($this->fields['id_lang']=='' || $this->fields['id_lang']<=0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
			if (!is_object($this->wce_site)){
				if (!isset($_SESSION['dims']['moduleid']) || (!(isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] > 0))){
					$lstwcemods=dims::getInstance()->getWceModules(true);
					$this->wce_site= new wce_site ($this->db,current($lstwcemods));
				}
				else
					$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
			}
			$this->fields['id_lang'] = $this->wce_site->getDefaultLanguage();
		}

		return parent::save();
	}

	function setWceSite($wcesite) {
		$this->wce_site=$wcesite;
	}

	public function getWceSite(){
		return $this->wce_site;
	}

	function delete() {
		global $db;
		// on supprimes les sous rubriques
		$params=array();
		$params[':id_heading']=$this->fields['id'];
		$params[':id_module']=$this->fields['id_module'];

		$rver=$db->query("select * from dims_mod_wce_heading where id_heading=:id_heading and id_module=:id_module",$params);

		if ($db->numrows($rver)>0) {
			while ($h=$db->fetchrow($rver)) {
				$head=new wce_heading();
				$head->openFromResultSet($h);
				// appel r�cursif de suppression
				$head->delete();
			}
		}

		// on supprime les articles attaches
		$params=array();
		$params[':id_heading']=$this->fields['id'];
		$params[':id_module']=$this->fields['id_module'];
		$rver=$db->query("select * from dims_mod_wce_article where id_heading=:id_heading and id_module=:id_module",$params);

		if ($db->numrows($rver)>0) {
			while ($art=$db->fetchrow($rver)) {
				$artice = new wce_article();
				$artice->openFromResultSet($art);
				$artice->delete();
			}
		}

		// on decale les rubriques
		$params=array();
		$params[':id_heading']=$this->fields['id'];
		$params[':position']=$this->fields['position'];
		$res=$db->query("UPDATE {$this->tablename} SET position = position - 1 WHERE position > :position AND id_heading = :id_heading",$params);

		// on supprime l'heading courant
		parent::delete(_WCE_OBJECT_HEADING);
	}

	function getFirstPage() {
		$fpage=0;

		$db = dims::getInstance()->getDb();
		$today = dims_createtimestamp();

		$sel = "SELECT		*
				FROM		dims_mod_wce_article
				WHERE		id_heading = :id_heading
				AND			id_module = :id_module
				AND			(timestp_published <= :timestp_published
								OR timestp_published = 0)
				AND			(timestp_unpublished >= :timestp_unpublished
								OR timestp_unpublished = 0)
				ORDER BY	position";
		$rver=$db->query($sel,array(':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
									':id_heading'=>array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT),
									':timestp_published'=>array('value'=>$today,'type'=>PDO::PARAM_INT),
									':timestp_unpublished'=>array('value'=>$today,'type'=>PDO::PARAM_INT)));

		if ($db->numrows($rver)>0) {
			if ($art=$db->fetchrow($rver)) {
				$fpage=$art['id'];
			}
		}

		return $fpage;
	}

	public function updateArticlePosition() {
		$db = dims::getInstance()->getDb();
		$cpte=1;
		if ($this->fields['id_module'] > 0){
			$params=array();
			$params[':id_heading']=$this->fields['id'];
			$params[':id_module']=$this->fields['id_module'];
			$rver=$db->query("select * from dims_mod_wce_article where id_heading=:id_heading and id_module=:id_module order by position",$params);

			if ($db->numrows($rver)>0) {
				while ($art=$db->fetchrow($rver)) {
					$params=array();
					$params[':id_heading']=$this->fields['id'];
					$params[':id']=$art['id'];
					$params[':pos']=$cpte;
					$db->query("update dims_mod_wce_article set position=:pos where id=:id and id_heading=:id_heading",$params);
					$cpte++;
				}
			}
		}
	}


	public function updatePosition() {
		$db = dims::getInstance()->getDb();

		$cpte=1;
		$params=array();
		$params[':id_heading']=$this->fields['id'];
		$params[':id_module']=$this->fields['id_module'];

		$rver=$db->query("select * from dims_mod_wce_heading where id_heading=:id_heading and id_module=:id_module order by position",$params);

		if ($db->numrows($rver)>0) {
			while ($h=$db->fetchrow($rver)) {
				$params=array();
				$params[':id_heading']=$this->fields['id'];
				$params[':id']=$h['id'];
				$params[':pos']=$cpte;
				$db->query("update dims_mod_wce_heading set position=:pos where id=:id and id_heading=:id_heading",$params);
				$cpte++;
			}
		}
	}

	public function getArticles($idCateg=0, $status=-1, $id_creator=-1, $date_modify_from=-1, $date_modify_to=-1, $not_in_lang=-1, $keywords=-1, $include_content=-1, $tags = null){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		a.*
				FROM		(SELECT a.* FROM ".wce_article::TABLE_NAME." a";

		$params = array();

		if( !empty($not_in_lang) && $not_in_lang != -1){
			$sel .= " LEFT JOIN dims_mod_wce_lang l ON l.id = a.id_lang AND a.id_lang = :id_lang";
			$params[':id_lang'] = array('value'=>$not_in_lang,'type'=>PDO::PARAM_INT);
		}

		if(!empty($tags)){
			//construction de la requête IN
			$i = 1;
			foreach($tags as $id => $tag){
				$sel .= "	INNER JOIN	dims_tag_globalobject tg".$i."
							ON			tg".$i.".id_globalobject = a.id_globalobject
							AND			tg".$i.".id_tag = :id_tag$i";
				$params[":id_tag$i"] = array('value'=>$id,'type'=>PDO::PARAM_INT);
				$i++;
			}

		}

		$sel .= "	WHERE	a.id_heading = :id_heading
					AND		a.id_module = :id_module";
		$params[':id_heading'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$params[':id_module'] = array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT);
		if ($idCateg > 0){
			$cat = new category();
			$cat->open($idCateg);
			$lstGo = $cat->searchGbLinkChild(dims_const::_SYSTEM_OBJECT_WCE_ARTICLE);
			if(count($lstGo) > 0){
				$sel .= " AND	a.id_globalobject IN (".$db->getParamsFromArray($lstGo,'go',$params).") ";
			}else
				$sel .= " AND	a.id = 0 ";
		}

		if(!empty($status) && $status != -1){
			if($status == 2 ) $status = 0;
			$sel .= ' AND a.uptodate = :uptodate';
			$params[':uptodate'] = array('value'=>$status,'type'=>PDO::PARAM_INT);
		}

		if(!empty($id_creator) && $id_creator != -1){
			$sel .= ' AND a.id_user = :id_user';
			$params[':id_user'] = array('value'=>$id_creator,'type'=>PDO::PARAM_INT);
		}

		if(!empty($date_modify_from) && $date_modify_from != -1){
			$sel .= ' AND a.timestp_modify >= :date_modify_from';
			$params[':date_modify_from'] = array('value'=>$date_modify_from,'type'=>PDO::PARAM_INT);
		}

		if(!empty($date_modify_to) && $date_modify_to != -1){
			$sel .= ' AND a.timestp_modify <= :date_modify_to';
			$params[':date_modify_to'] = array('value'=>$date_modify_to,'type'=>PDO::PARAM_INT);
		}

		if( !empty($not_in_lang) && $not_in_lang != -1){
			$sel .= ' AND l.id IS NULL ';
		}

		if( ! empty($keywords) && $keywords != -1){
			//gestion des keywords en utilisant l'index
			require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
			$dims = dims::getInstance();
			$dimsearch = new search($dims);
			global $_DIMS;
			// ajout des objects sur lequel la recherche va se baser
			$fields = array();
			$fields[] = 'title';
			if(!empty($include_content) && $include_content != -1){
				for($i=1;$i<=19;$i++){
					$fields[] = "content$i";
					$fields[] = "content$i";
				}
			}
			$fieldsBlock = $fields;
			$fields[] = 'id';
			// TODO : il faut corriger cela > dans l'indexation l'id_object est égal à 1 alors qu'on est sur un article
			$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], 1,$_DIMS['cste']['_ARTICLE'], false, $fields);
			$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_WCE_ARTICLE,$_DIMS['cste']['_ARTICLE'], false, $fields);
			if( ! empty($keywords) && $keywords != -1){
				$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_WCE_ARTICLE_BLOCK,'bloc', false, $fieldsBlock);
			}
			// reinitialise la recherche sur ce module courant, n'efface pas le cache result
			$dimsearch->initSearchObject();

			$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
			$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

			$dimsearch->executeSearch($keywords, $kword,$idmodule, $idobj, $idmetafield, $sens);
			$id_blocks = array();
			$id_articles = array();

			foreach($dimsearch->tabresultat as $id_module_type => $objects){
				foreach($objects as $id_mb_object => $tab){
					if($id_mb_object == dims_const::_SYSTEM_OBJECT_WCE_ARTICLE_BLOCK){
						foreach($tab as $id_block){
							$id_blocks[] = $id_block;
						}
					}
					else{
						foreach($tab as $id_article){
							$id_articles[$id_article] = $id_article;
						}
					}
				}
			}

			//on doit faire une requête subsidiaire pour aller choper les articles attachés aux blocs
			if(!empty($id_blocks)){
				$params2 = array();
				$res = $db->query("SELECT b.id_article
								   FROM dims_mod_wce_article_block b
								   WHERE b.id IN (".$db->getParamsFromArray($id_blocks,'id',$params2).")",$params2);

				while($fields = $db->fetchrow($res)){
					$id_articles[$fields['id_article']] = $fields['id_article'];
				}
			}
			if(count($id_articles) == 0) $id_articles[] = 0;
			$sel .= " AND a.id IN (".$db->getParamsFromArray($id_articles,'id',$params).") ";

		}

		$sel .= "
				ORDER BY id_lang
				) as a
				GROUP BY a.id
				ORDER BY	a.title ASC";

		$res = $db->query($sel,$params);
		$lst = array();

		while($r = $db->fetchrow($res)){
			$art = new wce_article();
			$art->openFromResultSet($r,false);
			$lst[$r['id']] = $art;
		}
		return $lst;
	}


	private $article_urlrewrite = "";
	private $lstRubriques = array();
	private $lstArticles = array();

	public static function getAllHeadings($label = ""){
		$db =dims::getInstance()->db;
		$res=0;
		$params=array();

		if (isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] != ''  && $_SESSION['dims']['moduleid'] > 0){
			$ord=(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC');
			if (isset($_SESSION['dims']['wce_default_lg'])) $l1=$_SESSION['dims']['wce_default_lg']; else $l1=1;
			if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $l2=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; else $l2=1;
			$select = "	SELECT		x.*
						FROM		(SELECT distinct h.*, a.urlrewrite as article_urlrewrite
									FROM		(SELECT		h.*
												FROM		dims_mod_wce_heading as h
												WHERE		h.id_module = :id_module
												".(($label!="")?"h.label LIKE :label":"")."
												AND			h.type = 0
												AND			h.id_heading = 0
												AND			h.id_lang IN (:l1,:l2)
												ORDER BY	h.id_lang ".$ord."
												) as h
									LEFT JOIN	dims_mod_wce_article as a
									ON			(a.id=h.linkedpage OR (h.linkedpage='' AND a.position=1 ))
									AND			a.id_lang IN (:l1,:l2, null)
									AND			a.id_heading>0
									AND			a.id_heading=h.id
									GROUP BY	h.id
									ORDER BY	a.id_lang ".$ord."
									) as x
						GROUP BY	x.id
						ORDER BY	x.depth, x.position";

			$params[':id_module']=$_SESSION['dims']['moduleid'];
			if ($label!="") $params[':label']="%".$label."%";
			$params[':l1']=$l1;
			$params[':l2']=$l2;
		}else{
			$ord=(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC');
			if (isset($_SESSION['dims']['wce_default_lg'])) $l1=$_SESSION['dims']['wce_default_lg']; else $l1=1;
			if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $l2=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; else $l2=1;
			$select = "	SELECT		x.*
						FROM		(SELECT distinct h.*, a.urlrewrite as article_urlrewrite
									FROM		(SELECT		h.*
												FROM		dims_mod_wce_heading as h
												WHERE		h.id_module IN (".$db->getParamsFromArray(dims::getInstance()->getWceModulesFromDomain(),'do',$params).")
												".(($label!="")?"h.label LIKE :label":"")."
												AND			h.type = 0
												AND			h.id_heading = 0
												AND			h.id_lang IN ($l1,$l2)
												ORDER BY	h.id_lang ".$ord."
												) as h
									LEFT JOIN	dims_mod_wce_article as a
									ON			(a.id=h.linkedpage OR (h.linkedpage='' AND a.position=1 ))
									AND			a.id_lang IN (:l1,:l2, null)
									AND			a.id_heading>0
									AND			a.id_heading=h.id
									GROUP BY	h.id
									ORDER BY	a.id_lang ".$ord."
									) as x
						GROUP BY	x.id
						ORDER BY	x.depth, x.position";

			if ($label!="") $params[':label']="%".$label."%";
			$params[':l1']=$l1;
			$params[':l2']=$l2;

		}

		$res = $db->query($select,$params);
		$lstHeadings = array();
		while($r = $db->fetchrow($res)){
			$head = new wce_heading();
			$tmp = $r;
			unset($tmp['article_urlrewrite']);
			$head->openFromResultSet($tmp);
			unset($tmp);
			$head->setArticleUrlRewrite($r['article_urlrewrite']);
			$head->setLstRubriques($head->getAllRubriques($label));
			$head->setLstArticles($head->getAllArticles($label));
			$lstHeadings[] = $head;
		}
		return $lstHeadings;
	}

	public function getAllRubriques($label = ""){
		$db =dims::getInstance()->db;
		$params=array();
		$ord=(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC');
		if (isset($_SESSION['dims']['wce_default_lg'])) $l1=$_SESSION['dims']['wce_default_lg']; else $l1=1;
		if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $l2=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; else $l2=1;

		$select = "	SELECT		x.*
				FROM		(SELECT distinct h.*, a.urlrewrite as article_urlrewrite
							FROM		(SELECT		h.*
										FROM		dims_mod_wce_heading as h
										WHERE		h.id_module = :id_module
										".(($label!="")?"h.label LIKE :label":"")."
										AND		h.type = 0
										AND		h.id_heading = :id_heading
										AND			h.id_lang IN (:l1,:l2)
										ORDER BY	h.id_lang ".$ord."
										) as h
							LEFT JOIN	dims_mod_wce_article as a
							ON			(a.id=h.linkedpage OR (h.linkedpage='' AND a.position=1 ))
							AND			a.id_lang IN (:l1,:l2, null)
							AND			a.id_heading>0
							AND			a.id_heading=h.id
							GROUP BY	h.id
							ORDER BY	a.id_lang ".$ord."
							) as x
				GROUP BY	x.id
				ORDER BY	x.depth, x.position";

		$params[':id_module']=$_SESSION['dims']['moduleid'];
		$params[':id_heading']=$this->fields['id'];
		if ($label!="") $params[':label']="%".$label."%";
		$params[':l1']=$l1;
		$params[':l2']=$l2;
		$res = $db->query($select,$params);
		$lstRubriques = array();
		while($r = $db->fetchrow($res)){
			$rubrique = new wce_heading();
			$tmp = $r;
			unset($tmp['article_urlrewrite']);
			$rubrique->openFromResultSet($tmp);
			unset($tmp);
			$rubrique->setArticleUrlRewrite($r['article_urlrewrite']);
			$rubrique->setLstRubriques($rubrique->getAllRubriques($label));
			$rubrique->setLstArticles($rubrique->getAllArticles($label));
			$lstRubriques[] = $rubrique;
		}
		return $lstRubriques;
	}

	public function getAllArticles($label = ""){
		$params=array();
		$db =dims::getInstance()->db;
		$ord=(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC');
		if (isset($_SESSION['dims']['wce_default_lg'])) $l1=$_SESSION['dims']['wce_default_lg']; else $l1=1;
		if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $l2=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; else $l2=1;

		$select = "SELECT		x.*
					FROM		(SELECT					*
								FROM			dims_mod_wce_article
								WHERE			id_heading = :id_heading
								AND				type = 0
								AND				id_module = :id_module
								AND			id_lang IN (:l1,:l2)
								ORDER BY	id_lang ".$ord."
								) as x
					GROUP BY	x.id
					ORDER BY	x.position";

		$params[':id_heading']=$this->fields['id'];
		$params[':id_module']=$this->fields['id_module'];
		$params[':l1']=$l1;
		$params[':l2']=$l2;
		$res = $db->query($select,$params);
		$lstArticles = array();
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_article.php";
		while($r = $db->fetchrow($res)){
			$art = new wce_article();
			$art->openFromResultSet($r);
			$lstArticles[] = $art;
		}
		return $lstArticles;
	}

	public function getArticleUrlRewrite(){
		return $this->article_urlrewrite;
	}
	public function setArticleUrlRewrite($url){
		$this->article_urlrewrite = $url;
	}

	public function getLstRubriques(){
		return $this->lstRubriques;
	}
	public function setLstRubriques($lst){
		$this->lstRubriques = $lst;
	}

	public function getLstArticles(){
		return $this->lstArticles;
	}
	public function setLstArticles($lst){
		$this->lstArticles = $lst;
	}

	public function displayArbo($tplRoot, $tplRubrique, $tplArticle, $openedHeadings, $articleid){
		if ($this->fields['id_heading'] == 0){
			$this->setLightAttribute('opened',($this->fields['id'] == $openedHeadings[count($openedHeadings)-1] && empty($articleid)));
			$this->display($tplRoot);
			$nbRub = count($this->getLstRubriques())-1+count($this->getLstArticles());
			foreach($this->getLstRubriques() as $key => $rub){
				$rub->setLightAttribute('isLast',($nbRub==$key));
				$rub->setLightAttribute('previousLast',array());
				$rub->setLightAttribute('opened',($rub->fields['id'] == $openedHeadings[count($openedHeadings)-1] && empty($articleid)));
				$rub->setLightAttribute('displayed',true);
				$rub->setLightAttribute('childOpened',in_array($rub->fields['id'],$openedHeadings));
				$rub->displayArbo($tplRoot, $tplRubrique, $tplArticle, $openedHeadings, $articleid);
			}
			$nbRub = count($this->getLstArticles())-1;
			foreach($this->getLstArticles() as $key => $art){
				$art->setLightAttribute('depth',0);
				$art->setLightAttribute('previousLast',array());
				$art->setLightAttribute('isLast',($nbRub==$key));
				$art->setLightAttribute('opened',($art->fields['id'] == $articleid));
				$art->setLightAttribute('displayed',true);
				$art->display($tplArticle);
			}
		}else{
			$this->display($tplRubrique);
			$nbRub = count($this->getLstRubriques())-1+count($this->getLstArticles());
			$prevLast = $this->getLightAttribute('previousLast');
			$prevLast[] = $this->getLightAttribute('isLast');
			foreach($this->getLstRubriques() as $key => $rub){
				$rub->setLightAttribute('isLast',($nbRub==$key));
				$rub->setLightAttribute('previousLast',$prevLast);
				$rub->setLightAttribute('opened',($rub->fields['id'] == $openedHeadings[count($openedHeadings)-1] && empty($articleid)));
				$rub->setLightAttribute('displayed',in_array($rub->fields['id_heading'],$openedHeadings));
				$rub->setLightAttribute('childOpened',in_array($rub->fields['id'],$openedHeadings));
				$rub->displayArbo($tplRoot, $tplRubrique, $tplArticle, $openedHeadings, $articleid);
			}
			$nbRub = count($this->getLstArticles())-1;
			$depth = count(explode(';',$this->fields['parents']))-1;
			foreach($this->getLstArticles() as $key => $art){
				$art->setLightAttribute('depth',$depth);
				$art->setLightAttribute('previousLast',$prevLast);
				$art->setLightAttribute('isLast',($nbRub==$key));
				$art->setLightAttribute('opened',($art->fields['id'] == $articleid));
				$art->setLightAttribute('displayed',in_array($art->fields['id_heading'],$openedHeadings));
				$art->display($tplArticle);
			}
		}
	}

	public function getRedirectArticle(){
		if (isset($this->fields['linkedpage']) && $this->fields['linkedpage'] != '' && $this->fields['linkedpage'] > 0){
			return $this->fields['linkedpage'];
		}elseif(isset($this->fields['linkedheading']) && $this->fields['linkedheading'] != '' && $this->fields['linkedheading'] > 0){
			$head = new wce_heading();
			$head->open($this->fields['linkedheading']);
			return $this->getRedirectArticle();
		}elseif(isset($this->fields['url']) && trim($this->fields['url']) != ''){
			return trim($this->fields['url']);
		}else
			return $this->getFirstPage();
	}

	public function getObjectCorresp($openObj = true){
		$lst = array();
		if ($this->fields['id'] != '' && $this->fields['id'] > 0){
			$db = dims::getInstance()->db;
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
			$sel = "SELECT	*
					FROM	".article_object_corresp::TABLE_NAME."
					WHERE	id_heading = :id_heading";
			$params = array();
			$params[':id_heading'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$res=$db->query($sel,$params);

			if ($openObj){
				while ($r = $db->fetchrow($res)) {
					$obj = new article_object_corresp();
					$obj->openFromResultSet($r);
					$lst[] = $obj;
				}
			}else{
				while ($r = $db->fetchrow($res)) {
					$lst[$r['id_object']] = $r['id_object'];
				}
			}
		}
		return $lst;
	}

	/*
	 * Fonction permettant de lister les langues de ce heading
	 */
	public function getListLang() {
		require_once DIMS_APP_PATH."modules/wce/wiki/include/class_wce_lang.php";
		$sql = "SELECT		DISTINCT l.*
				FROM		".wce_lang::TABLE_NAME." l
				INNER JOIN	".self::TABLE_NAME." h
				ON			h.id_lang = l.id
				WHERE		h.id = :id";

		$params=array();
		$params[":id"]=$this->fields['id'];

		$res = $this->db->query($sql,$params);
		$lst = array();
		while ($r = $this->db->fetchrow($res)) {
			$lang = new wce_lang();
			$lang->openFromResultSet($r);
			$lst[$r['id']] = $lang;
		}
		return $lst;
	}

	public function contructAriane(wce_article $art = null){
		$return = array();
		if(!is_null($art)){
			$elem = array();
			$elem['id']		= $this->fields['id'];
			$elem['type']	= 2; //Heading
			$elem['label']	= $this->fields['label'];
			$elem['parent']  = $this->fields['id_heading'];
			$script = "/index.php?articleid=".$art->fields['id'];
			if($art->fields['urlrewrite'] != '')
				$script = "/".$art->fields['urlrewrite'].".html";
			$elem['link']	= $script;
			$return[] = $elem;
			if($this->fields['depth'] > 2){
				$head = new wce_heading();
				$head->open($this->fields['id_heading']);
				$return = array_merge($head->contructAriane(),$return);
			}
		}else{
			if($this->fields['linkedpage'] != '' && $this->fields['linkedpage'] > 0){
				$art = new wce_article();
				$art->open($this->fields['linkedpage']);
				$elem = array();
				$elem['id']		= $this->fields['id'];
				$elem['type']	= 2; //Heading
				$elem['label']	= $this->fields['label'];
				$elem['parent']  = $this->fields['id_heading'];
				$script = "/index.php?articleid=".$art->fields['id'];
				if($art->fields['urlrewrite'] != '')
					$script = "/".$art->fields['urlrewrite'].".html";
				$elem['link']	= $script;
				$return[] = $elem;
			}elseif(($idArt = $this->getFirstPage()) != '' && $idArt > 0){
				$art = new wce_article();
				$art->open($idArt);
				$elem = array();
				$elem['id']		= $this->fields['id'];
				$elem['type']	= 2; //Heading
				$elem['label']	= $this->fields['label'];
				$elem['parent']  = $this->fields['id_heading'];
				$script = "/index.php?articleid=".$art->fields['id'];
				if($art->fields['urlrewrite'] != '')
					$script = "/".$art->fields['urlrewrite'].".html";
				$elem['link']	= $script;
				$return[] = $elem;
			}else{
				$elem = array();
				$elem['id']		= $this->fields['id'];
				$elem['type']	= 2; //Heading
				$elem['label']	= $this->fields['label'];
				$elem['link']	= "/index.php?headingid=".$this->fields['id'];;
				$return[] = $elem;
			}
			$head = new wce_heading();
			$head->open($this->fields['id_heading']);
			if($head->fields['depth'] > 1)
				$return = array_merge($head->contructAriane(),$return);
		}
		return $return;
	}

	public static function constructArianeHeadingsWCE(){
		$headings = self::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>0),'ORDER BY depth, position');
		$lst = array();
		foreach($headings as $h){
			$url = '<a href="'.module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PROPERTIES."&headingid=".$h->get('id').'">'.$h->get('label').'</a>';
			if($h->get('id_heading')>0 && isset($lst[$h->get('id_heading')])){
				$lst[$h->get('id')] = $lst[$h->get('id_heading')]." > ".$url;
			}else{
				$lst[$h->get('id')] = $url;
			}
		}
		return $lst;
	}
}
?>
