<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_valeur.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_lang.php";

class cata_champ extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_champ';
	const MY_GLOBALOBJECT_CODE = 231;

	const TYPE_TEXT = 'texte';
	const TYPE_LIST = 'liste';

	private $defaultLang = 1; // French
	private $types = array('texte'=>'texte','liste'=>'liste');

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id','id_module');
	}

	function save() {
		if(!isset($this->fields['id_module']) || ($this->fields['id_module'] == '' || $this->fields['id_module'] <= 0))
			$this->fields['id_module'] = $_SESSION["dims"]['moduleid'];
		if($this->isNew()){
			$db = dims::getInstance()->getDb();
			$sel = "SELECT		id
					FROM		".self::TABLE_NAME."
					WHERE		id_module = ".$this->fields['id_module']."
					ORDER BY	id";
			$res = $db->query($sel);
			$this->fields['id'] = 1;
			while($r = $db->fetchrow($res)){
				if($r['id'] == $this->fields['id'])
					$this->fields['id'] ++;
				else
					break;
			}
		}

		include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
		$id = $this->fields['id'];

		$art = new article();
		$art->init_description();
		if(!isset($art->fields['fields'.$id])){
			$sql = "ALTER TABLE ".article::TABLE_NAME." ADD fields".$id." VARCHAR(50)  NULL  DEFAULT NULL;";
			$db = dims::getInstance()->getDb();
			$db->query($sql);
			// On recharge la description de la table
			$art->getTableDescription(true);
			$art->inserMbField('fields'.$id);
		}


		$return = parent::save(self::MY_GLOBALOBJECT_CODE);
		$this->fields['id'] = $id;

		return $return;
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".cata_champ_valeur::TABLE_NAME."
				WHERE	id_chp = ".$this->fields['id'];
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$val = new cata_champ_valeur();
			$val->openFromResultSet($r);
			$val->delete();
		}

		// TODO : changer la mécanique pour la synchro : cf commentaire en dessous
		include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
		$sql = "UPDATE	".article::TABLE_NAME."
				SET	fields".$this->fields['id']." = NULL
				WHERE	id_module = ".$this->fields['id_module']."
				AND	fields".$this->fields['id']." IS NOT NULL";
		dims::getInstance()->getDb()->query($sql);

		/*$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".article::TABLE_NAME."
				WHERE	fields".$this->fields['id']." IS NOT NULL
				AND	id_module = ".$this->fields['id_module']."";
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$art = new article();
			$art->openFromResultSet($r);
			$art->fields["fields".$this->fields['id']] = "";
			$art->save();
		}*/

		parent::delete();
	}

	public static function getByLabel($label) {
		$db = dims::getInstance()->getDb();
		$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE libelle LIKE "'.$label.'" LIMIT 1');
		if ($db->numrows($rs)) {
			return $db->fetchrow($rs);
		}
		else {
			return null;
		}
	}

	public static function getObjectByLabel($label) {
		$db = dims::getInstance()->getDb();
		$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE libelle LIKE "'.$label.'" LIMIT 1');
		if ($db->numrows($rs)) {
			$champ = new cata_champ();
			$champ->openFromResultSet($db->fetchrow($rs));
			return $champ;
		}
		else {
			return null;
		}
	}

	public function getDefaultLang(){
		return $this->defaultLang;
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = $this->fields['libelle'];
	}

	public function setLabel($label) {
		$this->fields['libelle'] = $label;
	}

	public function setType($type) {
		$this->fields['type'] = $type;
	}

	public function setFiche($fiche) {
		$this->fields['fiche'] = $fiche;
	}

	public function setFiltre($filtre) {
		$this->fields['filtre'] = $filtre;
	}

	public function setPermanent($permanent) {
		$this->fields['permanent'] = $permanent;
	}

	public function addLibelle($id_lang, $libelle){
		if($this->isNew())
			$this->save();
		$lg = new cata_champ_lang();
		$lg->open($this->fields['id'],$id_lang,$this->fields['id_module']);
		if($lg->isNew()){
			$lg = new cata_champ_lang();
			$lg->init_description();
			$lg->fields['id_lang'] = $id_lang;
			$lg->fields['id_chp'] = $this->fields['id'];
			$lg->fields['id_module'] = $this->fields['id_module'];
		}
		$lg->fields['libelle'] = $libelle;
		$lg->save();
		if($id_lang == $this->defaultLang){
			$this->fields['libelle'] = $libelle;
			$this->save();
		}
	}

	public function addValues($lang, $values = array(), $delete = true){
		if($this->isNew())
			$this->save();

		// pour éviter les doublons
		$values = array_unique($values);
		// pour supprimer les valeurs vides
		$values = array_filter($values, function($value) { return trim($value) != ''; });
		// pour réinitialiser les clés
		$values = array_values($values);
		// pour pouvoir accéder à la position de l'élément facilement
		$values = array_flip($values);

		// dims_print_r($values);

		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".cata_champ_valeur::TABLE_NAME."
				WHERE	id_chp = ".$this->fields['id']."
				AND	id_lang = $lang
				AND	id_module = {$this->fields['id_module']}";
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$val = new cata_champ_valeur();
			$val->openFromResultSet($r);

			// mise à jour de la position
			if (isset($values[$val->fields['valeur']])) {
				$val->fields['position'] = $values[$val->fields['valeur']] + 1;
				$val->save();
			}
			// suppression de la valeur
			elseif ($delete) {
				$val->delete();
			}

			unset($values[$val->fields['valeur']]);
		}

		// Insertion des éléments restants
		foreach ($values as $value => $p) {
			$val = new cata_champ_valeur();
			$val->fields['id_chp'] = $this->fields['id'];
			$val->fields['valeur'] = $value;
			$val->fields['position'] = $p + 1;
			$val->fields['id_lang'] = $lang;
			$val->fields['id_module'] = $this->fields['id_module'];
			$val->save();
		}
	}

	public function addGlobalFilterLabel($id_lang, $label){
		if($this->isNew())
			$this->save();
		$lg = new cata_champ_lang();
		$lg->open($this->fields['id'],$id_lang,$this->fields['id_module']);
		if($lg->isNew()){
			$lg = new cata_champ_lang();
			$lg->init_description();
			$lg->fields['id_lang'] = $id_lang;
			$lg->fields['id_chp'] = $this->fields['id'];
			$lg->fields['id_module'] = $this->fields['id_module'];
		}
		$lg->fields['global_filter_label'] = $label;
		$lg->save();
	}

	function getvaleurs($lang, $simple = false, $values_ids = array(),$optimize=false) {
		$valeurs = array();
		if(!$this->isNew()){
			$db = dims::getInstance()->getDb();
			$sql = "SELECT		*
					FROM		".cata_champ_valeur::TABLE_NAME."
					WHERE		id_chp = {$this->fields['id']}
					AND		id_lang = $lang
					AND		id_module = {$this->fields['id_module']}";
			if (sizeof($values_ids)) {
				// FIXME : Use PDO params.
				// limite les résultats à la liste d'ids fournie en paramètre
				foreach ($values_ids as $k => $v) {
					// On ajoute les quotes pour les filtres de type texte
					$values_ids[$k] = '"'.addslashes($v).'"';
				}
				$sql .= " AND id IN (".implode(',', $values_ids).")";
			}
			$sql .= "
					ORDER BY	position";
			$res = $db->query($sql);
			if($simple){
				while ($row = $db->fetchrow($res)) {
					$valeurs[$row['id']] = $row['valeur'];
				}
			}else{
				while ($row = $db->fetchrow($res)) {

					if (!$optimize) {
						$val = new cata_champ_valeur();
						$val->openFromResultSet($row);
					}
					else {
						$val=$row;
					}
					$valeurs[$row['id']] = $val;
				}
			}
		}
		return $valeurs;
	}

	function getPropertiesFamille($id_famille){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ_famille.php";
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".cata_champ_famille::TABLE_NAME."
				WHERE	id_famille = $id_famille
				AND	id_champ = ".$this->fields['id']."
				AND	status = ".cata_champ_famille::_STATUS_OK;
		$res = $db->query($sel);
		if($r = $db->fetchrow($res)){
			$prop = new cata_champ_famille();
			$prop->openFromResultSet($r);
			return $prop;
		}else
			return null;
	}

	public static function getAll($keywords = ""){
		$query = "";
		if(!empty($keywords)){
			$query = "	INNER JOIN	".cata_champ_lang::TABLE_NAME." cl
						ON			cl.id_chp = c.id
						AND		cl.id_module = c.id_module
						AND		cl.libelle LIKE '%$keywords%' ";
		}
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		c.*
				FROM		".self::TABLE_NAME." c
				$query
				WHERE		c.id_module = ".$_SESSION['dims']['moduleid']."
				GROUP BY	c.id
				ORDER BY	c.libelle";
		$res = $db->query($sel);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$champ = new cata_champ();
			$champ->openFromResultSet($r);
			$lst[$champ->get('id')] = $champ;
		}
		return $lst;
	}

	public static function getAllGo($keywords){
		$lst = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		c.id_globalobject
				FROM		".self::TABLE_NAME." c
				INNER JOIN	".cata_champ_lang::TABLE_NAME." cl
				ON			cl.id_chp = c.id
				WHERE		c.id_module = ".$_SESSION['dims']['moduleid']."
				AND		cl.libelle LIKE '%$keywords%'";
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$lst[] = $r['id_globalobject'];
		}
		return $lst;
	}

	public static function getTypes(){
		$ch = new cata_champ();
		return $ch->types;
	}

	public function getLibelle($lang = null){
		if(is_null($lang)){
			return $this->fields['libelle'];
		}else{
			$lg = new cata_champ_lang();
			$lg->open($this->fields['id'],$lang,$this->fields['id_module']);
			if(isset($lg->fields['libelle']) && $lg->fields['libelle'] != '')
				return $lg->fields['libelle'];
			else
				return $this->fields['libelle'];
		}
	}

	public function isGlobalFilter() {
		return $this->fields['global_filter'] == 1;
	}

	public function getGlobalFilterLabel($lang = null) {
		if(is_null($lang)){
			return '';
		}else{
			$lg = new cata_champ_lang();
			$lg->open($this->fields['id'],$lang,$this->fields['id_module']);
			if(isset($lg->fields['global_filter_label']) && $lg->fields['global_filter_label'] != '')
				return $lg->fields['global_filter_label'];
			else
				return '';
		}
	}

	public function getGlobalFilterValue() {
		return $this->fields['global_filter_value'];
	}

	public static function getRootCategory(){
		include_once DIMS_APP_PATH."modules/system/class_category.php";
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		c.*
				FROM		".category::TABLE_NAME." c
				INNER JOIN	".category_object::TABLE_NAME." co
				ON			co.id_category = c.id
				WHERE		co.id_object = ".self::MY_GLOBALOBJECT_CODE."
				AND		co.object_id_module_type = ".$_SESSION['dims']['moduletypeid'];
		$res = $db->query($sel);
		$cat = new category();
		if($r = $db->fetchrow($res)){
			$cat->openFromResultSet($r);
		}else{
			$cat->createRoot("ROOT Champs libres",category::DIMS_CATEGORY_PRIVATE,self::MY_GLOBALOBJECT_CODE);
		}
		return $cat;
	}

	public function getLabelCateg(&$lstCateg = array()){
		include_once DIMS_APP_PATH."modules/system/class_category.php";
		$myCateg = current($this->searchGbLink(category::MY_GLOBALOBJECT_CODE));
		if($myCateg != '' && $myCateg > 0){
			if(!isset($lstCateg[$myCateg])){
				$cat = new category();
				$cat->openWithGB($myCateg);
				$lstCateg[$myCateg] = $cat;
			}
			$cat = $lstCateg[$myCateg];
			return $cat->getLabel();
		}else
			return "";
	}

	#Cyril - Méthode qui retourne la liste des champs donnée en param complétés par leurs valeurs
	#quand il s'agit de champs de type liste
	public static function completeListOfValuesFor($lstChamps){
		$db = dims::getInstance()->getDb();
		if( ! empty($lstChamps)){
			$res = $db->query("SELECT cv.id_chp, cv.id_lang, cv.valeur, cv.id
							   FROM ".cata_champ_valeur::TABLE_NAME." cv
							   INNER JOIN ".self::TABLE_NAME." c ON c.id = cv.id_chp AND cv.id_module = c.id_module
							   WHERE c.type='".self::TYPE_LIST."' AND id_chp IN (".implode(',', array_keys($lstChamps)).")
							   ORDER BY id_chp, id_lang, position ASC ");

			$values = array();
			while($fields = $db->fetchrow($res)){
				$values[$fields['id_chp']][$fields['id_lang']][$fields['id']] = $fields['valeur'];
			}
			foreach($values as $id_chp => $liste){
				$lstChamps[$id_chp]->setLightAttribute('values', $liste);
			}
		}
		return $lstChamps;
	}

	public static function sortByCategories($lstChamps){
		include_once DIMS_APP_PATH."modules/system/class_category.php";
		$db = dims::getInstance()->getDb();
		$rearange = array();
		if( ! empty($lstChamps)){
			$res =  $db->query("SELECT		chp.id as champ_id, c.label as categ_label
								FROM		".self::TABLE_NAME." chp
								INNER JOIN  dims_globalobject_link gbl
								ON			gbl.id_globalobject_from = chp.id_globalobject
								INNER JOIN  ".category::TABLE_NAME." c
								ON			c.id_globalobject = gbl.id_globalobject_to
								WHERE		chp.id IN (".implode(',', array_keys($lstChamps)).")
								ORDER BY chp.libelle");

			while($fields = $db->fetchrow($res)){
				$rearange[$fields['categ_label']][$fields['champ_id']] = $lstChamps[$fields['champ_id']];
			}
		}
		return $rearange;
	}

	public static function allGlobalFilters() {
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		c.*
				FROM		`".self::TABLE_NAME."` c
				WHERE		c.`id_module` = :id_module
				AND 		c.`global_filter` = 1
				GROUP BY	c.`id`
				ORDER BY	c.`libelle`";
		$res = $db->query($sel, array(
			':id_module' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid'])
			));
		$lst = array();
		while($r = $db->fetchrow($res)){
			$champ = new cata_champ();
			$champ->openFromResultSet($r);
			$lst[$champ->get('id')] = $champ;
		}
		return $lst;
	}

}
