<?

$time = time();

$tab_ratio = array(0 => array(60,60), 1 => array(40,40), 2 => array(24,24));
$max_size = 2097152;
$res = 0;
$_SESSION['business']['error_upload'] = array();
if (isset($_FILES['photo']) && !empty($_FILES['photo'])) {
	$logo_upload = $_FILES['photo'];

	//on recupere l'extension du fichier
		$ext = explode('.', $logo_upload['name']);
		$ext = strtolower($ext[count($ext)-1]);

	if(($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'png') || ($ext == 'gif')) {

		if ($logo_upload['tmp_name'] == "none") {
			$res = 1;
			$_SESSION['business']['error_upload'] = 'Erreur le nom du fichier est vide ou incorrect : '.$logo_upload['tmp_name'];
		}
		if ($logo_upload['size']> $max_size) {
			$res = 1;
			$_SESSION['private']['error_upload'] = 'Erreur la taille du fichier est incorrecte. ';
		}
		if ($res == 0) {
			$path = DIMS_WEB_PATH.'data/photo_ent/ent_'.$id_ent;
			if (!file_exists($path)) mkdir($path, 0777,true);

			//on verifie si le dossier existe deja, si oui, on le vide
			$files = scandir($path);
			if(!empty($files)){
				foreach($files as $file) {
					if ($file!="." && $file!="..") {
						$p_todel = $path."/".$file;
						if (file_exists($p_todel)) unlink($p_todel);
					}
				}
			}

			$path .= '/tmp_'.$id_ent.'.'.$ext;
			//on cree une image temporaire pour la redimensionner
			move_uploaded_file($logo_upload['tmp_name'],$path);

			//on stocke l'originale au format PNG quelque soit son extension initiale
			//convertImageInPng($path, dirname($path).'/original.png');
			dims_resizeimage2($path, 0, 0,'png',dirname($path).'/original.png');

		  //dimensionnement
			foreach($tab_ratio as $key => $ratio) {
				//dims_resizeimage($path, 0, $ratio[0], $ratio[1],'',0,dirname($path)."/photo".$ratio[0]."_".$time.".png");
				dims_resizeimage2($path, $ratio[0], $ratio[1],'png',dirname($path)."/photo".$ratio[0]."_".$time.".png");
			}
			unlink($path);
			//il n'y a pas d'erreur
			$_SESSION['business']['error_upload'] = '';
			$pct = new tiers();
			$pct->open($id_ent);
			$pct->fields['photo'] = "_".$time;
			$pct->save();
		}
	}
	else {
		$_SESSION['private']['error_upload'] = "Ce fichier n'a pas le bon format";
	}
}
?>
