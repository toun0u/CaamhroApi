<?php

class cata_carrier_rate extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_carriers_rates';

	private $reader 			= null; 	// Instance reader de PHPExcel
	private $excel 				= null; 	// Fichier excel chargé
	private $worksheet 			= null; 	// Première feuille du fichier chargé

	private $heading_row		= null;		// Ligne d'entete
	private $a_weights 			= null;		// Poids max définis dans l'entête

	private $package_start 		= 2;		// Au forfait / colonne de début
	private $package_end 		= 10;		// Au forfait / colonne de fin
	private $per_weight_start 	= 11;		// Par tranche de poids / colonne de début
	private $per_weight_end 	= 15;		// Par tranche de poids / colonne de fin


	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);

		$this->a_weights = array(
			2 => array(1, 9),
			3 => array(10, 19),
			4 => array(20, 29),
			5 => array(30, 39),
			6 => array(40, 49),
			7 => array(50, 59),
			8 => array(60, 69),
			9 => array(70, 79),
			10 => array(80, 100),
			11 => array(101, 299),
			12 => array(300, 499),
			13 => array(500, 699),
			14 => array(700, 999),
			15 => array(1000, 3000)
			);
	}

	public function delete() {
		if ($this->get('id_carrier') > 0) {
			$this->db->query('DELETE FROM `'.self::TABLE_NAME.'` WHERE `id_carrier` = '.$this->get('id_carrier'));
		}
	}

	public function setCarrier($id_carrier) {
		$this->fields['id_carrier'] = $id_carrier;
	}

	public function setFromFilename($filename) {
		require_once DIMS_APP_PATH.'lib/PHPExcel/PHPExcel.php';

		// On force un fichier XLSX pour l'instant
		// On pourra prévoir de détecter le type de fichier par la suite
		$this->reader 		= PHPExcel_IOFactory::createReader('Excel2007');
		$this->excel 		= $this->reader->load($filename);
		$this->worksheet 	= $this->excel->getSheet(0);

		// Recherche de la ligne d'entete
		$this->heading_row = $this->getHeadingRow();

		// Insertion des forfaits
		$this->delete();
		$this->insertRate();
	}

	public function getHeadingRow() {
		if (is_null($this->worksheet)) {
			return null;
		}
		$highest_row = $this->worksheet->getHighestRow();
		for ($row = 1; $row < $highest_row; ++$row) {
			if ($this->worksheet->getCellByColumnAndRow(0, $row)->getValue() == 'DPT') {
				return $row;
			}
		}
	}

	public function insertRate() {
		if (is_null($this->worksheet)) {
			return null;
		}

		$a_rates = array();
		$a_slices = array();
		$highest_row = $this->worksheet->getHighestRow();

		for ($row = $this->heading_row + 1; $row < $highest_row; ++$row) {
			// Numéro de département
			$county = $this->worksheet->getCellByColumnAndRow(0, $row)->getValue();
			if (!isset($a_rates[$county])) {
				$a_rates[$county] = array();
			}

			// Colonnes tarifaires
			for ($column = $this->package_start; $column <= $this->per_weight_end; ++$column) {

				// Tarif au forfait
				if ($column <= $this->package_end) {
					$weight = $this->a_weights[$column][1];
					$amount = $this->worksheet->getCellByColumnAndRow($column, $row)->getValue();

					// On prend le tarif le plus cher
					if ( !isset($a_rates[$county][$weight]) || $amount > $a_rates[$county][$weight] ) {
						$a_rates[$county][$weight] = $amount;
					}
				}
				// Pour les tranches de 100 Kg, on recalcule tous les palliers
				else {
					$slice = $this->worksheet->getCellByColumnAndRow($column, $row)->getValue();

					// On prend le tarif le plus cher
					if ( !isset($a_slices[$county][$column]) || $slice > $a_slices[$county][$column] ) {
						$a_slices[$county][$column] = $slice;
					}
				}
			}

			// Pour les tranches de 100 Kg, on recalcule tous les palliers
			for ($weight = 199; $weight <= $this->a_weights[$this->per_weight_end][1]; $weight += 100) {
				// Recherche de la colonne tarifaire
				for ($i = $this->per_weight_end; $i >= $this->per_weight_start; $i--) {

					if ( $weight >= $this->a_weights[$i][0] && $weight <= $this->a_weights[$i][1] ) {
						$slice = $a_slices[$county][$i];
						$nb_slices = (int)($weight / 100);
						$amount = $slice * ($nb_slices + 1);

						// On prend le tarif le plus cher
						if ( !isset($a_rates[$county][$weight]) || $amount > $a_rates[$county][$weight] ) {
							$a_rates[$county][$weight] = $amount;
						}
						break;
					}
				}
			}
		}

		// Insertion dans la BDD
		foreach ($a_rates as $county => $rate) {
			foreach ($rate as $weight => $amount) {
				$this->db->query('INSERT INTO `'.self::TABLE_NAME.'` (
						`id_carrier`,
						`county`,
						`weight`,
						`carriage_amount`,
						`id_user`,
						`id_module`,
						`id_workspace`,
						`timestp_create`,
						`timestp_modify`
					) VALUES (
						:id_carrier,
						:county,
						:weight,
						:amount,
						:id_user,
						:id_module,
						:id_workspace,
						:timestp_create,
						:timestp_modify
					)', array(
						':id_carrier' => array( 'type' => PDO::PARAM_INT, 'value' => $this->get('id_carrier') ),
						':county' => array( 'type' => PDO::PARAM_INT, 'value' => $county ),
						':weight' => array( 'type' => PDO::PARAM_INT, 'value' => $weight ),
						':amount' => array( 'type' => PDO::PARAM_INT, 'value' => $amount ),
						':id_user' => array( 'type' => PDO::PARAM_INT, 'value' => $this->get('id_user') ),
						':id_module' => array( 'type' => PDO::PARAM_INT, 'value' => $this->get('id_module') ),
						':id_workspace' => array( 'type' => PDO::PARAM_INT, 'value' => $this->get('id_workspace') ),
						':timestp_create' => array( 'type' => PDO::PARAM_INT, 'value' => dims_createtimestamp() ),
						':timestp_modify' => array( 'type' => PDO::PARAM_INT, 'value' => dims_createtimestamp() )
					));
			}
		}
	}

}
