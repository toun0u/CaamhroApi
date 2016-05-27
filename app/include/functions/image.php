<?php

function convertImageInPng($file, $dest){
	$infos = file($file);
	$format = strtolower($infos[0]);

	if(strpos($format, 'png') !== false){
		$imgsrc = ImageCreateFromPng($file);
	}
	else if(strpos($format, 'jpg') !== false || strpos($format, 'jpeg') !== false || strpos($format, 'jfif') !== false || strpos($format, 'rexif') !== false){
		$imgsrc = ImageCreateFromJPEG($file);
	}
	else if(strpos($format, 'gif') !== false){
		$imgsrc = imagecreatefromgif($file);
	}
	//header("Content-Type: image/png");
	imagepng($imgsrc, $dest);
	imagedestroy($imgsrc);
}

function dims_resizeimage($imagefile, $coef = 0, $wmax = 0, $hmax = 0, $format = '', $nbcolor = 0, $filename = '', $cropx = 0, $cropy = 0) {
	$filename_array = explode('.', $imagefile);
	$extension = strtolower($filename_array[sizeof($filename_array)-1]);

	$infos2 = pathinfo($imagefile);
	$format_file = strtolower($infos2['extension']);

	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$infos = finfo_file($finfo,$imagefile);
	$infos_2 = explode("/",$infos);
	$format = strtolower($infos_2[1]);

	if(strpos($format, 'png') !== false){
		$imgsrc = ImageCreateFromPng($imagefile);
	}
	else if(strpos($format, 'jpg') !== false || strpos($format, 'jpeg') !== false || strpos($format, 'jfif') !== false || strpos($format, 'rexif') !== false){
		$imgsrc = ImageCreateFromJPEG($imagefile);
	}
	else if(strpos($format, 'gif') !== false){
		$imgsrc = imagecreatefromgif($imagefile);
	}
	else if(strpos($format_file, 'ico') !== false){
		require_once DIMS_APP_PATH."include/functions/class.ico.php";
		$i = new Ico($imagefile);
		if(!($r=$i->GetIcon(0))){
			return false;
		}else{
			$imgsrc = $r;
		}
	}

	$w = imagesx($imgsrc);
	$h = imagesy($imgsrc);

	$decalx = 0;
	$decaly = 0;

	if ($cropx > 0 && $cropy > 0) {
		$coef = 1;
		// on a définit une taille fixée
		if ($cropx < $w && $cropy < $h) { // on a demandé une image plus petite que l'original
			// regardons la proportion, calcul du coeficient
			$cx = $w / $cropx;
			$cy = $h / $cropy;

			if ($cx != $cy) { // pas proportionnel
				if ($cx > $cy) {
					// on doit maintenant calculer pour revenir au meme coeff
					$dist = $w / $cy;
					$decalx = intval(($w - $dist) / 2);
				}
				else {
					$dist = $h / $cx;
					$decaly = intval(($h - $dist) / 2);
				}
			}
		}

		$wdest = $cropx;
		$hdest = $cropy;
	}
	else {
		if (!$coef) { // no coef defined
			if ($wmax) $coef = $w / $wmax;
			if ($hmax && $h / $hmax > $coef) $coef = $h / $hmax;
		}
		$wdest = $w / $coef;
		$hdest = $h / $coef;

	}

	$imgdest = imagecreatetruecolor ($wdest, $hdest);
	imagealphablending($imgdest, false);
	imagesavealpha($imgdest, true);

	imagecopyresampled($imgdest, $imgsrc, 0, 0, $decalx, $decaly, $wdest, $hdest, $w - 2 * $decalx, $h - 2 * $decaly);

	if ($nbcolor) imagetruecolortopalette($imgdest, true, $nbcolor);

	if($format != '') {
		$extension = $format;
		$imagefile = substr($imagefile, 0, strlen($imagefile) - strlen(dims_file_getextension($imagefile)) + 1);
	}

	if($filename == '') {
		header("Content-Type: image/$extension");
		header("Content-Disposition: inline; filename=$imagefile");

		switch($extension) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($imgdest);
				break;
			case 'png':
				imagepng($imgdest);
				break;
			case 'gif':
				imagepng($imgdest);
				break;
			default:
				return(0);
				break;
		}
	}
	else {
		switch($extension) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($imgdest,$filename);
				break;
			case 'png':
				imagepng($imgdest,$filename);
				break;
			case 'gif':
				imagegif($imgdest,$filename);
				break;
			default:
				return(0);
				break;
		}
	}
	return(1);
}

