<?php

/**
 * Description of class_import
 *
 * @author Patrick Nourrissier
 * @copyright Wave Software / Netlor 2011
 */

require_once(DIMS_APP_PATH . '/modules/system/import/class_import.php');

class import_tiers extends import {
	private $convmeta;
	private $contact_fields_mode;

	/*
	 * Constructeur, initialisation des variables + chargement des champs dynamiques
	 */
	public function __construct() {
		$convmeta=array();
		$contact_fields_mode=array();
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	const TABLE_NAME = "dims_mod_assurance_import";

	const STATUT_NO_FILE_IMPORT = _ASSUR_STATUT_NO_FILE_IMPORT;
	const STATUT_FILE_NOT_CORRECT = _ASSUR_STATUT_FILE_NOT_CORRECT;
	const STATUT_FILE_IMPORTER = _ASSUR_STATUT_FILE_IMPORTED;
	const STATUT_FILE_IMPORT_IN_PROGRESS = _ASSUR_STATUT_IMPORT_IN_PROGRESS;
	const STATUT_DATE_IMPORTED = _ASSUR_STATUT_DATE_IMPORTED;

	public function getIdFichierAssureur() {
		return $this->getAttribut("id_fichier_assureur", self::TYPE_ATTRIBUT_KEY);
	}

	public function getStatus() {
		return $this->getAttribut("status", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getRefTmpTable() {
		return $this->getAttribut("ref_tmp_table", self::TYPE_ATTRIBUT_STRING);
	}

	public function getIdAssureur() {
		return $this->getAttribut("id_assureur", self::TYPE_ATTRIBUT_KEY);
	}

	public function setStatus($status, $save = false) {
		$this->setAttribut("status", self::TYPE_ATTRIBUT_NUMERIC, $status, $save);
	}

	public function setComments($comments, $save = false) {
		$this->setAttribut("comments", self::TYPE_ATTRIBUT_STRING, $comments, $save);
	}

	public function getComments() {
		return $this->getAttribut("comments", self::TYPE_ATTRIBUT_STRING);
	}

	public function addComments($comments) {
		$old_comment = $this->getComments();
		if (empty($old_comment) || $old_comment == 'NULL') {
			$this->setComments($comments);
		} else {
			$this->setComments($this->getComments() . "," . $comments);
		}
	}

	// fonction permettant l'import des fichiers assureurs
	public static function getModelesFilesAssureurs() {
		global $_DIMS;
		$listimportfiles = array();

		$db = dims::getInstance()->getDB();


		$sql = "SELECT			t.id,t.intitule , fa.libelle as libellemodelfile,fa.id as id_modele_fichier
				FROM			dims_mod_business_tiers as t
				INNER JOIN		dims_mod_assurance_fichier_assureur as fa
				ON				t.type_tiers= :typetiers
				AND				fa.id_assureur = t.id
				ORDER BY		t.intitule asc
				";

		$res = $db->query($sql, array(
			':typetiers' => _ASSUR_TYPE_TIERS_ASSUREUR
		));

		while ($f = $db->fetchrow($res)) {
			$listimportfiles[] = $f;
		}

		return $listimportfiles;
	}

	// fonction permettant l'import des fichiers assureurs
	public static function getImportFilesAssureurs() {
		global $_DIMS;
		$listimportfiles = array();

		$db = dims::getInstance()->getDB();


		$sql = "SELECT			ai.*,t.intitule , fa.libelle as libellemodelfile
				FROM			dims_mod_business_tiers as t
				INNER JOIN		dims_mod_assurance_import as ai
				ON				t.id = ai.id_assureur
				AND				t.type_tiers= :typetiers
				INNER JOIN		dims_mod_assurance_fichier_assureur as fa
				ON				fa.id = ai.id_fichier_assureur
				ORDER BY		ai.timestp_modify DESC, t.intitule ASC
				";


		$res = $db->query($sql, array(
			':typetiers' => _ASSUR_TYPE_TIERS_ASSUREUR
		));

		while ($f = $db->fetchrow($res)) {
			$sel = "SELECT	COUNT(id) as nb
					FROM	" . $f['ref_tmp_table'];
			$r = $db->query($sel);
			$count = $db->fetchrow($r);
			$f['nb_restants'] = $count['nb'];
			$listimportfiles[] = $f;
		}

		return $listimportfiles;
	}

	public function getListeErrors($type = 0) {
		require_once DIMS_APP_PATH . '/modules/system/import/controller_op_import.php';
		$lstStatErr = array(controller_assurance_import::STATUT_TUPLE_TEMP_CC_NOT_FOUND,
			controller_assurance_import::STATUT_TUPLE_TEMP_CC_OVER,
			controller_assurance_import::STATUT_TUPLE_TEMP_ASSURE_NOT_FOUND,
			controller_assurance_import::STATUT_TUPLE_TEMP_ASSURE_A_RAPPROCHE_BIRTHDATE,
			controller_assurance_import::STATUT_TUPLE_TEMP_ASSURE_A_RAPPROCHE_ADRESSE,
			controller_assurance_import::STATUT_TUPLE_TEMP_COUVERTURE_NOT_FOUND);

		$db = dims::getInstance()->getDB();
		$params = array();
		$sel = "SELECT	id, status
				FROM	" . $this->getRefTmpTable() . "
				WHERE	status IN (" . $db->getParamsFromArray($lstStatErr, 'lstStatErr', $params) . ")";
		$res = $db->query($sel, $params);
		$lstErr = array();
		while ($r = $db->fetchrow($res))
			$lstErr[$r['status']][] = $r['id'];
		if ($type > 0)
			if (isset($lstErr[$type]))
				return $lstErr[$type];
			else
				return array();
		else
			return $lstErr;
	}

	public function getDataWithCorresp($id) {
		require_once DIMS_APP_PATH . '/modules/system/import/class_import_correspondance_colonne_champs.php';
		require_once DIMS_APP_PATH . '/modules/system/import/class_import_fichier_modele.php';
		require_once DIMS_APP_PATH . '/modules/system/imports/class_import_champs_fichier_modele.php';
		$db = dims::getInstance()->getDB();
		$res_data = array();
		$liste_correspondance_fichier = assurance_correspondance_colonne_champs::getListCorrespondanceByIdFichierLazy($this->getIdFichierAssureur());

		$liste_type_champs = assurance_champs_fichier_assureur::getListChamps();

		$liste_column_table_temp = dims_csv_import::getTabColumnForTableTemp($this->getRefTmpTable());
		$corresp_ok = true;
		$tableau_corresp_ok = array();
		foreach ($liste_type_champs as $list_champs) {
			foreach ($list_champs as $champs) {
				//On vérifie le modèle
				if (isset($liste_correspondance_fichier[$champs->getId()])) {
					$tableau_corresp_ok[$champs->getLibelle()] = $liste_correspondance_fichier[$champs->getId()];
					//On vérifie le fichier
					if (!isset($liste_column_table_temp[dims_csv_import::cleaningNameHeader($liste_correspondance_fichier[$champs->getId()]->getLibelleColonne())])) {
						if ($champs->isObligatoire()) {
							//On doit changer le statut de l'import ! Un des champs n'existe pas
							$this->setStatus(_ASSUR_STATUT_FILE_NOT_CORRECT);
							$this->addComments($champs->getLibelle());
							$corresp_ok = false;

							//return ;
						}
					}
				} else {
					if ($champs->isObligatoire()) {
						//On doit changer le statut de l'import ! Un des champs n'existe pas
						$this->setStatus(_ASSUR_STATUT_MODEL_NOT_CORRECT);
						$this->addComments($champs->getLibelle());
						$corresp_ok = false;

						//return ;
					}
				}
			}
		}
		$sel = "SELECT	*
						FROM	" . $this->getRefTmpTable() . "
						WHERE	id = :id ";
		$res = $db->query($sel, array(
			':id' => $id
		));
		if ($r = $db->fetchrow($res)) {
			foreach ($tableau_corresp_ok as $key => $val) {
				$res_data[$key] = $r[dims_csv_import::cleaningNameHeader($val->fields['libelle_colonne'])];
			}
		}
		return $res_data;
	}
}

?>
