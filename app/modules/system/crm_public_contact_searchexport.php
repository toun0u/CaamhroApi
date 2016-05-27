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
//dims_print_r($_SESSION['business']['exportdata']);
if (isset($_SESSION['business']['search_ct_sql'])) {
	$rs = $db->query($_SESSION['business']['search_ct_sql']);

	while($fields = $db->fetchrow($rs)) {
		$tab_ct[$fields['id_ct']] = $fields;

		//on va chercher les champs métier dans les layers
		$sql_l = 	"SELECT 	*
					FROM 		dims_mod_business_contact_layer
					WHERE 		id = :id
					AND 		type_layer = 1
					AND 		id_layer = :idlayer ";
		$res_l = $db->query($sql_l, array(
			':id'		=> $fields['id_ct'],
			':idlayer'	=> $_SESSION['dims']['workspaceid']
		));
		if($db->numrows($res_l) > 0) {
			while($lay = $db->fetchrow($res_l)) {
				if($lay['civilite'] != '') $tab_ct[$fields['id_ct']]['civilite'] = $lay['civilite'];
				if($lay['address'] != '') $tab_ct[$fields['id_ct']]['address'] = $lay['address'];
				if($lay['postalcode'] != '') $tab_ct[$fields['id_ct']]['postalcode'] = $lay['postalcode'];
				if($lay['city'] != '') $tab_ct[$fields['id_ct']]['city'] = $lay['city'];
				if($lay['country'] != '') $tab_ct[$fields['id_ct']]['country'] = $lay['country'];
				if($lay['phone'] != '') $tab_ct[$fields['id_ct']]['phone'] = $lay['phone'];
				if($lay['phone2'] != '') $tab_ct[$fields['id_ct']]['phone2'] = $lay['phone2'];
				if($lay['fax'] != '') $tab_ct[$fields['id_ct']]['fax'] = $lay['fax'];
				if($lay['mobile'] != '') $tab_ct[$fields['id_ct']]['mobile'] = $lay['mobile'];
				if($lay['email'] != '') $tab_ct[$fields['id_ct']]['email'] = $lay['email'];
				if($lay['email2'] != '') $tab_ct[$fields['id_ct']]['email2'] = $lay['email2'];
				if($lay['email3'] != '') $tab_ct[$fields['id_ct']]['email3'] = $lay['email3'];
				if($lay['comments'] != '') $tab_ct[$fields['id_ct']]['comments'] = $lay['comments'];
			}
		}
	}

	//dims_print_r($_SESSION['business']['exportdata']); die();
	$dims->debugmode=false;

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

	/*
	$worksheet->setColumn(0, 0, 30);
	$worksheet->setColumn(1, 1, 30);
	$worksheet->setColumn(2, 2, 15);
	$worksheet->setColumn(3, 3, 30);
	$worksheet->setColumn(4, 4, 40);
	$worksheet->setColumn(5, 5, 5);
	$worksheet->setColumn(6, 6, 20);
	$worksheet->setColumn(7, 7, 20);*/
	if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
		$worksheet->setColumn($i, $i++, 20);
		$worksheet->setColumn($i, $i++, 15);
		$worksheet->setColumn($i, $i++, 15);
	}

	//entetes de colonnes
	if (isset($_SESSION['business']['exportdata'])) {
		$i=0;
		foreach($_SESSION['business']['exportdata'] as $f) {
			$worksheet->writeString($l, $i++, utf8_decode(html_entity_decode($f['title'])), $format_title);
		}
	}
	/*
	$worksheet->writeString($l, 0, $_DIMS['cste']['_DIMS_LABEL_NAME'], $format_title);
	$worksheet->writeString($l, 1, $_DIMS['cste']['_FIRSTNAME'], $format_title);
	$worksheet->writeString($l, 2, $_DIMS['cste']['_PHONE_WORK'], $format_title);
	$worksheet->writeString($l, 3, $_DIMS['cste']['_DIMS_LABEL_EMAIL'], $format_title);
	$worksheet->writeString($l, 4, $_DIMS['cste']['_DIMS_LABEL_ADDRESS'], $format_title);
	$worksheet->writeString($l, 5, $_DIMS['cste']['_DIMS_LABEL_CP'], $format_title);
	$worksheet->writeString($l, 6, $_DIMS['cste']['_DIMS_LABEL_CITY'], $format_title);
	$worksheet->writeString($l, 7, $_DIMS['cste']['_DIMS_LABEL_COUNTRY'], $format_title);
	 */
	if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
		$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_ENT_NAME'], $format_title);
		$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'], $format_title);
		$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_FUNCTION'], $format_title);
	}

	//$ancien_id = 0;
	foreach($tab_ct as $id_ct => $fields) {
	//while ($fields = $db->fetchrow($rs)) {
		$l++;
		if (isset($_SESSION['business']['exportdata'])) {
			$i=0;
			foreach($_SESSION['business']['exportdata'] as $f) {
                            //   ajout de utf8_decode pour export excel
                            $worksheet->writeString($l, $i++, utf8_decode($fields[$f['namefield']]), $format);
			}
		}
		/*
		$worksheet->writeString($l, 0, strtoupper($fields['lastname']), $format);
		$worksheet->writeString($l, 1, strtoupper($fields['firstname']), $format);
		$worksheet->writeString($l, 2, ucwords(strtolower($fields['phone'])), $format);
		$worksheet->writeString($l, 3, $fields['email'], $format);
		$worksheet->writeString($l, 4, $fields['address'], $format);
		$worksheet->writeString($l, 5, $fields['postalcode'], $format);
		$worksheet->writeString($l, 6, $fields['city'], $format);
		$worksheet->writeString($l, 7, $_DIMS['cste'][$fields['country']], $format);
		 */
		if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
			$worksheet->writeString($l, $i++, utf8_decode($fields['intitule']), $format);
			$worksheet->writeString($l, $i++, utf8_decode($fields['type_lien']), $format);
			$worksheet->writeString($l, $i++, utf8_decode($fields['function']), $format);
		}
	}
	$workbook->close();
}
while (@ob_end_flush());
die();
?>
