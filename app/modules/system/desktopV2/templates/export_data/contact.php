<?
ob_end_clean();

if (isset($exported_contacts) && !empty($exported_contacts)) {
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
		$_SESSION['cste']['_REGISTRATION_NO'],
		$_SESSION['cste']['_CIVILITY'],
		$_SESSION['cste']['_DIMS_LABEL_TITLE'],
		$_SESSION['cste']['_DIMS_LABEL_FIRSTNAME'],
		$_SESSION['cste']['_DIMS_LABEL_NAME'],
		ucfirst($_SESSION['cste']['COMPANY_MINUSCULE']),
		$_SESSION['cste']['_DIMS_LABEL_EMAIL'],
		$_SESSION['cste']['_DIMS_LABEL_EMAIL2'],
		$_SESSION['cste']['_DIRECTORY_PHONE'],
		$_SESSION['cste']['_DIMS_LABEL_FAX'],
		$_SESSION['cste']['_DIMS_LABEL_ADDRESS'],
		$_SESSION['cste']['_DIMS_LABEL_ADDRESS']." 2",
		$_SESSION['cste']['_DIMS_LABEL_ADDRESS']." 3",
		$_SESSION['cste']['_DIMS_LABEL_CP'],
		$_SESSION['cste']['_DIMS_LABEL_CITY'],
		"BP",
		$_SESSION['cste']['_DIMS_LABEL_COUNTRY'],
		$_SESSION['cste']['_DIMS_LABEL_EMAIL'],
		$_SESSION['cste']['_DIRECTORY_PHONE'],
		$_SESSION['cste']['_DIMS_LABEL_FAX'],
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
	foreach($exported_contacts as $contact) {
		$i=0;
		$l++;
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['num_enregistrement']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['civilite']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['title']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['firstname']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['lastname']), $format);
		// Tiers lié
		$employers = $contact->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR');
		if(count($employers)){
			$tiers = current($employers);
			$worksheet->writeString($l, $i++, utf8_decode($tiers['intitule']), $format);
		}else{
			$worksheet->writeString($l, $i++, '', $format);
		}
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['email']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['email2']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['mobile']), $format);
		$worksheet->writeString($l, $i++, utf8_decode($contact->fields['fax']), $format);
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
			$worksheet->writeString($l, $i++, utf8_decode($add->getLightAttribute('email')), $format);
			$worksheet->writeString($l, $i++, utf8_decode($add->getLightAttribute('phone')), $format);
			$worksheet->writeString($l, $i++, utf8_decode($add->getLightAttribute('fax')), $format);
		}else{
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
			$worksheet->writeString($l, $i++, '', $format);
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
