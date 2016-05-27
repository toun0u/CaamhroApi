<?php
/**
* Generic import CSV class.
*
* @version 1.0
* @since 0.1
*
* @access public
* @abstract
*
* @package includes
* @subpackage data object
*
* @author Netlor SAS / Wave Software
* @copyright 2003-2011 Netlor SAS / Wave Software
* @license http://www.dims.fr
*/

class dims_csv_import extends DIMS_DATA_OBJECT {
  private $table_name;						// name of table
  private $file_name;						// input file
  private $use_header;						// use first line for columns names
  private $field_separate_char;				// character to separate fields
  private $field_enclose_char;				// character to enclose fields
  private $field_escape_char;				// char to escape special symbols
  private $headers;							// array of columns
  private $headers_conv;					// array of columns convertis
  private $table_exists;					// test if table exists
  private $encoding; 						//encoding table, used to parse the incoming file. Added in 1.5 version
  private $file_encoding; 					//encoding incoming file

  private $whitelist_headers;//Liste blanche de colonnes acceptée, pour contourner le pb de PDO qui entoure les colonnes avec des '' dans le load_data
  private $can_import;

  function __construct($file_name) {
		$this->file_name = $file_name;
		$this->headers = array();
		$this->headers_conv = array();
		$this->whitelist_headers = array();
		$this->can_import = false;//volontairement à false, doit passer soit par le squeeze des csv header, soit par la validation de la white list
		$this->use_csv_header = true;
		$this->field_separate_char = ",";
		$this->field_enclose_char  = "\"";
		$this->field_escape_char   = "\\";
		$this->table_exists = false;
		$this->file_encoding = 'UTF8';
		$this->db = dims::getInstance()->getDb();
	}

	function setfield_separate_char($sep){
		$this->field_separate_char = $sep;
	}
	function setfield_enclose_char($sep){
		$this->field_enclose_char = $sep;
	}
	function setuse_csv_header($sep){
		$this->use_csv_header = $sep;
	}

	function import() {

		if($this->table_name=="") $this->table_name = "temp_".date("d_m_Y_H_i_s");

		$this->table_exists = false;
		$this->createTableImport();

		if(empty($this->headers)) $this->getCsvHeaders();
		if($this->can_import){
			/* change start. Added in 1.5 version */
			if("" != $this->encoding && "default" != $this->encoding)
			  $this->set_encoding();
			/* change end */

			if($this->table_exists) {
				$params = array();
				/* --- Construction de la liste des headers ---
				Note : vu qu'on passe par PDO on ne peut plus envoyer les colonnes directement en param de PDO
				parce que PDO les entoure d'apostrophe et du coup ça pète l'import.
				C'est l'origine de la white list
				*/
				$columns = '';
				$a = 0;
				foreach($this->headers_conv as $col){
					if($a > 0) $columns .= ',';
					$columns .= $col;
					$a++;
				}
				$sql = 'LOAD DATA LOCAL INFILE :filename'.
					 ' INTO TABLE '.$this->table_name.
					 ' CHARACTER SET :file_encoding'.
					 ' FIELDS TERMINATED BY :separate_car'.
					 ' OPTIONALLY ENCLOSED BY :enclose'.
					 ' ESCAPED BY :escape'.
					 ($this->use_csv_header ? ' IGNORE 1 LINES ' : '')
					 ."(".$columns.")";

				$params[':filename'] 		= array('type' => PDO::PARAM_STR, 'value' => $this->file_name);
				$params[':separate_car'] 	= array('type' => PDO::PARAM_STR, 'value' => $this->field_separate_char);
				$params[':enclose'] 		= array('type' => PDO::PARAM_STR, 'value' => $this->field_enclose_char);
				$params[':escape'] 			= array('type' => PDO::PARAM_STR, 'value' => $this->field_escape_char);
				$params[':file_encoding'] 	= array('type' => PDO::PARAM_STR, 'value' => $this->file_encoding);

				$res = $this->db->query($sql, $params);
			}
		}
		else{
			self::deleteTableTemp($this->table_name);
		}
	}

