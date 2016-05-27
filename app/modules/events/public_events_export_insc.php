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

if (isset($_SESSION['business']['event_export_insc'])) {
	//dims_print_r($_SESSION['business']['search_ct_sql']); die();
	//$rs = $db->query($_SESSION['business']['event_export_insc']);

	$dims->debugmode=false;

	require_once 'Spreadsheet/Excel/Writer.php';

	// Creating a workbook
	$workbook = new Spreadsheet_Excel_Writer();

	// sending HTTP headers
	$workbook->send("excel.xls");

	$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
	$format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

	$l=0;

	//creation du tableau
	$worksheet =& $workbook->addWorksheet("Liste");
	$worksheet->setColumn(0, 0, 50);
	$worksheet->setColumn(1, 1, 50);
	$worksheet->setColumn(2, 2, 50);
	$worksheet->setColumn(3, 3, 50);
	$worksheet->setColumn(4, 4, 50);
	$worksheet->setColumn(5, 5, 50);
	$worksheet->setColumn(6, 6, 50);
	$worksheet->setColumn(7, 7, 50);
	$worksheet->setColumn(8, 8, 50);
	$worksheet->setColumn(9, 9, 50);

	//entetes de colonnes
	$worksheet->writeString($l, 0, $_DIMS['cste']['_DIMS_LABEL_NAME'], $format_title);
	$worksheet->writeString($l, 1, $_DIMS['cste']['_FIRSTNAME'], $format_title);
	$worksheet->writeString($l, 2, $_DIMS['cste']['_DIMS_LABEL_ADDRESS'], $format_title);
	$worksheet->writeString($l, 3, $_DIMS['cste']['_DIMS_LABEL_CP'], $format_title);
	$worksheet->writeString($l, 4, $_DIMS['cste']['_DIMS_LABEL_CITY'], $format_title);
	$worksheet->writeString($l, 5, $_DIMS['cste']['_DIMS_LABEL_COUNTRY'], $format_title);
	$worksheet->writeString($l, 6, html_entity_decode($_DIMS['cste']['_PHONE']), $format_title);
	$worksheet->writeString($l, 7, $_DIMS['cste']['_DIMS_LABEL_EMAIL'], $format_title);
	$worksheet->writeString($l, 8, $_DIMS['cste']['_DIMS_LABEL_COMPANY'], $format_title);
	$worksheet->writeString($l, 9, 'State', $format_title);

	//$ancien_id = 0;
	foreach($_SESSION['business']['exportdata'] as $id_insc => $fields) {
		$l++;
		$worksheet->writeString($l, 0, strtoupper($fields['lastname']), $format);
		$worksheet->writeString($l, 1, strtoupper($fields['firstname']), $format);
		$worksheet->writeString($l, 2, $fields['address'], $format);
		$worksheet->writeString($l, 3, $fields['postalcode'], $format);
		$worksheet->writeString($l, 4, $fields['city'], $format);
		$worksheet->writeString($l, 5, $fields['country'], $format);
		$worksheet->writeString($l, 6, ucwords(strtolower($fields['phone'])), $format);
		$worksheet->writeString($l, 7, $fields['email'], $format);
		$worksheet->writeString($l, 8, $fields['company'], $format);
		$worksheet->writeString($l, 9, $fields['validate'], $format);
	}
	$workbook->close();
}
while (@ob_end_flush());
die();
?>
