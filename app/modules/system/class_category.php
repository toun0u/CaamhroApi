<?php

require_once DIMS_APP_PATH.'modules/system/class_category_object.php';
require_once DIMS_APP_PATH.'modules/system/class_category_module.php';
require_once DIMS_APP_PATH.'modules/system/class_category_module_type.php';
require_once DIMS_APP_PATH.'modules/system/class_pagination.php';

class category extends pagination {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	private $subcat;//table des sous catégories
	private $rules; //table des sharing attachés à cette catégorie

	const DIMS_CATEGORY_PUPLIC = 0;
	const DIMS_CATEGORY_PRIVATE = 1;
	const DIMS_CATEGORY_UNIVERSAL = 2;

	const TABLE_NAME = 'dims_category';
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_CATEGORY;

	function __construct($pagined = true) {
		$this->setMatriceStandalone(true);
		parent::dims_data_object(self::TABLE_NAME, 'id');
		$this->isPageLimited = $pagined;

		$this->subcat = array();
		$this->rules = array();
	}

	public function  defineMatrice() {
		$this->matrice = array();
		$this->matrice['id_parent']['value'] = '';
	}

	function setid_object() {
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	function settitle() {
		$this->title = $this->fields['label'];
	}

	// Suppression des différents liens module/module type/objet
	public function delete(){
	$sel = "SELECT	*
		FROM	".self::TABLE_NAME."
		WHERE	id_parent = :idcategory";
	$res = $this->db->query($sel, array(
		':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	while ($r = $this->db->fetchrow($res)){
		$cat = $this->getInstance();
		$cat->openWithFields($r);
		$cat->delete();
	}
		$del = "DELETE FROM	dims_category_module
				WHERE		id_category = :idcategory";
		$this->db->query($del, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$del = "DELETE FROM	dims_category_module_type
				WHERE		id_category = :idcategory";
		$this->db->query($del, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$del = "DELETE FROM	dims_category_object
				WHERE		id_category = :idcategory";
		$this->db->query($del, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
	$sel = "SELECT	*
		FROM	".self::TABLE_NAME."
		WHERE	position > :position
		AND		id_parent = :idparent
		LIMIT	1";
	$res = $this->db->query($sel, array(
			':position' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
			':idparent' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_parent']),
		));
	if($r = $this->db->fetchrow($res)){
		$q = $this->getInstance();
		$q->openWithFields($r);
		$q->fields['position']--;
		$q->save();
	}
		parent::delete();
	}

	public function getInstance(){
	return new category();
	}

	/*
	 * Save -> surchargé pour pouvoir balancer l'id root_reference si il n'a pas déjà été enregistré sur tous les descedants
	 */
	public function save(){
		$new = $this->new;
		$prev_idp = $this->matrice['id_parent']['value'];
		if ($this->fields['id_parent'] != '' && $this->fields['id_parent'] > 0 && strpos($this->fields['parents'],$this->fields['id_parent']) === false){
			$par = new category();
			$par->open($this->fields['id_parent']);
			if ($par->fields['parents'] != '')
				$this->fields['parents'] = $par->fields['parents'].";".$par->getID();
			else
				$this->fields['parents'] = $par->getID();
		}
		parent::save(dims_const::_SYSTEM_OBJECT_CATEGORY); // permet l'enregistrement de l'objet
		if( ( $this->isRoot() && $new && ( is_null($this->getRootReference()) || $this->getRootReference() == 0 ) )
			|| ( !$new && $this->isRoot() && $prev_idp != $this->getParentID() ) )
		{
			$this->updateRootFamily();
		}
	}

	//parcours récursif de l'arborescence pour donner le root ref à chaque descendant
	public function updateRootFamily(){
		$ref = $this->getID();
		$pile = array();
		$pile[] = $this;

		while(count($pile)){
			$cat = array_pop($pile);
			$cat->setRootReference($ref);
			$cat->save();

			foreach($cat->getSubCategories() as $c){
				$pile[] = $c;
			}
		}
	}

	/*
	 * open complet c'est à dire qu'il charge la descendance de this
	 */
	public function fullOpen($id){
		$this->open($id);
		$this->initDescendance();
		if($this->isRoot()){
			$this->initRules();
		}
	}

	//initialisation de la descendance de this | prérequis --> $this doit être opend
	public function initDescendance(){
		//chargement de la descendance
		$sql = "SELECT * FROM ".$this->tablename.
			   " WHERE root_reference = :rootreference
				AND id_parent >= :idcategory
				ORDER BY id_parent ASC, position ASC";
		$res = $this->db->query($sql, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':rootreference' => array('type' => PDO::PARAM_INT, 'value' => $this->getRootReference()),
		));
		//traitement séquenciel des lignes
		$curPID = $this;
		while($tab = $this->db->fetchrow($res)){
			$sub = new category();
			$id_parent = $tab['id_parent'];
			if($curPID->getID() != $id_parent){//il faut aller rechercher le bon parent
				$curPID = $this->findChild($id_parent);
			}

			if(!is_null($curPID)){
				$sub->openFromResultSet($tab);
				$curPID->addCategoryObject($sub);
			} else break;
		}
	}

	/* retrouve une catégorie fille dans la descendance de $this, dont l'id est passé en paramètre */
	public function findChild($id){
		$pile = array();
		$pile[] = $this;

		while(count($pile)){
			$cat = array_pop($pile);
			if($cat->getID() == $id) return $cat;
			foreach($cat->getSubCategories() as $c){
				$pile[] = $c;
			}
		}
		return null;
	}

	public function getDirectChilds(){
		$sel = "SELECT		*
			FROM		".self::TABLE_NAME."
			WHERE		id_parent = :idparent
			ORDER BY	position";
		$res = $this->db->query($sel, array(':idparent' => $this->fields['id']));
		$lst = array();
		while($r = $this->db->fetchrow($res)){
			$sub = new category();
			$sub->openFromResultSet($r);
			$lst[] = $sub;
		}
		return $lst;
	}

	//retourne la liste des sous-catégories
	public function getSubCategories(){
	if (empty($this->subcat)){
		$this->subcat = $this->getDirectChilds();
	}
		return $this->subcat;
	}

	public function getLabel(){
		return $this->fields['label'];
	}

	public function setLabel($val){
		$this->fields['label'] = $val;
	}

	public function getLevel(){
		return $this->fields['level'];
	}
	public function setLevel($val){
		$this->fields['level'] = $val;
	}

	public function setParent($val){
		$this->fields['id_parent'] = $val;
		if($this->matrice['id_parent']['value'] != $val){//il faut aussi penser à repositionner l'élément this en fin de liste
			$father = new category();
			$father->fullOpen($val);
			$nb_elems = count($father->getSubCategories());
			$this->setPosition($nb_elems +1);
		}
	}

	public function getParentID(){
		return $this->fields['id_parent'];
	}

	public function setPosition($val, $init = false){
		if($init){
			//obtention du père pour réorganiser les frères
			$father = new category();
			$father->fullOpen($this->getParentID());
			$brothers = $father->getSubCategories();//assume qu'ils sont rangés par ordre de position
			for($i=0; $i<count($brothers); $i++){

				if($val < $this->getPosition()){
					if($brothers[$i]->getPosition() >= $val && $brothers[$i]->getPosition() < $this->getPosition()){
						$brothers[$i]->setPosition($brothers[$i]->getPosition() + 1);
						$brothers[$i]->save();
					}
				}
				else{
					if($brothers[$i]->getPosition() <= $val && $brothers[$i]->getPosition() > $this->getPosition()){
						$brothers[$i]->setPosition($brothers[$i]->getPosition() - 1);
						$brothers[$i]->save();
					}
				}
			}
			$this->fields['position'] = $val;
		}
		else $this->fields['position'] = $val;
	}

	public function getPosition(){
		return $this->fields['position'];
	}

	//définit si l'objet courant est une racine d'arborescence ou non
	public function isRoot(){
		return is_null($this->getParentID()) || $this->getParentID() == 0;
	}


	public function setRootReference($val){
		$this->fields['root_reference'] = $val;
	}

	public function getRootReference(){
		return $this->fields['root_reference'];
	}

	public function getID(){
		return $this->fields['id'];
	}

	//fonction permettant de créer une racine
	public function createRoot($lab, $lev, $object_id = null){
		$this->init_description();
		$this->setugm();
		$this->setLabel($lab);
		$this->setLevel($lev);
		$this->save();

		if($this->isPrivate()){

			$this->addModuleRule($this->fields['id_module']);
			if(!is_null($object_id)){
				$this->addObjectRule($this->fields['id_module'], $object_id);
			}

		}
	}

	//fonction permettant de créer une sous-catégorie et de l'ajouter à la liste de l'objet
	public function addSubCategory($lab, $lev = null){
		$sub = new category();
		$sub->init_description();
		$sub->setugm();
		$sub->setLabel($lab);
		$sub->setLevel((is_null($lev))?$this->getLevel():$lev);
		$sub->setPosition(count($this->getSubCategories()) + 1);//par défaut on l'affiche à la fin
		$sub->setParent($this->getID());
		if ($this->fields['parents'] != '')
			$sub->fields['parents'] = $this->fields['parents'].";".$this->getID();
		else
			$sub->fields['parents'] = $this->getID();

		//si c'est null ou vide ce sera 0 par défaut
		$sub->setRootReference($this->getRootReference());
		$sub->save();
		array_unshift($this->subcat, $sub);
		return $sub;
	}

	//ajoute une catégorie sous forme d'objet déjà ouvert à la liste des catégories de l'objet
	public function addCategoryObject($obj){
		array_unshift($this->subcat , $obj);//pour pouvoir tenir compte des positions imposées par la colonne position
	}

	//fonction permettant de dessiner simplement l'arborescence - utile pour le debug
	public function simpleDraw(){
		$pile = array();
		$root = array();
		$root['deep'] = 0;
		$root['cat'] = $this;
		$pile[] = $root;

		while(count($pile)){
			$cat_array = array_pop($pile);
			$cat = $cat_array['cat'];
			$deep = $cat_array['deep'];

				for($i =0; $i<$deep ; $i++){
				echo '|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			echo (($cat->hasSubCategories())?'|-':'&nbsp;&nbsp;');
			echo '	'.$cat->getLabel().'<br/>';

			foreach($cat->getSubCategories() as $c){
				$s = array();
				$s['deep'] = $deep + 1;
				$s['cat'] = $c;
				$pile[] = $s;
			}
		}
	}

	// fonction permettant d'afficher l'arborescence avec la classe dims_browser
	public function drawBrowser($name = 'listeCateg', $file = '/modules/system/class_category.tpl.php'){
        if($file == '/modules/system/class_category.tpl.php')
            $file = DIMS_APP_PATH.$file;
		require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');
		$arbo = $this->getArborescence();
		$res[$this->fields['id']] = $arbo;
		$browser = new dims_browser($arbo['nbLvl']+1,$res,$name);
		$browser->displayBrowser($file);
	}

	public function getArborescence(){
		$lvl = 0;
		if (count($this->getSubCategories()) > 0)
			$lvl ++;
		$res = array('data' => $this->getLabel(), 'child' => array(), 'nbLvl' => $lvl);
		$tmp = 0;
		foreach( $this->getSubCategories() as $cat){
			$r = $cat->getArborescence();
			if ($r['nbLvl'] > $tmp)
				$tmp = $r['nbLvl'];
			$res['child'][$cat->fields['id']] = $r;
		}
		$res['nbLvl'] += $tmp;
		return $res;
	}

	public function getArboForEdit(){
		$lvl = 0;
		if (count($this->getSubCategories()) > 0)
			$lvl ++;
		$res = array('data' => $this->getLabel().'<img src="./common/img/view.png" style="cursor:pointer;float:right;" /><input type="hidden" value="'.$this->fields['id'].'" />', 'child' => array(), 'nbLvl' => $lvl);
		$tmp = 0;
		foreach( $this->getSubCategories() as $cat){
			$r = $cat->getArboForEdit();
			if ($r['nbLvl'] > $tmp)
				$tmp = $r['nbLvl'];
			$res['child'][$cat->fields['id']] = $r;
		}
		$res['child'][0] = array('data' => '<span onclick="javascript:addCateg('.$this->fields['id'].');">Ajouter une cat&eacute;gorie</span>', 'child' => array());
		$res['nbLvl'] += $tmp;
		return $res;
	}

	//fonction permettant d'indiquer si l'objet courant possède des sous-catégories
	public function hasSubCategories(){
		return count($this->getSubCategories()) > 0;
	}

	//indique si la catégorie est privée
	public function isPrivate(){
		return self::DIMS_CATEGORY_PRIVATE == $this->getLevel();
	}

	public function getAriane(){
		if($this->isRoot())
			return $this->getLabel();
		else{
			$categ = new category();
			$categ->open($this->fields['id_parent']);
			return $categ->getAriane().'&nbsp;&gt;&nbsp;'.$this->getLabel();
		}
	}

	public function getArianeNoRoot(){
		if(!$this->isRoot()){
			$categ = new category();
			$categ->open($this->fields['id_parent']);
		if (!$categ->isRoot())
		return $categ->getArianeNoRoot().'&nbsp;&gt;&nbsp;'.$this->getLabel();
		else
		return $this->getLabel();
		}
	else return "";
	}

	public function getBrowserAriane(){
		if($this->isRoot())
			return array($this->fields['id']);
		else{
			$categ = new category();
			$categ->open($this->fields['id_parent']);
			$res = $categ->getBrowserAriane();
			$res[] = $res[count($res)-1].$this->fields['id'];
			return $res;
		}
	}

	public static function getListModObj($module_type,$object_type,$isSelect = false){
		if ($module_type != '' && $module_type > 0 && $object_type != '' && $object_type > 0){
			$db = dims::getInstance()->getDb();
			$lst = array();
			$sel = "SELECT	id_category
					FROM	dims_category_object
					WHERE	object_id_module_type = :idmoduletype
					AND		id_object = :idobject ";
			$res = $db->query($sel, array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $module_type),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $object_type),
			));
			while($r = $db->fetchrow($res)){
				$categ = new category();
				$categ->open($r['id_category']);
				$categ->initDescendance();
				if ($isSelect)
					$lst = array_merge_recursive($lst,$categ->getListSelect());
				else
					$lst = array_merge_recursive($lst,$categ->getDescListModObj());
			}
			return $lst;
		}else
			return false;
	}

	private function getListSelect(){
		$lst = array();
		$elem = array();
		$elem['id'] = $this->getId();
		$elem['label'] = $this->getAriane();
		$lst[] = $elem;
		foreach($this->getSubCategories() as $desc)
			$lst = array_merge_recursive($lst,$desc->getListSelect());
		return $lst;
	}

	private function getDescListModObj(){
		$lst = array();
		$elem = array();
		$elem[$this->getId()] = $this->getLabel();
		foreach($this->getSubCategories() as $desc)
			$elem['child'] = $desc->getDescListModObj();
		$lst[] = $elem;
		return $lst;
	}

	public function searchGbLinkChild($object = 0){
		$res = $this->searchGbLink($object);
		if ($this->hasSubCategories())
			foreach ($this->getSubCategories() as $desc)
				$res = array_merge($res,$desc->searchGbLinkChild($object));
		return $res;
	}

	public function getMyRoot($mode = 'full'){
		if($this->isRoot()) return $this;
		else{

			$c = new category();
			switch($mode){
				default:
				case 'full' :
					$c->fullOpen($this->getRootReference());
				break;
				case 'light' :
					$c->open($this->getRootReference());
				break;
			}
			return $c;
		}
	}

	public function getGOID(){
		return $this->fields['id_globalobject'];
	}

	public function getUser(){
		return $this->fields['id_user'];
	}
	/* ------------------- GESTION DES DROITS ---------------------------- */

	public function addModuleTypeRule($mt){
		$rule = new category_module_type();
		$rule->create($this->getID(), $mt);
		$rule->save();
		$this->rules['module_types'][] = $rule;
	}

	public function addModuleRule($m){
		$rule = new category_module();
		$rule->create($this->getID(), $m);
		$rule->save();
		$this->rules['modules'][] = $rule;
	}

	public function addObjectRule($m, $o){
		$rule = new category_object();
		require_once(DIMS_APP_PATH."modules/system/class_module.php");
		$module = new module();
		$module->open($m);
		$rule->create($this->getID(),$o, $module->fields['id_module_type']);
		$rule->save();
		$this->rules['objects'][] = $rule;
	}

	public function initRules(){
		$sql = "SELECT *
				FROM dims_category_module_type
				WHERE id_category = :idcategory";

		$res = $this->db->query($sql, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while($tab = $this->db->fetchrow($res)){
			$rule = new category_module_type();
			$rule->openFromResultSet($tab);
			$this->rules['module_types'][] = $rule;
		}

		$sql = "SELECT *
				FROM dims_category_module
				WHERE id_category = :idcategory";

		$res = $this->db->query($sql, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while($tab = $this->db->fetchrow($res)){
			$rule = new category_module();
			$rule->openFromResultSet($tab);
			$this->rules['modules'][] = $rule;
		}

		$sql = "SELECT *
				FROM dims_category_object
				WHERE id_category = :idcategory";

		$res = $this->db->query($sql, array(
			':idcategory' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while($tab = $this->db->fetchrow($res)){
			$rule = new category_object();
			$rule->openFromResultSet($tab);
			$this->rules['objects'][] = $rule;
		}
	}

	public function getRules($mode = 'full'){
		switch($mode){

			case 'full':
				return $this->rules;
			break;
			default:
				return $this->rules[$mode];
			break;
		}

	}

	/*
	 * Fonction qui permet à tout moment, selon le contexte (user, workspace, module, object), de savoir si un système de catégories est utilisable
	 * L'algo va se dérouler en 4 étapes :
	 * 0. Si la catégorie est universelle, on ne se pose pas la question, elle est visible
	 * 1. On teste si l'utilisateur passé en param est le propriétaire de la catégorie si celle-ci est privée
	 * 2. On teste si pour le module type ou pour le module donné en param, on peut passer`
	 * 3. Si un objet est passé en param, on le teste pour savoir si il est couvert par une exception donné
	 */
	public function isUsable($u, $m, $o=null){
		$root = $this->getMyRoot();//on bosse sur la racine parce que les règles portent uniquement sur la racine d'une catégorisation

		/* ETAPE 0 - catégorie universelle ? -------------------*/
		if($root->getLevel() == self::DIMS_CATEGORY_UNIVERSAL) return true;
		/* -------- ETAPE 1 - test de propriété sur l'utilisateur --------------------------- */
		if(!is_null($u) && $root->isPrivate()){
			if($u != $root->getUser()){
				return false;
			}
		}

		$step_modules = false;
		if(!is_null($m)){
			require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
			$module = new module();
			$module->open($m);
			$mt = $module->fields['id_module_type'];//récupération du module type

			/* -------- ETAPE 2 - test sur l'existence d'une règle par module ou module type --------------------------- */
			if(isset($root->rules['module_types'])){
				foreach($root->rules['module_types'] as $r){
					if($r->getModuleType() == $mt){
						$step_modules = true;
						break;
					}
				}
			}

			if(!$step_modules && isset($root->rules['modules'])){//si le module_type n'a rien donné, on chèque au niveau des m
				foreach($root->rules['modules'] as $r){
					if($r->getModule() == $m){
						$step_modules = true;
						break;
					}
				}
			}

			/* -------- ETAPE 3 - test sur l'existence d'une règle par module ou module type --------------------------- */
			if($step_modules){//alors on peut tester si il y a une exception-prioritaire pour l'id_object passé en paramètre
				if( isset($root->rules['objects']) && count($root->rules['objects']) == 0 ) return true; // tout roule, l'ensemble du module, voire module_type est couvert par la catégorie
				else
				{
					if(!is_null($o) && isset($root->rules['objects'])){
						foreach($root->rules['objects'] as $r){
							if($r->fields['id_object'] == $o && $r->fields['object_id_module_type'] == $mt){
								return true;
							}
						}
						return false;//si on arrive là c'est que l'objet passé en param n'est pas lié à la catégorie
					}
					else return true;//si on ne passe pas d'objet c'est qu'on veut savoir pour le module courant, si on peut utiliser la catégorie
				}
			}
			else return false;
		}

		else return false;
	}

	//combinaison pour la pagination : On part depuis la caégorie courante
	public function getAll($search_text='', $pagination=false){
		$params = array();
		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getAll($search_text, true));
			$limit = " LIMIT :limitstart, :limitkey";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}
		else $limit="";
		$where = "WHERE parents LIKE :id ";
		$params[':id'] = array('type' => PDO::PARAM_STR, 'value' => $this->getId().'%');
		if(!empty($search_text)){
			$where .= " AND label LIKE :tag";
			$params[':tag'] = array('type' => PDO::PARAM_STR, 'value' => '%'.str_replace(' ', '%', $search_text).'%');
		}

		$sql =	"SELECT *
				 FROM ".self::TABLE_NAME."
				 $where
				 ORDER BY parents ASC, position ASC " . $limit;

		$res_sql = $this->db->query($sql, $params);
		$all = array();
		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($res_sql);
		}
		else {
			while($tab = $this->db->fetchrow($res_sql)){
				$res = array();
				$cat = new category();
				$cat->openFromResultSet($tab);
				$cat->fields['parents']=array();
				$cat->fields['labelparents']=array();

				if (isset($all[$cat->fields['id_parent']])) {
					$elempere=$all[$cat->fields['id_parent']];
					if (isset($elempere->fields['parents'])) {
					$cat->fields['parents']=$elempere->fields['parents'];

					if ($elempere->fields['parents']!='') {
						$cat->fields['parents'].=",";
						$cat->fields['labelparents'].=" > ";
					}
					}
					$cat->fields['parents'].=$cat->fields['id_parent'];
					$cat->fields['labelparents'].=$cat->fields['label'];
				}

				$all[$cat->fields['id']] = $cat;
			}
			return $all;
		}
	}

	public function getAllCateg($moduletypeid=0) {
		$resultelems = array();
		$labels = array();
		$taillemax = 32;

		if ($moduletypeid>0) {
			// on construction des points d'entree
			if ($moduletypeid>0) {
				// on construction des points d'entree
				$sql =	"SELECT *
						FROM dims_category_object as c
						WHERE object_id_module_type=:mti";

				$params[':mti']= array('type' => PDO::PARAM_INT, 'value' => $moduletypeid);
				$res_sql = $this->db->query($sql, $params);
				while($elem = $this->db->fetchrow($res_sql)){
					$arrayfirst[]=$elem['id_category'];
				}
			}

			$params=array();
			$where="";

			if (!empty($arrayfirst)) {
				foreach ($arrayfirst as $k => $id_categ) {
					if ($where=="") $where=" WHERE (";
					else $where.= " OR ";
					$where.= " parents LIKE :cat".$k;

					$where.= " OR id = :id".$k;
					$params[':cat'.$k] = array('type' => PDO::PARAM_STR, 'value' => $id_categ.'%');
					$params[':id'.$k] = array('type' => PDO::PARAM_INT, 'value' => $id_categ);
				}
				if ($where!="") $where.=") ";
			}

			$sql =	"SELECT *
					FROM ".self::TABLE_NAME." as c
					$where
					ORDER BY c.parents ASC, c.position ASC ";

			$res_sql = $this->db->query($sql, $params);

			while ($elem = $this->db->fetchrow($res_sql)) {
				$resultelems[$elem['id']]=$elem;

				if (isset($elem['id_parent'])) {
					if ($elem['id_parent']==0) {
						// on a le premier niveau
						$resultelems[$elem['id_parent']]['id']=0;
						$resultelems[$elem['id_parent']]['label']='root';
					}

					if ($elem['id_parent']>=0 && isset($resultelems[$elem['id_parent']])) {
						if (!isset($resultelems[$elem['id_parent']]['children']))
							$resultelems[$elem['id_parent']]['children']=array();

						$resultelems[$elem['id_parent']]['children'][]=$elem['id'];
					}
				}
			}
		}

		return $resultelems;
	}
}
