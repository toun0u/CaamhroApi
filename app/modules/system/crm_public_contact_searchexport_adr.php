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

if (isset($_SESSION['business']['search_ct_sql']))
{
	//dims_print_r($_SESSION['business']['search_ct_sql']); die();
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
			}
		}
	}

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
	$worksheet->setColumn(0, 0, 30);
	$worksheet->setColumn(1, 1, 30);
	$worksheet->setColumn(2, 2, 40);
	$worksheet->setColumn(3, 3, 15);
	$worksheet->setColumn(4, 4, 25);
	$worksheet->setColumn(5, 5, 25);


	//entetes de colonnes
	$worksheet->writeString($l, 0, $_DIMS['cste']['_DIMS_LABEL_NAME'], $format_title);
	$worksheet->writeString($l, 1, $_DIMS['cste']['_FIRSTNAME'], $format_title);
	$worksheet->writeString($l, 2, $_DIMS['cste']['_DIMS_LABEL_ADDRESS'], $format_title);
	$worksheet->writeString($l, 3, $_DIMS['cste']['_DIMS_LABEL_CP'], $format_title);
	$worksheet->writeString($l, 4, $_DIMS['cste']['_DIMS_LABEL_CITY'], $format_title);
	$worksheet->writeString($l, 5, $_DIMS['cste']['_DIMS_LABEL_COUNTRY'], $format_title);

	//$ancien_id = 0;
	foreach($tab_ct as $id_ct => $fields) {
		$l++;
		$worksheet->writeString($l, 0, strtoupper($fields['lastname']), $format);					/* civilite */
		$worksheet->writeString($l, 1, strtoupper($fields['firstname']), $format);
		$worksheet->writeString($l, 2, $fields['address'], $format);
		$worksheet->writeString($l, 3, $fields['postalcode'], $format);
		$worksheet->writeString($l, 4, $fields['city'], $format);
		$worksheet->writeString($l, 5, $_DIMS['cste'][$fields['country']], $format);
	}
	$workbook->close();
}
while (@ob_end_flush());
die();
?>
