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
//dims_print_r($_SESSION['business']['exportdata']);
if (isset($_SESSION['business']['search_ct_sql'])) {
	$rs = $db->query($_SESSION['business']['search_ct_sql']);

	while($fields = $db->fetchrow($rs)) {
		$tab_ct[$fields['id_ct']] = $fields;

		//on va chercher les champs métier dans les layers
		$sql_l =	"SELECT		*
					FROM		dims_mod_business_contact_layer
					WHERE		id = :id
					AND			type_layer = 1
					AND			id_layer = :idlayer ";
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
				if($lay['phone'] != '') $tab_ct[$fields['id_ct']]['phone'] = $lay['phone'];
				if($lay['phone2'] != '') $tab_ct[$fields['id_ct']]['phone2'] = $lay['phone2'];
				if($lay['fax'] != '') $tab_ct[$fields['id_ct']]['fax'] = $lay['fax'];
				if($lay['mobile'] != '') $tab_ct[$fields['id_ct']]['mobile'] = $lay['mobile'];
				if($lay['email'] != '') $tab_ct[$fields['id_ct']]['email'] = $lay['email'];
				if($lay['email2'] != '') $tab_ct[$fields['id_ct']]['email2'] = $lay['email2'];
				if($lay['email3'] != '') $tab_ct[$fields['id_ct']]['email3'] = $lay['email3'];
				if($lay['comments'] != '') $tab_ct[$fields['id_ct']]['comments'] = $lay['comments'];
			}
		}
	}

	define ('FPDF_FONTPATH',DIMS_APP_PATH . '/include/fpdf/font/');

	require_once DIMS_APP_PATH . '/include/fpdf/fpdf.php';
	require_once DIMS_APP_PATH . '/include/class_pdf.php';

	require_once DIMS_APP_PATH . '/include/class_pdf_etiquettes.php';

	header("Cache-control: private");
	header("Content-type: application/pdf");
	header("Pragma: public");

	ob_end_clean();

	//ob_start();

	$pdf = new PDF_ETIQUETTES('p','mm','A4');
	$pdf->open();

	$pdf->SetCreator("Dims");
	$pdf->SetAuthor("Netlor");
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);
	$pdf->SetMargins(0,0,0,0);
	$pdf->SetAutoPageBreak(false,0);

	$pdf->setElements($tab_ct);

	$pdf->fillTableUSer();

	$pdf->Output("etiquettes.pdf",'D');

	while (@ob_end_flush());

	die();
}
?>
