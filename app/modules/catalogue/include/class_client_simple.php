<?php
class client_simple extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function client_simple() {
		parent::dims_data_object('dims_mod_vpc_client','CREF');
	}

	function change_cref($new_cref) {
		global $db;

		// On s'assure qu'aucun client n'a cette reference
		$db->query("SELECT CREF FROM dims_mod_vpc_client WHERE CREF = '$new_cref'");
		if (!$db->numrows()) {
			/*
			 * Liste des tables / champs à impacter
			 *
			 * - dims_mod_vpc_client				CREF
			 * - dims_mod_vpc_client_detail			CREF
			 * - dims_mod_vpc_budget				id_client
			 * - dims_mod_vpc_cmd					ref_client
			 * - dims_mod_vpc_facture				ref_client
			 * - dims_mod_vpc_livraison				CLREF
			 * - dims_mod_vpc_prix_net				PNCLIE
			 * - dims_mod_vpc_remise_clf			RCREF
			 * - dims_mod_vpc_selection				ref_client
			 * - dims_group 						code
			 */

			$db->query("UPDATE dims_mod_vpc_client SET CREF = '$new_cref' WHERE CREF = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_client_detail SET CREF = '$new_cref' WHERE CREF = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_budget SET id_client = '$new_cref' WHERE id_client = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_cmd SET ref_client = '$new_cref' WHERE ref_client = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_facture SET ref_client = '$new_cref' WHERE ref_client = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_livraison SET CLREF = '$new_cref' WHERE CLREF = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_prix_net SET PNCLIE = '$new_cref' WHERE PNCLIE = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_remise_clf SET RCREF = '$new_cref' WHERE RCREF = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_mod_vpc_selection SET ref_client = '$new_cref' WHERE ref_client = '{$this->fields['CREF']}'");
			$db->query("UPDATE dims_group SET code = '$new_cref' WHERE code = '{$this->fields['CREF']}'");
		}
	}
}
