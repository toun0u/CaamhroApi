<?
ob_end_clean();

function add_txt($add,$org,$br=true)
{
	if($add!='')
	{
		$result = $add;
		if($br)
		{
			if($org!='') $result = $org.chr(10).$add;
		}
		else
		{
			if($org!='') $result = $org.' '.$add;
		}
		return $result;
	}
	else
	{
		return $org;
	}
}

if (isset($_SESSION['business']['search_ent_sql'])) {

	//dims_print_r($_SESSION['business']['search_ent_sql']); die();
	$rs = $db->query($_SESSION['business']['search_ent_sql']);
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

	if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
		$worksheet->setColumn($i, $i++, 30);
		$worksheet->setColumn($i, $i++, 30);
		$worksheet->setColumn($i, $i++, 30);
		$worksheet->setColumn($i, $i++, 15);
	}

	//entetes de colonnes
	if (isset($_SESSION['business']['exportdata'])) {
		$i=0;
		foreach($_SESSION['business']['exportdata'] as $f) {
			$worksheet->writeString($l, $i++, html_entity_decode($f['title']), $format_title);
		}
	}
	while ($fields = $db->fetchrow($rs)) {
		$l++;
		if (isset($_SESSION['business']['exportdata'])) {
			$i=0;
			foreach($_SESSION['business']['exportdata'] as $f) {
				$worksheet->writeString($l, $i++, strtolower($fields[$f['namefield']]), $format);
			}
		}
	//entetes de colonnes
	/*
	$worksheet->writeString($l, 0, $_DIMS['cste']['_DIMS_LABEL_ENT_NAME'], $format_title);
	$worksheet->writeString($l, 1, $_DIMS['cste']['_PHONE_WORK'], $format_title);
	$worksheet->writeString($l, 2, $_DIMS['cste']['_DIMS_LABEL_ADDRESS'], $format_title);
	$worksheet->writeString($l, 3, $_DIMS['cste']['_DIMS_LABEL_CP'], $format_title);
	$worksheet->writeString($l, 4, $_DIMS['cste']['_DIMS_LABEL_CITY'], $format_title);
	$worksheet->writeString($l, 5, $_DIMS['cste']['_DIMS_LABEL_COUNTRY'], $format_title);
	$worksheet->writeString($l, 6, html_entity_decode($_DIMS['cste']['_DIMS_LABEL_ENT_SECTACT']), $format_title);
	$worksheet->writeString($l, 7, html_entity_decode($_DIMS['cste']['_DIMS_PRESENTATION']), $format_title);
	if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
		$worksheet->writeString($l, 8, $_DIMS['cste']['_DIMS_LABEL_NAME'], $format_title);
		$worksheet->writeString($l, 9, $_DIMS['cste']['_FIRSTNAME'], $format_title);
		$worksheet->writeString($l, 10, $_DIMS['cste']['_DIMS_LABEL_EMAIL'], $format_title);
		$worksheet->writeString($l, 11, $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'], $format_title);
	}

	//$ancien_id = 0;
	while ($fields = $db->fetchrow($rs)) {
		$l++;
		$worksheet->writeString($l, 0, $fields['intitule'], $format);
		$worksheet->writeString($l, 1, ucwords(strtolower($fields['telephone'])), $format);
		$worksheet->writeString($l, 2, $fields['adresse'], $format);
		$worksheet->writeString($l, 3, $fields['codepostal'], $format);
		$worksheet->writeString($l, 4, $fields['ville'], $format);
		$worksheet->writeString($l, 5, $_DIMS['cste'][$fields['pays']], $format);
		$worksheet->writeString($l, 6, $_DIMS['cste'][$fields['ent_activiteprincipale']], $format);
		$worksheet->writeString($l, 7, $fields['presentation'], $format);*/
		if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
			$worksheet->writeString($l, $i++, strtoupper($fields['lastname']), $format);
			$worksheet->writeString($l, $i++, strtoupper($fields['firstname']), $format);
			$worksheet->writeString($l, $i++, $fields['email'], $format);
			$worksheet->writeString($l, $i++, $fields['type_lien'], $format);
		}
	}
	$workbook->close();

}
while (@ob_end_flush());
die();
?>