<?php

/**
 * Description of class_import
 *
 * @author Aurélien Tisserand / Patrick Nourrissier
 * @copyright Wave Software / Netlor 2011
 */
class dims_import extends dims_data_object{
	const TABLE_NAME = "dims_import";

	const STATUT_NO_FILE_IMPORT = _IMPORT_STATUT_NO_FILE_IMPORT;
	const STATUT_MODEL_NOT_CORRECT = _IMPORT_STATUT_MODEL_NOT_CORRECT ;
	const STATUT_FILE_NOT_CORRECT = _IMPORT_STATUT_FILE_NOT_CORRECT ;
	const STATUT_FILE_IMPORTER = _IMPORT_STATUT_FILE_IMPORTED ;
	const STATUT_FILE_IMPORT_IN_PROGRESS = _IMPORT_STATUT_IMPORT_IN_PROGRESS ;
	const STATUT_DATE_IMPORTED = _IMPORT_STATUT_DATE_IMPORTED ;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function delete(){
		$sql = "DROP TABLE IF EXISTS `".str_replace('`', '', $this->getRefTmpTable())."`";
		dims::getInstance()->db->query($sql);
		return parent::delete();
	}

	public function getIdFichierModele() {
		return $this->getAttribut("id_fichier_modele", self::TYPE_ATTRIBUT_KEY);
	}

