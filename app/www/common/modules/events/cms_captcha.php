<?php
session_start();

header("Content-type: image/png");

	$size = 32;
	$marge = 15;
	$font = 'fonts/Liberation.ttf';
    $mot = '';
	//Pas de char conflictuel : 0-o, 1-l, 5-s -> A remettre contre les robots ?
    //$charList = 'abcdefghijkmnpqrtuvwxyz2346789';
	$charList = 'ABCDEF12346789';
    $sizeCharList = strlen($charList);

    $nombre_aleatoire = mt_rand(3,4);

    for($i = 0; $i < $nombre_aleatoire; $i++) {
        $y = mt_rand(0, $sizeCharList-1);
        $mot .= $charList[$y];
    }

	$matrix_blur = array(
		array(1,1,1),
		array(1,1,1),
		array(1,1,1));

	$box = imagettfbbox($size, 0, $font, $mot);
	$largeur = $box[2] - $box[0];
	$hauteur = $box[1] - $box[7];
	$largeur_lettre = round($largeur/strlen($mot));

	$img = imagecreate($largeur+$marge, $hauteur+$marge);
	$blanc = imagecolorallocate($img, 255, 255, 255);
	$noir = imagecolorallocate($img, 0, 0, 0);

	$couleur = array(
		imagecolorallocate($img, 0x14, 0x14, 0x12),
		imagecolorallocate($img, 0x69, 0x69, 0x5E),
		imagecolorallocate($img, 0xBD, 0xBD, 0xAA),
		imagecolorallocate($img, 0x21, 0x21, 0x14),
		imagecolorallocate($img, 0x21, 0x21, 0x1F));

	for($i = 0; $i < strlen($mot);++$i)
	{
		$l = $mot[$i];
		$angle = mt_rand(-35,35);
		imagettftext($img,mt_rand($size-7,$size),$angle,($i*$largeur_lettre)+$marge, $hauteur+mt_rand(0,$marge/2),$couleur[array_rand($couleur)], $font, $l);
	}


	imageline($img, 2,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $couleur[array_rand($couleur)]);
	imageline($img, 2,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $couleur[array_rand($couleur)]);


	//imageconvolution($img, $matrix_blur,9,0);
	//imageconvolution($img, $matrix_blur,9,0);

	imagepng($img);
    imagedestroy($img);


$_SESSION['dims']['captcha'] = $mot;
?>
