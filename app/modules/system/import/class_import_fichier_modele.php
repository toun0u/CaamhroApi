<?php

/**import_fichier_modele
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class import_fichier_modele extends dims_data_object{
	const TABLE_NAME = "dims_import_fichier_modele";

	private $globalobject_concerned = null ;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	/**
	 * @complexity (requete=0-1, longueur=n)
	 * @return dims_globalobject
	 */
	public function getGlobalobjectConcerned(){
		if(is_null($this->globalobject_concerned)) {
			$this->globalobject_concerned = new dims_globalobject();
			$this->globalobject_concerned->open($this->getIdGlobalobjectConcerned());
		}
		return $this->globalobject_concerned;
	}

	private function setGlobalobjectConcerned(dims_globalobject $go_concerned){
		$this->globalobject_concerned = $go_concerned;
	}

	public function getIdGlobalobjectConcerned(){
		return $this->getAttribut('id_globalobject_concerned', parent::TYPE_ATTRIBUT_KEY);
	}

	public function getLibelle(){
		return $this->getAttribut('libelle', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getTmstpCreated(){
		return $this->getAttribut('tmstp_created', parent::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getTmstpModified(){
		return $this->getAttribut('tmstp_modified', parent::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getExtension(){
		return $this->getAttribut('extension', parent::TYPE_ATTRIBUT_STRING);
	}

	public function setIdGlobalobjectConcerned($id_globalobject_concerned, $save = false){
		$this->setAttribut('id_globalobject_concerned', parent::TYPE_ATTRIBUT_KEY, $id_globalobject_concerned, $save);
	}

	public function setLibelle($libelle, $save = false){
		$this->setAttribut('libelle', parent::TYPE_ATTRIBUT_STRING, $libelle, $save);
	}

	public function setTmstpCreated($tmstp_created, $save = false){
		$this->setAttribut('tmstp_created', parent::TYPE_ATTRIBUT_NUMERIC, $tmstp_created, $save);
	}

	public function setTmstpModified($tmstp_modified, $save = false){
		$this->setAttribut('tmstp_modified', parent::TYPE_ATTRIBUT_NUMERIC, $tmstp_modified, $save);
	}

	public function setExtension($extension, $save = false){
		$this->setAttribut('extension', parent::TYPE_ATTRIBUT_STRING, $extension, $save);
	}

	public static function getTiers() {
		global $_DIMS;
		$listassureurs =array();

		$db = dims::getInstance()->getDB();


		$sql = "SELECT			* from dims_mod_business_tiers
				WHERE			type_tiers= :type
				ORDER BY		intitule
				";

		$res = $db->query($sql, array(
			':type' => _ASSUR_TYPE_TIERS_CLIENT
		));

		while ($f=$db->fetchrow($res)) {
		   $listassureurs[]=$f;
		}

		return $listassureurs;
	}

	public static function getFichiersModele($id_globalobject_concerned) {
		$list_fichiers_modele = array () ;

		$db = dims::getInstance()->getDB();

		$sql = "SELECT * FROM ".self::TABLE_NAME."
				WHERE id_globalobject_concerned = :idglobalobjectconcerned
				ORDER BY ".self::TABLE_NAME.".tmstp_created DESC";

		$res = $db->query($sql, array(
			':idglobalobjectconcerned' => $id_globalobject_concerned
		));

		while ($f=$db->fetchrow($res)) {
			$list_fichiers_modele[]=$f;
		}
		return $list_fichiers_modele ;
	}

	/**
	 *
	 * @param type $id_globalobject_concerned
	 * @return import_fichier_modele
	 */
	public static function getFichiersModeleObject($id_globalobject_concerned) {
		$list_fichiers_modele = array () ;

		if($id_globalobject_concerned > 0){
			$db = dims::getInstance()->getDB();

			$sql = "SELECT * FROM ".self::TABLE_NAME."
					WHERE id_globalobject_concerned = :idglobalobjectconcerned
					ORDER BY ".self::TABLE_NAME.".tmstp_created DESC";

			$res = $db->query($sql, array(
				':idglobalobjectconcerned' => $id_globalobject_concerned
			));

			while ($f=$db->fetchrow($res)) {
				$fichier_modele = new import_fichier_modele();
				$fichier_modele->openWithFields($f);
				$list_fichiers_modele[]=$fichier_modele;
			}
		}
		return $list_fichiers_modele ;
	}

	/**
	 * Fonction qui retourne un tableau à deux entrées comprenant les fichiers
	 * de chaque globalobject
	 *
	 * @complexity (requete=1, longueur=n*m)
	 * @return array de import_fichier_modele
	 */
	public static function getFichiersModeles(){
		$list_fichiers_modele = array () ;
		$db = dims::getInstance()->getDB();


		$sql = "SELECT * FROM ".self::TABLE_NAME."
			INNER JOIN ".dims_globalobject::TABLE_NAME."
			ON ".self::TABLE_NAME.".id_globalobject_concerned = ".	dims_globalobject::TABLE_NAME.".id
			ORDER BY ".self::TABLE_NAME.".tmstp_created DESC
			";

		$res = $db->query($sql);

		$separation = $db->split_resultset($res);
		foreach ($separation as $row) {
			$import_fichier_modele = new import_fichier_modele();
			$import_fichier_modele->openWithFields($row[import_fichier_modele::TABLE_NAME]);

			$globalobject = new dims_globalobject();
			$globalobject->openWithFields($row[dims_globalobject::TABLE_NAME]);

			$import_fichier_modele->setGlobalobjectConcerned($globalobject);

			$list_fichiers_modele[$import_fichier_modele->getIdGlobalobjectConcerned()][$import_fichier_modele->getId()] = $import_fichier_modele;
		}

		return $list_fichiers_modele ;
	}

	public static function getModelFields() {
		$listfields =array();
		$db = dims::getInstance()->getDB();

		$sql = "SELECT			fm.id,fm.libelle,fm.obligatoire,fm.help_constant, cfm.libelle AS libelletype
				FROM			".import_champs_fichier_modele::TABLE_NAME." as fm
				INNER JOIN		".import_type_champs_fichier_modele::TABLE_NAME." as cfm
				ON				fm.id_type_champs=cfm.id
				ORDER BY		cfm.libelle,fm.libelle
			";

		$res = $db->query($sql);

		while ($f=$db->fetchrow($res)) {
		   $listfields[$f['id']]=$f;
		}

		return $listfields;
	}

	public static function setCorrespField($id_corresp,$id_columnfile) {

		if ($id_corresp >=0 && $id_columnfile>=0) {

			if (isset($_SESSION['dims']['import']['import']['headers_database'][$id_columnfile])) {
				// on a qq chose
				// on stocke
				if ($id_columnfile==0)
					unset($_SESSION['dims']['import']['corresp'][$id_corresp]);
				else
					$_SESSION['dims']['import']['corresp'][$id_corresp]=$id_columnfile;
			}
		}
	}

	public function saveFieldsCorresp() {

		if (isset($_SESSION['dims']['import']['corresp'])) {
			// on vide ce qu'il y a dans la table
			$db = dims::getInstance()->getDB();

			 $sql = "DELETE FROM".import_correspondance_colonne_champs::TABLE_NAME."
					WHERE  id_fichier_modele= :idfichiermodele";

			$res = $db->query($sql, array(
				':idfichiermodele' => $this->fields['id']
			));

			// on enregistre
			foreach ($_SESSION['dims']['import']['corresp'] as $id_champs => $id_columnfile) {
			   if (isset($_SESSION['dims']['import']['import']['headers_database'][$id_columnfile])) {
				   $correspfield = new import_correspondance_colonne_champs();
				   $correspfield->setIdChamps($id_champs);
				   $correspfield->setLibelleColonne($_SESSION['dims']['import']['import']['headers_database'][$id_columnfile]);
				   $correspfield->setIdFichierModele($this->getId());
				   $correspfield->save();
			   }
			}
		}
	}

	 public static function importFile($filepath,$session_dir,$structonly=false,$id_import) {
		$db = dims::getInstance()->getDB();
		$error=0;
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','1024M');

		$extension	= explode(".", $filepath);
		$extension	= $extension[count($extension)-1];
		$extension	= strtolower($extension);
		// traitement du fichier
		if ($extension=='csv' || $extension=='xls' || $extension=='xlsx') {
			if ($extension!='csv') {
				// conversion en csv
				$pathexec = str_replace(" ","\ ",$filepath);
				$exec="xls2csv -s UTF-8 -d UTF-8 ".escapeshellarg($pathexec)." > ".escapeshellarg($session_dir."/result.csv");
				shell_exec(escapeshellcmd('LANG=en_US.utf-8; '.$exec));
				$filepath=$session_dir."/result.csv";
			}
			require_once DIMS_APP_PATH . '/include/class_csv_import.php';
			$csvimport = new dims_csv_import($filepath);
			$_SESSION['dims']['import']['import']['file']=$filepath;

			if ($structonly) {
				$headers=$csvimport->getCsvHeaders();
				$headers_convert = array();

				// boucle sur les entetes
				foreach ($headers as $c => $head) {
					//$nameconv=strtolower(dims_convertaccents(html_entity_decode(strip_tags($head))));
					$headers_convert[$c] =str_replace(array(" ","-"),"_",$head);
				}

				$_SESSION['dims']['import']['import']['headers']=$headers;
				$_SESSION['dims']['import']['import']['headers_database']=$headers_convert;

			}
			else {
				// import complet du fichier
				$csvimport->setdb($db);
				$csvimport->import();
				$temptable=$csvimport->getTableTemp();

				$ai = new assurance_import();
				$ai->fields['id_globalobject_concerned']=$_SESSION['dims']['import']['id_globalobject'];
				$ai->fields['timestp_create']=dims_createtimestamp();
				$ai->fields['timestp_modify']=dims_createtimestamp();
				$ai->fields['ref_tmp_table'] = $temptable;
				$ai->fields['nbelements'] = 0;
				$ai->fields['status'] = _ASSUR_STATUT_FILE_IMPORTED;
				// calcul du nombre d'elements
				$res=$db->query('select count(*) as cpte from '.$temptable);
				if ($db->numrows($res)) {
					if ($f=$db->fetchrow($res)) {
						$ai->fields['nbelements'] = $f['cpte'];
					}
				}
				$ai->save();

				// ajout de la colonne status dans la table temp
				$db->query("ALTER TABLE `".$temptable."` ADD `status` TINYINT(4) NOT NULL DEFAULT '0'");

				// creation de l'id unique pour le traitement
				$db->query("ALTER TABLE `".$temptable."` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");

				// on retourne l'id import
				$id_import=$csvimport->fields['id'];
			}
		}
		else {
			$error=_ASSUR_STATUT_FILE_NOT_CORRECT;
		}

		return $error;
	}

//	todo
//	  public static function getAssureurs() {
//		  global $_DIMS;
//		  $listassureurs =array();
//
//		  $db = dims::getInstance()->getDB();
//
//
//		  $sql = "SELECT		  * from dims_mod_business_tiers
//				  WHERE			  type_tiers="._ASSUR_TYPE_TIERS_ASSUREUR."
//				  ORDER BY		  intitule
//				  ";
//
//		  $res = $db->query($sql);
//
//		  while ($f=$db->fetchrow($res)) {
//			 $listassureurs[]=$f;
//		  }
//
//		  return $listassureurs;
//	  }


//	  public static function importFile($filepath,$session_dir,$structonly=false,$id_import) {
//		  global $db;
//		  $error=0;
//		  ini_set('max_execution_time',-1);
//		  ini_set('memory_limit','1024M');
//
//		  $extension	= explode(".", $filepath);
//		  $extension	= $extension[count($extension)-1];
//		  $extension  = strtolower($extension);
//		  // traitement du fichier
//		  if ($extension=='csv' || $extension=='xls' || $extension=='xlsx') {
//			  if ($extension!='csv') {
//				  // conversion en csv
//				  $pathexec = str_replace(" ","\ ",$filepath);
//				  $exec="xls2csv -s UTF-8 -d UTF-8 ".$pathexec." > ".$session_dir."/result.csv";
//				  shell_exec('LANG=en_US.utf-8; '.$exec);
//				  $filepath=$session_dir."/result.csv";
//			  }
//			  require_once DIMS_APP_PATH . '/include/class_csv_import.php';
//			  $csvimport = new dims_csv_import($filepath);
//			  $_SESSION['dims']['import']['import']['file']=$filepath;
//
//			  if ($structonly) {
//				  $headers=$csvimport->getCsvHeaders();
//				  $headers_convert = array();
//
//				  // boucle sur les entetes
//				  foreach ($headers as $c => $head) {
//					  //$nameconv=strtolower(dims_convertaccents(html_entity_decode(strip_tags($head))));
//					  $headers_convert[$c] =str_replace(array(" ","-"),"_",$head);
//				  }
//
//				  $_SESSION['dims']['import']['import']['headers']=$headers;
//				  $_SESSION['dims']['import']['import']['headers_database']=$headers_convert;
//
//			  }
//			  else {
//				  // import complet du fichier
//				  $csvimport->setdb($db);
//				  $csvimport->import();
//				  $temptable=$csvimport->getTableTemp();
//
//				  $ai = new assurance_import();
//				  $ai->fields['id_assureur']=$_SESSION['dims']['import']['import']['id_assureur'];
//				  $ai->fields['id_fichier_assureur']=$_SESSION['dims']['import']['import']['id_model_assureur'];
//				  $ai->fields['timestp_create']=dims_createtimestamp();
//				  $ai->fields['timestp_modify']=dims_createtimestamp();
//				  $ai->fields['ref_tmp_table'] = $temptable;
//				  $ai->fields['nbelements'] = 0;
//				  $ai->fields['status'] = _ASSUR_STATUT_FILE_IMPORTED;
//				  // calcul du nombre d'elements
//				  $res=$db->query('select count(*) as cpte from '.$temptable);
//				  if ($db->numrows($res)) {
//					  if ($f=$db->fetchrow($res)) {
//						  $ai->fields['nbelements'] = $f['cpte'];
//					  }
//				  }
//				  $ai->save();
//
//				  // ajout de la colonne status dans la table temp
//				  $db->query("ALTER TABLE `".$temptable."` ADD `status` TINYINT(4) NOT NULL DEFAULT '0'");
//
//				  // creation de l'id unique pour le traitement
//				  $db->query("ALTER TABLE `".$temptable."` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
//
//				  // on retourne l'id import
//				  $id_import=$csvimport->fields['id'];
//			  }
//		  }
//		  else {
//			  $error=_ASSUR_STATUT_FILE_NOT_CORRECT;
//		  }
//
//		  return $error;
//	  }

}

?>
