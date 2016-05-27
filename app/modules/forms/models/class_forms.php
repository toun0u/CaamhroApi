<?php
/**
* @author	NETLOR CONCEPT
* @version	3.0
* @package	forms
* @access	public
*/

class forms extends pagination {
	const TABLE_NAME = 'dims_mod_forms';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	public $array_fields;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	function save() {
		if ($this->fields['tablename'] == '') $this->fields['tablename'] = $this->fields['label'];
		$this->fields['tablename'] = forms_createphysicalname($this->fields['tablename']);
		return(parent::save(_FORMS_OBJECT_FORM));
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		$this->deleteData();
		parent::delete(_FORMS_OBJECT_FORM);
	}

	public function deleteData() {
		// suppression des donnees enregistrees
		$rr = reply_field::find_by(array('id_forms'=>$this->get('id')));
		foreach($rr as $r){
			$r->delete();
		}
		$rr = field::find_by(array('id_forms'=>$this->get('id')));
		foreach($rr as $r){
			$r->delete();
		}
		$rr = reply::find_by(array('id_forms'=>$this->get('id')));
		foreach($rr as $r){
			$r->delete();
		}

		// suppression du dossier de stockage
		$path = _DIMS_PATHDATA.'forms-'.$this->fields['id_module']._DIMS_SEP.$this->fields['id_forms']._DIMS_SEP;
		dims_deletedir($path);
	}

	function getfields() {
		$db = dims::getInstance()->getDb();

		$fields = array();

		$select = "SELECT * FROM dims_mod_forms_field WHERE id_forms = :idform AND `separator` = 0";

		$res=$db->query($select, array(':idform' => $this->fields['id'] ) );

		while ($row = $db->fetchrow($res)) {
			$fields[$row['id']] = $row;
		}

		return($fields);
	}

	function getPreview() {
		$db = dims::getInstance()->getDb();
		// construction du content du formulaire
		ob_start();

		$forms_id  = $this->fields['id'];
		include(DIMS_APP_PATH . '/modules/forms/public_forms_display.php');
		$content = ob_get_contents();
		ob_end_clean();
		if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $rootpath="http://";
			else $rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];

