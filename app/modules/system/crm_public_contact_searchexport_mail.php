<?php
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

				if($lay['email'] != '') $tab_ct[$fields['id_ct']]['email'] = $lay['email'];
				if($lay['email2'] != '') $tab_ct[$fields['id_ct']]['email2'] = $lay['email2'];
				if($lay['email3'] != '') $tab_ct[$fields['id_ct']]['email3'] = $lay['email3'];

			}
		}
	}

	$dims->debugmode=false;

	require_once 'Spreadsheet/Excel/Writer.php';
	// Creating a workbook
	$workbook = new Spreadsheet_Excel_Writer();
	// sending HTTP headers
	$workbook->send("excel.xls");
	$workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'),$format_title);
	$workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10),$format);

	$l=0;

	$id_rub1= '';
	//creation du tableau
	$worksheet='';
	$worksheet=$workbook->addWorksheet("Liste");

	$worksheet->setColumn(0, 0, 30);
	$worksheet->setColumn(1, 1, 30);
	$worksheet->setColumn(2, 2, 30);

	//entetes de colonnes
	$worksheet->writeString($l, 0, $_DIMS['cste']['_DIMS_LABEL_NAME'], $format_title);
	$worksheet->writeString($l, 1, $_DIMS['cste']['_FIRSTNAME'], $format_title);
	$worksheet->writeString($l, 2, $_DIMS['cste']['_DIMS_LABEL_EMAIL'], $format_title);

	//$ancien_id = 0;
	$checkmail=array();

	foreach($tab_ct as $id_ct => $fields) {
		if (!isset($checkmail[$fields['email']])) {
			$checkmail[$fields['email']]=$fields['email'];
			$l++;
			$worksheet->writeString($l, 0, strtoupper($fields['lastname']), $format);
			$worksheet->writeString($l, 1, strtoupper($fields['firstname']), $format);
			$worksheet->writeString($l, 2, ucwords(strtolower($fields['email'])), $format);
		}
	}

	$workbook->close();
	die();
}
?>
