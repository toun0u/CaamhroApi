<?php
ini_set('max_execution_time',0);
ini_set('memory_limit',"1024M");

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// calcul de la largeur et hauteur
if (is_numeric($this->getLargeurColumn())) {
    $largeurcol1=($this->getLargeurColumn()+12)."px";
    $largeurcol2= ($this->getWidthForm() - $this->getLargeurColumn() -30)."px";
}

$cour=0;
// on calcule sur le nbre d'images
if ($this->nbImages>0) {
	// on recupere l'image selectionne
	if ($this->selectedImage!= '') {
		// on va se placer sur l'image courante
	}
	else {
		$this->selectedImage=0;
	}
	?>

	<div class="selector">
		<? echo $this->getSelectChoosePage( '/modules/doc/templates/gfx/previous_page.png',
										    '/modules/doc/templates/gfx/next_page.png',
											false,
											false,
										    '/modules/doc/templates/gfx/loupe_plus20.png',
											'/modules/doc/templates/gfx/loupe_moins20.png'); ?>
            <div style="float:right;width:164px;">
                <a style="text-decoration: none;font-size:14px;font-weight: bold;" href="?dims_op=doc_file_download&docfile_md5id=<?php echo $this->namepreview; ?>">
                                <img style="width:24px;" src="/common/modules/doc/img/document_download.png" />
                                <?php echo $_SESSION['cste']['_DIMS_DOWNLOAD']; ?>
                        </a>
                </div>
        </div>
	<div style="width:<? echo $largeurcol1; ?>;" class="thumbnail" id="thumbnail">
	<?

	// on regarde maintenant pour generer les images en miniatures pour les images affichees
	$paththumb=DIMS_WEB_PATH."data/".$this->pathData."/thumbs";

	if (!is_dir($paththumb) ) {
		dims_makedir($paththumb);
	}

        $iselected='';
	// on peut ecrire dans le dossier pour les miniatures
	if (is_dir($paththumb)) {

		echo "<ul>";

		for ($i=$cour;$i<$cour+$this->nbImages;$i++) {
			echo "<li id=\"li-pagepreview".$i."\" onclick=\"javascript:refreshPreviewDocument(".$i.");\">";

			// on affiche l'image réduite
			$sel = ($i == $this->selectedImage) ? 'selected' : '' ;
            if ($sel!='') $iselected=$i;

            // test si fichier thumb existe ou non
            if (!file_exists($paththumb."/".$this->listImages[$i])) {
                // on genère l'image
                list($width,$height)=getimagesize(DIMS_WEB_PATH."data/".$this->pathData."/".$this->listImages[$i]);
                $newwidth=240;
                $newheight=($height/$width)*$newwidth;

                dims_resizeimage2(DIMS_WEB_PATH."data/".$this->pathData."/".$this->listImages[$i],$newwidth,$newheight,'png',$paththumb."/".$this->listImages[$i]);
            }

			echo '<img class="'.$sel.'" style="width:'.$this->largeurcolumn.'px;" src="'.$this->webPathData.'/thumbs/'.$this->listImages[$i].'">';
			echo '<div class="num_page '.$sel.'">'.($i+1).'</div>';
			echo "</li>";
		}
		echo "</ul>";
	}
        echo "<script type=\"text/javascript\">var tmstr='';$(document).ready(function(){";
        if ($iselected!='') {
            echo 'setTimeout("updateScrollViewThumb('.($iselected-2).');",200);';
        }

        echo '$("#copy-button").zclip({
                path: "/js/ZeroClipboard.swf",
                copy: function(){
                    return $("div#contentselectedtext").text();
                    },
                afterCopy:function(){
                    $("#result-copy").html(\'<img src="./common/img/checkdo.png">\');
                }
                });';
        echo "}) </script>";
	?>
	</div>
	<?
}
?>
<div id="<? echo $this->namepreview;?>refresh" style="width:<? echo $largeurcol2; ?>;" class="content_visu">

	<?
	// on doit construire l'image courante qui va s'intégrer dans le contenu
	echo $this->zoomImage(false,false,false);

	?>
</div>
