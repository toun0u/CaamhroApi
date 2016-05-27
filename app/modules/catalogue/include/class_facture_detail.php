<?php

class cata_facture_detail extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_facture_det';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function save() {
		if($this->isNew()) {
			$this->fields['position'] = $this->getmaxposition() + 1;
		}

		return parent::save();
	}

	public function delete($forcedelete = false) {
		$returnvalue = true;

		if($this->fields['position'] < $this->getmaxposition()) {
			$this->db->query(
				'UPDATE '.self::TABLE_NAME.'
				SET     position = position - 1
				WHERE   id_facture = :factureid
				AND     position > :currentposition
				AND     deleted = 0',
				array(
					':factureid'        => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_facture']),
					':currentposition'  => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
				)
			);
		}

		if($forcedelete) {
			$returnvalue = parent::delete();
		} else {
			$this->fields['deleted'] = 1;
			$this->save();
		}

		return $returnvalue;
	}

	public function getReference() {
		return $this->fields['reference'];
	}

	public function getDesignation() {
		return $this->fields['design1'].$this->fields['design2'];
	}

	public function getQte() {
		return $this->fields['qte'];
	}

	public function getPuHT() {
		return $this->fields['puht'];
	}

	public function getRemise1() {
		return $this->fields['remise1'];
	}

	public function getCodeTVA() {
		return $this->fields['codtva'];
	}

	public function gettotalht() {
		return ($this->fields['pu_ht'] * $this->fields['qte']) * (1 - ($this->fields['remise'] / 100));
	}

	public function getmaxposition() {
		$sql = 'SELECT MAX(position) AS positionmax FROM '.self::TABLE_NAME.' WHERE id_facture = :factureid AND deleted = 0';

		$res = $this->db->query($sql, array(':factureid' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_facture'])));

		$data = $this->db->fetchrow($res);

		return $data['positionmax'];
	}

	public function moveup() {
		if($this->fields['position'] < $this->getmaxposition()) {
			$this->db->query(
				'UPDATE '.self::TABLE_NAME.'
				SET     position = position - 1
				WHERE   id_facture = :factureid
				AND     position = :currentposition + 1
				AND     deleted = 0',
				array(
					':factureid'        => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_facture']),
					':currentposition'  => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
				)
			);
			$this->fields['position']++;

			$this->save();
		}
	}

	public function movedown() {
		if($this->fields['position'] > 0) {
			$this->db->query(
				'UPDATE '.self::TABLE_NAME.'
				SET     position = position + 1
				WHERE   id_facture = :factureid
				AND     position = :currentposition - 1
				AND     deleted = 0',
				array(
					':factureid'        => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_facture']),
					':currentposition'  => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
				)
			);
			$this->fields['position']--;

			$this->save();
		}
	}
}
