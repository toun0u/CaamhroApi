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
require_once(DIMS_APP_PATH.'modules/system/class_lang.php');
require_once(DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block.php');
require_once(DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block_model.php');
require_once(DIMS_APP_PATH.'modules/wce/include/classes/class_article.php');

class wce_site extends pagination {
	private static $wce_site_instance = null;

	const TABLE_NAME = 'dims_mod_wce_site';

	var $modelblocks;
	var $id_module;
	var $db;
	var $urlrewrites_from;
	var $urlrewrites_to;
	var $arraypagerewrite;
	var $levelsite;

	/**
	 *
	 * @return dims
	 */
	public static function getInstance($db=null){
		if(wce_site::$wce_site_instance == null){
			//TODO Message Erreur exception - dims mal initialisé
			wce_site::setInstance(new wce_site($db));
		}
		return wce_site::$wce_site_instance;
	}

	public static function setInstance(wce_site $wce_site){
		if($wce_site != null){
			wce_site::$wce_site_instance = $wce_site ;
		}else{
			//TODO MEssage Erreur exception
		}
	}

	public function __construct($database,$moduleid=-1,$light=false) {
		$dims = dims::getInstance();

		if ($moduleid==-1) {
			if( empty($_SESSION['dims']['moduleid']) && !empty($_SESSION['dims']['wcemoduleid']) ){
				$_SESSION['dims']['moduleid'] = $_SESSION['dims']['wcemoduleid'];
			}
			$moduleid = $_SESSION['dims']['moduleid'];
		}

		$this->modelblocks=array();
		$this->id_module=$moduleid;
		$this->db=$database;
		$this->urlrewrites_from=array();
		$this->urlrewrites_to=array();
		$this->levelsite=1;


		$mod=$dims->getModule($this->id_module);
		if (isset($mod['adminrestricted']) && $mod['adminrestricted']) {
			$this->levelsite=2;
		}

		if ($light==false) {
			// ouverture de la configuration du site
			parent::dims_data_object(self::TABLE_NAME,'id_module');
			$this->open($moduleid);

			if ((!isset($this->fields['id_module']) || $this->fields['id_module']<=0) && $moduleid != '' ) {
				$this->new=true;
				$this->fields['id_module']=$moduleid;
				$this->save();
			}
		}
	}

	public function getBlockModels() {
		return $this->modelblocks;
	}

	public function loadBlockModels($light_load=false) {
		// on traite pour detecter si on a le modele present ou non
		$basepath = _WCE_MODELS_PATH._DIMS_SEP.'blocks'._DIMS_SEP;

		if ($this->id_module>0) {
			if (!$light_load) { // A faire en administration et non en rendu, optimisation de code
				$tplarrays=array();

				$sql="select * from dims_mod_wce_article_block_model where id_module= :id_module order by label";
				$params = array();
				$params[':id_module']=$this->id_module;
				$res=$this->db->query($sql,$params);

				if ($this->db->numrows($res)) {
					while ($mod=$this->db->fetchrow($res)) {
						$tplarrays[$mod['label']]=$mod;
					}
				}
				$p = @opendir(realpath($basepath));
				if (is_dir($basepath)) {
					while ($tpl = @readdir($p)) {
						$tplpath=realpath($basepath)._DIMS_SEP.$tpl;
						//$ext=strtolower(substr(strrchr($tplpath, "."),1));

						if ($tpl != '.' && $tpl != '..'  && is_dir($basepath.$tpl)) {
							// on regarde dans la base de donnees si cela existe ou non
							if (!isset($tplarrays[$tpl])) {
								// on ajoute le modele
								$blockmodel=new wce_block_model();
								$blockmodel->init_description();
								$blockmodel->setugm();
								$blockmodel->fields['filename']=$tpl;
								$blockmodel->fields['label']=$tpl;
								$blockmodel->fields['present']=1;
								$blockmodel->save();
								$tplarrays[$tpl]['id']=$blockmodel->fields['id'];
								$tplarrays[$tpl]['present']=1;
								$tplarrays[$tpl]['present2']=1;
								unset($blockmodel);
							}
							else {
								$tplarrays[$tpl]['present2']=1;
							}
						}
					}
				}

				// on update les disponibilites
				foreach ($tplarrays as $id=>$model) {
					if(!isset($model['present2'])) $model['present2'] = 0;
					if ($model['present']!=$model['present2']) {

						$blockmodel=new wce_block_model();
						$blockmodel->open($model['id']);

						if ($model['present2']==0) {
							// modele supprime
							$blockmodel->fields['present']=0;
							$blockmodel->save();
						}
						else {
							$blockmodel->fields['present']=1;
							$blockmodel->save();
						}
						unset($blockmodel);
					}
				}
			}

			$this->modelblocks=array();
			$sql="select * from dims_mod_wce_article_block_model where id_module=:id_module and present=1 order by label";
			$params=array();
			$params[':id_module']=$this->id_module;
			$res=$this->db->query($sql,$params);

			if ($this->db->numrows($res)) {
				while ($mod=$this->db->fetchrow($res)) {
					// chargement du fichier
					if (file_exists($basepath.$mod['label']."/block.tpl")) {
						$mod['content']=file_get_contents($basepath.$mod['label']."/block.tpl");
						$this->modelblocks[$mod['id']]=$mod;
					}
				}
			}

		}
	}

	public function loadRewritingUrl() {
		if (!(isset($_SESSION['dims']['URLREWRITE']))) {
			if (empty($this->urlrewrites_from) && isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) {
				$currentLang = new lang();
				$currentLang->open($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
				$urlLang = "";
				require_once DIMS_APP_PATH.'modules/wce/wiki/include/class_wce_lang.php';
				if(!$currentLang->isNew() && wce_lang::countAllFront() > 1)
					$urlLang = "/".$currentLang->fields['ref'];

				// construction des liens vers articles pour conversion
				// tableau de headings : $headings['list']
				global $wce_mode;
				global $adminedit;

				if (isset($_SESSION['dims']['wce_default_lg'])) $l1=$_SESSION['dims']['wce_default_lg']; else $l1=1;
				if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $l2=$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; else $l2=1;

				$ord=($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC';

				$select = "	SELECT	   h.*
							FROM		(
										SELECT		h.*
										FROM		".wce_heading::TABLE_NAME." h
										WHERE		h.id_module = :id_module
										AND			h.id_lang IN (:l1,:l2)
										ORDER BY	h.id_lang ".$ord."
										) as h
							GROUP BY	h.id";
				$params=array();
				$params[':l1']=$l1;
				$params[':l2']=$l2;
				$params[':id_module']=$this->id_module;

				$res=$this->db->query($select,$params);
				if ($this->db->numrows($res)>0) {
					if ($adminedit!='') $chrepl ="&adminedit=1"; else $chrepl = "";
					while ($art = $this->db->fetchrow($res)) {
						if($art['linkedpage'] != '' && $art['linkedpage'] > 0){
							$this->urlrewrites_from[]="/\/index\.php\?heading=".$art['id']."([^0-9])/";
							$this->urlrewrites_to[]="/index.php?articleid=".$art['linkedpage']."$1".$chrepl;
						}elseif($art['linkedheading'] != '' && $art['linkedheading'] > 0){
							$this->urlrewrites_from[]="/\/index\.php\?heading=".$art['id']."([^0-9])/";
							$this->urlrewrites_to[]="/index.php?heading=".$art['linkedheading']."$1".$chrepl;
						}else{
							$head = new wce_heading();
							$head->openFromResultSet($art);
							if(($idArt = $head->getFirstPage()) != '' && $idArt > 0){
								$art2 = new wce_article();
								$art2->open($idArt);
								$this->urlrewrites_from[]="/\/index\.php\?heading=".$art['id']."([^0-9])/";
								$this->urlrewrites_to[]="/index.php?articleid=".$art2->fields['id']."$1".$chrepl;
							}
						}
					}
				}

				$select =	"SELECT		a.*
							FROM		(
										SELECT		a.*
										FROM		".wce_article::TABLE_NAME." a
										WHERE		a.id_module = ".$this->fields['id_module']."
										AND			a.id_lang IN (:l1,:l2)
										ORDER BY	a.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."
										) as a
							GROUP BY	a.id";
				$params = array();
				$params[':l1'] = array('value'=>$_SESSION['dims']['wce_default_lg'],'type'=>PDO::PARAM_INT);
				$params[':l2'] = array('value'=>$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],'type'=>PDO::PARAM_INT);
				$res=$this->db->query($select,$params);
				if ($this->db->numrows($res)>0) {
					while ($art = $this->db->fetchrow($res)) {
						if ($art["urlrewrite"]!="" && isset($wce_mode) && $wce_mode!="render") {
							// lecture des heading
							// on a la liste des parents avec lui
							if (isset($headings['list'][$art['id_heading']]['parents'])) {
								$lsth=$headings['list'][$art['id_heading']]['parents'].";".$art['id_heading'];
								$lsth=explode(";",$lsth);
							}
							else {
								$lsth=array();
							}

							$chrepl=$urlLang;

							foreach ($lsth as $h) {
								if (isset($headings['list'][$h])) {
									$chpath=$headings['list'][$h]['urlrewrite'];
									if ($chpath!="") $chrepl.="/".$chpath;
								}
							}
							// on ajoute l'article
							//if ($chrepl!="")
							$chrepl.="/";
							$chrepl.=$art["urlrewrite"].".html";
							$this->arraypagerewrite[$art['id']]=$chrepl;

							//if ($wce_mode=="render") $rep_from[]="/index.php?wce_mode=render&articleid=".$art['id'];
							$this->urlrewrites_from[]="/\/index\.php\?articleid=".$art['id']."([^0-9])/";

							if ($adminedit!='') $chrepl.="?adminedit=1";
							$this->urlrewrites_to[]=$chrepl."$1";
						}
						else {
							if (isset($wce_mode) && $wce_mode=="render") {
								$this->urlrewrites_from[]="/\/index\.php\?articleid=".$art['id']."([^0-9])/";
								$chrepl="$urlLang/index.php?wce_mode=render&articleid=".$art['id'];
								if ($adminedit!='') $chrepl.="&adminedit=1";
								$this->urlrewrites_to[]=$chrepl."$1";
							}
							else {
								if (isset($adminedit) && $adminedit!='') {
									$this->urlrewrites_from[]="/\/index\.php\?articleid=".$art['id']."([^0-9])/";
									$chrepl="$urlLang/index.php?articleid=".$art['id']."&adminedit=1";
									$this->urlrewrites_to[]=$chrepl."$1";
								}
							}
						}
					}
				}
			}

			$_SESSION['dims']['URLREWRITE']['FROM']=$this->urlrewrites_from;
			$_SESSION['dims']['URLREWRITE']['TO']=$this->urlrewrites_to;
		}else {
			$this->urlrewrites_from=$_SESSION['dims']['URLREWRITE']['FROM'];
			$this->urlrewrites_to=$_SESSION['dims']['URLREWRITE']['TO'];
		}
	}

	public function replaceUrlContent($content) {
		if (empty($this->urlrewrites_from)) {
			$this->loadRewritingUrl();
		}
		// replace content
		return preg_replace($this->urlrewrites_from,$this->urlrewrites_to,$content);
	}

	public function getUrlFrom() {
		return $this->urlrewrites_from;
	}

	public function getUrlTo() {
		return $this->urlrewrites_to;
	}

	public function getArrayPageRewrite() {
		return $this->arraypagerewrite;
	}

	public function getLevelSite() {
		return $this->levelsite;
	}

	public function getArticleIDByObject($moduletype,$labelobject,&$extraparams, $idRecord = -1, &$articlesIds = array()) {
		$dims = dims::getInstance();
		$url='';

		$wcemods=$dims->getModuleByType('wce');
		$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

		if (!empty($wcemods)) {
			$modtype=$dims->getModuleByType($moduletype);

			if (!empty($modtype)) {
				$cur=current($modtype);
				$id_module_type=$cur['id'];
				$id_module=$cur['instanceid'];
				$instancename=$cur['instancename'];

				if ($id_module_type>0) {
					// recherche de l'id du wce_object depuis
					$id_wce_object=0;
					$id_article=0;
					$queryobj = "	SELECT	*
									FROM	dims_mb_wce_object
									WHERE	id_module_type = :id_module_type
									AND		label LIKE :label";
					$params = array();
					$params[':id_module_type'] = array('value'=>$id_module_type,'type'=>PDO::PARAM_INT);
					$params[':label'] = array('value'=>$labelobject,'type'=>PDO::PARAM_STR);
					$res=$this->db->query($queryobj,$params);

					if ($this->db->numrows($res)>0) {
						if ($ob = $this->db->fetchrow($res)) {
							$lstmodwce = array();
							$lstmodwce[] = 0;
							foreach($wcemods as $k=>$mod) {
								$lstmodwce[] = $mod['instanceid'];
							}

							$id_wce_object=$ob['id'];
							if($idRecord == -1){
								$pattern="[[".$id_wce_object.",".$id_module."/";
								$pattern2="[[ ".$id_wce_object.",".$id_module."/";
							}else{
								$pattern="[[".$id_wce_object.",".$id_module.",".$idRecord."/";
								$pattern2="[[ ".$id_wce_object.",".$id_module.",".$idRecord."/";
							}
							// on a tout ce qu'il faut, on peut rechercher l'objet parmis les differents wce
							// on doit regarder dans les contenus content.... ou blockcontent1,2,3,....
							$params = array();
							$select = "	SELECT	id,content1,content2,content3,content4,content5,content6,content7,content8,content9
										FROM	dims_mod_wce_article
										WHERE	id_module in (".$this->db->getParamsFromArray($lstmodwce,'id_module',$params).")";
							$res=$this->db->query($select,$params);

							if ($this->db->numrows($res)>0) {
								while ($art = $this->db->fetchrow($res)) {
									for ($i=1;$i<9;$i++) {
										if (strpos($art['content'.$i],$pattern)!==false || strpos($art['content'.$i],$pattern2)!==false) {
											// on a trouve l'article
											$id_article=$art['id'];
										}
									}
								}
							}

							if ($id_article==0) {
								// on regarde dans les blocs article
								$params = array();
								$select =	"SELECT		b.*
											FROM		dims_mod_wce_article_block as b
											INNER JOIN	dims_mod_wce_article as a
											ON			a.id=b.id_article
											AND			a.id_module in (".$this->db->getParamsFromArray($lstmodwce,'id_module',$params).")";

								$res=$this->db->query($select,$params);

								if ($this->db->numrows($res)>0) {
									while ($art = $this->db->fetchrow($res)) {
										for ($i=1;$i<9;$i++) {
											if (strpos($art['content'.$i],$pattern)!==false || strpos($art['content'.$i],$pattern2)!==false) {
												// on a trouve l'article
												$id_article=$art['id_article'];
												$id_lang=$art['id_lang'];
											}
										}
									}
								}
							}
							// si article trouve, verifie si article direct ou non
							// si oui proposition du lien, sinon construction de l'url avec articleid
							if ($id_article>0) {
								return $id_article;
							}
						}
					}
				}
			}
		}
		return null;
	}
	public function getArticleByObject($moduletype,$labelobject,&$extraparams, $idRecord = -1, &$articlesIds = array()) {
		$dims = dims::getInstance();
		$url='';

		$wcemods=$dims->getModuleByType('wce');
		$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

		if (!empty($wcemods)) {
			$modtype=$dims->getModuleByType($moduletype);

			if (!empty($modtype)) {
				$cur=current($modtype);
				$id_module_type=$cur['id'];
				$id_module=$cur['instanceid'];
				$instancename=$cur['instancename'];

				if ($id_module_type>0) {
					// recherche de l'id du wce_object depuis
					$id_wce_object=0;
					$id_article=0;
					$queryobj = "	SELECT	*
									FROM	dims_mb_wce_object
									WHERE	id_module_type = :id_module_type
									AND		label LIKE :label";
					$params = array();
					$params[':id_module_type'] = array('value'=>$id_module_type,'type'=>PDO::PARAM_INT);
					$params[':label'] = array('value'=>$labelobject,'type'=>PDO::PARAM_STR);

					$res=$this->db->query($queryobj,$params);

					if ($this->db->numrows($res)>0) {
						if ($ob = $this->db->fetchrow($res)) {
							$lstmodwce = array();
							$lstmodwce[] = 0;
							foreach($wcemods as $k=>$mod) {
								$lstmodwce[] = $mod['instanceid'];
							}

							$id_wce_object=$ob['id'];
							if($idRecord == -1){
								$pattern="[[".$id_wce_object.",".$id_module."/";
								$pattern2="[[ ".$id_wce_object.",".$id_module."/";
							}else{
								$pattern="[[".$id_wce_object.",".$id_module.",".$idRecord."/";
								$pattern2="[[ ".$id_wce_object.",".$id_module.",".$idRecord."/";
							}
							// on a tout ce qu'il faut, on peut rechercher l'objet parmis les differents wce
							// on doit regarder dans les contenus content.... ou blockcontent1,2,3,....
							$params = array();
							$select = "	SELECT	id,content1,content2,content3,content4,content5,content6,content7,content8,content9
										FROM	dims_mod_wce_article
										WHERE	id_module in (".$this->db->getParamsFromArray($lstmodwce,'id_module',$params).")";
							$res=$this->db->query($select,$params);

							if ($this->db->numrows($res)>0) {
								while ($art = $this->db->fetchrow($res)) {
									for ($i=1;$i<9;$i++) {
										if (strpos($art['content'.$i],$pattern)!==false || strpos($art['content'.$i],$pattern2)!==false) {
											// on a trouve l'article
											$id_article=$art['id'];
										}
									}
								}
							}

							if ($id_article==0) {
								// on regarde dans les blocs article
								$params = array();
								$select =	"SELECT		b.*
											FROM		dims_mod_wce_article_block as b
											INNER JOIN	dims_mod_wce_article as a
											ON			a.id=b.id_article
											AND			a.id_module in (".$this->db->getParamsFromArray($lstmodwce,'id_module',$params).")";

								$res=$this->db->query($select,$params);

								if ($this->db->numrows($res)>0) {
									while ($art = $this->db->fetchrow($res)) {
										for ($i=1;$i<9;$i++) {
											if (strpos($art['content'.$i],$pattern)!==false || strpos($art['content'.$i],$pattern2)!==false) {
												// on a trouve l'article
												$id_article=$art['id_article'];
												$id_lang=$art['id_lang'];
											}
										}
									}
								}
							}
							// si article trouve, verifie si article direct ou non
							// si oui proposition du lien, sinon construction de l'url avec articleid
							if ($id_article>0) {
								$articlesIds[] = $id_article;

								$art= new wce_article();
								$art->open($id_article, $id_lang);
								$id_workspace=$art->fields['id_workspace'];


								$sql = "SELECT			DISTINCT d.domain,d.ssl
										FROM			dims_domain as d
										INNER JOIN		dims_workspace_domain as wd
										ON				d.id=wd.id_domain and wd.access>=1
										AND				wd.id_workspace=:id_workspace
										AND				id_home_wce_article=:id_home_wce_article
										OR				id_home_wce_article=0
										ORDER BY		id_home_wce_article desc"; // prendre en premier le domaine concerne par l'artile trouve
								$params = array();
								$params[':id_workspace'] = array('value'=>$id_workspace,'type'=>PDO::PARAM_INT);
								$params[':id_home_wce_article'] = array('value'=>$id_article,'type'=>PDO::PARAM_INT);

								$ssl=false; // on recupere l'activation SSL du domaine trouve
								$res=$this->db->query($sql,$params);
								if ($this->db->numrows($res)>0) {
									if ($dom = $this->db->fetchrow($res)) {
										//$url=$dims->getProtocol().$dom['domain'];
										if ($dom['ssl']) $url="https://".$dom['domain'];
										else $url="http://".$dom['domain'];
									}
								}
								else {
									// on ajoute l'article id
									$extraparams="&articleid=".$id_article;

								}

								/*
									//Création de la listes des articles intégrant l'objet
									$articlesIds[] = $id_article;
								$res=$this->db->query("select d.domain from dims_mod_wce_article as a inner join dims_domain as d on d.id=a.id_redirect_domain and a.id=".$id_article);
								if ($this->db->numrows($res)>0) {
									if ($dom = $this->db->fetchrow($res)) {
									   $url=$dims->getProtocol().$dom['domain'];
									}
								}
								else {
									// on ajoute l'article id
									$extraparams="&articleid=".$id_article;
								}
															 */
							}
						}
					}
				}
			}
		}
		if ($url=='') {
			$url=$dims->getProtocol().$http_host;
		}
		return $url;
	}

	public function getDynamicObjects($standalone=false) {
		// listing des objets
		// gestion en mode front ou backoffice
		if ($_SESSION['dims']['mode'] != 'admin' && isset($_SESSION['dims']['wce_module_id']) && $_SESSION['dims']['wce_module_id']>0) {
			$sql="select * from dims_mod_wce_object where id_module=:id_module";
			$params = array();
			$params[':id_module'] = array('value'=>$_SESSION['dims']['wce_module_id'],'type'=>PDO::PARAM_INT);
		}else{
			$sql="select * from dims_mod_wce_object where id_module=:id_module";
			$params = array();
			$params[':id_module'] = array('value'=>$this->id_module,'type'=>PDO::PARAM_INT);
		}

		/*if ($standalone) {
			$sql.=" and template=''";
		}*/

		$res=$this->db->query($sql,$params);
		$lstobjects=array();

		if ($this->db->numrows($res)>0) {

			$color="";
			while ($ob=$this->db->fetchrow($res)) {
				$lstobjects[$ob['id']]=$ob;
			}
		}
		return $lstobjects;
	}

	/*
	* renvoie les slideshows
	*/
	public function getSlideShows() {
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow.php";
		$a_slideshows = array();
		$params = array();
		$params[':id_module'] = array('value'=>$this->id_module,'type'=>PDO::PARAM_INT);
		$rs = $this->db->query('SELECT	*
								FROM	'.wce_slideshow::TABLE_NAME.'
								WHERE	id_module = :id_module',$params);
		if ($this->db->numrows($rs)) {
			while ($row = $this->db->fetchrow($rs)) {
				$slide = new wce_slideshow();
				$slide->openFromResultSet($row);
				$a_slideshows[] = $slide;
			}
		}
		return $a_slideshows;
	}

	/*
	 * Fonction permettant de bien réécrire les liens dans les contenus
	 */
	public function getReplaceLinkContent($content='') {
		if(!isset($root_path)) $root_path = "";
		$rep_fromdeb = $rep_to = $rep_from = $rep_todeb = array();
		$rep_fromdeb[]= "'index.php";
		$rep_fromdeb[]= "\"index.php";

		$rep_todeb[]="'/index.php";
		$rep_todeb[]="\"/index.php";
		$content= str_replace($rep_fromdeb,$rep_todeb,$content);
		$rep_from=$this->getUrlFrom();
		$rep_to=$this->getUrlTo();

		$content = str_replace("./index.php",$root_path."/index.php",$content);
		$content = str_replace("./index-quick.php",$root_path."/index-quick.php",$content);
		$content = str_replace("./data/",$root_path."/data/",$content);
		return $content;
	}

	/*
	 * deprecated
	 */
	public function getBreves() {
		global $dims;
		$today = dims_createtimestamp();

		$sql= "	SELECT	*
				FROM	dims_mod_wce_article
				WHERE	id_module = :id_module
				AND		(timestp_published <= :timestp_published OR timestp_published = 0)
				AND		(timestp_unpublished >= :timestp_unpublished OR timestp_unpublished = 0)
				AND		id_heading=0";
		$params = array();
		$params[':id_module'] = array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT);
		$params[':timestp_published'] = array('value'=>$today,'type'=>PDO::PARAM_INT);
		$params[':timestp_unpublished'] = array('value'=>$today,'type'=>PDO::PARAM_INT);

		$res=$this->db->query($sql,$params);
		$lstobjects=array();

		if ($this->db->numrows($res)>0){
			while ($fields=$this->db->fetchrow($res)) {

				$path=realpath('.').'/data/articles/'.$fields['picto'];
				$webpath=$dims->getProtocol().$dims->getHttpHost().'/data/articles/'.$fields['picto'];
				$elem=array();
				$elem['path'] ='';
				$elem['pathactu'] = '';

				if ($fields['picto']!='' && file_exists($path)) {
					$ext = explode('.', $fields['picto']);
					$ext = strtolower($ext[count($ext)-1]);
					$webpathactu=$dims->getProtocol().$dims->getHttpHost().'/data/articles/art_'.$fields['id']."_500.".$ext;
					$elem['path'] = $webpath;
					$elem['pathactu'] = $webpathactu;
				}

				$elem['id'] = $fields['id'];
				$elem['title'] = $fields['title'];
				$elem['description'] = $fields['description'];
				$elem['year'] = substr($fields['lastupdate_timestp'],0, 4);
				$elem['month'] = $months[substr($fields['lastupdate_timestp'],4, 2)];//cf. include/global.php
				$elem['day'] = substr($fields['lastupdate_timestp'],6, 2);
				$elem['hour'] = substr($fields['lastupdate_timestp'],8, 2);
				$elem['minute'] = substr($fields['lastupdate_timestp'],10, 2);

				$elem['target']= "";

				//die($elem['target']);
				if($fields['id_article_link'] > 0)
				{
					$elem['link'] = $dims->getProtocol().$dims->getHttpHost()."/index.php?articleid=".$fields['id_article_link'];
					$elem['link_mode'] = _WCE_OBJECT_LINK_INTERNE;
				}
				else if ($fields['url']!='') {
					if (substr($fields['url'],0,4)!='http') {
						$fields['url']="http://".$fields['url'];
					}
					$elem['link']=str_replace("./", $dims->getProtocol().$dims->getHttpHost()."/",$fields['url']);
					$elem['link2'] = $elem['link'];//cyril : quel est le rôle du link ??
					$elem['link_mode'] = _WCE_OBJECT_LINK_EXTERNE;
				}
				if ($fields['url_window']) $elem['target']= " target='_blank' ";
				$lstobjects[]=$elem;
			}
		}
		return $lstobjects;
	}

	public function getHomePageUrl() {
		$homePage = '';
		$artIdHomePage = $this->getHomePageArtId();
		if ($artIdHomePage>0)
			$homePage = '/index.php?articleid='.$artIdHomePage;
		else // FIXME : Cette url ne correspond pas forcément à la première page du site
			$homePage = '/index.php?articleid=1&headingid=1';

		return $homePage;
	}

	public function getHomePageArtId() {
		$params = array();
		$params[':domain'] = array('value'=>$_SERVER['HTTP_HOST'],'type'=>PDO::PARAM_STR);
		$res = $this->db->query("SELECT id_home_wce_article FROM dims_domain WHERE (domain = :domain OR domain = '*') ",$params);
		if($this->db->numrows($res)){
			$fields = $this->db->fetchrow($res);
			return $fields['id_home_wce_article'];
		}
		else return -1;
	}

	public function libxml_display_error($error){
		$return = "<br/>\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "<b>Warning $error->code</b>: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "<b>Error $error->code</b>: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "<b>Fatal Error $error->code</b>: ";
				break;
		}
		$return .= trim($error->message);
		if ($error->file) {
			$return .=	  " in <b>$error->file</b>";
		}
		$return .= " on line <b>$error->line</b>\n";

		return $return;
	}

	public function libxml_display_errors() {
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			print $this->libxml_display_error($error);
		}
		libxml_clear_errors();
	}

	// save xml file
	public function saveSchema($path='',$model='',$id_bloc,$styleBlock) {
		$file=$path.$model.'.xml';
		$dom = new DOMDocument();

		if (file_exists($file)) {
			libxml_use_internal_errors(true);

			$dom->load($file);

			//$_SESSION['wce'][$_SESSION['dims']['moduleid']]['dom']=$dom;
			$schemapath=realpath('.').'/modules/wce/schema/'.$model.'.xsd';

			if (file_exists($schemapath)) {
				if($dom->schemaValidate($schemapath)) {
					$racine = $dom->documentElement;
					 $dimsbody = $dom->getElementsByTagName("dimsbody");
					foreach ($dimsbody as $body) {
						// call recursive blocks function
						$this->setRecursiveBlocks($body,$dom,$id_bloc,$styleBlock);
					}

					// save selected object
					$_SESSION['wce'][$_SESSION['dims']['moduleid']]['selectedobject']=$id_bloc;

					$version = $racine->getAttribute('version');
					$racine->SetAttribute('version',($version+1));
					$racine->SetAttribute('timestpmodify',date('YmdHis'));

				}
				$dom->save($path.$model.'.xml');
				chmod($path.$model.'.xml', octdec('777'));
				$_SESSION['wce'][$_SESSION['dims']['moduleid']]['dom']=$dom;
			}
			else {
				$this->libxml_display_errors();
				echo 'error';
			}
		 }
	}

	// chargement
	public function loadSchema($path='',$model='') {
		$file=$path.$model.'.xml';

		if (file_exists($file)) {
			$dom = new DOMDocument();

			libxml_use_internal_errors(true);

			$dom->load($file);

			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['dom']=$dom;
			$schemapath=realpath('.').'/modules/wce/schema/'.$model.'.xsd';

			if (file_exists($schemapath)) {
				if($dom->schemaValidate($schemapath)) {
					$racine = $dom->documentElement;
					//identifiant du type sur le site distant (couple site_id && ref)
					$version = $racine->getAttribute('version');

					$xml_timestp_modify = $racine->getAttribute('timestpmodify');
					$tpl_timestp_modify=0;

					if (file_exists($path.$model.'.tpl')) {
						 $tpl_timestp_modify=date("YmdHis.",filemtime($path.$model.'.tpl'));
					}

					if ($xml_timestp_modify>$tpl_timestp_modify || true) {
						$this->buildTemplateFile($path,$model,$tpl_timestp_modify,$dom);
					}
				}
			}
			else {
				$this->libxml_display_errors();
				echo 'error';
			}
		 }
	}

	// build .tpl file from xml + xsd files
	// Created by Pat : 2011-04-16
	private function buildTemplateFile($path,$model,$tpl_timestp_modify,$dom) {

		if (file_exists($path.$model.'.mdl')) {
			$content = file_get_contents($path.$model.'.mdl');
			$header=array();
			$body = array();
			$header['styles']= array();
			$header['javascript'] = array();
			$blocks = array();
			// Header process
			$dimsheader = $dom->getElementsByTagName("dimsheader");
			foreach( $dimsheader as $elem ) {
				$charset = $elem->getAttribute('charset');
				$additionnal_style = $elem->getAttribute('additionnal_style');
				if ($charset!='') {
					$header['charset'] = $charset;
				}
				else {
					if (!defined('_DIMS_ENCODING')) $header['charset'] = 'UTF-8';
					else $header['charset'] = _DIMS_ENCODING;
				}

				$header['additionnal_style'] = $additionnal_style;
			}

			$dimsstylesheet = $dom->getElementsByTagName("dimsstylesheet");
			foreach ($dimsstylesheet as $elem) {
				$ss = array();
				$ss['type']=$elem->getAttribute('type');
				$ss['href']=$elem->getAttribute('href');
				$ss['media']=$elem->getAttribute('media');
				$header['styles'][]=$ss;
			}

			$dimsjavascript = $dom->getElementsByTagName("dimsjavascript");
			foreach ($dimsjavascript as $elem) {
				$ss = array();
				$ss['src']=$elem->getAttribute('src');
				$ss['content']=$elem->getAttribute('content');
				$header['javascript'][]=$ss;
			}

			$dimsbody = $dom->getElementsByTagName("dimsbody");
			foreach ($dimsbody as $elem) {
				$body['class']=$elem->getAttribute('class');
				$body['style']=$elem->getAttribute('style');

				// call recursive blocks function
				$this->recursiveBlocks($elem,$dom,$blocks);
			}

			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['blocks']=$blocks;
			// construction des contenus remplaces
			// construction of header style
			$contentheader='><meta http-equiv="Content-Type" content="text/html; charset='.$header['charset'].'"/>'."\n";
			foreach ($header['styles'] as $ss) {
				$contentheader.='<link type="'.$ss['type'].'" rel="stylesheet" href="'.$ss['href'].'" media="'.$ss['media'].'" title="styles"/>'."\n";
			}

			foreach ($header['javascript'] as $ss) {
				if ($ss['src']!='') {
					$contentheader.='<script type="text/javascript" src="'.$ss['src'].'"></script>'."\n";
				}
				elseif ($ss['content']!='') {
					$contentheader.='<script type="text/javascript">'.$ss['content'].'</script>'."\n";
				}
			}

			if ($header['additionnal_style']!='') {
				$contentheader.='{literal}<style>'.$header['additionnal_style'].'</style>{/literal}'."\n";
			}

			$rep_from[]='{dimsheader}>';
			$rep_to[]=$contentheader;

			// construction of body style
			$body_replace='';
			if ($body['class']!='') {
				$body_replace.=' class="'.$body['class'].'"';
			}

			if ($body['style']!='') {
				$body_replace.=' style="'.$body['style'].'"';
			}
			$body_replace.='>'.$this->recursiveGetBlocks($blocks,$content);

			$rep_from[]='{dimsbody}>';
			$rep_to[]=$body_replace;
			$content = str_replace($rep_from,$rep_to,$content);
			file_put_contents($path.$model.'.tpl',$content);
			chmod($path, 0770);
		}
	}

	private function recursiveGetBlocks($blocks,&$content) {
		$result='';
		$childs_elem='';

		foreach($blocks as $elem) {
			$ct_elem='';
			if (isset($elem['childs']) && !empty($elem['childs'])) {
				$childs_elem=$this->recursiveGetBlocks($elem['childs'],$content);
			}

			if ($elem['id']!='') {
				$ct_elem.=' id="'.addslashes($elem['id']).'"';
			}

			if ($elem['class']!='') {
				$ct_elem.=' class="activedimsbloc '.addslashes($elem['class']).'"';
			}
			else {
				$ct_elem.=' class="activedimsbloc"';
			}
			if ($elem['style']!='') {
				$ct_elem.=' style="'.addslashes($elem['style']).'"';
			}

			$ct_elem.=">".$childs_elem;

			$rep_from='{'.$elem['type'].'.'.addslashes($elem['id']).'}>';
			if (strpos($content,$rep_from)===false) {
				// on a a completer le contenu
				$result.="<".$elem['type'].$ct_elem."</".$elem['type'].">";
			}
			else {
			   // on remplace dans $content
			   $content = str_replace($rep_from,trim($ct_elem),$content);
			}

		}
		return $result;
	}

	private function recursiveBlocks($elem,$dom,&$blocks,$first=0) {
		$bs = $elem->getElementsByTagName("dimsbloc");
		$id_parent=$elem->getAttribute('id');

		foreach ($bs as $el) {
			$idp=$el->parentNode->getAttribute('id');
			if ($id_parent==$idp) {
				$ind=sizeof($blocks);

				$blocks[$ind]['id']=$el->getAttribute('id');
				$blocks[$ind]['type']=$el->getAttribute('type');
				$blocks[$ind]['class']=$el->getAttribute('class');
				$blocks[$ind]['style']=$el->getAttribute('style');
				$blocks[$ind]['childs'] = array();

				$this->recursiveBlocks($el,$dom,$blocks[$ind]['childs']);
			}
		}
	}

	// collecte de la définition d'un bloc par son id
	public function getblockById($id_bloc,$blocks) {
		$block_return=false;

		foreach ($blocks as $bl) {
			if ($bl['id']==$id_bloc) {
			   return $bl;
			}
			elseif (isset($bl['childs']) && !empty($bl['childs'])) {
				$block_return=$this->getblockById($id_bloc,$bl['childs']);
			}
		}

		return $block_return;
	}

	private function setRecursiveBlocks($elem,&$dom,$id_bloc,$styleBlock) {
		$bs = $elem->getElementsByTagName("dimsbloc");
		$id_parent=$elem->getAttribute('id');

		foreach ($bs as $el) {
			$idp=$el->parentNode->getAttribute('id');
			if ($id_parent==$idp) {
				$id=$el->getAttribute('id');

				if ($id==$id_bloc) {
					$el->setAttribute('style',$styleBlock);
				}
				else {
					$this->setRecursiveBlocks($el,$dom,$id_bloc,$styleBlock);
				}
			}
		}
	}

	public function getJsonArticles() {
		$dims = dims::getInstance();
		$listarticle='[';

		$sql= "	SELECT		*
				FROM		dims_mod_wce_article
				WHERE		id_module=:id_module
				AND			id_heading=0
				AND			type=1
				ORDER BY	title";
		$params = array();
		$params[':id_module'] = array('value'=>$this->id_module,'type'=>PDO::PARAM_INT);

		$res=$this->db->query($sql,$params);
		$lstobjects=array();

		if ($this->db->numrows($res)>0){
			while ($fields=$this->db->fetchrow($res)) {
				$listarticle.="['".addslashes($fields['title'])."','/index.php?article=".$fields['id']."']";
			}
		}

		$listarticle.=']';

		return $listarticle;
	}

	/*
	 * Fonction permettant de renvoyer la langue par defaut
	 */
	public function getDefaultLanguage() {
		$defaultlang=0;

		$sql= "	SELECT		id
				FROM		dims_mod_wce_lang
				WHERE		id_module=:id_module
				AND			(`default`=1
				OR			ref='fr')
				ORDER BY	`default`";
		$params = array();
		$params[':id_module'] = array('value'=>$this->id_module,'type'=>PDO::PARAM_INT);

		$res=$this->db->query($sql,$params);
		$lstobjects=array();

		if ($this->db->numrows($res)>0){
			while ($fields=$this->db->fetchrow($res)) {
				$defaultlang=$fields['id'];
			}

		}

		return $defaultlang;
	}

}

?>
