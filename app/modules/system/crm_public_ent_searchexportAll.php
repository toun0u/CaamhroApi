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
			$worksheet->setColumn($i, $i, 20); //voir peut etre pour agrandir en fonction des champs
			$i++;
		}
	}
	/*
	if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
		$worksheet->setColumn($i, $i++, 30);
		$worksheet->setColumn($i, $i++, 30);
		$worksheet->setColumn($i, $i++, 30);
		$worksheet->setColumn($i, $i++, 15);
	}*/

	//entetes de colonnes
	if (isset($_SESSION['business']['exportdata'])) {
		$i=0;
		foreach($_SESSION['business']['exportdata'] as $f) {
			$worksheet->writeString($l, $i++, html_entity_decode(utf8_decode($f['title'])), $format_title);
		}
	}
	//while ($fields = $db->fetchrow($rs)) {
	foreach($exported_tiers as $fields){
		$l++;
		if (isset($_SESSION['business']['exportdata'])) {
			$i=0;
			$debdelem=array();

			foreach ( $fields as $key => $value) {
				if (is_array($value)) {
					$icour=$i;
					$nbelems=sizeof($value);
					$ctcour=1;
					//echo $nbelems;die();
					foreach ($value as $ct) {
						foreach ($ct as $elemct) {
							$worksheet->writeString($l, $i++, utf8_decode($elemct), $format);
						}

						// on a plusieurs donc on refait une ligne
						if ($nbelems>1 && $ctcour<$nbelems) {
							// on passe une ligne on réécrit les premières
							$l++;
							$i=0; // on remet à l'indice

							foreach ($debdelem as $value) {
								$worksheet->writeString($l, $i++, utf8_decode($value), $format);
							}
						}
						$ctcour++;
					}
				}
				else {
					$worksheet->writeString($l, $i++, utf8_decode($value), $format);
					$debdelem[]=$value;
				}
			}
			//foreach($_SESSION['business']['exportdata'] as $f) {
			//	$worksheet->writeString($l, $i++, strtolower(utf8_decode($fields[$f['namefield']])), $format);
			//}
		}

	}
	$workbook->close();

}
while (@ob_end_flush());
die();
?>
