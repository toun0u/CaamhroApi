<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// calcul de la largeur et hauteur
if (is_numeric($this->getLargeurColumn())) {
    $largeurcol1=($this->getLargeurColumn()+12)."px";
    $largeurcol2= ($this->getWidthForm() - $this->getLargeurColumn() -16)."px";
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
		<? echo $this->getSelectChoosePage(_DESKTOP_TPL_PATH."/gfx/preview/previous_page.png",
										   _DESKTOP_TPL_PATH."/gfx/preview/next_page.png",
										   false,
										   false,
										   _DESKTOP_TPL_PATH."/gfx/preview/loupe_plus20.png",
										   _DESKTOP_TPL_PATH."/gfx/preview/loupe_moins20.png"); ?>
	</div>
	<div style="width:<? echo $largeurcol1; ?>;height:<? echo $_SESSION['dims']['preview'][$this->namepreview]['heightform']; ?>px;" class="thumbnail">
	<?
	// on regarde maintenant pour generer les images en miniatures pour les images affichees
	$paththumb=_DIMS_PATHDATA.$this->pathData."/thumbs";

	if (!is_dir($paththumb) ) {
		dims_makedir($paththumb);
	}

	// on peut ecrire dans le dossier pour les miniatures
	if (is_dir($paththumb)) {
		echo "<ul>";

		for ($i=$cour;$i<$cour+$this->nbImages;$i++) {
			echo "<li onclick=\"javascript:refreshPreviewDocument(".$i.");\">";

			// on affiche l'image réduite
			$sel = ($i == $this->selectedImage) ? 'selected' : '' ;
			echo '<img class="'.$sel.'" style="width:'.$this->largeurcolumn.'px;" src="'.$this->webPathData.'/'.$this->listImages[$i].'">';
			echo '<div class="num_page '.$sel.'">'.($i+1).'</div>';
			echo "</li>";
		}
		echo "</ul>";
	}
	?>
	</div>
	<?
}
?>
<div id="<? echo $this->namepreview;?>refresh" style="width:<? echo $largeurcol2; ?>;height:<? echo $_SESSION['dims']['preview'][$this->namepreview]['heightform']; ?>px;" class="content_visu">

	<?
	// on doit construire l'image courante qui va s'intégrer dans le contenu
	echo $this->zoomImage(false,false,false);

	?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var heightdiv=$("#<? echo $this->namepreview; ?>").outerHeight()-$("#<? echo $this->namepreview; ?> div.selector").outerHeight();
		$("#<? echo $this->namepreview; ?> #<? echo $this->namepreview; ?>refresh").css('height',heightdiv+"px");
		$("#<? echo $this->namepreview; ?> #<? echo $this->namepreview; ?>refresh div:first").css('height',heightdiv+"px");
		$("#<? echo $this->namepreview; ?> div.thumbnail").css('height',heightdiv+"px");
		$("#<? echo $this->namepreview; ?> div.thumbnail").scrollTop(<? echo $this->selectedImage; ?>*($("#<? echo $this->namepreview; ?> div.thumbnail li:first").outerHeight()-5)-10);
	});
</script>