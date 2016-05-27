<?
ini_set('memory_limit','512M');
$id_mail_f = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
$nb_imp=0;

if(isset($_FILES['doc_mail']['name']) && $_FILES['doc_mail']['name'] != '' && $id_mail_f != '')
{
	$handle = fopen ($_FILES['doc_mail']['tmp_name'], "r");

	//on test l'extension
	$extension	= explode(".", $_FILES['doc_mail']['name']);
	$extension	= $extension[count($extension)-1];
	$extension  = strtolower($extension);

	// on charge la liste existante
	$sql = "select distinct email from dims_mod_newsletter_mailing_ct where id_mailing=:id_mailing";
        $params = array();
        $params[':id_mailing'] = array('type' => PDO::PARAM_INT, 'value' => $id_mail_f);
	$existings=array();

	$res=$db->query($sql);

	while ($f=$db->fetchrow($res)) {
	$existings[$f['email']]=$f['email'];
	}


	if($extension == 'txt' || $extension == 'csv') {
		$nb_imp = 0;
	$arrays=  explode("\r",  file_get_contents($_FILES['doc_mail']['tmp_name']));

	foreach ($arrays as $fields) {
        //while ($line = fgets($handle, 4096)) {
		$fields = strtolower(trim($fields)); //analyzeEmailsExpression($line);
		//dims_print_r($fields);die();
            if(!empty($fields) && !isset($existings[$fields])) {
				$mail_ct = new mailing_ct();
				$mail_ct->init_description();
				$mail_ct->fields['id_mailing'] = $id_mail_f;
				$mail_ct->fields['date_creation'] = date("YmdHis");
				$mail_ct->fields['actif'] = 1;
                $mail_ct->fields['email'] = $fields;
				$mail_ct->save();

				$nb_imp++;
		// on ajoute le nouvel email
		$existings[$fields]=$fields;
			}
		}
		fclose ($handle);
	}
	elseif($extension == 'xls' || $extension == 'xlsx') {

		$_SESSION['dims']['import_current_mailing'] = array();
		$created = array();
		$errors = array();
		$_FIELDS = array();

		$file	= $_FILES['doc_mail']['tmp_name'];

		/** PHPExcel_IOFactory */
		require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

		$liste_version["csv"]	= "CSV";
		$liste_version["xlsx"]	= "Excel2007";
		$liste_version["xls"]	= "Excel5";

		//on instancie un objet de lecture
		$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);
		//on charge le fichier qu'on veut lire
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		//printf(" %d <br>",memory_get_usage());
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

		$obj_all_sheets	= $objPHPExcel->getAllSheets();
		$nb_row			= $obj_all_sheets[0]->getHighestRow();			//Nombre de ligne
		$Column_max		= $obj_all_sheets[0]->getHighestColumn();//Nombre de cellule
		$nb_Column		= strlen($Column_max);
		//echo "Column max : ".$Column_max."<br/>";

		if ($nb_Column > 1) {
			$last_ocurence	= substr($Column_max, -1);
		}else{
			$last_ocurence	= $Column_max;
		}

		$fist_lettre	= "";

		//L'on parce l'alphabet
		$c=0;
		$d=0;
		$fist_lettre = "";
		$lettre = "";
		while ($fist_lettre.$lettre != $Column_max){
			$c++;
			$lettre = $alphabet[$c];
			$content[$fist_lettre.$lettre] = $objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre."1")->getValue();
			$content[$fist_lettre.$lettre] = strtolower(trim(str_replace('"','',$content[$fist_lettre.$lettre])));
			//Si l'on arrive sur la derniere colone l'on arret
			if ($alphabet[$c] == "Z") {
				$d++;
				$c = 0;
				$fist_lettre = $alphabet[$d];
			}
		}

		foreach($content AS $key => $value){
			//echo $value."<br/>";

			//On vérifie si on connait la clé
			$val = dims_convertaccents($value);

			switch($val){

				case "email":
				case "email address":
				case "e-mail address":
				case "courriel":
				case "emailaddress":
				case "mail":
				case "adressedemessagerie":
					$value = "email";
				break;

			}
			$_FIELDS[$key] = $value;
		}

		//Boucle sur le nombre de ligne
		for ($i=2; $i <= $nb_row; $i++){
			//printf("%d %d <br>",$i,memory_get_usage());
			$c=0;
			$d=0;
			$fist_lettre = "";
			$lettre = "";
			while ($fist_lettre.$lettre != $Column_max){
				$c++;
				$lettre = $alphabet[$c];
				//$fields_analyse = analyzeEmailsExpression($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue());
				$fields_analyse = trim($objPHPExcel->getActiveSheet()->getCell($fist_lettre.$lettre.$i)->getValue());
				$email=(strpos($fields_analyse,"@")>0);

				if(!empty($fields_analyse) && $email) {
					//$_SESSION['dims']['import_current_mailing'][$i][$_FIELDS[$fist_lettre.$lettre]] = $fields_analyse;
					$mail_ct = new mailing_ct();
					$mail_ct->init_description();
					$mail_ct->fields['id_mailing'] = $id_mail_f;
					$mail_ct->fields['date_creation'] = date("YmdHis");
					$mail_ct->fields['actif'] = 1;
					$mail_ct->fields['email'] = ($fields_analyse);
					$mail_ct->save();

					$nb_imp++;
				}

				//Si l'on arrive sur la derniere colone l'on arret
				if ($alphabet[$c] == "Z") {
					$d++;
					$c = 0;
					$fist_lettre = $alphabet[$d];
				}
			}
		}

//					  print "</pre>";
		unset($obj_all_sheets);
		unset($objPHPExcel);
	}
	else {
		$error_imp = $_DIMS['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'];
	}
}
else {
	$error_imp = $_DIMS['cste']['_DOC_LABEL_FILESNOFOUND'];
}
?>
