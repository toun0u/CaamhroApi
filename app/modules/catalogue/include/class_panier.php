<?php
class cata_panier extends dims_data_object {

	function cata_panier() {
		parent::dims_data_object('dims_mod_cata_panier');
	}

	function save() {
		$this->db->query('DELETE FROM dims_mod_cata_panier_detail WHERE id_panier = '.$this->fields['id']);

		parent::save();

		if (!empty($this->articles)) {
			foreach ($this->articles as $fields) {
				$this->db->query('INSERT INTO dims_mod_cata_panier_detail SET
					id_panier = '.$this->fields['id'].',
					ref = \''.$fields['ref'].'\',
					qte = '.$fields['qte'].',
					forced_price = '.$fields['forced_price']);
			}
		}
	}

	function open() {
		parent::open(func_get_arg(0));

		if (!empty($this->fields['id'])) {
			$this->articles = array();
			$rs = $this->db->query('SELECT ref, qte FROM dims_mod_cata_panier_detail WHERE id_panier = '.$this->fields['id']);
			while ($row = $this->db->fetchrow($rs)) {
				$this->articles[] = array('ref' => $row['ref'], 'qte' => $row['qte']);
			}
		}
	}

	function delete() {
		$this->db->query('DELETE FROM dims_mod_cata_panier_detail WHERE id_panier = '.$this->fields['id']);
		parent::delete();
	}

}
