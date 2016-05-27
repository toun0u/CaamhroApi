<?php
ini_set('memory_limit', '128M');

ob_end_clean();

header("Cache-control: private");
header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=promotion.pdf");
header("Pragma: public");

require_once DIMS_APP_PATH.'/modules/catalogue/include/functions.php';
require_once DIMS_APP_PATH.'/modules/catalogue/include/class_pdf_promotion.php';

$pdf_promotion = new pdf_promotion();

$pdf_promotion->promo = cata_getLastPromo();

if (sizeof($pdf_promotion->promo)) {
	$pdf_promotion->articles = cata_orderByFamily(array_keys($_SESSION['catalogue']['promo']['unlocked']));
	$pdf_promotion->template_path = $template_path;

	$work = new workspace();
	$work->open($_SESSION['dims']['workspaceid']);
	$pdf_promotion->tiers = $work->getTiers();

	$pdf_promotion->Open();
	$pdf_promotion->AliasNbPages();
	$pdf_promotion->AddPage();
	$pdf_promotion->Content();
	$pdf_promotion->Output("promotion.pdf", 'D');
}

while (@ob_end_flush());
die();