	public function getStatus() {
		return $this->getAttribut("status", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getRefTmpTable() {
		return $this->getAttribut("ref_tmp_table", self::TYPE_ATTRIBUT_STRING);
	}

	public function getIdGlobalObjectConcerned(){
		return $this->getAttribut("id_globalobject_concerned", self::TYPE_ATTRIBUT_KEY);
	}

	public function setStatus($status, $save = false){
		$this->setAttribut("status", self::TYPE_ATTRIBUT_NUMERIC, $status, $save);
	}

	public function setComments($comments, $save = false){
		$this->setAttribut("comments", self::TYPE_ATTRIBUT_STRING, $comments, $save);
	}

	public function getComments() {
		return $this->getAttribut("comments", self::TYPE_ATTRIBUT_STRING);
	}


	public function addComments($comments){
		$old_comment = $this->getComments() ;
		if(empty($old_comment) || $old_comment == 'NULL'){
			$this->setComments($comments);
		}else{
			$this->setComments($this->getComments().",".$comments);
		}
	}

	public function setIdGlobalobjectConcerned($id_global_object_concerned, $save = false){
		$this->setAttribut("id_global_object_concerned", self::TYPE_ATTRIBUT_KEY, $id_global_object_concerned, $save);
	}

	public function setTimestpCreate($timestp_create, $save = false){
		$this->setAttribut("timestp_create", self::TYPE_ATTRIBUT_NUMERIC, $timestp_create, $save);
	}

	public function setTimestpModify($timestp_modify, $save = false){
		$this->setAttribut("timestp_modify", self::TYPE_ATTRIBUT_NUMERIC, $timestp_modify, $save);
	}

	public function setIdFichierModele($id_fichier_modele, $save = false){
		$this->setAttribut("id_fichier_modele", self::TYPE_ATTRIBUT_KEY, $id_fichier_modele, $save);
	}

	public function setNbelements($nbelements, $save = false){
		$this->setAttribut("nbelements", self::TYPE_ATTRIBUT_NUMERIC, $nbelements, $save);
	}

	public function setRefTmpTable($ref_tmp_table, $save = false){
		$this->setAttribut("ref_tmp_table", self::TYPE_ATTRIBUT_STRING, $ref_tmp_table, $save);
	}

	public function setIdUser($id_user, $save = false){
		$this->setAttribut("id_user", self::TYPE_ATTRIBUT_KEY, $id_user, $save);
	}

	public function setIdModule($id_module, $save = false){
		$this->setAttribut("id_module", self::TYPE_ATTRIBUT_KEY,$id_module, $save);
	}

	public function setIdWorkspace($id_workspace, $save = false){
		$this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workspace, $save);
	}

	/*
	 * fonction getImportsFromUser
	 */
	public function getImportsFromUser($userid) {
		$db = dims::getInstance()->getDb();


	}

	/*
	 * chargement du fichier en base de données pour parcours
	 */
	public function loadImportFile($objectiers = array(), $id_fichier_modele= 0) {
		//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','1024M');
		$db = dims::getInstance()->getDb();

		if (!empty($objectiers))
			$id_globalobject_concerned=$objectiers['id_globalobject'];
		else
			$id_globalobject_concerned=0;

		//dims_print_r($_FILES);
		if (isset($_FILES['import_filesource'])) {
			$generationfichier=  session_id();

			$filepathtemp=$_FILES['import_filesource']['tmp_name'];
			$session_dir = DIMS_TMP_PATH . '/'.$generationfichier;
			if (is_dir($session_dir)) dims_deletedir ($session_dir);

			dims_makedir($session_dir);
			$filename=$_FILES['import_filesource']['name'];

			$extension = substr(strrchr($filename, "."),1);

			$filepath=$session_dir."/filetemp.".$extension;

			if (!is_uploaded_file($filepathtemp)) echo "Error file";
			else{

				//if (is_writable($filepath)) {
			   //echo "<br>".$filepathtemp;die();
				if (move_uploaded_file($filepathtemp, $filepath)) {

					// on load le fichier en csv
					if ($extension=='csv' || $extension=='xls' || $extension=='xlsx') {
						if ($extension!='csv') {
							// conversion en csv
							$pathexec = str_replace(" ","\ ",$filepath);
							//$exec="xls2csv  -d UTF-8 ".escapeshellarg($pathexec)." > ".escapeshellarg($session_dir."/result.csv");
							//shell_exec(('LANG=en_US.utf-8; '.$exec));
							//echo $exec;die();
							//echo ('LANG=en_US.utf-8; '.$exec);
							$filepath=$session_dir."/result.csv";

							// conversion du fichier
							$filepath=xls_to_csv_files($pathexec);
						}

						require_once DIMS_APP_PATH . '/include/class_csv_import.php';
						$csvimport = new dims_csv_import($filepath);
						$csvimport->setfield_separate_char(";");
						$_SESSION['dims']['import']['import']['file']=$filepath;

						$csvimport->import();
						$temptable=$csvimport->getTableTemp();

						// ajout de la colonne status dans la table temp
						$db->query("ALTER TABLE `".str_replace('`', '', $temptable )."` ADD `status` TINYINT(4) NOT NULL DEFAULT '0'");

						// creation de l'id unique pour le traitement
						$db->query("ALTER TABLE `".str_replace('`', '', $temptable )."` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");

						$ai = new dims_import();
						$ai->fields['id_globalobject_concerned']=$id_globalobject_concerned;
						$ai->fields['timestp_create']=dims_createtimestamp();
						$ai->fields['timestp_modify']=dims_createtimestamp();
						$ai->fields['ref_tmp_table'] = $temptable;
						$ai->fields['nbelements'] = 0;
						$ai->fields['status'] = _IMPORT_STATUT_FILE_IMPORTED;
						$ai->setIdUser(dims::getInstance()->getUserId());
						$ai->setIdFichierModele($id_fichier_modele);
						$ai->setIdModule(dims_const::_DIMS_MODULE_SYSTEM);
						$ai->setIdWorkspace(dims::getInstance()->getCurrentWorkspaceId());
						// calcul du nombre d'elements
						$res=$db->query('SELECT count(*) as cpte from `'.str_replace('`', '', $temptable ).'` ');
						if ($db->numrows($res)) {
							if ($f=$db->fetchrow($res)) {
								$ai->fields['nbelements'] = $f['cpte'];
							}
						}
						$ai->save();

						// on redirige avec l'import
						return $ai->getId();
					}

				}
				else {
					echo "Writable error";
				}
			//}

			}
		}
		return 0 ;

	}

	/**
	 * Prend un format date excel ou date classique et le converti en big int
	 */
	public static function getDateToDimsFormat($date) {
		$date_dims = 0;
		if(is_numeric($date)){
			$datedeb_timestp = mktime(0,0,0,1,$date-1,1900);
			$date_formatee = date('d/m/Y',$datedeb_timestp);
			$date_dims = dims_local2timestamp($date_formatee);
		}else{
			$date_temp = str_replace(array('-', ' ', ':', '.'), '', $date);
			if(strlen($date_temp) < 10){
				$date_dims = substr($date_temp,4).substr($date_temp,2,2).substr($date_temp,0,2)."000000";
			}else{
				$date_dims = $date_temp ;
			}
		}
		return $date_dims;
	}

	public static function getCountErrorForGlobalIdConcerned($global_id){
		$count = 0 ;

		if($global_id >0){
			$db = dims::getInstance()->getDb();

			$sql = "SELECT * FROM `".str_replace('`', '', self::TABLE_NAME )."`
				WHERE id_globalobject_concerned = :globalid
				AND status = :status
				";
			$liste_name_temp_table = array();
			$res = $db->query($sql, array(
				':globalid' => $global_id,
				':status'	=> self::STATUT_FILE_IMPORT_IN_PROGRESS
			));
			while ($row = $db->fetchrow($res)) {
				$liste_name_temp_table[] = $row['ref_tmp_table'];
			}

			$sql = "";
			foreach ($liste_name_temp_table as $name_temp_table) {
				$sql .= " SELECT COUNT(*) FROM `".$name_temp_table."` ;";
			}
			if(!empty($sql)){
				$mysqli = new mysqli($db->server, $db->user, $db->password, $db->database);
				$mysqli->set_charset("utf8");
				if (mysqli_connect_errno()) {
					printf("Échec de la connexion : %s\n", mysqli_connect_error());
				}else{
					if ($mysqli->multi_query($sql)) {
						do {
							if ($result = $mysqli->use_result()) {
								while ($row = $result->fetch_row()) {
									$count += $row[0];
								}
								$result->close();
							}
						} while ($mysqli->next_result());
					}

					$mysqli->close();
				}
			}
		}

		return $count ;
	}

	public static function getAllErrorForGlobalIdConcerned($global_id, $list_field){
		$list_error = array();

		if($global_id >0){
			$db = dims::getInstance()->getDb();

			$sql = "SELECT * FROM `".str_replace('`', '', self::TABLE_NAME )."`
				WHERE id_globalobject_concerned = :globalid
				AND status = :status
				";
			$liste_name_temp_table = array();
			$liste_import = array();
			$res = $db->query($sql, array(
				':globalid' => $global_id,
				':status'	=> self::STATUT_FILE_IMPORT_IN_PROGRESS
			));
			while ($row = $db->fetchrow($res)) {
				$import = new dims_import();
				$import->openWithFields($row, true);
				$liste_import[$import->getRefTmpTable()] = $import;
				$liste_name_temp_table[] = $import->getRefTmpTable();
			}

			$sql = "";
			foreach ($liste_name_temp_table as $name_temp_table) {
				$sql .= " SELECT * FROM `".$name_temp_table."` ;";
			}
			if(!empty($sql)){
				$mysqli = new mysqli($db->server, $db->user, $db->password, $db->database);
				$mysqli->set_charset("utf8");
				if (mysqli_connect_errno()) {
					printf("Échec de la connexion : %s\n", mysqli_connect_error());
				}else{
					if ($mysqli->multi_query($sql)) {
						do {
							if ($result = $mysqli->use_result()) {
								while ($row = $result->fetch_assoc()) {
									$field = $result->fetch_field() ;
									$t_field = (array)$field;
									//echo $t_field['table'] ;
									$list_error_temp[$t_field['table']][] = $row ;
								}
								$result->close();
							}
						} while ($mysqli->next_result());
					}
					$mysqli->close();
				}
			}

			if(!empty ($list_error_temp)){
				$i = 0;
				foreach ($list_error_temp as $table_temp => $l_tuple) {
					if(isset($liste_import[$table_temp])){
						$tab_corresp = $liste_import[$table_temp]->getTableauCorresp();
						foreach ($l_tuple as $tuple) {
							foreach ($list_field as $field) {
								if(isset($tuple[dims_csv_import::cleaningNameHeader($tab_corresp[$field]->getLibelleColonne())])){
									$list_error[$i][$field] = $tuple[dims_csv_import::cleaningNameHeader($tab_corresp[$field]->getLibelleColonne())];
								}else{
									$list_error[$i][$field] = "";
								}
							}
							$list_error[$i]['id'] = $tuple['id'];
							$list_error[$i]['status'] = $tuple['status'];
							$list_error[$i]['id_import'] = $liste_import[$table_temp]->getId() ;
							$i++;
						}
					}
				}
			}
		}

		return $list_error;
	}

	public function getTableauCorresp(){
		$liste_correspondance_fichier = import_correspondance_colonne_champs::getListCorrespondanceByIdFichierLazy($this->getIdFichierModele());

		$liste_type_champs = import_champs_fichier_modele::getListChamps();

		$liste_column_table_temp = dims_csv_import::getTabColumnForTableTemp($this->getRefTmpTable());
		$corresp_ok = true;
		$tableau_corresp_ok = array();
		foreach ($liste_type_champs as $list_champs) {
			foreach ($list_champs as $champs) {
				//On vérifie le modèle
				if (isset($liste_correspondance_fichier[$champs->getId()])) {
					$tableau_corresp_ok[$champs->getLibelle()] = $liste_correspondance_fichier[$champs->getId()];
				}
			}
		}
		return $tableau_corresp_ok;
	}

	public function getDataWithCorrespByIdTuple($id_tuple) {
		$res_data = array();

		if($id_tuple > 0){
			require_once DIMS_APP_PATH . '/modules/system/import/class_import_correspondance_colonne_champs.php';
			require_once DIMS_APP_PATH . '/modules/system/import/class_import_fichier_modele.php';
			require_once DIMS_APP_PATH . '/modules/system/import/class_import_champs_fichier_modele.php';
			require_once DIMS_APP_PATH . '/include/class_csv_import.php';
			$db = dims::getInstance()->getDB();

			$tableau_corresp_ok = $this->getTableauCorresp();

			$sql = "SELECT	*
					FROM	`".str_replace('`', '', $this->getRefTmpTable() )."`
					WHERE	id = :idtuple ";

			$res = $db->query($sql, array(
				':idtuple' => $id_tuple
			));
			if ($row = $db->fetchrow($res)) {
				foreach ($tableau_corresp_ok as $key => $val) {
					$res_data[$key] = $row[dims_csv_import::cleaningNameHeader($val->fields['libelle_colonne'])];
				}
				$res_data['id'] = $row['id'];
			}

		}

		return $res_data;
	}

	public function executeCheckContactImport(){
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','1024M');

		$ref_tmp_table = $this->getRefTmpTable();
		$id_fichier_modele = $this->getIdFichierModele();
		$first_try_import = false;
		if((empty($ref_tmp_table)) || (($this->getStatus() != _IMPORT_STATUT_FILE_IMPORTED) && ($this->getStatus() != _IMPORT_STATUT_IMPORT_IN_PROGRESS))){
			//bad parameters
			echo "bad parameters";
			return ;
		}

		require_once DIMS_APP_PATH.'include/class_csv_import.php';
		$liste_column_table_temp = dims_csv_import::getTabColumnForTableTemp($this->getRefTmpTable());

		$db = dims::getInstance()->db;
		if (!isset($liste_column_table_temp['id_tiers']))
			$db->query("ALTER TABLE `".$this->fields['ref_tmp_table']."` ADD `id_tiers` INT(11) NOT NULL DEFAULT '0'");
		if (!isset($liste_column_table_temp['id_contact']))
			$db->query("ALTER TABLE `".$this->fields['ref_tmp_table']."` ADD `id_contact` INT(11) NOT NULL DEFAULT '0'");

		unset($liste_column_table_temp['id']);
		unset($liste_column_table_temp['status']);
		unset($liste_column_table_temp['id_tiers']);
		unset($liste_column_table_temp['id_contact']);

		$liste_column_table_tempBis = array();
		foreach($liste_column_table_temp as $key => $val) {
			if (substr($key,0,4)==="tag_") {
				$liste_column_table_tempBis[str_replace("_"," ",$key)] = array();
			}
			else {
				$liste_column_table_tempBis[strtolower($key)] = array();
			}
		}

		$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON			mb.id=mf.id_mbfield
			RIGHT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object IN ( :objecttiers , :objectcontact )
			AND			mf.used=1
			ORDER BY	mc.position, mf.position
			";

		$res = $db->query($sql, array(
			':objecttiers'		=> dims_const::_SYSTEM_OBJECT_TIERS,
			':objectcontact'	=> dims_const::_SYSTEM_OBJECT_CONTACT
		));
		$lstChamps = array();
		$lstCateg = array();
		while ($r = $db->fetchrow($res)){
			$ch = array();
			$ch['id_mtf'] = $r['id'];
			$ch['namefield'] = $r['namefield'];
			$ch['titlefield'] = $r['titlefield'];
			$ch['name'] = $r['name'];
			$ch['type'] = $r['type'];
			$ch['format'] = $r['format'];
			$ch['values'] = $r['values'];
			$ch['maxlength'] = $r['maxlength'];
			$ch['protected'] = $r['protected'];
			$lstChamps[$r['id_object']][$r['id']] = $ch;

			if (isset($liste_column_table_tempBis[strtolower($r['namefield'])])){
				$liste_column_table_tempBis[strtolower($r['namefield'])][$r['id_object']] = $r['id'];
			}elseif (isset($liste_column_table_tempBis[strtolower(((isset($_SESSION['cste'][$r['titlefield']]))?$_SESSION['cste'][$r['titlefield']]:$r['titlefield']))])){
				$liste_column_table_tempBis[strtolower(((isset($_SESSION['cste'][$r['titlefield']]))?$_SESSION['cste'][$r['titlefield']]:$r['titlefield']))][$r['id_object']] = $r['id'];
			}

		}
		$_SESSION['dims']['import']['fields_ct_tiers'] = $lstChamps;

		//dims_print_r($liste_column_table_tempBis);die();
		require_once DIMS_APP_PATH."modules/system/import/class_check_fields.php";
		$lstTiers = import_check_fields::getListForType(dims_const::_SYSTEM_OBJECT_TIERS);

		$lstCt = import_check_fields::getListForType(dims_const::_SYSTEM_OBJECT_CONTACT);
		foreach($liste_column_table_tempBis as $key => $val){
			if (isset($lstTiers[$key])) {
				//die($lstTiers[$key]);
				$liste_column_table_tempBis[$key][dims_const::_SYSTEM_OBJECT_TIERS] = $lstTiers[$key];
			}
			if (isset($lstCt[$key]))
				$liste_column_table_tempBis[$key][dims_const::_SYSTEM_OBJECT_CONTACT] = $lstCt[$key];
		}


		return $liste_column_table_tempBis;
	}

	public function returnListObject($nameObj,$typeObj, $lstCorresp){
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','1024M');
		$db = dims::getInstance()->db;
		if (!isset($_SESSION['dims']['import']['fields_ct_tiers'][$typeObj])){
			$sql =	"
				SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
				FROM		dims_mod_business_meta_field as mf
				INNER JOIN	dims_mb_field as mb
				ON			mb.id=mf.id_mbfield
				RIGHT JOIN	dims_mod_business_meta_categ as mc
				ON			mf.id_metacateg=mc.id
				WHERE		mf.id_object = :typeobj
				AND			mf.used=1
				ORDER BY	mc.position, mf.position
				";
			$res = $db->query($sql, array(
				':typeobj' => $typeObj
			));
			$lstChamps = array();
			$lstCateg = array();
			while ($r = $db->fetchrow($res)){
				$ch = array();
				$ch['id_mtf'] = $r['id'];
				$ch['namefield'] = $r['namefield'];
				$ch['titlefield'] = $r['titlefield'];
				$ch['name'] = $r['name'];
				$ch['type'] = $r['type'];
				$ch['format'] = $r['format'];
				$ch['values'] = $r['values'];
				$ch['maxlength'] = $r['maxlength'];
				$ch['protected'] = $r['protected'];
				$lstChamps[$r['id']] = $ch;
			}
			$_SESSION['dims']['import']['fields_ct_tiers'][$typeObj] = $lstChamps;
		}

		$sql = "SELECT	*
				FROM	".$this->getRefTmpTable()."
				WHERE	status != :status ";
		$res = $db->query($sql, array(
			':status' => _STATUS_IMPORT_OK
		));
		$lst = array();
		$timp = dims_createtimestamp();

		$tagscorresp=array();
		$tagsexists=$this->loadTags();

		//dims_print_r($tagsexists);

		while($r = $db->fetchrow($res)){

			$tags=array();

			$r2 = array_change_key_case($r, CASE_LOWER);
			$r3 = array_keys($r2);

			foreach($r as $k => $v) {
				$keytag=0;

				if (substr($k,0,3)=="tag") {
					if ($v!="") {
						if (!isset($tagscorresp[$k])) {
							// on calcule la premiere fois la correspondance
							$v2=trim(str_replace("_"," ",substr($k,3)));
							$ind=in_array($v2,$tagsexists);
							if ($ind>0) {
								$keytag=array_search($v2,$tagsexists);
							}

						}

						if ($keytag>0) $tags[$keytag]=1;
					}
				}
			}

			//dims_print_r($tags);die();
			foreach($r3 as $k => $v) {
				$r3[$k] = str_replace(array(" ","-","."),"_",$v);
			}

			$r2 = array_combine($r3,$r);
			$obj = new $nameObj;
			$obj->init_description();

			foreach($lstCorresp as $key => $val){
				if (isset($_SESSION['dims']['import']['fields_ct_tiers'][$typeObj][$val]['namefield']) && isset($obj->fields[$_SESSION['dims']['import']['fields_ct_tiers'][$typeObj][$val]['namefield']]))
					$obj->fields[$_SESSION['dims']['import']['fields_ct_tiers'][$typeObj][$val]['namefield']] = $r2[$key];
			}

			if (isset($r['JobTitle'])) $obj->JobTitle=$r['JobTitle'];

			$obj->setLightAttribute('id_tmp',$r['id']);
			$obj->setLightAttribute('tags',$tags);

			if (isset($r['date'])){
				$obj->setLightAttribute('date',dims_local2timestamp($r['date']));
			}elseif (isset($r['Date'])){
				$obj->setLightAttribute('date',dims_local2timestamp($r['Date']));
			}else{
				$obj->setLightAttribute('date',$timp);
			}

			if ($nameObj=='contact' && isset($obj->fields['lastname']) && trim($obj->fields['lastname'])!=""
				&& isset($obj->fields['firstname']) && trim($obj->fields['firstname'])!="")
				$lst[] = $obj;
			elseif ($nameObj=='tiers' && isset($obj->fields['intitule']) && trim($obj->fields['intitule'])!="") {
				$lst[] = $obj;
			}

			if ( isset($obj->fields['lastname']) && trim($obj->fields['lastname'])==""
				&& isset($obj->fields['firstname']) && trim($obj->fields['firstname'])=="") {
				$db->query("delete from ".$this->getRefTmpTable()." where id=".$r['id']);

			}
		}
		return $lst;
	}


	public function deleteTags() {
		$db = dims::getInstance()->db;

		$res = $db->query("delete from dims_import_tag where id_import=".$this->fields['id']);
	}


	public function checkTag($tagvalue){
		// on va créer l'association des tags avec les personnes et tiers concernes
		require_once DIMS_APP_PATH.'/modules/system/import/class_import_tag.php';
		require_once DIMS_APP_PATH.'/modules/system/class_tag.php';
		$id_tag=0;

		$params=array();
		$params[":tag"]=($tagvalue);

		$res = $this->db->query("select t.id from dims_tag as t WHERE t.tag like :tag",$params);

		if ($this->db->numrows($res)>0) {
			if ($f=$this->db->fetchrow($res)) {
				$id_tag=$f['id'];
				//dims_print_r($f);
			}
		}
		else {
			// on cree le tag
			$tag = new tag();
			$tag->fields['tag']=$tagvalue;
			$tag->fields['type']=0;
			$tag->fields['timestp_create']=  dims_createtimestamp();
			$tag->fields['timestp_modify']=  dims_createtimestamp();
			$tag->setugm();
			$id_tag=$tag->save();


		}

		if ($id_tag>0) {
			// on enregistre le tag
			$import_tag = new import_tag();
			$import_tag->fields['id_import']=$this->fields['id'];
			$import_tag->fields['id_tag']=$id_tag;
			$import_tag->save();
		}
	}

	public function loadTags() {

		$tags=array();
		$params=array();
		$params[":id_import"]=strtolower($this->fields['id']);
		$res = $this->db->query("select it.id_tag,t.tag from dims_import_tag as it inner join dims_tag as t on t.id=it.id_tag AND id_import = :id_import",$params);

		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$tags[$f['id_tag']]=str_replace("_"," ",$f['tag']);
			}
		}

