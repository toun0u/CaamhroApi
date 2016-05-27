<?

/*
 *	Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *	This program is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 2 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */
require_once DIMS_APP_PATH."modules/wce/include/global.php";
require_once DIMS_APP_PATH."modules/wce/include/classes/class_heading.php";
require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_block.php";
require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";

class wce_article extends dims_data_object {

	const TABLE_NAME = 'dims_mod_wce_article';
	private $model;
	private $blocks;
	private $pagebreakblocks;
	private $nbelement;
	private $wce_site;
	const NB_LEVEL=3;

	function __construct($type = '') {
		parent::dims_data_object(self::TABLE_NAME,'id','id_lang');
		$this->model = "";
		$this->nbelement=19;
		$this->blocks = array();
	}

	function setWceSite($wcesite) {
		$this->wce_site=$wcesite;
	}

	public function getWceSite(){
		return $this->wce_site;
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
					foreach($lstwcemods as $wce){
						$this->wce_site= new wce_site ($this->db,$wce);
						$id_lang = $this->wce_site->getDefaultLanguage();
						parent::open($id,$id_lang);
						if(isset($this->fields['id_module']))
							break;
					}
				}
				else{
					$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
					$id_lang = $this->wce_site->getDefaultLanguage();
					parent::open($id,$id_lang);
				}
			}else{
				$id_lang = $this->wce_site->getDefaultLanguage();
				parent::open($id,$id_lang);
			}
		}else
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
				if ($this->fields['id_lang'] != $id_lang2){
					$def = new wce_article();
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

	function setid_object() {
	 $this->id_globalobject = _WCE_OBJECT_ARTICLE;
	}

	function settitle(){
	$this->title = $this->fields['title'];
	}
	function save($default = true) {
		$new = $this->isNew();
		if($new || empty($this->fields['timestp'])){
			$this->fields['timestp'] = dims_createtimestamp();
		}
		$this->fields['updated_by'] = $_SESSION['dims']['user']['id_contact'];
		if (!isset($this->fields['id_lang']) || (isset($this->fields['id_lang']) && ($this->fields['id_lang'] == '' || $this->fields['id_lang']<=0))){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
			if (!is_object($this->wce_site)){
				$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
			}
			$this->fields['id_lang'] = $this->wce_site->getDefaultLanguage();
		}

		// Il faut publier les images si présentes dans le content
		$regexp = '/src="('.str_replace(array('.','/'),array('\.','\/'),_DIMS_WEBPATHDATA).'(doc-[0-9]*\/[0-9]{8}\/)([0-9]+_[0-9]*\.[a-zA-Z]{2,3}))"/';
		$lstDoc = array();
		for($i=1;$i<=19;$i++){
			if(preg_match_all($regexp, $this->fields["content$i"], $matches) !== false){
				for($o=0;$o<count($matches[0]);$o++){
					$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
													'path'=>$matches[2][$o],
													'filename'=>$matches[3][$o]);
				}
			}
			if(preg_match_all($regexp, $this->fields["draftcontent$i"], $matches) !== false){
				for($o=0;$o<count($matches[0]);$o++){
					$lstDoc[$matches[1][$o]] = array('fullpath'=>$matches[1][$o],
													'path'=>$matches[2][$o],
													'filename'=>$matches[3][$o]);
				}
			}
		}
		foreach($lstDoc as $doc){
			if(!file_exists(DIMS_ROOT_PATH."www/data/".$doc['path']))
				dims_makedir(DIMS_ROOT_PATH."www/data/".$doc['path']);
			if(file_exists(DIMS_ROOT_PATH."data/".$doc['path'].$doc['filename']) && !file_exists(DIMS_ROOT_PATH."www/data/".$doc['path'].$doc['filename']))
				copy(DIMS_ROOT_PATH."data/".$doc['path'].$doc['filename'],DIMS_ROOT_PATH."www/data/".$doc['path'].$doc['filename']);
		}

		parent::save(_WCE_OBJECT_ARTICLE);
		// on update les contenus
		if(!$new)
			$this->updateInternalLinks();

		// on update les liens
		//$this->updateDocLinks();

		if($new && $default){
			include_once DIMS_APP_PATH . "modules/wce/include/classes/class_wce_site.php";
			if (!is_object($this->wce_site)){
				if (!isset($_SESSION['dims']['moduleid']) || (!(isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] > 0))){
					$lstwcemods=dims::getInstance()->getWceModules(true);
					$this->wce_site= new wce_site ($this->db,current($lstwcemods));
				}
				else
					$this->wce_site= new wce_site ($this->db,$_SESSION['dims']['moduleid']);
			}
			$defLang = $this->wce_site->getDefaultLanguage();
			if($defLang != $this->fields['id_lang']){
				$defArticle = new wce_article();
				$defArticle->open($this->fields['id'],$defLang);
				if($defArticle->isNew()){
					$defArticle->setWceSite($this->wce_site);
					$defArticle->fields = $this->fields;
					$defArticle->fields['id_lang'] = $defLang;
					$defArticle->save(false);
				}
			}
		}

		return $this->fields['id'];
	}

	function verifVersion() {
		global $db;

		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] == 0) {
			$rver = $db->query("select * from dims_mod_wce_article_version where articleid= :articleid " , array(
				':articleid' => $this->fields['id']
			));

			if ($db->numrows($rver) == 0) {

				require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_articleversion.php');
				$artversion = new wce_version();
				$artversion->fields['articleid'] = $this->fields['id'];
				$artversion->fields['type'] = "draft";
				$artversion->fields['version'] = "0";
				$artversion->fields['draftversion'] = "1";
				$artversion->fields['author'] = $this->fields['author'];
				$artversion->fields['id_module'] = $this->fields['id_module'];
				$artversion->fields['id_workspace'] = $this->fields['id_workspace'];
				$artversion->fields['id_user'] = $this->fields['id_user'];
				$artversion->fields['meta_description'] = $this->fields['meta_description'];
				$artversion->fields['meta_keywords'] = $this->fields['meta_keywords'];
				$artversion->fields['timestp_modify'] = dims_createtimestamp();

				for ($i = 1; $i < $this->nbelement; $i++) {
					if ($this->fields['draftcontent' . $i] == "" && $this->fields['content' . $i] != "")
						$artversion->fields['content' . $i] = $this->fields['content' . $i];
					else
						$artversion->fields['content' . $i] = $this->fields['draftcontent' . $i];
				}

				$artversion->save();
			}
		}
	}

	function isModify() {
		$result = false;
		// check if model with block or not
		if ($this->isBlock()) {
			foreach ($this->getBlocks() as $id => $block) {
				for ($i = 1; $i <= $this->nbelement; $i++) {
					if ($block['draftcontent' . $i] != $block['content' . $i]) {
						$result = true;
					}
				}
			}
		} else {
			for ($i = 1; $i <= $this->nbelement; $i++) {
				if (strcmp($this->fields['draftcontent' . $i], $this->fields['content' . $i]) != 0) {
					$result = true;
				}
			}
		}
		return $result;
	}

	//permet de mettre à jour le flag qui dit si tous les content sont identiques aux drafts
	public function setUpToDate($val){
		$this->fields['uptodate'] = $val;
	}

	public function isUptodate(){
		return $this->fields['uptodate'];
	}

	function delete($or = true) {
		$db = dims::getInstance()->getDb();
		if ($or){
			if ($this->fields['position'] > 0 && $this->fields['id_heading'] > 0){
				$params = array();
				$params[':position'] = array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT);
				$params[':id_heading'] = array('value'=>$this->fields['id_heading'],'type'=>PDO::PARAM_INT);
				$res = $db->query("	UPDATE	".self::TABLE_NAME."
									SET		position = position - 1
									WHERE	position > :position
									AND		id_heading = :id_heading",$params);
			}

			$params = array();
			$params[':articleid'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$rver = $db->query("delete from dims_mod_wce_article_version where articleid=:articleid",$params);

			// on supprime les reférences docs
			$params = array();
			$params[':articleid'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$rver = $db->query("delete from dims_mod_wce_article_doc where id_article=:articleid",$params);

			// on supprime les reférences blocs
			$params = array();
			$params[':articleid'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$rver = $db->query("delete from dims_mod_wce_article_block where id_article=:articleid",$params);

			foreach($this->getExternalLinks() as $art){
				$art->deleteLinks($this->fields['id']);
			}
			$sel = "SELECT	*
					FROM	".self::TABLE_NAME."
					WHERE	id = :id
					AND		id_lang != :id_lang";
			$params = array();
			$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$params[':id_lang'] = array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT);
			$res = $db->query($sel,$params);
			while($r = $db->fetchrow($res)){
				$art = new wce_article();
				$art->openFromResultSet($r);
				$art->delete(false);
			}
		}
		parent::delete(_WCE_OBJECT_ARTICLE);
	}

	public function updateModelPath() {
		$db = dims::getInstance()->getDb();

		$params = array();
		$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$params[':model'] = array('value'=>$this->fields['model'],'type'=>PDO::PARAM_STR);
		$res = $db->query("UPDATE ".self::TABLE_NAME." SET model=:model WHERE id = :id");
	}

	public function prePublish() {
		$db = dims::getInstance()->getDb();
		$params = array();
		$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$res = $db->query("UPDATE ".self::TABLE_NAME." SET prepublish='1' WHERE id = :id", $params);
	}

	public function publish($idLang) {
		$db = dims::getInstance()->getDb();
		require_once (DIMS_APP_PATH . 'modules/wce/include/classes/class_articleversion.php');
		require_once(DIMS_APP_PATH . 'modules/wce/include/classes/class_article_block_version.php');

		$isblock = false;

		// verification du modele
		if ($this->isBlock()) {
			$blocks = $this->getBlocks(false,$idLang);
			foreach ($blocks as $i => $block) {
				// on va regarder pour y mettre une version nouvelle que pour les lignes de blocks
				$select = "	SELECT	COUNT(version) as maxversion
							FROM	dims_mod_wce_article_block_version
							WHERE	blockid = :blockid
							AND		type='online'";
				$params = array();
				$params[':blockid'] = array('value'=>$block['id'],'type'=>PDO::PARAM_INT);
				$res = $db->query($select,$params);
				if ($db->numrows($res) > 0) {
					$fields = $db->fetchrow($res);
					$version = $fields['maxversion'] + 1;
				}
				else
					$version=1;
			}

			$draftversion = 0;
			$typeversion = "online";
			$this->fields['version'] = $version . "." . $draftversion;

			foreach ($blocks as $i => $block) {
				$artblockversion = new wce_block_version();
				$artblockversion->fields['blockid'] = $block['id'];
				$artblockversion->fields['type'] = $typeversion;
				$artblockversion->fields['version'] = $version;
				$artblockversion->fields['draftversion'] = $draftversion;
				$artblockversion->fields['author'] = $this->fields['author'];
				$artblockversion->fields['id_module'] = $this->fields['id_module'];
				$artblockversion->fields['id_workspace'] = $this->fields['id_workspace'];
				$artblockversion->fields['id_user'] = $this->fields['id_user'];
				$artblockversion->fields['timestp_modify'] = dims_createtimestamp();



				for ($i = 1; $i < $this->nbelement; $i++)
					$artblockversion->fields['content' . $i] = $block['draftcontent' . $i];

				$artblockversion->save();

				$artblock = new wce_block();

				$artblock->open($block['id'],$idLang);
				for ($i = 1; $i < $this->nbelement; $i++)
					$artblock->fields['content' . $i] = $artblock->fields['draftcontent' . $i];
				$artblock->fields['uptodate'] = 0;
				$artblock->save();
			}

			$this->fields['timestp_published'] = date('Ymd000000');//dims_createtimestamp();
			$this->fields['prepublish'] = 0; // on reinit


			$this->updateUrlRewrite();
			//$this->fields['prepublish'] = 0; // on reinit
			//
			// on sauve l'indice de version
			$this->save();
		} else {
			// calcul de la version courante
			$select = "	SELECT	COUNT(version) as maxversion
						FROM	dims_mod_wce_article_version
						WHERE	articleid = :articleid
						AND		type='online'";
			$params = array();
			$params[':articleid'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$res = $db->query($select,$params);
			if ($db->numrows($res) > 0) {
				$fields = $db->fetchrow($res);
				$version = $fields['maxversion'] + 1;
			}
			else
				$version=1;

			$draftversion = 0;
			$typeversion = "online";
			$this->fields['version'] = $version . "." . $draftversion;
			// d&eacute;finition de la version courante
			$artversion = new wce_version();
			$artversion->fields['articleid'] = $this->fields['id'];
			$artversion->fields['type'] = $typeversion;
			$artversion->fields['version'] = $version;
			$artversion->fields['draftversion'] = $draftversion;
			$artversion->fields['author'] = $this->fields['author'];
			$artversion->fields['id_module'] = $this->fields['id_module'];
			$artversion->fields['id_workspace'] = $this->fields['id_workspace'];
			$artversion->fields['id_user'] = $this->fields['id_user'];
			$artversion->fields['meta_description'] = $this->fields['meta_description'];
			$artversion->fields['meta_keywords'] = $this->fields['meta_keywords'];
			$artversion->fields['timestp_modify'] = dims_createtimestamp();

			for ($i = 1; $i < $this->nbelement; $i++)
				$artversion->fields['content' . $i] = $this->fields['draftcontent' . $i];

			$artversion->save();

			for($i=1; $i <= 19;$i++){
				$this->fields["content$i"] = $this->fields["draftcontent$i"];
			}
			$this->fields['uptodate'] = 1;
			/*$this->fields['content1'] = $this->fields['draftcontent1'];
			$this->fields['content2'] = $this->fields['draftcontent2'];
			$this->fields['content3'] = $this->fields['draftcontent3'];
			$this->fields['content4'] = $this->fields['draftcontent4'];
			$this->fields['content5'] = $this->fields['draftcontent5'];
			$this->fields['content6'] = $this->fields['draftcontent6'];
			$this->fields['content7'] = $this->fields['draftcontent7'];
			$this->fields['content8'] = $this->fields['draftcontent8'];
			$this->fields['content9'] = $this->fields['draftcontent9'];*/
			$this->fields['timestp_published'] = date('Ymd000000');
			$this->fields['prepublish'] = 0; // on reinit
			$this->updateUrlRewrite();
			$this->save();
		}
	}

	// check if block model is used for current article
	function isBlock() {
		if (isset($this->fields["model"])){
			$model = $this->fields["model"];
			if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$art_temp = new wce_article;

			$art_temp->open($this->fields["id_article_link"]);
			if ($art_temp->fields['id'] == $this->fields["id_article_link"]) {
				$model = $this->fields["model"];
			}
			}

			if (isset($this->fields["model"]) && $this->fields["model"]) {
				$modelcontent=$this->getModel();

				$resu = strpos($this->getModel(), "<BLOCK>");
			if ($resu === false) {
				$resu=strpos($modelcontent, "<DIMSSECTION");
				return ($resu === false) ? false : true;
			}else {
				return true;
			}

			} else {
			return false;
			}
		}else
			return false;
	}

	function isSection() {
		$model = $this->fields["model"];
		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$art_temp = new wce_article;

			$art_temp->open($this->fields["id_article_link"]);
			if ($art_temp->fields['id'] == $this->fields["id_article_link"]) {
				$model = $this->fields["model"];
			}
		}

		if (isset($this->fields["model"]) && $this->fields["model"]) {
		$modelcontent=$this->getModel();

			$resu = strpos($modelcontent, "<DIMSSECTION");
			return ($resu === false) ? false : true;
		} else {
			return false;
		}

	}

	function updatecount() {
		if (isset($this->fields['meter'])) {
			$this->fields['meter']++;
			$db = dims::getInstance()->getDb();

			$params = array();
			$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			//$params[':timestp_modify'] = array('value'=>dims_createtimestamp(),'type'=>PDO::PARAM_STR);
			$res = $db->query("	UPDATE	".self::TABLE_NAME."
								SET		meter = meter + 1
								WHERE	id	= :id",$params);
		}
	}

	function getDomainList() {
		$db = dims::getInstance()->getDb();
		$tablist = array();
		// recherche sur le workspaceid des domaines rattach�s
		$select = "	SELECT			d.domain
					FROM			dims_domain as d
					INNER JOIN		dims_workspace_domain as wd
					ON				d.id=wd.id_domain
					AND				wd.id_workspace = :id_workspace
					AND				wd.access>0";
		$params = array();
		$params[':id_workspace'] = array('value'=>$this->fields['id_workspace'],'type'=>PDO::PARAM_INT);

		$res = $db->query($select,$params);

		if ($db->numrows($res) > 0) {
			// boucle sur les �l�ments de r�sultats
			while ($dom = $db->fetchrow($res)) {
				$tablist[] = $dom;
			}
		}
		else
			$tablist[]['domain'] = $_SESSION['cste']['_WCE_NO_DOMAIN'];

		return $tablist;
	}

	function getUrlHeading() {
		$db = dims::getInstance()->getDb();

		$heading = new wce_heading();
		$heading->open($this->fields['id_heading']);

		// on a la liste des parents avec lui
		$lsth = explode(';',$heading->fields['parents']);
		$lsth[] = $heading->fields['id'];

		$tablist = array();
		// recherche sur le workspaceid des domaines rattach�s
		$params = array();
		$select = "	SELECT		id,urlrewrite
					FROM		dims_mod_wce_heading
					WHERE		id in (" .$db->getParamsFromArray($lsth,'id',$params). ")
					AND			urlrewrite<>''
					ORDER BY	depth";
		$params[':id_workspace'] = array('value'=>$this->fields['id_workspace'],'type'=>PDO::PARAM_INT);

		$res = $db->query($select,$params);

		if ($db->numrows($res) > 0) {
			// boucle sur les �l�ments de r�sultats
			while ($h = $db->fetchrow($res)) {
				$tablist[] = $h;
			}
		}
		return $tablist;
	}

	public function getAllUrls($string, $strict=true) {
		$types = array("src", "href", "background", "url");
		while (list(, $type) = each($types)) {
			$innerT = $strict ? '[a-z0-9:?=&@/._-]+?' : '.+?';
			preg_match_all("|" . $type . "[\=\(]+([\"'`])(" . $innerT . ")\\1|i", $string, $matches);
			$ret[$type] = $matches[2];
		}

		return $ret;
	}

	public function getUrls($string, $strict=true) {
		//$types = array("src", "href", "background");
		$types = array("href");
		while (list(, $type) = each($types)) {
			$innerT = $strict ? '[a-z0-9:?=&@/._-]+?' : '.+?';

			preg_match_all("|$type\=([\"'`])(" . $innerT . ")\\1|i", $string, $matches);

			$ret = $matches[2];
		}
		return $ret;
	}

	public function updateDocLinks() {
		// on delete ce que l'on connait
		$params = array();
		$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$this->db->query("DELETE FROM dims_mod_wce_article_doc WHERE id_article=:id_article",$params);
		$chupdate = '';
		$tabdocs = array();

		if ($this->isBlock()) {

			foreach ($this->getBlocks() as $id => $block) {
				for ($i = 1; $i <= $this->nbelement; $i++) {
					$urls = $this->getUrls($block['content' . $i], false);
					if (!empty($urls)) {
						foreach ($urls as $id => $url) {
							$pos = strpos($url, 'docfile_md5id=');
							if ($pos > 0) {
								$str = substr($url, $pos + 14);
								$tabdocs[$str] = "'" . $str . "'";
							}
						}
					}
				}
			}
		} else {
			for ($i = 1; $i <= $this->nbelement; $i++) {
				$urls = $this->getUrls($this->fields['content' . $i], false);
				if (!empty($urls)) {
					foreach ($urls as $id => $url) {
						$pos = strpos($url, 'docfile_md5id=');
						if ($pos > 0) {
							$str = substr($url, $pos + 14);
							$tabdocs[$str] = "'" . $str . "'";
						}
					}
				}
			}
		}

		// on traite maintenant l'enregistrement des documents dans les articles // vérification des problemes de doc
		if (!empty($tabdocs)) {
			$params = array();
			$sql = "SELECT		id
					FROM		dims_mod_doc_file
					WHERE		md5id LIKE (" . $this->db->getParamsFromArray($tabdocs,'md5id',$params). ")";

			$res = $this->db->query($sql,$params);

			$chupdate = array();
			$chupdate[] = $this->fields['id'];
			while ($doc = $this->db->fetchrow($res)) {
				$chupdate[] = $doc['id'];
			}

			// on a des correspondances, on  insert
			if (count($chupdate) > 1) {
				$params = array();
				//echo $chupdate."<br>";
				$this->db->query("insert into dims_mod_wce_article_doc values (".$this->db->getParamsFromArray($chupdate,'values',$params).")",$params);
			}
		}
	}

	function getPreview() {
		$db = dims::getInstance()->getDb();
		global $smarty;
		// construction du content du formulaire
		//ob_start();
		//$content = ob_get_contents();
		//ob_end_clean();
		if (substr($_SERVER['SERVER_PROTOCOL'], 0, 5) == "HTTP/")
			$rootpath = "http://";
		else
			$rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];
		//return $content;
	}

	function sendByMail($content, $from, $to, $spool_archive=false) {
		if ($content != "") {
			if ($spool_archive) {

				require_once(DIMS_APP_PATH . '/include/class_spool_mail.php');
				$spool_mail = new spool_mail();

				if (isset($from['0']['address']))
					$spool_mail->fields['from'] = $from['0']['address'];
				else
					$spool_mail->fields['from'] = $from;

				$spool_mail->fields['to'] = $to;
				$spool_mail->fields['content'] = $content;
				$spool_mail->fields['date_creation'] = dims_getdatetime();
				$spool_mail->fields['spool'] = 1;
				$spool_mail->save();
			}
			else {
				require_once DIMS_APP_PATH . "/include/functions/mail.php";

				$today = dims_createtimestamp();
				$dateday = dims_timestamp2local($today);

				dims_send_mail($from, $to, str_replace("{DATE}", $dateday['date'], $this->fields['title']), $content);
			}
			/*
			  # SEND THE EMAIL
			  ini_set('max_execution_time', 3600);

			  $a_email = array();
			  foreach ($_SESSION['webletter']['email'] as $id => $email) {
			  if (in_array($id, $listsub)) {
			  $mail_sent = mail( $email, $webletter->fields['subject'], $msg, $headers );
			  echo $mail_sent ? "Mail sent to $email<br/>" : "Mail failed for $email<br/>";
			  }
			  }
			 */
		}
	}

	public function getRootPath() {
		$rootpath = "";
		// traitement du protocole

		if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == "on")
				|| (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) == 'on')
				|| (isset($_SERVER['SCRIPT_URI']) && substr(strtolower($_SERVER['SCRIPT_URI']), 0, 6) == "https:")) {

			$rootpath = "https://";
		} else {
			$rootpath = "http://";
		}
		$rootpath.=$_SERVER['HTTP_HOST'];

		return($rootpath);
	}

	public function getUrlRewriting() {
		$db = dims::getInstance()->getDb();
		$path = "";

		// r�cup�ration du path courant
		$pathroot = $this->getRootPath();
		$path = $pathroot;
		if (isset($this->fields['urlrewrite']) && $this->fields['urlrewrite'] != '') {
			// r�cup�ration des headings + path +
			$heading = new wce_heading();
			$heading->open($this->fields['id_heading']);

			// on a la liste des parents avec lui

			$lsth = array();
			if (isset($heading->fields['parents']) && $heading->fields['parents']!='')
				$lsth = explode(';',$heading->fields['parents']);
			$lsth[] =  $heading->fields['id'];

			//$lsth = ( isset($heading->fields['parents']) ? $heading->fields['parents'] . ";" : '' ) . $heading->fields['id'];

			// recuperation des parents puis requete
			$params = array();
			$select = "	SELECT		id,urlrewrite
						FROM		dims_mod_wce_heading
						WHERE		id in (".$db->getParamsFromArray($lsth,'id',$params).")
						AND			urlrewrite<>''
						ORDER BY	depth";

			$res = $db->query($select,$params);

			if ($db->numrows($res) > 0) {
				// boucle sur les �l�ments de r�sultats
				while ($h = $db->fetchrow($res)) {
					$path.="/" . $h['urlrewrite'];
				}
			}
			$path.="/" . $this->fields['urlrewrite'] . ".html";
		} else {
			$path = $pathroot . "/index.php?articleid=" . $this->fields['id'];
		}
		return ($path);
	}

	// verification d'un lien symbolique
	public function verifyLinkedContent() {
		$db = dims::getInstance()->getDb();
		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$art_temp = new wce_article;
			$art_temp->open($this->fields["id_article_link"]);

			if (isset($art_temp->fields['content1'])) {
				for ($i = 1; $i < $this->nbelement; $i++)
					$this->fields['content' . $i] = $art_temp->fields['content' . $i];
			}
			// on prend aussi le model si il y a des blocs
			$this->fields["model"] = $art_temp->fields["model"];
			$this->fields["timestp_published"] = $art_temp->fields["timestp_published"];
		}
	}

	// deplacement d'un article : suppression de l'article du heading courant + ajout de le suivant
	public function moveto($headingid) {
		$db = dims::getInstance()->getDb();

		// on decremente les positions du heading courant
		$params = array();
		$params[':id_heading'] = array('value'=>$this->fields['id_heading'],'type'=>PDO::PARAM_INT);
		$params[':position'] = array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT);
		$res = $db->query("	UPDATE	".self::TABLE_NAME."
							SET		position = position - 1
							WHERE	position > :position
							AND		id_heading = :id_heading",$params);

		// on calcul la position
		$params = array();
		$params[':id_heading'] = array('value'=>$this->fields['id_heading'],'type'=>PDO::PARAM_INT);
		$respos = $db->query("	SELECT	MAX(position) as maxi
								FROM	".self::TABLE_NAME."
								WHERE	id_heading=:id_heading",$params);
		if ($db->numrows($respos) > 0) {
			$fresu = $db->fetchrow($respos);
			$maxi = $fresu['maxi'];
		}
		else
			$maxi=1;

		$this->fields['id_heading'] = $headingid;
		$this->fields['position'] = $maxi + 1;

		// on deplace l'article avec les variantes de langue
		$params = array();
		$params[':id_heading'] = array('value'=>$headingid,'type'=>PDO::PARAM_INT);
		$params[':position'] = array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT);
		$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$res = $db->query("	UPDATE	".self::TABLE_NAME."
							SET		id_heading = :id_heading,position=:position
							WHERE	id = :id",$params);
		//$this->save();
	}

	public function checkModel() {
		$db = dims::getInstance()->getDb();

		if ($this->fields["model"] != "") {
			if (file_exists(_WCE_MODELS_PATH . "/pages_publiques/" . $this->fields["model"] . "/page.tpl")) {
				$this->model = file_get_contents(_WCE_MODELS_PATH . "/pages_publiques/" . $this->fields["model"] . "/page.tpl");
				$array_from[]='{$page.TITLE}';
				$array_to[]=$this->fields['title'];

				if (isset($_SESSION['dims']['front_template_path']) && $_SESSION['dims']['front_template_path']!='') {
					$array_from[]='{$site.TEMPLATE_ROOT_PATH}';
					$array_to[]=$_SESSION['dims']['front_template_path'];
				}

				$this->model=str_replace($array_from,$array_to,$this->model);

			} else {
				echo "Template " . _WCE_MODELS_PATH . "/pages_publiques/" . $this->fields["model"] . "/page.tpl" . " manquant !";
			}
		}
	}

	public function getNbElements() {

		return $this->nbelement;
	}

	public function getModel($force=false) {
		if ($this->model == "" || $force) {
			$this->checkModel();
		}
		return $this->model;
	}


	/*
	 * fonction permettant le remplacement des sections de blocks entre eux
	 */
	public function replaceBlockSection($page='',$adminedit,$smarty) {
		$blockcontent='';
		// check page content
		if ($page=='') $page=$this->getModel();

		// recuperation des blocks
		$blocksSection=$this->getBlocksSection();
		// recuperation des modeles d'affichage
		$models=$this->wce_site->getBlockModels();

		// sections modeles
		$sections=$this->getSections();

		// chargement des objets
		$dynobjects=$this->wce_site->getDynamicObjects();

		foreach ($sections as $idsection => $section) {
		// on initialise
		$contentsection='';

		switch ($section['type']) {
			case 'object':
			// init du contenu d'objet
			$contentobject=$section['value'];
			if (isset($blocks[$idsection][1]['content1'])) $contentobject=$blocks[$idsection][1]['content1'];

			if ($adminedit) {
				$this->getEditContentObject($contentobject,$contentsection,$dynobjects,$smarty);
			}
			else {
				$this->getRenderContentObject($contentobject,$contentsection,$dynobjects,$smarty);
			}

			break;

			case 'text':
			// init des blocks si non defini
			$blocks=array();

			if (isset($blocksSection[$idsection])) {
				$blocks=$blocksSection[$idsection];
			}

			if ($adminedit) {
				$this->getEditContentBlocks($blocks,$contentsection,$idsection,$models);
			}
			else {
				$this->getRenderContentBlocks($blocks,$contentsection,$idsection,$models);
							global $subpages;
							if (!empty($subpages)) $smarty->assign('subpages', $subpages);
			}
			break;

		}

		// on va remplacer les differentes balises
		$page=str_replace($section['pattern'],$contentsection,$page);
		}

		// retour de la page editable
		return $page;
	}

	/*
	 * fonction permettant le remplacement de block entre eux
	 */
	public function replaceBlock($page='',$adminedit) {
		$blockcontent='';
		// check page content
		if ($page=='') $page=$this->getModel ();

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

		return str_replace("</BLOCK>", "", $page);
	}

	public function getAllBlocks($obj = false) {
		$this->blocks = array();
		$idart = $this->fields['id'];

		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$idart = $this->fields["id_article_link"];
		}

		$params = array();
		$params[':id_article'] = array('value'=>$idart,'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT);
		$res = $this->db->query("SELECT		*
				 FROM		dims_mod_wce_article_block
				 WHERE		id_article = :id_article
				 AND		id_lang = :id_lang
				 ORDER BY	position,id_lang",$params);

		if ($this->db->numrows($res) > 0) {
			if ($obj)
				while ($ob = $this->db->fetchrow($res)) {
					$bloc = new wce_block();
					$bloc->openFromResultSet($ob);
					$this->blocks[] = $bloc;
				}
			else
				while ($ob = $this->db->fetchrow($res)) {
					$this->blocks[] = $ob;
				}
		}
		return $this->blocks;
	}

	public function getBlocks($obj = false,$id_lang = 0) {
		if (empty($id_lang) || $id_lang <= 0){
			$id_lang = $this->fields['id_lang'];
			/*if (!is_object($this->wce_site)) $this->wce_site= new wce_site ($this->db,$this->fields['id_module']);
			$id_lang = $this->wce_site->getDefaultLanguage();*/
		}
		$this->blocks = array();
		$idart = $this->fields['id'];

		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$idart = $this->fields["id_article_link"];
		}

		$position=1;
		$params = array();
		$params[':id_article'] = array('value'=>$idart,'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$id_lang,'type'=>PDO::PARAM_INT);
		$res = $this->db->query("	SELECT		*
									FROM		dims_mod_wce_article_block
									WHERE		id_article = :id_article
									AND			id_lang = :id_lang
									ORDER BY	position,id_lang",$params);

		if ($this->db->numrows($res) > 0) {
			if ($obj){
				while ($ob = $this->db->fetchrow($res)) {
					$bloc = new wce_block();
					$bloc->openFromResultSet($ob);
					if ($bloc->fields['position']!=$position) {
						// on remet à jour
						$bloc->fields['position']=$position;
						//die("update dims_mod_wce_article set position=".$position." where id=".$bloc->fields['id']." and id_lang=".$id_lang);
						$params = array();
						$params[':id'] = array('value'=>$bloc->fields['id'],'type'=>PDO::PARAM_INT);
						$params[':position'] = array('value'=>$position,'type'=>PDO::PARAM_INT);
						$params[':id_lang'] = array('value'=>$id_lang,'type'=>PDO::PARAM_INT);
						$this->db->query("	UPDATE	".self::TABLE_NAME."
											SET		position=:position
											WHERE	id=:id
											AND		id_lang=:id_lang",$params);
					}
					$this->blocks[] = $bloc;
					$position++;
				}
			}else{
				while ($ob = $this->db->fetchrow($res)) {
					if ($ob['position']!=$position) {
						// on remet à jour
						$ob['position']=$position;
						//die("update dims_mod_wce_article set position=".$position." where id=".$ob['id']." and id_lang=".$id_lang);
						$params = array();
						$params[':id'] = array('value'=>$ob['id'],'type'=>PDO::PARAM_INT);
						$params[':position'] = array('value'=>$position,'type'=>PDO::PARAM_INT);
						$params[':id_lang'] = array('value'=>$id_lang,'type'=>PDO::PARAM_INT);
						$this->db->query("	UPDATE	".self::TABLE_NAME."
											SET		position=:position
											WHERE	id=:id
											AND		id_lang=:id_lang",$params);
					}
					$this->blocks[] = $ob;
					$position++;
				}
			}
		}
		return $this->blocks;
	}

	public function getHeadingBlocksSection(){
		$sel = "SELECT		*
				FROM		".self::TABLE_NAME."
				WHERE		id_heading = :getInstance
				ORDER BY	position";
		$params = array();
		$params[':id_heading'] = array('value'=>$this->fields['id_heading'],'type'=>PDO::PARAM_INT);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$art = new wce_article();
			$art->openFromResultSet($r);
			$elem = array();
			$elem['TITLE'] = $art->fields['title'];
			$elem['LINK'] = dims::getInstance()->getScriptEnv()."?articleid=".$art->fields['id'];
			$elem['SECTIONS'] = array();
			$art->getBlocksSection($elem['SECTIONS'],false);
			$lst[] = $elem;
		}
		return $lst;
	}
	/*
	 * fonction permettant de collecter les blocks par section
	 */
	public function getBlocksSection(&$tpl_sections='',$withnumber=true,$versionid=0,$id_lang = 0) {
		if (empty($id_lang) || $id_lang <= 0){
			// if (!is_object($this->wce_site)) $this->wce_site= new wce_site ($this->db,$this->fields['id_module']);
			// $id_lang = $this->wce_site->getDefaultLanguage();
			$id_lang = $this->fields['id_lang'];
		}
		$idart = $this->fields['id'];

		$rewrite = false;
		if (dims::getInstance()->getScriptEnv() != 'admin.php'){
			if(trim($this->fields['urlrewrite']) != ''){
				$rewrite = true;
				$scriptenv = $this->fields['urlrewrite'].'.html&';
			} else
				$scriptenv=dims::getInstance()->getScriptEnv()."?";
		} else
			$scriptenv=dims::getInstance()->getScriptEnv()."?";

		$pospoint=strpos(dims::getInstance()->getScriptEnv(),"?");

		if ($pospoint>0) $sep="&";
		else $sep="?";

		$wcelink='';

		$reqserver = array();
		if (isset($_SERVER['QUERY_STRING'])) {
			// on a directement la valeur dans les params
			$reqserver=explode("&",$_SERVER['QUERY_STRING']);
			foreach($reqserver as $idtab=>$elem) {
				if (substr($elem,0,12)=='WCE_section_' || substr($elem,0,10)=='urlrewrite' || substr($elem,0,11)=='pathrewrite' || substr($elem,0,9)=='headingid' || substr($elem,0,9)=='articleid') {
					unset($reqserver[$idtab]);
				}
			}
			if(!$rewrite){
				$reqserver[] = "articleid=".$this->fields['id'];
			}
		}

		if (count($reqserver) > 0)
			$wcelink.=$scriptenv.implode("&",$reqserver)."&";
		else
			$wcelink.=$scriptenv;

		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$idart = $this->fields["id_article_link"];
		}

		// on va switcher sur la version en cours de sélection
		if ($versionid>0) {
			$lstvers=$this->getListVersion($id_lang);
			//dims_print_r($lstvers);echo $versionid;die();
			if (in_array($versionid, $lstvers)) {
				// on charge les blocs de cette version
				$blocksversion=$this->loadBlocksFromVersion($versionid,$id_lang);
			}
		}

		$params = array();
		$params[':id_article'] = array('value'=>$idart,'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$id_lang,'type'=>PDO::PARAM_INT);
		$res = $this->db->query("SELECT		*
					 FROM		dims_mod_wce_article_block
					 WHERE		id_article = :id_article
					 AND		section > 0
					 AND		id_lang = :id_lang
					 ORDER BY	section,position",$params);

		$indicepagebreak=1;
		$indiceSections=array();
		$oldlevel=1;
		$nblevel=wce_article::NB_LEVEL;

		if ($this->db->numrows($res) > 0) {
			$pageSel = 1;
			if(isset($_GET)) {
				foreach($_GET as $key => $val) {
					if(strpos($key,'WCE_section_') !== false) {
						$pageSel = dims_load_securvalue($key,dims_const::_DIMS_NUM_INPUT,true,false);
					}
				}
			}
			while ($ob = $this->db->fetchrow($res)) {
				if (!isset($this->pagebreakblocks[$ob['section']])) $this->pagebreakblocks[$ob['section']]=0;

				$this->pagebreakblocks[$ob['section']] |= $ob['page_break'];

				if ($ob['page_break']) $indicepagebreak++;

				$updated=false;
				$elemupdate=array();

				if (!isset($indiceSections[$ob['section']])) {
					$indiceSections[$ob['section']]=array();
					for ($j=$ob['level']+1;$j<=$nblevel;$j++) $indiceSections[$ob['section']][$j]=0;

					// init du premier niveau à 1
					//$indiceSections[$ob['section']][1]=1;
				}

				// detection si on remonte d'un ou plusieurs niveaux on reinitialise
				if ($oldlevel>$ob['level']) {
					for ($j=$ob['level']+1;$j<=$nblevel;$j++) $indiceSections[$ob['section']][$j]=0;

					$oldlevel=$ob['level'];

					// on incremente le niveau courant
					if(isset($indiceSections[$ob['section']][$ob['level']]))
						$indiceSections[$ob['section']][$ob['level']]++;
				}
				else {
					if ($ob['level']>$oldlevel) {
						// on est au supérieur strict
						for ($j=$ob['level']+1;$j<=$nblevel;$j++) $indiceSections[$ob['section']][$j]=0;

						$oldlevel=$ob['level'];
					}
					// on incrémente le niveau courant de 1
					if( ! isset($indiceSections[$ob['section']][$ob['level']])) $indiceSections[$ob['section']][$ob['level']] = 0;
					$indiceSections[$ob['section']][$ob['level']]++;
				}

				// traitement des numérotations des sections (3 niveaux)
				if ($ob['level']<=0) {
					$indiceSections[$ob['section']][$ob['level']]=1;
					$elemupdate['level']=1;
					$updated=true;
				}

				// test si niveau au regard du level est ok
				for ($k=1;$k<=$nblevel;$k++) {
					if (isset($indiceSections[$ob['section']][$k]) && $indiceSections[$ob['section']][$k]!=$ob['l'.$k]) {
						$ob['l'.$k]=$indiceSections[$ob['section']][$k];
						$elemupdate['l'.$k]=$indiceSections[$ob['section']][$k];
						$updated=true;
					}
				}

				// on met a jour le block
				if ($updated) {
					$bblock = new wce_block();
					$bblock->open($ob['id']);
					foreach ($elemupdate as $nomelem=>$val) {
						$bblock->fields[$nomelem]=$val;
						$ob[$nomelem]=$val;

					}
					//$bblock->fields=$elemupdate;
					//$bblock->fields["id"] = $ob['id'];//Cyril - Sinon l'update dans DDO ne fonctionne pas des masses
					//dims_print_r($bblock->fields);
					//die();

					$bblock->save();

				}

				// on transforme le contenu si on a une version antérieure sélectionnee et existante

				if ($versionid >0 && isset($blocksversion[$ob['section']][$ob['id']])) {
					$ob['contentversion']=$blocksversion[$ob['section']][$ob['id']];
				}
				$this->blocks[$ob['section']][] = $ob;

				// on traite les lignes pour chaque section
				$elem=array();
				$elem['LABEL']=$ob['title'];
				$elem['POSITION']=$ob['position'];
				$elem['LEVEL']=$ob['level'];
				if ($withnumber)
					$elem['DECAL']=str_repeat('&nbsp;',$ob['level']);
				else
					$elem['DECAL']=str_repeat('&nbsp;',$ob['level']*2);

				if ($withnumber) {
					$elem['INDICE']=$ob['l1'];

					for ($jj=2;$jj<=$nblevel;$jj++) {
						if ($ob['level']>=$jj) $elem['INDICE'].=".".$ob['l'.$jj];
					}
				}
				else {
					$elem['INDICE']='';
				}

				$elem['ID']=$ob['id'];
				$elem['DISPLAY_TITLE'] = $ob['display_title'];
				$elem['LINK']=$wcelink.'WCE_section_'.$idart.'_'.$ob['section'].'='.$indicepagebreak."#".$ob['id'];
				$elem['POSITION']=$ob['position'];
				$elem['SEL'] = ($pageSel == $indicepagebreak);
				$elem['POSITION_PAGEBREAK']=$indicepagebreak;

				$tpl_sections[$ob['section']][]=$elem;
			}
		}

		return $this->blocks;
	}

	/*
	 * fonction permettant de renvoyer la présence ou non de pagebreak par section
	 */
	public function GetPageBreakBlocks() {
		return $this->pagebreakblocks;
	}
	/*
	 * fonction permettant de construire l'edition des contenus de blocks
	 */
	public function getEditContentObject($contenu,&$blockcontent,$dynobjects,$smarty) {
		$blockcontent='<div style="border: 1px dashed grey;overflow:hidden;padding:5px;"><div style="width:99%;clear:both;display:block;padding-bottom:2px;float:left;text-align:left;"></div>';

		if (file_exists(_WCE_MODELS_PATH."/objects/".$contenu) && $contenu!='') {
		ob_start();
		$smarty->display('file:'._WCE_MODELS_PATH."/objects/".$contenu);
		$blockcontent .= ob_get_contents();
		ob_end_clean();

		}
		$blockcontent.="</div>";
	}

	/*
	 * fonction permettant de generer le contenu de blocs
	 */
	public function getRenderContentObject($contenu,&$blockcontent,$dynobjects,$smarty) {

		if (file_exists(_WCE_MODELS_PATH."/objects/".$contenu)) {
		ob_start();
		$smarty->display('file:'._WCE_MODELS_PATH."/objects/".$contenu);
		$blockcontent = ob_get_contents();
		ob_end_clean();
		}
		//$blockcontent='{include file="'._WCE_MODELS_PATH."/objects/".$contenu.'"}';
	}


	/*
	 * fonction permettant de construire le rendu des contenus de blocks
	 */
	public function getRenderContentBlocks($blocks,&$blockcontent,$idsection,$models) {
		// recuperation des modeles d'affichage
		$models=$this->wce_site->getBlockModels();
		$num_subpage = dims_load_securvalue('num_subpage',dims_const::_DIMS_NUM_INPUT,true,true);

		$position=0;

		$subPages=false; // on desactive pour l'instant DSK
		$titleMissing	= false;
		foreach($blocks as $pos => $block) {
			if (isset($models[$block['id_model']])) {
				if($block['page_break']) $subPages = true;
				if(empty($block['title']) && $pos > 0) $titleMissing = true;
			}
		}
		// nous allons boucler sur l'ensemble des objets text et/ou object
		$blockcontent='';
		$key_subpage = 0;

		foreach($blocks as $pos=>$block) {
			if (isset($models[$block['id_model']])) {
				// construction des styles
				$float=(isset($block['float']) &&  $block['float']!='') ? $block['float'] : 'left';
				$width = (isset($block['width']) && $block['width']!='') ? $block['width'] : '100%';
				$height = (isset($block['height']) && $block['height']!='') ? $block['height'] : '';
				$display = (isset($block['display']) && $block['display']!='') ? $block['display'] : 'block';
				$padding = (isset($block['padding']) && $block['padding']!='') ? $block['padding'] : '0px';
				$margin = (isset($block['margin']) && $block['margin']!='') ? $block['margin'] : '0px 0px 0px 0px';
				$fontfamily = (isset($block['font-family']) && $block['font-family']!='') ? 'font-family:'.$block['font-family'] : '';
				$fontsize = (isset($block['font-size']) && $block['font-size']!='' && $block['font-size']>0) ? 'font-size:'.$block['font-size'] : '';
				$fontweight = (isset($block['font-weight']) && $block['font-weight']!='') ? 'font-weight:'.$block['font-weight'] : '';
				$color = (isset($block['color']) && $block['color']!='') ? 'color:'.$block['color'] : '';
				$backgroundcolor = (isset($block['background-color']) && $block['background-color']!='') ? 'background-color:'.$block['background-color'] : '';
				$borderstyle = (isset($block['border-style']) && $block['border-style']!='') ? 'border-style:'.$block['border-style'] : '';
				$bordercolor = (isset($block['border-color']) && $block['border-color']!='') ? 'border-color:'.$block['border-color'] : '';
				$bordersize = (isset($block['border-size']) && $block['border-size']!='') ? 'border-size:'.$block['border-size'] : '';

				$blockcontent.='<div style="'.$borderstyle.';'.$bordercolor.';'.$bordersize.';'.$backgroundcolor.';'.$color.';'.$fontfamily.';'.$fontsize.';'.$fontweight.';width:'.$width.';height:'.$height.';display:'.$display.';float:'.$float.';padding:'.$padding.';margin:'.$margin.';">';

				if($subPages && $block['page_break']) {
					$key_subpage++;

					if($titleMissing)
						$subpages['list'][$key_subpage]['title']	= (empty($block['title'])) ? 'Page '.$key_subpage : 'Page '.$key_subpage.' - '.$block['title'];
					else
						$subpages['list'][$key_subpage]['title']	= $block['title'];

					$subpages['list'][$key_subpage]['selected'] = ($key_subpage == $num_subpage) ? 'selected' : '';
					$subpages['list'][$key_subpage]['link']		= '/index.php?headingid='.$this->fields['id_heading'].'&articleid='.$this->fields['id'].'&num_subpage='.$key_subpage;
				}

				if(!$subPages || $key_subpage == $num_subpage) {
					$contenu=$models[$block['id_model']]['content'];


					// test si title
					if ($block['display_title']) {
						$blockcontent.="<h2 style=\"clear:both\"><balise id=\"".$block['id']."\">".$block['title']."</balise></h2>";
					}

					// affichage de la box pour editer, changer de position
					$ischange=false;

					for($j=1;$j<=$this->getNbElements();$j++) {
						if ($block['content'.$j]!=$block['draftcontent'.$j]) {
							$ischange=true;
						}
					}

					// on boucle sur l'ensemble des contenus <CONTENT1, 2,3, etc.>
					for($i=1;$i<=$this->getNbElements();$i++) {
						// detection de parametres eventuels
						$posstart=strpos($contenu,"<CONTENT$i>");
						//$dims->getModeOffice()!="web" || ($dims->getModeOffice()=="web" &&
						if ($ischange && isset($wce_mode) && $wce_mode!="online" && isset($adminedit) && $adminedit==1) {
							if ($block['draftcontent'.$i]=='') {
								$block['draftcontent'.$i]="&nbsp;";
							}
							$ctemp=$block['draftcontent'.$i];
						}
						else {
							if ($block['content'.$i]=='') {
								$block['content'.$i]="&nbsp;";
							}
							$ctemp=$block['content'.$i];
						}

						if ($posstart>=0 ) {
							$posend=strpos($contenu,"</CONTENT$i>");

							if (($posstart+strlen("<CONTENT$i>"))==$posend || ($posend==0))	{
								// cas ou pas de params en plus ou </content inexistant
								$contenu = str_replace("<CONTENT$i>",$ctemp, $contenu);
								$contenu = str_replace("</CONTENT$i>", "", $contenu);
							}
							else {
								// on  a qq chose
								$chparams=substr($page,$posstart+strlen("<CONTENT$i>"),$posend-($posstart+strlen("<CONTENT$i>")));
								$contenu = str_replace("<CONTENT$i>$chparams</CONTENT$i>", $ctemp, $contenu);
								$contenu = str_replace("</CONTENT$i>", "", $contenu);
							}
						}
					}

				}
				$blockcontent.= $contenu.'</div>';
			}
		}
		if(!empty($subpages)) {
			$key_subpage = 0;
			$block = $blocks[0];

			$first_bloc['title']	= (empty($block['title'])) ? $this->fields['title'] : $block['title'];
			$first_bloc['selected'] = ($key_subpage == $num_subpage) ? 'selected' : '';
			$first_bloc['link']	= '/index.php?headingid='.$this->fields['id_heading'].'&articleid='.$this->fields['id'].'&num_subpage='.$key_subpage;
			array_unshift($subpages['list'], $first_bloc);

			$total_subpages = count($subpages['list']);

			$subpages['prev'] = array('link' => '', 'title' => '');
			$subpages['next'] = array('link' => '', 'title' => '');

			if($num_subpage != 0) {
				$subpages['prev']['link']  = '/index.php?headingid='.$this->fields['id_heading'].'&articleid='.$this->fields['id'].'&num_subpage='.($num_subpage-1);
				$subpages['prev']['title'] = $subpages['list'][$num_subpage-1]['title'];
			}

			if($num_subpage+1 != $total_subpages) {
				$subpages['next']['link']  = '/index.php?headingid='.$this->fields['id_heading'].'&articleid='.$this->fields['id'].'&num_subpage='.($num_subpage+1);
				$subpages['next']['title'] = $subpages['list'][$num_subpage+1]['title'];
			}

			$subpages['first']	= '/index.php?headingid='.$this->fields['id_heading'].'&articleid='.$this->fields['id'].'&num_subpage=0';
			$subpages['last']	= '/index.php?headingid='.$this->fields['id_heading'].'&articleid='.$this->fields['id'].'&num_subpage='.($total_subpages-1);
			$subpages['total']	= $total_subpages;
			$subpages['current']= intval($num_subpage);
		}

	}

	/*
	 * fonction permettant de construire l'edition des contenus de blocks
	 */
	public function getEditContentBlocks($blocks,&$blockcontent,$idsection,$models) {

		// recuperation des modeles d'affichage
		$models=$this->wce_site->getBlockModels();

		$position=0;

		// nous allons boucler sur l'ensemble des objets text et/ou object
		$blockcontent='<div style="border: 1px dashed grey;overflow:hidden;padding:5px;"><div style="width:99%;clear:both;display:block;padding-bottom:2px;float:left;text-align:left;"><a title="Ajouter une section" href="javascript:void(0);" onclick="javascript:window.parent.wceAddBlock('.$idsection.')"><img alt="Ajouter une section" src="/common/modules/wce/img/add_section.png" border="0"></a></div>';

		foreach($blocks as $pos=>$block) {
			$position++;
			// verification de la position des blocs
			if ($block['position']!=$position) {
				$bblock = new wce_block();
				$bblock->open($block['id'],$block['id_lang']);
				$bblock->fields['position']=$position;
				$bblock->save();

				// on actualise la structure
				$blocks[$pos]['position']=$position;
				$block['position']=$position;
			}

			if (isset($models[$block['id_model']])) {
				$contenu=$models[$block['id_model']]['content'];
				$maxblock=0;

				// affichage de la box pour editer, changer de position
				$ischange=false;

				for($j=1;$j<=$this->getNbElements();$j++) {
					if ($block['content'.$j]!=$block['draftcontent'.$j]) {
						$ischange=true;
					}

					if (!(strpos($contenu,"<CONTENT".$j.">")===false)) {
						$maxblock=$j;
					}
				}

				// construction des styles
				$float=($block['float']!='') ? $block['float'] : 'left';
				$width = ($block['width']!='') ? $block['width'] : '98%';
				$height = ($block['height']!='') ? $block['height'] : '';
				$display = ($block['display']!='') ? $block['display'] : 'block';
				$padding = ($block['padding']!='') ? $block['padding'] : '1px';
				$margin = ($block['margin']!='') ? $block['margin'] : '5px 0px 0px 0px';
				$fontfamily = ($block['font-family']!='') ? 'font-family:'.$block['font-family'] : '';
				$fontsize = ($block['font-size']!='' && $block['font-size']>0) ? 'font-size:'.$block['font-size'] : '';
				$fontweight = ($block['font-weight']!='') ? 'font-weight:'.$block['font-weight'] : '';
				$color = ($block['color']!='') ? 'color:'.$block['color'] : '';
				$backgroundcolor = ($block['background-color']!='') ? 'background-color:'.$block['background-color'] : '';
				$borderstyle = ($block['border-style']!='') ? 'border-style:'.$block['border-style'] : '';
				$bordercolor = ($block['border-color']!='') ? 'border-color:'.$block['border-color'] : '';
				$bordersize = ($block['border-size']!='') ? 'border-size:'.$block['border-size'] : '';

				$blockcontent.='<div style="border: dashed 1px #AAAAAA;'.$borderstyle.';'.$bordercolor.';'.$bordersize.';'.$backgroundcolor.';'.$color.';'.$fontfamily.';'.$fontsize.';'.$fontweight.';width:'.$width.';height:'.$height.';display:'.$display.';float:'.$float.';padding:'.$padding.';margin:'.$margin.';">';

				$blockcontent.='<div style="clear:both;display:block;float:left;font-size:12px;color:#AAAAAA;width:99%;">'.$block['title'];
				// on place le bouton edition + status
				if ($ischange) {
					$blockcontent.='&nbsp;<img src="/common/modules/wce/img/ico_wait.png" "'.$_SESSION['cste']['_MODIFY'].'">';
				}
				else {
					$blockcontent.='&nbsp;<img src="/common/modules/wce/img/ico_yes.png">';
				}

				// on ajoute le bouton de modification
				$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceModifBlock('.$block['id'].');"><img src="./common/img/edit.png" border="0"></a>';

				// on ajoute le bouton de modification des proprietes avancees
				//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceModifBlockStyles('.$block['id'].');"><img src="./common/img/move.png" border="0"></a>';
				//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceSupBlock('.$block['id'].');"><img src="./common/img/delete.png" border="0"></a>';

				// on peut supprimer l'article
				$blockcontent.="&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:window.parent.dims_confirmlink('/admin.php?op=manage_block&action=delete_block&block_id=".$block['id']."','Etes-vous sur de vouloir supprimer ce bloc ?')\"><img border=\"0\" src=\"./common/img/delete.png\"/></a>";

				if($block['page_break'])
					$blockcontent .= '<span style="font-weight: bold; color: #7D7D7D;"> -- PAGE BREAK -- </span>';

				$blockcontent.='</div>';

				if($block['page_break'])
					$blockcontent .= '<span style="font-weight: bold; color: #7D7D7D;"> -- PAGE BREAK -- </span>';

				$blockcontent.='</div>';

				if ($block['display_title']) {
					$blockcontent.="<h2 style=\"clear:both\"><balise id=\"".$block['id']."\">".$block['title']."</balise></h2>";
				}

				// on boucle sur l'ensemble des contenus <CONTENT1, 2,3, etc.>
				for($i=1;$i<=10;$i++) {
					// detection de parametres eventuels
					$posstart=strpos($contenu,"<CONTENT$i>");

					$ctemp='<div style="border: dashed 1px #FF2222;clear:both;overflow:auto;"><div style="float:left;"><a href="javascript:void(0);" onclick="window.parent.wceModifBlockContent('.$block['id'].','.$i.');"><img src="./common/img/edit.png" border="0"></a>';

					// on regarde la position pour eventuellement inverser les contenus
					if ($i>1) {
						$ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(0,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_left.gif" border="0"></a>';
					}

					if ($i<$maxblock) {
						$ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(1,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_right.gif" border="0"></a>';
					}

					if ($ischange) {
						if ($block['draftcontent'.$i]=='') {
							$block['draftcontent'.$i]="&nbsp;";
						}

						$ctemp.='</div>'.$block['draftcontent'.$i].'</div>';
					}
					else {
						if ($block['content'.$i]=='') {
							$block['content'.$i]="&nbsp;";
						}
						$ctemp.='</div>'.$block['content'.$i].'</div>';
					}

					if ($posstart>=0 ) {
						$posend=strpos($contenu,"</CONTENT$i>");

						if (($posstart+strlen("<CONTENT$i>"))==$posend || ($posend==0))	{
							// cas ou pas de params en plus ou </content inexistant

							$contenu = str_replace("<CONTENT$i>",$ctemp, $contenu);
							$contenu = str_replace("</CONTENT$i>", "", $contenu);
						}
						else {
							// on  a qq chose
							$chparams=substr($page,$posstart+strlen("<CONTENT$i>"),$posend-($posstart+strlen("<CONTENT$i>")));
							$contenu = str_replace("<CONTENT$i>$chparams</CONTENT$i>", $ctemp, $contenu);
							$contenu = str_replace("</CONTENT$i>", "", $contenu);
						}

					}
				}

				$blockcontent.= $contenu.'</div>';
			}
		}
		$blockcontent.='</div>';
	}


	/*
	 * fonction de recupération des sections d'un modele courant
	 *
	 */

	function getSections() {
		$sections=array();

		$content=$this->getModel();

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

					// on a bien un element a stocker
					if ($id>0) {
						$sections[$id]=$struct;

						// creation de la structure des multipages si non defini
						if (!isset($_SESSION['dims']['wcecurrentsections'][$this->fields['id']])) {
						$_SESSION['dims']['wcecurrentsections'][$this->fields['id']]=array();
						}

						// init des sections courantes
						if (!isset($_SESSION['dims']['wcecurrentsections'][$this->fields['id']][$id]))
						$_SESSION['dims']['wcecurrentsections'][$this->fields['id']][$id]=0; // pas de page suivante
					}
				}
			}
		}
		return $sections;
	}

	function getContentToXml($id_lang = 1) {

		if ($id_lang < 1) $id_lang = 1;

		$idart = $this->fields['id'];


		if (isset($this->fields["id_article_link"]) && $this->fields["id_article_link"] > 0) {
			$idart = $this->fields["id_article_link"];
		}

		// construction du flux XML
		header("Content-type: text/xml");
		header('Content-Disposition: attachment; filename="article_'.$this->fields['id'].'.xml"');

		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo "<dims_article>";

		$params = array();
		$params[':id_article'] = array('value'=>$idart,'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$id_lang,'type'=>PDO::PARAM_INT);
		$res = $this->db->query("SELECT		*
				 FROM		dims_mod_wce_article_block
				 WHERE		id_article = :id_article
				 AND		id_lang = :id_lang
				 ORDER BY	section,position",$params);

		$id_section_cour=0;
		if ($this->db->numrows($res) > 0) {
			while ($ob = $this->db->fetchrow($res)) {
				if ($id_section_cour!=$ob['section']) {

					if ($id_section_cour>0) echo "</section>";
					// on créé la section
					echo "<section id =\"".$ob['section']."\">";

					$id_section_cour=$ob['section'];
				}
				// on genère le bloc
				for ($i=1;$i<20;$i++) {
					if ($i==1) $title="title=\"".  str_replace('"','&quot;',$ob['title'])."\"";
					else $title='';
					$bloc= "<block id=\"".$ob['id']."_".$i."\" ".$title."><![CDATA[";
					$displaybloc=false;

					if ($ob['content'.$i]!=$ob['draftcontent'.$i]) {
						// on affiche le draft
						if (trim($ob['draftcontent'.$i])!="") {
							$bloc.= $ob['draftcontent'.$i];
							$displaybloc=true;
						}
					}
					else {
						// le content
						if (trim($ob['content'.$i])!="") {
							$bloc.= $ob['content'.$i];
							$displaybloc=true;
						}
					}
					$bloc.= "]]></block>";

					// affichage du bloc
					if ($displaybloc) {
						echo $bloc;
					}
				}

			}
		}

		if ($id_section_cour>0) echo "</section>";

		echo "</dims_article>";

		die();
	}

	public function setContentXml($file,$id_lang,$deletePrev = false){
		if ($deletePrev){
			$sel = "SELECT	*
					FROM	".wce_block::TABLE_NAME."
					WHERE	id_article = :id_article
					AND		id_lang = :id_lang";
			$params = array();
			$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$params[':id_lang'] = array('value'=>$id_lang,'type'=>PDO::PARAM_INT);
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,$params);
			while($r = $db->fetchrow($res)){
				$b = new wce_block();
				$b->openFromResultSet($r);
				$b->delete();
			}
		}
		$xml = simplexml_load_file($file);
		if (!is_object($this->wce_site)) $this->wce_site= new wce_site ($this->db,$this->fields['id_module']);
		$currentlang=$this->wce_site->getDefaultLanguage();
		foreach($xml->children() as $section){
			$attr = $section->attributes();
			$id_section = $attr['id'];
			$prevId = 0;
			$bloc = new wce_block();
			$bloc->init_description();
			$bloc->fields['id_lang'] = $id_lang;
			$bloc->fields['id_article'] = $this->fields['id'];
			foreach($section->children() as $block){
				$attributes = $block->attributes();
				$ids = explode('_',$attributes['id']);
				if ($prevId != $ids[0]){
					if ($bloc->fields['id'] > 0)
						$bloc->save();
					$prevId = $ids[0];
					$oldBlock = new wce_block();
					$oldBlock->init_description();
					$oldBlock->open($ids[0],$currentlang);
					$bloc = new wce_block();
					$bloc->init_description();
					$bloc->fields = $oldBlock->fields;
					$bloc->fields['id_lang'] = $id_lang;
					$bloc->fields['id_article'] = $this->fields['id'];
					$bloc->setugm();
					$bloc->fields['uptodate'] = 0;
					for($i=1;$i<=19;$i++){
						$bloc->fields["content$i"] = "";
						$bloc->fields["draftcontent$i"] = "";
					}
					$bloc->fields["title"] = str_replace('&quot;','"',$attributes['title']);
				}
				$bloc->fields["draftcontent".$ids[1]] = html_entity_decode($block);
			}
			$bloc->save();
		}
	}

	function getRewriteLang(){
		$tmps = dims_createtimestamp();
		include_once DIMS_APP_PATH."modules/wce/wiki/include/class_wce_lang.php";
		$sql = "SELECT		l.id, a.urlrewrite
				FROM		".wce_lang::TABLE_NAME." l
				INNER JOIN	".self::TABLE_NAME." a
				ON			a.id_lang = l.id
				WHERE		a.id = :idarticle
				AND			(a.timestp_published <= :timestamp OR a.timestp_published = 0)
				AND			(a.timestp_unpublished >= :timestamp OR a.timestp_unpublished = 0)
				AND			l.is_active = 1
				AND			l.id_module = a.id_module";
		$params = array();
		$params[':idarticle'] = array('value' => $this->fields['id'], 'type' => PDO::PARAM_INT);
		$params[':timestamp'] = array('value' => $tmps, 'type' => PDO::PARAM_INT);
		$res = $this->db->query($sql, $params);
		$lst = array();
		while ($r = $this->db->fetchrow($res)) {
			$lst[$r['id']] = $r['urlrewrite'];
		}
		return $lst;
	}

	public function valide($idLang){
		$this->fields['uptodate'] = 1;
		for($i=1; $i <= 19;$i++){
			$this->fields["content$i"] = $this->fields["draftcontent$i"];
		}
		$this->save();
		foreach($this->getBlocks(true,$idLang) as $bloc)
			$bloc->valide();
	}

	public static function getLastMessages($id_user, $limit = null){
		$db = dims::getInstance()->getDb();

		$limitation = '';
		$params = array();
		if( ! is_null($limit) ){
			$limitation = ' LIMIT 0,:limit';
			$params[':limit'] = array('value'=>$limit,'type'=>PDO::PARAM_INT);
		}
		$params[':id_user'] = array('value'=>$id_user,'type'=>PDO::PARAM_INT);

		$res = $db->query("	SELECT		*
							FROM		".todo::TABLE_NAME." t
							INNER JOIN	".self::TABLE_NAME." a
							ON			a.id_globalobject = t.id_globalobject_ref
							AND			a.id_user = :id_user
							INNER JOIN	dims_globalobject o
							ON			o.id = t.id_globalobject_ref
							LEFT JOIN	dims_user u
							ON			t.id_user = u.id
							LEFT JOIN	dims_mod_business_contact c
							ON			c.id = u.id_contact
							WHERE		t.state = ".todo::TODO_STATE_RELEASED."
							AND			t.considered_as = ".todo::TODO_SIMPLE_MESSAGE."
							AND			t.id_parent = 0
							ORDER BY	t.timestp_create DESC ". $limitation ,$params);

		$todos = array();
		$separation = $db->split_resultset($res);

		foreach ($separation as $tab) {
			$todo = new todo();
			$todo->openFromResultSet($tab['t']);
			$todo->initDestinataires();
				$contact = new contact();
				$contact->init_description();
				$contact->setugm();
				if( !empty($tab['c']['id']) ){
				$contact->openFromResultSet($tab['c']);
			}

			$gobject = new dims_globalobject();
			$gobject->openFromResultSet($tab['o']);

			$todo->setLightAttribute('creator', $contact);
			$todo->setLightAttribute('article_title', $tab['a']['title']);
			$todo->setLightAttribute('gobject', $gobject);

			$todos[] = $todo;
		}

	return $todos;

	}

	/*
	 * Fonction permettant de récupérer les versions
	 */
	public function getListVersion($currentlang=0) {
		$lstversions=array();

		if ($currentlang==0) {
			if (!is_object($this->wce_site)) $this->wce_site= new wce_site ($this->db,$this->fields['id_module']);

			$currentlang=$this->wce_site->getDefaultLanguage();
		}

		// si on a des blocs
		if ($this->isBlock()) {
			$sql= " SELECT		DISTINCT bv.version
					FROM		dims_mod_wce_article_block_version as bv
					INNER JOIN	dims_mod_wce_article_block as ab
					ON			ab.id=bv.blockid
					AND			ab.id_article = :id_article
					AND			ab.id_lang = :id_lang
					ORDER BY	bv.version DESC";
			$params = array();
			$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$params[':id_lang'] = array('value'=>$currentlang,'type'=>PDO::PARAM_INT);
			$res = $this->db->query($sql,$params);

			if ($this->db->numrows($res) > 0) {
				while ($f=$this->db->fetchrow($res)) {
					$lstversions[]=$f['version'];
				}
			}
		}

		return($lstversions);
	}
	/*
	 * Fonction permettant de lister les versions par langues par rapport aux blocs
	 */
	public function getListArticleLangVersion() {
		$lst = array();
		if(!$this->isNew()){
			require_once DIMS_APP_PATH."modules/wce/wiki/include/class_wce_lang.php";
			$sql = "SELECT		DISTINCT l.*
					FROM		".wce_lang::TABLE_NAME." l
					INNER JOIN	dims_mod_wce_article_block b
					ON			b.id_lang = l.id
					WHERE		b.id_article = :id_article";
			$params = array();
			$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$res = $this->db->query($sql,$params);
			while ($r = $this->db->fetchrow($res)) {
				$lang = new wce_lang();
				$lang->openFromResultSet($r);
				$lst[$r['id']] = $lang;
			}
		}
		return $lst;
	}

	/*
	 * Fonction permettant de lister les versions par langues par rapport aux blocs
	 */
	public function getListArticleLangVersionWCE() {
		$lst = array();
		if(!$this->isNew()){
			require_once DIMS_APP_PATH."modules/wce/wiki/include/class_wce_lang.php";
			$sql = "SELECT		DISTINCT l.*
					FROM		".wce_lang::TABLE_NAME." l
					INNER JOIN	".self::TABLE_NAME." a
					ON			a.id_lang = l.id
					WHERE		a.id = :id";
			$params = array();
			$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
			$res = $this->db->query($sql,$params);
			while ($r = $this->db->fetchrow($res)) {
				$lang = new wce_lang();
				$lang->openFromResultSet($r);
				$lst[$r['id']] = $lang;
			}
		}
		return $lst;
	}

	public function getListArticleLangNotVersion() {
		$sql = "SELECT		DISTINCT l.*
				FROM		".wce_lang::TABLE_NAME." l
				WHERE		l.id NOT IN (
					SELECT	DISTINCT id_lang b
					FROM	dims_mod_wce_article_block b
					WHERE		b.id_article = :id_article
				)
				AND			id_module = :id_module
				AND			id_workspace = :id_workspace
				AND			is_active = 1";
		$params = array();
		$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$params[':id_module'] = array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT);
		$params[':id_workspace'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);
		$res = $this->db->query($sql,$params);
		$lst = array();
		while ($r = $this->db->fetchrow($res)) {
			$lang = new wce_lang();
			$lang->openFromResultSet($r);
			$lst[$r['id']] = $lang;
		}
		return $lst;
	}

	/*
	 * Fonction permettant de charger les blocks d'une version antérieure
	 */
	public function loadBlocksFromVersion($versionid,$idlang) {
		$blocks=array();
		$lstvers=$this->getListVersion($idlang);
		//dims_print_r($lstvers);die();
		if (in_array($versionid, $lstvers)) {
			// on charge les blocs de cette version
			if ($this->isBlock()) {
				$sql= "	SELECT		bv.*,ab.section,ab.position
						FROM		dims_mod_wce_article_block_version as bv
						INNER JOIN	dims_mod_wce_article_block as ab
						ON			ab.id=bv.blockid
						AND			ab.id_article=:id_article
						AND			ab.section>0
						AND			bv.version=:version
						ORDER BY	ab.section,ab.position";
				$params = array();
				$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
				$params[':version'] = array('value'=>$versionid,'type'=>PDO::PARAM_INT);
				$res = $this->db->query($sql,$params);
				if ($this->db->numrows($res) > 0) {
					while ($f=$this->db->fetchrow($res)) {
						$blocks[$f['section']][$f['blockid']]=$f;
					}
				}
			}
		}

		return $blocks;
	}


	/*
	 * fonction permettant de nettoyer les contenus pour suppression des liens internes saisis à la main
	 */
	public function updateInternalLinks() {
		require_once(DIMS_APP_PATH.'modules/system/class_workspace.php');
		$work = new workspace();

		$work->open($_SESSION['dims']['workspaceid']);
		$lstwebdomain=$work->getFrontDomains();

		if ($this->isBlock()) {
			foreach ($lstwebdomain as $domain) {
				$domain = $domain['domain'];
				$sqlupdate='UPDATE dims_mod_wce_article_block SET ';
				$sqlupdatehttps='UPDATE dims_mod_wce_article_block SET ';

				$params1 = array();
				$params2 = array();

				for ($i=1;$i<=$this->nbelement;$i++) {
					if ($i>1) {
						$sqlupdate.=', ';
						$sqlupdatehttps.=', ';
					}
					$sqlupdate.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/index.php?articleid=")';
					$params1[':content'.$i] = array('value'=>'http://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);
					$sqlupdate.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/index.php?articleid=")';
					$params1[':draftcontent'.$i] = array('value'=>'http://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);

					$sqlupdatehttps.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/index.php?articleid=")';
					$params2[':content'.$i] = array('value'=>'https://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);
					$sqlupdatehttps.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/index.php?articleid=")';
					$params2[':draftcontent'.$i] = array('value'=>'https://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);
				}

				$sqlupdate.=' WHERE id_article=:id_article';
				$sqlupdatehttps.=' WHERE id_article=:id_article';
				$params1[':id_article'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);
				$params2[':id_article'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);

				// execution pour les articles
				$this->db->query($sqlupdate,$params1);
				$this->db->query($sqlupdatehttps,$params2);


				// on traite les datas
				$sqlupdate='UPDATE dims_mod_wce_article_block SET ';
				$sqlupdatehttps='UPDATE dims_mod_wce_article_block SET ';

				$params1 = array();
				$params2 = array();

				for ($i=1;$i<=$this->nbelement;$i++) {
					if ($i>1) {
						$sqlupdate.=', ';
						$sqlupdatehttps.=', ';
					}

					$sqlupdate.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/data")';
					$params1[':content'.$i] = array('value'=>'http://'.$domain.'/data','type'=>PDO::PARAM_STR);
					$sqlupdate.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/data")';
					$params1[':draftcontent'.$i] = array('value'=>'http://'.$domain.'/data','type'=>PDO::PARAM_STR);

					$sqlupdatehttps.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/data")';
					$params2[':content'.$i] = array('value'=>'https://'.$domain.'/data','type'=>PDO::PARAM_STR);
					$sqlupdatehttps.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/data")';
					$params2[':draftcontent'.$i] = array('value'=>'https://'.$domain.'/data','type'=>PDO::PARAM_STR);
				}

				$sqlupdate.=' WHERE id_article=:id_article';
				$sqlupdatehttps.=' WHERE id_article=:id_article';
				$params1[':id_article'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);
				$params2[':id_article'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);

				// execution pour les urls contenant le dossier data
				$this->db->query($sqlupdate,$params1);
				$this->db->query($sqlupdatehttps,$params2);
			}
		}else {
			// cas standard sans modele de block
			foreach ($lstwebdomain as $domain) {
				$domain = $domain['domain'];
				$sqlupdate='UPDATE dims_mod_wce_article SET ';
				$sqlupdatehttps='UPDATE dims_mod_wce_article SET ';

				$params1 = array();
				$params2 = array();

				for ($i=1;$i<=$this->nbelement;$i++) {
					if ($i>1) {
						$sqlupdate.=', ';
						$sqlupdatehttps.=', ';
					}
					$sqlupdate.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/index.php?articleid=")';
					$params1[':content'.$i] = array('value'=>'http://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);
					$sqlupdate.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/index.php?articleid=")';
					$params1[':draftcontent'.$i] = array('value'=>'http://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);

					$sqlupdatehttps.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/index.php?articleid=")';
					$params2[':content'.$i] = array('value'=>'https://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);
					$sqlupdatehttps.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/index.php?articleid=")';
					$params2[':draftcontent'.$i] = array('value'=>'https://'.$domain.'/index.php?articleid=','type'=>PDO::PARAM_STR);
				}

				$sqlupdate.=' WHERE id=:id';
				$sqlupdatehttps.=' WHERE id=:id';
				$params1[':id'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);
				$params2[':id'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);

				// execution pour les datas
				$this->db->query($sqlupdate,$params1);
				$this->db->query($sqlupdatehttps,$params2);

				$sqlupdate='UPDATE dims_mod_wce_article SET ';
				$sqlupdatehttps='UPDATE dims_mod_wce_article SET ';

				$params1 = array();
				$params2 = array();

				for ($i=1;$i<=$this->nbelement;$i++) {
					if ($i>1) {
						$sqlupdate.=', ';
						$sqlupdatehttps.=', ';
					}
					$sqlupdate.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/data")';
					$params1[':content'.$i] = array('value'=>'http://'.$domain.'/data','type'=>PDO::PARAM_STR);
					$sqlupdate.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/data")';
					$params1[':draftcontent'.$i] = array('value'=>'http://'.$domain.'/data','type'=>PDO::PARAM_STR);

					$sqlupdatehttps.=' content'.$i.'= replace(content'.$i.',:content'.$i.',"/data")';
					$params2[':content'.$i] = array('value'=>'https://'.$domain.'/data','type'=>PDO::PARAM_STR);
					$sqlupdatehttps.=', draftcontent'.$i.'= replace(draftcontent'.$i.',:draftcontent'.$i.',"/data")';
					$params2[':draftcontent'.$i] = array('value'=>'https://'.$domain.'/data','type'=>PDO::PARAM_STR);
				}

				$sqlupdate.=' WHERE id=:id';
				$sqlupdatehttps.=' WHERE id=:id';
				$params1[':id'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);
				$params2[':id'] = array('value'=>$this->getId(),'type'=>PDO::PARAM_INT);

				// execution pour les datas
				$this->db->query($sqlupdate,$params1);
				$this->db->query($sqlupdatehttps,$params2);
			}
		}
	}

	public function getObjectCorresp($openObj = true){
		$lst = array();
		if ($this->fields['id'] != '' && $this->fields['id'] > 0){
			$db = dims::getInstance()->getDb();
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
			$sel = "SELECT	*
					FROM	".article_object_corresp::TABLE_NAME."
					WHERE	id_article=:id_article";
			$params = array();
			$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
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
	* fonction getReferences permettant de collecter les références attachées à l'article
	*/
	public function getReferences() {
		$dims=dims::getinstance();

		require_once(DIMS_APP_PATH . '/modules/wce/wiki/include/class_wce_reference.php');

		$refs=array();
		$sql= "	SELECT		DISTINCT wr.*,d.md5id,d.extension
				FROM		dims_mod_wce_reference as wr
				LEFT join	dims_mod_doc_file as d
				ON			d.id=wr.id_doc_link and wr.id_doc_link>0
				WHERE		wr.id_article = :id_article
				AND			wr.id_module = :id_module
				AND			wr.id_lang = :id_lang
				ORDER BY	position";
		$params = array();
		$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$params[':id_module'] = array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT);
		$res = $this->db->query($sql,$params);

		$c=1;
		if ($this->db->numrows($res) > 0) {
			while ($f=$this->db->fetchrow($res)) {
			if ($f['typelink']==0) {
				// test si présence de http devant
				if (strtolower(substr($f['link'], 0,4))!='http') {
					$f['link']='http://'.$f['link'];
				}
				$f['img']='';
			}
			else {
				// doc
				$f['link']=$dims->getProtocol().$dims->getHttpHost()."/index.php?dims_op=doc_file_download&docfile_md5id=".$f['md5id'];

				$icon='txt';
				switch(strtolower($f['extension'])) {
					case 'xlsx' :
					case 'xls' :
						$icon='xls';
						break;
					case 'pdf' :
						$icon='pdf';
						break;

					case 'doc' :
					case 'docx' :
						$icon='doc';
						break;

					case 'zip' :
					case 'tar' :
					case 'tgz' :
						$icon='zip';
						break;
				}

				if ($icon!='') $f['img']='<img style="width:24px;" src="/common/modules/doc/img/file_types/icon_'.$icon.'_32x32.gif" alt="">';
			}

			if ($f['position']!=$c) {
				// on modifie
				$ref = new wce_reference();
				$ref->open($f['id']);
				$ref->fields['position']=$c;
				$ref->save();
			}

			$refs[]=$f;

			// on incremente
			$c++;

			}

		}

		return $refs;
	}

	public static function convertLinksToLinksEdit($content,$module = 'wce') {
		switch($module){
			case 'wiki':
				return preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i'," href=\"javascript:void(0);\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=$1&wce_mode=edit&readonly=0&adminedit=1$2")."');\" ",$content);
				break;
			default:
				return preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i'," href=\"javascript:void(0);\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&articleid=$1&wce_mode=edit&readonly=0&adminedit=1$2');\" ",$content);
				break;
		}
		//return preg_replace('/ href=["|\'][a-zA-Z-_\/.0-9#:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9#:+?%=&;,]*["|\']/i'," href=\"javascript:void(0);\" onclick=\"javascript:window.parent.updateCompleteArticle('".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=$1&wce_mode=edit")."');\" ",$content);
	}

	// permet de récupérer la liste des liens vers un autre article
	public function getInternalLinks(){
		$lst = array();
		for ($i=1; $i<=19;$i++){
			$matches = array();
			if(preg_match_all('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i',$this->fields["content$i"],$matches)){
				if (isset($matches[1]))
					foreach($matches[1] as $match)
						if (isset($match))
							$lst[$match] = $match;
			}
			$matches = array();
			if(preg_match_all('/ href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=([0-9]+)[a-zA-Z-_\/.0-9:+?%=&;,]*((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?["|\']/i',$this->fields["draftcontent$i"],$matches)){
				if (isset($matches[1]))
					foreach($matches[1] as $match)
						if (isset($match))
							$lst[$match] = $match;
			}
		}
		foreach($this->getAllBlocks(true) as $block){
			$lst = array_merge($lst, $block->getInternalLinks());
		}
		return $lst;
	}

	// permet de récupérer la liste des articles ayant un lien poitant vers celui-ci
	public function getExternalLinks(){
		$lst = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	id_module = :id_module
				AND		id != :id";
		$params = array();
		$params[':id'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$params[':id_module'] = array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT);
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$art = new wce_article();
			$art->openFromResultSet($r);
			if(in_array($this->fields['id'],$art->getInternalLinks()))
				$lst[$art->fields['id']] = $art;
		}

		return $lst;
	}

	public function replaceLinks($from, $to){
		for ($i=1; $i<=19; $i++){
			$this->fields["content$i"] = preg_replace('/( href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=)'.$from.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\'])/i',"$1<DIMS_TO_REPLACE>$2$5",$this->fields["content$i"]);
			$this->fields["content$i"] = str_replace("<DIMS_TO_REPLACE>",$to,$this->fields["content$i"]);
			$this->fields["draftcontent$i"] = preg_replace('/( href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid=)'.$from.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\'])/i',"$1<DIMS_TO_REPLACE>$2$5",$this->fields["draftcontent$i"]);
			$this->fields["draftcontent$i"] = str_replace("<DIMS_TO_REPLACE>",$to,$this->fields["draftcontent$i"]);
		}
		foreach($this->getAllBlocks(true) as $block){
			$block->replaceLinks($from,$to);
		}
		$this->save();
	}

	public function deleteLinks($to){
		for ($i=1; $i<=19; $i++){
			$this->fields["content$i"] = preg_replace('/<a.* href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid='.$to.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\']).*>(.*)<\/a>/iU',"$5",$this->fields["content$i"]);
			$this->fields["draftcontent$i"] = preg_replace('/<a.* href=["|\'][a-zA-Z-_\/.0-9:+?%=&;,]*articleid='.$to.'([a-zA-Z-_\/.:+?%=&;,]+[a-zA-Z-_\/.0-9:+?%=&;,]*)?((&|&amp;)WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)?(["|\']).*>(.*)<\/a>/iU',"$5",$this->fields["draftcontent$i"]);
		}
		foreach($this->getAllBlocks(true) as $block){
			$block->deleteLinks($to);
		}
		$this->save();
	}

	public function generateNewLang($currLang, $idLang){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		*
				FROM		".wce_block::TABLE_NAME."
				WHERE		id_article = :id_article
				AND			id_lang = :id_lang
				ORDER BY	section,position";
		$params = array();
		$params[':id_article'] = array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT);
		$params[':id_lang'] = array('value'=>$currLang,'type'=>PDO::PARAM_INT);
		$res = $db->query($sel,$params);
		while ($ob = $db->fetchrow($res)) {
			$bloc = new wce_block();
			$bloc->openFromResultSet($ob);
			$bloc->setNew(true);
			$bloc->fields['id_lang'] = $idLang;
			$bloc->fields['uptodate'] = 0;
			$bloc->fields['id_globalobject'] = 0;
			$bloc->setugm();
			for($i=1;$i<=19;$i++){
				$bloc->fields["content$i"] = "";
				$bloc->fields["draftcontent$i"] = "";
			}
			$bloc->save();
		}
		$this->fields['uptodate'] = 0;
		$this->save();
	}

	public function updateCountTags(){
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_tags.php";
		foreach($this->getMyTags() as $tag){
			article_tags::updateTags($tag->fields['id'],$this->fields['id_module'],$this->fields['id_workspace']);
		}
	}

	public static function getArticleByRewrite($id_workspace, $rewrite){
		$db = dims::getInstance()->getDb();
		$params = array();
		$params[':id_workspace'] = array('value'=>$id_workspace,'type'=>PDO::PARAM_INT);
		$params[':urlrewrite'] = array('value'=>$rewrite,'type'=>PDO::PARAM_STR);
		$res = $db->query("	SELECT	*
							FROM	".self::TABLE_NAME."
							WHERE	id_workspace = :id_workspace
							AND		urlrewrite = :urlrewrite
							LIMIT	0,1",$params);
		if($db->numrows($res)){
			$fields = $db->fetchrow($res);
			$art = new wce_article();
			$art->openFromResultSet($fields);
			return $art;
		}
		else return null;
	}

	public function str_replace($search,$replace){
		for ($i=1; $i<=19; $i++){
			$this->fields["content$i"] = str_replace($search,$replace,$this->fields["content$i"]);
			$this->fields["draftcontent$i"] = str_replace($search,$replace,$this->fields["draftcontent$i"]);
		}
		foreach($this->getAllBlocks(true) as $block){
			$block->str_replace($search,$replace);
		}
		$this->save();
	}

	public function updateUrlRewrite() {
		// controle si url rewrite deja présent
		if ($this->fields['urlrewrite']=='') {
			$this->fields['urlrewrite']=generateValideUrl($this->fields['title']);
		}
	}

	public function contructAriane(){
		$head = new wce_heading();
		$head->open($this->fields['id_heading']);
		$elem = array();
		$elem['id']	= 0;
		$elem['type']	= 2; //Heading
		$elem['label']	= 'Accueil';
		$elem['link']	= '/index.php';
		$return = array($elem);
		return array_merge($return,$head->contructAriane($this));
	}

	public static function getArticles(/*$idCateg=0,*/ $status=-1, $id_creator=-1, $date_modify_from=-1, $date_modify_to=-1, $not_in_lang=-1, $keywords=-1, $include_content=-1, $tags = null, $type=0){
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

		$sel .= "	INNER JOIN 	".wce_heading::TABLE_NAME." h
					ON 			h.id = a.id_heading
					WHERE		a.id_workspace = :wk
					AND 		h.type = :type";
		$params[':wk'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);
		$params[':type'] = array('value'=>$type,'type'=>PDO::PARAM_INT);
		/*if ($idCateg > 0){
			$cat = new category();
			$cat->open($idCateg);
			$lstGo = $cat->searchGbLinkChild(dims_const::_SYSTEM_OBJECT_WCE_ARTICLE);
			if(count($lstGo) > 0){
				$sel .= " AND	a.id_globalobject IN (".$db->getParamsFromArray($lstGo,'go',$params).") ";
			}else
				$sel .= " AND	a.id = 0 ";
		}*/

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
			$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], 1,$_SESSION['cste']['_ARTICLE'], false, $fields);
			$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_WCE_ARTICLE,$_SESSION['cste']['_ARTICLE'], false, $fields);
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
}
?>
