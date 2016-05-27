<?php

require_once DIMS_APP_PATH.'modules/catalogue/include/class_carrier_rate.php';

class cata_carrier extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_carriers';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function setRateFromFilename($filename) {
		$rate = new cata_carrier_rate();
		$rate->setugm();
		$rate->setCarrier($this->get('id'));
		$rate->setFromFilename($filename);
	}


	public function getName() {
		return $this->fields['name'];
	}

	public function getCarriageAmount($county, $weight) {
		$rs = $this->db->query('SELECT 	`carriage_amount`
			FROM 	`'.cata_carrier_rate::TABLE_NAME.'`
			WHERE 	`id_carrier` = :id_carrier
			AND 	`county` = :county
			AND 	`weight` > :weight
			ORDER BY `weight` ASC
			LIMIT 1
			', array(
				':id_carrier' 	=> array( 'type' => PDO::PARAM_INT, 'value' => $this->get('id') ),
				':county' 		=> array( 'type' => PDO::PARAM_INT, 'value' => $county ),
				':weight' 		=> array( 'type' => PDO::PARAM_INT, 'value' => $weight )
				));
		if ($this->db->numrows($rs)) {
			$row = $this->db->fetchrow($rs);
			return $row['carriage_amount'];
		}
		else {
			return null;
		}
	}

}
