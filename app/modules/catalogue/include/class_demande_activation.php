<?php
class demande_activation extends dims_data_object {
	function demande_activation() {
		parent::dims_data_object('dims_mod_vpc_demande_activation', 'code_client');
	}

	function findByActivationKey($key) {
		if ($key != '') {
			$this->resultid	= $this->db->query('SELECT * FROM dims_mod_vpc_demande_activation WHERE activation_key = \''.$key.'\'');
			$this->numrows	= $this->db->numrows($this->resultid);
			$this->fields	= $this->db->fetchrow($this->resultid);

			if ($this->numrows) $this->new = false;

			return $this->numrows;
		}
	}
}
