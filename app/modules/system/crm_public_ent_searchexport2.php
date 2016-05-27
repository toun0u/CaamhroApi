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
	//while ($fields = $db->fetchrow($rs)) {
	foreach($exported_tiers as $tiers){
		$l++;
		if (isset($_SESSION['business']['exportdata'])) {
			$i=0;
			foreach($_SESSION['business']['exportdata'] as $f) {
				$worksheet->writeString($l, $i++, strtolower(utf8_decode($tiers->fields[$f['namefield']])), $format);
			}
		}
		//entetes de colonnes
		if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
			$worksheet->writeString($l, $i++, strtoupper(utf8_decode($tiers->fields['lastname'])), $format);
			$worksheet->writeString($l, $i++, strtoupper(utf8_decode($tiers->fields['firstname'])), $format);
			$worksheet->writeString($l, $i++, utf8_decode($tiers->fields['email']), $format);
			$worksheet->writeString($l, $i++, utf8_decode($tiers->fields['type_lien']), $format);
		}
	}
	$workbook->close();

}
while (@ob_end_flush());
die();
?>
