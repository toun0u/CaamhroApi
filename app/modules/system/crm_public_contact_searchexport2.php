<?
ob_end_clean();

function add_txt($add,$org,$br=true) {
	if($add!='') {
		$result = $add;
		if($br) {
			if($org!='') $result = $org.chr(10).$add;
		}
		else {
			if($org!='') $result = $org.' '.$add;
		}
		return $result;
	}
	else {
		return $org;
	}
}

if (isset($exported_contacts) && !empty($exported_contacts)) {
	//while($fields = $db->fetchrow($rs)) {
	foreach($exported_contacts as $contact){
		//on va chercher les champs métier dans les layers
		$sql_l = 	"SELECT 	*
					FROM 		dims_mod_business_contact_layer
					WHERE 		id = :id
					AND 		type_layer = 1
					AND 		id_layer = :idlayer ";
		$res_l = $db->query($sql_l, array(
			':id'		=> $contact->getId(),
			':idlayer'	=> $_SESSION['dims']['workspaceid']
		));
		if($db->numrows($res_l) > 0) {
			while($lay = $db->fetchrow($res_l)) {
				if($lay['civilite'] != '')		$contact->fields['civilite'] = $lay['civilite'];
				if($lay['address'] != '')		$contact->fields['address'] = $lay['address'];
				if($lay['postalcode'] != '')	$contact->fields['postalcode'] = $lay['postalcode'];
				if($lay['city'] != '')			$contact->fields['city'] = $lay['city'];
				if($lay['country'] != '')		$contact->fields['country'] = $lay['country'];
				if($lay['phone'] != '')			$contact->fields['phone'] = $lay['phone'];
				if($lay['phone2'] != '')		$contact->fields['phone2'] = $lay['phone2'];
				if($lay['fax'] != '')			$contact->fields['fax'] = $lay['fax'];
				if($lay['mobile'] != '')		$contact->fields['mobile'] = $lay['mobile'];
				if($lay['email'] != '')			$contact->fields['email'] = $lay['email'];
				if($lay['email2'] != '')		$contact->fields['email2'] = $lay['email2'];
				if($lay['email3'] != '')		$contact->fields['email3'] = $lay['email3'];
				if($lay['comments'] != '')		$contact->fields['comments'] = $lay['comments'];
			}
		}
	}

	//dims_print_r($_SESSION['business']['exportdata']); die();
	$dims->debugmode=false;

	//require_once DIMS_APP_PATH . '/lib/PHPExcel/PHPExcel.php';
	require_once 'Spreadsheet/Excel/Writer.php';

	// Creating a workbook
	$workbook = new Spreadsheet_Excel_Writer();

	// sending HTTP headers
	$workbook->send("excel.xls");

	$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
	$format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

	$l=0;

	$id_rub1= '';
	//creation du tableau
	$worksheet =& $workbook->addWorksheet("Liste");
	if (isset($_SESSION['business']['exportdata'])) {
		$i=0;
		foreach($_SESSION['business']['exportdata'] as $d) {
			$worksheet->setColumn($i, $i, 30); //voir peut etre pour agrandir en fonction des champs
			$i++;
		}
	}

	if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
		$worksheet->setColumn($i, $i++, 20);
		$worksheet->setColumn($i, $i++, 15);
		$worksheet->setColumn($i, $i++, 15);
	}

	//entetes de colonnes
	if (isset($_SESSION['business']['exportdata'])) {
		$i=0;
		foreach($_SESSION['business']['exportdata'] as $f) {
			$worksheet->writeString($l, $i++, utf8_decode(($f['title'])), $format_title);
		}
	}

	if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
		$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_ENT_NAME'], $format_title);
		$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'], $format_title);
		$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_FUNCTION'], $format_title);
	}

	//$ancien_id = 0;
	foreach($exported_contacts as $contact) {
		//while ($fields = $db->fetchrow($rs)) {
		$l++;
		if (isset($_SESSION['business']['exportdata'])) {
			$i=0;
			foreach($_SESSION['business']['exportdata'] as $f) {
				//ajout de utf8_decode pour export excel
				$worksheet->writeString($l, $i++, utf8_decode($contact->fields[$f['namefield']]), $format);
			}
		}

		if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
			$worksheet->writeString($l, $i++, utf8_decode($contact->fields['intitule']), $format);
			$worksheet->writeString($l, $i++, utf8_decode($contact->fields['type_lien']), $format);
			$worksheet->writeString($l, $i++, utf8_decode($contact->fields['function']), $format);
		}
	}
	$workbook->close();
}
while (@ob_end_flush());
die();
?>