  public function getTableTemp() {
	  return $this->table_name;
  }

  public function set_whitelist_headers($tab){
  	$this->whitelist_headers = $tab;
  }

  //returns array of CSV headers
  function getCsvHeaders() {
	$this->arr_csv_columns = array();
	$ffile = fopen($this->file_name, "r");

	if ($ffile) {

		$arr = @fgetcsv($ffile, 10*1024, $this->field_separate_char);
		if(is_array($arr) && !empty($arr)) {
			if($this->use_csv_header) {
				$i = 1;
				foreach($arr as $val) {
					if(trim($val)!="") {
						$conv = str_replace(array(" ","-", "'"),"_",$val);
						if(isset($this->whitelist_headers[$conv])){
							$this->headers[$i] = $val;
							$this->headers_conv[$i] = $conv;
							$i++;
						}
						else{//Cyril - on squizze tout parce que c'est en erreur PDO ne fonctionnera pas
							$this->headers = array();
							$this->headers_conv = array();
							return $this->headers;
						}
					}
				}
			}
			else {
				$i = 1;
				foreach($arr as $key => $val) {
					if (!($key == sizeof($arr) && trim($val) == "")) {
						$this->headers[$i] = "column".$i;
						$this->headers_conv[$i] = "column".$i;
						$i++;
					}
				}
			}
		}
		unset($arr);
		fclose($ffile);
	}
	//si on arrive jusque là c'est qu'on a passé la white list ou qu'on utilise des colonnes pré-formatées on peut donc importer
	$this->can_import = true;
	return $this->headers;
  }

  function createTableImport() {
	$sql = "CREATE TABLE IF NOT EXISTS ".$this->table_name." (";

	if(empty($this->headers))
		$this->getCsvHeaders();

	if(!empty($this->headers)) {
		$arr = array();
		for($i=1; $i<=sizeof($this->headers); $i++) {
			$arr[] = "`".$this->headers_conv[$i]."` TEXT";
		}
		$sql .= implode(",", $arr);
		$sql .= ") character set utf8 COLLATE utf8_general_ci";
		$res = $this->db->query($sql);
		$this->table_exists=true;
	}
  }

  /* change start. Added in 1.5 version */
  //returns recordset with all encoding tables names, supported by your database
  function get_encodings() {
	$rez = array();
	$sql = "SHOW CHARACTER SET";
	$res = $this->db->query($sql);
	if($this->db->numrows($res) > 0) {
	  while ($row = $this->db->fetchrow($res)) {
		$rez[$row["Charset"]] = ("" != $row["Description"] ? $row["Description"] : $row["Charset"]); //some MySQL databases return empty Description field
	  }
	}
	return $rez;
  }

  //defines the encoding of the server to parse to file
  function set_encoding($encoding="") {
	if("" == $encoding)
	  $encoding = $this->encoding;
	$sql = "SET SESSION character_set_database = :encoding"; //'character_set_database' MySQL server variable is [also] to parse file with rigth encoding
	$res = $this->db->query($sql, array(':encoding' => $encoding));
	return true;
  }

	public function set_file_encoding($encoding="") {
		if ($encoding != "") {
			$this->file_encoding = $encoding;
		}
	}

