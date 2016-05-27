<?php
if (!empty($lstContacts)) {
	$dims->debugmode = false;

	// construction de la requete d'export
	$sql =	"
		SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
								mb.protected,mb.name as namefield,mb.label as titlefield
		FROM		dims_mod_business_meta_field as mf
		INNER JOIN	dims_mb_field as mb
		ON			mb.id=mf.id_mbfield
		RIGHT JOIN	dims_mod_business_meta_categ as mc
		ON		mf.id_metacateg=mc.id
		WHERE		  mf.id_object = :idsobject
		AND			mf.used=1
		AND			mf.option_exportview=1
		ORDER BY	mc.position, mf.position";
	$rs_fields=$db->query($sql, array(
		':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
	));
	$_SESSION['business']['exportdata']=array();
	while ($fields = $db->fetchrow($rs_fields)) {
		if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
		else $namevalue=$fields['name'];
		$elem=array();
		$elem['title']=$namevalue;
		$elem['namefield']=$fields['namefield'];

		$_SESSION['business']['exportdata'][]=$elem;
	}

	require_once 'Spreadsheet/Excel/Writer.php';

	// Creating a workbook
	$workbook = new Spreadsheet_Excel_Writer();

	// sending HTTP headers
	$workbook->send('contacts.xls');

	// Creating a worksheet
	$worksheet =& $workbook->addWorksheet('Contacts');

	$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
	$format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

	// entetes
	$line = 0;
	if (isset($_SESSION['business']['exportdata'])) {
		foreach ($_SESSION['business']['exportdata'] as $col => $field) {
			$worksheet->setColumn($col, $col, 30); //voir peut etre pour agrandir en fonction des champs
			$worksheet->writeString($line, $col, utf8_decode($field['title']), $format_title);
		}
	}

	// donnees
	foreach ($lstContacts as $contact) {
		$line++;
		foreach ($_SESSION['business']['exportdata'] as $col => $field) {
			$worksheet->writeString($line, $col, utf8_decode($contact->fields[$field['namefield']]), $format);
		}
	}

	// Let's send the file
	$workbook->close();
}