		return $tags;
	}

	public function attachTagsGo($id_go,$tagused=null) {
		$listtags=array();
		require_once DIMS_APP_PATH.'/modules/system/class_tag_globalobject.php';
		if ($tagused==null) {
			// verification si attache ou non
			$params=array();
			$params[":idgo"]=strtolower($id_go);
			$res = $this->db->query("select id_tag from dims_tag_globalobject WHERE id_globalobject = :idgo",$params);

			$tabexists=array();
			if ($this->db->numrows($res)>0) {
				while ($f=$this->db->fetchrow($res)) {
					$tabexists[$f['id_tag']]=$f['id_tag'];
				}
			}
			//dims_print_r($tabexists);

			// on regarde maintenant par rapport aux nouveaux
			if (!isset($this->existingTags)) {
				$this->existingTags=$this->loadTags();
			}

			$tagused=$this->existingTags;
		}

		// on boule sur tous les tags
		//dims_print_r($tagused);die();
		foreach ($tagused as $newtag =>$nomtag) {
			if (!isset($tabexists[$newtag])) {
				//die($newtag." ");
				// on ajoute la correspondance
				$tg =  new tag_globalobject();
				$tg->fields['id_tag']=$newtag;
				$tg->fields['id_globalobject']=$id_go;
				$tg->fields['timestp_modify']=  dims_createtimestamp();
				$tg->save();
			}
		}

	}
}

?>