  /**
   *
   * @param String $name_table : nom de la table à charger
   * @param array $list_col : $liste des colonnes indexée par leur valeur de champs
   * exempe : $list_col['contrat collectif'] = KVNAME
   * @param int or array $status : Status des tuples à remonter
   * @return array de résultat indexés par l'id du tuple temporaire. Ce tableau
   * contient un second tableau contenant les valeurs remontées chaque tuples
   * Ces valeurs sont indexées via le nom du champs.
   * Exempe : $res[1]['contrat_collectif'] = CC_Assureu_r2011
   */
  public static function requestTempTable($name_table, array $list_col, $status, $list_id = null, $order_by = ''){

	  $list_tuple = array();
	  $db = dims::getInstance()->getDb();

	  $params = array();
	  if(is_array($list_id)){
		  $listid_string = " AND id IN (".$db->getParamsFromArray($list_id, 'id', $params).")";
	  }else{
		  $listid_string = "";
	  }

	  if(!empty($list_col)){
		  $select = "" ;
		  foreach ($list_col as $key => $col) {
		  // FIXME : Should check fields match table columns.
			  $select .= "`".self::cleaningNameHeader($col)."`"." as ".$key.",";
		  }
		  $select = substr($select, 0, strlen($select)-1);

		  if(is_array($status)){
			  $sql = "SELECT id,".$select." FROM ".$name_table."
				  WHERE status IN (".$db->getParamsFromArray($status, 'status', $params).")".$listid_string;
		  }else{
			  $sql = "SELECT id,".$select." FROM ".$name_table."
				  WHERE status = :status ".$listid_string ;
		$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => $status);
		  }
		  if($order_by != ''){
			  $sql .= " ORDER BY `".self::cleaningNameHeader($order_by)."` ASC ";
		  }
	  }else{
		  if(is_array($status)){
			  $sql = "SELECT id FROM ".$name_table."
				  WHERE status IN (".$db->getParamsFromArray($status, 'status', $params).")".$listid_string;
		  }else{
			  $sql = "SELECT id FROM ".$name_table."
				  WHERE status = :status ".$listid_string ;
		$params[':status'] = array('type' => PDO::PARAM_INT, 'value' => $status);
		  }
		  if($order_by != ''){
			  $sql .= " ORDER BY `".self::cleaningNameHeader($order_by)."` ASC ";
		  }
	  }

	  $res = $db->query($sql, $params);

	  while($row = $db->fetchrow($res)){
		  $list_tuple[$row['id']] = $row ;
	  }