// Nécessite l'installation de php5-imagick
function dims_resizeimage2($imagefile, $col = 0, $row = 0, $format = '', $filename = '',$border = true) {
	if(extension_loaded("imagick")){
		if(file_exists($imagefile)){
			$image = new Imagick($imagefile);
			$image->setCompressionQuality(100);
			if($col>0 || $row>0){
				$d1 = $image->getImageGeometry();
				if($d1['width'] < $col && $d1['height'] < $row){ // si l'image est plus petite on ne redimensionne pas : on ajoute une bordure
					$color=new ImagickPixel("rgba(255,255,255,0)");
					$image->borderImage($color,($col-$d1['width'])/2,($row-$d1['height'])/2);
				}elseif($d1['width'] < $col && $d1['height'] > $row){
					$image->resizeImage((int)(($d1['width']*$row)/$d1['height']), $row, Imagick::FILTER_SINC, 0.99, true);
					if($border){
						$d2 = $image->getImageGeometry();
						if($d2['width'] < $col || $d2['height'] < $row){ // si dimensions trop petites, on ajoute une bordure
							$color=new ImagickPixel("rgba(255,255,255,0)");
							$image->borderImage($color,($col-$d2['width'])/2,0);
						}
					}
				}else{
					if($border){
						if($col == 0){
							$col = ($d1['width']*$row)/$d1['height'];
						}elseif($row == 0){
							$row = ($d1['height']*$col)/$d1['width'];
						}
						$image->resizeImage($col, $row, Imagick::FILTER_SINC, 0.99, true);
						//$image->adaptiveResizeImage($col, $row, true);
						$d2 = $image->getImageGeometry();
						if($d2['width'] < $col || $d2['height'] < $row){ // si dimensions trop petites, on ajoute une bordure
							$color=new ImagickPixel("rgba(255,255,255,0)");
							$image->borderImage($color,($col-$d2['width'])/2,($row-$d2['height'])/2);
						}
					}else{
						if($col == 0){
							$col = ($d1['width']*$row)/$d1['height'];
						}elseif($row == 0){
							$row = ($d1['height']*$col)/$d1['width'];
						}
						$image->resizeImage($col, $row, Imagick::FILTER_SINC, 0.99, true);
					}
				}
			}
			$ext = $image->getImageFormat();
			if($format != '' && $ext != $format){
				$lstExt = array(
					'jpg'=>'jpg',
					'jpeg'=>'jpg',
					'png'=>'png',
					'gif'=>'gif',
				);
				if(isset($lstExt[$format])){
					$image->setImageFormat($format);
					$ext = $lstExt[$format];
				}else{
					return 0;
				}
			}
			if($filename == '') {
				header("Content-Type: image/$ext");
				header("Content-Disposition: inline; filename=".$image->getFilename());
				echo $image;
			}
			else {
				$fo = fopen($filename,'w');
				$image->writeImageFile($fo);
				fclose($fo);
			}
			return 1;
		}else
			return 0;
	}else{
		return dims_resizeimage($imagefile, 0, $col, $row, $format, 0, $filename, 0, 0);
	}
}

/************************************/
/* phpcs.com 						*/
/************************************/
function dims_rvbToHexa($rvb){
	if(!is_array($rvb)) return 0;
	$hex = "";

	for($i=0; $i<3; $i++) {
		if( ($rvb[$i] > 255) || ($rvb[$i] < 0) ) return 0;

		$temp = dechex($rvb[$i]);

		 // test si la chaine fait 1 pour ajouter un 0 devant
		if(strlen($temp) < 2) $hex .= "0". $temp;
		else $hex .= $temp;
	}
return $hex;
}

function dims_hexaToRvb($hexa){
	$hexa=str_replace("#","",$hexa);

	$rvb = array();
	$hex[0]=substr($hexa,0,2);
	$hex[1]=substr($hexa,2,2);
	$hex[2]=substr($hexa,4,2);

	for($i=0; $i<3; $i++) {
		$rvb[$i]=hexdec($hex[$i]);
	}

	return $rvb;
}


?>
