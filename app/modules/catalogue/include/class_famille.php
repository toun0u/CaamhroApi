<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_famille_thumb.php";
class cata_famille extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_famille';
	const MY_GLOBALOBJECT_CODE = 233;

	const DISPLAY_MODE_LIST 		= 1;
	const DISPLAY_MODE_COMPARATOR 	= 2;
	const DISPLAY_MODE_CMS 			= 3;

	private $oldLabel = '';
	private $a_selections = null;

	public $fields_head = array();
	public $fields_lang = array();
	public $fields_lang_default = array();

	public function __construct() {
		$this->fields_head = array();
		$this->fields_lang = array();
		$this->fields_lang_default = array();

		parent::dims_data_object(self::TABLE_NAME, 'id', 'id_lang');

		$this->to_index(array('label', 'description'));
	}

	function open() {
		$id=0;
		$id_lang="";
		$numargs = func_num_args();
		for ($i = 0; $i < $numargs; $i++) {
			switch ($i) {
				case 0:
					$id=func_get_arg($i);
					break;
				case 1:
					$id_lang= func_get_arg($i);
					break;
			}
		}
		if ($id_lang=='' || $id_lang<=0){
			include_once DIMS_APP_PATH.'/modules/catalogue/include/class_param.php';
			$id_lang = cata_param::getDefaultLang();
		}
		parent::open($id,$id_lang);

		// sauvegarde du label original pour détecter sa modification (rewrite)
		if (!$this->isNew()) {
			$this->oldLabel = $this->fields['label'];
		}

		$this->fields_head = $this->fields;
	}

	public function getByCode($code, $id_lang = 0) {
		if ($id_lang == 0) {
			include_once DIMS_APP_PATH.'/modules/catalogue/include/class_param.php';
			$id_lang = cata_param::getDefaultLang();
		}

		$sql = "SELECT * FROM `".self::TABLE_NAME."` WHERE `code` = '$code' AND `id_lang` = $id_lang";

		$this->resultid = $this->db->query($sql, $id);
		$this->numrows = $this->db->numrows($this->resultid);
		$this->fields = $this->db->fetchrow($this->resultid);

		if ($this->numrows>0) $this->new = false;
		$this->updateGOOnOpenedRow();

		// sauvegarde du label original pour détecter sa modification (rewrite)
		if (!$this->isNew()) {
			$this->oldLabel = $this->fields['label'];
		}

		$this->fields_head = $this->fields;
	}

	######################
	# MANAGEMENT METHODS #
	######################

	function save($updatePos = true) {
		/*$this->setugm(); //Modele/group/user
		if($setcm) $this->setcm();	//create / modify*/

		// default display mode
		if (empty($this->fields['display_mode'])) {
			$this->fields['display_mode'] = self::DISPLAY_MODE_LIST;
		}

		// Gestion position
		if((!($this->fields['position'] != '' && $this->fields['position'] > 0)) && $this->fields['id_parent'] != '' && $this->fields['id_parent'] > 0){
			$par = new cata_famille();
			$par->open($this->fields['id_parent']);
			$nbElem = $par->getNbChildren();
			$this->fields['position'] = $nbElem+1;
		}
		$isNew = $this->isNew();
		$parentUpdate = false;
		if($isNew){
			if(!($this->fields['id'] != '' && $this->fields['id'] > 0)){
				$sel = "SELECT 		*
						FROM 		".self::TABLE_NAME."
						WHERE 		id_parent = '".$this->fields['id_parent']."'
						AND 		position = ".$this->fields['position']."";
				$res = $this->db->query($sel);
				$first = true;
				while($r = $this->db->fetchrow($res)){
					$fam = new cata_famille();
					$fam->openFromResultSet($r);
					$fam->fields['position'] ++;
					$fam->save($first);
					$first = false;
				}
				$this->updateParents();
			}
		}else{
			if(isset($this->fields_head['id_globalobject'])){
				if($updatePos && $this->fields['id_parent'] != $this->fields_head['id_parent']){
					$sel = "SELECT 	*
							FROM 	".self::TABLE_NAME."
							WHERE 	id_parent = '".$this->fields['id_parent']."'
							AND 	position >= ".$this->fields['position']."
							AND 	id != ".$this->fields['id'];
					$res = $this->db->query($sel);
					while($r = $this->db->fetchrow($res)){
						$fam = new cata_famille();
						$fam->openFromResultSet($r);
						$fam->fields['position'] ++;
						$fam->save(false);
					}
					$sel = "SELECT 	*
							FROM 	".self::TABLE_NAME."
							WHERE 	id_parent = '".$this->fields_head['id_parent']."'
							AND 	position > ".$this->fields_head['position']."
							AND 	id != ".$this->fields_head['id'];
					$res = $this->db->query($sel);
					while($r = $this->db->fetchrow($res)){
						$fam = new cata_famille();
						$fam->openFromResultSet($r);
						$fam->fields['position'] --;
						$fam->save(false);
					}
					$this->updateParents();
				}elseif($updatePos && $this->fields['position'] != $this->fields_head['position']){
					if ($this->fields['position'] < $this->fields_head['position'])
						$sel = "SELECT 	*
								FROM 	".self::TABLE_NAME."
								WHERE 	id_parent = '".$this->fields['id_parent']."'
								AND 	position BETWEEN ".$this->fields['position']." AND ".$this->fields_head['position']."
								AND 	id != ".$this->fields['id'];
					else
						$sel = "SELECT 	*
								FROM 	".self::TABLE_NAME."
								WHERE 	id_parent = '".$this->fields['id_parent']."'
								AND 	position BETWEEN ".$this->fields_head['position']." AND ".$this->fields['position']."
								AND 	id != ".$this->fields['id'];
					$res = $this->db->query($sel);
					while($r = $this->db->fetchrow($res)){
						$fam = new cata_famille();
						$fam->openFromResultSet($r);
						if($this->fields['position'] < $this->fields_head['position'])
							$fam->fields['position'] ++;
						else
							$fam->fields['position'] --;
						$fam->save(false);
					}
				}
			}else
				$isNew=true;
		}
		//Profondeur
		$this->fields['depth'] = $this->getlevel();

		// URL rewrite
		if ($this->fields['label'] != $this->oldLabel) {
			$this->fields['urlrewrite'] = cata_genSmartFamRewrite($this->fields['label']);
		}

		$return = parent::save(self::MY_GLOBALOBJECT_CODE);
		// update parents parents fields
		if(!$isNew && $this->fields['parents'] != $this->fields_head['parents']){
			foreach($this->getChildrens() as $child){
				$child->updateParents();
				$child->save();
			}
		}

		// Héritage des champs libres de la famille parente
		if ($isNew && $this->fields['id_parent'] > 0) {
			require_once DIMS_APP_PATH."modules/catalogue/include/class_champ_famille.php";
				$db = dims::getInstance()->getDb();
				$sel = "SELECT *
						FROM 	".cata_champ_famille::TABLE_NAME."
						WHERE 	id_famille = ".$this->fields['id_parent'];
				$res = $db->query($sel);
				while($r = $db->fetchrow($res)){
					$lk = new cata_champ_famille();
					if (!$lk->open($this->fields['id'], $r['id_champ'])) {
						$lk->init_description();
					}
					$lk->fields = $r;
					$lk->fields['id_famille'] = $this->fields['id'];
					$lk->fields['inherited'] = cata_champ_famille::_INHERITED;
					$lk->save();
				}
		}

		return ($return);
	}

	public function settitle(){
		$this->title = "CATA FAMILLE ".$this->get('id');
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function updateParents(){
		if ($this->fields['id_parent'] > 0) {
			$sel = "SELECT 	parents
					FROM 	".self::TABLE_NAME."
					WHERE 	id = '".$this->fields['id_parent']."'";
			$res = $this->db->query($sel);
			if($r = $this->db->fetchrow($res)){
				$this->fields['parents'] = $r['parents'].";".$this->fields['id_parent'];
			}else
				$this->fields['parents'] = "0;".$this->fields['id_parent'];
		}
		else {
			$this->fields['parents'] = '0';
		}

	}

	function delete($rec=true) {
		if($rec){
			foreach($this->getChildrens() as $child){
				$child->delete();
			}
			include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
			$sel = "SELECT 	*
					FROM 	".cata_article_famille::TABLE_NAME."
					WHERE 	id = ".$this->get('id');
			$res = $this->db->query($sel);
			while($r = $this->db->fetchrow($res)){
				$lk = new cata_article_famille();
				$lk->openFromResultSet($r);
				$lk->delete();
			}
			$sel = "SELECT 	*
					FROM 	".self::TABLE_NAME."
					WHERE 	id = ".$this->get('id')."
					AND 	id_lang != ".$this->fields['id_lang'];
			$res = $this->db->query($sel);
			while($r = $this->db->fetchrow($res)){
				$lg = new cata_famille();
				$lg->openFromResultSet($r);
				$lg->delete(false);
			}
		}
		parent::delete();
	}

	/**
	* @access public
	*/
	public function createchild() {
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	*
				FROM 	".self::TABLE_NAME."
				WHERE 	id = ".$this->fields['id']
;		$res = $db->query($sel);
		$return = null;
		while($r = $db->fetchrow($res)){
			$child = new cata_famille();
			$child->init_description();
			$child->fields['id'] = '';
			$child->fields['id_globalobject'] = '';
			$child->fields['position'] = '';
			if(!is_null($return)){
				$child->fields['id'] = $return->fields['id'];
				$child->fields['id_globalobject'] = $return->fields['id_globalobject'];
				$child->fields['position'] = $return->fields['position'];
			}
			$child->fields['id_parent'] = $r['id'];
			$child->fields['id_lang'] = $r['id_lang'];
			$child->fields['parents'] = $r['parents'].";".$r['id'];
			$child->fields['label'] = "Fils de : ".$r['label'];
			$child->setugm();
			$child->setcm();
			$child->fields['date_create'] = $child->fields['date_modify'] = dims_createtimestamp();
			$child->save();
			if(is_null($return))
				$return = $child;
		}

		return $return;
	}

	/**
	* @access public
	*/
	public function createclone() {
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	*
				FROM 	".self::TABLE_NAME."
				WHERE 	id = ".$this->fields['id'];
		$res = $db->query($sel);
		$return = null;
		while($r = $db->fetchrow($res)){
			$child = new cata_famille();
			$child->init_description();
			$child->fields = $r;
			$child->fields['id'] = '';
			$child->fields['id_globalobject'] = '';
			$child->fields['position'] = '';
			if(!is_null($return)){
				$child->fields['id'] = $return->fields['id'];
				$child->fields['id_globalobject'] = $return->fields['id_globalobject'];
				$child->fields['position'] = $return->fields['position'];
			}
			$child->fields['id_parent'] = $r['id_parent'];
			$child->fields['id_lang'] = $r['id_lang'];
			$child->fields['parents'] = $r['parents'];
			$child->fields['label'] = "Clone de : ".$r['label'];
			$child->fields['description'] = $r['description'];
			$child->setugm();
			$child->setcm();
			$child->fields['date_create'] = $child->fields['date_modify'] = dims_createtimestamp();
			$child->save();
			if(is_null($return))
				$return = $child;
		}

		include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
		$sel = "SELECT 	*
				FROM 	".cata_article_famille::TABLE_NAME."
				WHERE 	id_famille = ".$this->get('id');
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$lk = new cata_article_famille();
			$lk->openFromResultSet($r);
			$lk->fields['id'] = '';
			$lk->fields['id_famille'] = $return->get('id');
			$lk->setNew(true);
			$lk->save();
		}
		return $return;
	}


	/*
	* retrieves children list of a specficic heading.
	*
	* @access public
	*
	* @param int idHeading
	* @return array list of child(ren)
	*/
	function getchildren($idFamille = -1) {
		// declarations
		$arrayChildren = array();

		if ($idFamille == -1) $idFamille = $this->fields['id'];

		$query = "SELECT id, depth FROM dims_mod_cata_famille WHERE id_parent = $idFamille AND id_lang = {$this->fields['id_lang']}";
		$result = $this->db->query($query);

		if ($result) {
			while ($row = $this->db->fetchrow($result, MYSQL_ASSOC)) {
				if (!isset($arrayChildren[$row['depth']])) $arrayChildren[$row['depth']] = array();
				array_push($arrayChildren[$row['depth']], $row['id']);

				$child = $this->getchildren($row['id']);
				foreach ($child as $child_key => $child_id_famille) {
					foreach ($child_id_famille as $child2_key => $child2_id_famille) {
						if (!isset($arrayChildren[$child_key])) $arrayChildren[$child_key] = array();
						array_push($arrayChildren[$child_key], $child2_id_famille);
					}
				}
			}
			return $arrayChildren;
		}
		return false;
	}

	/*
	* retrieves parents list of a specficic heading.
	*
	* @access public
	*
	* @param int idHeading
	* @return array list of parent(s)
	*/
	function getparents($idFamille = -1, &$parents) {
		if ($idFamille == -1)
			$idFamille = $this->fields['id'];

		if ($parents == '')
			$parents = $idFamille;

		// declaration
		$query = '';
		$result = false;
		$row = array();
		$fatherId = -1;

		$query = "SELECT id_parent FROM dims_mod_cata_famille WHERE id = $idFamille";
		$result = $this->db->query($query);

		if ($result) {
			while ($row = $this->db->fetchrow($result, MYSQL_ASSOC)) {
				$fatherId = $row['id_parent'];
				$parents = "$fatherId;$parents";
				$this->getparents($fatherId, $parents);
			}
		}

		return $parents;
	}

	/*
	* retrieves brothers list of a specficic heading.
	*
	* @access public
	*
	* @param int idHeading
	* @return array list of brother(s) (i.e. : list of heading having the same level and the same parent)
	*/
	function getbrothers($idParent = -1) {
		if ($idParent == -1)
			$idParent = $this->fields['id_parent'];

		// declarations
		$arrayBrothers = array();

		$query = "
			SELECT		id
			FROM		".self::TABLE_NAME."
			WHERE		id_module = ".$this->get('id_module')."
			AND			id_workspace IN (0,". dims_viewworkspaces($this->get('id_module')) .")
			AND			id_parent = $idParent
			GROUP BY 	id";
		$result = $this->db->query($query);
		while($r = $this->db->fetchrow($result))
			$arrayBrothers[] = $r['id'];
		return $arrayBrothers;
	}


	/*
	* retrieves all headings of as specific level
	*
	* @access public
	*
	* @param int level
	* @return array list of heading having the same level
	*/
	function getsamelevel($level = -1) {
		if ($level == -1) {
			$level = $this->getlevel();
		}

		// declarations
		$arraySameLevel = array();

		$query = "
			SELECT	id,parents
			FROM	dims_mod_cata_family
			WHERE	id_module = {$_SESSION['dims']['moduleid']}
			AND		id_workspace IN (". dims_viewgroups($_SESSION['dims']['moduleid']) .")";
		$result = $this->db->query($query);
		while($row = $this->db->fetchrow($result, MYSQL_ASSOC)) {
			if (count(explode(';', $row['parents']) == $level)) {
				array_push($arraySameLevel, $row['id']);
			}
		}

		return $arraySameLevel;
	}

	/*
	* retrieves heading's level
	*
	* @access public
	*
	* @param int idHeading
	* @return int heading's level
	*/
	function getlevel() {
		return (substr_count($this->fields['parents'], ';') + 1);
	}

	/*
	* retrieves heading's father
	*
	* @access public
	*
	* @param int idHeading
	* @return int heading's father
	*/
	function getfather($idFamille = -1) {
		if ($idFamille == -1)
			$idFamille = $this->fields['id'];

		// declarations
		$fatherId = -1;

		$query = "
			SELECT	id_parent
			FROM	dims_mod_cata_famille
			WHERE	id_module = {$_SESSION['dims']['moduleid']}
			AND		id_workspace IN (". dims_viewgroups($_SESSION['dims']['moduleid']) .")
			AND		id = $idFamille";
		$result = $this->db->query($query);
		if ($result) {
			$fatherId = $this->db->fetchrow($result, MYSQL_ASSOC);
			return $fatherId['id_parent'];
		}

		return false;
	}

	function getmodele() {
		$this->db->query("SELECT * FROM dims_mod_cata_modele WHERE id = {$this->fields['id']}");
		return $this->db->fetchrow();
	}

	public $childrens = null;

	public static function contructFinder($idSel = 0){
		$selectedFam = array();
		if($idSel != '' && $idSel > 0){
			$sel = new cata_famille();
			$sel->open($idSel);
			$selectedFam = explode(';',$sel->fields['parents']);
			$selectedFam[] = $idSel;
		}

		$fam = new cata_famille();
		$sel = "SELECT 		f.*
				FROM 		".self::TABLE_NAME." f
				WHERE 		f.id_module = ".$_SESSION['dims']['moduleid']."
				AND 		f.id_workspace = ".$_SESSION['dims']['workspaceid']."
				AND 		f.id_parent = 0
				GROUP BY 	f.id";

		$db = dims::getInstance()->getDb();
		$res = $db->query($sel);
		if($r = $db->fetchrow($res)){
			$fam->openFromResultSet($r);
			$fam->getChildrens($selectedFam);
		} else {
			$fam->createRootFam();
		}
		return $fam;
	}

	public function createRootFam() {
		$this->init_description();
		$this->setugm();
		$this->fields['date_create'] = $this->fields['date_modify'] = dims_createtimestamp();
		$this->fields['user_create'] = $this->fields['user_modify'] = $_SESSION['dims']['userid'];
		$this->fields['id_lang'] = 1;
		$this->fields['id_parent'] = 0;
		$this->fields['parents'] = '0';
		$this->fields['depth'] = 1;
		$this->fields['position'] = 1;
		$this->fields['published'] = 1;
		$this->fields['visible'] = 1;
		$this->fields['label'] = 'Racine';
		$this->fields['urlrewrite'] = cata_genSmartFamRewrite('Racine');
		parent::save();
		return $this;
	}

	public function getChildrens($lstSel = array()){
		if(is_null($this->childrens)){
			$this->childrens = array();
			$sel = "SELECT 		f.*
					FROM 		".self::TABLE_NAME." f
					WHERE 		f.id_module = ".$this->fields['id_module']."
					AND 		f.id_workspace = ".$this->fields['id_workspace']."
					AND 		f.id_parent = ".$this->fields['id']."
					GROUP BY 	f.id
					ORDER BY 	f.position";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			while($r = $db->fetchrow($res)){
				$fam = new cata_famille();
				$fam->openFromResultSet($r);
				$this->childrens[] = $fam;
				if(in_array($fam->fields['id'], $lstSel))
					$fam->getChildrens($lstSel);
			}
		}
		return $this->childrens;
	}

	#Cyril - 30/11/2012 <-> Lot de fonction permettant de remonter l'arborescence d'une famille
	public static function getRootCatalogue(){
		$fam = new cata_famille();

		$sel = "SELECT 		f.*
				FROM 		".self::TABLE_NAME." f
				WHERE 		f.id_module = ".$_SESSION['dims']['moduleid']."
				AND 		f.id_workspace = ".$_SESSION['dims']['workspaceid']."
				AND 		f.id_parent = 0
				GROUP BY 	f.id
				LIMIT 		1";

		$db = dims::getInstance()->getDb();
		$res = $db->query($sel);
		if($db->numrows($res)){
			$split = $db->split_resultset($res);
			$fam->openFromResultSet($split[0]['f']);
		}
		else $fam->init_description();
		return $fam;
	}

	public function initDescendance(){
		$lst = array();

		#Une seule requête pour l'optimisation

		$sel = "SELECT 		f.*
				FROM 		".self::TABLE_NAME." f
				WHERE 		f.id_module = ".$_SESSION['dims']['moduleid']."
				AND 		f.parents REGEXP '(^".$this->fields['id'].";|;".$this->fields['id']."$|;".$this->fields['id'].";)'
				AND 		f.id_workspace = ".$_SESSION['dims']['workspaceid']."
				GROUP BY 	f.id
				ORDER BY 	f.position";


		$db = dims::getInstance()->getDb();
		$res = $db->query($sel);

		$elements = array();
		if($db->numrows($res)){
			$split = $db->split_resultset($res);
			foreach($split as $tab){
				$fam = new dims_data_object(self::TABLE_NAME); #Cyril : obligé d'utiliser ddo plutôt que cata_famille à cause des describe à répétition
				$fam->openFromResultSet($tab['f']);
				$elements[$fam->fields['id_parent']][] = $fam;
			}
		}

		$lst[] = $this;
		while(!empty($lst)){
			$fam = array_shift($lst);
			$fam->descendance = array();
			$children = isset($elements[$fam->fields['id']]) ? $elements[$fam->fields['id']] : null;
			if( ! empty($children) ){
				foreach($children as $child){
					$fam->descendance[$child->fields['id']] = $child;
					array_unshift($lst, $child);
				}
			}
		}
	}

	public function getAllDescendance($obj = true){
		$sel = "SELECT 	*
				FROM 	".self::TABLE_NAME."
				WHERE 	parents LIKE '".$this->fields['parents'].";".$this->fields['id'].";%'
				OR 		parents LIKE '".$this->fields['parents'].";".$this->fields['id']."'";
		$db = dims::getInstance()->getDb();
		$lst = array();
		$res = $db->query($sel);
		if($obj){
			while($r = $db->fetchrow($res)){
				$fam = new cata_famille();
				$fam->openFromResultSet($r);
				$lst[] = $fam;
			}
		}else{
			while($r = $db->fetchrow($res)){
				$lst[] = $r['id'];
			}
		}
		return $lst;
	}

	public function getLabel($idLg = 0){
		if($idLg > 0){
			$sel = "SELECT 	label
					FROM 	".self::TABLE_NAME."
					WHERE 	id = ".$this->fields['id']."
					AND 	id_lang = $idLg";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			if($r = $db->fetchrow($res))
				return $r['label'];
		}
		return $this->fields['label'];
	}

	public function getDescription($idLg = 0){
		if($idLg > 0){
			$sel = "SELECT 	description
					FROM 	".self::TABLE_NAME."
					WHERE 	id = ".$this->fields['id']."
					AND 	id_lang = $idLg";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			if($r = $db->fetchrow($res))
				return $r['description'];
		}
		return $this->fields['description'];
	}

	public function hasChildren(){
		if(!is_null($this->childrens) && count($this->childrens))
			return true;
		else{
			$sel = "SELECT 		*
					FROM 		".self::TABLE_NAME."
					WHERE 		id_module = ".$_SESSION['dims']['moduleid']."
					AND 		id_workspace = ".$_SESSION['dims']['workspaceid']."
					AND 		id_parent = ".$this->fields['id']."
					ORDER BY 	position";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			return ($db->numrows($res) > 0);
		}
	}

	public function getDateCreatedHum(){
		return substr($this->fields['date_create'],6,2)."/".substr($this->fields['date_create'],4,2)."/".substr($this->fields['date_create'],0,4);
	}

	public function getDateUpdatedHum(){
		return substr($this->fields['date_modify'],6,2)."/".substr($this->fields['date_modify'],4,2)."/".substr($this->fields['date_modify'],0,4);
	}

	public function getUserCreate(){
		include_once DIMS_APP_PATH."modules/system/class_user.php";
		$user = new user();
		$user->open($this->fields['user_create']);
		return $user;
	}

	public function getArticles($obj = true){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
		include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		a.*
				FROM 		".article::TABLE_NAME." a
				INNER JOIN 	".cata_article_famille::TABLE_NAME." af
				ON 			af.id_article = a.id
				AND 		af.id_famille = ".$this->fields['id']."
				WHERE 		a.status LIKE 'OK'
				AND 		a.id_lang = ".cata_param::getDefaultLang()."
				ORDER BY 	af.position";
		$res = $db->query($sel);
		$lstRes = array();
		if($obj){
			while($r = $db->fetchrow($res)){
				$art = new article();
				$art->openFromResultSet($r);
				$lstRes[$r['id']] = $art;
			}
		}else{
			while($r = $db->fetchrow($res)){
				$lstRes[$r['id']] = $r['id'];
			}
		}
		return $lstRes;
	}

	public function getNbChildren(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 	MAX(position) as nb
				FROM 	".self::TABLE_NAME."
				WHERE 	id_parent = ".$this->fields['id'];
		$res = $db->query($sel);
		if($r = $db->fetchrow($res))
			return $r['nb'];
		else
			return 0;
	}

	public function getDirectChilds($object = true){
		$lst = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		*
				FROM 		".self::TABLE_NAME."
				WHERE 		id_parent = ".$this->fields['id']."
				AND 		id_lang = ".$this->fields['id_lang']."
				ORDER BY 	position";
		$res = $db->query($sel);
		if($object){
			while($r = $db->fetchrow($res)){
				$elem = new cata_famille();
				$elem->openFromResultSet($r);
				$lst[$r['id']] = $elem;
			}
		}else{
			while($r = $db->fetchrow($res)){
				$lst[$r['id']] = $r['id'];
			}
		}
		return $lst;
	}

	public function getChampsLibre($keywords = ""){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_famille.php";
		$query = "";
		$params = array(
			':idm'=>$this->fields['id_module'],
			':idf'=>$this->get('id'),
		);
		if(!empty($keywords)){
			$query = "	INNER JOIN 	".cata_champ_lang::TABLE_NAME." cl
						ON 			cl.id_chp = c.id
						AND 		cl.libelle LIKE :lib ";
			$params[':lib'] = "%$keywords%";
		}
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		c.*
				FROM 		".cata_champ::TABLE_NAME." c
				INNER JOIN 	".cata_champ_famille::TABLE_NAME." cf
				ON 			c.id = cf.id_champ
				$query
				WHERE 		c.id_module = :idm
				AND 		cf.id_famille = :idf
				AND 		cf.status = ".cata_champ_famille::_STATUS_OK."
				ORDER BY 	cf.position";
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$champ = new cata_champ();
			$champ->openFromResultSet($r);
			$lst[$r['id']] = $champ;
		}
		return $lst;
	}

	public function getThumbnails($limit = 0){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		d.*, lk.*
				FROM 		".docfile::TABLE_NAME." d
				INNER JOIN 	".cata_fam_thumb::TABLE_NAME." lk
				ON 			lk.id_doc = d.id
				WHERE 		lk.id_famille = ".$this->fields['id']."
				ORDER BY 	lk.position
				".(($limit > 0)?"LIMIT $limit":"");
		$res = $db->query($sel);
		$lst = array();
		foreach($db->split_resultset($res) as $r){
			$lk = new cata_fam_thumb();
			$lk->openFromResultSet($r['lk']);
			$doc = new docfile();
			$doc->openFromResultSet($r['d']);
			$lk->setDocFile($doc);
			$lst[] = $lk;
		}
		return $lst;
	}

	public function getNbThumbnails(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		d.*, lk.*
				FROM 		".docfile::TABLE_NAME." d
				INNER JOIN 	".cata_fam_thumb::TABLE_NAME." lk
				ON 			lk.id_doc = d.id
				WHERE 		lk.id_famille = ".$this->fields['id']."
				ORDER BY 	lk.position";
		$res = $db->query($sel);
		return $db->numrows($res);
	}


	public function getIdDoc(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT  id_doc
				FROM    ".cata_fam_thumb::TABLE_NAME."
				WHERE   id_famille = ".$this->fields['id'];
		$res = $db->query($sel);
		$lst = array();
		while($r = $db->fetchrow($res))
			$lst[] = $r['id_doc'];
		return $lst;
	}

	public function linkArticle($lst){
		$already = $this->getArticles(false);
		include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
		foreach($lst as $idArt){
			if(!in_array($idArt, $already)){
				$lk = new cata_article_famille();
				$lk->init_description();
				$lk->fields['id_article'] = $idArt;
				$lk->fields['id_famille'] = $this->fields['id'];
				$lk->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$lk->save();
			}
		}
	}

	# retourne toutes les traductions de la famille
	public function getTranslations(){
		$translations = array();
		$translations[$this->fields['id_lang']] = $this;
		$res = $this->db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE id = '.$this->fields['id'].' AND id_lang != '.$this->fields['id_lang']);
		while($fields = $this->db->fetchrow($res)){
			$art = new cata_famille();
			$art->openFromResultSet($fields);
			$translations[$art->fields['id_lang']] = $art;
		}
		return $translations;
	}

	// Retourne toutes les sélections de la famille
	public function getSelections() {
		if ($this->isNew()) {
			return array();
		}

		if (is_null($this->a_selections)) {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_param.php';
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_selection.php';
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_famille_selection.php';
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_famille_selection_article.php';

			// Langue par défaut
			$id_lang = cata_param::getDefaultLang();

			$a_selections = array();
			$rs = $this->db->query('SELECT s.*, fs.`position`, COUNT(fsa.`article_id`) AS nb_art
				FROM `'.cata_famille_selection::TABLE_NAME.'` fs
				INNER JOIN 	`'.cata_selection::TABLE_NAME.'` s
				ON 			s.`id` = fs.`selection_id`
				AND 		s.`id_lang` = '.$id_lang.'
				LEFT JOIN 	`'.cata_famille_selection_article::TABLE_NAME.'` fsa
				ON 			fsa.`family_id` = '.$this->get('id').'
				AND 		fsa.`selection_id` = s.`id`
				WHERE fs.`family_id` = '.$this->get('id').'
				GROUP BY fsa.`family_id`, fsa.`selection_id`
				ORDER BY fs.`position`');
			if ($this->db->numrows($rs)) {
				$separation = $this->db->split_resultset($rs);
				foreach ($separation as $elements) {
					$selection = new cata_selection();
					$selection->openFromResultSet($elements['s']);

					$a_selections[$elements['fs']['position']] = array(
						'nb_art' 		=> $elements['unknown_table']['nb_art'],
						'position' 		=> $elements['fs']['position'],
						'selection' 	=> $selection
						);
				}
			}
			$this->a_selections = $a_selections;
		}
		return $this->a_selections;
	}

	public function getRewritedLink() {
		if($this->get('id_article_wce') > 0){
			$dims = dims::getInstance();
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article.php";
			$art = wce_article::find_by(array('id'=>$this->get('id_article_wce')),null,1);
			$from = "";
			if ( isset($_SESSION['catalogue']['familys']) ) {
				$from = "?".http_build_query(array('catafam'=>'/'.$_SESSION['catalogue']['familys']['list'][$this->get('id')]['urlrewrite']));
			}
			if(!empty($art)){
				if($art->get('urlrewrite') != ''){
					return $dims->getProtocol().$dims->getHttpHost().'/'.$art->get('urlrewrite').".html".$from;
				}else{
					return $dims->getProtocol().$dims->getHttpHost().'/index.php?articleid='.$this->get('id_article_wce').$from;
				}
			}

		}
		if ( isset($_SESSION['catalogue']['familys']) && isset($_SESSION['catalogue']['familys']['list'][$this->get('id')]) ) {
			$dims = dims::getInstance();
			return $dims->getProtocol().$dims->getHttpHost().'/'.$_SESSION['catalogue']['familys']['list'][$this->get('id')]['urlrewrite'];
		}
		else {
			return '';
		}
	}
}
