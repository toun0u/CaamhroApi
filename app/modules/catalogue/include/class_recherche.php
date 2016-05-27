<?php
class recherche extends dims_data_object {
	function recherche () {
		parent::dims_data_object('dims_mod_vpc_recherche');
	}

	function findByMotscles($motscles, $a_kwdref) {
		$motscles = trim(addslashes($motscles));
		if ($motscles != '') {
			$this->resultid	= $this->db->query("SELECT * FROM dims_mod_vpc_recherche WHERE motscles = '".addslashes($motscles)."'");
			$this->numrows	= $this->db->numrows($this->resultid);

			if ($this->numrows) {
				$this->new = false;
				$this->fields = $this->db->fetchrow($this->resultid);
			}
			else {
				$this->new = true;
				$this->fields = array('id' => '', 'motscles' => $motscles, 'c' => 0);
			}

			if (isset($a_kwdref[$motscles])) {
				$this->fields['reference'] = 1;
			}
			else {
				$this->fields['reference'] = 0;
			}

			return $this->numrows;
		} else {
			return 0;
		}
	}

	function save() {
		$this->fields['c']++;
		parent::save();
	}
}
