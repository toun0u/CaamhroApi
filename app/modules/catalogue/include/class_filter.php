<?php

class cata_filter extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_champ';

	public function cata_filter() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save() {
		if ($this->isNew() && $this->get('id') == '') {
			// Génération de l'id car pas en auto_increment
			$rs = $this->db->query('SELECT MAX(`id`) AS m FROM `'.self::TABLE_NAME.'`');
			$row = $this->db->fetchrow($rs);
			$this->setId($row['m'] + 1);
		}
		parent::save();
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

	public function getValuesLabels() {
		if ($this->fields['type'] == 'liste') {
			$rs = $this->db->query("
				SELECT	id, valeur
				FROM	dims_mod_cata_champ_valeur
				WHERE	id IN (".implode(',', array_keys($this->values)).")");
			while ($row = $this->db->fetchrow($rs)) {
				$this->values[$row['id']] = $row['valeur'];
			}
		} else {
			foreach ($this->values as $value => $empty) {
				$this->values[$value] = $value;
			}
		}
	}

	private function setId($id) {
		$this->fields['id'] = $id;
	}

	public function setLabel($label) {
		$this->fields['libelle'] = $label;
	}

}
