<?php

/**
 * Description of class_import
 *
 * @author Aurélien Tisserand / Patrick Nourrissier
 * @copyright Wave Software / Netlor 2011
 */

require_once(DIMS_APP_PATH . '/modules/system/import/class_import.php');

class import_contact extends import {
	private $convmeta;
	private $contact_fields_mode;

	/*
	 * Constructeur, initialisation des variables + chargement des champs dynamiques
	 */
	public function __construct() {
		$convmeta=array();
		$contact_fields_mode=array();
		$this->loadContactMeta();
	}

	/*
	 * chargement du fichier en base de données pour parcours
	 */
	public function loadImportFile() {
		//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','1024M');

		// on load le fichier en csv
		if ($extension=='csv' || $extension=='xls' || $extension=='xlsx') {
			if ($extension!='csv') {
				// conversion en csv
				$pathexec = str_replace(" ","\ ",$filepath);
				$exec="xls2csv -s UTF-8 -d UTF-8 ".escapeshellarg($pathexec)." > ".escapeshellarg($session_dir."/result.csv");
				shell_exec(escapeshellcmd('LANG=en_US.utf-8; '.$exec));
				$filepath=$session_dir."/result.csv";
			}
			die($filepath);
			require_once DIMS_APP_PATH . '/include/class_csv_import.php';
			$csvimport = new dims_csv_import($filepath);
			$_SESSION['dims']['assurance']['import']['file']=$filepath;


		}
	}


	/*
	 * Chargement des champs avec leurs proprietes
	 */
	public function loadContactMeta() {
		$db = dims::getInstance()->getDb();
		//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
		$sql =	"
					SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
											mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON			mb.id=mf.id_mbfield
					RIGHT JOIN	dims_mod_business_meta_categ as mc
					ON			mf.id_metacateg=mc.id
					WHERE		mf.id_object = :idobject
					AND			mc.admin=1
					AND			mf.used=1
					ORDER BY	mc.position, mf.position
								";
		$rs_fields=$db->query($sql, array(
			':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
		));

		$rubgen=array();
		$convmeta = array();

		while ($fields = $db->fetchrow($rs_fields)) {
				if (!isset($rubgen[$fields['id_cat']]))  {
						$rubgen[$fields['id_cat']]=array();
						$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
						$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
						if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
				}

				// on ajoute maintenant les champs dans la liste
				$fields['use']=0;// par defaut non utilise
				$fields['enabled']=array();
				if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

				$this->contact_fields_mode[$fields['id']]=$fields['mode'];

				// enregistrement de la conversion
				$this->convmeta[$fields['namefield']]=$fields['id'];
		}
	}

	/*
	 * fonction getImportsFromUser
	 */
	public function getImportsFromUser($userid) {
		$db = dims::getInstance()->getDb();
	}
}

?>
