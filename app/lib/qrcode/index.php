<?php

require 'phpqrcode/qrlib.php';
require 'fpdf/fpdf.php';

$imageFile  ='temp.png';

$data[] = 'Nom : Nourrissier';
$data[] = 'Prenom : Patrick';
$data[] = 'Grade : A';
$data[] = 'N° d\'immatriculation : 000123456';
$data[] = 'Service : Netlor';
$data[] = 'Date de Naissance : 21/03/1977';

QRcode::png(implode("\n", $data), $imageFile, QR_ECLEVEL_L, 10);

$pdf = new FPDF();
$pdf->AddPage();

// qrcode
$size = getimagesize($imageFile);

$large = $size[0];
$height = $size[1];
$ratio = 120 / $height;
$newlarge = $large * $ratio;
$posi = (210 - $newlarge) / 2;

$pdf->Image($imageFile, $posi, 65, 0, 120);

// Text
$pdf->SetFont('Times','',12);
foreach($data as $elem) {
	$pdf->Cell(0, 10 , utf8_decode($elem), 0, 1);
}
$pdf->Output();