	  return $list_tuple ;
  }

  public static function cleaningNameHeader($name_header){
	  $name_conv = "";

	  $name_conv = str_replace(array(" ","-"),"_",$name_header);

	  return $name_conv;
  }

  public static function updateStatusTempTable($table, $list_id, $status){

	  if(!empty($list_id)){
		  $db = dims::getInstance()->getDb();
		  $list_id = array_unique($list_id);
	  $params = array();
	  $params[':status'] = array('type' => PDO::PARAM_INT, 'value' => $status);
		  $sql = "UPDATE ".$table."
			  SET status = :status
			  WHERE id IN (".$db->getParamsFromArray($list_id, 'id', $params).")
			  ";
		  $db->query($sql, $params);
	  }

  }

  public static function reinitialiseStatusTempTable($table, $status){
	  $db = dims::getInstance()->getDb();
	  $sql = "UPDATE ".$table."
		  SET status = :status
			  ";
	  $db->query($sql, array(
		':status' => array('type' => PDO::PARAM_INT, 'value' => $status),
	  ));
  }

  public static function deleteRowTempTableForListStatus($table, $list_status){
	  if(!empty($list_id)){
		  $db = dims::getInstance()->getDb();

		$params = array();
		  $sql = "DELETE FROM ".$table."
			  WHERE status IN (".$db->getParamsFromArray($list_status, 'status', $params).")
			  ";
		  $db->query($sql, $params);
	  }
  }

  public static function deleteRowTempTableForListId($table, $list_id){
	  if(!empty($list_id)){
		  $db = dims::getInstance()->getDb();

		$params = array();
		  $sql = "DELETE FROM ".$table."
			  WHERE id IN (".$db->getParamsFromArray($list_id, 'id', $params).")
			  ";
		  $db->query($sql, $params);
	  }
  }

  public static function getIdRowTempTableForListeStatus($table, $list_status){
	  $liste_id_tuple_temp = array() ;

	  if(!empty ($list_status)){
		  $db = dims::getInstance()->getDb();

		$params = array();
		  $sql = "SELECT id FROM ".$table."
			  WHERE status IN (".$db->getParamsFromArray($list_status, 'status', $params).")
			  ";

		  $res = $db->query($sql, $params);
		  while ($row = $db->fetchrow($res)) {
			  $liste_id_tuple_temp[] = $row['id'];
		  }
	  }

	  return $liste_id_tuple_temp ;
  }

  public static function getTabColumnForTableTemp($table_temp){
	  $list_col = array();

	  $db = dims::getInstance()->getDb();

	  $sql = "DESCRIBE `".str_replace("`", "", $table_temp)."`";
	  $result = $db->query($sql);

	  while ($row = $db->fetchrow($result)){
		  $list_col[$row['Field']] = '';
	  }

	  return $list_col ;
  }

	public static function getNbRowForTableTemp($table_temp){
		$db = dims::getInstance()->getDb();

		if($db->tableexist($table_temp)) {
			$sql = "SELECT COUNT(*) AS C FROM `".str_replace("`", "", $table_temp)."`";
			$res = $db->query($sql);
			$row = $db->fetchrow($res);
			if ($row) {
				return $row['C'] ;
			}
		}
		return 0;
	}

	public static function deleteTableTemp($table_temp){
		$db = dims::getInstance()->getDb();

		if($db->tableexist($table_temp)) {
			$sql = "DROP TABLE `".str_replace("`", "", $table_temp)."`";;

			$db->query($sql);
		}
	}

	public static function importFile($filepath,$session_dir,$structonly=false,$id_import) {
		$db = dims::getInstance()->getDb();

		$error=0;
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','1024M');

		if($db->tableexist($table_temp)) {
			$extension	= explode(".", $filepath);
			$extension	= $extension[count($extension)-1];
			$extension	= strtolower($extension);
			// traitement du fichier
			if ($extension=='csv' || $extension=='xls' || $extension=='xlsx') {
				if ($extension!='csv') {
					// conversion en csv
					$pathexec = str_replace(" ","\ ",$filepath);
					$exec="xls2csv -s UTF-8 -d UTF-8 ".escapeshellarg($pathexec)." > ".escapeshellarg($session_dir."/result.csv");
					shell_exec('LANG=en_US.utf-8; '.escapeshellcmd($exec));
					$filepath=$session_dir."/result.csv";
				}
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

					$ai = new import();
					$ai->setIdGlobalobjectConcerned($_SESSION['dims']['import']['import']['id_globalobject_concerned']);
					$ai->setIdFichierModele($_SESSION['dims']['import']['import']['id_fichier_modele']);
					$ai->setTimestpCreate(dims_createtimestamp());
					$ai->setTimestpModify(dims_createtimestamp());
					$ai->setRefTmpTable($temptable);
					$ai->setNbelements(0);
					$ai->setStatus(_IMPORT_STATUT_FILE_IMPORTED);

					// calcul du nombre d'elements
					$res=$db->query('select count(*) as cpte from `'.str_replace("`", "", $temptable).'`');
					if ($db->numrows($res)) {
						$f=$db->fetchrow($res) ;
						if ($f) {
							$ai->setNbelements($f['cpte']);
						}
					}
					$ai->save();

					// ajout de la colonne status dans la table temp
					$db->query("ALTER TABLE `".str_replace("`", "", $temptable)."` ADD `status` TINYINT(4) NOT NULL DEFAULT '0'");

					// creation de l'id unique pour le traitement
					$db->query("ALTER TABLE `".str_replace("`", "", $temptable)."` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");

					// on retourne l'id import
					$id_import=$csvimport->fields['id'];
				}
			}
			else {
				$error=_IMPORT_STATUT_FILE_NOT_CORRECT;
			}
		}

		return $error;
	}

}
