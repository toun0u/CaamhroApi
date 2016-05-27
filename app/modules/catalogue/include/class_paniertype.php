<?php
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_paniertype_detail.php';

class paniertype extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_panierstypes';

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	public function paniertype() {
		parent::dims_data_object('dims_mod_cata_panierstypes','id');
	}

	public function save() {
		if ($this->new) {
			if (!isset($this->fields['id_user'])) $this->fields['id_user'] = $_SESSION['dims']['userid'];
			if (!isset($this->fields['id_group']) && isset($_SESSION['catalogue']['groupid'])) $this->fields['id_group'] = $_SESSION['catalogue']['groupid'];
		}
		else {
			$this->db->query("DELETE FROM dims_mod_cata_panierstypes_details WHERE id_paniertype = {$this->fields['id']}");
		}
		parent::save();
		if(!empty($this->articles)){
			foreach ($this->articles as $ref_article => $article) {
				$detail = new paniertype_detail();
				$detail->fields['id_paniertype'] = $this->fields['id'];
				$detail->fields['ref_article'] = $ref_article;
				$detail->fields['qte'] = $article['qte'];
				$detail->save();
			}
		}
	}

	public function delete() {
		$this->db->query("DELETE FROM dims_mod_cata_panierstypes_details WHERE id_paniertype = {$this->fields['id']}");
		parent::delete();
	}

	public function getarticles() {
		$articles = array();
		$rs = $this->db->query("
			SELECT	ptd.ref_article, ptd.*, sel.selection
			FROM	dims_mod_cata_panierstypes pt

			INNER JOIN	dims_mod_cata_panierstypes_details ptd
			ON			pt.id = ptd.id_paniertype

			LEFT JOIN	dims_mod_vpc_selection sel
			ON			sel.ref_article = ptd.ref_article
			AND			sel.ref_client = '{$_SESSION['catalogue']['code_client']}'

			WHERE	pt.id = {$this->fields['id']}
			GROUP BY ptd.ref_article
			ORDER BY sel.selection DESC, ptd.ref_article ASC");
		while ($row = $this->db->fetchrow($rs)) {
			$articles[$row['ref_article']] = $row;
		}
		return($articles);
	}

	public function getrefqte() {
		$articles = array();
		$this->db->query('SELECT ref_article, qte FROM dims_mod_cata_panierstypes_details WHERE id_paniertype = '.$this->fields['id']);
		while ($row = $this->db->fetchrow()) {
			$articles[$row['ref_article']]['qte'] = $row['qte'];
		}
		return $articles;
	}

	public function getByLabel($label) {
		$rs = $this->db->query('
			SELECT *
			FROM `'.self::TABLE_NAME.'`
			WHERE id_user = '.$_SESSION['dims']['userid'].'
			AND libelle = \''.addslashes($label).'\'
			LIMIT 0, 1');
		if ($this->db->numrows($rs)) {
			$row = $this->db->fetchrow($rs);
			$this->openFromResultSet($row);
			return true;
		}
		else {
			return false;
		}
	}

	public static function all($conditions = '',$params=array()) {
		$db = dims::getInstance()->getDb();

		$a_pt = array();
		$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE id_user = '.$_SESSION['dims']['userid'].' '.$conditions);
		while ($row = $db->fetchrow($rs)) {
			$pt = new paniertype();
			$pt->openFromResultSet($row);
			$a_pt[] = $pt;
		}

		return $a_pt;
	}

}
