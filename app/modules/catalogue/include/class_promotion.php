<?php
class cata_promotion extends pagination {

	const TABLE_NAME = 'dims_mod_cata_promotions';

	public $articles;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function open() {
		$id = func_get_arg(0);
		$open = parent::open($id);

		$this->articles = array();
		$sql = "SELECT ref_article, prix FROM dims_mod_cata_promotion_article WHERE id_promo = {$this->fields['id']} ORDER BY ref_article";
		$rs = $this->db->query($sql);
		while ($row = $this->db->fetchrow($rs)) {
			$this->articles[$row['ref_article']] = $row['prix'];
		}
		return $open;
	}

	public function save_lite() {
		parent::save();
	}

	public function save() {
		if (!$this->new) {
			$this->db->query("DELETE FROM dims_mod_cata_promotion_article WHERE id_promo = {$this->fields['id']}");
		}
		else {
			parent::save();
		}
		foreach ($this->articles as $ref_article => $prix) {
			if (trim($ref_article) != "" && trim($prix) != "") {
				$this->db->query("INSERT INTO dims_mod_cata_promotion_article VALUES ('{$this->fields['id']}','". trim($ref_article) ."','". floatval(str_replace(',', '.', $prix)) ."')");
			}
		}
		parent::save();
	}

	public function delete() {
		$this->db->query("DELETE FROM dims_mod_cata_promotion_article WHERE id_promo = {$this->fields['id']}");
		parent::delete();
	}

	public function build_index() {
		$a_promos = array();
		$rs = $this->db->query('SELECT * FROM `'.self::TABLE_NAME.'`');
		$this->total_index = $this->db->numrows($rs);
		while ($row = $this->db->fetchrow($rs)) {
			$promo = new cata_promotion();
			$promo->openFromResultSet($row);
			$a_promos[] = $promo;
		}
		return $a_promos;
	}

	public static function all($order = '',$params=array()) {
		$db = dims::getInstance()->getDb();

		$a_promos = array();

		$sql = 'SELECT * FROM `'.self::TABLE_NAME.'`';
		if ($order != '') {
			$sql .= ' ORDER BY '.$order;
		}

		$rs = $db->query($sql);
		while ($row = $db->fetchrow($rs)) {
			$promo = new cata_promotion();
			$promo->openFromResultSet($row);
			$a_promos[] = $promo;
		}

		return $a_promos;
	}

	public static function allIds() {
		$db = dims::getInstance()->getDb();

		$a_promos = array();
		$rs = $db->query('SELECT id FROM `'.self::TABLE_NAME.'`');
		while ($row = $db->fetchrow($rs)) {
			$a_promos[$row['id']] = $row['id'];
		}

		return $a_promos;
	}

	public function findByLabelAndDates($label, $date_deb, $date_fin) {
		if ($label != '' && $date_deb != '' && $date_fin != '') {
			$this->resultid	= $this->db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE libelle = \''.$label.'\' AND date_debut = \''.$date_deb.'\' AND date_fin = \''.$date_fin.'\'');
			$this->numrows	= $this->db->numrows($this->resultid);
			$this->fields	= $this->db->fetchrow($this->resultid);
			if ($this->numrows) {
                $this->open($this->fields['id']);
            }
			return $this->numrows;
		}
	}

	public static function allActives($order = '') {
		$db = dims::getInstance()->getDb();
		$ts = dims_createtimestamp();

		$a_promos = array();

		$sql = 'SELECT * FROM `'.self::TABLE_NAME.'` WHERE active = 1 AND date_debut < '.$ts.' AND date_fin > '.$ts;
		if ($order != '') {
			$sql .= ' ORDER BY '.$order;
		}

		$rs = $db->query($sql);
		while ($row = $db->fetchrow($rs)) {
			$promo = new cata_promotion();
			$promo->openFromResultSet($row);
			$a_promos[] = $promo;
		}

		return $a_promos;
	}

	public function isActive() {
		return $this->fields['active'];
	}

	public function getLibelle() {
		return stripslashes($this->fields['libelle']);
	}

	public function setLibelle($libelle) {
		$this->fields['libelle'] = addslashes(str_replace('"', '&quot;', $libelle));
	}

	public function getCode() {
		return stripslashes($this->fields['code']);
	}

	public function setCode($code) {
		$this->fields['code'] = addslashes($code);
	}

	public function getDateDebut() {
		return $this->fields['date_debut'];
	}

	public function getDateDebutFormatted() {
		$dd = $this->getDateDebut();
		if ($dd > 0) {
			$a_dd = dims_timestamp2local($dd);
			return $a_dd['date'];
		}
		else {
			return '';
		}
	}

	public function setDateDebut($date) {
		$this->fields['date_debut'] = $date;
	}

	public function getDateFin() {
		return $this->fields['date_fin'];
	}

	public function getDateFinFormatted() {
		$df = $this->getDateFin();
		if ($df > 0) {
			$a_df = dims_timestamp2local($df);
			return $a_df['date'];
		}
		else {
			return '';
		}
	}

	public function setDateFin($date) {
		$this->fields['date_fin'] = $date;
	}

	public function setIdUser($id_user) {
		$this->fields['id_user'] = $id_user;
	}

	public function setIdModule($id_module) {
		$this->fields['id_module'] = $id_module;
	}

	public function setIdWorkspace($id_workspace) {
		$this->fields['id_workspace'] = $id_workspace;
	}

	public function getArticles() {
		return $this->articles;
	}

	public function getAllArticles() {
		$a_objects = array();

		if(!$this->isNew()) {
			$rs = $this->db->query('
				SELECT 	a.*, pa.prix
				FROM `dims_mod_cata_promotion_article` pa
				INNER JOIN 	`'.article::TABLE_NAME.'` a
				ON 		a.reference = pa.ref_article
				WHERE 	pa.id_promo = '.$this->get('id').'
				ORDER BY a.reference');
			$separation = $this->db->split_resultset($rs);
			foreach ($separation as $sep) {
				$article = new article();
				$article->openFromResultSet($sep['a']);
				$a_objects[$sep['a']['reference']]['article'] = $article;
				$a_objects[$sep['a']['reference']]['prix'] = $sep['pa']['prix'];
			}
		}

		return $a_objects;
	}

	public function setArticles($articles) {
		$this->articles = $articles;
	}

	public function activate() {
		$this->fields['active'] = 1;
	}

	public function deactivate() {
		$this->fields['active'] = 0;
	}

	public function getImage() {
		if (!$this->fields['image_doc_id']) {
			return null;
		}

		include_once DIMS_APP_PATH.'/modules/doc/class_docfile.php';
		$doc = new docfile();
		$doc->open($this->fields['image_doc_id']);
		return $doc;
	}

	public function setImage($doc) {
		$this->fields['image_doc_id'] = $doc->get('id');
	}

	public function dropImage() {
		$this->fields['image_doc_id'] = 0;
		parent::save();
	}

}
