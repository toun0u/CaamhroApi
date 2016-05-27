<?php
error_reporting(0);
session_start();

header("Content-type: image/png");

	$size = 32;
	$marge = 15;
	$largestFont = 'fonts/cmr10.ttf';
	$fonts = array('fonts/Liberation.ttf', 'fonts/FreeMonoBold.ttf', 'fonts/GenAR102.ttf', 'fonts/cmr10.ttf');
	//Pas de char conflictuel : 0-o, 1-l, 5-s -> A remettre contre les robots ?
    //$charList = 'abcdefghijkmnpqrtuvwxyz2346789';
	$charList = 'ABCDEFKMNPRSWXZ12346789';
    $sizeCharList = strlen($charList);


	// Génération du mot
	$mot = '';
	$nbLettres = mt_rand(3, 4);
	for($i = 0; $i < $nbLettres; $i++) {
		$y = mt_rand(0, $sizeCharList-1);
		$mot .= $charList[$y];
	}

	$box = imagettfbbox($size, 0, $largestFont, $mot);
	$largeur = $box[2] - $box[0];
	$hauteur = $box[1] - $box[7];
	$largeur_lettre = round($largeur/strlen($mot));

	$img = imagecreate($largeur+$marge, $hauteur+$marge);
	$blanc = imagecolorallocate($img, 255, 255, 255);
	$noir = imagecolorallocate($img, 0, 0, 0);

	$couleur = array(
		imagecolorallocate($img, 0x14, 0x14, 0x12),
		imagecolorallocate($img, 0x69, 0x69, 0x5E),
		imagecolorallocate($img, 0x08, 0x21, 0x08),
		imagecolorallocate($img, 0x21, 0x21, 0x14),
		imagecolorallocate($img, 0x21, 0x21, 0x1F));

	// Placement des lettres
	for($i = 0; $i < strlen($mot);++$i)
	{
		$l = $mot[$i];
		$angle = mt_rand(-35,35);
		imagettftext($img, mt_rand($size-7,$size), $angle,
			($i*$largeur_lettre)+$marge, $hauteur+mt_rand(0,$marge/2),
			$couleur[array_rand($couleur)], $fonts[array_rand($fonts)], $l);
	}

	// Ajout de deux lignes
	imageline($img, 2,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $couleur[array_rand($couleur)]);
	imageline($img, 2,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $couleur[array_rand($couleur)]);

	// Ajout de deux à cinq arcs de cercle
	$nbArcs = mt_rand(2, 5);
	for($i=0 ; $i<$nbArcs ; $i++) {
		$startAngle = mt_rand(0, 380);
		$endAngle = ($startAngle + mt_rand(50, 130)) % 380;
		imagearc($img, mt_rand(0, $largeur), mt_rand(0, $hauteur),
			mt_rand(30, 60), mt_rand(30, 60), $startAngle, $endAngle, $couleur[array_rand($couleur)]);
	}

	// Affichage
	imagepng($img);
    imagedestroy($img);


$_SESSION['dims']['captcha'] = $mot;
?>
