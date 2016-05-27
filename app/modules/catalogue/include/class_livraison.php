<?php
class livraison extends dims_data_object {
	function livraison() {
		parent::dims_data_object('dims_mod_vpc_livraison', 'CLREF', 'CLNO');
	}

	function save() {
		if (!(isset($this->fields['CLNO']) && $this->fields['CLNO'] != '')) {
			// On crée le numéro de l'adresse
			$rs = $this->db->query('SELECT MAX(CLNO*1) as max FROM dims_mod_vpc_livraison WHERE CLREF = \''.$_SESSION['catalogue']['code_client'].'\'');
			if ($this->db->numrows($rs)) {
				$row = $this->db->fetchrow($rs);

				if ($row['max'] != NULL) {
					$this->fields['CLNO'] = $row['max'] + 1;
				}
				else {
					$this->fields['CLNO'] = 0;
				}
			}
			else {
				$this->fields['CLNO'] = 0;
			}
		}

		foreach ($this->fields as $field => $value) {
			$this->fields[$field] = html_entity_decode($value);
		}

		parent::save();
	}

	function delete() {
		// On vérifie que l'adresse de livraison n'est pas rattachée à un groupe
		$rs = $this->db->query('SELECT id_groupe FROM dims_mod_vpc WHERE id_livraison = '.$this->fields['id']);
		if ($this->db->numrows($rs)) {
			dims_redirect("$scriptenv?op=manage_account&err=0");
		}
		else {
			$this->fields['CAUXL'] .= ' DELETED';
			parent::save();
		}
	}
}
