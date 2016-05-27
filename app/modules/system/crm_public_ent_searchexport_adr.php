<?php
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

if (isset($_SESSION['business']['search_ent_sql']))
{
	//dims_print_r($_SESSION['business']['search_ct_sql']); die();
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
	$worksheet->setColumn(0, 0, 40);
	$worksheet->setColumn(1, 1, 50);
	$worksheet->setColumn(2, 2, 15);
	$worksheet->setColumn(3, 3, 25);
	$worksheet->setColumn(4, 4, 25);


	//entetes de colonnes
	$worksheet->writeString($l, 0, $_DIMS['cste']['_DIMS_LABEL_ENT_NAME'], $format_title);
	$worksheet->writeString($l, 1, $_DIMS['cste']['_DIMS_LABEL_ADDRESS'], $format_title);
	$worksheet->writeString($l, 2, $_DIMS['cste']['_DIMS_LABEL_CP'], $format_title);
	$worksheet->writeString($l, 3, $_DIMS['cste']['_DIMS_LABEL_CITY'], $format_title);
	$worksheet->writeString($l, 4, $_DIMS['cste']['_DIMS_LABEL_COUNTRY'], $format_title);

	//$ancien_id = 0;
	while ($fields = $db->fetchrow($rs)) {
		$l++;
		$worksheet->writeString($l, 0, strtoupper($fields['intitule']), $format);
		$worksheet->writeString($l, 1, $fields['adresse'], $format);
		$worksheet->writeString($l, 2, $fields['codepostal'], $format);
		$worksheet->writeString($l, 3, $fields['ville'], $format);
		$worksheet->writeString($l, 4, $_DIMS['cste'][$fields['country']], $format);
	}
	$workbook->close();
}
while (@ob_end_flush());
die();
?>