		return $content;
	}

	/*
	 * fonction permettant de lister les valeurs saisies d'un formulaire
	 */
	function getListData($where,$pagination=false) {
		$data=array();

		if (!$pagination) {
			pagination::liste_page($this->getListData($where,true));
			$limit				= "LIMIT ".$this->sql_debut.", ".$this->limite_key;
		}
		else {
			$limit="";
		}

		if ($where!='') {
			// construction de la liste des reply qui correspondent aux crit�res saisis
			$lstreply='';

			$select =	"
					SELECT		distinct id_reply
					FROM		dims_mod_forms_reply_field
					WHERE		id_forms = :idform
					$where";

			$result = $this->db->query($select, array(':idform' => $this->fields['id'] ) );

			$listreply='';
			while ($fields = $this->db->fetchrow($result)) {
				if ($listreply=='') {
					$listreply=$fields['id_reply'];
				}
				else {
					$listreply.=",".$fields['id_reply'];
				}

			}

			if ($listreply=='') {
				$listreply=0;
			}

			$where= " and fr.id in (".$listreply.")";

		}
		$select =	"
					SELECT		fr.*,
								u.id as userid,
								u.firstname,
								u.lastname,
								u.login
					FROM		dims_mod_forms_reply fr

					LEFT JOIN	dims_user u
					ON			fr.id_user = u.id

					WHERE		fr.id_forms = :idform
					$where
					$limit
					";

		$result = $this->db->query($select, array(':idform' => $this->fields['id'] ) );

		if ($pagination) {
			return $this->db->numrows($result);
		}
		else {
			$listreply='';
			while ($fields = $this->db->fetchrow($result)) {
				if ($listreply=='') {
					$listreply=$fields['id'];
				}
				else {
					$listreply.=",".$fields['id'];
				}

			}

			if ($listreply=='') {
				$listreply=0;
			}

			// construction des reponses
			$select =	"
					SELECT		rf.*, f.type
					FROM		dims_mod_forms_reply_field as rf
					INNER JOIN	dims_mod_forms_field as f
					ON			f.id = rf.id_field
					AND			rf.id_forms= :idform
					AND			rf.id_reply in (".$listreply.")
					AND			f.option_cmsdisplaylabel=1
					order by	f.position";

			$rs_replies = $this->db->query($select, array(':idform' => $this->fields['id']) );

			$array_values = array();

			while ($fields_replies = $this->db->fetchrow($rs_replies)) {
				$data[$fields_replies['id_reply']][$fields_replies['id_field']] = (isset($fields_replies['value'])) ? str_replace('"','\'',$fields_replies['value']) : '';
			}

			// on boucle sur les elements de reponses
			return $data;
		}
	}

	public static function getAllForms($moduleid = 0) {
		if (empty($modulesid)) {
			// FIXME : Avoid globals in method - Should be deleted.
			$moduleid = $_SESSION['dims']['moduleid'];
		}

		$db = dims::getInstance()->getDb();
		$workspaces = explode(',',dims_viewworkspaces($moduleid));

		$params = array(
			':idmodule' => $moduleid,
		);
		$sql = "SELECT 		".self::TABLE_NAME.". * , COUNT( fr.id ) AS cpte
				FROM 		".self::TABLE_NAME."
				LEFT JOIN 	".reply::TABLE_NAME." AS fr
				ON 			fr.id_forms = ".self::TABLE_NAME.".id
				WHERE 		".self::TABLE_NAME.".id_module = :idmodule
				AND 		".self::TABLE_NAME.".id_workspace IN (".$db->getParamsFromArray($workspaces, 'wp', $params).")
				GROUP BY 	".self::TABLE_NAME.".id
				ORDER BY 	pubdate_start DESC , pubdate_end DESC";

		$res=$db->query($sql,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$f = new self();
			$f->openFromResultSet($r);
			$f->setLightAttribute('cpte',$r['cpte']);
			$lst[$f->get('id')] = $f;
		}
		return $lst;
	}

	public function getAllFields(){
		return field::find_by(array('id_forms'=>$this->get('id'))," ORDER BY position ");
	}

	public function getAnswers($filtre = "", $paginat = false, $pagination = false){
		if(!empty($filtre)){
			$db = dims::getInstance()->getDb();
			$params = array(
				':v'=>"%$filtre%",
			);
			if($paginat){
				$limit = "";
				if (!$pagination) {
					self::liste_page($this->getAnswers($filtre,true,true));
					$params[':start'] = array('type'=>PDO::PARAM_INT,'value'=>$this->sql_debut);
					$params[':end'] = array('type'=>PDO::PARAM_INT,'value'=>$this->limite_key);
					$limit = " LIMIT :start, :end ";
				}
			}
			$sel = "SELECT 		r.*
					FROM 		".reply::TABLE_NAME." r
					INNER JOIN 	".reply_field::TABLE_NAME." f
					ON 			r.id = f.id_reply
					AND 		r.id_forms = f.id_forms
					WHERE 		value LIKE :v
					ORDER BY 	r.date_validation $limit";
			$res = $db->query($sel,$params);
			if($paginat){
				if ($pagination) {
					return $db->numrows($res);
				}else{
					$lst = array();
					while($r = $db->fetchrow($res)){
						$rr = new reply();
						$rr->openFromResultSet($r);
						$lst[$rr->get('id')] = $rr;
					}
					return $lst;
				}
			}else{
				$lst = array();
				while($r = $db->fetchrow($res)){
					$rr = new reply();
					$rr->openFromResultSet($r);
					$lst[$rr->get('id')] = $rr;
				}
				return $lst;
			}
		}else{
			if($paginat){
				if (!$pagination) {
					self::liste_page($this->getAnswers($filtre,true,true));
					return reply::find_by(array('id_forms'=>$this->get('id'))," ORDER BY date_validation ",$this->sql_debut,$this->limite_key);
				}else{
					return count(reply::find_by(array('id_forms'=>$this->get('id'))," ORDER BY date_validation "));
				}
			}else{
				return reply::find_by(array('id_forms'=>$this->get('id'))," ORDER BY date_validation ");
			}
		}
	}

	public function valideRight($id_reply = 0){
		if(!$this->isNew()){
			require_once DIMS_APP_PATH . '/include/functions/workflow.php';
			$db = dims::getInstance()->getDb();
			$wfusers = array();
			$wgroups = array();

			$dims_workflow_get = dims_workflow_get(_FORMS_OBJECT_FORM, $this->get('id'),-1,-1,_FORMS_ACTION_ADDREPLY);
			foreach($dims_workflow_get as $value) {
				if ($value['type_workflow']=='user') {
					$wfusers[] = $value['id_workflow'];
				}
				else {
					$wfgroups[] = $value['id_workflow'];
				}
			}

			$pubusers = array();
			if (!empty($wfusers)) {
				$params = array();
				$sql = "SELECT 		DISTINCT id,login,lastname,firstname
						FROM 		".user::TABLE_NAME."
						WHERE 		id IN (".$db->getParamsFromArray($wfusers, ':id', $params).")
						ORDER BY 	lastname, firstname";
				$res = $db->query($sql,$params);
				while ($row = $db->fetchrow($res)){
					$pubusers[$row['id']] = $row;
				}
			}

			if (!empty($wfgroups)) {
				$params = array();
				$sql = "SELECT 		DISTINCT u.id,u.login,u.lastname,u.firstname
						FROM 		".user::TABLE_NAME." u
						INNER JOIN 	".group_user::TABLE_NAME." AS gu
						ON 			gu.id_user = u.id
						AND			gu.id_group IN (".implode(',',$wfgroups).")
						ORDER BY 	u.lastname, u.firstname";
				$res=$db->query($sql,$params);
				while ($row = $db->fetchrow($res)){
					$pubusers[$row['id']] = $row;
				}
			}
			$readonlyform=!isset($pubusers[$_SESSION['dims']['userid']]);
		}
		return (dims_isadmin() || (dims_isactionallowed(_FORMS_ACTION_ADDREPLY) && $id_reply==0) || dims_isactionallowed(0) || (($forms->fields['option_modify'] == 'user' && $forms->fields['userid'] == $_SESSION['dims']['userid']) || ($forms->fields['option_modify'] == 'all')) ||  !$readonlyform);
	}

	public function replyTo(reply &$reply, $post = array(), $files = array()){
		if(empty($post) && isset($_POST)) $post = $_POST;
		if(empty($files) && isset($_FILES)) $files = $_FILES;
		$lstReply = array();
		if($reply->isNew()){
			$reply->init_description();
			$reply->setugm();
			$reply->set('ip',$_SERVER['REMOTE_ADDR']);
			$reply->set('id_forms',$this->get('id'));
			$reply->set('date_validation',dims_createtimestamp());
		}elseif($reply->get('id_forms') != $this->get('id')){
			return false;
		}else{
			$lstReply = $reply->getFields();
		}
		$fields = $this->getAllFields();

		// on fait une première boucle pour vérifier que tous les champs obligatoires sont présents
		foreach($fields as $f){
			if($f->get('option_needed') && (empty($post['field_'.$f->get('id')]) && empty($files['field_'.$f->get('id')]))){
				return false;
			}
			if(!isset($lstReply[$f->get('id')])){
				$reply_field = new reply_field();
				$reply_field->init_description();
				$reply_field->setugm();
				$reply_field->set('id_forms',$this->get('id'));
				$reply_field->set('id_field',$f->get('id'));
				$lstReply[$f->get('id')] = $reply_field;
			}
		}
		$reply->save();

		foreach($fields as $f){
			$reply_field = $lstReply[$f->get('id')];
			$reply_field->set('timestp_modify',dims_createtimestamp());
			$reply_field->set('id_reply',$reply->get('id'));
			switch ($f->get('type')) {
				case 'file':
					$value = "";
					$path = _DIMS_PATHDATA.'forms-'.$this->get('id_module')._DIMS_SEP.$this->get('id')._DIMS_SEP.$reply->get('id')._DIMS_SEP;
					if (!empty($files['field_'.$f->get('id')]['name'])) {
						if(!$reply_field->isNew() && $reply_field->get('value') != "" && file_exists($path.$reply_field->get('value'))){
							unlink($path.$reply_field->get('value'));
						}
						$value = $files['field_'.$f->get('id')]['name'];
						dims_makedir($path);
						if (file_exists($path) && is_writable($path)) {
							move_uploaded_file($files['field_'.$f->get('id')]['tmp_name'], $path.$value);
							{
								chmod($path.$value, 0777);
							}
						}
					}
					$reply_field->set('value',$value);
					$reply_field->save();
					break;
				case 'autoincrement':
					$select = "	SELECT 	MAX(value) as maxinc
								FROM 	".reply_field::TABLE_NAME."
								WHERE 	id_forms = :idform
								AND 	id_field = :idfield";
					$params = array(
						':idform' => $this->get('id'),
						':idfield' => $f->get('id'),
					);
					$db = dims::getInstance()->getDb();
					$rs_maxinc = $db->query($select, $params);
					if($r = $db->fetchrow($rs_maxinc))
						$reply_field->set('value',((!empty($r['maxinc']))?$r['maxinc']+1:1));
					else
						$reply_field->set('value',1);
					break;
				default:
					$value = "";
					if(isset($post['field_'.$f->get('id')])){
						if(is_array($post['field_'.$f->get('id')]))
							$value = implode("||", $post['field_'.$f->get('id')]);
						else
							$value = $post['field_'.$f->get('id')];
					}
					$reply_field->set('value',$value);
					$reply_field->save();
					break;
			}
			$lstReply[$f->get('id')] = $reply_field;
		}
		return true;
	}

	public function export($format = "CSV", $search = "", $front = false){
		$replies = $this->getAnswers($search);
		$fields = $this->getAllFields();//option_exportview

		while (@ob_end_clean());
		switch ($format) {
			default:
			case 'CSV':
				header("Cache-control: private");
				header("Content-type: text/x-csv");
				header("Content-Disposition: attachment; filename=export_".str_replace(" ", "_", $this->get('label')).".csv");
				header("Pragma: public");

				$fullCSV = array();

				$ff = array();
				foreach ($fields as $f) {
					if($f->get('option_exportview')){
						$ff[] = str_replace('"', '\"', $f->get('name'));
					}
				}
				if(!$front){
					if($this->get('option_displaydate')){
						$ff[] = str_replace('"', '\"', "Date de création");
					}
					if($this->get('option_displayip')){
						$ff[] = "IP";
					}
				}
				$fullCSV[] = '"'.implode('";"', $ff).'"';

				foreach($replies as $a){
					$Afields = $a->getFields();
					$ff = array();
					foreach($fields as $f){
						if($f->get('option_exportview')){
							$ff[] = isset($Afields[$f->get('id')])?str_replace('"', '\"', $Afields[$f->get('id')]->get('value')):"";
						}
					}
					if(!$front){
						if($this->get('option_displaydate')){
							$dd = dims_timestamp2local($a->get('date_validation'));
							$ff[] = $dd['date']." ".$dd['time'];
						}
						if($this->get('option_displayip')){
							$ff[] = $a->get('ip');
						}
					}
					$fullCSV[] = '"'.implode('";"', $ff).'"';
				}
				echo implode("\r\n", $fullCSV);
				break;
			case 'XSL':
				dims::getInstance()->debugmode=false;
				require_once 'Spreadsheet/Excel/Writer.php';
				$workbook = new Spreadsheet_Excel_Writer();
				$workbook->send("export_".str_replace(" ", "_", $this->get('label')).".xls");
				$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
				$format =& $workbook->addFormat( array( 'TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));
				$l = $c = 0;
				$worksheet =& $workbook->addWorksheet("export");

				foreach ($fields as $f) {
					if($f->get('option_exportview')){
						$worksheet->write($l, $c++, utf8_decode($f->get('name')), $format_title);
					}
				}
				if(!$front){
					if($this->get('option_displaydate')){
						$worksheet->write($l, $c++, utf8_decode("Date de création"), $format_title);
					}
					if($this->get('option_displayip')){
						$worksheet->write($l, $c++, utf8_decode("IP"), $format_title);
					}
				}

				foreach($replies as $a){
					$l++;
					$c = 0;
					$Afields = $a->getFields();
					$ff = array();
					foreach($fields as $f){
						if($f->get('option_exportview')){
							$v = isset($Afields[$f->get('id')])?str_replace('"', '\"', $Afields[$f->get('id')]->get('value')):"";
							$worksheet->write($l, $c++, utf8_decode($v), $format);
						}
					}
					if(!$front){
						if($this->get('option_displaydate')){
							$dd = dims_timestamp2local($a->get('date_validation'));
							$worksheet->write($l, $c++, utf8_decode($dd['date']." ".$dd['time']), $format);
						}
						if($this->get('option_displayip')){
							$worksheet->write($l, $c++, utf8_decode($a->get('ip')), $format);
						}
					}
				}
				$workbook->close();
				break;
		}
		die();
	}

	public function importFile($file,$name){
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','512M');

		$extension	= explode(".", $name);
		$extension	= strtolower($extension[count($extension)-1]);

		$liste_version = array(
			"csv"	=> "CSV",
			"xlsx"	=> "Excel2007",
			"xls"	=> "Excel5",
		);

		/** PHPExcel_IOFactory */
		require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

		//echo date('H:i:s') . " Load from Excel2007 file\n<br>";

		//on instancie un objet de lecture
		$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);
		//on charge le fichier qu'on veut lire
		$objPHPExcel = PHPExcel_IOFactory::load($file);

		$obj_all_sheets	= $objPHPExcel->getAllSheets();
		$nb_row			= $obj_all_sheets[0]->getHighestRow();			//Nombre de ligne
		$Column_max		= $obj_all_sheets[0]->getHighestColumn();//Nombre de cellule
		$nb_Column		= strlen($Column_max);

		if(strlen($Column_max) > 2) $Column_max = 'AZ';

		$alphabet[1]	= "A";
		$alphabet[]		= "B";
		$alphabet[]		= "C";
		$alphabet[]		= "D";
		$alphabet[]		= "E";
		$alphabet[]		= "F";
		$alphabet[]		= "G";
		$alphabet[]		= "H";
		$alphabet[]		= "I";
		$alphabet[]		= "J";
		$alphabet[]		= "K";
		$alphabet[]		= "L";
		$alphabet[]		= "M";
		$alphabet[]		= "N";
		$alphabet[]		= "O";
		$alphabet[]		= "P";
		$alphabet[]		= "Q";
		$alphabet[]		= "R";
		$alphabet[]		= "S";
		$alphabet[]		= "T";
		$alphabet[]		= "U";
		$alphabet[]		= "V";
		$alphabet[]		= "W";
		$alphabet[]		= "X";
		$alphabet[]		= "Y";
		$alphabet[]		= "Z";

		$import = array(
			'firstdataline' => 1,
			'nbrow' => $nb_row,
			'nbcol' => $nb_Column,
			'data' => array(),
		);

		//Boucle sur le nombre de ligne
		for ($i=1; $i <= $nb_row; $i++){
			//printf("%d %d <br>",$i,memory_get_usage());
			$c=0;
			$d=0;
			$fist_lettre = "";
			$lettre = "";
			while ($fist_lettre.$lettre != $Column_max){
				$c++;
				$lettre = $alphabet[$c];
				$import['data'][$i][$fist_lettre.$lettre] = $objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue();
				//Si l'on arrive sur la derniere colone l'on arret
				if ($alphabet[$c] == "Z") {
					$d++;
					$c = 0;
					$fist_lettre = $alphabet[$d];
				}

				if(!isset($import['formatcol'][$fist_lettre.$lettre])){
					$import['formatcol'][$fist_lettre.$lettre] = "string";
					$import['typecol'][$fist_lettre.$lettre] = "text";
					$import['titlecol'][$fist_lettre.$lettre] = $import['data'][$i][$fist_lettre.$lettre];
				}
			}
		}

		unset($obj_all_sheets);
		unset($objPHPExcel);

		return $import;
	}

	public function importData($import,$firstdataline){
		ini_set('max_execution_time',-1);
		ini_set('memory_limit','512M');

		$position=0;
		$correspfield=array();
		// on créé les champs
		foreach ($import['titlecol'] as $i=>$col) {
			$field = new field();
			$field->init_description();
			$field->fields['id_forms'] = $this->get('id');
			$field->fields['name'] = $col;
			$field->fields['type'] = $import['typecol'][$i];
			$field->fields['format'] = $import['formatcol'][$i];
			$field->fields['separator'] = 0;
			$field->fields['position'] = $position++;
			if (!isset($field_option_needed)) $field->fields['option_needed'] = 0;
			if (!isset($field_option_arrayview)) $field->fields['option_arrayview'] = 1;
			if (!isset($field_option_exportview)) $field->fields['option_exportview'] = 1;
			if (!isset($field_option_cmsgroupby)) $field->fields['option_cmsgroupby'] = 0;
			if (!isset($field_option_cmsorderby)) $field->fields['option_cmsorderby'] = 0;
			if (!isset($field_option_cmsdisplaylabel)) $field->fields['option_cmsdisplaylabel'] = 0;
			if (!isset($field_option_cmsshowfilter)) $field->fields['option_cmsshowfilter'] = 0;
			// on construit la liste de ce que l'on a trouve
			if ($field->fields['type']=="select") {
				$values=array();
				foreach ($import['data'] as $li=>$row) {
					if (!isset($values[$row[$i]]) && $firstdataline!=$i) {
						$values[$row[$i]]=$row[$i];
					}
				}
				$field->fields['values'] = implode('||',$values);
			}
			$field->save();
			$correspfield[$i]=$field->fields['id'];
			$this->fields['nb_fields']++;
		}

		$this->save();

		// on passe aux données
		// pour chaque ligne on créé un id_reply et on va construire la requete de données à faire pour les éléments
		foreach ($import['data'] as $li=>$row) {
			if ($firstdataline!=$li) {
				$reply = new reply();
				$reply->fields['date_validation'] = dims_createtimestamp();
				$reply->setugm();
				$reply->fields['id_forms'] = $this->get('id');
				$reply->fields['id_user'] = $_SESSION['dims']['userid'];
				$reply->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$reply->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$reply->fields['ip'] = $_SERVER['REMOTE_ADDR'];
				$reply->save();

				foreach ($row as $ci=>$col) {
					$reply_field = new reply_field();
					$reply_field->init_description();
					$reply_field->fields['id_field'] = $correspfield[$ci];
					$reply_field->fields['id_forms'] = $this->get('id');
					$reply_field->fields['id_reply'] = $reply->fields['id'];
					$reply_field->setugm();
					$reply_field->fields['value'] = $col;
					$reply_field->save();
				}
			}
		}
	}
}
