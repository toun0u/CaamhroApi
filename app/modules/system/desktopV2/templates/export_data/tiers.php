<?
ob_end_clean();

if (isset($exported_tiers) && !empty($exported_tiers)) {
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

	$lstTitles = array(
		$_SESSION['cste']['_BUSINESS_FIELD_NAME'],
		$_SESSION['cste']['_DIMS_LABEL_EMAIL'],
		$_SESSION['cste']['_DIRECTORY_PHONE'],
		$_SESSION['cste']['_DIMS_LABEL_FAX'],
		$_SESSION['cste']['_DIMS_LABEL_ADDRESS'],
		$_SESSION['cste']['_DIMS_LABEL_ADDRESS']." 2",
		$_SESSION['cste']['_DIMS_LABEL_ADDRESS']." 3",
		$_SESSION['cste']['_DIMS_LABEL_CP'],
		$_SESSION['cste']['_DIMS_LABEL_CITY'],
		"BP",
		$_SESSION['cste']['_DIMS_LABEL_COUNTRY'],
	);
	$i=0;
	foreach($lstTitles as $d) {
		$worksheet->setColumn($i, $i, 30); //voir peut etre pour agrandir en fonction des champs
		$i++;
	}

	//entetes de colonnes
	$i=0;
	foreach($lstTitles as $f) {
		$worksheet->writeString($l, $i++, utf8_decode($f), $format_title);
	}

	require_once DIMS_APP_PATH.'modules/system/class_address.php';
	foreach($exported_tiers as $contact) {
		$i=0;
		$l++;
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['intitule']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['mel']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['telephone']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['telecopie']), $format);
		// Adresse liée
		$addresses = address::getAddressesFromGo($contact->get('id_globalobject'));
		if(count($addresses)){
			$add = current($addresses);
			$worksheet->writeString($l, $i++, utf8_decode($add->get('address')), $format);
			$worksheet->writeString($l, $i++, utf8_decode($add->get('address2')), $format);
			$worksheet->writeString($l, $i++, utf8_decode($add->get('address3')), $format);
			$worksheet->writeString($l, $i++, utf8_decode($add->get('postalcode')), $format);
			$city = $add->getCity();
			if(!empty($city) && !$city->isnew())
				$worksheet->writeString($l, $i++, utf8_decode($city->get('label')), $format);
			else
				$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, utf8_decode($add->get('bp')), $format);
			$country = $add->getCountry();
			$worksheet->writeString($l, $i++, utf8_decode($country->get('printable_name')), $format);
		}else{
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
		}
	}
	$workbook->close();
}
while (@ob_end_flush());
die();
