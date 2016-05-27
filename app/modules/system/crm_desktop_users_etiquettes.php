<?
/*
 * Generation des étiquettes
 */
define ('FPDF_FONTPATH',DIMS_APP_PATH . '/include/fpdf/font/');

require_once DIMS_APP_PATH . '/include/fpdf/fpdf.php';
require_once DIMS_APP_PATH . '/include/class_pdf.php';
require_once DIMS_APP_PATH . '/include/class_barcode.php';
require_once DIMS_APP_PATH . '/include/class_pdf_etiquettes.php';

if (!file_exists(realpath(DIMS_APP_PATH)."/data/barcode/")) {
	dims_makedir(realpath(DIMS_APP_PATH)."/data/barcode/");
}

$moduleid=$_SESSION['dims']['current_object']['id_module'];
$objectid=$_SESSION['dims']['current_object']['id_object'];
$recordid=$_SESSION['dims']['current_object']['id_record'];
$reference='';
$elems=array();

if ($moduleid>0 && $objectid>0 && $recordid>0) {
	switch($objectid) {
			case dims_const::_SYSTEM_OBJECT_CONTACT:
				$obj=new contact();
				$obj->open($recordid);
				$elem['line1']= $obj->fields['lastname']." ".$obj->fields['firstname'];
				$elem['line2']= $obj->fields['address'];
				$elem['line3']= $obj->fields['postalcode']." ".$obj->fields['city'];
				$reference='ct_'.$recordid;
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS:
				$obj=new tiers();
				$obj->open($recordid);

				$elem['line1']= $obj->fields['intitule'];
				$elem['line2']= $obj->fields['adresse'];
				$elem['line3']= utf8_decode($obj->fields['codepostal']." ".$obj->fields['ville']);
				$reference='t_'.$recordid;
				break;
		}
}

$elem['reference']=$reference;
$elems[]=$elem;

//generation de l'image si n'existe pas
if (!file_exists(DIMS_APP_PATH . "/data/barcode/". $reference .".png")) {
		// Formattage du numero de l'agent
		$length = strlen($recordid);

		$barcode = "33";
		for ($j=1;$j<=11-$length;$j++)
				$barcode.="0";

		$barcode .= $recordid;

		$code_barre = new debora($barcode);
		$code_barre->makeImage("png",$barcode,$reference);
}

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

$pdf->setElements($elems);

$pdf->fill_table();

$pdf->Output("etiquettes.pdf",'D');

while (@ob_end_flush());

die();

?>
